<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminBookingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,service_approved,approved,rejected,cancelled,draft',
            'location_id' => 'nullable|exists:locations,id',
            'start_date' => 'nullable|date',
            'organizer_email' => 'nullable|email',
            'title' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255',
        ];
    }
}
