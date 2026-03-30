<?php

namespace App\Policies;

use App\Models\Lease;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeasePolicy
{
    /**
     * Determine if the user can view the lease.
     */
    public function view(User $user, Lease $lease): bool
    {
        // Customers can only view their own leases
        return $user->id === $lease->customer_id || $user->isAdmin();
    }

    /**
     * Determine if the user can update the lease.
     */
    public function update(User $user, Lease $lease): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the lease.
     */
    public function delete(User $user, Lease $lease): bool
    {
        return $user->isAdmin();
    }
}
