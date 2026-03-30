<?php
// app/Models/MaintenanceEquipment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceEquipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_code',
        'name',
        'model',
        'serial_number',
        'type',
        'status',
        'purchase_date',
        'last_calibration',
        'next_calibration',
        'specifications',
        'notes',
        'description',
        'location',

    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_calibration' => 'date',
        'next_calibration' => 'date',
    ];

    // Auto-generate equipment code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->equipment_code)) {
                $prefix = match($model->type) {
                    'splicing_machine' => 'SM',
                    'otdr' => 'OTDR',
                    'power_meter' => 'PM',
                    'light_source' => 'LS',
                    'fibre_identifier' => 'FI',
                    default => 'EQ'
                };
                $model->equipment_code = $prefix . '-' . date('Ym') . '-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function workOrders()
    {
        return $this->belongsToMany(MaintenanceWorkOrder::class, 'work_order_equipment')
                    ->withPivot('hours_used', 'condition')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeNeedsCalibration($query)
    {
        return $query->where('next_calibration', '<=', now()->addDays(30))
                    ->where('status', '!=', 'retired');
    }
}
