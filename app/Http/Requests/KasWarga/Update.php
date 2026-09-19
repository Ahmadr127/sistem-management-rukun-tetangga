<?php

namespace App\Http\Requests\KasWarga;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'rt_id' => 'nullable|exists:rts,id',
            'warga_id' => 'required|exists:warga,id',
            'periode_type' => 'required|in:weekly,monthly',
            'periode' => 'required|string|max:20',
            'nominal' => 'required|numeric|min:0',
            'tanggal_bayar' => 'nullable|date',
            'status' => 'required|in:belum_bayar,sudah_bayar',
            'catatan' => 'nullable|string|max:500',
        ];
    }
}
