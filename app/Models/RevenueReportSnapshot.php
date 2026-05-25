<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueReportSnapshot extends Model
{
    protected $fillable = [
        'period_start',
        'period_end',
        'customer_id',
        'lease_id',
        'billing_id',
        'currency',
        'billed_amount',
        'paid_amount',
        'outstanding_amount',
        'service_type',
        'region',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function billing()
    {
        return $this->belongsTo(ConsolidatedBilling::class, 'billing_id');
    }
}
