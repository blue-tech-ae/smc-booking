<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    public function rules(): array
    {
        return [
            'role_id' => 'required|integer|exists:roles,id',
        ];
    }
}
