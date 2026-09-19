<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'jenis' => 'required|in:PEMASUKAN,PENGELUARAN',
            'kategori' => 'required|string|max:100',
            'jumlah' => 'required|numeric|min:0',
            'rt_id' => 'nullable|exists:rts,id',
            'sumber_dana' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'bukti' => 'nullable|string|max:255',
        ];
    }
}
