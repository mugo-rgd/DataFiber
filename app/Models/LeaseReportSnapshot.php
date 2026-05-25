<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaseReportSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'currency',
        'service_type',
        'status',
        'region',
        'lease_count',
        'monthly_revenue',
        'contract_value',
        'leased_distance_km',
        'leased_cores',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];
}
