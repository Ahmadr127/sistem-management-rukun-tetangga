<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'display_name' => 'required|string|max:150',
            'type' => 'required|in:text,textarea,image',
            'value' => 'nullable|string|max:65535',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'description' => 'nullable|string|max:255',
        ];
    }
}
