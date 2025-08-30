<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PendingEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    public function rules(): array
    {
        return [
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'title' => 'nullable|string|max:255',
            'organizer_email' => 'nullable|email',
            'status' => 'nullable|in:pending,approved,rejected,cancelled,draft',
            'search' => 'nullable|string|max:255',
        ];
    }
}
