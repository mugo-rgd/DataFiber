<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'certificate_number',
        'design_request_id',
        'certificate_type',
        'issued_date',
        'status',
        'valid_until',
        'inspector_name',
        'conditions',
        'remarks',
        'acceptance_date',
        'completion_date',
        'accepted_by',
        'accepted_by_position',
        'warranty_period',
        'final_amount',
        'terms_conditions',
        'client_feedback',
        'supporting_document_path',
        'inspection_report_path',
        'acceptance_document_path',
        'completion_report_path',
        'client_signature_path',
        'generated_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'issued_date' => 'datetime',
        'valid_until' => 'datetime',
        'acceptance_date' => 'datetime',
        'completion_date' => 'datetime',
        'final_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the design request that owns the certificate.
     */
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }

    /**
     * Get the user who generated the certificate.
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get the user who approved the certificate.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all files for this certificate.
     */
    public function files(): HasMany
    {
        return $this->hasMany(CertificateFile::class);
    }

    /**
     * Scope a query to only include conditional certificates.
     */
    public function scopeConditional($query)
    {
        return $query->where('certificate_type', 'conditional');
    }

    /**
     * Scope a query to only include acceptance certificates.
     */
    public function scopeAcceptance($query)
    {
        return $query->where('certificate_type', 'acceptance');
    }

    /**
     * Check if certificate is conditional.
     */
    public function isConditional(): bool
    {
        return $this->certificate_type === 'conditional';
    }

    /**
     * Check if certificate is acceptance.
     */
    public function isAcceptance(): bool
    {
        return $this->certificate_type === 'acceptance';
    }

    /**
     * Check if certificate is issued.
     */
    public function isIssued(): bool
    {
        return in_array($this->status, ['issued', 'signed']);
    }

    /**
     * Get the warranty end date.
     */
    public function getWarrantyEndDateAttribute()
    {
        if (!$this->warranty_period || !$this->completion_date) {
            return null;
        }

        return $this->completion_date->addDays($this->warranty_period);
    }

    /**
     * Get formatted certificate number.
     */
    public function getFormattedCertificateNumberAttribute()
    {
        return strtoupper($this->certificate_number);
    }
}
