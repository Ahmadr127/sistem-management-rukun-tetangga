<?php

namespace App\Http\Services;

use App\Models\MutasiWarga;
use App\Models\Warga;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;

class MutasiWargaService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getMutasi(array $filters = [])
    {
        $query = MutasiWarga::with(['warga.kartuKeluarga', 'kkLama', 'kkBaru']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('warga', fn($q)=> $q->where('nama','like',"%{$search}%")->orWhere('nik','like',"%{$search}%"));
        }
        if (!empty($filters['jenis_mutasi'])) {
            $query->where('jenis_mutasi', $filters['jenis_mutasi']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('tanggal_mutasi', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('tanggal_mutasi', '<=', $filters['date_to']);
        }

        $perPage = in_array((int)($filters['per_page'] ?? 10), [5,10,25,50,100]) ? (int)($filters['per_page'] ?? 10) : 10;

        return $query->latest('tanggal_mutasi')->latest()->paginate($perPage)->withQueryString();
    }

    public function createMutasi(array $data)
    {
        return DB::transaction(function() use ($data) {
            $mutasi = MutasiWarga::create($data);

            // Auto update warga status / kk if needed
            $warga = Warga::find($data['warga_id']);
            if ($warga) {
                switch ($data['jenis_mutasi']) {
                    case 'MENINGGAL':
                        $warga->update(['status_warga' => 'MENINGGAL']);
                        break;
                    case 'KELUAR':
                        $warga->update(['status_warga' => 'PINDAH']);
                        break;
                    case 'PINDAH_KK':
                        if (!empty($data['kk_baru_id'])) {
                            $warga->update(['kartu_keluarga_id' => $data['kk_baru_id']]);
                        }
                        break;
                    case 'MASUK':
                        if (!empty($data['kk_baru_id'])) {
                            $warga->update(['kartu_keluarga_id' => $data['kk_baru_id'], 'status_warga' => 'AKTIF']);
                        }
                        break;
                }
            }

            $mutasi->load(['warga', 'kkLama', 'kkBaru']);
            $this->activityLogger->log($mutasi, 'created', $mutasi->toArray(), "Mutasi {$mutasi->jenis_mutasi} warga {$mutasi->warga->nama}");

            return $mutasi;
        });
    }

    public function updateMutasi(MutasiWarga $mutasi, array $data)
    {
        $oldData = $mutasi->toArray();
        $oldData['warga'] = $mutasi->warga?->toArray();

        $mutasi->update($data);
        $mutasi->refresh();
        $mutasi->load(['warga', 'kkLama', 'kkBaru']);

        $newData = $mutasi->toArray();
        $newData['warga'] = $mutasi->warga?->toArray();

        $this->activityLogger->logUpdated($mutasi, $oldData, $newData);

        return $mutasi;
    }

    public function deleteMutasi(MutasiWarga $mutasi)
    {
        $mutasi->loadMissing(['warga', 'kkLama', 'kkBaru']);
        $this->activityLogger->logDeleted($mutasi);
        return $mutasi->delete();
    }
}
