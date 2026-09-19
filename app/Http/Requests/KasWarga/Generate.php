<?php

namespace App\Http\Requests\KasWarga;

use Illuminate\Foundation\Http\FormRequest;

class Generate extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'rt_id' => 'nullable|exists:rts,id',
            'periode_type' => 'required|in:weekly,monthly',
            'periode' => 'required|string|max:20',
            'nominal' => 'required|numeric|min:0',
        ];
    }
}
