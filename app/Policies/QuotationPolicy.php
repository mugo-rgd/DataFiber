<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Quotation;
use Illuminate\Auth\Access\Response;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;

        // return in_array($user->role, [
        //     'admin',
        //     'system_admin',
        //     'account_manager',
        //     'accountmanager_admin',
        //     'customer',
        //     'designer','finance', 'technical_admin'
        // ]);
    }

    /**
 * Determine whether the user can view the model.
 */
    public function view(User $user, Quotation $quotation): bool
    {
        // System admin and admin can view all quotations
        if (in_array($user->role, ['system_admin', 'admin'])) {
            return true;
        }

        // Account managers can view quotations assigned to them
        if (in_array($user->role, ['account_manager', 'accountmanager_admin'])) {
            return $quotation->account_manager_id === $user->id;
        }

        // Designers can view quotations for their design requests
        if ($user->role === 'designer') {
            return $quotation->designRequest && $quotation->designRequest->designer_id === $user->id;
        }

        // Customer can view quotations that belong to them
        if ($user->role === 'customer') {
            return $quotation->customer_id === $user->id ||
                   ($quotation->designRequest && $quotation->designRequest->customer_id === $user->id);
        }

          return $user->hasAnyRole([
        'admin',
        'account_manager',
        'designer',
        'finance',
        'technical_admin', 'accountmanager_admin'
    ]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'system_admin',
            'account_manager',
            'accountmanager_admin',
            'designer'
        ]);
    }

    public function update(User $user, Quotation $quotation): bool
    {
        // Only draft quotations can be updated
        if ($quotation->status !== 'draft') {
            return false;
        }

        // System admin and admin can update any draft quotation
        if (in_array($user->role, ['system_admin', 'admin'])) {
            return true;
        }

        // Account managers can update their own draft quotations
        if (in_array($user->role, ['account_manager', 'accountmanager_admin'])) {
            return $quotation->account_manager_id === $user->id;
        }

        // Designers can update quotations for their design requests
        if ($user->role === 'designer') {
            return $quotation->designRequest && $quotation->designRequest->designer_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        // Only draft quotations can be deleted
        if ($quotation->status !== 'draft') {
            return false;
        }

        // System admin and admin can delete any draft quotation
        if (in_array($user->role, ['system_admin', 'admin'])) {
            return true;
        }

        // Account managers can delete their own draft quotations
        if (in_array($user->role, ['account_manager', 'accountmanager_admin'])) {
            return $quotation->account_manager_id === $user->id;
        }

        // Designers can delete quotations for their design requests
        if ($user->role === 'designer') {
            return $quotation->designRequest && $quotation->designRequest->designer_id === $user->id;
        }

        return false;
    }

    public function send(User $user, Quotation $quotation): bool
    {
        // Only draft quotations can be sent
        if ($quotation->status !== 'draft') {
            return false;
        }

        // System admin, admin, and account managers can send quotations
        return in_array($user->role, [
            'admin',
            'system_admin',
            'account_manager',
            'accountmanager_admin'
        ]);
    }

    public function approve(User $user, Quotation $quotation): bool
    {
        // System admin, admin and account managers can approve internally
        if (in_array($user->role, ['admin', 'system_admin', 'account_manager', 'accountmanager_admin'])) {
            return $quotation->status === 'draft';
        }

        return false;
    }

    public function reject(User $user, Quotation $quotation): bool
    {
        // System admin, admin and account managers can reject internally
        if (in_array($user->role, ['admin', 'system_admin', 'account_manager', 'accountmanager_admin'])) {
            return $quotation->status === 'draft';
        }

        return false;
    }

    // Alias methods for clarity
    public function customerApprove(User $user, Quotation $quotation): bool
    {
        // Customer can only approve quotations that belong to them and are in sent status
        if ($user->role === 'customer') {
            $isCustomerQuotation = $quotation->customer_id === $user->id ||
                                  ($quotation->designRequest && $quotation->designRequest->customer_id === $user->id);

            return $isCustomerQuotation &&
                   $quotation->status === 'sent' &&
                   !in_array($quotation->customer_approval_status, ['approved', 'rejected']);
        }

        return false;
    }

    public function customerReject(User $user, Quotation $quotation): bool
    {
        // Customer can only reject quotations that belong to them and are in sent status
        if ($user->role === 'customer') {
            $isCustomerQuotation = $quotation->customer_id === $user->id ||
                                  ($quotation->designRequest && $quotation->designRequest->customer_id === $user->id);

            return $isCustomerQuotation &&
                   $quotation->status === 'sent' &&
                   !in_array($quotation->customer_approval_status, ['approved', 'rejected']);
        }

        return false;
    }

    /**
     * Additional method to check if user can download quotation
     */
    public function download(User $user, Quotation $quotation): bool
    {
        return $this->view($user, $quotation);
    }
}
