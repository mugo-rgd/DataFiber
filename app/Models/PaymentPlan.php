<?php
// app/Models/PaymentPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentPlan extends Model
{
    protected $table = 'payment_plans';

    protected $fillable = [
        'consolidated_billing_id',
        'user_id',
        'total_amount',
        'down_payment',
        'installment_count',
        'installment_amount',
        'start_date',
        'end_date',
        'frequency',
        'status',
        'terms',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
        'total_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'installment_amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ConsolidatedBilling::class, 'consolidated_billing_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PaymentPlanInstallment::class);
    }

    public function getPaidTotalAttribute(): float
    {
        return $this->installments()->sum('paid_amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_total;
    }

    public function getCompletionPercentageAttribute(): float
    {
        return $this->total_amount > 0 ? ($this->paid_total / $this->total_amount) * 100 : 0;
    }
}
