<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HasFactory;

class LeaseBilling extends Model
{
    protected $fillable = [
        'billing_number',
        'lease_id',
        'user_id',
        'customer_id',
        'billing_date',
        'due_date',
        'total_amount',
        'amount',
        'currency',
        'billing_cycle',
        'period_start',
        'period_end',
        'description',
        'status',
        'sent_at',
        'paid_at'
    ];

    protected $casts = [
        'billing_date' => 'date',
        'due_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'total_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the lease that owns the billing.
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * Scope for draft billings
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'lease_id');
        // Adjust the foreign key if it's different in your database
    }
    /**
     * Scope for sent billings
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for paid billings
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for overdue billings
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope for old draft billings (for retry)
     */
    public function scopeOldDrafts($query, $hours = 2)
    {
        return $query->draft()->where('created_at', '<=', now()->subHours($hours));
    }

    /**
     * Mark billing as sent
     */
    public function markAsSent(): bool
    {
        return $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark billing as paid
     */
    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark billing as overdue
     */
    public function markAsOverdue(): bool
    {
        return $this->update([
            'status' => 'overdue',
        ]);
    }

    /**
     * Check if billing is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    /**
     * Get overdue days
     */
    public function getOverdueDaysAttribute(): int
    {
        if (!$this->due_date || $this->status === 'paid') {
            return 0;
        }

        return max(0, now()->diffInDays($this->due_date));
    }

      /**
     * Get the customer (this is the actual customer who pays)
     * Since customer_id is in users table, we use belongsTo with User
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

       /**
     * Scope for pending billings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }



}
