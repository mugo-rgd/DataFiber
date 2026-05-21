<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomRoute extends Model
{
    protected $fillable = [
        'design_request_id',
        'created_by',
        'name_of_route',
        'region',
        'option',
        'tech_type',
        'fiber_cores',
        'no_of_cores_required',
        'unit_cost_per_core_per_km_per_month',
        'approx_distance_km',
        'capital_expenditure',
        'currency',
        'availability',
        'route_description',
        'design_notes',
        'contract_duration_months',
    ];

    protected $casts = [
        'fiber_cores' => 'integer',
        'no_of_cores_required' => 'integer',
        'unit_cost_per_core_per_km_per_month' => 'decimal:2',
        'approx_distance_km' => 'decimal:2',
        'capital_expenditure' => 'decimal:2',
        'contract_duration_months' => 'integer',
    ];
// In app/Models/CommercialRoute.php

public static function getGroupedByOption()
{
    $routes = [];
    $options = ['Premium', 'Non Premium', 'Metro'];

    foreach ($options as $option) {
        $routes[$option] = self::where('option', $option)
            ->where('availability', 'YES')
            ->get();
    }

    return $routes;
}
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getMonthlyCostAttribute(): float
    {
        return (float) $this->unit_cost_per_core_per_km_per_month
            * (float) $this->approx_distance_km
            * (int) $this->no_of_cores_required;
    }
}
