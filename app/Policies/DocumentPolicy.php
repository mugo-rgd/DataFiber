<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view documents.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'system_admin',
            'technical_admin',
            'account_manager',
            'accountmanager_admin',
            'finance',
            'ict_engineer'
        ]);
    }

    /**
     * Determine if the user can view specific design request documents.
     */
    public function view(User $user, $designRequest): bool
    {
        // Admins and system admins can view all
        if (in_array($user->role, ['admin', 'system_admin', 'technical_admin'])) {
            return true;
        }

        // Account managers can view their assigned customers' documents
        if ($user->role === 'account_manager') {
            return $designRequest->customer->account_manager_id === $user->id;
        }

        // ICT engineers can view documents for requests they're assigned to
        if ($user->role === 'ict_engineer') {
            return $designRequest->ict_engineer_id === $user->id
                || $designRequest->assigned_ict_engineer_id === $user->id;
        }

        // Finance can view all documents
        if ($user->role === 'finance') {
            return true;
        }

        // Customers can view their own documents
        if ($user->role === 'customer') {
            return $designRequest->customer_id === $user->id;
        }

        return false;
    }
}
