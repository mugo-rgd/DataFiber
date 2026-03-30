<?php
// app/Models/PaymentStatement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentStatement extends Model
{
    protected $table = 'payment_statements';

    protected $fillable = [
        'user_id', 'statement_number', 'statement_date',
        'period_start', 'period_end', 'opening_balance',
        'total_debits', 'total_credits', 'closing_balance',
        'status', 'file_path', 'generated_at', 'sent_at'
    ];

    protected $casts = [
        'statement_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'opening_balance' => 'decimal:2',
        'total_debits' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'generated_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    /**
     * Get the user that owns the statement
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
     * Get transactions for this statement period
     */
    public function getTransactions()
    {
        return Transaction::where('user_id', $this->user_id)
            ->whereBetween('transaction_date', [$this->period_start, $this->period_end])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Generate unique statement number
     */
    public static function generateStatementNumber()
    {
        $prefix = 'STM';
        $year = date('Y');
        $month = date('m');

        $lastStatement = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStatement) {
            $lastNumber = intval(substr($lastStatement->statement_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$year}{$month}{$newNumber}";
    }
}
