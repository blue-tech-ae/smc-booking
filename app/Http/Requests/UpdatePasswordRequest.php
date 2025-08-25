<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'password' => 'required|string|min:8|confirmed',
        ];

        if ($this->user()?->password) {
            $rules['current_password'] = 'required|string';
        }

        return $rules;
    }
}
