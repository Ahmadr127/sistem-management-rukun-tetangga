<?php

namespace App\Http\Controllers;

use App\Http\Requests\Warga\Store;
use App\Http\Requests\Warga\Update;
use App\Http\Services\WargaService;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    public function __construct(protected WargaService $wargaService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search','rt','rt_id','rw','kartu_keluarga_id','jenis_kelamin','status_warga','date_from','date_to','per_page']);
        $warga = $this->wargaService->getWarga($filters);
        $rtList = \App\Models\Rt::active()->orderBy('kode_rt')->get();
        $rwList = $this->wargaService->getRwList();
        $kkList = KartuKeluarga::orderBy('no_kk')->get(['id','no_kk','kepala_keluarga','rt','rw','rt_id']);
        // scope kk for RT users
        if (auth()->user() && !auth()->user()->isSuperAdmin() && auth()->user()->rt_id) {
            $kkList = $kkList->where('rt_id', auth()->user()->rt_id);
        }
        return view('warga.index', compact('warga','rtList','rwList','kkList'));
    }

    public function create()
    {
        $rts = \App\Models\Rt::active()->orderBy('kode_rt')->get();
        $kkList = KartuKeluarga::orderBy('no_kk')->get();
        if (auth()->user() && !auth()->user()->isSuperAdmin() && auth()->user()->rt_id) {
            $kkList = KartuKeluarga::where('rt_id', auth()->user()->rt_id)->orderBy('no_kk')->get();
        }
        return view('warga.create', compact('kkList','rts'));
    }

    public function store(Store $request)
    {
        $warga = $this->wargaService->createWarga($request->validated());
        $redirect = $request->input('redirect_to');
        if ($redirect) {
            return redirect($redirect)->with('success', 'Data warga berhasil ditambahkan!');
        }
        // if came from KK context, redirect to KK show
        if ($request->filled('kartu_keluarga_id')) {
            $kk = \App\Models\KartuKeluarga::find($request->input('kartu_keluarga_id'));
            if ($kk) return redirect()->route('kartu-keluarga.show', $kk)->with('success', 'Anggota keluarga berhasil ditambahkan!');
        }
        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan!');
    }

    public function show(Warga $warga)
    {
        $this->wargaService->ensureRtAccess($warga);
        $warga->load(['kartuKeluarga.rtRelation.alamatRt', 'mutasi','rt.alamatRt']);
        return view('warga.show', compact('warga'));
    }

    public function edit(Warga $warga)
    {
        $this->wargaService->ensureRtAccess($warga);
        $rts = \App\Models\Rt::active()->orderBy('kode_rt')->get();
        $kkList = KartuKeluarga::orderBy('no_kk')->get();
        if (auth()->user() && !auth()->user()->isSuperAdmin() && auth()->user()->rt_id) {
            $kkList = KartuKeluarga::where('rt_id', auth()->user()->rt_id)->orderBy('no_kk')->get();
        }
        return view('warga.edit', compact('warga','kkList','rts'));
    }

    public function update(Update $request, Warga $warga)
    {
        $this->wargaService->updateWarga($warga, $request->validated());
        $redirect = $request->input('redirect_to');
        if ($redirect) return redirect($redirect)->with('success', 'Data warga berhasil diperbarui!');
        if ($request->filled('kartu_keluarga_id')) {
            $kk = \App\Models\KartuKeluarga::find($request->input('kartu_keluarga_id'));
            if ($kk) return redirect()->route('kartu-keluarga.show', $kk)->with('success', 'Data warga berhasil diperbarui!');
        }
        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui!');
    }

    public function destroy(Warga $warga)
    {
        $kartuKeluargaId = $warga->kartu_keluarga_id;
        $this->wargaService->deleteWarga($warga);
        $redirect = request()->input('redirect_to');
        if ($redirect) return redirect($redirect)->with('success', 'Data warga berhasil dihapus!');
        if ($kartuKeluargaId && request()->has('from_kk')) {
            return redirect()->route('kartu-keluarga.show', $kartuKeluargaId)->with('success', 'Data warga berhasil dihapus!');
        }
        return redirect()->route('warga.index')->with('success', 'Data warga berhasil dihapus!');
    }
}
