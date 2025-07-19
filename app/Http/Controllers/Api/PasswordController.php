<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class PasswordController extends Controller
{
    /**
     * @OA\Put(
     *     path="/api/user/password",
     *     summary="Set or change password",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password"},
     *             @OA\Property(property="current_password", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password updated")
     * )
     */
    public function update(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->password) {
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return response()->json(['error' => 'Current password is incorrect'], 422);
            }
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password updated']);
    }
}
