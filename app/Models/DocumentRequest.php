<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequest extends Model
{
    protected $table = 'document_requests';

    protected $fillable = [
        'user_id',
        'lease_id',
        'document_types',
        'additional_notes',
        'status',
        'requested_at',
        'processed_at',
        'processed_by'
    ];

    protected $casts = [
        'document_types' => 'array',  // This automatically casts JSON to array
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer who made the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the lease associated with the request
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'lease_id');
    }

    /**
     * Get the admin who processed the request
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get document types as array (accessor)
     */
    public function getDocumentTypesListAttribute()
    {
        $types = $this->document_types;
        if (is_string($types)) {
            $types = json_decode($types, true);
        }
        return is_array($types) ? $types : [];
    }

    /**
     * Get document types as formatted string
     */
    public function getDocumentTypesStringAttribute()
    {
        $types = $this->getDocumentTypesListAttribute();
        return !empty($types) ? implode(', ', $types) : 'No documents specified';
    }
}
