<?php

namespace App\Http\Services;

use App\Models\KasWarga;
use App\Models\Warga;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasWargaService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getKasWarga(array $filters = [])
    {
        $query = KasWarga::with(['warga','rt','creator']);

        if ($user = Auth::user()) {
            if (!$user->isSuperAdmin() && $user->rt_id) {
                $query->where('rt_id', $user->rt_id);
            } elseif (!empty($filters['rt_id'])) {
                $query->where('rt_id', $filters['rt_id']);
            }
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('warga', fn($q)=> $q->where('nama','like',"%{$search}%")->orWhere('nik','like',"%{$search}%"));
        }
        if (!empty($filters['periode_type'])) $query->where('periode_type', $filters['periode_type']);
        if (!empty($filters['periode'])) $query->where('periode', $filters['periode']);
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['warga_id'])) $query->where('warga_id', $filters['warga_id']);

        $perPage = in_array((int)($filters['per_page'] ?? 10), [5,10,25,50,100]) ? (int)($filters['per_page'] ?? 10) : 10;
        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function createKas(array $data): KasWarga
    {
        $user = Auth::user();
        $warga = Warga::findOrFail($data['warga_id']);
        // Security: RT users only for their RT
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            if ((int)$warga->rt_id !== (int)$user->rt_id) abort(403, 'Warga bukan dari RT Anda.');
            $data['rt_id'] = $user->rt_id;
        } else {
            // superadmin must have rt_id either from warga or explicit
            if (empty($data['rt_id'])) $data['rt_id'] = $warga->rt_id;
        }
        if (empty($data['rt_id'])) abort(422, 'RT tidak ditemukan untuk warga tersebut.');
        // Ensure warga rt matches provided rt_id
        if ((int)$data['rt_id'] !== (int)$warga->rt_id) abort(422, 'RT warga tidak sesuai dengan RT kas.');

        $data['created_by'] = $user?->id;
        $kas = KasWarga::create($data);
        $this->activityLogger->log($kas, 'created', $kas->toArray(), "Kas {$kas->periode} {$kas->warga->nama} Rp ".number_format((float)$kas->nominal,0,',','.'));
        return $kas;
    }

    public function updateKas(KasWarga $kas, array $data): KasWarga
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id && (int)$kas->rt_id !== (int)$user->rt_id) abort(403,'Akses kas ditolak.');
        if (isset($data['warga_id']) && (int)$data['warga_id'] !== (int)$kas->warga_id) {
            $warga = Warga::findOrFail($data['warga_id']);
            if ($user && !$user->isSuperAdmin() && (int)$warga->rt_id !== (int)$user->rt_id) abort(403,'Warga bukan RT Anda.');
            $data['rt_id'] = $warga->rt_id;
        }
        if (isset($data['rt_id']) && $user && !$user->isSuperAdmin() && (int)$data['rt_id'] !== (int)$user->rt_id) abort(403,'Tidak bisa pindah RT.');

        $old = $kas->toArray();
        $kas->update($data);
        $kas->refresh();
        $this->activityLogger->logUpdated($kas, $old, $kas->toArray());
        return $kas;
    }

    public function deleteKas(KasWarga $kas): bool
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id && (int)$kas->rt_id !== (int)$user->rt_id) abort(403,'Akses ditolak.');
        $kas->loadMissing(['warga','rt']);
        $this->activityLogger->logDeleted($kas);
        return $kas->delete();
    }

    public function generateKas(array $data): int
    {
        $user = Auth::user();
        $rtId = $data['rt_id'] ?? $user?->rt_id;
        if (!$rtId) abort(422, 'RT harus dipilih.');
        if ($user && !$user->isSuperAdmin() && (int)$rtId !== (int)$user->rt_id) abort(403,'Tidak bisa generate untuk RT lain.');
        $periodeType = $data['periode_type'];
        $periode = $data['periode'];
        $nominal = $data['nominal'];

        $wargas = Warga::where('rt_id', $rtId)->where('status_warga','AKTIF')->get();
        $created = 0;
        DB::transaction(function() use ($wargas, $rtId, $periodeType, $periode, $nominal, &$created, $user) {
            foreach ($wargas as $w) {
                $exists = KasWarga::where('warga_id',$w->id)->where('periode_type',$periodeType)->where('periode',$periode)->exists();
                if ($exists) continue;
                KasWarga::create([
                    'rt_id' => $rtId,
                    'warga_id' => $w->id,
                    'periode_type' => $periodeType,
                    'periode' => $periode,
                    'nominal' => $nominal,
                    'status' => 'belum_bayar',
                    'created_by' => $user?->id,
                ]);
                $created++;
            }
        });
        return $created;
    }

    public function getWargaByRt($rtId)
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) $rtId = $user->rt_id;
        return Warga::where('rt_id',$rtId)->where('status_warga','AKTIF')->orderBy('nama')->get(['id','nama','nik']);
    }
}
