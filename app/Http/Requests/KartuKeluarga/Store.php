<?php

namespace App\Http\Requests\KartuKeluarga;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'no_kk' => 'required|string|size:16|unique:kartu_keluarga,no_kk',
            'kepala_keluarga' => 'nullable|string|max:255',
            'kepala_keluarga_id' => 'nullable|exists:warga,id',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'rt_id' => 'nullable|exists:rts,id',
            'dusun' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
        ];
    }
}
