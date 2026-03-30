<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConditionalCertificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'ref_number',
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
        'inspection_report_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'calibration_date' => 'date',
        'certificate_date' => 'date',
        'lessee_date' => 'date',
        'certificate_issue_date' => 'datetime',
        'commissioning_end_date' => 'datetime',
        'total_length' => 'decimal:3',
        'average_loss' => 'decimal:2',
        'splice_joints' => 'integer',
        'ior' => 'decimal:4',
    ];

    /**
     * Get the design request that this certificate belongs to.
     */
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class, 'request_id');
    }

    /**
     * Get the client through the design request.
     */
    public function client()
    {
        return $this->designRequest->client;
    }

    /**
     * Get the full path for engineer signature.
     *
     * @return string|null
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
     *
     * @return string|null
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
     *
     * @return string|null
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
     *
     * @return string
     */
    public function getFormattedTotalLengthAttribute(): string
    {
        return number_format((float)$this->total_length, 3) . ' Km';
    }

    /**
     * Format the average loss with unit.
     *
     * @return string
     */
    public function getFormattedAverageLossAttribute(): string
    {
        // return number_format($this->average_loss, 2) . ' dB';
        return number_format((float)$this->average_loss, 2) . ' dB';
    }

    /**
     * Get the commissioning period in days.
     *
     * @return int
     */
    public function getCommissioningPeriodAttribute(): int
    {
        return $this->commissioning_end_date->diffInDays($this->certificate_issue_date);
    }

    /**
     * Check if the commissioning period has ended.
     *
     * @return bool
     */
    public function getIsCommissioningPeriodEndedAttribute(): bool
    {
        return now()->greaterThan($this->commissioning_end_date);
    }

    /**
     * Get the remaining days of commissioning period.
     *
     * @return int|null
     */
    public function getRemainingCommissioningDaysAttribute(): ?int
    {
        if ($this->is_commissioning_period_ended) {
            return 0;
        }

        return now()->diffInDays($this->commissioning_end_date, false);
    }

    /**
     * Scope a query to only include certificates for a specific request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $requestId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRequest($query, $requestId)
    {
        return $query->where('request_id', $requestId);
    }

    /**
     * Scope a query to only include certificates for a specific year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $year
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForYear($query, $year)
    {
        return $query->whereYear('created_at', $year);
    }

    /**
     * Scope a query to only include active commissioning certificates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveCommissioning($query)
    {
        return $query->where('commissioning_end_date', '>', now());
    }

    /**
     * Scope a query to only include completed commissioning certificates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompletedCommissioning($query)
    {
        return $query->where('commissioning_end_date', '<=', now());
    }

    /**
     * Get the next certificate reference for a given year.
     *
     * @param  int  $year
     * @return string
     */
    public static function generateNextRefNumber($year = null)
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
     * Check if a certificate exists for a specific request.
     *
     * @param  int  $requestId
     * @return bool
     */
    public static function existsForRequest($requestId)
    {
        return self::where('request_id', $requestId)->exists();
    }

    /**
     * Get the certificate for a specific request.
     *
     * @param  int  $requestId
     * @return ConditionalCertificate|null
     */
    public static function getForRequest($requestId)
    {
        return self::where('request_id', $requestId)->first();
    }

       public function request(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class, 'request_id');
    }

    /**
     * Get the lease associated with this certificate
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'lease_id');
    }
}
