<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'county';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'region',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active counties.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by region.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $region
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope a query to search counties by name or code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%")
              ->orWhere('region', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get the users assigned to this county.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'county_id');
    }

    /**
     * Get the design requests for this county.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function designRequests()
    {
        return $this->hasMany(DesignRequest::class, 'county_id');
    }

    /**
     * Get the ICT engineers for this county.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ictEngineers()
    {
        return $this->hasMany(User::class, 'county_id')
                    ->where('role', 'ict_engineer')
                    ->where('is_active', true);
    }

    /**
     * Get all regional engineers for this county.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regionalEngineers()
    {
        return $this->hasMany(User::class, 'county_id')
                    ->whereIn('role', ['engineer', 'regional_engineer', 'ict_engineer'])
                    ->where('is_active', true);
    }

    /**
     * Check if county is associated with a specific region.
     *
     * @param  string  $region
     * @return bool
     */
    public function isInRegion($region)
    {
        return $this->region === $region;
    }

    /**
     * Get all counties grouped by region.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByRegions()
    {
        return self::active()
            ->orderBy('region')
            ->orderBy('name')
            ->get()
            ->groupBy('region');
    }

    /**
     * Get counties for a specific region.
     *
     * @param  string  $region
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByRegion($region)
    {
        return self::active()
            ->where('region', $region)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get county by code.
     *
     * @param  string  $code
     * @return \App\Models\County|null
     */
    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }

    /**
     * Get all counties as options for select dropdown.
     *
     * @return array
     */
    public static function getOptions()
    {
        return self::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Get counties with their request counts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function withRequestCounts()
    {
        return self::active()
            ->withCount(['designRequests' => function($query) {
                $query->whereIn('status', ['pending', 'assigned']);
            }])
            ->orderBy('name')
            ->get();
    }
}
