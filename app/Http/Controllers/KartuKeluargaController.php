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

    public function peta(Request $request)
    {
        $user = auth()->user();
        $rtId = $request->integer('rt_id') ?: null;
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            $rtId = (int) $user->rt_id;
        }

        ['data' => $kkList, 'total' => $total] = $this->kkService->getPetaData($rtId);
        $rts = \App\Models\Rt::active()->orderBy('kode_rt')->get();

        $markers = $kkList->map(fn($kk) => [
            'id' => $kk->id,
            'lat' => (float) $kk->latitude,
            'lng' => (float) $kk->longitude,
            'no_kk' => $kk->no_kk,
            'kepala' => $kk->kepala_keluarga ?? '-',
            'alamat' => $kk->alamat_lengkap,
            'rt' => $kk->rtRelation?->kode_rt ?? $kk->rt ?? '-',
            'url' => route('kartu-keluarga.show', $kk),
        ])->values();

        return view('kartu-keluarga.peta', [
            'markers' => $markers,
            'total' => $total,
            'rts' => $rts,
            'selectedRtId' => $rtId,
            'isSuper' => $user?->isSuperAdmin() ?? false,
        ]);
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
