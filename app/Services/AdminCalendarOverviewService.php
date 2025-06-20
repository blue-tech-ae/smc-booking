<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminCalendarOverviewService
{
    public function getOverview(array $filters): Collection
    {
        $query = Event::with('location', 'user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (!empty($filters['month'])) {
            $start = Carbon::parse($filters['month'])->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $query->whereBetween('start_time', [$start, $end]);
        }

        return $query->orderBy('start_time')->get();
    }
}
