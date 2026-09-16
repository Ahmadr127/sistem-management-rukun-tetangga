<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventaris\Store;
use App\Http\Requests\Inventaris\Update;
use App\Http\Services\InventarisService;
use App\Models\Inventaris;
use Illuminate\Http\Request;

class InventarisController extends Controller
{
    public function __construct(protected InventarisService $inventarisService) {}

    public function index(Request $request)
    {
        $inventaris = $this->inventarisService->getInventaris($request->only(['search','kategori','kondisi','lokasi','per_page']));
        $kategoriList = Inventaris::whereNotNull('kategori')->distinct()->pluck('kategori');
        return view('inventaris.index', compact('inventaris','kategoriList'));
    }

    public function create()
    {
        return view('inventaris.create');
    }

    public function store(Store $request)
    {
        $this->inventarisService->createInventaris($request->validated());
        return redirect()->route('inventaris.index')->with('success', 'Inventaris berhasil ditambahkan!');
    }

    public function show(Inventaris $inventari)
    {
        // route model binding: inventari (laravel singular)
        $inventari->load(['peminjaman.warga', 'peminjaman.inventaris']);
        return view('inventaris.show', ['inventaris' => $inventari]);
    }

    public function edit(Inventaris $inventari)
    {
        return view('inventaris.edit', ['inventaris' => $inventari]);
    }

    public function update(Update $request, Inventaris $inventari)
    {
        $this->inventarisService->updateInventaris($inventari, $request->validated());
        return redirect()->route('inventaris.index')->with('success', 'Inventaris berhasil diperbarui!');
    }

    public function destroy(Inventaris $inventari)
    {
        $result = $this->inventarisService->deleteInventaris($inventari);
        if (isset($result['success']) && $result['success'] === false) {
            return redirect()->route('inventaris.index')->with('error', $result['message']);
        }
        return redirect()->route('inventaris.index')->with('success', 'Inventaris berhasil dihapus!');
    }

    public function laporan(Request $request)
    {
        $laporan = $this->inventarisService->getLaporan();
        return view('inventaris.laporan', compact('laporan'));
    }
}
