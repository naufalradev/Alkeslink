<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequestForm extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isRecipient();
    }

    public function rules(): array
    {
        return [
            'item_id'      => [
                'required',
                'exists:items,id',
                // Prevent duplicate requests on the same item by the same user
                Rule::unique('donation_requests')->where(function ($query) {
                    return $query
                        ->where('recipient_id', $this->user()->id)
                        ->whereIn('status', ['pending', 'approved', 'handover']);
                }),
            ],
            'reason'       => ['required', 'string', 'min:20', 'max:1000'],
            'support_doc'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.min'        => 'Alasan minimal 20 karakter.',
            'item_id.unique'    => 'Anda sudah memiliki pengajuan aktif untuk barang ini.',
            'support_doc.mimes' => 'Dokumen harus berformat JPG, PNG, atau PDF.',
            'support_doc.max'   => 'Ukuran dokumen maksimal 3 MB.',
        ];
    }
}
