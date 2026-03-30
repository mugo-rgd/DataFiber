<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'account_manager_id',
        'amount',
        'due_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'reminded_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
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
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['pending', 'reminded']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForAccountManager($query, $accountManagerId)
    {
        return $query->where('account_manager_id', $accountManagerId);
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)])
                    ->whereIn('status', ['pending', 'reminded']);
    }

    // Helper Methods
    public function isOverdue()
    {
        return $this->due_date->lt(now()) &&
               in_array($this->status, ['pending', 'reminded']);
    }

    public function isDueSoon()
    {
        return $this->due_date->between(now(), now()->addDays(3)) &&
               in_array($this->status, ['pending', 'reminded']);
    }

    public function getStatusBadgeClass()
    {
        return [
            'pending' => 'badge-warning',
            'reminded' => 'badge-info',
            'paid' => 'badge-success',
            'overdue' => 'badge-danger',
        ][$this->status] ?? 'badge-secondary';
    }
}
