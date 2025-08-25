<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

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
}
