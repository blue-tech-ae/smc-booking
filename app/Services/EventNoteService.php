<?php

namespace App\Services;

use App\Models\EventNote;

class EventNoteService
{
    public function store(int $eventServiceId, int $userId, string $note): EventNote
    {
        return EventNote::create([
            'event_service_id' => $eventServiceId,
            'user_id' => $userId,
            'note' => $note,
        ]);
    }
}
