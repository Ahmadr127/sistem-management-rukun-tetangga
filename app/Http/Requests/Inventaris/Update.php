<?php

namespace App\Http\Requests\Inventaris;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $inv = $this->route('inventari') ?? $this->route('inventaris');
        $id = is_object($inv) ? $inv->id : $inv;
        return [
            'kode_barang' => ['required','string','max:50', Rule::unique('inventaris','kode_barang')->ignore($id)],
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
            'foto' => 'nullable|array',
            'foto.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072',
            'remove_foto' => 'nullable|array',
            'remove_foto.*' => 'string',
        ];
    }
}
