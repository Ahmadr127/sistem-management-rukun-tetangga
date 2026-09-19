<?php

namespace App\Http\Services;

use App\Models\Warga;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Storage;

class WargaService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getWarga(array $filters = [])
    {
        $query = Warga::with(['kartuKeluarga','rt']);

        // RT scoping: RT users only see their RT
        if ($user = auth()->user()) {
            if (!$user->isSuperAdmin() && $user->rt_id) {
                $query->where('rt_id', $user->rt_id);
            } elseif (!empty($filters['rt_id'])) {
                $query->where('rt_id', $filters['rt_id']);
            } elseif (!empty($filters['rt']) && is_numeric($filters['rt'])) {
                $query->where('rt_id', $filters['rt']);
            }
            // superadmin can filter by rt_id, legacy rt string handled below
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('pekerjaan', 'like', "%{$search}%");
            });
        }
        if (!empty($filters['rt']) && !is_numeric($filters['rt'])) {
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

    public function ensureRtAccess(Warga $warga): void
    {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            if ((int)$warga->rt_id !== (int)$user->rt_id) {
                abort(403, 'Akses ditolak: warga bukan dari RT Anda.');
            }
        }
    }

    public function createWarga(array $data)
    {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            $data['rt_id'] = $user->rt_id;
        }
        if (empty($data['rt_id']) && !empty($data['kartu_keluarga_id'])) {
            $kk = \App\Models\KartuKeluarga::find($data['kartu_keluarga_id']);
            if ($kk && $kk->rt_id) $data['rt_id'] = $kk->rt_id;
        }
        if ($user && !$user->isSuperAdmin() && $user->rt_id && (int)($data['rt_id'] ?? 0) !== (int)$user->rt_id) {
            abort(403, 'Tidak dapat membuat warga untuk RT lain.');
        }
        // handle foto upload
        if (isset($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {
            $data['foto'] = $data['foto']->store('warga/foto', 'public');
        } else {
            unset($data['foto']);
        }
        unset($data['remove_foto']);
        $warga = Warga::create($data);
        $this->syncKepalaKeluarga($warga);
        return $warga;
    }

    private function syncKepalaKeluarga(Warga $warga): void
    {
        // If warga is Kepala Keluarga, sync KK's kepala_keluarga field
        if (($warga->hubungan_keluarga ?? '') === 'Kepala Keluarga' && $warga->kartu_keluarga_id) {
            $kk = \App\Models\KartuKeluarga::find($warga->kartu_keluarga_id);
            if ($kk && $kk->kepala_keluarga !== $warga->nama) {
                $kk->update(['kepala_keluarga' => $warga->nama]);
            }
        }
        // Also if KK has no kepala_keluarga yet, set it to first anggota if none is Kepala Keluarga
        if ($warga->kartu_keluarga_id) {
            $kk = \App\Models\KartuKeluarga::find($warga->kartu_keluarga_id);
            if ($kk && empty($kk->kepala_keluarga)) {
                $kk->update(['kepala_keluarga' => $warga->nama]);
            }
        }
    }

    public function updateWarga(Warga $warga, array $data)
    {
        $this->ensureRtAccess($warga);
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            $data['rt_id'] = $user->rt_id;
        }
        if (isset($data['rt_id']) && $user && !$user->isSuperAdmin() && (int)$data['rt_id'] !== (int)$user->rt_id) {
            abort(403, 'Tidak dapat memindahkan warga ke RT lain.');
        }
        $oldData = $warga->toArray();
        $oldData['kartu_keluarga'] = $warga->kartuKeluarga?->toArray();

        // handle foto remove
        $removeFoto = !empty($data['remove_foto']);
        unset($data['remove_foto']);
        if ($removeFoto && $warga->foto) {
            Storage::disk('public')->delete($warga->foto);
            $data['foto'] = null;
        }
        // handle foto upload
        if (isset($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {
            if ($warga->foto) Storage::disk('public')->delete($warga->foto);
            $data['foto'] = $data['foto']->store('warga/foto', 'public');
        } elseif (array_key_exists('foto', $data) && $data['foto'] === null) {
            unset($data['foto']);
        } elseif (!isset($data['foto'])) {
            unset($data['foto']);
        }

        $warga->update($data);
        $warga->refresh();
        $warga->load('kartuKeluarga');
        $this->syncKepalaKeluarga($warga);

        $newData = $warga->toArray();
        $newData['kartu_keluarga'] = $warga->kartuKeluarga?->toArray();

        $this->activityLogger->logUpdated($warga, $oldData, $newData);

        return $warga;
    }

    public function deleteWarga(Warga $warga)
    {
        $this->ensureRtAccess($warga);
        $warga->loadMissing('kartuKeluarga');
        if ($warga->foto) Storage::disk('public')->delete($warga->foto);
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
