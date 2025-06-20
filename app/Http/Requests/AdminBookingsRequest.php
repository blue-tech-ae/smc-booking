<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminBookingsRequest extends FormRequest
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
            'start_date' => 'nullable|date',
            'organizer_email' => 'nullable|email',
            'title' => 'nullable|string|max:255',
        ];
    }
}
