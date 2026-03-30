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
}
