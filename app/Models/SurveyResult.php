<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyResult extends Model
{
   // Add to SurveyResult model
public function surveyRoutes(): HasMany
{
    return $this->hasMany(SurveyRoute::class);
}

// Method to create routes from survey
public function createSurveyRoutes(): void
{
    // Create main route
    $mainRoute = SurveyRoute::create([
        'route_name' => $this->designRequest->title . ' - Main Route',
        'survey_result_id' => $this->id,
        'design_request_id' => $this->design_request_id,
        'surveyor_id' => $this->surveyor_id,
        'route_type' => $this->aerial_distance_km > $this->underground_distance_km ? 'aerial' : 'underground',
        'total_distance_km' => $this->total_distance_km,
        'aerial_distance_km' => $this->aerial_distance_km,
        'underground_distance_km' => $this->underground_distance_km,
        'conduit_distance_km' => $this->conduit_distance_km,
        'pole_count' => $this->pole_count,
        'manhole_count' => $this->manhole_count,
        'splice_point_count' => $this->splice_point_count,
        // ... other fields
    ]);

    // You can create alternative routes here if needed
}
}
