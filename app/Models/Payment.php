<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    protected $table = 'payments';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_VALIDATED = 'validated';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REFUNDED = 'refunded';

    // Payment method constants
    const METHOD_BANK_TRANSFER = 'Bank Transfer (RTGS/EFT)';
    const METHOD_CHEQUE = 'Cheque Deposit';
    const METHOD_CASH = 'Cash Deposit';
    const METHOD_MPESA = 'M-Pesa';
    const METHOD_MOBILE_MONEY = 'Mobile Money';

    protected $fillable = [
        'payment_number',
        'user_id',
        'billing_id',
        'transaction_id',
        'amount',
        'currency',
        'amount_kes',
        'amount_usd',
        'payment_date',
        'payment_method',
        'reference_number',
        'bank_name',
        'bank_branch',
        'deposit_slip_path',
        'status',
        'validated_by',
        'validated_at',
        'validation_notes',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_kes' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'payment_date' => 'date',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = static::generatePaymentNumber();
            }

            // Set currency-specific amount
            if ($payment->currency === 'KES') {
                $payment->amount_kes = $payment->amount;
                $payment->amount_usd = null;
            } else {
                $payment->amount_usd = $payment->amount;
                $payment->amount_kes = null;
            }
        });
    }

    // ========== RELATIONSHIPS ==========

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(ConsolidatedBilling::class, 'billing_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function allocatedInvoices(): BelongsToMany
    {
        return $this->belongsToMany(
            ConsolidatedBilling::class,
            'payment_allocations',
            'payment_id',
            'invoice_id'
        )->withPivot('allocated_amount', 'currency')->withTimestamps();
    }

    // ========== ACCESSORS ==========

    public function getStatusBadgeColorAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_VALIDATED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_REFUNDED => 'info',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_PENDING => 'Pending Validation',
            self::STATUS_VALIDATED => 'Validated',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_REFUNDED => 'Refunded',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    public function getFormattedAmountKesAttribute(): string
    {
        return $this->amount_kes ? 'KES ' . number_format($this->amount_kes, 2) : 'N/A';
    }

    public function getFormattedAmountUsdAttribute(): string
    {
        return $this->amount_usd ? 'USD ' . number_format($this->amount_usd, 2) : 'N/A';
    }

    // ========== HELPER METHODS ==========

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function validatePayment(int $validatorId, string $notes = null): bool
    {
        $this->status = self::STATUS_VALIDATED;
        $this->validated_by = $validatorId;
        $this->validated_at = now();
        $this->validation_notes = $notes;

        return $this->save();
    }

    public function rejectPayment(int $validatorId, string $reason): bool
    {
        $this->status = self::STATUS_REJECTED;
        $this->validated_by = $validatorId;
        $this->validated_at = now();
        $this->validation_notes = $reason;

        return $this->save();
    }

    public function getAllocatedAmount(): float
    {
        return $this->allocations()->sum('allocated_amount');
    }

    public function getUnallocatedAmount(): float
    {
        $amount = $this->currency === 'KES'
            ? ($this->amount_kes ?? $this->amount)
            : ($this->amount_usd ?? $this->amount);

        return $amount - $this->getAllocatedAmount();
    }

    public function isFullyAllocated(): bool
    {
        return $this->getUnallocatedAmount() <= 0.01;
    }

    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');

        $lastPayment = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->payment_number, -4));
            $sequence = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }

        return $prefix . $year . $month . $sequence;
    }

    // ========== SCOPES ==========

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeValidated($query)
    {
        return $query->where('status', self::STATUS_VALIDATED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', strtoupper($currency));
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month);
    }
}
