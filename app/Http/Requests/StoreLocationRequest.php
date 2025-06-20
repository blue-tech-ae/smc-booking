<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:locations,name|max:255',
        ];
    }
}

