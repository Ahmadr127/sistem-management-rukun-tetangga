<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rt extends Model
{
    use HasFactory, Auditable;

    protected $table = 'rts';

    protected $fillable = [
        'kode_rt',
        'nama_rt',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static $autoAudit = true;

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'rt_id');
    }

    public function warga(): HasMany
    {
        return $this->hasMany(Warga::class, 'rt_id');
    }

    public function kartuKeluarga(): HasMany
    {
        return $this->hasMany(KartuKeluarga::class, 'rt_id');
    }

    public function keuangan(): HasMany
    {
        return $this->hasMany(Keuangan::class, 'rt_id');
    }

    public function alamatRt(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AlamatRt::class, 'rt_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->nama_rt ?? $this->kode_rt;
    }
}
