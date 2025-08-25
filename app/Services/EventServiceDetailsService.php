<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventService;
use App\Http\Requests\StoreEventServiceRequest;

class EventServiceDetailsService
{
    public function store(Event $event, StoreEventServiceRequest $request): EventService
    {
        $data = $request->validated();
        // Services no longer require individual staff approval.
        // Mark them as accepted when created or updated.
        $data['status'] = 'accepted';

        return EventService::updateOrCreate(
            ['event_id' => $event->id, 'service_type' => $data['service_type']],
            $data
        );
    }

    public function getDetails(int $serviceId, int $userId): ?EventService
    {
        return EventService::with(['event.location', 'notes.user'])
            ->where('id', $serviceId)
            ->where('assigned_to', $userId)
            ->first();
    }
}