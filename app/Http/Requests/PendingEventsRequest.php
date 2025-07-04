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
            'location_id' => 'nullable|exists:locations,id',
            'start_date' => 'nullable|date',
            'title' => 'nullable|string|max:255',
            'organizer_email' => 'nullable|email',
        ];
    }
}
