<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerCredit extends Model
{
    protected $table = 'customer_credits';

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'status',
        'notes',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'active',
        'currency' => 'KES',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the customer who owns this credit
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all transactions for this credit
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    /**
     * Add amount to this credit
     */
    public function addAmount(float $amount, string $reference = null, string $notes = null): CreditTransaction
    {
        $this->amount += $amount;
        $this->save();

        return CreditTransaction::create([
            'user_id' => $this->user_id,
            'credit_id' => $this->id,
            'amount' => $amount,
            'currency' => $this->currency,
            'type' => 'deposit',
            'reference' => $reference,
            'notes' => $notes,
        ]);
    }

    /**
     * Deduct amount from this credit
     */
    public function deductAmount(float $amount, string $reference = null, string $notes = null): CreditTransaction
    {
        if ($amount > $this->amount) {
            throw new \Exception('Insufficient credit amount');
        }

        $this->amount -= $amount;

        if ($this->amount <= 0) {
            $this->status = self::STATUS_USED;
        }

        $this->save();

        return CreditTransaction::create([
            'user_id' => $this->user_id,
            'credit_id' => $this->id,
            'amount' => $amount,
            'currency' => $this->currency,
            'type' => 'withdrawal',
            'reference' => $reference,
            'notes' => $notes,
        ]);
    }

    /**
     * Check if credit is active
     */
    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            $this->status = self::STATUS_EXPIRED;
            $this->save();
            return false;
        }

        return $this->amount > 0;
    }

    /**
     * Check if credit is expired
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED ||
               ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Get available balance
     */
    public function getAvailableBalance(): float
    {
        if (!$this->isActive()) {
            return 0;
        }

        return $this->amount;
    }

    /**
     * Scope for active credits
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('amount', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for credits by currency
     */
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', strtoupper($currency));
    }

    /**
     * Scope for credits expiring soon
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now());
    }
}
