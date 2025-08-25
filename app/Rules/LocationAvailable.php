<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Event;

class LocationAvailable implements Rule
{
    protected int $locationId;
    protected string $startTime;
    protected string $endTime;
    protected ?int $ignoreEventId;

    public function __construct($locationId, $startTime, $endTime, $ignoreEventId = null)
    {
        $this->locationId = $locationId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->ignoreEventId = $ignoreEventId;
    }

    public function passes($attribute, $value)
    {
        if (!$this->locationId || !$this->startTime || !$this->endTime) {
            return true; // other validation will handle required fields
        }

        $query = Event::where('location_id', $this->locationId)
            ->where('start_time', '<', $this->endTime)
            ->where('end_time', '>', $this->startTime);

        if ($this->ignoreEventId) {
            $query->where('id', '!=', $this->ignoreEventId);
        }

        return !$query->exists();
    }

    public function message(): string
    {
        return 'The selected location is unavailable for the chosen time.';
    }
}