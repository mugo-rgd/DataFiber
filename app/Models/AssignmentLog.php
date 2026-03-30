<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_request_id',
        'assigned_by_id',
        'assigned_to_id',
        'assignment_type',
        'notes',
        'priority'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the design request that this log belongs to.
     */
    public function designRequest()
    {
        return $this->belongsTo(DesignRequest::class);
    }

    /**
     * Get the user who made the assignment.
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    /**
     * Get the user who was assigned.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    /**
     * Scope a query to filter by assignment type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('assignment_type', $type);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
