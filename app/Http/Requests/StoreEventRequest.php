<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LocationAvailable;
use App\Rules\EventLeadTime;
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
            'location' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'campus' => ['required', new Enum(Campus::class)],
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
            'start_time' => [
                'required',
                'date',
                'after_or_equal:now',
                new EventLeadTime($this->expected_attendance, $this->start_time),
            ],
            'end_time' => [
                'required',
                'date',
                'after_or_equal:start_time',
                new LocationAvailable($this->location, $this->start_time, $this->end_time)
            ],
            'recurrence_frequency' => 'nullable|in:daily,weekly,fortnightly',
            'recurrence_count' => 'nullable|integer|min:1|max:52|required_with:recurrence_frequency',
            'services' => 'sometimes|array',
        ];
    }

}