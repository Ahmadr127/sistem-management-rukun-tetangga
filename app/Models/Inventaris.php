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

    public function getFotoUrlAttribute(): ?string
    {
        $first = $this->fotoArray[0] ?? null;
        if ($first && \Illuminate\Support\Facades\Storage::disk('public')->exists($first)) {
            return \Illuminate\Support\Facades\Storage::url($first);
        }
        // fallback for legacy single string not JSON
        if ($this->attributes['foto'] && !str_starts_with($this->attributes['foto'], '[') && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->attributes['foto'])) {
            return \Illuminate\Support\Facades\Storage::url($this->attributes['foto']);
        }
        return null;
    }

    public function getFotoArrayAttribute(): array
    {
        $val = $this->attributes['foto'] ?? null;
        if (!$val) return [];
        // try json decode
        $decoded = json_decode($val, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }
        // single string legacy
        return [$val];
    }

    public function getFotoUrlsAttribute(): array
    {
        return array_values(array_filter(array_map(function($p){
            return \Illuminate\Support\Facades\Storage::disk('public')->exists($p) ? \Illuminate\Support\Facades\Storage::url($p) : null;
        }, $this->fotoArray)));
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
