<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FibreStation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fibre_stations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lat',
        'lng',
        'name',
        'capacity',
        'fibreStatus',
        'darkFibreCores',
        'connectionType',
        'owner',
        'area',
        'location',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'darkFibreCores' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     public function getHasFibreAttribute()
    {
        return $this->fibre_status === 'Available' ||
               $this->fibreStatus === 'Available';
    }

    /**
     * Get the current status
     */
    public function getCurrentStatusAttribute()
    {
        return $this->fibre_status ?? $this->fibreStatus ?? 'Unknown';
    }

    /**
     * Scope for available fibre stations
     */
    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->where('fibre_status', 'Available')
              ->orWhere('fibreStatus', 'Available');
        });
    }

    /**
     * Scope for stations with dark fibre
     */
    public function scopeHasDarkFibre($query)
    {
        return $query->where('darkFibreCores', '>', 0);
    }

    /**
     * Scope by owner
     */
    public function scopeByOwner($query, $owner)
    {
        return $query->where('owner', $owner);
    }

    /**
     * Search by name, area, or location
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('area', 'LIKE', "%{$searchTerm}%")
              ->orWhere('location', 'LIKE', "%{$searchTerm}%");
        });
    }
    /**
     * Scope a query to filter by area.
     */
    public function scopeByArea($query, $area)
    {
        return $query->where('area', $area);
    }

    /**
     * Get the formatted coordinates.
     */
    public function getFormattedCoordinatesAttribute()
    {
        return "{$this->lat}, {$this->lng}";
    }

    /**
     * Check if the station has available fibre cores.
     */
    public function hasAvailableCores()
    {
        return $this->darkFibreCores > 0;
    }

    /**
     * Get stations within a certain radius (in kilometers).
     */
    public function scopeWithinRadius($query, $lat, $lng, $radius)
    {
        // Haversine formula for distance calculation
        $haversine = "(6371 * acos(cos(radians($lat)) * cos(radians(lat)) * cos(radians(lng) - radians($lng)) + sin(radians($lat)) * sin(radians(lat))))";

        return $query
            ->select('*')
            ->selectRaw("{$haversine} AS distance")
            ->whereRaw("{$haversine} < ?", [$radius])
            ->orderBy('distance');
    }
}
