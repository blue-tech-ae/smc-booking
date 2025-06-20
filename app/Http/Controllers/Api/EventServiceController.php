<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventServiceRequest;
use App\Models\Event;
use App\Services\EventServiceDetailsService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Post(
 *     path="/api/events/{id}/services",
 *     summary="Add or update services for an event",
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
 *         @OA\JsonContent(ref="#/components/schemas/StoreEventServiceRequest")
 *     ),
 *     @OA\Response(response=200, description="Services updated successfully"),
 *     @OA\Response(response=403, description="Not authorized")
 * )
 */
class EventServiceController extends Controller
{
    protected EventServiceDetailsService $eventService;

    public function __construct(EventServiceDetailsService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function store(StoreEventServiceRequest $request, Event $event): JsonResponse
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['error' => 'You are not authorized to modify this event.'], 403);
        }

        $data = $this->eventService->store($event, $request);

        return response()->json([
            'message' => 'Services updated successfully',
            'data' => $data,
        ]);
    }
}
