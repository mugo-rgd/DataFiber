<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Contract;
use Illuminate\Auth\Access\Response;

class ContractPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'system_admin',
            'account_manager',
            'accountmanager_admin',
            'customer'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contract $contract): bool
    {
        // Admin and system admin can view ALL contracts regardless of status
        if (in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin'])) {
            return true;
        }

        // Account managers can view contracts for their assigned customers
        if (in_array($user->role, ['account_manager','admin', 'technical_admin', 'system_admin', 'accountmanager_admin'])) {
            return $this->isAccountManagerForContract($user, $contract);
        }

        // Customer can only view their own contracts
        if ($user->role === 'customer') {
            return $this->isCustomerContractOwner($user, $contract);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow admin roles to create contracts
        return in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contract $contract): bool
    {
        // Admin and system admin can update contracts in draft or pending status
        if (in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin'])) {
            return in_array($contract->status, ['draft', 'sent_to_customer', 'pending_approval']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contract $contract): bool
    {
        // Only system admin can delete contracts, and only if they're not approved
        return $user->role === 'system_admin' &&
               in_array($contract->status, ['draft', 'sent_to_customer', 'pending_approval']);
    }


    // In your ContractPolicy - update the approve method:

/**
 * Determine whether the user can approve the contract (as customer or admin).
 */
public function approve(User $user, Contract $contract): bool
{
    // Customer can only approve their own contracts that are pending approval
    if ($user->role === 'customer') {
        return $this->isCustomerContractOwner($user, $contract) &&
               in_array($contract->status, ['sent_to_customer', 'pending_approval', 'sent']) &&
               $contract->customer_approval_status === 'pending';
    }

    // Admin/system admin can approve any contract that is pending approval
    if (in_array($user->role, ['admin', 'system_admin'])) {
        return in_array($contract->status, ['sent_to_customer', 'pending_approval', 'sent', 'draft']);
    }

    return false;
}

/**
 * Determine whether the user can reject the contract (as customer or admin).
 */
public function reject(User $user, Contract $contract): bool
{
    // Customer can only reject their own contracts that are pending approval
    if ($user->role === 'customer') {
        return $this->isCustomerContractOwner($user, $contract) &&
               in_array($contract->status, ['sent_to_customer', 'pending_approval', 'sent']) &&
               $contract->customer_approval_status === 'pending';
    }

    // Admin/system admin can reject any contract that is pending approval
    if (in_array($user->role, ['admin', 'system_admin'])) {
        return in_array($contract->status, ['sent_to_customer', 'pending_approval', 'sent', 'draft']);
    }

    return false;
}
       /**
     * Determine whether the user can download the contract PDF.
     */
    public function download(User $user, Contract $contract): bool
    {
        return $this->view($user, $contract);
    }

    /**
     * Determine whether the user can send contract to customer.
     */
    public function sendToCustomer(User $user, Contract $contract): bool
    {
        // Allow sending contracts that are in draft status
        if ($contract->status !== 'draft') {
            return false;
        }

        return in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin']);
    }

    /**
     * Determine whether the user can generate contracts manually.
     */
    public function generate(User $user): bool
    {
        return in_array($user->role, ['admin', 'system_admin', 'accountmanager_admin']);
    }

    /**
     * Check if user is the account manager for this contract's customer
     */
    private function isAccountManagerForContract(User $user, Contract $contract): bool
    {
        if (!$contract->quotation) {
            return false;
        }

        // Check if user is account manager for the quotation
        if ($contract->quotation->account_manager_id === $user->id) {
            return true;
        }

        // Check if user is account manager for the customer
        if ($contract->quotation->customer &&
            $contract->quotation->customer->account_manager_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is the owner of the contract (via quotation)
     */
    private function isCustomerContractOwner(User $user, Contract $contract): bool
    {
        if (!$contract->quotation) {
            return false;
        }

        // Contract owner is determined by the quotation's customer
        return $contract->quotation->customer_id === $user->id;
    }

    
}
