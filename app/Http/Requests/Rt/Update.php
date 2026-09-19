<?php

namespace App\Http\Requests\Rt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rtId = $this->route('rt')?->id ?? $this->route('rt');
        return [
            'kode_rt' => ['required','string','max:10', Rule::unique('rts','kode_rt')->ignore($rtId)],
            'nama_rt' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string|max:500',
        ];
    }
}
