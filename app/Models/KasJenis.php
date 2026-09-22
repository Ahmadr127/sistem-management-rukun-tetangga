<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KasJenis extends Model
{
    use HasFactory, Auditable;

    protected $table = 'kas_jenis';

    protected $fillable = [
        'rt_id',
        'nama',
        'periode_type',
        'nominal',
        'target_type',
        'deskripsi',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static $autoAudit = true;

    public const PERIODE_LABELS = [
        'weekly' => 'Mingguan',
        'monthly' => 'Bulanan',
        'yearly' => 'Tahunan',
    ];

    public const TARGET_LABELS = [
        'kk' => 'KK',
        'perorangan' => 'Perorangan',
    ];

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(KasPembayaran::class, 'kas_jenis_id');
    }

    public function getPeriodeLabelAttribute(): string
    {
        return self::PERIODE_LABELS[$this->periode_type] ?? $this->periode_type;
    }

    public function getTargetLabelAttribute(): string
    {
        return self::TARGET_LABELS[$this->target_type] ?? $this->target_type;
    }

    public function isKk(): bool
    {
        return $this->target_type === 'kk';
    }

    public function scopeForUser($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            return $query->where('kas_jenis.rt_id', $user->rt_id);
        }
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
