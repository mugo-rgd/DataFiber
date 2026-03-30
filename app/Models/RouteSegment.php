<?php
// app/Models/RouteSegment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_route_id',
        'segment_number',
        'segment_name',
        'installation_type',
        'distance_km',
        'terrain_type',
        'complexity',
        'pole_count',
        'manhole_count',
        'splice_count',
        'obstacles',
        'challenges',
        'cost_multiplier',
        'start_lat',
        'start_lng',
        'end_lat',
        'end_lng'
    ];

    protected $casts = [
        'distance_km' => 'decimal:3',
        'cost_multiplier' => 'decimal:2',
        'obstacles' => 'array',
        'start_lat' => 'decimal:10,6',
        'start_lng' => 'decimal:10,6',
        'end_lat' => 'decimal:10,6',
        'end_lng' => 'decimal:10,6',
    ];

    // Relationship with SurveyRoute
    public function surveyRoute()
    {
        return $this->belongsTo(SurveyRoute::class);
    }
}
