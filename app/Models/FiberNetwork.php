<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use MatanYadaev\EloquentSpatial\Objects\Point;

class FiberNetwork extends Model
{
    use HasSpatial;

    protected $table = 'fiber_networks';

    protected $fillable = [
        'network_id',
        'network_name',
        'region',
        'total_distance_km',
        'fiber_cores',
        'link_type',
        'cost_per_month',
        'currency',
        'status',
        'waypoints_json',
        'connection_sequence',
        'geometry'
    ];

    protected $casts = [
        'total_distance_km' => 'decimal:2',
        'cost_per_month' => 'decimal:2',
        'waypoints_json' => 'array',
        'geometry' => LineString::class,
    ];

    public function segments()
    {
        return $this->hasMany(FiberSegment::class, 'network_id', 'network_id');
    }

    public function nodes()
    {
        return $this->belongsToMany(FiberNode::class, 'fiber_network_nodes', 'network_id', 'node_id');
    }

    public function calculateTotalCost()
    {
        $totalCost = $this->segments->sum('cost_per_month');
        $this->cost_per_month = $totalCost;
        $this->save();

        return $totalCost;
    }
}
