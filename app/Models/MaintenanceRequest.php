<?php
// app/Models/MaintenanceRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'design_request_id',
        'reported_by',
        'title',
        'description',
        'priority',
        'status',
        'issue_type',
        'location',
        'latitude',
        'longitude',
        'reported_at',
        'resolved_at',
        'resolution_notes',
        'downtime_minutes',
        'repair_cost'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'repair_cost' => 'decimal:2',
    ];

    // Relationships
    public function designRequest()
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function workOrders()
    {
        return $this->hasMany(MaintenanceWorkOrder::class);
    }

    // Auto-generate request number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = 'MR-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
        });
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'assigned', 'in_progress']);
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
