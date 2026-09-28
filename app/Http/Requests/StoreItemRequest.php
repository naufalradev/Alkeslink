<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isDonor();
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:200'],
            'category_id'     => ['required', 'exists:categories,id'],
            'description'     => ['nullable', 'string', 'max:2000'],
            'condition'       => ['required', 'in:baik,cukup,perlu_servis'],
            'quantity'        => ['required', 'integer', 'min:1', 'max:99'],
            'province'        => ['required', 'string', 'max:100'],
            'city'            => ['required', 'string', 'max:100'],
            'district'        => ['required', 'string', 'max:100'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'images'          => ['required', 'array', 'min:1', 'max:5'],
            'images.*'        => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Mohon unggah minimal 1 foto barang.',
            'images.*.image'  => 'File harus berupa gambar (JPG/PNG/WebP).',
            'images.*.max'    => 'Ukuran foto maksimal 2 MB per file.',
            'category_id.exists' => 'Kategori tidak valid.',
        ];
    }
}
