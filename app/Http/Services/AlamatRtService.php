<?php
namespace App\Http\Services;
use App\Models\AlamatRt;
use App\Services\ActivityLogService;
class AlamatRtService
{
    public function __construct(protected ActivityLogService $activityLogger) {}
    public function getAlamat(array $filters = []) {
        $q = AlamatRt::with('rt');
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function($qq) use($s){
                $qq->where('alamat','like',"%{$s}%")
                  ->orWhere('kelurahan','like',"%{$s}%")
                  ->orWhere('kecamatan','like',"%{$s}%")
                  ->orWhereHas('rt', fn($r)=> $r->where('kode_rt','like',"%{$s}%"));
            });
        }
        $perPage = in_array((int)($filters['per_page'] ?? 10), [5,10,25,50,100]) ? (int)($filters['per_page']??10):10;
        return $q->latest()->paginate($perPage)->withQueryString();
    }
    public function createAlamat(array $data): AlamatRt {
        return AlamatRt::create($data);
    }
    public function updateAlamat(AlamatRt $alamat, array $data): AlamatRt {
        $old = $alamat->toArray();
        $alamat->update($data);
        $alamat->refresh();
        $this->activityLogger->logUpdated($alamat,$old,$alamat->toArray());
        return $alamat;
    }
    public function deleteAlamat(AlamatRt $alamat): bool {
        $alamat->loadMissing('rt');
        $this->activityLogger->logDeleted($alamat);
        return $alamat->delete();
    }
}
