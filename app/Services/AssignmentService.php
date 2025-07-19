<?php

namespace App\Services;

use App\Models\EventService;
use Illuminate\Support\Collection;

class AssignmentService
{
    public function getMyAssignedEvents(int $userId, string $role, array $filters = []): Collection
    {
        $query = EventService::with('event.location')
            ->where('service_type', $role)
            ->where('assigned_to', $userId);

        if (!empty($filters['status'])) {
            $query->whereHas('event', function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            });
        }

        if (!empty($filters['location_id'])) {
            $query->whereHas('event', function ($q) use ($filters) {
                $q->where('location_id', $filters['location_id']);
            });
        }

        if (!empty($filters['date'])) {
            $start = \Carbon\Carbon::parse($filters['date'])->startOfDay();
            $end = $start->copy()->endOfDay();
            $query->whereHas('event', function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end]);
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('event', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('created_at')->get()->map(function ($service) {
            return [
                'event_id' => $service->event->id,
                'event_title' => $service->event->title,
                'location' => $service->event->location->name ?? null,
                'start_time' => $service->event->start_time,
                'end_time' => $service->event->end_time,
                'service_type' => $service->service_type,
                'details' => $service->details,
                'status' => $service->status,
            ];
        });
    }
}
