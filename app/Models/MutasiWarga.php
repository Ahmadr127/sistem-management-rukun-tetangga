<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiWarga extends Model
{
    use HasFactory, Auditable;

    protected $table = 'mutasi_warga';

    protected $fillable = [
        'warga_id',
        'jenis_mutasi',
        'tanggal_mutasi',
        'kk_lama_id',
        'kk_baru_id',
        'alamat_asal',
        'alamat_tujuan',
        'alasan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
    ];

    protected static $autoAudit = true;

    public const JENIS = ['LAHIR', 'MASUK', 'KELUAR', 'PINDAH_KK', 'MENINGGAL'];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function kkLama(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kk_lama_id');
    }

    public function kkBaru(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kk_baru_id');
    }

    public function getLabelJenisAttribute(): string
    {
        return match($this->jenis_mutasi) {
            'LAHIR' => 'Kelahiran',
            'MASUK' => 'Masuk',
            'KELUAR' => 'Keluar',
            'PINDAH_KK' => 'Pindah KK',
            'MENINGGAL' => 'Kematian',
            default => $this->jenis_mutasi,
        };
    }
}
