<?php
namespace App\Http\Requests\AlamatRt;
use Illuminate\Foundation\Http\FormRequest;
class Store extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'rt_id' => 'required|exists:rts,id|unique:alamat_rt,rt_id',
            'alamat' => 'nullable|string|max:255',
            'rw' => 'nullable|string|max:5',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ];
    }
}
