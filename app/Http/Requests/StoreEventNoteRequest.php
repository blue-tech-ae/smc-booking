<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Catering', 'Photography', 'Security']);
    }

    public function rules(): array
    {
        return [
            'note' => 'required|string|max:1000',
        ];
    }
}
