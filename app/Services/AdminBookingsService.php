<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Collection;

class AdminBookingsService
{
    public function getFiltered(array $filters): Collection
    {
        $query = Event::with('user', 'services');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('start_time', '>=', $filters['start_date']);
        }

        if (!empty($filters['organizer_email'])) {
            $query->where('organizer_email', $filters['organizer_email']);
        }

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['role']) && in_array($filters['role'], ['catering', 'photography', 'security'])) {
            $query->whereHas('services', function ($q) use ($filters) {
                $q->where('service_type', $filters['role']);
            });
        }

        return $query->orderByDesc('created_at')->get();
    }
}
