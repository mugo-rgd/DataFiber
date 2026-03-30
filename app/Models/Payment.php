<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'lease_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'transaction_id',
        'payment_date',
        'due_date'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'due_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lease that owns the payment.
     */
    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function leaseBilling()
    {
        return $this->belongsTo(LeaseBilling::class);
    }
}
