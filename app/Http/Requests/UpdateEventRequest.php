<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Event;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'details' => 'nullable|string',
            'expected_attendance' => 'nullable|integer|min:1',
            'organizer_name' => 'nullable|string|max:255',
            'organizer_email' => 'nullable|email|max:255',
            'organizer_phone' => 'nullable|string|max:20',
            'location_id' => 'sometimes|exists:locations,id',
            'start_time' => 'sometimes|date|after_or_equal:now',
            'end_time' => 'sometimes|date|after:start_time',
            'services' => 'sometimes|array',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $event = $this->route('event');
            $locationId = $this->input('location_id', $event->location_id ?? null);
            $startTime = $this->input('start_time', $event->start_time ?? null);
            $endTime = $this->input('end_time', $event->end_time ?? null);

            if (!$locationId || !$startTime || !$endTime) {
                return;
            }

            $conflict = Event::where('location_id', $locationId)
                ->where('id', '!=', $event->id)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->exists();

            if ($conflict) {
                $validator->errors()->add('start_time', 'The selected location is unavailable for the chosen time.');
            }
        });
    }
}
