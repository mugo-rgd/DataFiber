<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FinancialParameter;

class FinancialParameterPolicy
{
    /**
     * Determine if the user can access financial parameters.
     */
    public function access(User $user): bool
    {
        // Example: only users with the "finance" role can access
        return $user->role === 'finance';
    }

    // You can add more methods like view, create, update, delete, etc.
}
