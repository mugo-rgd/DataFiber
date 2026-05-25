<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    protected $fillable = [
        'quotation_id',
        'account_manager_id',
        'customer_id',
        'contract_number',
        'contract_content',
        'status',
        'sent_at',
        'sent_to_customer_at',
        'customer_approval_status',
        'customer_approved_at',
        'customer_rejected_at',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'admin_approved_at',
        'approval_notes',
        'design_completed_at',
         'pdf_path',
    'pdf_generated_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'sent_to_customer_at' => 'datetime',
        'customer_approved_at' => 'datetime',
        'customer_rejected_at' => 'datetime',
        'approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'design_completed_at' => 'datetime',
         'pdf_generated_at' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_CUSTOMER_APPROVED = 'customer_approved';
    public const STATUS_CUSTOMER_REJECTED = 'customer_rejected';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ACTIVE = 'active';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = $contract->generateContractNumber();
            }

            if (empty($contract->status)) {
                $contract->status = self::STATUS_DRAFT;
            }

            if (empty($contract->customer_approval_status)) {
                $contract->customer_approval_status = 'pending';
            }
        });
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SENT,
            self::STATUS_CUSTOMER_APPROVED,
            self::STATUS_CUSTOMER_REJECTED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_ACTIVE,
        ];
    }

    public function generateContractNumber(): string
    {
        return 'CONTRACT-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

   public function approvals()
{
    return $this->hasMany(ContractApproval::class);
    // or depending on your relationship:
    // return $this->morphMany(ContractApproval::class, 'approvable');
}

    public function lease(): HasOne
{
    return $this->hasOne(Lease::class, 'quotation_id', 'quotation_id');
}

    public function canBeSent(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canBeApprovedByCustomer(): bool
    {
        return $this->status === self::STATUS_SENT
            && $this->customer_approval_status === 'pending';
    }

    public function canBeRejectedByCustomer(): bool
    {
        return $this->status === self::STATUS_SENT
            && $this->customer_approval_status === 'pending';
    }

    public function canBeApprovedByAdmin(): bool
    {
        return $this->status === self::STATUS_CUSTOMER_APPROVED
            && $this->customer_approval_status === 'approved';
    }

    public function canBeActivated(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SENT => 'info',
            self::STATUS_CUSTOMER_APPROVED => 'warning',
            self::STATUS_CUSTOMER_REJECTED => 'danger',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_ACTIVE => 'primary',
            default => 'secondary',
        };
    }

    public function getStatusDisplayText(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SENT => 'Sent to Customer',
            self::STATUS_CUSTOMER_APPROVED => 'Customer Approved',
            self::STATUS_CUSTOMER_REJECTED => 'Customer Rejected',
            self::STATUS_APPROVED => 'Admin Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_ACTIVE => 'Active Lease',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
