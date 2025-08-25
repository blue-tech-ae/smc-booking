<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    public function __construct(protected AvailabilityService $service) {}

    public function index(CheckAvailabilityRequest $request): JsonResponse
    {
        $start = $request->input('start_time');
        $end = $request->input('end_time');

        return response()->json([
            'data' => [
                'locations' => $this->service->getAvailableLocations($start, $end),
                'photography_available' => $this->service->photographyIsAvailable($start, $end),
            ],
        ]);
    }
}