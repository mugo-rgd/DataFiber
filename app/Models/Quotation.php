<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_request_id',
        'customer_id',
        'account_manager_id',
        'quotation_number',
        'line_items',
        'subtotal',
        'tax_rate',
        'amount',
        'tax_amount',
        'total_amount',
        'status',
        'valid_until',
        'sent_at',
        'approved_at',
        'approved_by',
        'approval_notes',
        'rejected_at',
        'rejected_by',
        'rejection_notes',
        'scope_of_work',
        'terms_and_conditions',
        'customer_notes',
        // Add customer approval fields
        'customer_approval_status',
        'customer_approved_at',
        'customer_rejected_at',
        'rejection_reason'
    ];

    protected $casts = [
        'line_items' => 'array',
        'valid_until' => 'datetime',
        'sent_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'customer_approved_at' => 'datetime',
        'customer_rejected_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'amount' => 'decimal:2',
    ];

    /**
     * Boot function to generate quotation number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            if (empty($quotation->quotation_number)) {
                $quotation->quotation_number = static::generateQuotationNumber();
            }
        });
    }

    /**
     * Generate unique quotation number
     */
    public static function generateQuotationNumber(): string
    {
        $prefix = 'QT';
        $year = date('Y');
        $month = date('m');

        $lastQuotation = static::where('quotation_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('quotation_number', 'desc')
            ->first();

        if ($lastQuotation) {
            $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    /**
     * Relationships
     */
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function commercialRoutes(): BelongsToMany
    {
        return $this->belongsToMany(CommercialRoute::class, 'quotation_commercial_routes')
                    ->withPivot('quantity', 'unit_price', 'total_price', 'duration_months')
                    ->withTimestamps();
    }

    /**
     * Get the contract associated with the quotation.
     */
    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    /**
     * CUSTOMER APPROVAL METHODS
     */

    /**
     * Approve quotation by customer and generate contract
     */
    public function approveByCustomer(): void
    {
        $this->update([
            'customer_approval_status' => 'approved',
            'customer_approved_at' => now(),
            'status' => 'approved', // Update main status
            'approved_at' => now(),
            'customer_rejected_at' => null,
            'rejection_reason' => null,
        ]);

        // Also update the design request status if it exists
        if ($this->designRequest) {
            $this->designRequest->update([
                'status' => DesignRequest::STATUS_APPROVED,
                'approved_at' => now(),
            ]);
        }

        // Generate contract automatically
        if (!$this->contract) {
            $contractService = app(\App\Services\ContractGenerationService::class);
            $contractService->generateContract($this);
        }
    }

    /**
     * Reject quotation by customer
     */
    public function rejectByCustomer(string $reason = null): void
    {
        $this->update([
            'customer_approval_status' => 'rejected',
            'customer_rejected_at' => now(),
            'status' => 'rejected', // Update main status
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'customer_approved_at' => null,
        ]);

        // Also update the design request status if it exists
        if ($this->designRequest) {
            $this->designRequest->update([
                'status' => DesignRequest::STATUS_REJECTED,
            ]);
        }
    }

    /**
     * Check if quotation can be approved by customer
     */
    public function canBeApprovedByCustomer(): bool
    {
        $customerId = Auth::id();

        // Debug the conditions
        $conditions = [
            'status_is_sent' => $this->status === 'sent',
            'belongs_to_customer' => $this->belongsToCustomer($customerId),
            'customer_approval_pending' => (!$this->customer_approval_status || $this->customer_approval_status === 'pending'),
            'no_contract' => !$this->contract
        ];

        // Log for debugging
        if ($this->status === 'sent') {
            Log::info('Quotation Approval Conditions', array_merge([
                'quotation_id' => $this->id,
                'customer_id' => $customerId
            ], $conditions));
        }

        return $this->status === 'sent' &&
               $this->belongsToCustomer($customerId) &&
               (!$this->customer_approval_status || $this->customer_approval_status === 'pending') &&
               !$this->contract;
    }

    /**
     * Check if quotation can be rejected by customer
     */
    public function canBeRejectedByCustomer(): bool
    {
        $customerId = Auth::id();

        return $this->status === 'sent' &&
               $this->belongsToCustomer($customerId) &&
               (!$this->customer_approval_status || $this->customer_approval_status === 'pending');
    }

    /**
     * Check if quotation is pending customer approval
     */
    public function isPendingCustomerApproval(): bool
    {
        return $this->status === 'sent' &&
               (!$this->customer_approval_status || $this->customer_approval_status === 'pending');
    }

    /**
     * Check if quotation is approved by customer
     */
    public function isApprovedByCustomer(): bool
    {
        return $this->customer_approval_status === 'approved';
    }

    /**
     * Check if quotation is rejected by customer
     */
    public function isRejectedByCustomer(): bool
    {
        return $this->customer_approval_status === 'rejected';
    }

    /**
     * Check if quotation belongs to customer (with multiple checks)
     */
    public function belongsToCustomer($customerId): bool
    {
        // Check direct customer_id first
        if ($this->customer_id && $this->customer_id === $customerId) {
            return true;
        }

        // Check through design_request relationship
        if ($this->designRequest && $this->designRequest->customer_id === $customerId) {
            return true;
        }

        return false;
    }

    /**
     * Scope queries
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Check if quotation is expired
     */
    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    /**
     * Check if quotation can be edited (only account managers can edit drafts)
     */
    public function canBeEdited(): bool
    {
        return $this->status === 'draft' && Auth::user()->role === 'account_manager';
    }

    /**
     * Calculate totals from line items
     */
    public function calculateTotals(): void
    {
        $subtotal = 0;

        if ($this->line_items) {
            foreach ($this->line_items as $item) {
                $subtotal += $item['total'] ?? 0;
            }
        }

        $taxAmount = $subtotal * $this->tax_rate;
        $totalAmount = $subtotal + $taxAmount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'amount' => $subtotal,
        ]);
    }

    /**
     * Colocation services included in this quotation
     */
// In Quotation.php model
public function colocationServices()
{
    // This relationship connects to colocation_list table via service_id
    return $this->belongsToMany(ColocationList::class, 'quotation_colocation_services',
                                'quotation_id', 'colocation_service_id',
                                'id', 'service_id')
                ->withPivot(['quantity', 'duration_months', 'unit_price', 'total_price'])
                ->withTimestamps();
}

    public function isViewableByCustomer(): bool
    {
        // Customers can only view sent, approved, or rejected quotations
        return in_array($this->status, ['sent', 'approved', 'rejected']) &&
               $this->belongsToCustomer(Auth::id());
    }

    /**
     * Check if quotation can be approved (internal approval)
     */
    public function canBeApproved(): bool
    {
        // Only draft quotations can be approved internally
        return $this->status === 'draft';
    }

    /**
     * Check if quotation can be rejected (internal rejection)
     */
    public function canBeRejected(): bool
    {
        // Only draft quotations can be rejected internally
        return $this->status === 'draft';
    }

    /**
     * Check if quotation can be sent
     */
    public function canBeSent(): bool
    {
        // Only approved quotations can be sent to customers
        return $this->status === 'approved' && Auth::user()->role === 'admin';
    }

    /**
     * Mark as approved (internal approval)
     */
    public function markAsApproved($approvedBy, $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Mark as rejected (internal rejection)
     */
    public function markAsRejected($rejectedBy, $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $rejectedBy,
            'rejection_notes' => $notes,
        ]);
    }

    /**
     * Mark as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'customer_approval_status' => 'pending', // Set to pending when sent to customer
        ]);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        if ($this->amount === null) {
            return '$0.00';
        }
        return '$' . number_format((float) $this->amount, 2);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        if ($this->total_amount === null) {
            return '$0.00';
        }
        return '$' . number_format((float) $this->total_amount, 2);
    }

    /**
     * Get customer approval status badge color
     */
    public function getCustomerApprovalBadgeColor(): string
    {
        return match($this->customer_approval_status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get customer approval status display text
     */
    public function getCustomerApprovalDisplayText(): string
    {
        return match($this->customer_approval_status) {
            'approved' => 'Approved by Customer',
            'rejected' => 'Rejected by Customer',
            'pending' => 'Pending Customer Approval',
            default => 'Not Sent to Customer'
        };
    }

    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'sent' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'expired' => 'dark',
            default => 'light'
        };
    }

    public function getStatusDisplayText(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'sent' => 'Sent',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
            default => ucfirst($this->status)
        };
    }

    /**
     * Check if customer can request revision
     */
    public function canRequestRevision(): bool
    {
        // Customers can request revisions for sent quotations
        return $this->status === 'sent' && $this->belongsToCustomer(Auth::id());
    }

    /**
     * Check if quotation is owned by current customer
     */
    public function isOwnedByCustomer(): bool
    {
        return $this->belongsToCustomer(Auth::id());
    }

    /**
     * Check if quotation has a contract
     */
    public function hasContract(): bool
    {
        return $this->contract !== null;
    }

    /**
     * Get contract status for display
     */
    public function getContractStatusDisplay(): string
    {
        if (!$this->contract) {
            return 'No Contract';
        }

        return $this->contract->getStatusDisplayText();
    }

    /**
     * Get contract status badge color
     */
    public function getContractStatusBadgeColor(): string
    {
        if (!$this->contract) {
            return 'secondary';
        }

        return $this->contract->getStatusBadgeColor();
    }

       /**
     * Get the lease associated with the quotation
     */
    public function lease(): HasOne
    {
        return $this->hasOne(Lease::class, 'quotation_id');
    }

    /**
     * Get the contract associated with the quotation
     */
    // public function contract(): HasOne
    // {
    //     return $this->hasOne(Contract::class, 'quotation_id');
    // }

    /**
     * Get the customer that owns the quotation
     */
    // public function customer(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'customer_id');
    // }

    /**
     * Get the account manager for this quotation
     */
    // public function accountManager(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'account_manager_id');
    // }
}
