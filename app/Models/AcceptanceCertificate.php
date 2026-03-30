<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcceptanceCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'certificate_ref',
        'to_company',
        'route_name',
        'link_name',
        'cable_type',
        'distance',
        'cores_count',
        'effective_date',
        'lessor',
        'lessee',
        'lessee_address',
        'lessee_contact',

        'witness1_name',
        'witness1_date',
        'witness1_signature_path',
        'witness1_stamp_path',

        'witness2_name',
        'witness2_date',
        'witness2_signature_path',
        'witness2_stamp_path',

        'witness3_name',
        'witness3_date',
        'witness3_signature_path',
        'witness3_stamp_path',

        'lessee1_name',
        'lessee1_date',
        'lessee1_signature_path',
        'lessee1_stamp_path',

        'lessee2_name',
        'lessee2_date',
        'lessee2_signature_path',
        'lessee2_stamp_path',

        'test_report_path',
        'additional_documents_path',
        'status',
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
    ];

    /**
     * Get the design request associated with the certificate.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class, 'request_id');
    }

    /**
     * Get the client through the design request.
     */
    public function client()
    {
        return $this->request->client();
    }

    /**
     * Get all signatories as an array.
     */
    public function getSignatoriesAttribute(): array
    {
        return [
            [
                'name' => $this->witness1_name,
                'date' => $this->witness1_date,
                'title' => 'INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)',
                'signature' => $this->witness1_signature_path,
                'stamp' => $this->witness1_stamp_path,
            ],
            [
                'name' => $this->witness2_name,
                'date' => $this->witness2_date,
                'title' => 'TELECOM LEAD ENGINEER, Kenya Power',
                'signature' => $this->witness2_signature_path,
                'stamp' => $this->witness2_stamp_path,
            ],
            [
                'name' => $this->witness3_name,
                'date' => $this->witness3_date,
                'title' => 'TELECOM MANAGER, Kenya Power',
                'signature' => $this->witness3_signature_path,
                'stamp' => $this->witness3_stamp_path,
            ],
            [
                'name' => $this->lessee1_name,
                'date' => $this->lessee1_date,
                'title' => 'LEAD ENGINEER / TECHNICAL REPRESENTATIVE',
                'signature' => $this->lessee1_signature_path,
                'stamp' => $this->lessee1_stamp_path,
            ],
            [
                'name' => $this->lessee2_name,
                'date' => $this->lessee2_date,
                'title' => 'MANAGER',
                'signature' => $this->lessee2_signature_path,
                'stamp' => $this->lessee2_stamp_path,
            ],
        ];
    }

    /**
     * Get formatted effective date.
     */
    public function getFormattedEffectiveDateAttribute(): array
    {
        return [
            'day' => date('jS', strtotime($this->effective_date)),
            'month' => date('F', strtotime($this->effective_date)),
            'year' => date('Y', strtotime($this->effective_date)),
        ];
    }

   // In your AcceptanceCertificate model
public function designRequest()
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
