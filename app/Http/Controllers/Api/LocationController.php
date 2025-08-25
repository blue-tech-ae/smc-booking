<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/locations",
 *     summary="List all locations",
 *     @OA\Response(response=200, description="List of locations")
 * )
 */
class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::select('id', 'name', 'description')->get();

        return response()->json(['data' => $locations]);
    }
}