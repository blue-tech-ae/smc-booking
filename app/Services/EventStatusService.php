<?php

namespace App\Services;

use App\Models\Event;

class EventStatusService
{
    public function approve(Event $event): Event
    {
        $event->status = 'approved';
        $event->save();

        return $event;
    }

    public function reject(Event $event, ?string $reason = null): Event
    {
        $event->status = 'rejected';
        if ($reason) {
            $event->rejection_reason = $reason;
        }
        $event->save();

        return $event;
    }
}
