<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

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

        $attendance = $this->expectedAttendance ?? 0;

        if ($attendance <= 10) {
            $minDays = 7;
        } elseif ($attendance <= 30) {
            $minDays = 14;
        } elseif ($attendance <= 50) {
            $minDays = 28;
        } else {
            $minDays = 42;
        }

        return Carbon::parse($this->startTime)->gte(Carbon::now()->addDays($minDays));
    }

    public function message(): string
    {
        return 'The event must be booked with more notice based on expected attendance.';
    }
}
