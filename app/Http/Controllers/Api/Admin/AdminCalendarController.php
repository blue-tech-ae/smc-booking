<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterCalendarRequest;
use App\Services\AdminCalendarService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/admin/calendar-view",
 *     summary="Admin calendar view with filters",
 *     tags={"Admin - Calendar"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum={"draft","pending","approved","rejected","cancelled"})
 *     ),
 *     @OA\Parameter(
 *         name="location_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="month",
 *         in="query",
 *         required=false,
 *         description="Format: YYYY-MM",
 *         @OA\Schema(type="string", example="2025-06")
 *     ),
 *     @OA\Response(response=200, description="List of events"),
 * )
 */
class AdminCalendarController extends Controller
{
    protected AdminCalendarService $calendarService;

    public function __construct(AdminCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(FilterCalendarRequest $request): JsonResponse
    {
        $events = $this->calendarService->getFilteredEvents($request->validated());

        return response()->json(['data' => $events]);
    }
}
