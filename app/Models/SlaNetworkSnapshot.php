<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaNetworkSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'lease_id',
        'customer_id',
        'total_incidents',
        'open_incidents',
        'resolved_incidents',
        'downtime_minutes',
        'uptime_percent',
        'sla_target_percent',
        'sla_compliance_percent',
        'mttr_minutes',
        'sla_breaches',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }
}
