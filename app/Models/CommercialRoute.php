<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommercialRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'option',
        'name_of_route',
        'region',
        'fiber_cores',
        'no_of_cores_required',
        'unit_cost_per_core_per_km_per_month',
        'approx_distance_km',
        'capital_expenditure',
        'availability',
        'currency',
        'tech_type'
    ];

    protected $casts = [
        'fiber_cores' => 'integer',
        'no_of_cores_required' => 'integer',
        'unit_cost_per_core_per_km_per_month' => 'decimal:2',
        'approx_distance_km' => 'decimal:2',
        'capital_expenditure' => 'decimal:2',
    ];

    /**
     * Calculate monthly cost for a given number of cores
     */
    public function calculateMonthlyCost($cores = null)
    {
        $cores = $cores ?? $this->no_of_cores_required;

        // Monthly cost = cores × unit_cost_per_core_per_km_per_month × distance_km
        $monthlyCost = $cores * $this->unit_cost_per_core_per_km_per_month * $this->approx_distance_km;

        return round($monthlyCost, 2);
    }

    /**
     * Calculate total contract value
     */
    public function calculateTotalContractValue($cores = null, $durationMonths = 12)
    {
        $monthlyCost = $this->calculateMonthlyCost($cores);

        // Total = monthly cost × duration + capital expenditure (one-time)
        $total = ($monthlyCost * $durationMonths) + $this->capital_expenditure;

        return round($total, 2);
    }

    /**
     * Get pricing breakdown
     */
    public function getPricingBreakdown($cores = null, $durationMonths = 12)
    {
        $cores = $cores ?? $this->no_of_cores_required;
        $monthlyCost = $this->calculateMonthlyCost($cores);
        $totalContractValue = $this->calculateTotalContractValue($cores, $durationMonths);

        return [
            'cores' => $cores,
            'unit_cost_per_core_per_month' => $this->unit_cost_per_core_per_km_per_month * $this->approx_distance_km,
            'monthly_cost' => $monthlyCost,
            'capital_expenditure' => $this->capital_expenditure,
            'total_contract_value' => $totalContractValue,
            'duration_months' => $durationMonths,
            'currency' => $this->currency,
        ];
    }

    /**
     * Scope to get available routes
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability', 'YES');
    }

    /**
     * Scope by technology type
     */
    public function scopeByTechnology($query, $techType)
    {
        return $query->where('tech_type', $techType);
    }

    /**
     * Scope by option (Premium/Non Premium/Metro)
     */
    public function scopeByOption($query, $option)
    {
        return $query->where('option', $option);
    }

    /**
     * Get service type based on option
     */
    public function getServiceTypeAttribute()
    {
        $serviceTypes = [
            'Premium' => 'Premium Fibre Lease',
            'Non Premium' => 'Standard Fibre Lease',
            'Metro' => 'Metro Fibre Lease'
        ];

        return $serviceTypes[$this->option] ?? $this->option;
    }

    /**
     * Get technology type display name
     */
    public function getTechnologyTypeAttribute()
    {
        $techTypes = [
            'ADSS' => 'All-Dielectric Self-Supporting (ADSS)',
            'OPGW' => 'Optical Ground Wire (OPGW)',
            'UG' => 'Underground (UG)',
            'OPGW/ADSS' => 'OPGW/ADSS Hybrid'
        ];

        return $techTypes[$this->tech_type] ?? $this->tech_type;
    }

    /**
     * Relationship with quotations
     */
    public function quotations()
    {
        return $this->belongsToMany(Quotation::class, 'quotation_commercial_routes')
                    ->withPivot(['quantity', 'duration_months', 'unit_price', 'total_price'])
                    ->withTimestamps();
    }

    /**
     * Calculate per core cost per km
     */
    public function getPerCorePerKmCostAttribute()
    {
        return $this->unit_cost_per_core_per_km_per_month;
    }

    /**
     * Get total fiber length in km
     */
    public function getTotalFiberLengthAttribute()
    {
        return $this->approx_distance_km;
    }

    /**
     * Check if route has available cores
     */
    public function hasAvailableCores($requestedCores)
    {
        return $this->fiber_cores === null || $requestedCores <= $this->fiber_cores;
    }
}
