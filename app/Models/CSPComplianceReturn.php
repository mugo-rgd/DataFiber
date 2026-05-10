<?php
// app/Models/CSPComplianceReturn.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CSPComplianceReturn extends Model
{
    protected $table = 'csp_compliance_returns';

    protected $fillable = [
        'licensee_name', 'license_no', 'other_licenses', 'financial_year', 'quarter',
        'physical_address', 'postal_address', 'contacts', 'address_changed',
        'services', 'money_transfer', 'numbering_resources', 'complaints',
        'fy_start', 'fy_end', 'pwd_aware', 'pwd_complied', 'pwd_actions',
        'pwd_challenges', 'pwd_future_plans', 'ewaste_initiatives',
        'carbon_initiatives', 'emca_status', 'comments', 'submitter_name',
        'submitter_title', 'submitter_date', 'company_stamp_path', 'documents',
        'official_checked_by', 'official_checked_title', 'official_checked_signature',
        'official_checked_date', 'official_verified_by', 'official_verified_title',
        'official_verified_signature', 'official_verified_date', 'official_approved_by',
        'official_approved_title', 'official_approved_signature', 'official_approved_date',
        'official_decision', 'official_remarks', 'official_stamp', 'compliance_id',
        'tracking_code', 'certificate_number', 'certificate_valid_until',
        'status', 'submitted_by', 'approved_by', 'submitted_at', 'approved_at'
    ];

    protected $casts = [
        'physical_address' => 'array',
        'postal_address' => 'array',
        'contacts' => 'array',
        'services' => 'array',
        'money_transfer' => 'array',
        'numbering_resources' => 'array',
        'complaints' => 'array',
        'documents' => 'array',
        'address_changed' => 'boolean',
        'pwd_aware' => 'boolean',
        'pwd_complied' => 'boolean',
        'fy_start' => 'date',
        'fy_end' => 'date',
        'submitter_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'certificate_valid_until' => 'date',
    ];

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
