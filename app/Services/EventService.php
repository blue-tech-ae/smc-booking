<?php

namespace App\Services;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;

class EventService
{
    public function create(StoreEventRequest $request): Event
    {
        $event = Event::create([
            'user_id' => $request->user()->id,
            'location_id' => $request->location_id,
            'title' => $request->title,
            'details' => $request->details,
            'expected_attendance' => $request->expected_attendance,
            'organizer_name' => $request->organizer_name,
            'organizer_email' => $request->organizer_email,
            'organizer_phone' => $request->organizer_phone,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'pending',
        ]);

        // Notify all admins that a new event requires approval
        $admins = \App\Models\User::role('Admin')->get();
        if ($admins->isNotEmpty()) {
            \Illuminate\Support\Facades\Mail::to($admins)
                ->send(new \App\Mail\EventApprovalRequest($event));
        }

        return $event;
    }

    public function update(Event $event, array $data): Event
    {
        $event->update($data);
        return $event;
    }
}
