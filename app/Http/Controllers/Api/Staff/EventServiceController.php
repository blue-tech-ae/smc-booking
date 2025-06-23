<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Services\EventServiceDetailsService;
use App\Services\EventServiceStatusService;
use App\Models\EventService;
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
    protected EventServiceStatusService $statusService;

    public function __construct(EventServiceDetailsService $eventService, EventServiceStatusService $statusService)
    {
        $this->eventService = $eventService;
        $this->statusService = $statusService;
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

    /**
     * @OA\Post(
     *     path="/api/event-services/{id}/accept",
     *     summary="Accept an assigned event service",
     *     tags={"Staff - Assignments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Service accepted"),
     *     @OA\Response(response=404, description="Not found or not assigned")
     * )
     */
    public function accept(int $id): JsonResponse
    {
        $userId = Auth::id();
        $service = EventService::where('id', $id)->where('assigned_to', $userId)->first();

        if (!$service) {
            return response()->json(['message' => 'Service not found or not assigned to you'], 404);
        }

        $updated = $this->statusService->accept($service);

        return response()->json(['data' => $updated]);
    }

    /**
     * @OA\Post(
     *     path="/api/event-services/{id}/reject",
     *     summary="Reject an assigned event service",
     *     tags={"Staff - Assignments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Service rejected"),
     *     @OA\Response(response=404, description="Not found or not assigned")
     * )
     */
    public function reject(int $id): JsonResponse
    {
        $userId = Auth::id();
        $service = EventService::where('id', $id)->where('assigned_to', $userId)->first();

        if (!$service) {
            return response()->json(['message' => 'Service not found or not assigned to you'], 404);
        }

        $updated = $this->statusService->reject($service);

        return response()->json(['data' => $updated]);
    }
}
