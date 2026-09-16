<?php

namespace App\Http\Requests\Inventaris;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'kode_barang' => 'required|string|max:50|unique:inventaris,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'nullable|string|max:20',
            'kondisi' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT,HILANG',
            'lokasi' => 'nullable|string|max:255',
            'harga_satuan' => 'nullable|numeric|min:0',
            'sumber_dana' => 'nullable|string|max:255',
            'tanggal_pengadaan' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ];
    }
}
