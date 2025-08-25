<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterCalendarRequest;
use App\Services\AdminCalendarOverviewService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/admin/calendar-overview",
 *     summary="Detailed calendar overview with events",
 *     tags={"Admin - Calendar"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum={"draft","pending","service_approved","approved","rejected","cancelled"})
 *     ),
 *     @OA\Parameter(
 *         name="location_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="date",
 *         in="query",
 *         required=false,
 *         description="Format: YYYY-MM-DD",
 *         @OA\Schema(type="string", example="2025-06-10")
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         required=false,
 *         description="Keyword to search in title or details",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200, description="Detailed calendar overview"),
 * )
 */
class AdminCalendarOverviewController extends Controller
{
    protected AdminCalendarOverviewService $calendarService;

    public function __construct(AdminCalendarOverviewService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(FilterCalendarRequest $request): JsonResponse
    {
        $user = $request->user();
        $role = $user->roles->pluck('name')->first();

        if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
            $events = $this->calendarService->getOverview($request->validated());
        } else {
            $events = $this->calendarService->getOverview($request->validated(), $role);
        }

        return response()->json(['data' => $events]);
    }
}
