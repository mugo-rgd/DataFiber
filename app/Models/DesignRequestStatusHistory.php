<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignRequestStatusHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'design_request_status_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'design_request_id',
        'status',
        'changed_by',
        'notes',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the design request that owns the status history.
     */
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }

    /**
     * Get the user who changed the status.
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scope a query to filter by design request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $designRequestId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDesignRequest($query, $designRequestId)
    {
        return $query->where('design_request_id', $designRequestId);
    }

    /**
     * Scope a query to filter by status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get the latest status change for a design request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $designRequestId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestForDesignRequest($query, $designRequestId)
    {
        return $query->forDesignRequest($designRequestId)
                     ->latest('created_at')
                     ->limit(1);
    }
}
