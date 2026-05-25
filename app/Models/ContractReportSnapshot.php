<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractReportSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'currency',
        'status',
        'contract_count',
        'contract_value',
        'expiring_30_days',
        'expiring_60_days',
        'expiring_90_days',
        'renewal_revenue_at_risk',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];
}
