<?php

namespace App\Models\CAK;

use Illuminate\Database\Eloquent\Model;

class ComplianceCertificate extends Model
{
    protected $fillable = [
        'form_type',
        'form_id',
        'certificate_no',
        'licensee_name',
        'license_no',
        'financial_year',
        'quarter',
        'issue_date',
        'expiry_date',
        'certificate_path',
        'status',
        'issued_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
}
