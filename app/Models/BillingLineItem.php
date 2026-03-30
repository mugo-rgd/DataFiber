<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingLineItem extends Model
{
    use HasFactory;
protected $table = 'billing_line_items';
    protected $fillable = [
        'consolidated_billing_id',
        'lease_id',
        'amount',
        'currency',
        'billing_cycle',
        'period_start',
        'period_end',
        'description',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'amount' => 'decimal:2',

         ];

          public function billing(): BelongsTo
    {
        return $this->belongsTo(ConsolidatedBilling::class, 'consolidated_billing_id');
    }

    public function lease()
    {
        // Assuming you have a Lease model
        return $this->belongsTo(Lease::class);
    }

    /**
     * Get the tax rate for this line item
     * Based on KRA requirements, dark fibre and colocation services have different VAT rates
     */
    public function getTaxRateAttribute(): float
    {
        $description = strtolower($this->description ?? '');

        // Check for VAT-exempt services
        if (str_contains($description, 'dark fibre') || str_contains($description, 'colocation')) {
            // Standard VAT rate for telecommunications in Kenya is 16%
            return 16.00;
        }

        // Default to standard VAT rate
        return 16.00;
    }

    /**
     * Get the HSCode for this item if needed
     */
    public function getHSCodeAttribute(): string
    {
        // For 16% VAT items, HSCode can be empty
        // Only needed for 0% or 8% VAT items
        return '';
    }
    public function consolidatedBilling()
    {
        return $this->belongsTo(ConsolidatedBilling::class);
    }

}
