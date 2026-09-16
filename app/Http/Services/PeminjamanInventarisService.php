<?php

namespace App\Http\Services;

use App\Models\PeminjamanInventaris;
use App\Models\Inventaris;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;

class PeminjamanInventarisService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getPeminjaman(array $filters = [])
    {
        $query = PeminjamanInventaris::with(['inventaris','warga']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama_peminjam','like',"%{$search}%")
                  ->orWhere('keperluan','like',"%{$search}%")
                  ->orWhereHas('warga', fn($qq)=> $qq->where('nama','like',"%{$search}%"))
                  ->orWhereHas('inventaris', fn($qq)=> $qq->where('nama_barang','like',"%{$search}%"));
            });
        }
        if (!empty($filters['status'])) $query->where('status',$filters['status']);
        if (!empty($filters['inventaris_id'])) $query->where('inventaris_id',$filters['inventaris_id']);
        if (!empty($filters['date_from'])) $query->where('tanggal_pinjam','>=',$filters['date_from']);
        if (!empty($filters['date_to'])) $query->where('tanggal_pinjam','<=',$filters['date_to']);

        $perPage = in_array((int)($filters['per_page'] ?? 10), [5,10,25,50,100]) ? (int)($filters['per_page'] ?? 10) : 10;

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function createPeminjaman(array $data)
    {
        $inventaris = Inventaris::findOrFail($data['inventaris_id']);
        $tersedia = $inventaris->stok_tersedia;
        if ($data['jumlah_pinjam'] > $tersedia) {
            throw new \InvalidArgumentException("Stok tidak cukup! Tersedia: {$tersedia}, diminta: {$data['jumlah_pinjam']}");
        }

        $data['disetujui_oleh'] = Auth::id();
        // if warga_id provided, clear nama_peminjam; if not, require nama
        if (!empty($data['warga_id'])) {
            $data['nama_peminjam'] = null;
        }

        $p = PeminjamanInventaris::create($data);
        $p->load(['inventaris','warga']);
        $this->activityLogger->log($p, 'created', $p->toArray(), "Peminjaman {$p->inventaris->nama_barang} oleh {$p->nama_peminjam_display}");
        return $p;
    }

    public function updatePeminjaman(PeminjamanInventaris $peminjaman, array $data)
    {
        $oldData = $peminjaman->toArray();
        $oldData['inventaris'] = $peminjaman->inventaris?->toArray();

        $peminjaman->update($data);
        $peminjaman->refresh();
        $peminjaman->load(['inventaris','warga']);

        $newData = $peminjaman->toArray();
        $newData['inventaris'] = $peminjaman->inventaris?->toArray();

        $this->activityLogger->logUpdated($peminjaman, $oldData, $newData);

        return $peminjaman;
    }

    public function kembalikan(PeminjamanInventaris $peminjaman, array $data)
    {
        if ($peminjaman->status === 'DIKEMBALIKAN') {
            throw new \InvalidArgumentException('Barang sudah dikembalikan!');
        }

        $oldData = $peminjaman->toArray();

        $update = [
            'status' => $data['status'] ?? 'DIKEMBALIKAN',
            'tanggal_kembali_aktual' => $data['tanggal_kembali_aktual'] ?? now()->toDateString(),
            'kondisi_kembali' => $data['kondisi_kembali'] ?? null,
            'keterangan' => $data['keterangan'] ?? $peminjaman->keterangan,
        ];

        $peminjaman->update($update);
        $peminjaman->refresh();
        $peminjaman->load(['inventaris','warga']);

        $newData = $peminjaman->toArray();
        $this->activityLogger->logUpdated($peminjaman, $oldData, $newData, "Pengembalian {$peminjaman->inventaris->nama_barang}");

        // if kondisi returned is RUSAK/HILANG, update inventaris kondisi
        if (in_array($update['status'], ['RUSAK','HILANG']) && $peminjaman->inventaris) {
            // optional: could adjust stock
        }

        return $peminjaman;
    }

    public function deletePeminjaman(PeminjamanInventaris $peminjaman)
    {
        $peminjaman->loadMissing(['inventaris','warga']);
        $this->activityLogger->logDeleted($peminjaman);
        return $peminjaman->delete();
    }
}
