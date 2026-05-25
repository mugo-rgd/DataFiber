<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopCustomerSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'customer_id',
        'currency',
        'revenue',
        'outstanding_amount',
        'revenue_contribution_percent',
        'active_leases',
        'active_contracts',
        'leased_km',
        'leased_cores',
        'risk_level',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
