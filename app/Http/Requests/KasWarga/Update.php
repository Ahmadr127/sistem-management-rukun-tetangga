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
            'nama' => 'required|string|max:100',
            'periode_type' => 'required|in:weekly,monthly,yearly',
            'nominal' => 'required|numeric|min:0',
            'target_type' => 'required|in:kk,perorangan',
            'deskripsi' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
