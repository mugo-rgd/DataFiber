<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    protected $table = 'payment_allocations';

    protected $fillable = [
        'payment_id',
        'invoice_id',
        'allocated_amount',
        'currency',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the payment that made this allocation
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the invoice that received this allocation
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ConsolidatedBilling::class, 'invoice_id');
    }

    /**
     * Get the remaining balance of the invoice after this allocation
     */
    public function getInvoiceRemainingBalance(): float
    {
        $totalPaid = PaymentAllocation::where('invoice_id', $this->invoice_id)
            ->where('id', '<=', $this->id)
            ->sum('allocated_amount');

        return $this->invoice->total_amount - $totalPaid;
    }

    /**
     * Check if this allocation fully paid the invoice
     */
    public function fullyPaidInvoice(): bool
    {
        $totalPaidToInvoice = PaymentAllocation::where('invoice_id', $this->invoice_id)
            ->sum('allocated_amount');

        return $totalPaidToInvoice >= $this->invoice->total_amount;
    }
}
