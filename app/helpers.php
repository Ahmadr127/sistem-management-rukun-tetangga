<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Ambil nilai pengaturan sistem berdasarkan key.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('site_logo_url')) {
    /**
     * URL logo sistem (hasil upload) atau logo bawaan.
     */
    function site_logo_url(): string
    {
        return Setting::logoUrl();
    }
}
