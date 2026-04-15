<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsolidatedBilling extends Model
{
    use HasFactory;
protected $table = 'consolidated_billings';
    protected $fillable = [
       'billing_number',
        'user_id',
        'billing_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'total_amount_kes',
        'paid_amount_kes',
        'currency',
        'kra_pin',
        'exchange_rate',
        'exchange_rate_source',
        'exchange_rate_date',
        'exchange_rate_timestamp',
        'description',
        'status',
        'metadata',
        'payment_date',
        'kra_invoice_number',
        'kra_qr_code',
        'kra_status',
        'kra_response',
        'buyer_pin',
        'tevin_control_code',
        'tevin_qr_code',
        'tevin_invoice_number',
        'tevin_middleware_invoice_number',
        'tevin_committed_at',
        'tevin_submitted_by',
        'tev_committed_timestamp',
        'tevin_submitted_at',
        'tevin_status',
        'tevin_response',
        'tevin_error_message',
        'tevin_error_code',
        'tev_submission_error'
    ];

    protected $casts = [
        'metadata' => 'array',
        'kra_response' => 'array',
        'tevin_response' => 'array',
        'total_amount' => 'decimal:2',
        'billing_date' => 'date',
        'exchange_rate_date' => 'date',
        'due_date' => 'date',
        'tevin_committed_at' => 'datetime',
         'tevin_job_started_at' => 'datetime',
        'tevin_job_failed_at' => 'datetime',
        'tevin_submitted_at' => 'datetime',
        'tev_committed_timestamp' => 'datetime',
        'tev_transmission_timestamp' => 'datetime',
        'total_amount_kes' => 'decimal:2',
        'exchange_rate' => 'decimal:2',
         'payment_date' => 'datetime',
        'paid_amount' => 'decimal:2',
        'paid_amount_kes' => 'decimal:2',
            ];

     public function getTevinStatusLabelAttribute(): string
    {
        return match($this->tevin_status) {
            'queued' => 'Queued',
            'processing' => 'Processing',
            'submitted' => 'Submitted',
            'duplicate' => 'Duplicate',
            'failed' => 'Failed',
            'permanently_failed' => 'Permanently Failed',
            default => 'Not Submitted'
        };
    }

    /**
     * 检查是否可以重新提交到TEVIN
     */
    public function canResubmitToTevin(): bool
    {
        return in_array($this->tevin_status, ['failed', 'permanently_failed', null]);
    }

    /**
     * 获取TEVIN QR码URL（如果有）
     */
    public function getTevinQrCodeUrlAttribute(): ?string
    {
        return $this->tevin_qr_code;
    }

    /**
     * 获取TEVIN控制码（如果有）
     */
    public function getTevinControlCodeAttribute(): ?string
    {
        return $this->tev_control_code;
    }
    // Relationship to billing_line_items
    public function lineItems()
    {
        return $this->hasMany(BillingLineItem::class, 'consolidated_billing_id');
    }
   public function billingLineItems()
{
    return $this->hasMany(BillingLineItem::class);
}
    // Alias for backward compatibility
    public function items()
    {
        return $this->lineItems();
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    public function leases()
    {
        return $this->hasManyThrough(Lease::class, BillingLineItem::class, 'consolidated_billing_id', 'id', 'id', 'lease_id');
    }

// In BillingLineItem model
public function consolidatedBilling()
{
    return $this->belongsTo(ConsolidatedBilling::class);
}

public function lease()
{
    return $this->belongsTo(Lease::class);
}

public function customer()
{
    return $this->belongsTo(User::class, 'user_id');
}


    /**
     * Get the user that owns the billing
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the transaction for this billing
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'reference', 'billing_number')
            ->where('type', 'invoice');
    }

    /**
     * Scope for pending billings
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'sent']);
    }

    /**
     * Scope for paid billings
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for overdue billings
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pending', 'sent'])
            ->where('due_date', '<', now());
    }

    /**
     * Check if billing is overdue
     */
    public function isOverdue(): bool
    {
        return in_array($this->status, ['pending', 'sent'])
            && $this->due_date < now();
    }

    public function getFormattedTotalAttribute()
    {
        $symbol = $this->currency === 'USD' ? '$' : 'KSh';
        return $symbol . ' ' . number_format($this->total_amount, 2);
    }

    public function getStatusColorAttribute()
    {
        return [
            'paid' => 'success',
            'pending' => 'warning',
            'overdue' => 'danger',
            'sent' => 'info',
            'partial' => 'primary',
            'cancelled' => 'secondary'
        ][$this->status] ?? 'secondary';
    }
}
