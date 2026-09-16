<?php

namespace App\Http\Requests\MutasiWarga;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'warga_id' => 'required|exists:warga,id',
            'jenis_mutasi' => 'required|in:LAHIR,MASUK,KELUAR,PINDAH_KK,MENINGGAL',
            'tanggal_mutasi' => 'required|date',
            'kk_lama_id' => 'nullable|exists:kartu_keluarga,id',
            'kk_baru_id' => 'nullable|exists:kartu_keluarga,id',
            'alamat_asal' => 'nullable|string|max:255',
            'alamat_tujuan' => 'nullable|string|max:255',
            'alasan' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ];
    }
}
