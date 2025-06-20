<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Collection;

class AdminPendingEventsService
{
    public function getFiltered(array $filters): Collection
    {
        $query = Event::with('location', 'user')
            ->where('status', 'pending');

        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
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

        return $query->orderBy('start_time')->get();
    }
}
