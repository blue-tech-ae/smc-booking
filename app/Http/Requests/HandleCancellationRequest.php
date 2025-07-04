<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HandleCancellationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:accepted,rejected',
        ];
    }
}
