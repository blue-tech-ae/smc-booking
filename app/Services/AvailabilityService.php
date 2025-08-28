<?php

namespace App\Services;

use App\Models\Location;
use App\Models\EventService;

class AvailabilityService
{
    public function getAvailableLocations(string $startTime, string $endTime)
    {
        return Location::whereDoesntHave('events', function ($query) use ($startTime, $endTime) {
            $query->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
        })->select('id', 'name', 'description')->get();
    }

    public function photographyIsAvailable(string $startTime, string $endTime): bool
    {
        return !EventService::where('service_type', 'photography')
            ->whereHas('event', function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->exists();
    }
}
