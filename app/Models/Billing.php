<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Billing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'invoice_type',
        'reference_number',
        'billing_cycle',
        'customer_id',
        'lease_id',
        'design_request_id',
        'billing_type',
        'subtotal_amount',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'tax_rate',
        'invoice_date',
        'due_date',
        'paid_date',
        'period_start_date',
        'period_end_date',
        'status',
        'payment_method',
        'payment_reference',
        'payment_notes',
        'service_description',
        'terms_conditions',
        'customer_notes',
        'internal_notes',
        'service_location',
        'circuit_id',
        'port_number',
        'bandwidth_mbps',
        'service_level',
        'created_by',
        'approved_by',
        'sent_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
        'period_start_date' => 'datetime',
        'period_end_date' => 'datetime',
        'sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'subtotal_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'bandwidth_mbps' => 'decimal:2',
    ];

      /**
     * Accessor for billing_date (backward compatibility)
     */
    public function getBillingDateAttribute()
    {
        return $this->invoice_date;
    }

    /**
     * Accessor for safe date formatting
     */
    public function getFormattedInvoiceDateAttribute()
    {
        return $this->invoice_date ? $this->invoice_date->format('M j, Y') : 'Not set';
    }

   public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillingItem::class);
    }
    /**
     * Or as a regular method to get overdue billings
     */
    public static function overdue()
    {
        return static::where('due_date', '<', now())
                    ->where('status', '!=', 'paid')
                    ->get();
    }

      public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relationship with the user who created the billing
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor for creator (alias for createdBy for backward compatibility)
     */
    public function getCreatorAttribute()
    {
        return $this->createdBy;
    }

    /**
     * Accessor for amount (backward compatibility)
     */
    public function getAmountAttribute()
    {
        return $this->total_amount;
    }

        public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? $this->due_date->format('M j, Y') : 'Not set';
    }

    /**
     * Check if billing is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status != 'paid';
    }

    /**
     * Boot function to auto-generate required fields
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-generate invoice number if empty
            if (empty($model->invoice_number)) {
                $model->invoice_number = static::generateInvoiceNumber();
            }

            // Set invoice date to current date if empty
            if (empty($model->invoice_date)) {
                $model->invoice_date = now();
            }

            // Set default service description if empty
            if (empty($model->service_description) && !empty($model->description)) {
                $model->service_description = $model->description;
            } elseif (empty($model->service_description)) {
                $model->service_description = 'Service provided';
            }
        });
    }

    /**
     * Generate invoice number
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV-';
        $timestamp = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        return "{$prefix}{$timestamp}-{$random}";
    }

    /**
     * Scope for overdue billings
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', 'pending');
    }
}
