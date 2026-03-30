<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * App\Models\Subscription
 *
 * @method static Builder|Subscription active()
 * @method static Builder|Subscription onTrial()
 * @method static Builder|Subscription cancelled()
 */
class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'quantity',
        'trial_ends_at',
        'ends_at',
        'amount',
        'interval',
        'interval_count',
        'status',
        'start_date',
        'next_billing_date',
        'metadata',
    ];

    protected $casts = [
        'trial_ends_at'      => 'datetime',
        'ends_at'            => 'datetime',
        'amount'             => 'decimal:2',
        'start_date'         => 'datetime',
        'next_billing_date'  => 'datetime',
        'metadata'           => 'array',
        'quantity'           => 'integer',
        'interval_count'     => 'integer',
    ];

    /* ---------------- Relationships ---------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /* ---------------- Instance checks (per subscription) ---------------- */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at?->isFuture() ?? false;
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled' || $this->ends_at !== null;
    }

    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    public function hasIncompletePayment(): bool
    {
        return $this->isPastDue() || $this->isUnpaid();
    }

    /* ---------------- Query scopes (for collections) ---------------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeOnTrial(Builder $query): Builder
    {
        return $query->whereNotNull('trial_ends_at')
                     ->where('trial_ends_at', '>', now());
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled')
                     ->orWhereNotNull('ends_at');
    }

    public function scopeNeedsBilling(Builder $query): Builder
    {
        return $query->where('next_billing_date', '<=', now())
                     ->where('status', 'active');
    }

    /* ---------------- Business logic helpers ---------------- */

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'ends_at' => now(),
        ]);
    }

    public function reactivate(): void
    {
        $this->update([
            'status' => 'active',
            'ends_at' => null,
        ]);
    }

    public function updateNextBillingDate(): void
    {
        $nextBillingDate = $this->calculateNextBillingDate();
        $this->update(['next_billing_date' => $nextBillingDate]);
    }

    protected function calculateNextBillingDate(): Carbon
    {
        if (!$this->next_billing_date) {
            return now()->addMonth();
        }

        return $this->next_billing_date->copy()->add($this->interval_count, $this->interval);
    }

    public function isDueForBilling(): bool
    {
        return $this->next_billing_date?->lte(now()) ?? false;
    }

    public function daysUntilNextBilling(): int
    {
        return $this->next_billing_date
            ? now()->diffInDays($this->next_billing_date, false)
            : 0;
    }

    /* ---------------- Accessors ---------------- */

    public function getTotalAmountAttribute(): float
    {
        return $this->amount * $this->quantity;
    }

    public function getServiceNameAttribute(): string
    {
        if (strpos($this->name, '-') !== false) {
            return trim(explode('-', $this->name)[0]);
        }
        return $this->name;
    }

    public function getPlanNameAttribute(): string
    {
        if (strpos($this->name, '-') !== false) {
            return trim(explode('-', $this->name)[1]);
        }
        return 'Basic Plan';
    }

    public function getBillingIntervalAttribute(): string
    {
        if ($this->interval_count > 1) {
            return "every {$this->interval_count} {$this->interval}s";
        }
        return "every {$this->interval}";
    }

    public function getNextBillingDateFormattedAttribute(): string
    {
        return $this->next_billing_date?->format('M j, Y') ?? 'Not set';
    }

    public function getStartDateFormattedAttribute(): string
    {
        return $this->start_date?->format('M j, Y') ?? 'Not set';
    }

  public function scopeExpired(Builder $query): Builder
    {
        return $query->where('ends_at', '<', now())
                    ->orWhere(function($q) {
                        $q->whereNotNull('trial_ends_at')
                          ->where('trial_ends_at', '<', now());
                    });
    }

         public function isExpired(): bool
    {
        return ($this->ends_at && $this->ends_at->isPast()) ||
               ($this->trial_ends_at && $this->trial_ends_at->isPast());
    }
}
