<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminCalendarService
{
    public function getFilteredEvents(array $filters): Collection
    {
        $query = Event::with('location', 'user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (!empty($filters['month'])) {
            $monthStart = Carbon::parse($filters['month'])->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $query->whereBetween('start_time', [$monthStart, $monthEnd]);
        }

        return $query->orderBy('start_time')->get();
    }
}
