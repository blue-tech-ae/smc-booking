<?php

namespace App\Services;

use App\Models\EventService;
use Illuminate\Support\Collection;

class AssignmentService
{
    public function getMyAssignedEvents(int $userId, string $role): Collection
    {
        return EventService::with('event.location')
            ->where('service_type', $role)
            ->where('assigned_to', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($service) {
                return [
                    'event_id' => $service->event->id,
                    'event_title' => $service->event->title,
                    'location' => $service->event->location->name ?? null,
                    'start_time' => $service->event->start_time,
                    'end_time' => $service->event->end_time,
                    'service_type' => $service->service_type,
                    'details' => $service->details,
                ];
            });
    }
}
