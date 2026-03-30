<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'customer_id',
        'assigned_to',
        'status',
        'priority',
        'created_by'
    ];

    protected $casts = [
        'created_by' => 'datetime',
    ];

    // Relationship with customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship with assigned user
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relationship with creator (if you have a created_by field)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'primary',
            'pending' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary'
        };
    }
}
