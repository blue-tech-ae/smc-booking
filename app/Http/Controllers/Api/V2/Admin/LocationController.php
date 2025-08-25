<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\UpdateLocationRequest;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function __construct(protected LocationService $service) {}

    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        $location = $this->service->update($location, $request->validated());
        return response()->json(['message' => 'Location updated successfully', 'data' => $location]);
    }
}
