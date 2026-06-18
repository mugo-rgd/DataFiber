<?php
// app/Models/MaintenanceRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $table = 'maintenance_requests';

    protected $fillable = [
        'commercial_route_id',
        'customer_id',
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
        'repair_cost',
        'lease_id',  // Add this
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'repair_cost' => 'decimal:2',
        'downtime_minutes' => 'integer',
    ];

    protected $dates = [
        'reported_at',
        'resolved_at',
        'created_at',
        'updated_at',
    ];

    // ==================== RELATIONSHIPS ====================
public function commercialRoute()
{
    return $this->belongsTo(CommercialRoute::class, 'commercial_route_id');
}
    public function designRequest()
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function workOrders()
    {
        return $this->hasMany(MaintenanceWorkOrder::class);
    }

    public function surveyRoute()
    {
        return $this->belongsTo(SurveyRoute::class);
    }

    // ==================== AUTO-GENERATE REQUEST NUMBER ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = $model->generateRequestNumber();
            }
        });
    }

    /**
     * Generate a unique request number
     */
    public function generateRequestNumber()
    {
        $prefix = 'MR';
        $year = date('Y');
        $month = date('m');

        $lastRequest = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRequest) {
            $lastNumber = intval(substr($lastRequest->request_number, -4));
            $sequence = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$sequence}";
    }

    // ==================== SCOPES ====================

    /**
     * Scope for open requests (not resolved or closed)
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'assigned', 'in_progress']);
    }

    /**
     * Scope for closed/resolved requests
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for critical priority requests
     */
    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    /**
     * Scope for high priority requests
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    /**
     * Scope for recent requests
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope by priority
     */
    public function scopeOfPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope by issue type
     */
    public function scopeOfIssueType($query, $issueType)
    {
        return $query->where('issue_type', $issueType);
    }

    /**
     * Scope by date range
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('reported_at', [$startDate, $endDate]);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'light'
        };
    }

    /**
     * Get priority icon
     */
    public function getPriorityIconAttribute()
    {
        return match($this->priority) {
            'critical' => 'exclamation-triangle',
            'high' => 'arrow-up',
            'medium' => 'equals',
            'low' => 'arrow-down',
            default => 'circle'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'open' => 'danger',
            'assigned' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'light'
        };
    }

    /**
     * Get issue type icon
     */
    public function getIssueTypeIconAttribute()
    {
        return match($this->issue_type) {
            'fibre_cut' => 'cut',
            'equipment_failure' => 'microchip',
            'signal_degradation' => 'signal',
            'power_issue' => 'bolt',
            'environmental' => 'tree',
            'preventive_maintenance' => 'tools',
            'other' => 'question-circle',
            default => 'wrench'
        };
    }

    /**
     * Get formatted request number with prefix
     */
    public function getFormattedRequestNumberAttribute()
    {
        return $this->request_number;
    }

    /**
     * Get age of request in days
     */
    public function getAgeInDaysAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Check if request is overdue
     */
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'resolved' || $this->status === 'closed') {
            return false;
        }

        // Critical: 24 hours, High: 48 hours, Medium: 72 hours, Low: 5 days
        $hoursLimit = match($this->priority) {
            'critical' => 24,
            'high' => 48,
            'medium' => 72,
            'low' => 120,
            default => 72
        };

        return $this->created_at->diffInHours(now()) > $hoursLimit;
    }

    /**
     * Get resolution time in hours
     */
    public function getResolutionTimeHoursAttribute()
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->reported_at->diffInHours($this->resolved_at);
    }

    /**
     * Get formatted resolution time
     */
    public function getFormattedResolutionTimeAttribute()
    {
        $hours = $this->resolution_time_hours;

        if ($hours === null) {
            return 'Not resolved';
        }

        if ($hours < 24) {
            return "{$hours} hours";
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        if ($remainingHours > 0) {
            return "{$days} days, {$remainingHours} hours";
        }

        return "{$days} days";
    }

    // ==================== HELPER METHODS ====================

    /**
     * Mark request as resolved
     */
    public function markAsResolved($resolutionNotes = null, $repairCost = null, $downtimeMinutes = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $resolutionNotes ?? $this->resolution_notes,
            'repair_cost' => $repairCost ?? $this->repair_cost,
            'downtime_minutes' => $downtimeMinutes ?? $this->downtime_minutes,
        ]);
    }

    /**
     * Mark request as closed
     */
    public function markAsClosed()
    {
        $this->update(['status' => 'closed']);
    }

    /**
     * Assign request to technician
     */
    public function assignToTechnician($technicianId)
    {
        $this->update(['status' => 'assigned']);

        // Create work order
        return MaintenanceWorkOrder::create([
            'maintenance_request_id' => $this->id,
            'assigned_technician' => $technicianId,
            'assigned_by' => auth()->id(),
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);
    }

    /**
     * Check if request can be edited
     */
    public function getCanBeEditedAttribute()
    {
        return in_array($this->status, ['open', 'assigned']);
    }

    public function lease()
{
    return $this->belongsTo(Lease::class);
}
    /**
     * Check if request can be deleted
     */
    public function getCanBeDeletedAttribute()
    {
        return in_array($this->status, ['open', 'assigned']);
    }
}
