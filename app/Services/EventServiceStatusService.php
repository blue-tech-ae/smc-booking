<?php

namespace App\Services;

use App\Models\EventService;

class EventServiceStatusService
{
    public function accept(EventService $service): EventService
    {
        $service->status = 'accepted';
        $service->save();
        
        $event = $service->event;
        $total = $event->services()->count();
        $accepted = $event->services()->where('status', 'accepted')->count();

        if ($total > 0 && $total === $accepted && $event->status !== 'approved') {
            $event->status = 'service_approved';
            $event->save();
        }
        return $service;
    }

    public function reject(EventService $service): EventService
    {
        $service->status = 'rejected';
        $service->save();

        return $service;
    }
}
