<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KartuKeluarga extends Model
{
    use HasFactory, Auditable;

    protected $table = 'kartu_keluarga';

    protected $fillable = [
        'no_kk',
        'kepala_keluarga',
        'alamat',
        'rt',
        'rw',
        'rt_id',
        'dusun',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
    ];

    protected static $autoAudit = true;

    public function warga(): HasMany
    {
        return $this->hasMany(Warga::class, 'kartu_keluarga_id');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(Warga::class, 'kartu_keluarga_id');
    }

    public function getJumlahAnggotaAttribute(): int
    {
        return $this->warga()->count();
    }

    public function getAlamatLengkapAttribute(): string
    {
        $parts = array_filter([$this->alamat, $this->dusun, $this->desa]);
        $rtRw = trim(($this->rt ? "RT {$this->rt}" : '') . ($this->rw ? " / RW {$this->rw}" : ''));
        if ($rtRw) $parts[] = $rtRw;
        return implode(', ', $parts) ?: '-';
    }

    public function rtRelation(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function scopeByRt($query, $rt)
    {
        // Support both legacy string rt and new rt_id
        if (is_numeric($rt)) {
            return $query->where('rt_id', $rt);
        }
        return $query->where('rt', $rt);
    }

    public function scopeByRw($query, $rw)
    {
        return $query->where('rw', $rw);
    }

    public function scopeForUser($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            return $query->where('rt_id', $user->rt_id);
        }
        return $query;
    }
}
