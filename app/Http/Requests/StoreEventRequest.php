<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LocationAvailable;
use App\Rules\EventLeadTime;
use App\Models\Event;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Campus;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'expected_attendance' => 'nullable|integer|min:1',
            'organizer_name' => 'nullable|string|max:255',
            'organizer_email' => 'nullable|email|max:255',
            'organizer_phone' => 'nullable|string|max:20',
            'location_id' => 'required|exists:locations,id',
            'department_id' => 'required|exists:departments,id',
            'campus' => ['required', new Enum(Campus::class)],
            'security_note' => 'nullable|string',
            'start_time' => [
                'required',
                'date',
                'after_or_equal:now',
                new EventLeadTime($this->expected_attendance, $this->start_time),
            ],
            'end_time' => [
                'required',
                'date',
                'after:start_time',
                new LocationAvailable($this->location_id, $this->start_time, $this->end_time)
            ],
            'end_date' => 'nullable|date|after_or_equal:start_time',
            'recurrence_frequency' => 'nullable|in:daily,weekly,fortnightly',
            'recurrence_count' => 'nullable|integer|min:1|max:52|required_with:recurrence_frequency',
            'services' => 'sometimes|array',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->location_id || !$this->start_time || !$this->end_time) {
                return;
            }

            $conflict = Event::where('location_id', $this->location_id)
                ->where('start_time', '<', $this->end_time)
                ->where('end_time', '>', $this->start_time)
                ->exists();

            if ($conflict) {
                $validator->errors()->add('start_time', 'The selected location is unavailable for the chosen time.');
            }

            if ($this->campus && $this->location_id) {
                $matches = \App\Models\Location::where('id', $this->location_id)
                    ->where('campus', $this->campus)
                    ->exists();
                if (!$matches) {
                    $validator->errors()->add('location_id', 'The selected location does not belong to the chosen campus.');
                }
            }
        });
    }
}