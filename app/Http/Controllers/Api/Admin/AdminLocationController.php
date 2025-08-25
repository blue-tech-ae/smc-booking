<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;

class AdminLocationController extends Controller
{
    public function __construct(protected LocationService $service) {}

    /**
     * @OA\Post(
     *     path="/api/admin/locations",
     *     summary="Add a new location",
     *     tags={"Admin - Locations"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","campus"},
     *             @OA\Property(property="name", type="string", example="Main Hall"),
     *             @OA\Property(property="campus", type="string", example="Davisson Street Campus"),
     *             @OA\Property(property="description", type="string", example="<p>Spacious hall</p>")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Location created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Main Hall"),
     *                 @OA\Property(property="campus", type="string", example="Davisson Street Campus"),
     *                 @OA\Property(property="description", type="string", example="<p>Spacious hall</p>"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = $this->service->store($request->validated());
        return response()->json(['message' => 'Location created successfully', 'data' => $location]);
    }


    /**
     * @OA\Put(
     *     path="/api/admin/locations/{location}",
     *     summary="Update an existing location",
     *     tags={"Admin - Locations"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="location",
     *         in="path",
     *         required=true,
     *         description="ID of the location",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","campus"},
     *             @OA\Property(property="name", type="string", example="Updated Hall Name"),
     *             @OA\Property(property="campus", type="string", example="Dalton Road Campus"),
     *             @OA\Property(property="description", type="string", example="<p>Updated description</p>")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Location updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Hall Name"),
     *                 @OA\Property(property="campus", type="string", example="Dalton Road Campus"),
     *                 @OA\Property(property="description", type="string", example="<p>Updated description</p>"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */

    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        $location = $this->service->update($location, $request->validated());
        return response()->json(['message' => 'Location updated successfully', 'data' => $location]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/locations/{location}",
     *     summary="Delete a location",
     *     tags={"Admin - Locations"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="location",
     *         in="path",
     *         required=true,
     *         description="ID of the location",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Location deleted successfully")
     *         )
     *     )
     * )
     */

    public function destroy(Location $location): JsonResponse
    {
        $this->service->delete($location);
        return response()->json(['message' => 'Location deleted successfully']);
    }
}
