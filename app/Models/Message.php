<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{

    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'type',
        'attachment_path',    // Changed from file_path
        'attachment_name',    // Changed from file_name
        'file_size',
        'mime_type',          // Changed from file_type
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Helper methods
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Get the conversation that owns the message
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that sent the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if message is a file
     */
    public function isFile(): bool
    {
        return $this->type === 'file' || $this->type === 'image';
    }

    /**
     * Check if message is text
     */
    public function isText(): bool
    {
        return $this->type === 'text';
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return 'Unknown size';

        $bytes = (int)$this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file icon based on mime type
     */
    public function getFileIconAttribute(): string
    {
        if (!$this->mime_type) return 'fa-file';

        $type = strtolower($this->mime_type);

        if (strpos($type, 'image') !== false) return 'fa-file-image';
        if (strpos($type, 'pdf') !== false) return 'fa-file-pdf';
        if (strpos($type, 'word') !== false || strpos($type, 'document') !== false) return 'fa-file-word';
        if (strpos($type, 'excel') !== false || strpos($type, 'sheet') !== false) return 'fa-file-excel';
        if (strpos($type, 'zip') !== false || strpos($type, 'rar') !== false) return 'fa-file-archive';

        // Check by extension
        $extension = pathinfo($this->attachment_name ?? '', PATHINFO_EXTENSION);
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webp':
                return 'fa-file-image';
            case 'pdf':
                return 'fa-file-pdf';
            case 'doc':
            case 'docx':
                return 'fa-file-word';
            case 'xls':
            case 'xlsx':
                return 'fa-file-excel';
            case 'zip':
            case 'rar':
            case '7z':
                return 'fa-file-archive';
            default:
                return 'fa-file';
        }
    }

    /**
     * Get file name (alias for attachment_name)
     */
    public function getFileNameAttribute()
    {
        return $this->attachment_name;
    }

    /**
     * Get file path (alias for attachment_path)
     */
    public function getFilePathAttribute()
    {
        return $this->attachment_path;
    }

    /**
     * Get file type (alias for mime_type)
     */
    public function getFileTypeAttribute()
    {
        return $this->mime_type;
    }

}
