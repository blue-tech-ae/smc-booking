<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HandleCancellationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:accepted,rejected',
        ];
    }
}
