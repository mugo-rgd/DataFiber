<?php
// app/Models/Transaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'transaction_number',
        'transaction_date',
        'type',
        'description',
        'amount',
        'currency',          // Added: KSH or USD
        'direction',
        'balance',
        'reference',
        'status',
        'created_by',        // Added: user who created the transaction
        'payment_method',     // Added: credit_card, bank_transfer, cash, etc.
        'category',          // Added: invoice_payment, expense, etc.
        'reference_number',  // Added: external reference
        'notes',             // Added: additional notes
        'completed_at'       // Added: when transaction was completed
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user (customer) that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for user() to maintain compatibility
     */
    public function customer()
    {
        return $this->user();
    }

    /**
     * Get the user who created this transaction
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the consolidated billing associated with this transaction
     */
    public function consolidatedBilling()
    {
        return $this->belongsTo(ConsolidatedBilling::class, 'reference', 'billing_number');
    }

    /**
     * Alias for consolidatedBilling() - for compatibility with views
     */
    public function billing()
    {
        return $this->belongsTo(ConsolidatedBilling::class, 'reference', 'billing_number');
    }

    // ==================== SCOPES ====================

    /**
     * Scope for invoices (income)
     */
    public function scopeInvoices($query)
    {
        return $query->where('type', 'income')->where('category', 'invoice_payment');
    }

    /**
     * Scope for expenses
     */
    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope for a specific currency
     */
    public function scopeCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope for KSH transactions
     */
    public function scopeKsh($query)
    {
        return $query->where('currency', 'KSH');
    }

    /**
     * Scope for USD transactions
     */
    public function scopeUsd($query)
    {
        return $query->where('currency', 'USD');
    }

    /**
     * Scope for a specific user/customer
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope for completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for income transactions
     */
    public function scopeIncome($query)
    {
        return $query->where('direction', 'in');
    }

    /**
     * Scope for expense transactions
     */
    public function scopeExpense($query)
    {
        return $query->where('direction', 'out');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get formatted amount with currency symbol
     */
    public function getFormattedAmountAttribute(): string
    {
        if ($this->currency == 'USD') {
            return '$' . number_format($this->amount, 2);
        }
        return 'KSh ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted balance with currency symbol
     */
    public function getFormattedBalanceAttribute(): string
    {
        if ($this->currency == 'USD') {
            return '$' . number_format($this->balance, 2);
        }
        return 'KSh ' . number_format($this->balance, 2);
    }

    /**
     * Get amount with sign based on direction
     */
    public function getSignedAmountAttribute(): string
    {
        $sign = $this->direction == 'in' ? '+' : '-';
        return $sign . ' ' . $this->formatted_amount;
    }

    /**
     * Get amount class for styling (text-success or text-danger)
     */
    public function getAmountClassAttribute(): string
    {
        return $this->direction == 'in' ? 'text-success' : 'text-danger';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            default => 'info'
        };
    }

    /**
     * Get type badge class
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return $this->type == 'income' ? 'success' : 'danger';
    }

    /**
     * Get formatted transaction date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->transaction_date->format('d M Y');
    }

    /**
     * Get formatted created at date
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d M Y H:i');
    }

    // ==================== METHODS ====================

    /**
     * Generate unique transaction number
     */
    public static function generateTransactionNumber(): string
    {
        $prefix = 'TXN';
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $lastTransaction = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->transaction_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$year}{$month}{$day}{$newNumber}";
    }

    /**
     * Check if transaction is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is income
     */
    public function isIncome(): bool
    {
        return $this->direction === 'in';
    }

    /**
     * Check if transaction is expense
     */
    public function isExpense(): bool
    {
        return $this->direction === 'out';
    }

    /**
     * Mark transaction as completed
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(): bool
    {
        return $this->update([
            'status' => 'failed',
            'completed_at' => now()
        ]);
    }

    /**
     * Get transaction summary for reporting
     */
    public static function getSummary($startDate = null, $endDate = null): array
    {
        $query = self::query();

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        return [
            'total_income_ksh' => (clone $query)->income()->ksh()->completed()->sum('amount'),
            'total_income_usd' => (clone $query)->income()->usd()->completed()->sum('amount'),
            'total_expense_ksh' => (clone $query)->expense()->ksh()->completed()->sum('amount'),
            'total_expense_usd' => (clone $query)->expense()->usd()->completed()->sum('amount'),
            'count' => $query->count(),
            'pending_count' => (clone $query)->pending()->count(),
        ];
    }
}
