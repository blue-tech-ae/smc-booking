<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateEventStatusRequest;
use App\Models\Event;
use App\Services\EventStatusService;
use Illuminate\Http\JsonResponse;

class AdminEventApprovalController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/events/{id}/approve",
     *     summary="Approve an event",
     *     tags={"Admin - Events"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Event approved")
     * )
     */
    public function approve(Event $event, EventStatusService $service): JsonResponse
    {
        $updated = $service->approve($event);

        return response()->json(['data' => $updated]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/events/{id}/reject",
     *     summary="Reject an event",
     *     tags={"Admin - Events"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="Not enough capacity on selected date.")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Event rejected")
     * )
     */
    public function reject(Event $event, UpdateEventStatusRequest $request, EventStatusService $service): JsonResponse
    {
        $updated = $service->reject($event, $request->validated('reason'));

        return response()->json(['data' => $updated]);
    }
}
