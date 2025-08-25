<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterMyAssignmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Catering', 'Photography', 'Security']);
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,approved,rejected,cancelled,draft',
            'location_id' => 'nullable|exists:locations,id',
            'date' => 'nullable|date',
            'search' => 'nullable|string|max:255',
        ];
    }
}
