<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class AdminUserService
{
    public function getAll(): Collection
    {
        return User::with('roles')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
