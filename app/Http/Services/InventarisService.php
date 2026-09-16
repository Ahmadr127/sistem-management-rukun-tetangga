<?php

namespace App\Http\Services;

use App\Models\Inventaris;
use App\Services\ActivityLogService;

class InventarisService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getInventaris(array $filters = [])
    {
        $query = Inventaris::withCount('peminjaman');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama_barang','like',"%{$search}%")
                  ->orWhere('kode_barang','like',"%{$search}%")
                  ->orWhere('kategori','like',"%{$search}%");
            });
        }
        if (!empty($filters['kategori'])) $query->where('kategori',$filters['kategori']);
        if (!empty($filters['kondisi'])) $query->where('kondisi',$filters['kondisi']);
        if (!empty($filters['lokasi'])) $query->where('lokasi','like',"%{$filters['lokasi']}%");

        $perPage = in_array((int)($filters['per_page'] ?? 10), [5,10,25,50,100]) ? (int)($filters['per_page'] ?? 10) : 10;

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function createInventaris(array $data)
    {
        if (!empty($data['harga_satuan']) && !empty($data['jumlah'])) {
            $data['total_harga'] = $data['harga_satuan'] * $data['jumlah'];
        }
        $inv = Inventaris::create($data);
        $this->activityLogger->log($inv, 'created', $inv->toArray(), "Inventaris {$inv->nama_barang} dibuat");
        return $inv;
    }

    public function updateInventaris(Inventaris $inventaris, array $data)
    {
        if (isset($data['harga_satuan']) || isset($data['jumlah'])) {
            $harga = $data['harga_satuan'] ?? $inventaris->harga_satuan;
            $jumlah = $data['jumlah'] ?? $inventaris->jumlah;
            if ($harga !== null) $data['total_harga'] = $harga * $jumlah;
        }
        $oldData = $inventaris->toArray();
        $inventaris->update($data);
        $inventaris->refresh();
        $newData = $inventaris->toArray();
        $this->activityLogger->logUpdated($inventaris, $oldData, $newData);
        return $inventaris;
    }

    public function deleteInventaris(Inventaris $inventaris)
    {
        if ($inventaris->peminjaman()->where('status','DIPINJAM')->exists()) {
            return ['success'=>false, 'message'=>'Inventaris tidak dapat dihapus karena masih ada peminjaman aktif!'];
        }
        $inventaris->loadMissing('peminjaman');
        $this->activityLogger->logDeleted($inventaris);
        $inventaris->delete();
        return ['success'=>true];
    }

    public function getLaporan(): array
    {
        $totalBarang = Inventaris::count();
        $totalUnit = Inventaris::sum('jumlah');
        $totalNilai = Inventaris::sum('total_harga');
        $byKategori = Inventaris::selectRaw('kategori, COUNT(*) as jml_barang, SUM(jumlah) as total_unit, SUM(total_harga) as total_nilai')
            ->groupBy('kategori')->get();
        $byKondisi = Inventaris::selectRaw('kondisi, COUNT(*) as jml')->groupBy('kondisi')->get();
        $byLokasi = Inventaris::selectRaw('lokasi, COUNT(*) as jml')->groupBy('lokasi')->get();
        $all = Inventaris::withCount('peminjaman')->orderBy('nama_barang')->get();

        return compact('totalBarang','totalUnit','totalNilai','byKategori','byKondisi','byLokasi','all');
    }
}
