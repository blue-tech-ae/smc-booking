<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCancellationRequest;
use App\Http\Requests\HandleCancellationRequest;
use App\Models\CancellationRequest;
use App\Models\Event;
use App\Services\CancellationService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Post(
 *     path="/api/events/{id}/cancel",
 *     summary="Request event cancellation",
 *     tags={"Events"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the event to cancel",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreCancellationRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Cancellation request submitted"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized or invalid state"
 *     )
 * )
 */
class CancellationController extends Controller
{
    protected CancellationService $cancellationService;

    public function __construct(CancellationService $cancellationService)
    {
        $this->cancellationService = $cancellationService;
    }

    public function store(StoreCancellationRequest $request, Event $event): JsonResponse
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['error' => 'You cannot cancel this event.'], 403);
        }

        $cancellation = $this->cancellationService->store($request, $event);

        return response()->json([
            'message' => 'Cancellation request submitted',
            'data' => $cancellation
        ], 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/cancellations/{id}",
     *     summary="Approve or reject a cancellation request",
     *     tags={"Admin - Cancellations"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Cancellation request ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"accepted", "rejected"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=403, description="Not authorized"),
     * )
     */
    public function handle(HandleCancellationRequest $request, CancellationRequest $cancellationRequest): JsonResponse
    {
        $updated = $this->cancellationService->updateStatus($cancellationRequest, $request->status);

        return response()->json([
            'message' => 'Cancellation request status updated',
            'data' => $updated,
        ]);
    }
}
