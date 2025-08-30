<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Collection;

class AdminPendingEventsService
{
    public function getFiltered(array $filters): Collection
    {
        $query = Event::with('user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', 'pending');
        }

        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('start_time', '>=', $filters['start_date']);
        }

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['organizer_email'])) {
            $query->where('organizer_email', $filters['organizer_email']);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('start_time')->get();
    }
}
