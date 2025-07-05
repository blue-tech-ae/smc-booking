<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class MatchesServiceRole implements Rule
{
    protected string $serviceType;

    public function __construct(?string $serviceType)
    {
        $this->serviceType = $serviceType ? strtolower($serviceType) : '';
    }

    public function passes($attribute, $value): bool
    {
        if (!$value) {
            return true; // handled elsewhere or default assignment
        }

        $user = User::find($value);
        if (!$user) {
            return false;
        }

        $role = ucfirst($this->serviceType);
        return $user->hasRole($role);
    }

    public function message(): string
    {
        return 'The selected user does not have the appropriate role for this service.';
    }
}
