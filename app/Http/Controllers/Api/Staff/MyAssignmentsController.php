<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Services\AssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Get(
 *     path="/api/my-assignments",
 *     summary="Get events assigned to the logged-in user",
 *     tags={"Staff - Assignments"},
 *     security={{"sanctum":{}}},
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

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $role = $user->roles->pluck('name')->first(); // assuming one role per user

        $events = $this->service->getMyAssignedEvents($user->id, $role);

        return response()->json(['data' => $events]);
    }
}
