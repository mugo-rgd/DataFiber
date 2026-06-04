<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConditionalCertificate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conditional_certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ref_number',
    'request_id',
    'county_id',
    'lessor',
    'lessee',
    'link_name',
    'otdr_serial',
    'calibration_date',
    'engineer_name',
    'certificate_date',
    'site_a',
    'site_b',
    'fibre_technology',
    'odf_connector_type',
    'total_length',
    'average_loss',
    'splice_joints',
    'test_wavelength',
    'ior',
    'lessee_contact_name',
    'lessee_date',
    'lessee_designation',
    'certificate_issue_date',
    'commissioning_end_date',
    'engineer_signature_path',
    'certificate_status',
    'inspection_report_path',
    'otdr_trace_path',
    'lease_id',
    'ict_engineer_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'calibration_date' => 'date',
        'certificate_date' => 'date',
        'certificate_issue_date' => 'date',
        'commissioning_end_date' => 'date',
        'lessee_date' => 'date',
        'email_sent_at' => 'datetime',
        'designer_acknowledged_at' => 'datetime',
        'total_length' => 'decimal:3',
        'average_loss' => 'decimal:2',
        'splice_joints' => 'integer',
        'ior' => 'decimal:4',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // Add any sensitive fields if needed
    ];

    /**
     * Get the design request that this certificate belongs to.
     */
   public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class, 'request_id');
    }

    /**
     * Alias for designRequest() for backward compatibility.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class, 'design_request_id');
    }

    /**
     * Get the ICT engineer who created this certificate.
     */
    public function ictEngineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ict_engineer_id');
    }

    /**
     * Get the lease associated with this certificate.
     */
   public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'lease_id');
    }

    /**
     * Get the client through the design request.
     */
    public function getClientAttribute()
    {
        return $this->designRequest?->customer;
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the full path for engineer signature.
     */
    public function getEngineerSignatureUrlAttribute(): ?string
    {
        if (!$this->engineer_signature_path) {
            return null;
        }

        return asset('storage/' . $this->engineer_signature_path);
    }

    /**
     * Get the full path for inspection report.
     */
    public function getInspectionReportUrlAttribute(): ?string
    {
        if (!$this->inspection_report_path) {
            return null;
        }

        return asset('storage/' . $this->inspection_report_path);
    }

    /**
     * Get the file name of the inspection report.
     */
    public function getInspectionReportFileNameAttribute(): ?string
    {
        if (!$this->inspection_report_path) {
            return null;
        }

        return basename($this->inspection_report_path);
    }

    /**
     * Format the total length with unit.
     */
    public function getFormattedTotalLengthAttribute(): string
    {
        return number_format((float)$this->total_length, 3) . ' km';
    }

    /**
     * Format the average loss with unit.
     */
    public function getFormattedAverageLossAttribute(): string
    {
        return number_format((float)$this->average_loss, 3) . ' dB';
    }

    /**
     * Get the commissioning period in days.
     */
    public function getCommissioningPeriodAttribute(): int
    {
        if (!$this->commissioning_end_date || !$this->certificate_issue_date) {
            return 0;
        }
        return $this->commissioning_end_date->diffInDays($this->certificate_issue_date);
    }

    /**
     * Check if the commissioning period has ended.
     */
    public function getIsCommissioningPeriodEndedAttribute(): bool
    {
        if (!$this->commissioning_end_date) {
            return false;
        }
        return now()->greaterThan($this->commissioning_end_date);
    }

    /**
     * Get the remaining days of commissioning period.
     */
    public function getRemainingCommissioningDaysAttribute(): ?int
    {
        if (!$this->commissioning_end_date) {
            return null;
        }

        if ($this->is_commissioning_period_ended) {
            return 0;
        }

        return now()->diffInDays($this->commissioning_end_date, false);
    }

    /**
     * Get formatted certificate date.
     */
    public function getFormattedCertificateDateAttribute(): string
    {
        return $this->certificate_date?->format('d F Y') ?? 'N/A';
    }

    /**
     * Get formatted commissioning end date.
     */
    public function getFormattedCommissioningEndDateAttribute(): string
    {
        return $this->commissioning_end_date?->format('d F Y') ?? 'N/A';
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        $colors = [
            'draft' => 'secondary',
            'pending_designer' => 'warning',
            'sent_to_designer' => 'info',
            'acknowledged' => 'primary',
            'completed' => 'success',
            'rejected' => 'danger',
        ];

        return $colors[$this->certificate_status ?? 'draft'] ?? 'secondary';
    }

    /**
     * Get status badge text.
     */
    public function getStatusBadgeTextAttribute(): string
    {
        $texts = [
            'draft' => 'Draft',
            'pending_designer' => 'Pending Designer',
            'sent_to_designer' => 'Sent to Designer',
            'acknowledged' => 'Acknowledged',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
        ];

        return $texts[$this->certificate_status ?? 'draft'] ?? ucfirst($this->certificate_status ?? 'Unknown');
    }

    // ==================== SCOPES ====================

    /**
     * Scope a query to only include certificates for a specific request.
     */
    public function scopeForRequest($query, $requestId)
    {
        return $query->where('design_request_id', $requestId)
            ->orWhere('request_id', $requestId);
    }

    /**
     * Scope a query to only include certificates for a specific year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->whereYear('created_at', $year);
    }

    /**
     * Scope a query to only include active commissioning certificates.
     */
    public function scopeActiveCommissioning($query)
    {
        return $query->where('commissioning_end_date', '>', now());
    }

    /**
     * Scope a query to only include completed commissioning certificates.
     */
    public function scopeCompletedCommissioning($query)
    {
        return $query->where('commissioning_end_date', '<=', now());
    }

    /**
     * Scope a query to only include certificates with specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('certificate_status', $status);
    }

    /**
     * Scope a query to only include certificates by ICT engineer.
     */
    public function scopeByEngineer($query, $engineerId)
    {
        return $query->where('ict_engineer_id', $engineerId);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Generate the next certificate reference for a given year.
     */
    public static function generateNextRefNumber($year = null): string
    {
        $year = $year ?? date('Y');

        $lastCertificate = self::where('ref_number', 'like', 'KPLC/CC/' . $year . '/%')
            ->orderBy('ref_number', 'desc')
            ->first();

        if ($lastCertificate) {
            $parts = explode('/', $lastCertificate->ref_number);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'KPLC/CC/' . $year . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate the next certificate number.
     */
    public static function generateNextCertificateNumber(): string
    {
        $year = date('Y');
        $lastCertificate = self::where('certificate_number', 'like', 'COND-' . $year . '-%')
            ->orderBy('certificate_number', 'desc')
            ->first();

        if ($lastCertificate) {
            $parts = explode('-', $lastCertificate->certificate_number);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'COND-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if a certificate exists for a specific request.
     */
    public static function existsForRequest($requestId): bool
    {
        return self::where('design_request_id', $requestId)
            ->orWhere('request_id', $requestId)
            ->exists();
    }

    /**
     * Get the certificate for a specific request.
     */
    public static function getForRequest($requestId): ?self
    {
        return self::where('design_request_id', $requestId)
            ->orWhere('request_id', $requestId)
            ->first();
    }

    /**
     * Mark certificate as sent to designer.
     */
    public function markAsSent(): bool
    {
        return $this->update(['certificate_status' => 'sent_to_designer']);
    }

    /**
     * Mark certificate as acknowledged.
     */
    public function markAsAcknowledged(): bool
    {
        return $this->update(['certificate_status' => 'acknowledged']);
    }

    /**
     * Mark certificate as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['certificate_status' => 'completed']);
    }

    /**
     * Mark certificate as rejected.
     */
    public function markAsRejected($reason = null): bool
    {
        return $this->update([
            'certificate_status' => 'rejected',
            'remarks' => $reason ?? $this->remarks,
        ]);
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'draft' => 'Draft',
            'pending_designer' => 'Pending Designer Review',
            'sent_to_designer' => 'Sent to Designer',
            'acknowledged' => 'Acknowledged',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
        ];

        return $labels[$this->certificate_status ?? 'draft'] ?? 'Unknown';
    }
}
