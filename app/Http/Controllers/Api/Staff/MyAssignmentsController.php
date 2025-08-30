<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Services\AssignmentService;
use App\Http\Requests\FilterMyAssignmentsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Get(
 *     path="/api/my-assignments",
 *     summary="Get events assigned to the logged-in user with optional filters",
 *     tags={"Staff - Assignments"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum={"draft","pending","approved","rejected","cancelled"})
 *     ),
 *     @OA\Parameter(
 *         name="location",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
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
 *     @OA\Response(response=200, description="List of assigned events")
 * )
 */
class MyAssignmentsController extends Controller
{
    protected AssignmentService $service;

    public function __construct(AssignmentService $service)
    {
        $this->service = $service;
    }

    public function index(FilterMyAssignmentsRequest $request): JsonResponse
    {
        $user = Auth::user();
        $role = $user->roles->pluck('name')->first(); // assuming one role per user

        $events = $this->service->getMyAssignedEvents($user->id, $role, $request->validated());

        return response()->json(['data' => $events]);
    }
}