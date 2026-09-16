<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keuangan extends Model
{
    use HasFactory, Auditable;

    protected $table = 'keuangan';

    protected $fillable = [
        'tanggal',
        'jenis',
        'kategori',
        'jumlah',
        'sumber_dana',
        'keterangan',
        'deskripsi',
        'bukti',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    protected static $autoAudit = true;

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePemasukan($query)
    {
        return $query->where('jenis', 'PEMASUKAN');
    }

    public function scopePengeluaran($query)
    {
        return $query->where('jenis', 'PENGELUARAN');
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        if ($from) $query->where('tanggal', '>=', $from);
        if ($to) $query->where('tanggal', '<=', $to);
        return $query;
    }

    public function getFormattedJumlahAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->jumlah, 0, ',', '.');
    }
}
