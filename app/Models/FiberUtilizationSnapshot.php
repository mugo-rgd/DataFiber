<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiberUtilizationSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'network_id',
        'route_name',
        'region',
        'total_fibre_km',
        'leased_fibre_km',
        'available_fibre_km',
        'total_cores',
        'used_cores',
        'available_cores',
        'utilization_percent',
        'capacity_status',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];
}
