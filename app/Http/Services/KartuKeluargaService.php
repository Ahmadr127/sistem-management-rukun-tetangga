<?php

namespace App\Http\Services;

use App\Models\KartuKeluarga;
use App\Services\ActivityLogService;

class KartuKeluargaService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getKK(array $filters = [])
    {
        $query = KartuKeluarga::withCount('warga');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhere('kepala_keluarga', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }
        if (!empty($filters['rt'])) {
            $query->where('rt', $filters['rt']);
        }
        if (!empty($filters['rw'])) {
            $query->where('rw', $filters['rw']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $perPage = in_array((int)($filters['per_page'] ?? 10), [5,10,25,50,100]) ? (int)($filters['per_page'] ?? 10) : 10;

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function createKK(array $data)
    {
        return KartuKeluarga::create($data);
    }

    public function updateKK(KartuKeluarga $kk, array $data)
    {
        $oldData = $kk->toArray();
        $kk->update($data);
        $kk->refresh();
        $newData = $kk->toArray();
        $this->activityLogger->logUpdated($kk, $oldData, $newData);
        return $kk;
    }

    public function deleteKK(KartuKeluarga $kk)
    {
        if ($kk->warga()->count() > 0) {
            return ['success' => false, 'message' => 'KK tidak dapat dihapus karena masih memiliki anggota keluarga!'];
        }
        $kk->loadMissing('warga');
        $this->activityLogger->logDeleted($kk);
        $kk->delete();
        return ['success' => true];
    }
}
