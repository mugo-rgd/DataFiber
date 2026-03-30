<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices';

    protected $fillable = [
        'invoice_number',
        'user_id',
        'lease_id',
        'amount',
        'tax_amount',
        'total_amount',
        'currency',
        'invoice_date',
        'due_date',
        'paid_date',
        'status',
        'description',
        'line_items',
        'notes',
        'payment_terms',
        'billed_at',  // ← COMMA ADDED HERE
    ];

    protected $casts = [
        'line_items' => 'array',
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
        'billed_at' => 'datetime',
        'amount' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
    ];

    /**
     * Get the subscription associated with the invoice.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'paid');
    }

    /**
     * Get the line items for the invoice.
     */
    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class);
    }

    /**
     * Get the user that owns the invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lease that owns the invoice.
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Y') . '-';
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')->latest()->first();

        if ($lastInvoice) {
            $lastNumber = (int) str_replace($prefix, '', $lastInvoice->invoice_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
