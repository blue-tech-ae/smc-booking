<?php

namespace App\Http\Requests;

use App\Enums\Campus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:locations,name|max:255',
            'description' => 'nullable|string',
            'campus' => ['required', 'string', Rule::in(array_column(Campus::cases(), 'value'))],
        ];
    }
}

