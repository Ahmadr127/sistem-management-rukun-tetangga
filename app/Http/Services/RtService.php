<?php

namespace App\Http\Services;

use App\Models\Rt;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;

class RtService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getRts(array $filters = [])
    {
        $query = Rt::query()->withCount(['warga', 'kartuKeluarga']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('kode_rt', 'like', "%{$search}%")
                  ->orWhere('nama_rt', 'like', "%{$search}%");
            });
        }
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool)$filters['is_active']);
        }

        $perPage = in_array((int)($filters['per_page'] ?? 10), [5,10,25,50,100]) ? (int)($filters['per_page'] ?? 10) : 10;
        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function createRt(array $data): Rt
    {
        return Rt::create($data);
    }

    public function updateRt(Rt $rt, array $data): Rt
    {
        $old = $rt->toArray();
        $rt->update($data);
        $rt->refresh();
        $this->activityLogger->logUpdated($rt, $old, $rt->toArray());
        return $rt;
    }

    public function deleteRt(Rt $rt): bool
    {
        // prevent delete if has warga or users
        if ($rt->warga()->exists() || $rt->users()->exists()) {
            throw new \Exception('RT tidak dapat dihapus karena masih memiliki warga atau user terkait.');
        }
        $rt->loadMissing('warga');
        $this->activityLogger->logDeleted($rt);
        return $rt->delete();
    }

    public function toggleStatus(Rt $rt): Rt
    {
        $rt->update(['is_active' => !$rt->is_active]);
        return $rt;
    }
}
