<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class FiberNode extends Model
{
    use HasSpatial;

    protected $table = 'fiber_nodes';

    protected $fillable = [
        'node_id',
        'node_name',
        'node_type',
        'latitude',
        'longitude',
        'region',
        'address',
        'description',
        'location'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'location' => Point::class,
    ];

    protected static function booted()
    {
        static::creating(function ($node) {
            $node->location = new Point($node->latitude, $node->longitude);
        });

        static::updating(function ($node) {
            if ($node->isDirty(['latitude', 'longitude'])) {
                $node->location = new Point($node->latitude, $node->longitude);
            }
        });
    }

    public function networks()
    {
        return $this->belongsToMany(FiberNetwork::class, 'fiber_network_nodes', 'node_id', 'network_id');
    }
}
