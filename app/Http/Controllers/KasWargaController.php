<?php

namespace App\Http\Controllers;

use App\Http\Requests\KasWarga\Bayar;
use App\Http\Requests\KasWarga\Store;
use App\Http\Requests\KasWarga\Update;
use App\Http\Services\KasWargaService;
use App\Models\KasJenis;
use App\Models\KasPembayaran;
use App\Models\Rt;
use Illuminate\Http\Request;

class KasWargaController extends Controller
{
    public function __construct(protected KasWargaService $kasService) {}

    /** Daftar master jenis kas */
    public function index(Request $request)
    {
        $jenis = $this->kasService->getJenis($request->only(['search', 'rt_id', 'periode_type', 'target_type', 'is_active', 'per_page']));
        $rts = Rt::active()->orderBy('kode_rt')->get();
        return view('kas-warga.index', compact('jenis', 'rts'));
    }

    /** Form tambah jenis kas */
    public function create(Request $request)
    {
        $rts = Rt::active()->orderBy('kode_rt')->get();
        $selectedRt = $request->get('rt_id') ?? auth()->user()->rt_id;
        return view('kas-warga.create', compact('rts', 'selectedRt'));
    }

    public function store(Store $request)
    {
        $jenis = $this->kasService->createJenis($request->validated());
        return redirect()->route('kas-warga.show', $jenis)->with('success', 'Jenis kas berhasil ditambahkan! Silakan input pembayaran.');
    }

    /** Tabel ledger matriks: No, Nama KK/Warga, NIK + kolom tanggal */
    public function show(Request $request, KasJenis $kasJenis)
    {
        $kasJenis->load(['rt']);
        $ledger = $this->kasService->getLedger($kasJenis, $request->only(['mode', 'bulan', 'minggu', 'tahun', 'search', 'per_page']));
        return view('kas-warga.show', array_merge(['kasJenis' => $kasJenis], $ledger));
    }

    public function edit(KasJenis $kasJenis)
    {
        $this->kasService->assertJenisAccess($kasJenis);
        $rts = Rt::active()->orderBy('kode_rt')->get();
        return view('kas-warga.edit', compact('kasJenis', 'rts'));
    }

    public function update(Update $request, KasJenis $kasJenis)
    {
        $this->kasService->updateJenis($kasJenis, $request->validated());
        return redirect()->route('kas-warga.index')->with('success', 'Jenis kas berhasil diperbarui!');
    }

    public function destroy(KasJenis $kasJenis)
    {
        $this->kasService->deleteJenis($kasJenis);
        return redirect()->route('kas-warga.index')->with('success', 'Jenis kas beserta seluruh pembayarannya berhasil dihapus!');
    }

    /** Simpan pembayaran dari modal klik tanggal (waktu otomatis sekarang) */
    public function bayar(Bayar $request, KasJenis $kasJenis)
    {
        $this->kasService->bayar($kasJenis, $request->validated());
        return redirect()
            ->route('kas-warga.show', array_merge(['kasJenis' => $kasJenis->id], $request->only(['mode', 'bulan', 'minggu', 'tahun', 'search'])))
            ->with('success', 'Pembayaran berhasil disimpan!');
    }

    /** Batalkan pembayaran (kembalikan sel menjadi belum bayar) */
    public function batalBayar(Request $request, KasPembayaran $pembayaran)
    {
        $kasJenisId = $pembayaran->kas_jenis_id;
        $this->kasService->batalBayar($pembayaran);
        return redirect()
            ->route('kas-warga.show', array_merge(['kasJenis' => $kasJenisId], $request->only(['mode', 'bulan', 'minggu', 'tahun', 'search'])))
            ->with('success', 'Pembayaran dibatalkan.');
    }
}
