<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiberPricing extends Model
{
    protected $table = 'fiber_pricing';

    protected $fillable = [
        'link_type',
        'base_rate_km_kes',
        'base_rate_km_usd',
        'volume_discount_threshold',
        'volume_discount_percent',
        'description'
    ];

    protected $casts = [
        'base_rate_km_kes' => 'decimal:2',
        'base_rate_km_usd' => 'decimal:2',
        'volume_discount_percent' => 'decimal:2'
    ];

    public static function calculateCost($distance, $linkType, $fiberCores)
    {
        $pricing = self::where('link_type', $linkType)->first();

        if (!$pricing) {
            return 0;
        }

        $baseCost = $distance * $pricing->base_rate_km_kes;

        // Apply volume discount
        if ($distance >= $pricing->volume_discount_threshold && $pricing->volume_discount_threshold > 0) {
            $baseCost *= (1 - $pricing->volume_discount_percent / 100);
        }

        // Adjust for fiber cores (cores beyond 24 have 2% premium per additional core)
        if ($fiberCores > 24) {
            $baseCost *= (1 + ($fiberCores - 24) * 0.02);
        }

        return round($baseCost, 2);
    }
}
