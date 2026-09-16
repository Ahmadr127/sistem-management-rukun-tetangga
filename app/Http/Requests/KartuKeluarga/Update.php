<?php

namespace App\Http\Requests\KartuKeluarga;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $kk = $this->route('kartu_keluarga') ?? $this->route('kartu_keluargan') ?? $this->route('kk');
        $id = is_object($kk) ? $kk->id : $kk;
        return [
            'no_kk' => ['required','string','size:16', Rule::unique('kartu_keluarga','no_kk')->ignore($id)],
            'kepala_keluarga' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'dusun' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
        ];
    }
}
