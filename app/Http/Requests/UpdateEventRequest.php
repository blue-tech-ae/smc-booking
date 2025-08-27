<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Event;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Campus;

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
            'department' => 'sometimes|string|max:255',
            'campus' => ['sometimes', new Enum(Campus::class)],
            'security_note' => 'nullable|string',
            'setup_details' => 'nullable|array',
            'setup_details.av_technician' => 'nullable|boolean',
            'setup_details.av_equipment' => 'nullable|array',
            'setup_details.av_equipment.*' => 'string',
            'setup_details.chairs' => 'nullable|integer|min:0',
            'setup_details.tables' => 'nullable|integer|min:0',
            'setup_details.table_type' => 'nullable|string',
            'gift_details' => 'nullable|array',
            'gift_details.required' => 'nullable|boolean',
            'gift_details.quantity' => 'nullable|integer|min:1',
            'gift_details.delivery_location' => 'nullable|string',
            'gift_details.type' => 'nullable|string',
            'floral_details' => 'nullable|array',
            'floral_details.required' => 'nullable|boolean',
            'floral_details.delivery_time' => 'nullable|date',
            'floral_details.amount' => 'nullable|numeric|min:0',
            'floral_details.theme' => 'nullable|string',
            'start_time' => 'sometimes|date|after_or_equal:now',
            'end_time' => 'sometimes|date|after_or_equal:start_time',
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

            $campus = $this->input('campus', $event->campus ?? null);
            if ($campus && $locationId) {
                $matches = \App\Models\Location::where('id', $locationId)
                    ->where('campus', $campus)
                    ->exists();
                if (!$matches) {
                    $validator->errors()->add('location_id', 'The selected location does not belong to the chosen campus.');
                }
            }
        });
    }
}