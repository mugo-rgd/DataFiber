<?php
// app/Models/Document.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
    'lease_id',
    'user_id',
    'source',
    'is_manually_uploaded',
    'name',
    'slug',
    'has_expiry',
    'document_type',
    'file_path',
    'disk',
    'file_name',
    'uploaded_by',
    'status',
    'mime_type',
    'file_size',
    'rejection_reason',
    'expiry_date',
    'is_required',
    'description',
    'approved_by',
    'approved_at',
];

    protected $casts = [
        'has_expiry' => 'boolean',
        'is_required' => 'boolean',
        'file_size' => 'integer',
        'expiry_date' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'disk' => 'local'
    ];

    /**
     * Get the user who uploaded the document.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user that owns the document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the lease that owns the document.
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * Get document type name (string, not relationship)
     */
    public function getDocumentTypeNameAttribute()
    {
        // Since document_type is a string, we can use it directly
        // If you want to get the name from DocumentType model, you'd need to query it
        return $this->document_type;
    }

    /**
     * Check if document is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        return $this->has_expiry && $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending', 'pending_review' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'expired' => 'secondary',
            default => 'light'
        };
    }

    // Scopes
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeTemplates($query)
    {
        return $query->whereNull('uploaded_by');
    }

    public function scopeUserUploads($query)
    {
        return $query->whereNotNull('uploaded_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending')->orWhere('status', 'pending_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if this is a template (not an actual uploaded file)
     */
    public function isTemplate(): bool
    {
        return is_null($this->uploaded_by);
    }

    /**
     * Check if document has been uploaded by a user
     */
    public function isUploaded(): bool
    {
        return !is_null($this->uploaded_by);
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute(): string
    {
        if ($this->disk === 'public') {
            return Storage::url($this->file_path);
        }
        return route('documents.download', $this->id);
    }

    /**
     * Check if document is approved (alias for isApproved)
     */
    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if document is pending
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending' || $this->status === 'pending_review';
    }

    /**
     * Check if document is rejected
     */
    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            // Generate slug from name or file_name
            if (empty($document->slug)) {
                $baseName = $document->name ?? $document->file_name ?? 'document';
                $document->slug = Str::slug($baseName) . '-' . uniqid();
            }
        });
    }

    /**
     * Get the uploaded by user
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
