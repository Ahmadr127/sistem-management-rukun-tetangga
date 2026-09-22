<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\Store;
use App\Http\Requests\Setting\Update;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /** Daftar pengaturan (logo, nama sistem, dll). */
    public function index(Request $request)
    {
        $settings = Setting::query()
            ->when($request->get('search'), fn ($q, $s) => $q->where(
                fn ($w) => $w->where('key', 'like', "%{$s}%")->orWhere('display_name', 'like', "%{$s}%")
            ))
            ->orderBy('is_system', 'desc')
            ->orderBy('key')
            ->paginate(15)
            ->withQueryString();

        return view('settings.index', compact('settings'));
    }

    public function create()
    {
        return view('settings.create');
    }

    public function store(Store $request)
    {
        $data = $request->validated();

        if ($data['type'] === 'image') {
            $data['value'] = $request->file('logo')->store('logos', 'public');
        }

        unset($data['logo']);
        Setting::create($data);

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil ditambahkan!');
    }

    public function show(Setting $setting)
    {
        return view('settings.show', compact('setting'));
    }

    public function edit(Setting $setting)
    {
        return view('settings.edit', compact('setting'));
    }

    public function update(Update $request, Setting $setting)
    {
        $data = $request->validated();

        if ($data['type'] === 'image') {
            if ($request->hasFile('logo')) {
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }
                $data['value'] = $request->file('logo')->store('logos', 'public');
            } else {
                // Tanpa file baru: pertahankan path lama
                unset($data['value']);
            }
        } elseif ($setting->isImage() && $setting->value) {
            // Pindah dari image ke teks: hapus file yatim
            Storage::disk('public')->delete($setting->value);
        }

        unset($data['logo']);
        $setting->update($data);

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }

    public function destroy(Setting $setting)
    {
        if ($setting->is_system) {
            return redirect()->route('settings.index')->with('error', 'Pengaturan sistem tidak dapat dihapus!');
        }

        $setting->delete();

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil dihapus!');
    }
}
