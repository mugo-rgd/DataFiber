<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTransaction extends Model
{
    protected $table = 'credit_transactions';

    protected $fillable = [
        'user_id',
        'credit_id',
        'payment_id',
        'amount',
        'currency',
        'type',
        'reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Transaction type constants
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_REFUND = 'refund';
    const TYPE_EXPIRY = 'expiry';

    /**
     * Get the user who owns this transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the credit associated with this transaction
     */
    public function credit(): BelongsTo
    {
        return $this->belongsTo(CustomerCredit::class, 'credit_id');
    }

    /**
     * Get the payment associated with this transaction
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Check if this is a deposit
     */
    public function isDeposit(): bool
    {
        return $this->type === self::TYPE_DEPOSIT;
    }

    /**
     * Check if this is a withdrawal
     */
    public function isWithdrawal(): bool
    {
        return $this->type === self::TYPE_WITHDRAWAL;
    }

    /**
     * Check if this is a refund
     */
    public function isRefund(): bool
    {
        return $this->type === self::TYPE_REFUND;
    }

    /**
     * Check if this is an expiry adjustment
     */
    public function isExpiry(): bool
    {
        return $this->type === self::TYPE_EXPIRY;
    }

    /**
     * Get the formatted amount with sign
     */
    public function getSignedAmount(): string
    {
        $sign = in_array($this->type, [self::TYPE_DEPOSIT, self::TYPE_REFUND]) ? '+' : '-';
        return $sign . ' ' . $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Scope for deposits only
     */
    public function scopeDeposits($query)
    {
        return $query->where('type', self::TYPE_DEPOSIT);
    }

    /**
     * Scope for withdrawals only
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('type', self::TYPE_WITHDRAWAL);
    }

    /**
     * Scope for transactions by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for transactions by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
