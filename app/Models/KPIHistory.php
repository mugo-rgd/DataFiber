<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KPIHistory extends Model
{
    protected $table = 'kpi_history';

    protected $fillable = [
        'account_manager_id', 'snapshot_date',
        'total_mrr_usd', 'total_tcv_usd', 'arpc_usd',
        'total_mrr_ksh', 'total_tcv_ksh', 'arpc_ksh',
        'total_mrr_combined', 'total_tcv_combined',
        'total_customers', 'total_leases', 'active_leases', 'terminated_leases',
        'total_distance_km', 'total_cores_leased',
        'avg_contract_term_years', 'short_term_contracts', 'mid_term_contracts', 'long_term_contracts',
        'upcoming_renewals_90days', 'renewal_revenue_at_risk',
        'churn_rate',
        'performance_score', 'performance_rating',
        'technology_mix', 'customer_breakdown', 'snapshot_data'
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'technology_mix' => 'array',
        'customer_breakdown' => 'array',
        'snapshot_data' => 'array',
        'total_mrr_usd' => 'decimal:2',
        'total_tcv_usd' => 'decimal:2',
        'arpc_usd' => 'decimal:2',
        'total_mrr_ksh' => 'decimal:2',
        'total_tcv_ksh' => 'decimal:2',
        'arpc_ksh' => 'decimal:2',
        'total_mrr_combined' => 'decimal:2',
        'total_tcv_combined' => 'decimal:2',
    ];

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }
}
