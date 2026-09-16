<?php

namespace App\Http\Services;

use App\Models\Warga;
use App\Services\ActivityLogService;

class WargaService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getWarga(array $filters = [])
    {
        $query = Warga::with('kartuKeluarga');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('pekerjaan', 'like', "%{$search}%");
            });
        }
        if (!empty($filters['rt'])) {
            $query->whereHas('kartuKeluarga', fn($q)=> $q->where('rt', $filters['rt']));
        }
        if (!empty($filters['rw'])) {
            $query->whereHas('kartuKeluarga', fn($q)=> $q->where('rw', $filters['rw']));
        }
        if (!empty($filters['kartu_keluarga_id'])) {
            $query->where('kartu_keluarga_id', $filters['kartu_keluarga_id']);
        }
        if (!empty($filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }
        if (!empty($filters['status_warga'])) {
            $query->where('status_warga', $filters['status_warga']);
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

    public function createWarga(array $data)
    {
        return Warga::create($data);
    }

    public function updateWarga(Warga $warga, array $data)
    {
        $oldData = $warga->toArray();
        $oldData['kartu_keluarga'] = $warga->kartuKeluarga?->toArray();

        $warga->update($data);
        $warga->refresh();
        $warga->load('kartuKeluarga');

        $newData = $warga->toArray();
        $newData['kartu_keluarga'] = $warga->kartuKeluarga?->toArray();

        $this->activityLogger->logUpdated($warga, $oldData, $newData);

        return $warga;
    }

    public function deleteWarga(Warga $warga)
    {
        $warga->loadMissing('kartuKeluarga');
        $this->activityLogger->logDeleted($warga);
        return $warga->delete();
    }

    public function getRtList()
    {
        return \App\Models\KartuKeluarga::whereNotNull('rt')->distinct()->pluck('rt')->sort()->values();
    }

    public function getRwList()
    {
        return \App\Models\KartuKeluarga::whereNotNull('rw')->distinct()->pluck('rw')->sort()->values();
    }
}
