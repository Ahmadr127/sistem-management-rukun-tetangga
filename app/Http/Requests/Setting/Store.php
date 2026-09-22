<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|alpha_dash:ascii|max:100|unique:settings,key',
            'display_name' => 'required|string|max:150',
            'type' => 'required|in:text,textarea,image',
            'value' => 'nullable|string|max:65535',
            'logo' => 'required_if:type,image|nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required_if' => 'File logo wajib diunggah untuk tipe gambar.',
        ];
    }
}
