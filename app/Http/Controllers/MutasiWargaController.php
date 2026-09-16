<?php

namespace App\Http\Controllers;

use App\Http\Requests\MutasiWarga\Store;
use App\Http\Requests\MutasiWarga\Update;
use App\Http\Services\MutasiWargaService;
use App\Models\MutasiWarga;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use Illuminate\Http\Request;

class MutasiWargaController extends Controller
{
    public function __construct(protected MutasiWargaService $mutasiService) {}

    public function index(Request $request)
    {
        $mutasi = $this->mutasiService->getMutasi($request->only(['search','jenis_mutasi','date_from','date_to','per_page']));
        return view('mutasi-warga.index', compact('mutasi'));
    }

    public function create()
    {
        $warga = Warga::orderBy('nama')->get();
        $kk = KartuKeluarga::orderBy('no_kk')->get();
        return view('mutasi-warga.create', compact('warga','kk'));
    }

    public function store(Store $request)
    {
        try {
            $this->mutasiService->createMutasi($request->validated());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
        return redirect()->route('mutasi-warga.index')->with('success', 'Mutasi warga berhasil dicatat!');
    }

    public function show(MutasiWarga $mutasiWarga)
    {
        $mutasiWarga->load(['warga.kartuKeluarga','kkLama','kkBaru']);
        return view('mutasi-warga.show', compact('mutasiWarga'));
    }

    public function edit(MutasiWarga $mutasiWarga)
    {
        $warga = Warga::orderBy('nama')->get();
        $kk = KartuKeluarga::orderBy('no_kk')->get();
        return view('mutasi-warga.edit', compact('mutasiWarga','warga','kk'));
    }

    public function update(Update $request, MutasiWarga $mutasiWarga)
    {
        $this->mutasiService->updateMutasi($mutasiWarga, $request->validated());
        return redirect()->route('mutasi-warga.index')->with('success', 'Mutasi warga berhasil diperbarui!');
    }

    public function destroy(MutasiWarga $mutasiWarga)
    {
        $this->mutasiService->deleteMutasi($mutasiWarga);
        return redirect()->route('mutasi-warga.index')->with('success', 'Mutasi warga berhasil dihapus!');
    }
}
