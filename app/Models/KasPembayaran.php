<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasPembayaran extends Model
{
    use HasFactory, Auditable;

    protected $table = 'kas_pembayaran';

    protected $fillable = [
        'kas_jenis_id',
        'kartu_keluarga_id',
        'warga_id',
        'tanggal',
        'nominal_bayar',
        'waktu_bayar',
        'status',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_bayar' => 'datetime',
        'nominal_bayar' => 'decimal:2',
    ];

    protected static $autoAudit = true;

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(KasJenis::class, 'kas_jenis_id');
    }

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSudahBayar($query)
    {
        return $query->where('status', 'sudah_bayar');
    }
}
