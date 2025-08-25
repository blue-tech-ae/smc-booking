<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhotographyTypeRequest;
use App\Services\PhotographyTypeService;
use Illuminate\Http\JsonResponse;

class AdminPhotographyTypeController extends Controller
{
    public function __construct(protected PhotographyTypeService $service) {}

    public function store(StorePhotographyTypeRequest $request): JsonResponse
    {
        $type = $this->service->store($request->validated());

        return response()->json([
            'message' => 'Photography type created successfully',
            'data' => $type,
        ]);
    }
}