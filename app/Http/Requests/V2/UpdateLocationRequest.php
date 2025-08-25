<?php

namespace App\Http\Requests\V2;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Admin', 'Super Admin']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:locations,name,' . $this->location->id . '|max:255',
            'campus_id' => 'required|exists:campuses,id',
            'description' => 'nullable|string',
        ];
    }
}
