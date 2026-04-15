<?php

namespace App\Models;

use App\Services\AutomatedBillingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lease extends Model
{
    use HasFactory;


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'last_billed_at' => 'datetime',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'activated_at' => 'datetime',
        'terminated_at' => 'datetime',
        'acceptance_certificate_generated_at' => 'datetime',
        'test_date' => 'date',
        'monthly_cost' => 'decimal:2',
        'installation_fee' => 'decimal:2',
        'total_contract_value' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lease_number',
        'title',
        'service_type',
        'bandwidth',
        'cores_required',
        'account_manager_id',
        'technology',
        'start_location',
        'end_location',
        'host_location',
        'distance_km',
        'monthly_cost',
        'installation_fee',
        'total_contract_value',
        'currency',
        'start_date',
        'end_date',
        'contract_term_months',
        'billing_cycle',
        'status',
        'next_billing_date',
        'last_billed_at',
        'sent_at',
        'accepted_at',
        'activated_at',
        'terminated_at',
        'technical_specifications',
        'service_level_agreement',
        'terms_and_conditions',
        'special_requirements',
        'notes',
        'attachments',
        'test_report_path',
        'test_report_type',
        'test_date',
        'test_report_description',
        'acceptance_certificate_path',
        'acceptance_certificate_generated_at',
        'customer_id',
        'county_id',
        'quotation_id',
        'design_request_id',
    ];

         protected $appends = [
        'formatted_monthly_cost',
        'monthly_cost_with_currency',
        'formatted_start_date',
        'formatted_end_date',
        'expiry_status',
        'is_active',
        'is_expired',
        'is_expiring_soon',
        'days_until_expiry',
    ];


    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
      public function daysSinceStart()
    {
        if (!$this->start_date) {
            return 0;
        }

        return Carbon::parse($this->start_date)->diffInDays(Carbon::now());
    }
// In Lease model
protected static function booted()
{
    static::created(function ($lease) {
        if ($lease->status === 'active') {
            // Create initial billing if lease starts today or earlier
            if ($lease->start_date <= Carbon::today()) {
                app(AutomatedBillingService::class)->createInitialBilling($lease);
            }
        }
    });

    static::updated(function ($lease) {
        // If lease status changed to active
        if ($lease->isDirty('status') && $lease->status === 'active') {
            if ($lease->start_date <= Carbon::today()) {
                app(AutomatedBillingService::class)->createInitialBilling($lease);
            }
        }
    });
}
    public function designRequest()
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(LeaseBilling::class);
    }

/**
 * Get the billing line items for this lease
 */
public function billingLineItems(): HasMany
{
    return $this->hasMany(BillingLineItem::class, 'lease_id');
}

/**
 * Alias for billingLineItems() - for backward compatibility
 */
// public function billings()
// {
//     return $this->billingLineItems();
// }

      public function accountManager()
    {
        return $this->hasOneThrough(
            User::class,              // Target model (Account Manager)
            User::class,               // Intermediate model (Customer)
            'id',                      // Foreign key on intermediate table (users.id)
            'id',                       // Foreign key on target table (users.id)
            'customer_id',             // Local key on leases table
            'account_manager_id'       // Local key on intermediate table (users.account_manager_id)
        )->where('role', 'account_manager'); // Only get users with account_manager role
    }

    /**
     * Alternative simpler method to get account manager
     */
    public function getAccountManagerAttribute()
    {
        if (!$this->customer) {
            return null;
        }

        return User::find($this->customer->account_manager_id);
    }
    // Accessors
    public function getTotalRevenueAttribute()
    {
        if ($this->billing_cycle === 'one_time') {
            return $this->installation_fee + $this->monthly_cost;
        }

        $months = $this->contract_term_months;
        $monthlyRevenue = $this->monthly_cost * $months;
        return $monthlyRevenue + $this->installation_fee;
    }

    /**
     * Format monthly cost for display.
     */
    public function getFormattedMonthlyCostAttribute(): string
    {
        return number_format((float)($this->monthly_cost ?? 0), 2);
    }

    /**
     * Get monthly cost with currency symbol.
     */
    public function getMonthlyCostWithCurrencyAttribute(): string
    {
        $amount = number_format((float)($this->monthly_cost ?? 0), 2);
        $currency = strtoupper($this->currency ?? 'USD');

        return "{$amount} {$currency}";
    }

    /**
     * Get raw monthly cost as float.
     */
    public function getMonthlyCostFloatAttribute(): float
    {
        return (float)($this->monthly_cost ?? 0);
    }

    /**
     * Get formatted start date.
     */
    public function getFormattedStartDateAttribute(): string
    {
       return $this->start_date ? Carbon::parse($this->start_date)->format('M d, Y') : 'N/A';
    }

    /**
     * Get formatted end date.
     */
    public function getFormattedEndDateAttribute(): string
    {
        return $this->start_date ? Carbon::parse($this->end_date)->format('M d, Y') : 'N/A';
    }

    /**
     * Check if the lease is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && now()->gt($this->end_date);
    }

    /**
     * Get days until expiry (positive) or days since expiry (negative)
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->end_date) {
            return 999; // No end date set, return a large number
        }

        return Carbon::now()->diffInDays($this->end_date, false);
    }

    /**
     * Check if lease is active and not expired
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && !$this->is_expired;
    }

    /**
     * Check if lease is expiring soon (within 30 days)
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->status === 'active' &&
               $this->days_until_expiry > 0 &&
               $this->days_until_expiry <= 30;
    }

    /**
     * Get expiry status badge
     */
    public function getExpiryStatusAttribute(): string
    {
        if ($this->is_expired) {
            return 'expired';
        } elseif ($this->is_expiring_soon) {
            return 'expiring_soon';
        } else {
            return 'active';
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'expired')
              ->orWhere('end_date', '<=', now());
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to get leases for specific customer
     */
    public function scopeForCustomer($query, $customerId = null)
    {
        return $query->where('customer_id', $customerId ?? Auth::id());
    }

    /**
     * Scope to get leases for account manager's customers
     */
    public function scopeForAccountManager($query, $accountManagerId = null)
    {
        return $query->whereHas('customer', function($q) use ($accountManagerId) {
            $q->where('account_manager_id', $accountManagerId ?? Auth::id())
              ->where('role', 'customer');
        });
    }

    // Methods
    public function markAsSent()
    {
        $this->update([
            'status' => 'pending',
            'sent_at' => now()
        ]);
    }

    public function markAsAccepted()
    {
        $this->update([
            'status' => 'active',
            'accepted_at' => now(),
            'activated_at' => now()
        ]);
    }

    public function markAsActive()
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now()
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired'
        ]);
    }

    public function terminate()
    {
        $this->update([
            'status' => 'terminated',
            'terminated_at' => now()
        ]);
    }

    // Static methods
    public static function generateLeaseNumber()
    {
        $prefix = 'LSE';
        $date = now()->format('Ymd');
        $lastLease = static::where('lease_number', 'like', "{$prefix}-{$date}-%")->latest()->first();

        if ($lastLease) {
            $lastNumber = intval(substr($lastLease->lease_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "{$prefix}-{$date}-{$newNumber}";
    }

    /**
     * Calculate prorated amount for billing
     */
    private function calculateProratedAmount(Lease $lease, Carbon $periodStart, Carbon $periodEnd): float
    {
        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
        $daysBilled = $periodStart->diffInDays(now()) + 1;

        $dailyRate = $lease->monthly_cost / $daysInPeriod;
        return round($dailyRate * $daysBilled, 2);
    }

    public function isExpired(): bool
{
     // If it's already a Carbon instance
    if ($this->end_date instanceof Carbon) {
        return $this->end_date->isPast();
    }

    // If it's a string, parse it first
    return Carbon::parse($this->end_date)->isPast();
}

public function daysUntilExpiry(): int
{
    $endDate = $this->end_date instanceof \Carbon\Carbon
        ? $this->end_date
        : \Carbon\Carbon::parse($this->end_date);

    return now()->diffInDays($endDate, false);
}

/**
 * Safe method to get start date as Carbon instance
 */
public function getStartDateAttribute($value)
{
    return $value instanceof Carbon ? $value : Carbon::parse($value);
}

/**
 * Safe method to get end date as Carbon instance
 */
public function getEndDateAttribute($value)
{
    return $value instanceof Carbon ? $value : Carbon::parse($value);
}

public function getStatusBadgeClass(): string
{
    return match($this->status) {
        'draft' => 'secondary',
        'active' => 'success',
        'pending' => 'warning',
        'expired' => 'danger',
        'terminated' => 'dark',
        default => 'light'
    };
}

public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the support tickets for the lease.
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    ///////////
    //   public function quotation(): BelongsTo
    // {
    //     return $this->belongsTo(Quotation::class, 'quotation_id');
    // }

    /**
     * Get the design request associated with the lease
     */
    // public function designRequest(): BelongsTo
    // {
    //     return $this->belongsTo(DesignRequest::class, 'design_request_id');
    // }

    /**
     * Get the customer that owns the lease
     */
    // public function customer(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'customer_id');
    // }

    /**
     * Get all documents for this lease
     */
    // public function documents(): HasMany
    // {
    //     return $this->hasMany(Document::class);
    // }

    /**
     * Get the acceptance certificate for this lease
     */
    public function acceptanceCertificate(): HasOne
    {
        return $this->hasOne(AcceptanceCertificate::class, 'lease_id');
    }

    /**
     * Get the conditional certificate for this lease
     */
    public function conditionalCertificate(): HasOne
    {
        return $this->hasOne(ConditionalCertificate::class, 'lease_id');
    }

    /**
     * Get the contract for this lease
     */
    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class)->whereHas('quotation', function($query) {
            $query->where('id', $this->quotation_id);
        });
    }

    /**
     * Scope a query to only include overdue leases.
     */
    public function scopeOverdue($query)
    {
        return $query->where('next_billing_date', '<', now())
                    ->whereIn('status', ['active', 'pending']);
    }

    /**
     * Check if lease is overdue.
     */
    public function isOverdue()
    {
        return $this->next_billing_date &&
               $this->next_billing_date < now() &&
               in_array($this->status, ['active', 'pending']);
    }

       /**
     * Get the formatted total contract value.
     */
    public function getFormattedTotalContractValueAttribute()
    {
        $symbol = $this->currency === 'USD' ? '$' : '';
        return $symbol . number_format($this->total_contract_value, 2);
    }

    /**
     * Calculate remaining contract value.
     */
    public function getRemainingContractValueAttribute()
    {
        if ($this->status !== 'active') {
            return 0;
        }

        $remainingMonths = max(0, $this->end_date->diffInMonths(now()));
        return $remainingMonths * $this->monthly_cost;
    }

    /**
     * Calculate days until next billing.
     */
    public function getDaysUntilNextBillingAttribute()
    {
        if (!$this->next_billing_date) {
            return null;
        }

        return now()->diffInDays($this->next_billing_date, false);
    }
}
