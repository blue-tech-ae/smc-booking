<?php

namespace App\Services;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventApprovalRequest;
use Carbon\Carbon;

class EventService
{
    public function create(StoreEventRequest $request): array
    {
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $frequency = $request->recurrence_frequency;
        $count = $request->recurrence_count ? (int) $request->recurrence_count : 1;
        $interval = match ($frequency) {
            'daily' => 1,
            'weekly' => 7,
            'fortnightly' => 14,
            default => 0,
        };

        $created = [];
        $conflicts = [];

        for ($i = 0; $i < $count; $i++) {
            $currentStart = (clone $start)->addDays($i * $interval);
            $currentEnd = (clone $end)->addDays($i * $interval);
            $currentEndDate = $request->end_date
                ? Carbon::parse($request->end_date)->addDays($i * $interval)->toDateString()
                : null;

            $hasConflict = Event::where('location_id', $request->location_id)
                ->where('start_time', '<', $currentEnd)
                ->where('end_time', '>', $currentStart)
                ->exists();

            if ($hasConflict) {
                $conflicts[] = $currentStart->toDateTimeString();
                continue;
            }

            $event = Event::create([
                'user_id' => $request->user()->id,
                'location_id' => $request->location_id,
                'department' => $request->department,
                'campus' => $request->campus,
                'title' => $request->title,
                'details' => $request->details,
                'expected_attendance' => $request->expected_attendance,
                'organizer_name' => $request->organizer_name,
                'organizer_email' => $request->organizer_email,
                'organizer_phone' => $request->organizer_phone,
                'security_note' => $request->security_note,
                'start_time' => $currentStart,
                'end_time' => $currentEnd,
                'end_date' => $currentEndDate,
                'status' => 'pending',
            ]);

            $admins = User::role('Admin')->get();
            if ($admins->isNotEmpty()) {
                Mail::to($admins)->send(new EventApprovalRequest($event));
            }

            $created[] = $event;
        }

        return [$created, $conflicts];
    }

    public function update(Event $event, array $data): Event
    {
        $event->update($data);
        return $event;
    }
}
