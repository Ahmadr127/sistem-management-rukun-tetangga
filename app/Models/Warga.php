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
        'rt_id',
        'alamat_detail',
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
        'foto',
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

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
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

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto)) {
            return \Illuminate\Support\Facades\Storage::url($this->foto);
        }
        return null;
    }

    public function getInisialAttribute(): string
    {
        $parts = explode(' ', trim($this->nama ?? ''));
        if (count($parts) >= 2) return strtoupper(substr($parts[0],0,1).substr($parts[1],0,1));
        return strtoupper(substr($this->nama ?? 'W',0,1));
    }

    // Legacy string rt/rw from KK (avoid collision with rt relation).
    // Use $warga->rt_kode / $warga->rw_kode if needed.
    public function getRtKodeAttribute(): ?string
    {
        return $this->kartuKeluarga?->rt;
    }

    public function getRwKodeAttribute(): ?string
    {
        return $this->kartuKeluarga?->rw;
    }

    public function scopeAktif($query)
    {
        return $query->where('status_warga', 'AKTIF');
    }

    public function scopeByRt($query, $rt)
    {
        if (is_numeric($rt)) {
            return $query->where('rt_id', $rt);
        }
        return $query->whereHas('kartuKeluarga', fn($q) => $q->where('rt', $rt));
    }

    public function scopeByRw($query, $rw)
    {
        return $query->whereHas('kartuKeluarga', fn($q) => $q->where('rw', $rw));
    }

    public function scopeForUser($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            return $query->where('rt_id', $user->rt_id);
        }
        return $query;
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
