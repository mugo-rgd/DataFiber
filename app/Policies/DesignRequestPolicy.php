<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DesignRequest;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class DesignRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'account_manager', 'designer', 'surveyor', 'accountmanager_admin', 'customer']);
    }

public function view(User $user, DesignRequest $designRequest): bool
{
    Log::info('DesignRequestPolicy::view - Checking access', [
        'user_id' => $user->id,
        'user_role' => $user->role,
        'design_request_id' => $designRequest->id,
        'customer_id' => $designRequest->customer_id,
        'is_account_manager' => $user->role === 'account_manager',
        'in_array_check' => in_array($user->role, ['admin','customer', 'technical_admin', 'system_admin', 'account_manager', 'designer', 'surveyor', 'accountmanager_admin']),
        'allowed_roles' => ['admin','customer', 'technical_admin', 'system_admin', 'account_manager', 'designer', 'surveyor', 'accountmanager_admin']
    ]);

    if ($user->role === 'customer') {
        return $designRequest->customer_id === $user->id;
    }

    $result = in_array($user->role, ['admin','customer', 'technical_admin', 'system_admin', 'account_manager', 'designer', 'surveyor', 'accountmanager_admin']);

    Log::info('DesignRequestPolicy::view - Result: ' . ($result ? 'ALLOWED' : 'DENIED'));

    return $result;
}

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'account_manager', 'accountmanager_admin', 'customer', 'designer']);
    }

    public function update(User $user, DesignRequest $designRequest): bool
    {
        return in_array($user->role, ['admin','customer', 'technical_admin', 'system_admin', 'account_manager','account-manager',  'accountmanager_admin', 'designer']);

    }

    public function delete(User $user, DesignRequest $designRequest): bool
    {
        return in_array($user->role, ['admin', 'technical_admin', 'system_admin']);
    }
    public function approve(User $user, DesignRequest $designRequest): bool
    {
        return $user->id === $designRequest->customer_id &&
               $designRequest->canBeApproved();
    }
     // Add custom policy methods for your additional actions
    public function assignDesigner(User $user, DesignRequest $designRequest): bool
    {
        return in_array($user->role, ['admin', 'account_manager']);
    }

    public function assignSurveyor(User $user, DesignRequest $designRequest): bool
    {
        return in_array($user->role, ['admin', 'account_manager']);
    }

    public function updateStatus(User $user, DesignRequest $designRequest): bool
    {
        return in_array($user->role, ['admin', 'account_manager', 'designer']);
    }

    public function updateSurveyStatus(User $user, DesignRequest $designRequest): bool
    {
        return in_array($user->role, ['admin', 'account_manager', 'surveyor']);
    }
}
