<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Services\EventService;
use App\Http\Requests\StoreEventServiceRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\EventService as EventServiceModel;
use App\Notifications\ServiceAssignedNotification;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;


class EventController extends Controller
{
    protected EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Create a new event booking",
     *     tags={"Events"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreEventRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Event created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $event = $this->eventService->create($request);

        foreach ($request->input('services', []) as $serviceData) {
            $validator = Validator::make(
                $serviceData,
                (new StoreEventServiceRequest())->rules($serviceData['service_type'] ?? null)
            );
            $validated = $validator->validate();
            $validated['event_id'] = $event->id;
            $service = EventServiceModel::updateOrCreate(
                ['event_id' => $event->id, 'service_type' => $validated['service_type']],
                $validated
            );

            $service->refresh();
            if ($service->assignedUser) {
                $service->assignedUser->notify(new ServiceAssignedNotification($service));
            }
        }

        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/events/{id}",
     *     summary="Update an existing event",
     *     tags={"Events"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the event",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateEventRequest")
     *     ),
@@ -86,54 +92,59 @@ public function store(StoreEventRequest $request): JsonResponse
     *         response=200,
     *         description="Event updated successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized or cannot edit"
     *     )
     * )
     */
    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['error' => 'You are not allowed to edit this event.'], 403);
        }

        if (Carbon::parse($event->start_time)->lessThanOrEqualTo(Carbon::now()->addWeeks(2))) {
            return response()->json(['error' => 'You cannot modify this event less than two weeks before it starts.'], 403);
        }

        $data = $request->validated();

        if ($event->status === 'approved') {
            $data['status'] = 'pending';
        }

        $updated = $this->eventService->update($event, $data);

        foreach ($request->input('services', []) as $serviceData) {
            $validator = Validator::make(
                $serviceData,
                (new StoreEventServiceRequest())->rules($serviceData['service_type'] ?? null)
            );
            $validated = $validator->validate();
            $validated['event_id'] = $event->id;

            EventServiceModel::updateOrCreate(
                ['event_id' => $event->id, 'service_type' => $validated['service_type']],
                $validated
            );
        }

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $updated
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/my-bookings",
     *     summary="List all bookings for the authenticated user",
     *     tags={"Events"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of events",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Event")
     *         )
     *     )
     * )
     */
    public function myBookings(Request $request): JsonResponse
    {
        $bookings = Event::with('location', 'services')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $bookings
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     summary="Get full details of an event",
     *     tags={"Events"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the event",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Event details"),
     *     @OA\Response(response=403, description="Not authorized")
     * )
     */
    public function show(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();

        if ($event->user_id !== $user->id && !$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['error' => 'You are not authorized to view this event.'], 403);
        }

        $event->load('location', 'services');

        return response()->json(['data' => $event]);
    }
}
