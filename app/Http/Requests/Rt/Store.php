<?php

namespace App\Http\Requests\Rt;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_rt' => 'required|string|max:10|unique:rts,kode_rt',
            'nama_rt' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string|max:500',
        ];
    }
}
