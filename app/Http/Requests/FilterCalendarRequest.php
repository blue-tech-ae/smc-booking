<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,approved,rejected,cancelled,draft',
            'location_id' => 'nullable|exists:locations,id',
            'date' => 'nullable|date', // مثال: 2025-06-10
            'search' => 'nullable|string|max:255',
        ];
    }
}
