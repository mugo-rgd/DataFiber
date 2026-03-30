<?php
// app/Models/Contract.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Add this import
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;



class Contract extends Model
{
    protected $fillable = [
        'quotation_id',
        'contract_number',
        'contract_content',
        'status',
        'sent_to_customer_at',
        'customer_approved_at',
        'admin_approved_at',
        'design_completed_at',
    ];

    protected $casts = [
           'sent_to_customer_at' => 'datetime',
        'customer_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'design_completed_at' => 'datetime',
    ];
/**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_SENT_TO_CUSTOMER = 'sent_to_customer';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

      /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_SENT_TO_CUSTOMER,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];
    }

    /**
     * Scope for contracts sent to customer
     */
    public function scopeSentToCustomer($query)
    {
        return $query->where('status', self::STATUS_SENT_TO_CUSTOMER);
    }

    /**
     * Check if contract is sent to customer
     */
    public function isSentToCustomer(): bool
    {
        return $this->status === self::STATUS_SENT_TO_CUSTOMER;
    }

    /**
     * Check if contract is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }
    /**
     * Boot function to generate contract number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = $contract->generateContractNumber();
            }
        });
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ContractApproval::class);
    }

    public function generateContractNumber(): string
    {
        return 'CONTRACT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'pending_approval' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayText(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => $this->status
        };
    }

    /**
     * Approve contract by customer
     */
    public function approveByCustomer(): void
    {
        $this->update([
            'status' => 'pending_approval',
            'customer_approved_at' => now()
        ]);

        // Record approval history
        ContractApproval::create([
            'contract_id' => $this->id,
            'approved_by' => 'customer',
            'notes' => 'Customer approved the contract'
        ]);
    }

    /**
     * Approve contract by admin
     */
    public function approveByAdmin(): void
    {
        $this->update([
            'status' => 'approved',
            'admin_approved_at' => now(),
            'design_completed_at' => now()
        ]);

        // Record approval history
        ContractApproval::create([
            'contract_id' => $this->id,
            'approved_by' => 'admin',
            'notes' => 'Admin approved the contract. Design request completed.'
        ]);
    }

    /**
     * Check if contract can be approved by customer
     */
    public function canBeApprovedByCustomer(): bool
    {
        return $this->status === 'draft' &&
               $this->quotation->customer_id === Auth::id();
    }

    /**
     * Check if contract can be approved by admin
     */
    public function canBeApprovedByAdmin(): bool
    {
        return $this->status === 'pending_approval' &&
               $this->customer_approved_at !== null;
    }

     // In Contract model
public function isSigned(): bool
{
    return !is_null($this->signed_at);
}

public function canBeApproved(): bool
{
    return $this->status === 'sent' &&
           $this->customer_approval_status === 'pending';
}

public function canBeSent(): bool
{
    return $this->status === 'draft';
}

// public function quotation(): BelongsTo
//     {
//         return $this->belongsTo(Quotation::class);
//     }

    /**
     * Get the lease associated with this contract
     */
    public function lease(): HasOne
    {
        return $this->hasOne(Lease::class, 'quotation_id', 'quotation_id');
    }
}
