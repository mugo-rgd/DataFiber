<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lease_id',
        'ticket_number',
        'subject',
        'category',
        'description',
        'attachment_path',
        'status',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the support ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lease that owns the support ticket.
     */
    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    // app/Models/Ticket.php

public function getPriorityBadgeClass()
{
    return match($this->priority) {
        'low' => 'badge-priority-low',
        'medium' => 'badge-priority-medium',
        'high' => 'badge-priority-high',
        'urgent' => 'badge-priority-urgent',
        default => 'badge-secondary',
    };
}

public function getStatusBadgeClass()
{
    return match($this->status) {
        'open' => 'badge-status-open',
        'in_progress' => 'badge-status-in_progress',
        'resolved' => 'badge-status-resolved',
        'closed' => 'badge-status-closed',
        default => 'badge-secondary',
    };
}

public function isOverdue()
{
    return $this->due_date && $this->due_date->isPast() && $this->status !== 'closed' && $this->status !== 'resolved';
}
}
