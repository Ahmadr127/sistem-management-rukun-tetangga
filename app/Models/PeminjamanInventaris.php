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
}
