<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EventLeadTime implements Rule
{
    protected ?int $expectedAttendance;
    protected ?string $startTime;

    public function __construct($expectedAttendance, $startTime)
    {
        $this->expectedAttendance = $expectedAttendance ? (int) $expectedAttendance : null;
        $this->startTime = $startTime;
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->startTime) {
            return true;
        }

        $user = Auth::user();
        if ($user && $user->hasAnyRole(['Admin', 'Super Admin'])) {
            return true;
        }

        // Regardless of expected attendance, the event must be booked
        // at least 14 days before the start time.
        return Carbon::parse($this->startTime)->gte(Carbon::today()->addDays(14));
    }

    public function message(): string
    {
        return 'The event must be booked at least 14 days in advance.';
    }
}