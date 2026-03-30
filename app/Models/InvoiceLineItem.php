<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLineItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_line_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'tax_rate',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the invoice that owns the line item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Calculate total price before saving (optional automation)
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($lineItem) {
            // Auto-calculate total_price if not set
            if (empty($lineItem->total_price) && !empty($lineItem->quantity) && !empty($lineItem->unit_price)) {
                $lineItem->total_price = $lineItem->quantity * $lineItem->unit_price;
            }
        });
    }
}
