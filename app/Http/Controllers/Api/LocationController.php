<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/locations",
 *     summary="List all locations",
 *     @OA\Response(
 *         response=200,
 *         description="List of locations",
 *         @OA\JsonContent(type="object",
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="description", type="string"),
 *                     @OA\Property(property="campus", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::select('id', 'name', 'description', 'campus')->get();

        return response()->json(['data' => $locations]);
    }

    /**
     * Return campuses with their locations
     */
    public function campuses(): JsonResponse
    {
        $campuses = Location::select('id', 'name', 'description', 'campus')
            ->get()
            ->groupBy('campus')
            ->map(fn ($locations, $campus) => [
                'campus' => $campus,
                'locations' => $locations->map(fn ($location) => $location->only(['id', 'name', 'description']))->values(),
            ])
            ->values();

        return response()->json(['data' => $campuses]);
    }

    /**
     * Return booked days for a location in a given month
     */
    public function bookedDays(Location $location, Request $request): JsonResponse
    {
        $month = $request->query('month');

        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = Carbon::parse($month . '-01')->endOfMonth();

        $days = Event::where('location_id', $location->id)
            ->whereBetween('start_time', [$start, $end])
            ->pluck('start_time')
            ->map(fn ($date) => Carbon::parse($date)->day)
            ->unique()
            ->values();

        return response()->json(['data' => $days]);
    }
}
