<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AcceptanceCertificate extends Model
{
    use HasFactory;

    protected $table = 'acceptance_certificates';

    protected $fillable = [
        'certificate_ref', 'request_id', 'lease_id',
        'to_company', 'route_name', 'link_name', 'cable_type',
        'distance', 'cores_count', 'effective_date',
        'lessor', 'lessee', 'lessee_address', 'lessee_contact',
        'witness1_name', 'witness1_date', 'witness1_signature_path', 'witness1_stamp_path',
        'witness2_name', 'witness2_date', 'witness2_signature_path', 'witness2_stamp_path',
        'witness3_name', 'witness3_date', 'witness3_signature_path', 'witness3_stamp_path',
        'lessee1_name', 'lessee1_date', 'lessee1_signature_path', 'lessee1_stamp_path',
        'lessee2_name', 'lessee2_date', 'lessee2_signature_path', 'lessee2_stamp_path',
        'test_report_path', 'additional_documents_path', 'status'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'witness1_date' => 'date',
        'witness2_date' => 'date',
        'witness3_date' => 'date',
        'lessee1_date' => 'date',
        'lessee2_date' => 'date',
        'distance' => 'decimal:3',
        'additional_documents_path' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the design request associated with the certificate.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class, 'request_id');
    }

    /**
     * Alias for request() for backward compatibility.
     */
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class, 'request_id');
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
        return $this->request?->customer;
    }

    // ==================== ACCESSORS ====================

    /**
     * Get formatted distance with unit.
     */
    public function getFormattedDistanceAttribute(): string
    {
        return number_format($this->distance, 3) . ' km';
    }

    /**
     * Get formatted effective date.
     */
    public function getFormattedEffectiveDateAttribute(): string
    {
        if (!$this->effective_date) {
            return 'N/A';
        }
        return $this->effective_date->format('F d, Y');
    }

    /**
     * Get effective date parts for certificate display.
     */
    public function getEffectiveDatePartsAttribute(): array
    {
        if (!$this->effective_date) {
            return ['day' => '', 'month' => '', 'year' => ''];
        }

        return [
            'day' => $this->effective_date->format('jS'),
            'month' => $this->effective_date->format('F'),
            'year' => $this->effective_date->format('Y'),
        ];
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        $colors = [
            'draft' => 'secondary',
            'issued' => 'success',
            'sent' => 'info',
            'acknowledged' => 'primary',
            'rejected' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'draft' => 'Draft',
            'issued' => 'Issued',
            'sent' => 'Sent to Customer',
            'acknowledged' => 'Acknowledged',
            'rejected' => 'Rejected',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get formatted certificate reference.
     */
    public function getFormattedCertificateRefAttribute(): string
    {
        return $this->certificate_ref;
    }

    /**
     * Get all signatories as an array.
     */
    public function getSignatoriesAttribute(): array
    {
        return [
            [
                'name' => $this->witness1_name,
                'date' => $this->witness1_date?->format('F d, Y'),
                'title' => 'INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)',
                'signature' => $this->witness1_signature_path,
                'stamp' => $this->witness1_stamp_path,
                'signature_url' => $this->getSignatureUrl('witness1'),
                'stamp_url' => $this->getStampUrl('witness1'),
            ],
            [
                'name' => $this->witness2_name,
                'date' => $this->witness2_date?->format('F d, Y'),
                'title' => 'TELECOM LEAD ENGINEER, Kenya Power',
                'signature' => $this->witness2_signature_path,
                'stamp' => $this->witness2_stamp_path,
                'signature_url' => $this->getSignatureUrl('witness2'),
                'stamp_url' => $this->getStampUrl('witness2'),
            ],
            [
                'name' => $this->witness3_name,
                'date' => $this->witness3_date?->format('F d, Y'),
                'title' => 'TELECOM MANAGER, Kenya Power',
                'signature' => $this->witness3_signature_path,
                'stamp' => $this->witness3_stamp_path,
                'signature_url' => $this->getSignatureUrl('witness3'),
                'stamp_url' => $this->getStampUrl('witness3'),
            ],
            [
                'name' => $this->lessee1_name,
                'date' => $this->lessee1_date?->format('F d, Y'),
                'title' => 'LEAD ENGINEER / TECHNICAL REPRESENTATIVE',
                'signature' => $this->lessee1_signature_path,
                'stamp' => $this->lessee1_stamp_path,
                'signature_url' => $this->getSignatureUrl('lessee1'),
                'stamp_url' => $this->getStampUrl('lessee1'),
            ],
            [
                'name' => $this->lessee2_name,
                'date' => $this->lessee2_date?->format('F d, Y'),
                'title' => 'MANAGER',
                'signature' => $this->lessee2_signature_path,
                'stamp' => $this->lessee2_stamp_path,
                'signature_url' => $this->getSignatureUrl('lessee2'),
                'stamp_url' => $this->getStampUrl('lessee2'),
            ],
        ];
    }

    /**
     * Get signature URL helper.
     */
    private function getSignatureUrl($prefix): ?string
    {
        $path = $this->{$prefix . '_signature_path'};
        return $path && Storage::disk('public')->exists($path)
            ? Storage::disk('public')->url($path)
            : null;
    }

    /**
     * Get stamp URL helper.
     */
    private function getStampUrl($prefix): ?string
    {
        $path = $this->{$prefix . '_stamp_path'};
        return $path && Storage::disk('public')->exists($path)
            ? Storage::disk('public')->url($path)
            : null;
    }

    /**
     * Get test report URL.
     */
    public function getTestReportUrlAttribute(): ?string
    {
        return $this->test_report_path && Storage::disk('public')->exists($this->test_report_path)
            ? Storage::disk('public')->url($this->test_report_path)
            : null;
    }

    /**
     * Get additional documents URLs.
     */
    public function getAdditionalDocumentsUrlsAttribute(): array
    {
        if (!$this->additional_documents_path) {
            return [];
        }

        $paths = is_array($this->additional_documents_path)
            ? $this->additional_documents_path
            : json_decode($this->additional_documents_path, true);

        if (!is_array($paths)) {
            return [];
        }

        return collect($paths)->map(function($path) {
            return Storage::disk('public')->exists($path)
                ? Storage::disk('public')->url($path)
                : null;
        })->filter()->toArray();
    }

    /**
     * Get download URL for the certificate.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('designer.certificates.acceptance.download', $this);
    }

    /**
     * Get view URL for the certificate.
     */
    public function getViewUrlAttribute(): string
    {
        return route('designer.certificates.acceptance.show', $this);
    }

    // ==================== SCOPES ====================

    /**
     * Scope a query to only include issued certificates.
     */
    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    /**
     * Scope a query to only include certificates for a specific designer.
     */
    public function scopeForDesigner($query, $designerId)
    {
        return $query->whereHas('designRequest', function($q) use ($designerId) {
            $q->where('designer_id', $designerId);
        });
    }

    /**
     * Scope a query to only include certificates for a specific request.
     */
    public function scopeForRequest($query, $requestId)
    {
        return $query->where('request_id', $requestId);
    }

    /**
     * Scope a query to only include certificates for a specific year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->whereYear('created_at', $year);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if certificate has test report.
     */
    public function hasTestReport(): bool
    {
        return !empty($this->test_report_path) && Storage::disk('public')->exists($this->test_report_path);
    }

    /**
     * Check if certificate has additional documents.
     */
    public function hasAdditionalDocuments(): bool
    {
        $docs = $this->additional_documents_path;
        if (!$docs) return false;

        $paths = is_array($docs) ? $docs : json_decode($docs, true);
        return !empty($paths);
    }

    /**
     * Get additional documents count.
     */
    public function getAdditionalDocumentsCountAttribute(): int
    {
        if (!$this->additional_documents_path) return 0;

        $paths = is_array($this->additional_documents_path)
            ? $this->additional_documents_path
            : json_decode($this->additional_documents_path, true);

        return is_array($paths) ? count($paths) : 0;
    }

    /**
     * Mark certificate as sent.
     */
    public function markAsSent(): bool
    {
        return $this->update(['status' => 'sent']);
    }

    /**
     * Mark certificate as acknowledged.
     */
    public function markAsAcknowledged(): bool
    {
        return $this->update(['status' => 'acknowledged']);
    }

    /**
     * Check if certificate is issued.
     */
    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    /**
     * Generate next certificate reference.
     */
    public static function generateNextReference($requestId = null): string
    {
        $year = date('Y');
        $prefix = "KPLC/AC/{$year}/";

        $lastCert = self::where('certificate_ref', 'like', $prefix . '%')
            ->orderBy('certificate_ref', 'desc')
            ->first();

        if ($lastCert) {
            $parts = explode('/', $lastCert->certificate_ref);
            $lastNumber = (int) end($parts);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
