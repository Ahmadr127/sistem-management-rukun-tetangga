<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Warga extends Model
{
    use HasFactory, Auditable;

    protected $table = 'warga';

    protected $fillable = [
        'kartu_keluarga_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'pendidikan',
        'pekerjaan',
        'status_perkawinan',
        'hubungan_keluarga',
        'kewarganegaraan',
        'golongan_darah',
        'nama_ayah',
        'nama_ibu',
        'no_hp',
        'status_warga',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    protected static $autoAudit = true;

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function kk(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(MutasiWarga::class, 'warga_id');
    }

    public function peminjamanInventaris(): HasMany
    {
        return $this->hasMany(PeminjamanInventaris::class, 'warga_id');
    }

    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir ? Carbon::parse($this->tanggal_lahir)->age : null;
    }

    public function getRtAttribute(): ?string
    {
        return $this->kartuKeluarga?->rt;
    }

    public function getRwAttribute(): ?string
    {
        return $this->kartuKeluarga?->rw;
    }

    public function scopeAktif($query)
    {
        return $query->where('status_warga', 'AKTIF');
    }

    public function scopeByRt($query, $rt)
    {
        return $query->whereHas('kartuKeluarga', fn($q) => $q->where('rt', $rt));
    }

    public function scopeByRw($query, $rw)
    {
        return $query->whereHas('kartuKeluarga', fn($q) => $q->where('rw', $rw));
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('pekerjaan', 'like', "%{$search}%");
        });
    }
}
