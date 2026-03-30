<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingItem extends Model
{
    protected $fillable = [
        'billing_id',
        'item_type',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'tax_rate',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }
}
