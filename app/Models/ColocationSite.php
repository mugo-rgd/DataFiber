<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ColocationSite extends Model
{
    use HasFactory;

    protected $table = 'colocation_sites';

    // Service type constants matching your ENUM values
    const SERVICE_TYPE_SHELTER_SPACE = 'shelter_space';
    const SERVICE_TYPE_RACK = 'rack';
    const SERVICE_TYPE_CAGE = 'cage';
    const SERVICE_TYPE_SUITES = 'suites';

    protected $fillable = [
        'design_request_id',
        'site_name',
        'service_type',
    ];

       protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    // Relationships
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function colocationServices(): HasMany
    {
        return $this->hasMany(ColocationService::class, 'colocation_site_id');
    }

    // Accessors & Mutators
    public function getServiceTypeLabelAttribute(): string
    {
        return match($this->service_type) {
            self::SERVICE_TYPE_SHELTER_SPACE => 'Shelter Space',
            self::SERVICE_TYPE_RACK => 'Rack',
            self::SERVICE_TYPE_CAGE => 'Cage',
            self::SERVICE_TYPE_SUITES => 'Suites',
            default => ucfirst(str_replace('_', ' ', $this->service_type))
        };
    }

    // Scopes
    public function scopeByServiceType($query, string $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeShelterSpaces($query)
    {
        return $query->where('service_type', self::SERVICE_TYPE_SHELTER_SPACE);
    }

    public function scopeRacks($query)
    {
        return $query->where('service_type', self::SERVICE_TYPE_RACK);
    }

    public function scopeCages($query)
    {
        return $query->where('service_type', self::SERVICE_TYPE_CAGE);
    }

    public function scopeSuites($query)
    {
        return $query->where('service_type', self::SERVICE_TYPE_SUITES);
    }

    // Static Methods
    public static function getServiceTypeOptions(): array
    {
        return [
            self::SERVICE_TYPE_SHELTER_SPACE => 'Shelter Space',
            self::SERVICE_TYPE_RACK => 'Rack',
            self::SERVICE_TYPE_CAGE => 'Cage',
            self::SERVICE_TYPE_SUITES => 'Suites',
        ];
    }

    public static function getServiceTypeEnumValues(): array
    {
        return [
            self::SERVICE_TYPE_SHELTER_SPACE,
            self::SERVICE_TYPE_RACK,
            self::SERVICE_TYPE_CAGE,
            self::SERVICE_TYPE_SUITES,
        ];
    }

    // Helper Methods
    public function isShelterSpace(): bool
    {
        return $this->service_type === self::SERVICE_TYPE_SHELTER_SPACE;
    }

    public function isRack(): bool
    {
        return $this->service_type === self::SERVICE_TYPE_RACK;
    }

    public function isCage(): bool
    {
        return $this->service_type === self::SERVICE_TYPE_CAGE;
    }

    public function isSuite(): bool
    {
        return $this->service_type === self::SERVICE_TYPE_SUITES;
    }

    /**
     * Get display name for the site
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->site_name} ({$this->service_type_label})";
    }

    /**
     * Get related design request number
     */
    public function getDesignRequestNumberAttribute(): ?string
    {
        return $this->designRequest->request_number ?? null;
    }
}
