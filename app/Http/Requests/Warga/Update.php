<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $warga = $this->route('warga') ?? $this->route('wargan');
        $id = is_object($warga) ? $warga->id : $warga;
        return [
            'kartu_keluarga_id' => 'nullable|exists:kartu_keluarga,id',
            'nik' => ['required','string','size:16', Rule::unique('warga','nik')->ignore($id)],
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'nullable|string|max:50',
            'hubungan_keluarga' => 'nullable|string|max:50',
            'kewarganegaraan' => 'nullable|string|max:10',
            'golongan_darah' => 'nullable|string|max:3',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'status_warga' => 'nullable|in:AKTIF,PINDAH,MENINGGAL',
        ];
    }
}
