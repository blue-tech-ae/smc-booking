<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBookingsRequest;
use App\Services\AdminBookingsService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/admin/bookings",
 *     summary="Get all bookings with filters",
 *     tags={"Admin - Events"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"pending","service_approved","approved","rejected","cancelled","draft"})),
 *     @OA\Parameter(name="location_id", in="query", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="start_date", in="query", @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="organizer_email", in="query", @OA\Schema(type="string", format="email")),
 *     @OA\Parameter(name="title", in="query", @OA\Schema(type="string")),
 *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
 *     @OA\Parameter(name="role", in="query", @OA\Schema(type="string", enum={"catering","photography","security"})),
 *     @OA\Response(response=200, description="List of bookings")
 * )
 */
class AdminAllBookingsController extends Controller
{
    protected AdminBookingsService $service;

    public function __construct(AdminBookingsService $service)
    {
        $this->service = $service;
    }

    public function index(AdminBookingsRequest $request): JsonResponse
    {
        $bookings = $this->service->getFiltered($request->validated());

        return response()->json(['data' => $bookings]);
    }
}
