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
            'url' => Socialite::driver('microsoft')->stateless()->redirect()->getTargetUrl(),
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
        /*return response()->json([
            'user' => [
                'id' => 1,
                'name' => 'EventsTest1',
                'email' => 'EventsTest1@stmonicas-epping.com',
                'azure_id' => 'd6f625b7-cfd7-41f5-800c-47649195dca6',
                'avatar' => null,
                'is_active' => 1,
                'remember_token' => null,
                'created_at' => '2025-07-03T15:15:36.000000Z',
                'updated_at' => '2025-07-04T16:42:25.000000Z',
                'roles' => [
                    [
                        'id' => 1,
                        'name' => 'Super Admin',
                        'guard_name' => 'web',
                        'created_at' => '2025-07-03T15:15:36.000000Z',
                        'updated_at' => '2025-07-03T15:15:36.000000Z',
                        'pivot' => [
                            'model_type' => 'App\\Models\\User',
                            'model_id' => 1,
                            'role_id' => 1,
                        ],
                    ],
                ],
            ],
            'token' => '5|Npd56jXTsblOWvlaO3hHHA9cW2LanrfGLGfypP7nf995a347',
        ]);*/
        \Log::info('Microsoft SSO callback query', [
            'query' => request()->all()
        ]);

        try {
            $microsoftUser = Socialite::driver('microsoft')->stateless()->user();
            
            \Log::info('Microsoft SSO user info', [
                'user' => [
                    'id' => $microsoftUser->getId(),
                    'name' => $microsoftUser->getName(),
                    'email' => $microsoftUser->getEmail(),
                    // ...أي شيء ثاني تحتاجه
                ]
            ]);

            // تأكد من النطاق الصحيح للبريد
            // if (!Str::endsWith($microsoftUser->getEmail(), '@yourdomain.com')) {
            //     return response()->json(['error' => 'Unauthorized domain'], 403);
            // }

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

            if ($user->roles->isEmpty()) {
                $user->assignRole('General');
            }

            // إصدار توكن Sanctum
            $token = $user->createToken('auth-token')->plainTextToken;

            //for test only // to remove later
            //$redirectUrl = env('FRONTEND_AUTH_SUCCESS_URL', 'http://localhost:3000/auth-success');

            //return redirect()->away("{$redirectUrl}?token={$token}");
            //end of test code//
            
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()/*'Authentication failed'*/], 401);
        }
    }
}
