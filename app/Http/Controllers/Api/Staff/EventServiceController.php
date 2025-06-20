<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Services\EventServiceDetailsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Get(
 *     path="/api/event-services/{id}",
 *     summary="Get full details of an assigned event service",
 *     tags={"Staff - Assignments"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Service details"),
 *     @OA\Response(response=404, description="Not found or not assigned")
 * )
 */
class EventServiceController extends Controller
{
    protected EventServiceDetailsService $eventService;

    public function __construct(EventServiceDetailsService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function show(int $id): JsonResponse
    {
        $userId = Auth::id();

        $service = $this->eventService->getDetails($id, $userId);

        if (!$service) {
            return response()->json(['message' => 'Service not found or not assigned to you'], 404);
        }

        return response()->json(['data' => $service]);
    }
}
