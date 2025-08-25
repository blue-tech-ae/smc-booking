<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminCalendarOverviewService
{
    public function getOverview(array $filters, ?string $role = null): Collection
    {
        $query = Event::with('location', 'user', 'services');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (!empty($filters['date'])) {
            $start = Carbon::parse($filters['date'])->startOfDay();
            $end = $start->copy()->endOfDay();

            $query->whereBetween('start_time', [$start, $end]);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['Catering', 'Photography', 'Security'])) {
            $query->whereHas('services', function ($q) use ($role) {
                $q->where('service_type', strtolower($role));
            });
        }
        return $query->orderBy('start_time')->get();
    }
}
