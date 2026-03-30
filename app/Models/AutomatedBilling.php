<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomatedBilling extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
        'amount',
        'billing_date',
        'due_date',
        'status',
        'description',
        'failure_reason'
    ];

    protected $casts = [
        'billing_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship with customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Scope for pending billings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'generated');
    }

    /**
     * Scope for failed billings
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mark as sent
     */
    public function markAsSent()
    {
        $this->update(['status' => 'sent']);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($reason)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason
        ]);
    }
}
