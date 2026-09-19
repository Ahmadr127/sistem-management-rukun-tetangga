<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlamatRt extends Model
{
    use HasFactory, Auditable;

    protected $table = 'alamat_rt';

    protected $fillable = [
        'rt_id',
        'alamat',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static $autoAudit = true;

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function toApiArray(): array
    {
        return [
            'rt_id' => $this->rt_id,
            'rt' => $this->rt?->kode_rt ?? null,
            'rw' => $this->rw,
            'alamat' => $this->alamat,
            'kelurahan' => $this->kelurahan,
            'kecamatan' => $this->kecamatan,
            'kota' => $this->kota,
            'provinsi' => $this->provinsi,
            'kode_pos' => $this->kode_pos,
        ];
    }
}
