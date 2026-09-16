<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventaris extends Model
{
    use HasFactory, Auditable;

    protected $table = 'inventaris';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'jumlah',
        'satuan',
        'kondisi',
        'lokasi',
        'harga_satuan',
        'total_harga',
        'sumber_dana',
        'tanggal_pengadaan',
        'keterangan',
        'foto',
    ];

    protected $casts = [
        'tanggal_pengadaan' => 'date',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    protected static $autoAudit = true;

    public function peminjaman(): HasMany
    {
        return $this->hasMany(PeminjamanInventaris::class, 'inventaris_id');
    }

    public function peminjamanAktif(): HasMany
    {
        return $this->hasMany(PeminjamanInventaris::class, 'inventaris_id')->where('status', 'DIPINJAM');
    }

    public function getStokTersediaAttribute(): int
    {
        $dipinjam = $this->pemimpinActiveCount();
        return max(0, $this->jumlah - $dipinjam);
    }

    private function pemimpinActiveCount(): int
    {
        return $this->peminjamanAktif()->sum('jumlah_pinjam');
    }

    public function getIsTersediaAttribute(): bool
    {
        return $this->stok_tersedia > 0;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_barang', 'like', "%{$search}%")
              ->orWhere('kode_barang', 'like', "%{$search}%")
              ->orWhere('kategori', 'like', "%{$search}%");
        });
    }
}
