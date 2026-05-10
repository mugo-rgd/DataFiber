<?php
// app/Models/NFPComplianceReturn.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFPComplianceReturn extends Model
{
    protected $table = 'nfp_compliance_returns';

    protected $fillable = [
        'licensee_name', 'license_no', 'other_licenses', 'financial_year', 'quarter',
        'physical_address', 'postal_address', 'contacts', 'address_changed',
        'infrastructure', 'primary_numbers', 'secondary_numbers', 'bulk_sms',
        'broadband_infrastructure', 'staff_data', 'pwd_aware', 'pwd_complied',
        'pwd_actions', 'pwd_challenges', 'pwd_future_plans', 'ewaste_initiatives',
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
        'infrastructure' => 'array',
        'primary_numbers' => 'array',
        'secondary_numbers' => 'array',
        'bulk_sms' => 'array',
        'broadband_infrastructure' => 'array',
        'staff_data' => 'array',
        'documents' => 'array',
        'address_changed' => 'boolean',
        'pwd_aware' => 'boolean',
        'pwd_complied' => 'boolean',
        'submitter_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
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
