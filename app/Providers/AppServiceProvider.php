<?php

namespace App\Providers;

use App\Models\DesignRequest;
use App\Observers\DesignRequestObserver;
use App\Services\InvoicePdfService;
use App\Services\BillingService;
use App\Services\ReportService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{


    public function boot(): void
    {

    // Schema::defaultStringLength(191);
Paginator::useBootstrapFive();
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\ImportConversionData::class,
                \App\Console\Commands\GenerateConversionReports::class,
            ]);
        }

    // SAP Assignment access gate
    Gate::define('access-finance', function ($user) {
        return in_array($user->role, [
            'finance',
            'finance_admin',
            'debt_manager',
            'admin',
            'system_admin'
        ]);
    });

    // SAP Management gate
    Gate::define('manage-sap', function ($user) {
        return Gate::allows('access-finance', $user);
    });

    // Add other missing admin gates
    Gate::define('isSystemAdmin', function ($user) {
        return $user->role === 'system_admin';
    });

    Gate::define('isMarketingAdmin', function ($user) {
        return $user->role === 'accountmanager_admin';
    });

    Gate::define('isTechnicalAdmin', function ($user) {
        return $user->role === 'technical_admin';
    });
 DesignRequest::observe(DesignRequestObserver::class);
        // Status color helper
        Blade::directive('statusColor', function ($status) {
            return "<?php echo \App\Helpers\StatusHelper::getStatusColor($status); ?>";
        });

        Blade::directive('surveyStatusColor', function ($status) {
            return "<?php echo \App\Helpers\StatusHelper::getSurveyStatusColor($status); ?>";
        });

        Blade::directive('statusBadge', function ($expression) {
        return "<?php echo match($expression) {
            'pending' => 'bg-secondary',
            'Assigned' => 'badge-custom-assigned',
            'designed' => 'bg-success',
            'quoted' => 'bg-info',
            default => 'bg-light text-dark'
        }; ?>";
    });
        // Existing role-based gates
        Gate::define('isAdmin', function ($user) {
            \Illuminate\Support\Facades\Log::info('isAdmin Gate Called', [
                'user_id' => $user->id,
                'role' => $user->role,
                'result' => $user->role === 'admin'
            ]);

            return $user->role === 'admin';
        });

        Gate::define('isCustomer', function ($user) {
            return $user->role === 'customer';
        });

        Gate::define('isDesigner', function ($user) {
            return $user->role === 'designer';
        });

        Gate::define('isFinance', function ($user) {
            return $user->role === 'finance';
        });

        Gate::define('isSurveyor', function ($user) {
            return $user->role === 'surveyor';
        });

        // Add technician role gate
        Gate::define('isTechnician', function ($user) {
            return $user->role === 'technician';
        });

        Gate::define('isAccountManager', function ($user) {
            return $user->role === 'account_manager';
        });
        Gate::define('isICTEngineer', function ($user) {
            return $user->role === 'ict_engineer';
        });

        Gate::define('isDebtManager', function ($user) {
            return $user->role === 'debt_manager';
        });
         Gate::define('financeOrDebtManager', function ($user) {
        return in_array($user->role, ['finance', 'debt_manager']);
    });
    Gate::define('ictengineerOrDesigner', function ($user) {
        return in_array($user->role, ['designer', 'ict_engineer', 'account_manager']);
    });
Gate::define('accountManagerOrDesigner', function ($user) {
        return in_array($user->role, ['designer', 'account_manager']);
    });

        // =========================================================================
        // FINANCE MODULE GATES
        // =========================================================================

        // View finance module
        Gate::define('view-finance', function ($user) {
            return in_array($user->role, ['admin', 'finance', 'account_manager', 'debt_manager']);
        });

        // Create billings
        Gate::define('create-billings', function ($user) {
            return in_array($user->role, ['admin', 'finance', 'account_manager', 'debt_manager']);
        });

        // Manage payments
        Gate::define('manage-payments', function ($user) {
            return in_array($user->role, ['admin', 'finance', 'debt_manager']);
        });

        // View financial reports
        Gate::define('view-financial-reports', function ($user) {
            return in_array($user->role, ['admin', 'finance', 'account_manager', 'debt_manager']);
        });

        // Manage transactions
        Gate::define('manage-transactions', function ($user) {
            return in_array($user->role, ['admin', 'finance', 'debt_manager']);
        });

        // Export financial data
        Gate::define('export-financial-data', function ($user) {
            return in_array($user->role, ['admin', 'finance', 'debt_manager']);
        });

        // Manage auto-billing
        Gate::define('manage-auto-billing', function ($user) {
            return in_array($user->role, ['admin', 'finance', 'debt_manager']);
        });

        // =========================================================================
        // MAINTENANCE MODULE GATES
        // =========================================================================

        // View maintenance module
        Gate::define('view-maintenance', function ($user) {
            return in_array($user->role, ['admin', 'technician', 'designer', 'surveyor', 'customer']);
        });

        // Create maintenance requests
        Gate::define('create-maintenance-request', function ($user) {
            return in_array($user->role, ['admin', 'technician', 'designer', 'surveyor', 'customer']);
        });

        // Assign work orders
        Gate::define('assign-work-orders', function ($user) {
            return in_array($user->role, ['admin', 'designer']);
        });

        // Manage equipment
        Gate::define('manage-equipment', function ($user) {
            return in_array($user->role, ['admin', 'technician']);
        });

        // Update work order status
        Gate::define('update-work-order-status', function ($user) {
            return in_array($user->role, ['admin', 'technician', 'surveyor']);
        });

        // Resolve maintenance requests
        Gate::define('resolve-maintenance-requests', function ($user) {
            return in_array($user->role, ['admin', 'technician', 'designer']);
        });

        // View maintenance reports
        Gate::define('view-maintenance-reports', function ($user) {
            return in_array($user->role, ['admin', 'designer', 'finance']);
        });

        // Manage maintenance settings
        Gate::define('manage-maintenance-settings', function ($user) {
            return $user->role === 'admin';
        });

        // =========================================================================
        // UTILITY GATES FOR MAINTENANCE TEAMS
        // =========================================================================

        // Check if user is part of maintenance team
        Gate::define('is-maintenance-team', function ($user) {
            return in_array($user->role, ['admin', 'technician', 'designer']);
        });

        // Check if user is part of field team
        Gate::define('is-field-team', function ($user) {
            return in_array($user->role, ['technician', 'surveyor']);
        });

        // Check if user can access customer data
        Gate::define('access-customer-data', function ($user) {
            return in_array($user->role, ['admin', 'designer', 'customer']);
        });

        // =========================================================================
        // SPECIFIC MAINTENANCE PERMISSIONS
        // =========================================================================

        // Can close maintenance requests
        Gate::define('close-maintenance-requests', function ($user) {
            return in_array($user->role, ['admin', 'technician', 'designer']);
        });

        // Can view maintenance costs
        Gate::define('view-maintenance-costs', function ($user) {
            return in_array($user->role, ['admin', 'designer', 'finance']);
        });

        // Can manage maintenance schedules
        Gate::define('manage-maintenance-schedules', function ($user) {
            return in_array($user->role, ['admin', 'designer']);
        });

        // Can export maintenance data
        Gate::define('export-maintenance-data', function ($user) {
            return in_array($user->role, ['admin', 'designer', 'finance']);
        });
    }

    public function register()
    {
        $this->app->singleton(InvoicePdfService::class, function ($app) {
            return new InvoicePdfService($app->make('dompdf.wrapper'));
        });

        $this->app->singleton(BillingService::class, function ($app) {
            return new BillingService();
        });

        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService();
        });
        $this->app->singleton('app.version', function () {
            // Try to get from env or composer.json
            return env('APP_VERSION', '1.0.0');
        });
    }

}

