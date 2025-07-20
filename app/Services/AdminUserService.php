<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class AdminUserService
{
    /**
     * Retrieve all users with optional name search.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = User::with('roles')->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->get();
    }
}
