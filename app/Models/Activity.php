<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    protected $fillable = [
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
        'event',
        'batch_uuid',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include activities by a given causer.
     */
    public function scopeCausedBy($query, $causer)
    {
        return $query->where('causer_id', $causer->id)
            ->where('causer_type', get_class($causer));
    }

    /**
     * Scope a query to only include activities for a given subject.
     */
    public function scopeForSubject($query, $subject)
    {
        return $query->where('subject_id', $subject->id)
            ->where('subject_type', get_class($subject));
    }

    /**
     * Scope a query to only include activities of a given event.
     */
    public function scopeForEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope a query to only include activities in a given log.
     */
    public function scopeInLog($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Get the latest activities.
     */
    public function scopeLatest($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}
