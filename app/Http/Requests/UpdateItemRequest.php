<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('item');
        return auth()->check()
            && ($this->user()->isAdmin() || $item->donor_id === $this->user()->id);
    }

    public function rules(): array
    {
        return [
            'title'           => ['sometimes', 'required', 'string', 'max:200'],
            'category_id'     => ['sometimes', 'required', 'exists:categories,id'],
            'description'     => ['nullable', 'string', 'max:2000'],
            'condition'       => ['sometimes', 'required', 'in:baik,cukup,perlu_servis'],
            'quantity'        => ['sometimes', 'required', 'integer', 'min:1'],
            'province'        => ['sometimes', 'required', 'string', 'max:100'],
            'city'            => ['sometimes', 'required', 'string', 'max:100'],
            'district'        => ['sometimes', 'required', 'string', 'max:100'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
