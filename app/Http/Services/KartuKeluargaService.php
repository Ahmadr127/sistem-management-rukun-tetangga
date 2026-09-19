<?php

namespace App\Http\Services;

use App\Models\KartuKeluarga;
use App\Services\ActivityLogService;

class KartuKeluargaService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getKK(array $filters = [])
    {
        $query = KartuKeluarga::withCount('warga')->with('rtRelation');

        if ($user = auth()->user()) {
            if (!$user->isSuperAdmin() && $user->rt_id) {
                $query->where('rt_id', $user->rt_id);
            } elseif (!empty($filters['rt_id'])) {
                $query->where('rt_id', $filters['rt_id']);
            }
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhere('kepala_keluarga', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }
        if (!empty($filters['rt']) && !is_numeric($filters['rt'])) {
            $query->where('rt', $filters['rt']);
        } elseif (!empty($filters['rt']) && is_numeric($filters['rt'])) {
            $query->where('rt_id', $filters['rt']);
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

    public function ensureRtAccess(KartuKeluarga $kk): void
    {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            if ((int)$kk->rt_id !== (int)$user->rt_id) abort(403, 'Akses KK ditolak.');
        }
    }

    private function applyMasterAlamat(array &$data): void
    {
        if (empty($data['rt_id'])) return;
        $rt = \App\Models\Rt::with('alamatRt')->find($data['rt_id']);
        if (!$rt) return;
        // sync legacy rt string
        if (preg_match('/\d+/', $rt->kode_rt, $m)) $data['rt'] = str_pad($m[0], 2, '0', STR_PAD_LEFT);
        else $data['rt'] = $rt->kode_rt;
        // override wilayah from master if master exists (trust master, not frontend)
        if ($master = $rt->alamatRt) {
            if ($master->rw !== null) $data['rw'] = $master->rw;
            if ($master->kelurahan !== null) $data['desa'] = $master->kelurahan; // map kelurahan -> desa
            if ($master->kecamatan !== null) $data['kecamatan'] = $master->kecamatan;
            if ($master->kota !== null) $data['kabupaten'] = $master->kota;
            if ($master->provinsi !== null) $data['provinsi'] = $master->provinsi;
            if ($master->kode_pos !== null) $data['kode_pos'] = $master->kode_pos;
            // note: alamat detail (jalan+no) stays as $data['alamat'] provided by user, not overwritten with master alamat
        }
    }

    public function createKK(array $data)
    {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            $data['rt_id'] = $user->rt_id;
        }
        if ($user && !$user->isSuperAdmin() && isset($data['rt_id']) && (int)$data['rt_id'] !== (int)$user->rt_id) {
            abort(403, 'Tidak dapat membuat KK untuk RT lain.');
        }
        // handle kepala_keluarga_id -> set kepala_keluarga string
        if (!empty($data['kepala_keluarga_id'])) {
            $w = \App\Models\Warga::find($data['kepala_keluarga_id']);
            if ($w) $data['kepala_keluarga'] = $w->nama;
        }
        unset($data['kepala_keluarga_id']);
        $this->applyMasterAlamat($data);
        return KartuKeluarga::create($data);
    }

    public function updateKK(KartuKeluarga $kk, array $data)
    {
        $this->ensureRtAccess($kk);
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            $data['rt_id'] = $user->rt_id;
        }
        if (isset($data['rt_id']) && $user && !$user->isSuperAdmin() && (int)$data['rt_id'] !== (int)$user->rt_id) abort(403,'Tidak dapat pindah KK ke RT lain.');
        if (!empty($data['kepala_keluarga_id'])) {
            $w = \App\Models\Warga::find($data['kepala_keluarga_id']);
            if ($w) $data['kepala_keluarga'] = $w->nama;
        }
        // if kepala_keluarga_id is empty string/null and kepala_keluarga text is also empty, keep existing
        unset($data['kepala_keluarga_id']);
        $this->applyMasterAlamat($data);
        $oldData = $kk->toArray();
        $kk->update($data);
        $kk->refresh();
        $newData = $kk->toArray();
        $this->activityLogger->logUpdated($kk, $oldData, $newData);
        return $kk;
    }

    public function deleteKK(KartuKeluarga $kk)
    {
        $this->ensureRtAccess($kk);
        if ($kk->warga()->count() > 0) {
            return ['success' => false, 'message' => 'KK tidak dapat dihapus karena masih memiliki anggota keluarga!'];
        }
        $kk->loadMissing('warga');
        $this->activityLogger->logDeleted($kk);
        $kk->delete();
        return ['success' => true];
    }
}
