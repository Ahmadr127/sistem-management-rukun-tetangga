<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeminjamanInventaris\Store;
use App\Http\Requests\PeminjamanInventaris\Update;
use App\Http\Requests\PeminjamanInventaris\Kembalikan;
use App\Http\Services\PeminjamanInventarisService;
use App\Models\PeminjamanInventaris;
use App\Models\Inventaris;
use App\Models\Warga;
use Illuminate\Http\Request;

class PeminjamanInventarisController extends Controller
{
    public function __construct(protected PeminjamanInventarisService $service) {}

    public function index(Request $request)
    {
        $peminjaman = $this->service->getPeminjaman($request->only(['search','status','inventaris_id','date_from','date_to','per_page']));
        $inventarisList = Inventaris::orderBy('nama_barang')->get();
        return view('peminjaman-inventaris.index', compact('peminjaman','inventarisList'));
    }

    public function create()
    {
        $inventaris = Inventaris::orderBy('nama_barang')->get();
        $warga = Warga::orderBy('nama')->get();
        return view('peminjaman-inventaris.create', compact('inventaris','warga'));
    }

    public function store(Store $request)
    {
        try {
            $this->service->createPeminjaman($request->validated());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
        return redirect()->route('peminjaman-inventaris.index')->with('success', 'Peminjaman berhasil dicatat!');
    }

    public function show(PeminjamanInventaris $peminjamanInventari)
    {
        $peminjamanInventari->load(['inventaris','warga','approver']);
        return view('peminjaman-inventaris.show', ['peminjaman' => $peminjamanInventari]);
    }

    public function edit(PeminjamanInventaris $peminjamanInventari)
    {
        $inventaris = Inventaris::orderBy('nama_barang')->get();
        $warga = Warga::orderBy('nama')->get();
        return view('peminjaman-inventaris.edit', ['peminjaman' => $peminjamanInventari, 'inventaris' => $inventaris, 'warga' => $warga]);
    }

    public function update(Update $request, PeminjamanInventaris $peminjamanInventari)
    {
        $this->service->updatePeminjaman($peminjamanInventari, $request->validated());
        return redirect()->route('peminjaman-inventaris.index')->with('success', 'Peminjaman berhasil diperbarui!');
    }

    public function destroy(PeminjamanInventaris $peminjamanInventari)
    {
        $this->service->deletePeminjaman($peminjamanInventari);
        return redirect()->route('peminjaman-inventaris.index')->with('success', 'Peminjaman berhasil dihapus!');
    }

    public function kembalikan(Kembalikan $request, PeminjamanInventaris $peminjamanInventari)
    {
        try {
            $this->service->kembalikan($peminjamanInventari, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->route('peminjaman-inventaris.index')->with('success', 'Barang berhasil dikembalikan!');
    }
}
