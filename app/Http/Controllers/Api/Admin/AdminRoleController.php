<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

/**
 * @OA\Get(
 *     path="/api/admin/roles",
 *     summary="List all roles",
 *     tags={"Admin - Users"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="List of roles")
 * )
 */
class AdminRoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::select('id', 'name')->get();

        return response()->json(['data' => $roles]);
    }
}
