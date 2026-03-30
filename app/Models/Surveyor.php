<?php
// app/Models/Surveyor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Surveyor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'specialization',
        'certifications',
        'vehicle_type',
        'survey_equipment',
        'is_active',
    ];

    protected $casts = [
        'certifications' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user associated with the surveyor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the design requests assigned to this surveyor.
     */
    public function designRequests(): HasMany
    {
        return $this->hasMany(DesignRequest::class);
    }

    /**
     * Get the active design requests assigned to this surveyor.
     */
    public function activeDesignRequests(): HasMany
    {
        return $this->designRequests()->whereIn('survey_status', ['assigned', 'in_progress']);
    }

    /**
     * Scope a query to only include active surveyors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the number of active assignments.
     */
    public function getActiveAssignmentsCountAttribute(): int
    {
        return $this->activeDesignRequests()->count();
    }

    /**
     * Get the workload percentage.
     */
    public function getWorkloadPercentageAttribute(): int
    {
        $maxAssignments = 3;
        $activeCount = $this->active_assignments_count;
        return min(100, ($activeCount / $maxAssignments) * 100);
    }
}
