<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversation extends Model
{
    use SoftDeletes;
     use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'last_message_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

  /**
 * Get the other participant in a direct conversation
 */
public function getOtherParticipant($userId)
{
    return $this->participants()
        ->where('user_id', '!=', $userId)
        ->first();
}

/**
 * Get unread message count for a user
 */
public function unreadCount($userId = null)
{
    $userId = $userId ?? auth()->id();

    return $this->messages()
        ->where('user_id', '!=', $userId)
        ->whereNull('read_at')
        ->count();
}

/**
 * Scope for direct conversations between two users
 */
public function scopeDirectConversations($query, $userId1, $userId2)
{
    return $query->where('type', 'direct')
        ->whereHas('participants', function ($q) use ($userId1) {
            $q->where('user_id', $userId1);
        })
        ->whereHas('participants', function ($q) use ($userId2) {
            $q->where('user_id', $userId2);
        });
}

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // public function scopeDirectConversations($query, $userId1, $userId2)
    // {
    //     return $query->where('type', 'direct')
    //         ->whereHas('participants', function ($q) use ($userId1) {
    //             $q->where('user_id', $userId1);
    //         })
    //         ->whereHas('participants', function ($q) use ($userId2) {
    //             $q->where('user_id', $userId2);
    //         });
    // }

    // Helper methods
    // public function getOtherParticipant($userId)
    // {
    //     return $this->users()->where('users.id', '!=', $userId)->first();
    // }

    public function markAsReadForUser($userId)
    {
        $this->participants()
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }

    // public function unreadCountForUser($userId)
    // {
    //     $lastRead = $this->participants()
    //         ->where('user_id', $userId)
    //         ->value('last_read_at');

    //     if (!$lastRead) {
    //         return $this->messages()->count();
    //     }

    //     return $this->messages()
    //         ->where('created_at', '>', $lastRead)
    //         ->where('user_id', '!=', $userId)
    //         ->count();
    // }

    // In app/Models/Conversation.php

public function getOtherUserAttribute()
{
    return $this->users
        ->where('id', '!=', auth()->id())
        ->first();
}


//////////////


    /**
     * Get the users in this conversation
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    /**
     * Get the messages in this conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the last message in this conversation
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Count unread messages for a user
     */
    public function unreadCountForUser($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
