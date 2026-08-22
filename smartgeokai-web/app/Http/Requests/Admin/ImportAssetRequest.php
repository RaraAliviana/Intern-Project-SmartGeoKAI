<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // Max 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required' => 'Silakan pilih file CSV yang akan diunggah.',
            'csv_file.mimes'    => 'File yang diunggah harus berformat CSV.',
            'csv_file.max'      => 'Ukuran file CSV maksimal 5MB.',
        ];
    }
}