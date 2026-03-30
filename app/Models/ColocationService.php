<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ColocationService extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'colocation_services';

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_CANCELLED = 'cancelled';

    // Service type constants
    const SERVICE_TYPE_SHELTER_SPACE = 'shelter_space';
    const SERVICE_TYPE_RACK = 'rack';
    const SERVICE_TYPE_CAGE = 'cage';
    const SERVICE_TYPE_SUITE = 'suite';
    const SERVICE_TYPE_POWER_ONLY = 'power_only';

    // Power type constants
    const POWER_TYPE_SINGLE_PHASE = 'single_phase';
    const POWER_TYPE_THREE_PHASE = 'three_phase';
    const POWER_TYPE_DC = 'dc_power';

    // Billing cycle constants
    const BILLING_CYCLE_MONTHLY = 'monthly';
    const BILLING_CYCLE_QUARTERLY = 'quarterly';
    const BILLING_CYCLE_ANNUALLY = 'annually';

    // Port speed constants
    const PORT_SPEED_1G = '1G';
    const PORT_SPEED_10G = '10G';
    const PORT_SPEED_25G = '25G';
    const PORT_SPEED_40G = '40G';
    const PORT_SPEED_100G = '100G';

    protected $fillable = [
        'service_number',
        'user_id',
        'design_request_id',
        'service_type',
        'rack_units',
        'service_area',
        'cabinet_size',
        'location_reference',
        'power_amps',
        'power_type',
        'power_circuits',
        'network_ports',
        'port_speed',
        'monthly_price',
        'setup_fee',
        'billing_cycle',
        'start_date',
        'end_date',
        'contract_months',
        'status',
        'notes',
    ];

    protected $casts = [
        'rack_units' => 'integer',
        'service_area' => 'decimal:2',
        'power_amps' => 'decimal:2',
        'power_circuits' => 'integer',
        'network_ports' => 'integer',
        'monthly_price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'contract_months' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }

 public function quotations()
{
    return $this->belongsToMany(Quotation::class, 'quotation_colocation_service')
                ->withPivot(['quantity', 'unit_price', 'total_price', 'duration_months'])
                ->withTimestamps();
}

    // Accessors & Mutators
    public function getFormattedMonthlyPriceAttribute(): string
    {
        // return '$' . number_format($this->monthly_price, 2);
        return '$' . number_format($this->monthly_price ?? 0, 2);
    }

    public function getFormattedSetupFeeAttribute(): string
    {
        // return '$' . number_format($this->setup_fee, 2);
        return '$' . number_format($this->setup_fee ?? 0, 2);
    }

    public function getTotalContractValueAttribute(): float
    {
        return ($this->monthly_price * $this->contract_months) + $this->setup_fee;
    }

    public function getFormattedTotalContractValueAttribute(): string
    {
        return '$' . number_format($this->total_contract_value, 2);
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return match($this->service_type) {
            self::SERVICE_TYPE_SHELTER_SPACE => 'Shelter Space',
            self::SERVICE_TYPE_RACK => 'Rack',
            self::SERVICE_TYPE_CAGE => 'Cage',
            self::SERVICE_TYPE_SUITE => 'Suite',
            self::SERVICE_TYPE_POWER_ONLY => 'Power Only',
            default => ucfirst(str_replace('_', ' ', $this->service_type))
        };
    }

    public function getPowerTypeLabelAttribute(): string
    {
        return match($this->power_type) {
            self::POWER_TYPE_SINGLE_PHASE => 'Single Phase',
            self::POWER_TYPE_THREE_PHASE => 'Three Phase',
            self::POWER_TYPE_DC => 'DC Power',
            default => ucfirst(str_replace('_', ' ', $this->power_type))
        };
    }

    public function getBillingCycleLabelAttribute(): string
    {
        return match($this->billing_cycle) {
            self::BILLING_CYCLE_MONTHLY => 'Monthly',
            self::BILLING_CYCLE_QUARTERLY => 'Quarterly',
            self::BILLING_CYCLE_ANNUALLY => 'Annually',
            default => ucfirst($this->billing_cycle)
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->service_number} - {$this->service_type_label}";
    }

    // Business Logic Methods
    public function calculateTotalCost(?int $durationMonths = null): float
    {
        $durationMonths = $durationMonths ?? $this->contract_months;
        return ($this->monthly_price * $durationMonths) + $this->setup_fee;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this->end_date && Carbon::parse($this->end_date)->isPast();
    }

    public function getRemainingContractMonths(): int
    {
        if (!$this->end_date) {
            return 0;
        }

        return max(0, now()->diffInMonths($this->end_date));
    }

    public function getRemainingContractValue(): float
    {
        $remainingMonths = $this->getRemainingContractMonths();
        return $this->monthly_price * $remainingMonths;
    }

    public function getPowerConsumptionKVA(): float
    {
        // Convert amps to kVA (assuming 120V for single phase, 208V for three phase)
        $voltage = match($this->power_type) {
            self::POWER_TYPE_SINGLE_PHASE => 120,
            self::POWER_TYPE_THREE_PHASE => 208,
            self::POWER_TYPE_DC => 48,
            default => 120
        };

        return ($this->power_amps * $voltage) / 1000;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>', now());
    }

    public function scopeByServiceType($query, string $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithHighPower($query, float $minAmps = 20)
    {
        return $query->where('power_amps', '>=', $minAmps);
    }

    public function scopeWithMultipleCircuits($query, int $minCircuits = 2)
    {
        return $query->where('power_circuits', '>=', $minCircuits);
    }

    // Static Methods
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function getServiceTypeOptions(): array
    {
        return [
            self::SERVICE_TYPE_SHELTER_SPACE => 'Shelter Space',
            self::SERVICE_TYPE_RACK => 'Rack',
            self::SERVICE_TYPE_CAGE => 'Cage',
            self::SERVICE_TYPE_SUITE => 'Suite',
            self::SERVICE_TYPE_POWER_ONLY => 'Power Only',
        ];
    }

    public static function getPowerTypeOptions(): array
    {
        return [
            self::POWER_TYPE_SINGLE_PHASE => 'Single Phase',
            self::POWER_TYPE_THREE_PHASE => 'Three Phase',
            self::POWER_TYPE_DC => 'DC Power',
        ];
    }

    public static function getBillingCycleOptions(): array
    {
        return [
            self::BILLING_CYCLE_MONTHLY => 'Monthly',
            self::BILLING_CYCLE_QUARTERLY => 'Quarterly',
            self::BILLING_CYCLE_ANNUALLY => 'Annually',
        ];
    }

    public static function getPortSpeedOptions(): array
    {
        return [
            self::PORT_SPEED_1G => '1 Gigabit',
            self::PORT_SPEED_10G => '10 Gigabit',
            self::PORT_SPEED_25G => '25 Gigabit',
            self::PORT_SPEED_40G => '40 Gigabit',
            self::PORT_SPEED_100G => '100 Gigabit',
        ];
    }

    // Model Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($colocationService) {
            if (empty($colocationService->service_number)) {
                $colocationService->service_number = 'COL-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
            }
        });
    }
}
