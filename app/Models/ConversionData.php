<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConversionData extends Model
{
    use HasFactory;

    protected $table = 'conversion_data';

    protected $fillable = [
        'customer_ref',
        'customer_id',
        'customer_name',
        'route_name',
        'links_name',
        'cores_leased',
        'bandwidth',
        'distance_km',
        'price_per_core_per_km_per_month_usd',
        'monthly_link_value_usd',
        'monthly_link_kes',
        'link_class',
        'contract_duration_yrs',
        'total_contract_value_usd',
        'total_contract_value_kes'
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'price_per_core_per_km_per_month_usd' => 'decimal:2',
        'monthly_link_value_usd' => 'decimal:2',
        'monthly_link_kes' => 'decimal:2',
        'total_contract_value_usd' => 'decimal:2',
        'total_contract_value_kes' => 'decimal:2',
        'cores_leased' => 'integer',
        'contract_duration_yrs' => 'integer',
    ];

    // Scopes for common queries
    public function scopeByCustomer($query, $customerName)
    {
        return $query->where('customer_name', $customerName);
    }

    public function scopeByLinkClass($query, $linkClass)
    {
        return $query->where('link_class', $linkClass);
    }

    public function scopeActiveContracts($query)
    {
        return $query->where('contract_duration_yrs', '>', 0);
    }

    public function scopeHighValue($query, $threshold = 10000)
    {
        return $query->where('monthly_link_value_usd', '>', $threshold);
    }

    // Calculate annual value
    public function getAnnualValueUsdAttribute()
    {
        return $this->monthly_link_value_usd * 12;
    }

    // Calculate total contract value if not set
    public function getCalculatedTotalContractValueUsdAttribute()
    {
        if ($this->total_contract_value_usd) {
            return $this->total_contract_value_usd;
        }

        return $this->monthly_link_value_usd * 12 * ($this->contract_duration_yrs ?: 0);
    }

    // Relationship with customer summary (if we create a customers table)
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_name', 'name');
    }
}
