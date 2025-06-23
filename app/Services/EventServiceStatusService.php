<?php

namespace App\Services;

use App\Models\EventService;

class EventServiceStatusService
{
    public function accept(EventService $service): EventService
    {
        $service->status = 'accepted';
        $service->save();

        return $service;
    }

    public function reject(EventService $service): EventService
    {
        $service->status = 'rejected';
        $service->save();

        return $service;
    }
}
