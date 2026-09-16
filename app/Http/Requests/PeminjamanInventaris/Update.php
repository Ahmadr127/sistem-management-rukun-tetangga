<?php

namespace App\Http\Requests\PeminjamanInventaris;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'inventaris_id' => 'required|exists:inventaris,id',
            'warga_id' => 'nullable|exists:warga,id',
            'nama_peminjam' => 'nullable|string|max:255',
            'no_hp_peminjam' => 'nullable|string|max:20',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'tanggal_kembali_aktual' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'status' => 'required|in:DIPINJAM,DIKEMBALIKAN,TERLAMBAT,HILANG,RUSAK',
            'keperluan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'kondisi_kembali' => 'nullable|string',
        ];
    }
}
