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
        $warga = $this->wargaService->getWarga($request->only(['search','rt','rw','kartu_keluarga_id','jenis_kelamin','status_warga','date_from','date_to','per_page']));
        $rtList = $this->wargaService->getRtList();
        $rwList = $this->wargaService->getRwList();
        $kkList = KartuKeluarga::orderBy('no_kk')->get(['id','no_kk','kepala_keluarga','rt','rw']);
        return view('warga.index', compact('warga','rtList','rwList','kkList'));
    }

    public function create()
    {
        $kkList = KartuKeluarga::orderBy('no_kk')->get();
        return view('warga.create', compact('kkList'));
    }

    public function store(Store $request)
    {
        $this->wargaService->createWarga($request->validated());
        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan!');
    }

    public function show(Warga $warga)
    {
        $warga->load(['kartuKeluarga', 'mutasi']);
        return view('warga.show', compact('warga'));
    }

    public function edit(Warga $warga)
    {
        $kkList = KartuKeluarga::orderBy('no_kk')->get();
        return view('warga.edit', compact('warga','kkList'));
    }

    public function update(Update $request, Warga $warga)
    {
        $this->wargaService->updateWarga($warga, $request->validated());
        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui!');
    }

    public function destroy(Warga $warga)
    {
        $this->wargaService->deleteWarga($warga);
        return redirect()->route('warga.index')->with('success', 'Data warga berhasil dihapus!');
    }
}
