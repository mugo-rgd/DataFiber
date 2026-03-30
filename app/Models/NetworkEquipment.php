<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkEquipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'model',
        'serial_number',
        'location',
        'status',
        'ip_address',
        'specifications',
        'purchase_date',
        'warranty_expiry',
        'notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'specifications' => 'array'
    ];
}
