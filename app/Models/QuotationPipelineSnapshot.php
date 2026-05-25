<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationPipelineSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'currency',
        'stage',
        'status',
        'quotation_count',
        'pipeline_value',
        'won_value',
        'lost_value',
        'conversion_rate_percent',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];
}
