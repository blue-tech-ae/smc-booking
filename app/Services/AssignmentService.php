<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Collection;

class AssignmentService
{
    public function getMyAssignedEvents(int $userId, string $role, array $filters = []): Collection
    {
        $query = Event::with(['user', 'services'])
            ->whereHas('services', function ($q) use ($userId, $role) {
                $q->where('assigned_to', $userId)
                  ->where('service_type', $role);
            });

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        if (!empty($filters['date'])) {
            $start = \Carbon\Carbon::parse($filters['date'])->startOfDay();
            $end = $start->copy()->endOfDay();
            $query->whereBetween('start_time', [$start, $end]);
        }

        if (!empty($filters['query'])) {
            $search = $filters['query'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('start_time')->get();
    }
}