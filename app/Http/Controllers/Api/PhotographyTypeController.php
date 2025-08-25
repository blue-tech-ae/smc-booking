<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotographyType;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/photography-types",
 *     summary="List all photography types",
 *     @OA\Response(response=200, description="List of photography types")
 * )
 */
class PhotographyTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $types = PhotographyType::select('id', 'name')->get();

        return response()->json(['data' => $types]);
    }
}