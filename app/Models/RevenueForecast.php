<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueForecast extends Model
{
    protected $fillable = [
        'forecast_date',
        'currency',
        'actual_revenue',
        'forecast_revenue',
        'growth_rate_percent',
        'forecast_method',
        'metadata',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'metadata' => 'array',
    ];
}
