<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PendingEventsRequest;
use App\Services\AdminPendingEventsService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/admin/pending-events",
 *     summary="Get all pending events with filters",
 *     tags={"Admin - Events"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="location_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="start_date",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2025-06-10")
 *     ),
 *     @OA\Parameter(
 *         name="title",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="organizer_email",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", format="email")
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum={"draft","pending","approved","rejected","cancelled"})
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of pending events"
 *     )
 * )
 */
class AdminPendingEventsController extends Controller
{
    protected AdminPendingEventsService $service;

    public function __construct(AdminPendingEventsService $service)
    {
        $this->service = $service;
    }

    public function index(PendingEventsRequest $request): JsonResponse
    {
        $events = $this->service->getFiltered($request->validated());

        return response()->json(['data' => $events]);
    }
}
