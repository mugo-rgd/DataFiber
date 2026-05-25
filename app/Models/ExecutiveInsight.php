<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutiveInsight extends Model
{
    protected $fillable = [
        'snapshot_date',
        'category',
        'severity',
        'title',
        'message',
        'value',
        'currency',
        'metadata',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'metadata' => 'array',
    ];
}
