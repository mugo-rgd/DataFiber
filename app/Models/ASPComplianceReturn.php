<?php
// app/Models/ASPComplianceReturn.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ASPComplianceReturn extends Model
{
    protected $table = 'asp_compliance_returns';

    protected $fillable = [
        'licensee_name', 'license_no', 'other_licenses', 'financial_year', 'quarter',
        'physical_address', 'postal_address', 'contacts', 'address_changed',
        'm2m_services', 'subscriptions', 'mobile_devices', 'data_subscriptions',
        'broadband_subscriptions', 'fixed_data_speed', 'number_portability',
        'voice_traffic', 'sms_traffic', 'international_traffic',
        'roaming_outbound', 'roaming_inbound', 'quality_of_service',
        'complaints', 'county_subscriptions', 'staff_data',
        'numbering_resources', 'other_numbering', 'cybersecurity',
        'pwd_aware', 'pwd_complied', 'pwd_actions', 'pwd_challenges',
        'pwd_future_plans', 'ewaste_initiatives', 'carbon_initiatives',
        'emca_status', 'comments', 'submitter_name', 'submitter_title',
        'submitter_date', 'company_stamp_path', 'documents',
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
        'm2m_services' => 'array',
        'subscriptions' => 'array',
        'mobile_devices' => 'array',
        'data_subscriptions' => 'array',
        'broadband_subscriptions' => 'array',
        'fixed_data_speed' => 'array',
        'number_portability' => 'array',
        'voice_traffic' => 'array',
        'sms_traffic' => 'array',
        'international_traffic' => 'array',
        'roaming_outbound' => 'array',
        'roaming_inbound' => 'array',
        'quality_of_service' => 'array',
        'complaints' => 'array',
        'county_subscriptions' => 'array',
        'staff_data' => 'array',
        'numbering_resources' => 'array',
        'other_numbering' => 'array',
        'cybersecurity' => 'array',
        'documents' => 'array',
        'address_changed' => 'boolean',
        'pwd_aware' => 'boolean',
        'pwd_complied' => 'boolean',
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
