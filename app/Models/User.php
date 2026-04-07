<?php

namespace App\Models;

use App\Http\Controllers\CustomerProfileController;
use App\Notifications\CustomResetPassword;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPassword as ResetPasswordNotification;


/**
 * @property-read \App\Models\CompanyProfile|null $companyProfile
 */
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string $account_manager_id
 * @property Carbon $assigned_at
 * @property string $assignment_notes
 * @property string $company_name
 * @property string $phone
 * @property string $account_status
 * @property Carbon $profile_completed_at
 * @property Carbon $lease_start_date
 * @property string $billing_frequency
 * @property float $monthly_rate
 * @property Carbon $next_billing_date
 * @property bool $auto_billing_enabled
 * @property string $specialization
 * @property Carbon $last_login_at
 * @property array $preferences
 * @property string $timezone
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User accountManagers()
 * @method static \Illuminate\Database\Eloquent\Builder|User customers()
 * @method static \Illuminate\Database\Eloquent\Builder|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder|User designers()
 * @method static \Illuminate\Database\Eloquent\Builder|User technicians()
 * @method static \Illuminate\Database\Eloquent\Builder|User surveyors()
 * @method static \Illuminate\Database\Eloquent\Builder|User finance()
 * @method static \Illuminate\Database\Eloquent\Builder|User withAccountManager()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutAccountManager()
 * @method static \Illuminate\Database\Eloquent\Builder|User active()
 * @method static \Illuminate\Database\Eloquent\Builder|User autoBillingEnabled()
 * @method static \Illuminate\Database\Eloquent\Builder|User dueForBilling()
 * @method static \Illuminate\Database\Eloquent\Builder|User bySpecialization(string $specialization)
 * @method static \Illuminate\Database\Eloquent\Builder|User byRole(array|string $roles)
 * @method static \Illuminate\Database\Eloquent\Builder|User withPendingBillings()
 * @method static \Illuminate\Database\Eloquent\Builder|User withOverdueBillings()
 * @method static \Illuminate\Database\Eloquent\Builder|User withCompleteProfile()
 * @method static \Illuminate\Database\Eloquent\Builder|User withIncompleteProfile()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable,HasRoles;

    // Role constants
    const ROLE_SYSTEM_ADMIN = 'system_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MARKETING_ADMIN = 'accountmanager_admin';
    const ROLE_TECHNICAL_ADMIN = 'technical_admin';
    const ROLE_FINANCE = 'finance';
    const ROLE_DESIGNER = 'designer';
      const ROLE_ICT = 'ict_engineer';
    const ROLE_SURVEYOR = 'surveyor';
    const ROLE_TECHNICIAN = 'technician';
    const ROLE_ACCOUNT_MANAGER = 'account_manager';
    const ROLE_DEBT_MANAGER = 'debt_manager';
    const ROLE_CUSTOMER = 'customer';

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    // Billing frequency constants
    const BILLING_MONTHLY = 'monthly';
    const BILLING_QUARTERLY = 'quarterly';
    const BILLING_ANNUALLY = 'annually';

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'password',
        'role',
        'account_manager_id',
        'assigned_at',
        'assignment_notes',
        'lease_start_date',
        'billing_frequency',
        'monthly_rate',
        'next_billing_date',
        'auto_billing_enabled',
        'status',
        'profile_completed_at',
        'specialization',
        'county_id',
        'last_login_at',
        'preferences',
        'timezone','address','city','country',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'profile_completion_percentage',
        'has_complete_profile',
        'assigned_customers_count',
        'open_tickets_count',
        'pending_payments_count',
        'full_role_name',
        'is_online',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'assigned_at' => 'datetime',
            'lease_start_date' => 'date',
            'next_billing_date' => 'date',
            'monthly_rate' => 'decimal:2',
            'auto_billing_enabled' => 'boolean',
            'profile_completed_at' => 'datetime',
            'last_login_at' => 'datetime',
            // 'is_active' => 'boolean',
            'preferences' => 'array','role' => 'string',
        ];
    }

    // ==================== ROLE METHODS ====================

    // public function isSystemAdmin(): bool
    // {
    //     return $this->role === self::ROLE_SYSTEM_ADMIN;
    // }

    public function isSystemAdmin(): bool
{
    return in_array($this->role, [self::ROLE_SYSTEM_ADMIN, self::ROLE_ADMIN], true);
}

    public function isMarketingAdmin(): bool
    {
        return $this->role === self::ROLE_MARKETING_ADMIN;
    }

    public function isTechnicalAdmin(): bool
    {
        return $this->role === self::ROLE_TECHNICAL_ADMIN;
    }

    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }

    public function isDesigner(): bool
    {
        return $this->role === self::ROLE_DESIGNER;
    }
 public function isIctEngineer(): bool
    {
        return $this->role === self::ROLE_ICT;
    }
    public function isSurveyor(): bool
    {
        return $this->role === self::ROLE_SURVEYOR;
    }

    public function isTechnician(): bool
    {
        return $this->role === self::ROLE_TECHNICIAN;
    }

    public function isAccountManager(): bool
    {
        return $this->role === self::ROLE_ACCOUNT_MANAGER;
    }
public function customers()
{
    // Make sure 'account_manager_id' is the correct foreign key
    return $this->hasMany(Customer::class, 'account_manager_id');
}
    public function isDebtManager(): bool
    {
        return $this->role === self::ROLE_DEBT_MANAGER;
    }
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
 * Check if user has admin role
 *
 * @return bool
 */
    public function isAdmin(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_MARKETING_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
        ]);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [
            self::ROLE_FINANCE,
            self::ROLE_DESIGNER,
            self::ROLE_ICT,
            self::ROLE_SURVEYOR,
            self::ROLE_TECHNICIAN,
            self::ROLE_ACCOUNT_MANAGER,
        ]);
    }

    public function isFieldStaff(): bool
    {
        return in_array($this->role, [
            self::ROLE_DESIGNER,
            self::ROLE_ICT,
            self::ROLE_SURVEYOR,
            self::ROLE_TECHNICIAN,
        ]);
    }

    // ==================== PERMISSION METHODS ====================

    // Maintenance permissions
    public function canViewMaintenance(): bool
    {
        return !$this->isCustomer();
    }

    public function canCreateMaintenanceRequest(): bool
    {
        return true; // All users can create maintenance requests
    }

    public function canAssignWorkOrders(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
            self::ROLE_DESIGNER,
            self::ROLE_ICT,
            self::ROLE_ACCOUNT_MANAGER,
        ]);
    }

    public function canManageEquipment(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
            self::ROLE_TECHNICIAN,
        ]);
    }

    public function canUpdateWorkOrderStatus(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
            self::ROLE_TECHNICIAN,
            self::ROLE_SURVEYOR,
        ]);
    }

    public function canAccessTechnicianPanel(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
            self::ROLE_TECHNICIAN,
        ]);
    }

    // Finance permissions
    public function canViewFinancialReports(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_FINANCE,
            self::ROLE_MARKETING_ADMIN,
            self::ROLE_ACCOUNT_MANAGER,
        ]);
    }

    public function canProcessPayments(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_FINANCE,
        ]);
    }

    // Design permissions
    public function canCreateDesigns(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
            self::ROLE_DESIGNER,
        ]);
    }

    // Survey permissions
    public function canCreateSurveys(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
            self::ROLE_SURVEYOR,
            self::ROLE_DESIGNER,
        ]);
    }

    // User management permissions
    public function canManageUsers(): bool
    {
        return in_array($this->role, [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
        ]);
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeByRole(Builder $query, $roles): Builder
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return $query->whereIn('role', $roles);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->whereIn('role', [
            self::ROLE_SYSTEM_ADMIN,
            self::ROLE_MARKETING_ADMIN,
            self::ROLE_TECHNICAL_ADMIN,
        ]);
    }

    public function scopeCustomers(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_CUSTOMER);
    }

    public function scopeDesigners(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_DESIGNER);
    }

    public function scopeSurveyors(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_SURVEYOR);
    }

    public function scopeTechnicians(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_TECHNICIAN);
    }

    public function scopeFinance(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_FINANCE);
    }

    public function scopeAccountManagers(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ACCOUNT_MANAGER);
    }

    public function scopeBySpecialization(Builder $query, string $specialization): Builder
    {
        return $query->where('specialization', $specialization);
    }

    public function scopeWithAccountManager(Builder $query): Builder
    {
        return $query->whereNotNull('account_manager_id');
    }

    public function scopeWithoutAccountManager(Builder $query): Builder
    {
        return $query->whereNull('account_manager_id');
    }

    public function scopeAutoBillingEnabled(Builder $query): Builder
    {
        return $query->where('auto_billing_enabled', true);
    }

    public function scopeDueForBilling(Builder $query): Builder
    {
        return $query->where('next_billing_date', '<=', now());
    }

    public function scopeWithPendingBillings(Builder $query): Builder
    {
        return $query->whereHas('leaseBillings', function ($q) {
            $q->where('status', 'pending');
        });
    }

    public function scopeWithOverdueBillings(Builder $query): Builder
    {
        return $query->whereHas('leaseBillings', function ($q) {
            $q->where('due_date', '<', now())
              ->where('status', 'pending');
        });
    }

    public function scopeWithCompleteProfile(Builder $query): Builder
    {
        return $query->whereNotNull('profile_completed_at');
    }

    public function scopeWithIncompleteProfile(Builder $query): Builder
    {
        return $query->whereNull('profile_completed_at');
    }

    public function scopeRecentlyActive(Builder $query, int $days = 7): Builder
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }

    // ==================== RELATIONSHIPS ====================

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }
// In app/Models/User.php
public function billingLineItems()
{
    return $this->hasManyThrough(
        BillingLineItem::class,
        ConsolidatedBilling::class,
        'user_id', // Foreign key on consolidated_billings table
        'consolidated_billing_id', // Foreign key on billing_line_items table
        'id', // Local key on users table
        'id' // Local key on consolidated_billings table
    );
}
    //  public function managedCustomers(): HasMany
    // {
    //     return $this->hasMany(User::class, 'account_manager_id')
    //             ->where('role', 'customer');
    // }
    public function assignedCustomers(): HasMany
    {
        return $this->hasMany(User::class, 'account_manager_id')->where('role', self::ROLE_CUSTOMER);
    }

    public function consolidatedBillings()
    {
        return $this->hasMany(ConsolidatedBilling::class);
    }
    public function surveyAssignments(): HasMany
    {
        return $this->hasMany(SurveyAssignment::class, 'surveyor_id');
    }

    public function assignedWorkOrders(): HasMany
    {
        return $this->hasMany(MaintenanceWorkOrder::class, 'assigned_technician');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'reported_by');
    }

    public function designRequests(): HasMany
    {
        return $this->hasMany(DesignRequest::class, 'customer_id');
    }

    public function createdDesignRequests(): HasMany
    {
        return $this->hasMany(DesignRequest::class, 'designer_id');
    }

    public function surveyRoutes(): HasMany
    {
        return $this->hasMany(SurveyRoute::class, 'surveyor_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(CustomerSupportTicket::class, 'customer_id');
    }

    public function assignedSupportTickets(): HasMany
    {
        return $this->hasMany(CustomerSupportTicket::class, 'account_manager_id');
    }

    public function paymentFollowups(): HasMany
    {
        return $this->hasMany(PaymentFollowup::class, 'customer_id');
    }

    public function assignedPaymentFollowups(): HasMany
    {
        return $this->hasMany(PaymentFollowup::class, 'account_manager_id');
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class, 'customer_id');
    }

    public function createdBillings(): HasMany
    {
        return $this->hasMany(Billing::class, 'created_by');
    }

    public function automatedBillings(): HasMany
    {
        return $this->hasMany(AutomatedBilling::class, 'customer_id');
    }
   public function customerProfile()
    {
        return $this->hasOne(CompanyProfile::class, 'user_id');
    }
    public function colocationServices(): HasMany
    {
        return $this->hasMany(ColocationService::class);
    }

    public function activeColocationServices(): HasMany
    {
        return $this->colocationServices()->active();
    }

    // public function companyProfile(): HasOne
    // {
    //     return $this->hasOne(CompanyProfile::class);
    // }

    //      public function documents()
    // {
    //     return $this->hasMany(Document::class, 'uploaded_by');
    // }
// public function documents()
// {
//     return $this->hasMany(Document::class, 'user_id');
// }
    public function managedLeases(): HasManyThrough
    {
        return $this->hasManyThrough(
            Lease::class,
            User::class,
            'account_manager_id',
            'customer_id',
            'id',
            'id'
        );
    }

    public function leaseBillings(): HasMany
    {
        return $this->hasMany(LeaseBilling::class, 'customer_id');
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'customer_id');
    }

    public function pendingBillings(): HasMany
    {
        return $this->leaseBillings()->where('status', 'pending');
    }

    // ==================== ATTRIBUTE ACCESSORS ====================

public function getProfileCompletionPercentageAttribute(): int
{
    $totalFields = 11; // Company profile fields
    $completedFields = 0;

    // Check company profile completion
    if ($this->companyProfile) {
        $profile = $this->companyProfile->toArray();
        $requiredFields = [
            'kra_pin', 'phone_number', 'registration_number', 'company_type',
            'contact_name_1', 'contact_phone_1', 'physical_location', 'road',
            'town', 'address', 'code'
        ];

        foreach ($requiredFields as $field) {
            if (!empty($profile[$field])) {
                $completedFields++;
            }
        }
    }

    // Get active document types and check uploads
    $requiredDocumentTypes = DocumentType::where('is_active', true)
    ->where('is_required', true)->get();
    $uploadedDocs = 0;

    foreach ($requiredDocumentTypes as $docType) {
        if ($this->documents()->where('document_type', $docType->document_type)->exists()) {
            $uploadedDocs++;
        }
    }

    // Calculate percentages with proper weighting
    // Company profile: 60% weight, Documents: 40% weight
    $profileCompletion = ($completedFields / $totalFields) * 60;
    $documentCompletion = $requiredDocumentTypes->count() > 0
        ? ($uploadedDocs / $requiredDocumentTypes->count()) * 40
        : 0;

    $totalCompletion = $profileCompletion + $documentCompletion;

    // Debug logging
    Log::info('Profile completion calculation:', [
        'user_id' => $this->id,
        'completed_profile_fields' => $completedFields,
        'total_profile_fields' => $totalFields,
        'uploaded_document_types' => $uploadedDocs,
        'total_required_document_types' => $requiredDocumentTypes->count(),
        'total_uploaded_documents' => $this->documents()->count(),
        'profile_completion' => $profileCompletion,
        'document_completion' => $documentCompletion,
        'total_completion' => $totalCompletion
    ]);

    return min(100, (int) round($totalCompletion));
}

    public function getHasCompleteProfileAttribute(): bool
    {
        return $this->hasCompleteProfile();
    }

    public function getAssignedCustomersCountAttribute(): int
    {
        return $this->managedCustomers()->count();
    }

    public function getOpenTicketsCountAttribute(): int
    {
        return $this->assignedSupportTickets()
            ->whereIn('status', ['open', 'in_progress'])
            ->count();
    }

    public function getPendingPaymentsCountAttribute(): int
    {
        return $this->assignedPaymentFollowups()
            ->whereIn('status', ['pending', 'reminded'])
            ->count();
    }

    public function getFullRoleNameAttribute(): string
    {
        return match($this->role) {
            self::ROLE_SYSTEM_ADMIN => 'System Administrator',
            self::ROLE_MARKETING_ADMIN => 'Marketing Administrator',
            self::ROLE_TECHNICAL_ADMIN => 'Technical Administrator',
            self::ROLE_FINANCE => 'Finance Manager',
            self::ROLE_DESIGNER => 'Network Designer',
            self::ROLE_ICT => 'Network Designer',
            self::ROLE_SURVEYOR => 'Field Surveyor',
            self::ROLE_TECHNICIAN => 'Field Technician',
            self::ROLE_ACCOUNT_MANAGER => 'Account Manager',
            self::ROLE_DEBT_MANAGER => 'Debt Manager',
            self::ROLE_CUSTOMER => 'Customer',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

  public function getQuickActionCount()
 {
     if ($this->isSystemAdmin()) {
         return 4;
     } elseif ($this->isTechnicalAdmin()) {
         return 4;
          } elseif ($this->isIctEngineer()) {
         return 4;
     } elseif ($this->isAccountManager()) {
         return 4;
           } elseif ($this->isDebtManager()) {
         return 4;
     } elseif ($this->isFinance()) {
         return 4;
     } elseif ($this->isDesigner()) {
         return 4;
     } elseif ($this->isSurveyor()) {
         return 4;
     } elseif ($this->isTechnician()) {
         return 4;
     } elseif ($this->isCustomer()) {
         $count = 0;
         if (Route::has('customer.design-requests.create')) $count++;
         if (Route::has('customer.leases.index')) $count++;
         if (Route::has('customer.tickets')) $count++;
         if (Route::has('customer.profile')) $count++;
         return $count;
     } else {
         // Default admin actions
         return 6;
     }
 }
    public function getIsOnlineAttribute(): bool
    {
        return $this->last_login_at && $this->last_login_at->gt(now()->subMinutes(5));
    }

    // ==================== BUSINESS LOGIC METHODS ====================

    public function calculateNextBillingDate(): ?Carbon
    {
        if (!$this->lease_start_date || !$this->billing_frequency) {
            return null;
        }

        $lastBillingDate = $this->next_billing_date ?: $this->lease_start_date;

        return match($this->billing_frequency) {
            self::BILLING_MONTHLY => Carbon::parse($lastBillingDate)->addMonth(),
            self::BILLING_QUARTERLY => Carbon::parse($lastBillingDate)->addMonths(3),
            self::BILLING_ANNUALLY => Carbon::parse($lastBillingDate)->addYear(),
            default => Carbon::parse($lastBillingDate)->addMonth(),
        };
    }

    public function calculateBillingAmount(): float
    {
        return match($this->billing_frequency) {
            self::BILLING_MONTHLY => $this->monthly_rate,
            self::BILLING_QUARTERLY => $this->monthly_rate * 3,
            self::BILLING_ANNUALLY => $this->monthly_rate * 12,
            default => $this->monthly_rate,
        };
    }

    public function getBillingDescription(): string
    {
        $frequency = ucfirst($this->billing_frequency);
        $period = now()->format('F Y');

        return "DarkFibre Lease - {$frequency} Service Fee ({$period})";
    }

    public function hasColocationServices(): bool
    {
        return $this->colocationServices()->exists();
    }

    public function hasActiveColocationServices(): bool
    {
        return $this->activeColocationServices()->exists();
    }

   public function hasCompleteProfile(): bool
{
    if (!$this->companyProfile) {
        return false;
    }

    $requiredDocs = Document::required()->pluck('document_type')->toArray();

    foreach ($requiredDocs as $docType) {
        $hasUploadedDoc = $this->documents()
            ->where('document_type', $docType)
            ->exists(); // Changed from where('status', 'approved')

        if (!$hasUploadedDoc) {
            return false;
        }
    }

    return true;
}

    public function hasUploadedRequiredDocuments(): bool
    {
        $requiredDocs = Document::required()->pluck('document_type')->toArray();

        foreach ($requiredDocs as $docType) {
            $hasDoc = $this->documents()
                ->where('document_type', $docType)
                ->exists();

            if (!$hasDoc) {
                return false;
            }
        }

        return true;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function activate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'profile_completed_at' => now(),
        ]);
    }

    public function suspend(): void
    {
        $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    public function deactivate(): void
    {
        $this->update(['status' => self::STATUS_INACTIVE]);
    }

    public function hasPendingBillings(): bool
    {
        return $this->leaseBillings()->where('status', 'pending')->exists();
    }

    public function recordLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    public function getAccessibleModules(): array
    {
        return match($this->role) {
            self::ROLE_SYSTEM_ADMIN => ['dashboard', 'users', 'billing', 'reports', 'settings', 'analytics'],
            self::ROLE_MARKETING_ADMIN => ['dashboard', 'analytics', 'customers', 'reports'],
            self::ROLE_TECHNICAL_ADMIN => ['dashboard', 'network', 'infrastructure', 'reports'],
            self::ROLE_FINANCE => ['dashboard', 'billing', 'invoices', 'reports'],
            self::ROLE_DESIGNER => ['dashboard', 'design', 'projects'],
            self::ROLE_ICT => ['dashboard', 'design', 'projects'],
            self::ROLE_SURVEYOR => ['dashboard', 'surveys', 'reports'],
            self::ROLE_TECHNICIAN => ['dashboard', 'workorders', 'maintenance'],
            self::ROLE_ACCOUNT_MANAGER => ['dashboard', 'customers', 'support', 'billing'],
            self::ROLE_DEBT_MANAGER => ['dashboard', 'billing', 'invoices', 'reports','debt', 'customers'],
            self::ROLE_CUSTOMER => ['dashboard', 'services', 'support', 'billing'],
            default => ['dashboard'],
        };
    }

    public function getDashboardStats(): array
    {
        $baseStats = [
            'profile_completion' => $this->profile_completion_percentage,
            'last_login' => $this->last_login_at?->diffForHumans(),
        ];

        return array_merge($baseStats, match($this->role) {
            self::ROLE_ACCOUNT_MANAGER => [
                'managed_customers' => $this->assigned_customers_count,
                'open_tickets' => $this->open_tickets_count,
                'pending_payments' => $this->pending_payments_count,
            ],
            self::ROLE_CUSTOMER => [
                'active_services' => $this->activeColocationServices()->count(),
                'pending_billings' => $this->pendingBillings()->count(),
                'open_tickets' => $this->supportTickets()->whereIn('status', ['open', 'in_progress'])->count(),
            ],
            self::ROLE_TECHNICIAN => [
                'assigned_work_orders' => $this->assignedWorkOrders()->where('status', '!=', 'completed')->count(),
                'completed_this_month' => $this->assignedWorkOrders()
                    ->where('status', 'completed')
                    ->whereMonth('completed_at', now()->month)
                    ->count(),
            ],
            default => [],
        });
    }

     public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // In your User model
public function isAccountManagerAdmin()
{
    return $this->role === 'accountmanager_admin';
}

public function getRoleBadgeColor()
{
    return match($this->role) {
        'admin' => 'danger',
        'technical_admin' => 'warning',
        'system_admin' => 'primary',
        'accountmanager_admin' => 'info',
        'account_manager' => 'info',
        'debt_manager' => 'info',
         'ict_engineer' => 'info',
        'technician' => 'warning',
        'finance' => 'success',
        'customer' => 'primary',
        'designer' => 'purple',
        'surveyor' => 'secondary',
        default => 'secondary'
    };
}

public function getFullRoleName()
{
    return match($this->role) {
        'admin' => 'Administrator',
        'technical_admin' => 'Technical Administrator',
        'system_admin' => 'System Administrator',
        'accountmanager_admin' => 'Marketing Administrator',
        'account_manager' => 'Account Manager',
        'debt_manager' => 'Debt Manager',
        'technician' => 'Field Technician',
        'ict_engineer' => 'ICT Engineer',
        'finance' => 'Finance Manager',
        'designer' => 'Network Designer',
        'surveyor' => 'Field Surveyor',
        'customer' => 'Customer',
        default => ucfirst(str_replace('_', ' ', $this->role))
    };
}
/**
 * Get the county assigned to this user.
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function county()
{
    return $this->belongsTo(County::class, 'county_id');
}

/**
 * Scope a query to filter users by county.
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
 * Scope a query to filter users by region through county.
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
    // ==================== CHAT-RELATED METHODS ====================

    /**
     * Get the user's conversations
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'participants')
            ->withPivot(['joined_at', 'last_read_at', 'role'])
            ->withTimestamps();
    }

    /**
     * Get the user's messages
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get conversations with unread messages
     */
    public function unreadConversations()
    {
        return $this->conversations()->whereHas('messages', function ($query) {
            $query->where('created_at', '>', \DB::raw('participants.last_read_at'))
                ->where('user_id', '!=', $this->id);
        });
    }

//    /**
//      * Send the password reset notification.
//      *
//      * @param  string  $token
//      * @return void
//      */
//     public function sendPasswordResetNotification($token)
//     {
//         $this->notify(new ResetPasswordNotification($token));
//     }

    /**
     * Check if user can chat with another user
     * Based on role permissions and assignment relationships
     */
    public function canChatWith($otherUser)
    {
        // Can't chat with yourself
        if ($this->id === $otherUser->id) {
            return false;
        }

        // Admins can chat with anyone
        if ($this->isAdmin()) {
            return true;
        }

        // Customers can only chat with admins/account managers
        if ($this->role === 'customer') {
            return in_array($otherUser->role, [
                'admin', 'system_admin', 'account_manager',
                'accountmanager_admin', 'technical_admin', 'ict_engineer'
            ]);
        }

        // Check if this user is assigned to the other user (customer to account manager)
        if ($this->role === 'customer' && $otherUser->role === 'account_manager') {
            return $this->account_manager_id === $otherUser->id;
        }

        // Check if other user is assigned to this user (account manager to customer)
        if ($this->role === 'account_manager' && $otherUser->role === 'customer') {
            return $otherUser->account_manager_id === $this->id;
        }

        // Check surveyor assignments
        if ($this->role === 'customer' && $otherUser->role === 'surveyor') {
            // Check if surveyor is assigned to any of customer's design requests
            return $this->designRequests()
                ->where('surveyor_id', $otherUser->id)
                ->exists();
        }

        if ($this->role === 'surveyor' && $otherUser->role === 'customer') {
            return $otherUser->designRequests()
                ->where('surveyor_id', $this->id)
                ->exists();
        }

        // Internal staff can chat with each other
        $internalRoles = [
            'admin', 'system_admin', 'account_manager', 'designer',
            'surveyor', 'technician', 'finance', 'ict_engineer',
            'accountmanager_admin', 'technical_admin', 'debt_manager'
        ];

        return in_array($this->role, $internalRoles) &&
               in_array($otherUser->role, $internalRoles);
    }

    /**
     * Get users that this user is allowed to chat with
     */
    public function getAvailableChatUsers()
    {
        return User::where('id', '!=', $this->id)
            ->where(function ($query) {
                if ($this->role === 'customer') {
                    // Customers can only chat with admins and their assigned account manager
                    $query->whereIn('role', [
                        'admin', 'system_admin', 'account_manager',
                        'accountmanager_admin', 'technical_admin', 'ict_engineer'
                    ]);

                    // Also include their assigned account manager specifically
                    if ($this->account_manager_id) {
                        $query->orWhere('id', $this->account_manager_id);
                    }
                } elseif ($this->role === 'account_manager') {
                    // Account managers can chat with their assigned customers and other staff
                    $query->where(function ($q) {
                        $q->whereIn('role', [
                            'admin', 'system_admin', 'accountmanager_admin',
                            'technical_admin', 'designer', 'surveyor',
                            'technician', 'finance', 'ict_engineer', 'debt_manager'
                        ]);
                    })->orWhere(function ($q) {
                        // Their assigned customers
                        $q->where('role', 'customer')
                          ->where('account_manager_id', $this->id);
                    });
                } else {
                    // Other staff can chat with other staff
                    $query->whereIn('role', [
                        'admin', 'system_admin', 'account_manager', 'designer',
                        'surveyor', 'technician', 'finance', 'ict_engineer',
                        'accountmanager_admin', 'technical_admin', 'debt_manager'
                    ]);
                }
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Get or create a direct conversation with another user
     */
    public function getOrCreateConversationWith($otherUserId)
    {
        $otherUser = User::findOrFail($otherUserId);

        // Check if conversation already exists
        $conversation = Conversation::whereHas('participants', function ($q) {
            $q->where('user_id', $this->id);
        })->whereHas('participants', function ($q) use ($otherUserId) {
            $q->where('user_id', $otherUserId);
        })->where('type', 'direct')->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::create([
                'type' => 'direct',
                'title' => null
            ]);

            // Add participants
            $conversation->participants()->create([
                'user_id' => $this->id,
                'role' => 'member'
            ]);

            $conversation->participants()->create([
                'user_id' => $otherUserId,
                'role' => 'member'
            ]);
        }

        return $conversation;
    }

    /**
     * Get recent conversations with last message
     */
    public function getRecentConversations($limit = 10)
    {
        return $this->conversations()
            ->with(['lastMessage', 'users' => function ($query) {
                $query->where('users.id', '!=', $this->id);
            }])
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                    ->from('messages')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->orderByDesc('created_at')
                    ->limit(1);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all messages in a conversation as read
     */
    public function markConversationAsRead($conversationId)
    {
        $conversation = $this->conversations()->find($conversationId);

        if ($conversation) {
            // Update participant's last_read_at
            $conversation->participants()
                ->where('user_id', $this->id)
                ->update(['last_read_at' => now()]);

            // Mark messages as read
            Message::where('conversation_id', $conversationId)
                ->where('user_id', '!=', $this->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    /**
     * Check if user is online (for chat presence)
     */
    public function isOnlineForChat(): bool
    {
        return $this->last_login_at && $this->last_login_at->gt(now()->subMinutes(15));
    }

    /**
     * Get user's chat status
     */
    public function getChatStatusAttribute(): string
    {
        if (!$this->last_login_at) {
            return 'offline';
        }

        if ($this->last_login_at->gt(now()->subMinutes(5))) {
            return 'online';
        } elseif ($this->last_login_at->gt(now()->subMinutes(15))) {
            return 'away';
        } else {
            return 'offline';
        }
    }
    // In app/Models/User.php

/**
 * Get the customers managed by this account manager
 */
public function managedCustomers()
{
    return $this->hasMany(User::class, 'account_manager_id', 'id')
                ->where('role', 'customer');  // This is important!
}

/**
 * Get the count of managed customers
 */
public function getManagedCustomersCountAttribute()
{
    return $this->managedCustomers()->count();
}

   /**
     * Get transactions for this user (as customer)
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Get statements for this user (as customer)
     */
    public function statements(): HasMany
    {
        return $this->hasMany(PaymentStatement::class, 'user_id');
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->transactions()
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first()?->balance ?? $this->opening_balance;
    }

///////////////////////////
// In app/Models/User.php
public function companyProfile()
{
    return $this->hasOne(CompanyProfile::class, 'user_id');
}

// Optional: if you use 'profile' as the relationship name
public function profile()
{
    return $this->hasOne(CompanyProfile::class, 'user_id');
}

public function documents()
{
    return $this->hasMany(Document::class, 'user_id');
}
/**
 * Get total unread messages count across all conversations
 */
public function totalUnreadMessages()
{
    return \App\Models\Message::whereHas('conversation.participants', function ($query) {
            $query->where('user_id', $this->id);
        })
        ->where('user_id', '!=', $this->id)
        ->whereNull('read_at')
        ->count();
}

/**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

}

