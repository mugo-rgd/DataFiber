<?php
// app/Models/DocumentType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'document_type',
        'name',
        'description',
        'is_required',
        'is_active',
        'max_file_size',
        'allowed_extensions',
        'sort_order'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'allowed_extensions' => 'array',

    ];

    // Scope for active document types
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for required document types
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    // Scope ordered by sort order
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get allowed extensions as array, handling string conversion
     */
    public function getAllowedExtensionsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
