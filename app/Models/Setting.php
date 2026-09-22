<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'display_name',
        'type',
        'value',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public const TYPES = ['text', 'textarea', 'image'];

    /** Ambil nilai setting berdasarkan key. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /** Simpan/buat nilai setting berdasarkan key. */
    public static function set(string $key, mixed $value): static
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        if (!$setting->exists) {
            $setting->display_name = $key;
            $setting->type = 'text';
        }
        $setting->save();

        return $setting;
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /** URL publik file (khusus tipe image), null bila belum ada / file hilang. */
    public function valueUrl(): ?string
    {
        if (!$this->isImage() || !$this->value) {
            return null;
        }

        return Storage::disk('public')->exists($this->value)
            ? Storage::url($this->value)
            : null;
    }

    /** URL logo sistem (upload) atau fallback logo bawaan. */
    public static function logoUrl(): string
    {
        return static::where('key', 'site_logo')->first()?->valueUrl()
            ?? asset('images/logo.png');
    }

    protected static function booted(): void
    {
        static::deleting(function (Setting $setting) {
            // Pengaturan sistem tidak boleh dihapus
            if ($setting->is_system) {
                return false;
            }
            if ($setting->isImage() && $setting->value) {
                Storage::disk('public')->delete($setting->value);
            }
        });
    }
}
