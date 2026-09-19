<?php

namespace App\Http\Controllers;

use App\Http\Requests\KartuKeluarga\Store;
use App\Http\Requests\KartuKeluarga\Update;
use App\Http\Services\KartuKeluargaService;
use App\Models\KartuKeluarga;
use Illuminate\Http\Request;

class KartuKeluargaController extends Controller
{
    public function __construct(protected KartuKeluargaService $kkService) {}

    public function index(Request $request)
    {
        $kk = $this->kkService->getKK($request->only(['search','rt','rt_id','rw','date_from','date_to','per_page']));
        $rts = \App\Models\Rt::active()->orderBy('kode_rt')->get();
        return view('kartu-keluarga.index', compact('kk','rts'));
    }

    public function create()
    {
        $rts = \App\Models\Rt::active()->orderBy('kode_rt')->get();
        return view('kartu-keluarga.create', compact('rts'));
    }

    public function store(Store $request)
    {
        $this->kkService->createKK($request->validated());
        return redirect()->route('kartu-keluarga.index')->with('success', 'Kartu Keluarga berhasil ditambahkan!');
    }

    public function show(KartuKeluarga $kartuKeluarga)
    {
        $this->kkService->ensureRtAccess($kartuKeluarga);
        $kartuKeluarga->load(['warga.rt','rtRelation.alamatRt','warga.kartuKeluarga']);
        $rts = \App\Models\Rt::active()->orderBy('kode_rt')->get();
        return view('kartu-keluarga.show', compact('kartuKeluarga','rts'));
    }

    public function edit(KartuKeluarga $kartuKeluarga)
    {
        $this->kkService->ensureRtAccess($kartuKeluarga);
        $kartuKeluarga->load('warga');
        $rts = \App\Models\Rt::active()->orderBy('kode_rt')->get();
        return view('kartu-keluarga.edit', compact('kartuKeluarga','rts'));
    }

    public function update(Update $request, KartuKeluarga $kartuKeluarga)
    {
        $this->kkService->updateKK($kartuKeluarga, $request->validated());
        return redirect()->route('kartu-keluarga.index')->with('success', 'Kartu Keluarga berhasil diperbarui!');
    }

    public function destroy(KartuKeluarga $kartuKeluarga)
    {
        $result = $this->kkService->deleteKK($kartuKeluarga);
        if (isset($result['success']) && $result['success'] === false) {
            return redirect()->route('kartu-keluarga.index')->with('error', $result['message']);
        }
        return redirect()->route('kartu-keluarga.index')->with('success', 'Kartu Keluarga berhasil dihapus!');
    }
}
