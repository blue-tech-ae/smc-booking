<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * @OA\Tag(name="Authentication")
 */
class MicrosoftAuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/auth/microsoft",
     *     tags={"Authentication"},
     *     summary="إعادة توجيه المستخدم لتسجيل الدخول عبر Microsoft",
     *     @OA\Response(response=302, description="Redirect to Microsoft Login")
     * )
     */
    public function redirect()
    {
        return response()->json([
            'url' => Socialite::driver('azure')->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/microsoft/callback",
     *     tags={"Authentication"},
     *     summary="استقبال المستخدم بعد تسجيل الدخول عبر Microsoft",
     *     @OA\Parameter(name="code", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="نجاح تسجيل الدخول، إرجاع التوكن"),
     *     @OA\Response(response=403, description="دومين غير مسموح"),
     *     @OA\Response(response=401, description="فشل المصادقة")
     * )
     */
    public function callback()
    {
        try {
            $microsoftUser = Socialite::driver('azure')->stateless()->user();

            // تأكد من النطاق الصحيح للبريد
            if (!Str::endsWith($microsoftUser->getEmail(), '@yourdomain.com')) {
                return response()->json(['error' => 'Unauthorized domain'], 403);
            }

            // إنشاء أو تحديث المستخدم
            $user = User::updateOrCreate(
                ['email' => $microsoftUser->getEmail()],
                [
                    'name' => $microsoftUser->getName(),
                    'azure_id' => $microsoftUser->getId(),
                    'avatar' => $microsoftUser->getAvatar(),
                    'is_active' => true,
                ]
            );

            // إصدار توكن Sanctum
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }
    }
}
