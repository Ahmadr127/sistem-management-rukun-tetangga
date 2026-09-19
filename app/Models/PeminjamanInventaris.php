<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PeminjamanInventaris extends Model
{
    use HasFactory, Auditable;

    protected $table = 'peminjaman_inventaris';

    protected $fillable = [
        'inventaris_id',
        'warga_id',
        'nama_peminjam',
        'no_hp_peminjam',
        'jumlah_pinjam',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'status',
        'keperluan',
        'keterangan',
        'kondisi_kembali',
        'foto_kembali',
        'disetujui_oleh',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
    ];

    protected static $autoAudit = true;

    public function inventaris(): BelongsTo
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function getNamaPeminjamDisplayAttribute(): string
    {
        if ($this->warga) return $this->warga->nama;
        return $this->nama_peminjam ?? '-';
    }

    public function getIsTerlambatAttribute(): bool
    {
        if ($this->status === 'DIKEMBALIKAN') return false;
        if (!$this->tanggal_kembali_rencana) return false;
        return Carbon::now()->gt(Carbon::parse($this->tanggal_kembali_rencana));
    }

    public function getHariTerlambatAttribute(): int
    {
        if (!$this->is_terlambat) return 0;
        return (int) Carbon::now()->diffInDays(Carbon::parse($this->tanggal_kembali_rencana));
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'DIPINJAM');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'DIPINJAM')
                     ->where('tanggal_kembali_rencana', '<', now()->toDateString());
    }

    public function getFotoKembaliUrlAttribute(): ?string
    {
        $first = $this->fotoKembaliArray[0] ?? null;
        if ($first && \Illuminate\Support\Facades\Storage::disk('public')->exists($first)) {
            return \Illuminate\Support\Facades\Storage::url($first);
        }
        if ($this->attributes['foto_kembali'] && !str_starts_with($this->attributes['foto_kembali'], '[') && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->attributes['foto_kembali'])) {
            return \Illuminate\Support\Facades\Storage::url($this->attributes['foto_kembali']);
        }
        return null;
    }

    public function getFotoKembaliArrayAttribute(): array
    {
        $val = $this->attributes['foto_kembali'] ?? null;
        if (!$val) return [];
        $decoded = json_decode($val, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }
        return [$val];
    }

    public function getFotoKembaliUrlsAttribute(): array
    {
        return array_values(array_filter(array_map(function($p){
            return \Illuminate\Support\Facades\Storage::disk('public')->exists($p) ? \Illuminate\Support\Facades\Storage::url($p) : null;
        }, $this->fotoKembaliArray)));
    }
}
