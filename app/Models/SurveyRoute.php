<?php
// app/Models/SurveyRoute.php

namespace App\Models;

use App\Http\Controllers\SurveyRouteController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyRoute extends Model
{
    // protected $fillable = [
    //     // 'route_code',
    //     'route_name',
    //     'survey_result_id',
    //     'design_request_id',
    //     'surveyor_id',
    //     'route_type',
    //     'complexity',
    //     'terrain_type',
    //     'total_distance_km',
    //     'aerial_distance_km',
    //     'underground_distance_km',
    //     'conduit_distance_km',
    //     'pole_count',
    //     'manhole_count',
    //     'splice_point_count',
    //     'chamber_count',
    //     'start_location',
    //     'end_location',
    //     'gps_waypoints',
    //     'major_landmarks',
    //     'right_of_way_notes',
    //     'environmental_constraints',
    //     'permitting_requirements',
    //     'estimated_construction_days',
    //     'difficulty_factor',
    //     'cost_multipliers',
    //     'is_approved',
    //     'is_active',
    //     'is_reusable',
    //     'rejection_reason',
    //     'route_description',
    //     'total_distance',
    //     'estimated_cost',
    //     'status'
    // ];

    // protected $casts = [
    //     'gps_waypoints' => 'array',
    //     'major_landmarks' => 'array',
    //     'cost_multipliers' => 'array',
    //     'total_distance_km' => 'decimal:3',
    //     'aerial_distance_km' => 'decimal:3',
    //     'underground_distance_km' => 'decimal:3',
    //     'conduit_distance_km' => 'decimal:3',
    //     'estimated_construction_days' => 'decimal:1',
    //     'difficulty_factor' => 'decimal:2',
    //     'is_approved' => 'boolean',
    //     'is_active' => 'boolean',
    //     'is_reusable' => 'boolean',
    //     'total_distance' => 'decimal:3',
    //     'estimated_cost' => 'decimal:2',
    // ];

    /**
     * Relationships
     */
    public function surveyResult(): BelongsTo
    {
        return $this->belongsTo(SurveyResult::class);
    }

    // public function designRequest(): BelongsTo
    // {
    //     return $this->belongsTo(DesignRequest::class);
    // }

    // public function surveyor(): BelongsTo
    // {
    //     return $this->belongsTo(SurveyRouteController::class);
    // }

    public function segments(): HasMany
    {
        return $this->hasMany(RouteSegment::class);
    }

    public function designItems(): HasMany
    {
        return $this->hasMany(DesignItem::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeReusable($query)
    {
        return $query->where('is_reusable', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('route_type', $type);
    }

    public function scopeByComplexity($query, $complexity)
    {
        return $query->where('complexity', $complexity);
    }

    /**
     * Generate route code
     */
      /**
     * Calculate base cost for this route
     */
    public function calculateBaseCost($costPerKm): float
    {
        $baseCost = $this->total_distance_km * $costPerKm;
        return $baseCost * $this->difficulty_factor;
    }

    /**
     * Get route summary for selection
     */
    public function getRouteSummaryAttribute(): string
    {
        return "{$this->route_name} | {$this->total_distance_km}km | " .
               ucfirst($this->route_type) . " | " .
               ucfirst($this->complexity) . " complexity";
    }

     protected $fillable = [
        'route_code',
        'design_request_id',
        'surveyor_id',
        'route_name',
        'route_description',
        'total_distance',
        'estimated_cost',
        'status'
    ];

    protected $casts = [
        'total_distance' => 'decimal:3',
        'estimated_cost' => 'decimal:2',
    ];

    // Relationship with DesignRequest
    public function designRequest()
    {
        return $this->belongsTo(DesignRequest::class);
    }

    // Relationship with User (Surveyor)
    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    // Fix: Add relationship with RouteSegments
    public function routeSegments()
    {
        return $this->hasMany(RouteSegment::class, 'survey_route_id');
    }

    // Generate route code automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->route_code)) {
                $model->route_code = 'RT-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
        });
    }

        public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'survey_route_id');
    }

}



