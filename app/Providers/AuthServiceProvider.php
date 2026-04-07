<?php

namespace App\Providers;

use App\Models\Contract;
use App\Models\Lease;
use App\Models\User;
use App\Models\DesignRequest;
use App\Models\Quotation;
use App\Models\Conversation;
use App\Policies\ContractPolicy;
use App\Policies\DesignRequestPolicy;
use App\Policies\LeasePolicy;
use App\Policies\QuotationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View as FacadesView;
use Illuminate\View\View;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Lease::class => LeasePolicy::class,
        \App\Models\FinancialParameter::class => \App\Policies\FinancialParameterPolicy::class,
        Contract::class => ContractPolicy::class,
        DesignRequest::class => DesignRequestPolicy::class,
        Quotation::class => QuotationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-system-documents', function ($user) {
            return in_array($user->role, [
                'admin',
                'system_admin',
                'technical_admin',
                'account_manager',
                'accountmanager_admin',
                'finance',
                'ict_engineer'
            ]);
        });

        Gate::define('isAccountManager', function ($user) {
            return $user->role === 'account_manager';
        });

        Gate::define('accessAdminPanel', function (User $user) {
            return in_array($user->role, ['admin', 'account_manager']);
        });

        // ===== QUOTATION GATES =====
        Gate::define('manage-quotations', function (?User $user) {
            return $user && in_array($user->role, ['admin', 'account_manager', 'accountmanager_admin', 'designer']);
        });

        Gate::define('view-quotations', function (?User $user) {
            return $user && in_array($user->role, ['admin', 'account_manager', 'accountmanager_admin', 'designer']);
        });

        Gate::define('create-quotations', function (?User $user) {
            return $user && in_array($user->role, ['admin', 'account_manager', 'accountmanager_admin', 'designer']);
        });

        Gate::define('edit-quotations', function (?User $user, $quotation = null) {
            if (!$user) return false;

            if (!$quotation) {
                return in_array($user->role, ['admin', 'account_manager', 'accountmanager_admin', 'designer']);
            }

            if (in_array($user->role, ['account_manager', 'accountmanager_admin', 'designer'])) {
                return $quotation->status === 'draft' && $quotation->account_manager_id === $user->id;
            }

            return $user->role === 'admin' && $quotation->status === 'draft';
        });

        Gate::define('send-quotations', function (?User $user) {
            return $user && in_array($user->role, [
                'admin',
                'system_admin',
                'account_manager',
                'accountmanager_admin'
            ]);
        });

        Gate::define('reject-quotations', function (?User $user) {
            return $user && in_array($user->role, [
                'admin',
                'system_admin',
                'account_manager',
                'accountmanager_admin'
            ]);
        });

        Gate::define('approve-quotations', function (?User $user) {
            return $user && in_array($user->role, [
                'admin',
                'system_admin',
                'account_manager',
                'accountmanager_admin'
            ]);
        });

        // ===== LEASE-RELATED GATES =====
        Gate::define('access-leases', function (?User $user) {
            return $user && in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'account_manager', 'accountmanager_admin']);
        });

        Gate::define('view-customer-leases', function (?User $user, $customerId) {
            if (!$user) return false;

            if (in_array($user->role, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin'])) {
                return true;
            }

            if ($user->role === 'account_manager') {
                return $user->managedCustomers()->where('id', $customerId)->exists();
            }

            return false;
        });

        Gate::define('manage-customers', function ($user) {
            return $user->isAccountManager();
        });

        Gate::define('finance-access', function (?User $user) {
            return $user && in_array($user->role, ['admin', 'finance', 'accountmanager_admin', 'debt_manager']);
        });

        // ===== CONTRACT GATES =====
        Gate::define('sendToCustomer', function (User $user) {
            return in_array($user->role, [
                'admin',
                'technical_admin',
                'system_admin',
                'account_manager',
                'debt_manager',
                'accountmanager_admin'
            ]);
        });

        // ===== CUSTOMER QUOTATION GATES =====
        Gate::define('customerView', function (User $user, Quotation $quotation) {
            return $user->role === 'customer' && $quotation->customer_id === $user->id;
        });

        Gate::define('customerViewAny', function (User $user) {
            return $user->role === 'customer';
        });

        Gate::define('isICTEngineer', function ($user) {
            return in_array($user->role, ['ict_engineer', 'account_manager']);
        });

        Gate::define('isEngineer', function ($user) {
            return in_array($user->role, ['ict_engineer', 'designer', 'technician', 'surveyor']);
        });

        Gate::define('customerApprove', function (User $user, Quotation $quotation) {
            return $user->role === 'customer'
                && $quotation->customer_id === $user->id
                && $quotation->status === 'sent';
        });

        Gate::define('customerReject', function (User $user, Quotation $quotation) {
            return $user->role === 'customer'
                && $quotation->customer_id === $user->id
                && $quotation->status === 'sent';
        });

        Gate::define('customerRequestRevision', function (User $user, Quotation $quotation) {
            return $user->role === 'customer'
                && $quotation->customer_id === $user->id
                && $quotation->status === 'sent';
        });

        Gate::define('access-quotations', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('designer');
        });

        // ===== CHAT GATES =====
        $this->defineChatGates();

        // ===== ROLE-SPECIFIC GATES =====
        $this->defineRoleGates();

        // ===== MAINTENANCE MODULE GATES =====
        $this->defineMaintenanceGates();

        // ===== DESIGN REQUESTS GATES =====
        $this->defineDesignRequestGates();

        // ===== TECHNICAL ADMIN GATES =====
        $this->defineTechnicalGates();
    }

    /**
     * Define role-specific gates
     */
    protected function defineRoleGates(): void
    {
        $roles = [
            'isAdmin' => ['admin', 'designer', 'technical_admin', 'system_admin', 'accountmanager_admin', 'account_manager', 'debt_manager'],
            'isCustomer' => ['customer'],
            'isFinance' => ['finance'],
            'isDesigner' => ['designer'],
            'isTechnician' => ['technician'],
            'isSurveyor' => ['surveyor'],
            'isTechnicalAdmin' => ['technical_admin'],
            'isSystemAdmin' => ['system_admin'],
            'isMarketingAdmin' => ['accountmanager_admin'],
            'isAccountManager' => ['account_manager'],
            'isDebtManager' => ['debt_manager'],
            'isUser' => ['admin', 'technical_admin', 'system_admin', 'customer', 'finance', 'designer', 'surveyor', 'technician', 'debt_manager', 'account_manager', 'accountmanager_admin'],
        ];

        foreach ($roles as $gateName => $allowedRoles) {
            Gate::define($gateName, function (?User $user) use ($allowedRoles) {
                return $user && in_array($user->role, $allowedRoles);
            });
        }
    }

    /**
     * Define chat module gates
     */
    protected function defineChatGates(): void
    {
        // Who can use the chat feature
        Gate::define('use-chat', function (?User $user) {
            if (!$user) return false;

            $allowedRoles = [
                'admin',
                'system_admin',
                'technical_admin',
                'account_manager',
                'accountmanager_admin',
                'customer',
                'finance',
                'designer',
                'surveyor',
                'technician',
                'debt_manager',
                'ict_engineer'
            ];

            return in_array($user->role, $allowedRoles);
        });

        // Who can send messages
        Gate::define('send-messages', function (?User $user) {
            return Gate::allows('use-chat', $user);
        });

        // Who can start new conversations
        Gate::define('start-conversation', function (?User $user) {
            return Gate::allows('use-chat', $user);
        });

        // View a specific conversation
        Gate::define('view-conversation', function (?User $user, $conversation) {
            if (!$user) return false;

            // Admins can view all conversations
            if (in_array($user->role, ['admin', 'system_admin', 'technical_admin', 'accountmanager_admin'])) {
                return true;
            }

            // Check if user is a participant in the conversation
            if ($conversation instanceof Conversation) {
                return $conversation->participants()->where('user_id', $user->id)->exists();
            }

            // If conversation ID is passed instead of model
            $conversationId = is_numeric($conversation) ? $conversation : $conversation->id;
            return Conversation::where('id', $conversationId)
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->exists();
        });

        // Delete conversations (restricted to admins only)
        Gate::define('delete-conversation', function (?User $user) {
            return $user && in_array($user->role, ['admin', 'system_admin', 'technical_admin']);
        });
    }

    /**
     * Define maintenance module gates
     */
    protected function defineMaintenanceGates(): void
    {
        $maintenanceGates = [
            'view-maintenance' => ['admin', 'technical_admin', 'system_admin', 'technician', 'designer', 'surveyor', 'customer'],
            'create-maintenance-request' => ['admin', 'technical_admin', 'system_admin', 'designer', 'surveyor', 'customer'],
            'assign-work-orders' => ['admin', 'technical_admin', 'system_admin'],
            'manage-equipment' => ['admin', 'technical_admin', 'system_admin', 'technician'],
            'view-maintenance-reports' => ['admin', 'technical_admin', 'system_admin', 'technician'],
            'update-work-order-status' => ['admin', 'technical_admin', 'system_admin', 'technician'],
            'complete-work-orders' => ['admin', 'technical_admin', 'system_admin', 'technician'],
            'view-all-maintenance-requests' => ['admin', 'technical_admin', 'system_admin', 'technician', 'designer', 'surveyor'],
            'edit-maintenance-requests' => ['admin', 'technical_admin', 'system_admin'],
            'delete-maintenance-requests' => ['admin', 'technical_admin', 'system_admin'],
        ];

        foreach ($maintenanceGates as $gateName => $allowedRoles) {
            Gate::define($gateName, function (?User $user) use ($allowedRoles) {
                return $user && in_array($user->role, $allowedRoles);
            });
        }
    }

    /**
     * Define design request gates
     */
    protected function defineDesignRequestGates(): void
    {
        $designGates = [
            'view-design-requests' => ['admin', 'technical_admin', 'system_admin', 'account_manager', 'designer', 'surveyor', 'accountmanager_admin', 'customer'],
            'manage-design-requests' => ['admin', 'technical_admin', 'system_admin', 'account_manager', 'accountmanager_admin'],
            'assign-designer' => ['admin', 'technical_admin', 'system_admin', 'account_manager', 'accountmanager_admin'],
            'assign-surveyor' => ['admin', 'technical_admin', 'system_admin', 'account_manager', 'accountmanager_admin'],
            'create-quotation' => ['admin', 'technical_admin', 'system_admin', 'account_manager', 'accountmanager_admin'],
            'edit-design-requests' => ['admin', 'technical_admin', 'system_admin', 'account_manager', 'accountmanager_admin'],
            'delete-design-requests' => ['admin', 'technical_admin', 'system_admin'],
        ];

        foreach ($designGates as $gateName => $allowedRoles) {
            Gate::define($gateName, function (?User $user) use ($allowedRoles) {
                return $user && in_array($user->role, $allowedRoles);
            });
        }

        Gate::define('view-own-design-requests', function (?User $user, $designRequest) {
            if (!$user) return false;

            if ($user->role === 'customer') {
                return $designRequest->user_id === $user->id;
            }

            return in_array($user->role, [
                'admin', 'technical_admin', 'system_admin', 'account_manager',
                'designer', 'surveyor', 'accountmanager_admin'
            ]);
        });
    }

    /**
     * Define technical admin gates
     */
    protected function defineTechnicalGates(): void
    {
        $technicalGates = [
            'manage-system-config' => ['technical_admin', 'system_admin'],
            'access-technical-reports' => ['admin', 'technical_admin', 'system_admin', 'technician'],
            'manage-network-infrastructure' => ['admin', 'technical_admin', 'system_admin'],
        ];

        foreach ($technicalGates as $gateName => $allowedRoles) {
            Gate::define($gateName, function (?User $user) use ($allowedRoles) {
                return $user && in_array($user->role, $allowedRoles);
            });
        }
    }
}
