<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'designer_id',
        'request_number',
        'cores_required',
        'unit_cost',
        'distance',
        'terms',
        'technology_type',
        'link_class',
        'route_name',
        'tax_rate',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'distance' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'cores_required' => 'integer',
        'terms' => 'integer',
    ];

    /**
     * Get the customer that owns the design item.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the designer that owns the design item.
     */
    public function designer()
    {
        return $this->belongsTo(User::class);
    }
}
