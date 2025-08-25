<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminCalendarService
{
    public function getFilteredEvents(array $filters, ?string $role = null): Collection
    {
        $query = Event::with('location', 'user', 'services');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (!empty($filters['date'])) {
            $dayStart = Carbon::parse($filters['date'])->startOfDay();
            $dayEnd = $dayStart->copy()->endOfDay();

            $query->whereBetween('start_time', [$dayStart, $dayEnd]);
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
