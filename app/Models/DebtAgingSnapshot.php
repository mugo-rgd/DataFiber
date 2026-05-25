<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtAgingSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'customer_id',
        'currency',
        'current_amount',
        'days_1_30',
        'days_31_60',
        'days_61_90',
        'days_91_120',
        'days_120_plus',
        'total_outstanding',
        'billing_count',
        'overdue_count',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
