<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kartu_keluarga_id' => 'nullable|exists:kartu_keluarga,id',
            'rt_id' => 'nullable|exists:rts,id',
            'alamat_detail' => 'nullable|string|max:255',
            'nik' => 'required|string|size:16|unique:warga,nik',
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
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status_warga' => 'nullable|in:AKTIF,PINDAH,MENINGGAL',
        ];
    }
}
