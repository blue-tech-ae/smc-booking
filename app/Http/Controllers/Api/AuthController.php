<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\Auth\RegisterService;
use App\Services\Auth\LoginService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name=\"Authentication\")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path=\"/api/register\",
     *     tags={\"Authentication\"},
     *     summary=\"تسجيل مستخدم جديد\",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={\"name\", \"email\", \"password\"},
     *             @OA\Property(property=\"name\", type=\"string\", example=\"John Doe\"),
     *             @OA\Property(property=\"email\", type=\"string\", example=\"john@example.com\"),
     *             @OA\Property(property=\"password\", type=\"string\", example=\"secret123\"),
     *             @OA\Property(property=\"password_confirmation\", type=\"string\", example=\"secret123\")
     *         )
     *     ),
     *     @OA\Response(response=201, description=\"تم التسجيل\"),
     *     @OA\Response(response=422, description=\"خطأ في التحقق\")
     * )
     */
    public function register(RegisterRequest $request, RegisterService $service)
    {
        $result = $service->register($request->validated());
        return response()->json($result, 201);
    }

    /**
     * @OA\Post(
     *     path=\"/api/login\",
     *     tags={\"Authentication\"},
     *     summary=\"تسجيل الدخول\",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={\"email\", \"password\"},
     *             @OA\Property(property=\"email\", type=\"string\", example=\"john@example.com\"),
     *             @OA\Property(property=\"password\", type=\"string\", example=\"secret123\")
     *         )
     *     ),
     *     @OA\Response(response=200, description=\"نجاح تسجيل الدخول\"),
     *     @OA\Response(response=401, description=\"بيانات غير صحيحة\"),
     *     @OA\Response(response=403, description=\"الحساب غير مفعل\")
     * )
     */
    public function login(LoginRequest $request, LoginService $service)
    {
        $result = $service->login($request->validated());

        if ($result === 'inactive') {
            return response()->json(['error' => 'Account disabled'], 403);
        }

        if (!$result) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json($result);
    }

    /**
     * @OA\Post(
     *     path=\"/api/logout\",
     *     tags={\"Authentication\"},
     *     summary=\"تسجيل الخروج\",
     *     security={{\"sanctum\":{}}},
     *     @OA\Response(response=200, description=\"تم تسجيل الخروج\")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
