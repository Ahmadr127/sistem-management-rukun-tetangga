<?php

namespace App\Http\Controllers;

use App\Http\Requests\Keuangan\Store;
use App\Http\Requests\Keuangan\Update;
use App\Http\Services\KeuanganService;
use App\Models\Keuangan;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function __construct(protected KeuanganService $keuanganService) {}

    public function pemasukan(Request $request)
    {
        $keuangan = $this->keuanganService->getKeuangan($request->only(['search','kategori','date_from','date_to','per_page']), 'PEMASUKAN');
        $saldo = $this->keuanganService->getSaldo();
        $categories = $this->keuanganService->getCategories('PEMASUKAN');
        return view('keuangan.pemasukan.index', compact('keuangan','saldo','categories'));
    }

    public function pengeluaran(Request $request)
    {
        $keuangan = $this->keuanganService->getKeuangan($request->only(['search','kategori','date_from','date_to','per_page']), 'PENGELUARAN');
        $saldo = $this->keuanganService->getSaldo();
        $categories = $this->keuanganService->getCategories('PENGELUARAN');
        return view('keuangan.pengeluaran.index', compact('keuangan','saldo','categories'));
    }

    public function createPemasukan()
    {
        $categories = $this->keuanganService->getCategories('PEMASUKAN');
        return view('keuangan.pemasukan.create', compact('categories'));
    }

    public function createPengeluaran()
    {
        $categories = $this->keuanganService->getCategories('PENGELUARAN');
        return view('keuangan.pengeluaran.create', compact('categories'));
    }

    public function store(Store $request)
    {
        $data = $request->validated();
        $this->keuanganService->createKeuangan($data);
        $route = $data['jenis'] === 'PEMASUKAN' ? 'keuangan.pemasukan.index' : 'keuangan.pengeluaran.index';
        return redirect()->route($route)->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function edit(Keuangan $keuangan)
    {
        $categories = $this->keuanganService->getCategories($keuangan->jenis);
        $isPemasukan = $keuangan->jenis === 'PEMASUKAN';
        return view($isPemasukan ? 'keuangan.pemasukan.edit' : 'keuangan.pengeluaran.edit', compact('keuangan','categories'));
    }

    public function update(Update $request, Keuangan $keuangan)
    {
        $this->keuanganService->updateKeuangan($keuangan, $request->validated());
        $route = $keuangan->jenis === 'PEMASUKAN' ? 'keuangan.pemasukan.index' : 'keuangan.pengeluaran.index';
        // if jenis changed, redirect accordingly
        $newJenis = $request->validated()['jenis'] ?? $keuangan->jenis;
        $route = $newJenis === 'PEMASUKAN' ? 'keuangan.pemasukan.index' : 'keuangan.pengeluaran.index';
        return redirect()->route($route)->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Keuangan $keuangan)
    {
        $jenis = $keuangan->jenis;
        $this->keuanganService->deleteKeuangan($keuangan);
        $route = $jenis === 'PEMASUKAN' ? 'keuangan.pemasukan.index' : 'keuangan.pengeluaran.index';
        return redirect()->route($route)->with('success', 'Transaksi berhasil dihapus!');
    }

    public function laporan(Request $request)
    {
        $laporan = $this->keuanganService->getLaporan($request->only(['date_from','date_to']));
        $filters = $request->only(['date_from','date_to']);
        return view('keuangan.laporan', compact('laporan','filters'));
    }
}
