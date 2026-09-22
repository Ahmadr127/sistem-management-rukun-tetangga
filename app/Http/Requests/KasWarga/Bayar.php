<?php

namespace App\Http\Requests\KasWarga;

use Illuminate\Foundation\Http\FormRequest;

class Bayar extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'nominal_bayar' => 'required|numeric|min:0',
            'kartu_keluarga_id' => 'nullable|exists:kartu_keluarga,id',
            'warga_id' => 'nullable|exists:warga,id',
            'catatan' => 'nullable|string|max:255',
        ];
    }
}
