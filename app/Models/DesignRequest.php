<?php

namespace App\Models;

use App\Events\DesignRequestAssigned;
use App\Events\DesignRequestStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DesignRequest extends Model
{
    use HasFactory;

    // Status Constants - COMPLETE SET
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_DESIGN = 'in_design';
    const STATUS_DESIGNED = 'designed';
    const STATUS_QUOTED = 'quoted';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Survey Status Constants
    const SURVEY_STATUS_NOT_REQUIRED = 'not_required';
    const SURVEY_STATUS_REQUESTED = 'requested';
    const SURVEY_STATUS_ASSIGNED = 'assigned';
    const SURVEY_STATUS_IN_PROGRESS = 'in_progress';
    const SURVEY_STATUS_COMPLETED = 'completed';

    const ROUTE_TYPE_MAP_DEFINED = 'map_defined';
const ROUTE_TYPE_MANUAL_ENTRY = 'manual_entry';
const ROUTE_TYPE_NONE = 'none';

    protected $fillable = [
        'customer_id', 'designer_id', 'request_number', 'title', 'description',
        'status', 'technical_requirements', 'design_specifications', 'design_notes',
        'estimated_cost', 'quoted_amount', 'requested_at', 'assigned_at',
        'design_completed_at', 'quoted_at', 'attachments','cores_required','unit_cost','distance',
        'terms','technology_type','link_class','route_name','tax_rate','surveyor_id',
        'survey_status',
        'survey_requirements',
        'survey_estimated_hours',
        'survey_scheduled_at',
        'survey_completed_at',
        'survey_requested_at', 'route_points', 'total_distance', 'point_count','approved_at',
        'quotation_id', 'rejection_reason', 'rejected_at', 'cancelled_at','ict_engineer_id',
    'assigned_ict_engineer_id',
    'assigned_to_ict_at',
    'inspection_notes',
    'ict_status','technical_status',
        'technical_notes','conditional_certificate_id',
        'technical_reviewed_at','conditional_certificate_issued_at',
         'county_id',
    'certificate_id',
    'acceptance_certificate_id',
    'acceptance_certificate_issued_at',
    'inspection_date',
    'inspection_report_path',
    'survey_actual_hours',
        ];

    /**'route_name'
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'request_number';
    }

    protected $casts = [
        'survey_scheduled_at' => 'datetime',
        'survey_completed_at' => 'datetime',
        'survey_requested_at' => 'datetime',
        'requested_at' => 'datetime',
        'assigned_at' => 'datetime',
        'design_completed_at' => 'datetime',
        'quoted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'route_points' => 'array',
        'total_distance' => 'decimal:2',
        'distance' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'quoted_amount' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'survey_estimated_hours' => 'decimal:2',
        'attachments' => 'array',
        'assigned_to_ict_at' => 'datetime','technical_reviewed_at' => 'datetime','conditional_certificate_issued_at' => 'datetime',
           'acceptance_certificate_issued_at' => 'datetime',
    'inspection_date' => 'date',
    'survey_actual_hours' => 'decimal:2',
    ];

 public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'account_manager','accountmanager_admin']);
    }
    // Add these relationships if needed
public function rejectionHistory()
{
    return $this->hasMany(DesignRequestRejection::class)->latest();
}

public function statusHistory()
{
    return $this->hasMany(DesignRequestStatusHistory::class)->latest();
}

 protected static function booted()
    {
        static::updated(function ($model) {
            if ($model->isDirty('status')) {
                DesignRequestStatusHistory::create([
                    'design_request_id' => $model->id,
                    'status' => $model->status,
                    'changed_by' => auth()->id() ?? 1, // Default to user 1 if no auth
                    'notes' => 'Status changed from ' . $model->getOriginal('status') . ' to ' . $model->status
                ]);
            }
        });
    }
public function canTransitionTo(string $newStatus): bool
{
    $allowedTransitions = [
        self::STATUS_PENDING => [self::STATUS_ASSIGNED, self::STATUS_CANCELLED],
        self::STATUS_ASSIGNED => [self::STATUS_IN_DESIGN, self::STATUS_CANCELLED],
        self::STATUS_IN_DESIGN => [self::STATUS_DESIGNED, self::STATUS_CANCELLED],
        // ... define all allowed transitions
    ];

    return in_array($newStatus, $allowedTransitions[$this->status] ?? []);
}

public function transitionTo(string $newStatus, ?string $reason = null): bool
{
    if (!$this->canTransitionTo($newStatus)) {
        return false;
    }

    return $this->update(['status' => $newStatus]);
}
public function getRouteTypeAttribute(): string
{
    if ($this->hasMapRoute()) {
        return self::ROUTE_TYPE_MAP_DEFINED;
    } elseif ($this->hasManualEntry()) {
        return self::ROUTE_TYPE_MANUAL_ENTRY;
    } else {
        return self::ROUTE_TYPE_NONE;
    }
}
// Add these scopes for better querying
public function scopeNeedsAttention($query)
{
    return $query->whereIn('status', [
        self::STATUS_PENDING,
        self::STATUS_ASSIGNED,
        self::STATUS_IN_DESIGN
    ]);
}

public static function validationRules($id = null): array
{
    return [
        'customer_id' => 'required|exists:users,id',
        'title' => 'required|string|max:255',
        'route_name' => 'required|string|max:255',
        'description' => 'required|string',
        'technical_requirements' => 'nullable|string',
        'estimated_cost' => 'nullable|numeric|min:0',
        // ... add more rules as needed
    ];
}
public function scopeOverdue($query, $days = 7)
{
    return $query->where('created_at', '<', now()->subDays($days))
                ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
}

public function scopeByCustomer($query, $customerId)
{
    return $query->where('customer_id', $customerId);
}

public function notifyStatusChange()
{
    if ($this->isDirty('status')) {
        $oldStatus = $this->getOriginal('status');
        $newStatus = $this->status;

        // Trigger status change notification with all required arguments
        event(new DesignRequestStatusChanged($this, $oldStatus, $newStatus));
    }
}
public function notes()
{
    return $this->morphMany(Note::class, 'noteable');
}
    // Relationships
    public function surveyRoute()
    {
        return $this->hasOne(SurveyRoute::class);
    }

// In DesignRequest model
public function colocationSites()
{
    return $this->hasMany(ColocationSite::class);
}

public function colocationServices()
{
    return $this->hasMany(ColocationService::class);
}


    public function colocationList()
    {
        return $this->belongsToMany(ColocationList::class, 'design_request_colocation_service', 'design_request_id', 'colocation_service_id', 'id', 'service_id')
                    ->withPivot([
                        'rack_units',
                        'power_requirements',
                        'bandwidth_requirements',
                        'ip_address_count',
                        'special_requirements'
                    ])
                    ->withTimestamps();
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

      public function quotations()
    {
        return $this->hasMany(Quotation::class, 'design_request_id');
    }
    public function designItems()
    {
        return $this->hasMany(DesignItem::class, 'request_number', 'request_number');
    }

    public function quotation()
    {
        return $this->hasOne(Quotation::class);
    }

    public function surveyAssignments(): HasMany
    {
        return $this->hasMany(SurveyAssignment::class);
    }

    public function latestSurveyAssignment(): HasOne
    {
        return $this->hasOne(SurveyAssignment::class)->latest();
    }

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    public function assignmentLogs()
    {
        return $this->hasMany(SurveyAssignmentLog::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeInDesign($query)
    {
        return $this->scopeInProgress($query);
    }

    public function scopeDesigned($query)
    {
        return $query->where('status', self::STATUS_DESIGNED);
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', self::STATUS_ASSIGNED);
    }

    public function scopeQuoted($query)
    {
        return $query->where('status', self::STATUS_QUOTED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeNeedsSurvey($query)
    {
        return $query->where(function($q) {
            $q->where('survey_status', self::SURVEY_STATUS_NOT_REQUIRED)
              ->orWhere('survey_status', self::SURVEY_STATUS_REQUESTED)
              ->orWhereNull('surveyor_id');
        });
    }

    public function scopeHasAssignedSurveyor($query)
    {
        return $query->whereNotNull('surveyor_id')->where('survey_status', self::SURVEY_STATUS_ASSIGNED);
    }

    public function scopeWithMapRoute($query)
    {
        return $query->whereNotNull('route_points')
                    ->where('point_count', '>=', 2);
    }

    public function scopeWithManualEntry($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('cores_required')
              ->orWhereNotNull('distance')
              ->orWhereNotNull('terms');
        });
    }

    // Status Check Methods
    public function isAssigned(): bool
    {
        return $this->designer_id !== null;
    }

    public function canBeQuoted(): bool
    {
        return $this->status === self::STATUS_DESIGNED && $this->estimated_cost !== null;
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_QUOTED && $this->quotation;
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, [self::STATUS_QUOTED, self::STATUS_APPROVED, self::STATUS_IN_PROGRESS]);
    }

    public function canBeEdited(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeModified(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeDeleted(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeUnassigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    public function canModifyItems(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeAssigned(): bool
    {
        return $this->status === self::STATUS_PENDING && $this->hasRouteInformation();
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    // Status Action Methods
    public function approveQuote(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now()
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now()
        ]);
    }

    public function rejectWithReason(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'rejected_at' => now()
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now()
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now()
        ]);
    }

    // Route Methods
    public function getStartPointAttribute()
    {
        $points = $this->route_points;
        return $points && count($points) > 0 ? $points[0] : null;
    }

    public function getEndPointAttribute()
    {
        $points = $this->route_points;
        return $points && count($points) > 0 ? end($points) : null;
    }

    public function hasValidRoute()
    {
        return $this->point_count >= 2;
    }

    public function getRouteCoordinatesAttribute()
    {
        if (!$this->route_points) {
            return [];
        }

        return array_map(function($point) {
            return [
                'lat' => floatval($point['lat']),
                'lng' => floatval($point['lng'])
            ];
        }, $this->route_points);
    }

    public function hasMapRoute(): bool
    {
        return !empty($this->route_points) && $this->point_count >= 2;
    }

    public function hasManualEntry(): bool
    {
        return !empty($this->cores_required) || !empty($this->distance) || !empty($this->terms);
    }

    public function getDisplayDistanceAttribute(): ?float
    {
        return $this->total_distance ?? $this->distance;
    }

      public function hasRouteInformation(): bool
    {
        return $this->hasMapRoute() || $this->hasManualEntry();
    }

    public function getRouteSummaryAttribute(): array
    {
        if ($this->hasMapRoute()) {
            return [
                'type' => 'map_defined',
                'points' => $this->point_count,
                'distance' => $this->total_distance,
                'start_point' => $this->start_point,
                'end_point' => $this->end_point,
            ];
        } elseif ($this->hasManualEntry()) {
            return [
                'type' => 'manual_entry',
                'cores_required' => $this->cores_required,
                'distance' => $this->distance,
                'terms' => $this->terms,
                'technology_type' => $this->technology_type,
                'link_class' => $this->link_class,
            ];
        } else {
            return [
                'type' => 'none',
                'message' => 'No route information provided'
            ];
        }
    }

    public function getEnhancedRoutePointsAttribute(): array
    {
        if (!$this->route_points) {
            return [];
        }

        $enhancedPoints = [];
        foreach ($this->route_points as $index => $point) {
            $enhancedPoints[] = [
                'id' => $index + 1,
                'lat' => floatval($point['lat']),
                'lng' => floatval($point['lng']),
                'order' => $point['order'] ?? $index + 1,
                'is_start' => $index === 0,
                'is_end' => $index === count($this->route_points) - 1,
            ];
        }

        return $enhancedPoints;
    }

    public function getRouteBoundsAttribute(): ?array
    {
        if (!$this->route_points || count($this->route_points) < 2) {
            return null;
        }

        $lats = array_column($this->route_points, 'lat');
        $lngs = array_column($this->route_points, 'lng');

        return [
            'north' => max($lats),
            'south' => min($lats),
            'east' => max($lngs),
            'west' => min($lngs),
        ];
    }

    // Cost Calculation Methods
    public function calculateEstimatedCost(): ?float
    {
        if (!$this->unit_cost || !$this->display_distance) {
            return null;
        }

        $baseCost = $this->unit_cost * $this->display_distance;

        if ($this->tax_rate) {
            $baseCost += $baseCost * ($this->tax_rate / 100);
        }

        return round($baseCost, 2);
    }
public function calculateTotalCost(): float
{
    $baseCost = $this->quoted_amount ?? $this->estimated_cost ?? 0;

    // Add colocation costs
    $colocationCost = $this->colocationServices->sum('calculated_cost');

    // Add survey costs if applicable
    $surveyCost = $this->survey_estimated_hours ?
        $this->survey_estimated_hours * config('app.hourly_survey_rate', 100) : 0;

    return $baseCost + $colocationCost + $surveyCost;
}
    public function updateEstimatedCost(): void
    {
        $estimatedCost = $this->calculateEstimatedCost();
        if ($estimatedCost !== null) {
            $this->update(['estimated_cost' => $estimatedCost]);
        }
    }

    // Progress and Display Methods
    public function getProgressPercentageAttribute(): int
    {
        $statusProgress = [
            self::STATUS_DRAFT => 0,
            self::STATUS_PENDING => 10,
            self::STATUS_ASSIGNED => 25,
            self::STATUS_IN_DESIGN => 40,
            self::STATUS_DESIGNED => 60,
            self::STATUS_QUOTED => 75,
            self::STATUS_APPROVED => 85,
            self::STATUS_IN_PROGRESS => 90,
            self::STATUS_COMPLETED => 100,
            self::STATUS_REJECTED => 0,
            self::STATUS_CANCELLED => 0,
        ];

        return $statusProgress[$this->status] ?? 0;
    }

    // Accessor for status badges
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING => 'info',
            self::STATUS_ASSIGNED => 'primary',
            self::STATUS_IN_DESIGN => 'warning',
            self::STATUS_DESIGNED => 'success',
            self::STATUS_QUOTED => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_COMPLETED => 'info',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'dark',
            default => 'secondary'
        };
    }

    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_DESIGN => 'In Design',
            self::STATUS_DESIGNED => 'Designed',
            self::STATUS_QUOTED => 'Quoted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClass()
    {
        return $this->getStatusBadgeAttribute();
    }

    public function getFormattedStatus()
    {
        return $this->getFormattedStatusAttribute();
    }

    // KML Generation
    public function generateKML()
    {
        if (empty($this->route_points)) {
            return null;
        }

        $coordinates = '';
        foreach ($this->route_points as $point) {
            $coordinates .= "{$point['lng']},{$point['lat']},0 ";
        }

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
'<kml xmlns="http://www.opengis.net/kml/2.2">' . "\n" .
'  <Document>' . "\n" .
'    <name>Design Request #' . $this->request_number . '</name>' . "\n" .
'    <Style id="yellowLine">' . "\n" .
'      <LineStyle>' . "\n" .
'        <color>7f00ffff</color>' . "\n" .
'        <width>4</width>' . "\n" .
'      </LineStyle>' . "\n" .
'    </Style>' . "\n" .
'    <Placemark>' . "\n" .
'      <name>Fibre Route</name>' . "\n" .
'      <styleUrl>#yellowLine</styleUrl>' . "\n" .
'      <LineString>' . "\n" .
'        <coordinates>' . $coordinates . '</coordinates>' . "\n" .
'      </LineString>' . "\n" .
'    </Placemark>' . "\n" .
'  </Document>' . "\n" .
'</kml>';
    }

    // Static Methods
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_DESIGN => 'In Design',
            self::STATUS_DESIGNED => 'Designed',
            self::STATUS_QUOTED => 'Quoted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function getSurveyStatusOptions(): array
    {
        return [
            self::SURVEY_STATUS_NOT_REQUIRED => 'Not Required',
            self::SURVEY_STATUS_REQUESTED => 'Requested',
            self::SURVEY_STATUS_ASSIGNED => 'Assigned',
            self::SURVEY_STATUS_IN_PROGRESS => 'In Progress',
            self::SURVEY_STATUS_COMPLETED => 'Completed',
        ];
    }
    // Add these methods to your existing DesignRequest model

/**
 * Get all certificates for the design request.
 */
public function certificates(): HasMany
{
    return $this->hasMany(Certificate::class);
}

/**
 * Get conditional certificates for the design request.
 */
public function conditionalCertificates(): HasMany
{
    return $this->certificates()->where('certificate_type', 'conditional');
}

/**
 * Get acceptance certificates for the design request.
 */
public function acceptanceCertificates(): HasMany
{
    return $this->certificates()->where('certificate_type', 'acceptance');
}

/**
 * Get the latest conditional certificate.
 */
public function latestConditionalCertificate()
{
    return $this->hasOne(Certificate::class)
        ->where('certificate_type', 'conditional')
        ->latestOfMany();
}

/**
 * Get the latest acceptance certificate.
 */
public function latestAcceptanceCertificate()
{
    return $this->hasOne(Certificate::class)
        ->where('certificate_type', 'acceptance')
        ->latestOfMany();
}


/**
 * Get the county for this design request.
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function county()
{
    return $this->belongsTo(County::class, 'county_id');
}

/**
 * Scope a query to filter design requests by county.
 *
 * @param  \Illuminate\Database\Eloquent\Builder  $query
 * @param  int  $countyId
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeByCounty($query, $countyId)
{
    return $query->where('county_id', $countyId);
}

/**
 * Scope a query to filter design requests by region through county.
 *
 * @param  \Illuminate\Database\Eloquent\Builder  $query
 * @param  string  $region
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeByRegion($query, $region)
{
    return $query->whereHas('county', function($q) use ($region) {
        $q->where('region', $region);
    });
}
/**
 * Get the ICT engineer assigned to this request.
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function ictEngineer()
{
    return $this->belongsTo(User::class, 'ict_engineer_id');
}

/**
 * Get the assigned ICT engineer for this request.
 * (Alias for ictEngineer using assigned_ict_engineer_id)
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function assignedICTEngineer()
{
    return $this->belongsTo(User::class, 'assigned_ict_engineer_id');
}
//   public function conditionalCertificate()
//     {
//         return $this->belongsTo(ConditionalCertificate::class, 'conditional_certificate_id');
//     }

//       public function acceptanceCertificate()
//     {
//         return $this->hasOne(AcceptanceCertificate::class, 'request_id');
//     }

public function lease()
{
    return $this->hasOne(Lease::class, 'design_request_id');
}

// In Quotation model
public function contract()
{
    return $this->hasOne(Contract::class, 'quotation_id');
}

////
// Remove the duplicate conditionalCertificate() method that appears later in the file
// Keep this one:
public function conditionalCertificate()
{
    return $this->belongsTo(ConditionalCertificate::class, 'conditional_certificate_id');
}

// Update acceptanceCertificate to use hasOne with correct foreign key
public function acceptanceCertificate()
{
    return $this->hasOne(AcceptanceCertificate::class, 'request_id');
}

// Add this method to get all documents in one array
public function getAllDocumentsAttribute()
{
    return [
        'quotation' => [
            'document' => $this->quotation,
            'type' => 'quotation',
            'reference' => $this->quotation->quotation_number ?? null,
            'status' => $this->quotation->status ?? null,
            'created_at' => $this->quotation->created_at ?? null,
        ],
        'conditional_certificate' => [
            'document' => $this->conditionalCertificate,
            'type' => 'conditional_certificate',
            'reference' => $this->conditionalCertificate->ref_number ?? null,
            'status' => $this->conditionalCertificate->certificate_status ?? null,
            'created_at' => $this->conditionalCertificate->created_at ?? null,
        ],
        'acceptance_certificate' => [
            'document' => $this->acceptanceCertificate,
            'type' => 'acceptance_certificate',
            'reference' => $this->acceptanceCertificate->certificate_ref ?? null,
            'status' => $this->acceptanceCertificate->status ?? null,
            'created_at' => $this->acceptanceCertificate->created_at ?? null,
        ],
        'contract' => [
            'document' => $this->quotation->contract ?? null,
            'type' => 'contract',
            'reference' => $this->quotation->contract->contract_number ?? null,
            'status' => $this->quotation->contract->status ?? null,
            'created_at' => $this->quotation->contract->created_at ?? null,
        ],
        'lease' => [
            'document' => $this->lease,
            'type' => 'lease',
            'reference' => $this->lease->lease_number ?? null,
            'status' => $this->lease->status ?? null,
            'created_at' => $this->lease->created_at ?? null,
        ],
    ];
}

// Add this method to check document availability
public function getDocumentStatusAttribute()
{
    return [
        'quotation' => !empty($this->quotation_id),
        'conditional_certificate' => !empty($this->conditional_certificate_id),
        'acceptance_certificate' => !empty($this->acceptance_certificate_id),
        'contract' => !empty($this->quotation->contract ?? null),
        'lease' => !empty($this->lease),
    ];
}

// Add this method to get document counts
public function getDocumentCountAttribute()
{
    $status = $this->document_status;
    return array_sum(array_values($status));
}

// Add this method to get the next required document based on workflow
public function getNextRequiredDocument()
{
    $documents = $this->getAllDocumentsAttribute();

    foreach ($documents as $type => $doc) {
        if (!$doc['document']) {
            return [
                'type' => $type,
                'name' => ucwords(str_replace('_', ' ', $type)),
                'action' => $this->getDocumentAction($type)
            ];
        }
    }

    return null;
}

private function getDocumentAction($documentType)
{
    $actions = [
        'quotation' => 'Generate Quotation',
        'conditional_certificate' => 'Issue Conditional Certificate',
        'acceptance_certificate' => 'Issue Acceptance Certificate',
        'contract' => 'Create Contract',
        'lease' => 'Create Lease Agreement',
    ];

    return $actions[$documentType] ?? 'Create Document';
}

// Add this scope to eager load all documents
public function scopeWithAllDocuments($query)
{
    return $query->with([
        'quotation',
        'conditionalCertificate',
        'acceptanceCertificate',
        'lease',
        'quotation.contract'
    ]);
}
////
// In DesignRequest model - ensure these relationships exist
// For account managers
public function accountManager()
{
    return $this->hasOneThrough(
        User::class,
        User::class,
        'id', // Foreign key on users table (customer)
        'id', // Foreign key on users table (account manager)
        'customer_id', // Local key on design_requests
        'account_manager_id' // Foreign key on users table for customer's account manager
    )->where('users.role', 'account_manager');
}

}
