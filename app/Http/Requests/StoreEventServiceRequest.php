<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_type' => 'required|in:catering,photography,security',
            'assigned_to' => 'required|exists:users,id',
            'catering_required' => 'required|boolean',
            'catering_people' => 'nullable|required_if:catering_required,true|integer|min:1',
            'dietary_requirements' => 'nullable|string',
            'catering_notes' => 'nullable|string',

            'photography_required' => 'required|boolean',
            'photography_type' => 'nullable|required_if:photography_required,true|string',

            'security_required' => 'required|boolean',
            'security_guards' => 'nullable|required_if:security_required,true|integer|min:1',
            'risk_assessment' => 'nullable|string',
            'security_notes' => 'nullable|string',
        ];
    }
}
