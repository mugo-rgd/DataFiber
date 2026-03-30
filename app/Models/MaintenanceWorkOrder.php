<?php
// app/Models/MaintenanceWorkOrder.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceWorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_number',
        'maintenance_request_id',
        'assigned_technician',
        'survey_route_id',
        'route_segment_id',
        'title',
        'work_description',
        'status',
        'work_type',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'estimated_duration_minutes',
        'actual_duration_minutes',
        'work_performed',
        'materials_used',
        'labor_cost',
        'material_cost',
        'technician_notes',
        'customer_notes'
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'labor_cost' => 'decimal:2',
        'material_cost' => 'decimal:2',
    ];

    // Relationships
    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_technician');
    }

    public function surveyRoute()
    {
        return $this->belongsTo(SurveyRoute::class);
    }

    public function routeSegment()
    {
        return $this->belongsTo(RouteSegment::class);
    }

    public function equipmentUsed()
    {
        return $this->belongsToMany(MaintenanceEquipment::class, 'work_order_equipment')
                    ->withPivot('hours_used', 'condition')
                    ->withTimestamps();
    }

    // Auto-generate work order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->work_order_number)) {
                $model->work_order_number = 'WO-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
        });
    }

    // Calculate total cost
    public function getTotalCostAttribute()
    {
        return ($this->labor_cost ?? 0) + ($this->material_cost ?? 0);
    }

    // Calculate actual duration
    public function calculateActualDuration()
    {
        if ($this->actual_start && $this->actual_end) {
            $this->actual_duration_minutes = $this->actual_start->diffInMinutes($this->actual_end);
        }
    }
}
