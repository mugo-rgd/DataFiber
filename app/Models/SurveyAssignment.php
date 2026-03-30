<?php
// app/Models/SurveyAssignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_request_id',
        'surveyor_id',
        'assigned_by_id',
        'scheduled_at',
        'estimated_hours',
        'priority',
        'requirements',
        'admin_notes',
        'deadline',
        'status',
        'started_at',
        'completed_at',
        'actual_hours',
        'completion_notes',
        'reassignment_reason',
        'reassigned_at',
        'cancelled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'reassigned_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'deadline' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    /**
     * Get the design request that owns the survey assignment.
     */
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }

    /**
     * Get the surveyor assigned to this assignment.
     */
    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(Surveyor::class);
    }

    /**
     * Get the admin who assigned this survey.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    /**
     * Scope a query to only include active assignments.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'in_progress']);
    }

    /**
     * Scope a query to only include completed assignments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include assignments by priority.
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Check if assignment is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->deadline && $this->status !== 'completed') {
            return now()->greaterThan($this->deadline);
        }
        return false;
    }

    /**
     * Get the days remaining until deadline.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->deadline && $this->status !== 'completed') {
            return now()->diffInDays($this->deadline, false);
        }
        return null;
    }

    /**
     * Mark assignment as started.
     */
    public function markAsStarted(): bool
    {
        return $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark assignment as completed.
     */
    public function markAsCompleted($actualHours, $notes = null): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_hours' => $actualHours,
            'completion_notes' => $notes,
        ]);
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'assigned' => 'bg-primary',
            'in_progress' => 'bg-info',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the priority badge class.
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'normal' => 'bg-secondary',
            'high' => 'bg-warning',
            'urgent' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
