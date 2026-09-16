<?php

namespace App\Http\Requests\PeminjamanInventaris;

use Illuminate\Foundation\Http\FormRequest;

class Kembalikan extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'tanggal_kembali_aktual' => 'nullable|date',
            'status' => 'nullable|in:DIKEMBALIKAN,HILANG,RUSAK',
            'kondisi_kembali' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ];
    }
}
