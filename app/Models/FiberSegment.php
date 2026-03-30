<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use MatanYadaev\EloquentSpatial\Objects\Point;

class FiberSegment extends Model
{
    use HasSpatial;

    protected $table = 'fiber_segments';

    protected $fillable = [
        'segment_id',
        'network_id',
        'segment_order',
        'source_name',
        'source_lat',
        'source_lon',
        'destination_name',
        'dest_lat',
        'dest_lon',
        'cable_type',
        'distance_km',
        'fiber_cores',
        'link_type',
        'cost_per_month',
        'currency',
        'status',
        'geometry'
    ];

    protected $casts = [
        'source_lat' => 'decimal:7',
        'source_lon' => 'decimal:7',
        'dest_lat' => 'decimal:7',
        'dest_lon' => 'decimal:7',
        'distance_km' => 'decimal:2',
        'cost_per_month' => 'decimal:2',
        'geometry' => LineString::class,
    ];

    protected static function booted()
    {
        static::creating(function ($segment) {
            $segment->geometry = new LineString([
                new Point($segment->source_lat, $segment->source_lon),
                new Point($segment->dest_lat, $segment->dest_lon)
            ]);

            // Auto-calculate cost if not set
            if (!$segment->cost_per_month) {
                $segment->cost_per_month = FiberPricing::calculateCost(
                    $segment->distance_km,
                    $segment->link_type,
                    $segment->fiber_cores
                );
            }
        });

        static::created(function ($segment) {
            // Update network total cost
            $network = FiberNetwork::where('network_id', $segment->network_id)->first();
            if ($network) {
                $network->calculateTotalCost();
            }
        });

        static::updated(function ($segment) {
            if ($segment->isDirty(['distance_km', 'link_type', 'fiber_cores', 'cost_per_month'])) {
                $network = FiberNetwork::where('network_id', $segment->network_id)->first();
                if ($network) {
                    $network->calculateTotalCost();
                }
            }
        });
    }

    public function network()
    {
        return $this->belongsTo(FiberNetwork::class, 'network_id', 'network_id');
    }
}
