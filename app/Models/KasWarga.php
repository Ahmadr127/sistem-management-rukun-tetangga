<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasWarga extends Model
{
    use HasFactory, Auditable;

    protected $table = 'kas_warga';

    protected $fillable = [
        'rt_id',
        'warga_id',
        'periode_type',
        'periode',
        'nominal',
        'tanggal_bayar',
        'status',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    protected static $autoAudit = true;

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForUser($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            return $query->where('rt_id', $user->rt_id);
        }
        return $query;
    }

    public function scopeByPeriode($query, $periode = null, $type = null)
    {
        if ($periode) $query->where('periode', $periode);
        if ($type) $query->where('periode_type', $type);
        return $query;
    }
}
