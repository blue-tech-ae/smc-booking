<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminUserService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;


/**
 * @OA\Get(
 *     path="/api/admin/users",
 *     summary="List all users with their roles",
 *     tags={"Admin - Users"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Users list")
 * )
 */
class AdminUserController extends Controller
{
    protected AdminUserService $service;

    public function __construct(AdminUserService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $users = $this->service->getAll();

        return response()->json(['data' => $users]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/users/{id}/role",
     *     summary="Update a user's role",
     *     tags={"Admin - Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role"},
     *             @OA\Property(property="role", type="string", example="catering")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User role updated")
     * )
     */
    public function updateRole(User $user, UpdateUserRoleRequest $request): JsonResponse
    {
        $newRole = $request->validated('role');

        // remove old roles
        $user->syncRoles([$newRole]);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user->load('roles'),
        ]);
    }
}
