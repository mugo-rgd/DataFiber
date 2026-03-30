<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'account_manager_id',
        'title',
        'description',
        'priority',
        'status',
        'type',
        'due_date'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeForAccountManager($query, $accountManagerId)
    {
        return $query->where('account_manager_id', $accountManagerId);
    }

    // Helper Methods
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->lt(now()) &&
               in_array($this->status, ['open', 'in_progress']);
    }

    public function getPriorityBadgeClass()
    {
        return [
            'low' => 'badge-secondary',
            'medium' => 'badge-warning',
            'high' => 'badge-danger',
            'urgent' => 'badge-dark',
        ][$this->priority] ?? 'badge-secondary';
    }

    public function getStatusBadgeClass()
    {
        return [
            'open' => 'badge-success',
            'in_progress' => 'badge-primary',
            'resolved' => 'badge-info',
            'closed' => 'badge-secondary',
        ][$this->status] ?? 'badge-secondary';
    }
}
