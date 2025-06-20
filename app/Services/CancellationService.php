<?php

namespace App\Services;

use App\Models\CancellationRequest;
use App\Models\Event;
use App\Http\Requests\StoreCancellationRequest;

class CancellationService
{
    public function store(StoreCancellationRequest $request, Event $event): CancellationRequest
    {
        return CancellationRequest::create([
            'user_id' => $request->user()->id,
            'event_id' => $event->id,
            'reason' => $request->reason,
            'details' => $request->details,
            'status' => 'pending',
        ]);
    }

    public function updateStatus(CancellationRequest $cancellationRequest, string $status): CancellationRequest
    {
        $cancellationRequest->status = $status;
        $cancellationRequest->save();

        // Optional: update the event status if accepted
        if ($status === 'accepted') {
            $cancellationRequest->event->update(['status' => 'cancelled']);
        }

        return $cancellationRequest;
    }
}
