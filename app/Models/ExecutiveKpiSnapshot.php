<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutiveKpiSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'revenue_ksh',
        'revenue_usd',
        'accounts_receivable_ksh',
        'accounts_receivable_usd',
        'overdue_ksh',
        'overdue_usd',
        'quotation_pipeline_ksh',
        'quotation_pipeline_usd',
        'active_leases',
        'active_contracts',
        'expiring_contracts_30_days',
        'expiring_contracts_60_days',
        'expiring_contracts_90_days',
        'total_fibre_km',
        'leased_fibre_km',
        'available_fibre_km',
        'total_cores',
        'used_cores',
        'available_cores',
        'core_utilization_percent',
        'sla_compliance_percent',
        'network_availability_percent',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];
}
