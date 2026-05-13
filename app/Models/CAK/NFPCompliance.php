<?php

namespace App\Models\CAK;

use Illuminate\Database\Eloquent\Model;

class NFPCompliance extends Model
{
    protected $table = 'nfp_compliances';

    protected $fillable = [
        'licensee_name',
        'license_no',
        'other_licenses',
        'financial_year',
        'quarter',
        'form_data',
        'attachments',
        'status',
         'latitude',      // Add this
    'longitude',     // Add this
    'fibre_km',      // Add this
    'tower_count',   // Add this
        'pdf_path',
        'submitted_at',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'attachments' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];
}
