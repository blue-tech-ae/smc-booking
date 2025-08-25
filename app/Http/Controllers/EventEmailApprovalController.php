<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventStatusService;

class EventEmailApprovalController extends Controller
{
    public function __invoke(Event $event, EventStatusService $service)
    {
        $service->approve($event);

        return redirect()->away('https://smc.blue-tech.ae/booking-approved');
    }
}