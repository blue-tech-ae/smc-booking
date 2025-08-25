<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MatchesServiceRole;

class StoreEventServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(?string $type = null): array
    {
        $type = $type ?? $this->input('service_type');
        $rules = [
            'service_type' => 'required|in:catering,photography,security',
            'assigned_to' => ['nullable', 'exists:users,id', new MatchesServiceRole($type)],
            'details' => 'required|array',
        ];

        if ($type === 'catering') {
            // $rules['details.required'] = 'required|boolean';
            $rules['details.people'] = 'nullable|required_if:details.required,true|integer|min:1';
            $rules['details.dietary_requirements'] = 'nullable|string';
            $rules['details.notes'] = 'nullable|string';
        } elseif ($type === 'photography') {
            // $rules['details.required'] = 'required|boolean';
            $rules['details.photography_type_id'] = 'nullable|exists:photography_types,id';
            $rules['details.notes'] = 'nullable|string';
        } elseif ($type === 'security') {
            // $rules['details.required'] = 'required|boolean';
            $rules['details.guards'] = 'nullable|integer|min:1';
            $rules['details.risk_assessment'] = 'nullable|string';
            $rules['details.notes'] = 'nullable|string';
        }

        return $rules;
    }
}
