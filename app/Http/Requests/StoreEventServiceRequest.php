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
            $rules['details.external_guests'] = 'required|boolean';
            $rules['details.people'] = 'required|integer|min:1';
            $rules['details.service_time'] = 'required|date_format:H:i';
            $rules['details.food_types'] = 'required|array|min:1';
            $rules['details.food_types.*'] = 'string';
            $rules['details.coffee_station'] = 'required|boolean';
            $rules['details.beverages'] = 'nullable|array';
            $rules['details.beverages.*'] = 'string';
            $rules['details.dietary_requirements'] = 'nullable|array';
            $rules['details.dietary_requirements.*.type'] = 'required_with:details.dietary_requirements|string';
            $rules['details.dietary_requirements.*.count'] = 'required_with:details.dietary_requirements|integer|min:1';
            $rules['details.extra_notes'] = 'nullable|string';
        } elseif ($type === 'photography') {
            $rules['details.photographer_required'] = 'required_if:details.required,true|boolean';
            $rules['details.loan_camera_required'] = 'required_if:details.required,true|boolean';
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
