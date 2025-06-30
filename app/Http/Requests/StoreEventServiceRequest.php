<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(?string $type = null): array
    {
        $rules = [
            'service_type' => 'required|in:catering,photography,security',
            'assigned_to' => 'required|exists:users,id',
            'details' => 'required|array',
        ];

        $type = $type ?? $this->input('service_type');

        if ($type === 'catering') {
            $rules['details.required'] = 'required|boolean';
            $rules['details.people'] = 'nullable|required_if:details.required,true|integer|min:1';
            $rules['details.dietary_requirements'] = 'nullable|string';
            $rules['details.notes'] = 'nullable|string';
        } elseif ($type === 'photography') {
            $rules['details.required'] = 'required|boolean';
            $rules['details.type'] = 'nullable|required_if:details.required,true|string';
        } elseif ($type === 'security') {
            $rules['details.required'] = 'required|boolean';
            $rules['details.guards'] = 'nullable|required_if:details.required,true|integer|min:1';
            $rules['details.risk_assessment'] = 'nullable|string';
            $rules['details.notes'] = 'nullable|string';
        }

        return $rules;
    }
}
