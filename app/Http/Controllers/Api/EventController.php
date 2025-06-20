<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use App\Models\Event;

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
     *     @OA\Response(
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
        if ($event->user_id !== $request->user()->id || $event->status !== 'approved') {
            return response()->json(['error' => 'You are not allowed to edit this event.'], 403);
        }

        $updated = $this->eventService->update($event, $request->validated());

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
}
