<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginService
{
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();

        if ($user->roles->isEmpty()) {
            $user->assignRole('General');
        }

        if (!$user->is_active) {
            return 'inactive';
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }
}
