<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,approved,rejected,cancelled,draft',
            'location_id' => 'nullable|exists:locations,id',
            'month' => 'nullable|date_format:Y-m', // مثال: 2025-06
        ];
    }
}
