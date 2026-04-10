<?php

use App\Http\Controllers\AccountManager\DocumentApprovalController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\AccountManagerManualDocumentController;
use App\Http\Controllers\Admin\AdminQuotationController;
use App\Http\Controllers\Admin\DesignRequestController;
use App\Http\Controllers\Admin\DocumentRequestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ConversionDataController;
use App\Http\Controllers\Customer\BillingController;
use App\Http\Controllers\Customer\DocumentController;
use App\Http\Controllers\CustomerCertificateController;
use App\Http\Controllers\CustomerSapController;
use App\Http\Controllers\DarkfireController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\FinancialSyncController;
use App\Http\Controllers\ICTEngineerCertificateController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveyorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DesignerController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\DesignItemController;
use App\Http\Controllers\SurveyResultController;
use App\Http\Controllers\SurveyRouteController;
use App\Http\Controllers\Admin\SurveyRequestController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\CustomerDocumentController;
use App\Http\Controllers\CustomerLeaseController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\FinancialParameterController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PaymentFollowupController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\SystemAdminController;
use App\Http\Controllers\MarketingAdminController;
use App\Http\Controllers\TechnicalAdminController;
use App\Http\Controllers\Customer\DesignRequestController as CustomerDesignRequestController;
use App\Http\Controllers\Customer\QuotationController as CustomerQuotationController;
use App\Http\Controllers\Customer\ContractController as CustomerContractController;
use App\Models\DesignRequest;
use App\Models\Lease;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\CommercialRouteController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\KenyaFibreDashboardController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Finance\BillingController as FinanceBillingController;
use App\Http\Controllers\PdfController;
use App\Models\Quotation;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Finance\AiAnalyticsController;
use App\Http\Controllers\Finance\DebtManagementController;
use App\Http\Controllers\Finance\FinancialAnalyticsController;
use App\Http\Controllers\ICTEngineerController;
use App\Models\Conversation;
use App\Http\Controllers\PaymentStatementController;
use App\Http\Controllers\CustomerPortal\StatementController;
use App\Http\Middleware\CheckProfileCompletion;
use Illuminate\Support\Facades\Gate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================
// Rate Limiting Configuration
// ==========================
Route::pattern('id', '[0-9]+');
Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

// Configure rate limiters
RateLimiter::for('login', function ($job) {
    return Limit::perMinute(5)->by($job->ip());
});

RateLimiter::for('registration', function ($job) {
    return Limit::perMinute(3)->by($job->ip());
});

RateLimiter::for('api', function ($job) {
    return Limit::perMinute(60)->by($job->user()?->id ?: $job->ip());
});

RateLimiter::for('password-reset', function ($job) {
    return Limit::perMinute(3)->by($job->ip());
});

// ==========================
// Password Reset Routes (with rate limiting)
// ==========================
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:password-reset')
    ->name('password.email');
Route::get('password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ForgotPasswordController::class, 'reset'])
    ->middleware('throttle:password-reset')
    ->name('password.update');

// ==========================
// Public Routes
// ==========================
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/support', function () {
    return view('support');
})->name('support');

Route::get('/documentation', function () {
    return view('documentation');
})->name('documentation');

Route::get('/status', function () {
    return view('status');
})->name('status');

// Authentication Routes (with rate limiting)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer Registration Routes (with rate limiting)
Route::get('/register/customer', [AuthController::class, 'showCustomerRegistrationForm'])->name('register.customer.form');
Route::post('/register/customer', [AuthController::class, 'registerCustomer'])
    ->middleware('throttle:registration')
    ->name('register.customer');

// Public routes for quotation email links (with signed URLs and rate limiting)
Route::get('/quotations/{quotation}/view/{token}', [QuotationController::class, 'publicView'])
    ->middleware(['signed', 'throttle:20,60'])
    ->name('quotations.public.view');

Route::get('/quotations/{quotation}/accept/{token}', [QuotationController::class, 'publicAccept'])
    ->middleware(['signed', 'throttle:5,60'])
    ->name('quotations.public.accept');

// Conditional Certificate Routes
Route::get('/certificate/create', [CertificateController::class, 'create'])->name('certificates.create');
Route::post('/certificates/conditional/store', [CertificateController::class, 'storeConditional'])->name('certificates.conditional.store');
Route::post('/certificate/generate', [CertificateController::class, 'generate'])->name('certificates.generate');

// Acceptance Certificate Routes
Route::post('/certificates/acceptance/store', [CertificateController::class, 'storeAcceptance'])->name('certificates.acceptance.store');
Route::get('/certificates/acceptance/download/{id}', [CertificateController::class, 'downloadAcceptance'])->name('certificates.acceptance.download');
Route::get('/certificates/acceptance/preview/{id}', [CertificateController::class, 'previewAcceptance'])->name('certificates.acceptance.preview');

// ==========================
// Protected Routes (All routes below require authentication)
// ==========================
Route::middleware(['auth'])->group(function () {

    // ==========================
    // Common Dashboard Route
    // ==========================
    Route::get('/dashboard', function () {
        $user = Auth::user();

        return match($user->role) {
            'system_admin', 'accountmanager_admin', 'technical_admin', 'admin' =>
                redirect()->route('admin.dashboard'),
            'finance' =>
                redirect()->route('finance.dashboard'),
            'designer' =>
                redirect()->route('designer.dashboard'),
            'surveyor' =>
                redirect()->route('surveyor.dashboard'),
            'technician' =>
                redirect()->route('technician.dashboard'),
            'account_manager' =>
                redirect()->route('account-manager.dashboard'),
            'debt_manager' =>
                redirect()->route('finance.debt.dashboard'),
            'customer' =>
                redirect()->route('customer.customer-dashboard'),
            default =>
                redirect()->route('home'),
        };
    })->name('dashboard');

    // Registration routes group
    Route::prefix('register')->name('register.')->group(function () {
        Route::get('/admin', [App\Http\Controllers\Auth\RegisterController::class, 'showAdminRegistrationForm'])->name('admin.index');
        Route::post('/admin', [App\Http\Controllers\Auth\RegisterController::class, 'registerAdmin'])->name('admin.store');
    });

    // Notifications
    Route::get('/notifications/unread-count', function() {
        return response()->json([
            'count' => auth()->user()->unreadNotifications->count()
        ]);
    })->name('notifications.unread-count');

    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Documents Routes
    Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.index');
    Route::get('/documents/{designRequest}', [DocumentsController::class, 'show'])->name('documents.show');
    Route::get('/documents/{type}/{id}/details', [DocumentsController::class, 'details'])->name('documents.details');
    Route::post('/documents/upload', [DocumentsController::class, 'uploadDocuments'])->name('documents.upload');

    // Certificate download route
    Route::get('/certificates/{type}/{id}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // Statements
    Route::get('/statements', [PaymentStatementController::class, 'index'])->name('statements.index');
    Route::post('/statements/monthly', [PaymentStatementController::class, 'getByMonth'])->name('statements.monthly');
    Route::post('/statements/export', [PaymentStatementController::class, 'exportStatements'])->name('statements.export');
    Route::post('/statements/{id}/send', [PaymentStatementController::class, 'sendStatement'])->name('statements.send');
    Route::get('/statements/{id}/download', [PaymentStatementController::class, 'downloadStatement'])->name('statements.download');

    // ==========================
    // System Admin Routes (Full System Access)
    // ==========================
    Route::prefix('admin')->middleware(['can:isSystemAdmin'])->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SystemAdminController::class, 'dashboard'])->name('dashboard');

        // System Settings
        Route::get('/settings', [SystemAdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [SystemAdminController::class, 'updateSettings'])->name('settings.update');

        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Role & Permission Management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [SystemAdminController::class, 'roles'])->name('index');
            Route::put('/permissions', [SystemAdminController::class, 'updatePermissions'])->name('update-permissions');
        });

        // System Reports
        Route::get('/system-reports', [SystemAdminController::class, 'systemReports'])->name('system-reports');
        Route::get('/audit-logs', [SystemAdminController::class, 'auditLogs'])->name('audit-logs');
    });

    // ==========================
    // Marketing Admin Routes
    // ==========================
    Route::prefix('marketing-admin')->middleware(['can:isMarketingAdmin'])->name('marketing-admin.')->group(function () {
        Route::get('/dashboard', [MarketingAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/analytics', [MarketingAdminController::class, 'analytics'])->name('analytics');
        Route::get('/campaigns', [MarketingAdminController::class, 'campaigns'])->name('campaigns');
        Route::get('/customer-insights', [MarketingAdminController::class, 'customerInsights'])->name('customer-insights');
        Route::get('/reports', [MarketingAdminController::class, 'reports'])->name('reports');
        Route::get('/account-managers', [MarketingAdminController::class, 'accountManagers'])->name('account-managers');
        Route::get('/performance', [MarketingAdminController::class, 'performance'])->name('performance');
        Route::get('/targets', [MarketingAdminController::class, 'targets'])->name('targets');
        Route::get('/commissions', [MarketingAdminController::class, 'commissions'])->name('commissions');
        Route::get('/sales-pipeline', [MarketingAdminController::class, 'salesPipeline'])->name('sales-pipeline');
    });

    // ==========================
    // Technical Admin Routes
    // ==========================
    Route::prefix('technical-admin')->middleware(['can:isTechnicalAdmin'])->name('technical-admin.')->group(function () {
        Route::get('/dashboard', [TechnicalAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/network-monitor', [TechnicalAdminController::class, 'networkMonitor'])->name('network-monitor');
        Route::get('/infrastructure', [TechnicalAdminController::class, 'infrastructure'])->name('infrastructure');
        Route::get('/technical-reports', [TechnicalAdminController::class, 'technicalReports'])->name('technical-reports');
        Route::get('/system-health', [TechnicalAdminController::class, 'systemHealth'])->name('system-health');
    });

    // ==========================
    // ICT Engineer Routes
    // ==========================
    Route::prefix('ictengineer')->middleware(['can:ictengineerOrDesigner'])->name('ictengineer.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ICTEngineerController::class, 'dashboard'])->name('dashboard');

        // Design Requests
        Route::get('/requests', [ICTEngineerController::class, 'requests'])->name('requests.index');
        Route::get('/requests/{request}', [ICTEngineerController::class, 'showRequest'])->name('requests.show');
        Route::put('/requests/{request}', [ICTEngineerController::class, 'updateRequest'])->name('requests.update');
        Route::post('/requests/{id}/update-status', [ICTEngineerController::class, 'updateStatus'])->name('requests.update-status');

        // Network Management
        Route::get('/network-monitor', [ICTEngineerController::class, 'networkMonitor'])->name('network-monitor');

        // Tickets Management
        Route::get('/tickets', [ICTEngineerController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/{ticket}', [ICTEngineerController::class, 'showTicket'])->name('tickets.show');
        Route::put('/tickets/{ticket}/resolve', [ICTEngineerController::class, 'resolveTicket'])->name('tickets.resolve');

        // Quotation view
        Route::get('/quotations/{quotation}', [ICTEngineerController::class, 'showQuotation'])->name('quotations.show');

        // User Management
        Route::get('/users', [ICTEngineerController::class, 'users'])->name('users');

        // Infrastructure Management
        Route::get('/servers', [ICTEngineerController::class, 'servers'])->name('servers');
        Route::get('/equipment', [ICTEngineerController::class, 'equipment'])->name('equipment');

        // County Management
        Route::get('/county', [ICTEngineerController::class, 'county'])->name('county');

        // Reports & Analytics
        Route::get('/reports', [ICTEngineerController::class, 'reports'])->name('reports');
        Route::get('/reports/network', [ICTEngineerController::class, 'networkReport'])->name('reports.network');
        Route::get('/reports/performance', [ICTEngineerController::class, 'performanceReport'])->name('reports.performance');

        // System Management
        Route::get('/backups', [ICTEngineerController::class, 'backups'])->name('backups');
        Route::get('/security', [ICTEngineerController::class, 'security'])->name('security');

        // Settings
        Route::get('/settings', [ICTEngineerController::class, 'settings'])->name('settings');
        Route::put('/settings/profile', [ICTEngineerController::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/settings/password', [ICTEngineerController::class, 'updatePassword'])->name('settings.password.update');

        // Documentation & Help
        Route::get('/docs', [ICTEngineerController::class, 'docs'])->name('docs');
        Route::get('/helpdesk', [ICTEngineerController::class, 'helpdesk'])->name('helpdesk');

        // Certificate routes
        Route::prefix('certificates')->name('certificates.')->group(function () {
            // Conditional certificates
            Route::get('/conditional/{request}/create', [ICTEngineerCertificateController::class, 'createConditionalCertificate'])->name('conditional.create');
            Route::post('/conditional/{request}', [ICTEngineerCertificateController::class, 'storeConditionalCertificate'])->name('conditional.store');
            Route::get('/conditional/{certificate}', [ICTEngineerCertificateController::class, 'showConditionalCertificate'])->name('conditional.show');
            Route::get('/conditional/{certificate}/download', [ICTEngineerCertificateController::class, 'downloadConditionalCertificate'])->name('conditional.download');
            Route::get('/conditional/{certificate}/preview', [ICTEngineerCertificateController::class, 'previewConditionalCertificate'])->name('conditional.preview');

            // Acceptance certificates
            Route::get('/acceptance/{request}/create', [ICTEngineerCertificateController::class, 'createAcceptanceCertificate'])->name('acceptance.create');
            Route::post('/acceptance/{request}', [ICTEngineerCertificateController::class, 'storeAcceptanceCertificate'])->name('acceptance.store');
            Route::get('/acceptance/{certificate}', [ICTEngineerCertificateController::class, 'showAcceptanceCertificate'])->name('acceptance.show');
            Route::get('/acceptance/{certificate}/download', [ICTEngineerCertificateController::class, 'downloadAcceptanceCertificate'])->name('acceptance.download');
            Route::get('/acceptance/{certificate}/preview', [ICTEngineerCertificateController::class, 'previewAcceptanceCertificate'])->name('acceptance.preview');
        });

        // AJAX/API Routes
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/network-status', [ICTEngineerController::class, 'networkStatus'])->name('network.status');
            Route::get('/ticket-stats', [ICTEngineerController::class, 'ticketStats'])->name('ticket.stats');
            Route::get('/server-status', [ICTEngineerController::class, 'serverStatus'])->name('server.status');
        });
    });

    // ==========================
    // Legacy Admin Routes (Backward Compatibility)
    // ==========================
    Route::prefix('admin')->middleware(['can:isAdmin'])->name('admin.')->group(function () {
        // Dashboard & Settings
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');

        Route::prefix('account-managers')->name('account-managers.')->group(function () {
            Route::get('/{id}/customers', [App\Http\Controllers\AccountManagerController::class, 'getManagerCustomers'])
                ->name('customers');
        });

        // User Management
        Route::get('/users', [AdminController::class, 'usersIndex'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::put('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Customer Management
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [AdminController::class, 'customers'])->name('index');
            Route::get('/assign', [AdminController::class, 'showCustomerAssignment'])->name('assign');
            Route::post('/assign', [AdminController::class, 'storeCustomerAssignment'])->name('assign.store');
            Route::get('/assignments', [AdminController::class, 'customerAssignments'])->name('assignments');
            Route::delete('/assignments/{customer}', [AdminController::class, 'destroyAssignment'])->name('assignments.destroy');
            Route::get('/{id}/quotations', [AdminController::class, 'customerQuotations'])->name('quotations');
            Route::get('/{id}/requests', [AdminController::class, 'customerRequests'])->name('requests');
            Route::get('/{id}', [AdminController::class, 'showCustomer'])->name('show');
            Route::get('/{id}/profile', [AdminController::class, 'getProfile'])->name('profile');
            Route::post('/assign-manager', [AdminController::class, 'assignManager'])->name('assign-manager');
            Route::post('/{id}/disassign-manager', [AdminController::class, 'disassignManager'])->name('disassign-manager');
            Route::post('/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{customer}/approved-quotations', [LeaseController::class, 'getApprovedQuotations'])->name('approved-quotations');
        });

        // Account Managers Management
        Route::prefix('account-managers')->name('account-managers.')->group(function () {
            Route::get('/', [AccountManagerController::class, 'index'])->name('index');
            Route::get('/create', [AccountManagerController::class, 'create'])->name('create');
            Route::post('/', [AccountManagerController::class, 'store'])->name('store');
            Route::get('/{id}', [AccountManagerController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [AccountManagerController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AccountManagerController::class, 'update'])->name('update');
            Route::post('/{id}/toggle-status', [AccountManagerController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{id}/customers', [AccountManagerController::class, 'getManagerCustomers'])->name('customers');
            Route::get('/analytics/overview', [AccountManagerController::class, 'analytics'])->name('analytics');
        });

        // Quotations Management
        Route::prefix('quotations')->name('quotations.')->group(function () {
            Route::get('/', [QuotationController::class, 'index'])->name('index');
            Route::get('/create', [QuotationController::class, 'create'])->name('create');
            Route::post('/', [QuotationController::class, 'store'])->name('store');
            Route::get('/{quotation}', [QuotationController::class, 'show'])->name('show');
            Route::get('/{quotation}/edit', [QuotationController::class, 'edit'])->name('edit');
            Route::put('/{quotation}', [QuotationController::class, 'update'])->name('update');
            Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->name('destroy');
            Route::post('/{quotation}/approve', [QuotationController::class, 'approve'])->name('approve');
            Route::post('/{quotation}/reject', [QuotationController::class, 'reject'])->name('reject');
            Route::post('/{quotation}/send', [QuotationController::class, 'send'])->name('send');
            Route::post('/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('duplicate');
            Route::get('/{quotation}/download', [QuotationController::class, 'download'])->name('download');
            Route::get('/{quotation}/print', [QuotationController::class, 'print'])->name('print');
            Route::post('/{quotation}/send-email', [QuotationController::class, 'sendEmail'])->name('send-email');
            Route::get('/{quotation}/preview-email', [QuotationController::class, 'previewEmail'])->name('preview-email');
            Route::get('/{quotation}/customer-details', [QuotationController::class, 'getCustomerDetails'])->name('customer-details');
            Route::post('/{quotation}/approve-and-send', [QuotationController::class, 'approveAndSend'])->name('approve-and-send');
            Route::get('/{quotation}/download-pdf', [QuotationController::class, 'downloadPdf'])->name('download-pdf');
        });

        // Design Requests Management
        Route::prefix('design-requests')->name('design-requests.')->group(function () {
            Route::get('/', [DesignRequestController::class, 'index'])->name('index');
            Route::get('/create', [DesignRequestController::class, 'create'])->name('create');
            Route::post('/', [DesignRequestController::class, 'store'])->name('store');
            Route::get('/{designRequest}', [DesignRequestController::class, 'show'])->name('show');
            Route::get('/{designRequest}/edit', [DesignRequestController::class, 'edit'])->name('edit');
            Route::put('/{designRequest}', [DesignRequestController::class, 'update'])->name('update');
            Route::delete('/{designRequest}', [DesignRequestController::class, 'destroy'])->name('destroy');
            Route::get('/{designRequest}/assign-designer', [DesignRequestController::class, 'assignDesignerForm'])->name('assign-designer-form');
            Route::post('/{designRequest}/assign-designer', [DesignRequestController::class, 'assignDesigner'])->name('assign-designer');
            Route::delete('/{designRequest}/unassign-designer', [DesignRequestController::class, 'unassignDesigner'])->name('unassign-designer');
            Route::post('/{designRequest}/assign-surveyor', [DesignRequestController::class, 'assignSurveyor'])->name('assign-surveyor');
            Route::delete('/{designRequest}/unassign-surveyor', [DesignRequestController::class, 'unassignSurveyor'])->name('unassign-surveyor');
            Route::get('/{designRequest}/assign-surveyor', [DesignRequestController::class, 'assignSurveyorForm'])->name('assign-surveyor-form');
            Route::post('/{designRequest}/update-status', [DesignRequestController::class, 'updateStatus'])->name('update-status');
            Route::post('/{designRequest}/update-survey-status', [DesignRequestController::class, 'updateSurveyStatus'])->name('update-survey-status');
            Route::post('/{designRequest}/complete', [DesignRequestController::class, 'completeDesign'])->name('complete');
        });

        // Lease Management
        Route::prefix('leases')->name('leases.')->group(function () {
            Route::patch('/{lease}/approve', [LeaseController::class, 'approve'])->name('approve');
            Route::get('/{lease}/pdf', [LeaseController::class, 'generatePdf'])->name('pdf');
            Route::put('/leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('terminate');
            Route::put('/leases/{lease}/activate', [LeaseController::class, 'activate'])->name('activate');
            Route::post('/{lease}/generate-certificate', [LeaseController::class, 'generateAcceptancePdf'])->name('generate-certificate');
            Route::post('/{lease}/regenerate-certificate', [LeaseController::class, 'regenerateAcceptancePdf'])->name('regenerate-certificate');
            Route::delete('/{lease}/delete-certificate', [LeaseController::class, 'deleteAcceptanceCertificate'])->name('delete-certificate');
            Route::post('/{lease}/upload-test-report', [LeaseController::class, 'uploadTestReport'])->name('upload-test-report');
            Route::post('/{lease}/invoice', [LeaseController::class, 'generateInvoice'])->name('invoice.generate');
        });

        // Leases resource
        Route::resource('leases', LeaseController::class)->except(['create', 'store']);
        Route::get('/leases/create', [LeaseController::class, 'create'])->name('leases.create');
        Route::post('/leases', [LeaseController::class, 'store'])->name('leases.store');

        // Invoice Management
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/{invoice}', [AdminController::class, 'showInvoice'])->name('show');
            Route::get('/{invoice}/download', [AdminController::class, 'downloadInvoice'])->name('download');
            Route::post('/{invoice}/send', [AdminController::class, 'sendInvoice'])->name('send');
            Route::get('/create', function () {
                return redirect()->back()->with('info', 'Invoices module coming soon!');
            })->name('create');
            Route::post('/', function () {
                return redirect()->back()->with('info', 'Invoices module coming soon!');
            })->name('store');
        });

        // Payment Routes
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [AdminController::class, 'payments'])->name('index');
            Route::post('/', [AdminController::class, 'storePayment'])->name('store');
            Route::get('/create', [AdminController::class, 'createPayment'])->name('create');
            Route::get('/{payment}', [AdminController::class, 'showPayment'])->name('show');
            Route::get('/{payment}/edit', [AdminController::class, 'editPayment'])->name('edit');
            Route::put('/{payment}', [AdminController::class, 'updatePayment'])->name('update');
            Route::delete('/{payment}', [AdminController::class, 'deletePayment'])->name('destroy');
        });

        // System Management
        Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/survey-requests', [SurveyRequestController::class, 'index'])->name('survey-requests');

        // Contracts
        Route::prefix('contracts')->name('contracts.')->group(function () {
            Route::get('/', [AdminController::class, 'contracts'])->name('index');
            Route::get('/{contract}', [AdminController::class, 'showContract'])->name('show');
            Route::post('/{contract}/approve', [AdminController::class, 'approveContract'])->name('approve');
            Route::post('/{contract}/reject', [AdminController::class, 'rejectContract'])->name('reject');
            Route::post('/{contract}/send-to-customer', [AdminController::class, 'sendContractToCustomer'])->name('send-to-customer');
            Route::get('/{contract}/download', [AdminController::class, 'downloadContract'])->name('download');
            Route::get('/{contract}/edit', [AdminController::class, 'editContract'])->name('edit');
            Route::put('/{contract}', [AdminController::class, 'updateContract'])->name('update');
        });
    });

    // ==========================
    // Customer Routes (Profile Completion Check Applied)
    // ==========================
    Route::prefix('customer')->middleware(['can:isCustomer'])->name('customer.')->group(function () {
        // Dashboard
        Route::get('/customer-dashboard', [CustomerController::class, 'dashboard'])->name('customer-dashboard');
        Route::get('/', [CustomerController::class, 'index'])->name('index');

        // Public customer routes (no profile completion check)
        Route::get('/welcome', [App\Http\Controllers\Customer\CustomerDashboardController::class, 'welcome'])->name('welcome');

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/create', [CustomerProfileController::class, 'create'])->name('create');
            Route::post('/', [CustomerProfileController::class, 'store'])->name('store');
            Route::get('/edit', [CustomerProfileController::class, 'edit'])->name('edit');
            Route::put('/', [CustomerProfileController::class, 'update'])->name('update');
            Route::get('/show', [CustomerProfileController::class, 'show'])->name('show');
        });

        // Document routes (accessible without complete profile)
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [CustomerDocumentController::class, 'index'])->name('index');
            Route::get('/upload', [CustomerDocumentController::class, 'create'])->name('create');
            Route::post('/upload', [CustomerDocumentController::class, 'store'])->name('store');
            Route::delete('/{document}', [CustomerDocumentController::class, 'destroy'])->name('destroy');

            // Profile document routes
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('/upload', [CustomerDocumentController::class, 'createProfileDocument'])->name('create');
                Route::post('/upload', [CustomerDocumentController::class, 'storeProfileDocument'])->name('store');
                Route::get('/{document}', [CustomerDocumentController::class, 'showProfileDocument'])->name('show');
                Route::delete('/{document}', [CustomerDocumentController::class, 'destroyProfileDocument'])->name('destroy');
            });

            // Document request routes
            Route::prefix('requests')->name('requests.')->group(function () {
                Route::get('/', [CustomerDocumentController::class, 'requestDocsIndex'])->name('index');
                Route::post('/', [CustomerDocumentController::class, 'storeRequest'])->name('store');
            });

            // Lease document routes
            Route::get('/lease/{lease}', [CustomerDocumentController::class, 'showLeaseDocuments'])->name('lease.show');

            // Document download
            Route::get('/download/{document}', [CustomerDocumentController::class, 'download'])->name('download');
        });

        // Complete profile
        Route::get('/complete-profile', [AuthController::class, 'showCompleteCustomerProfileForm'])->name('complete-profile');
        Route::post('/complete-profile', [AuthController::class, 'completeCustomerProfile'])->name('complete-profile.submit');

        // Routes that require COMPLETE profile (all documents uploaded)
        Route::middleware([CheckProfileCompletion::class])->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Customer\CustomerDashboardController::class, 'dashboard'])->name('dashboard');

            // Customer main index
            Route::get('/', [CustomerController::class, 'index'])->name('index');

            // Certificate routes
            Route::prefix('certificates')->name('certificates.')->group(function () {
                // Conditional Certificates
                Route::get('/conditional', [CustomerCertificateController::class, 'indexConditional'])->name('conditional.index');
                Route::get('/conditional/{id}', [CustomerCertificateController::class, 'showConditional'])->name('conditional.show');
                Route::get('/conditional/{id}/download', [CustomerCertificateController::class, 'downloadConditional'])->name('conditional.download');
                Route::get('/conditional/{id}/preview', [CustomerCertificateController::class, 'previewConditional'])->name('conditional.preview');

                // Acceptance Certificates
                Route::get('/acceptance', [CustomerCertificateController::class, 'indexAcceptance'])->name('acceptance.index');
                Route::get('/acceptance/{id}', [CustomerCertificateController::class, 'showAcceptance'])->name('acceptance.show');
                Route::get('/acceptance/{id}/download', [CustomerCertificateController::class, 'downloadAcceptance'])->name('acceptance.download');
                Route::get('/acceptance/{id}/preview', [CustomerCertificateController::class, 'previewAcceptance'])->name('acceptance.preview');
            });

            // Quotations Routes
            Route::prefix('quotations')->name('quotations.')->controller(QuotationController::class)->group(function () {
                Route::get('/', 'customerIndex')->name('index');
                Route::get('/{quotation}', 'customerShow')->name('show');
                Route::post('/{quotation}/accept', 'accept')->name('accept');
                Route::post('/{quotation}/decline', 'decline')->name('decline');
                Route::post('/{quotation}/request-revision', 'requestRevision')->name('request-revision');
                Route::patch('/{quotation}/approve', 'customerApprove')->name('approve');
                Route::patch('/{quotation}/reject', 'customerReject')->name('reject');
                Route::get('/{quotation}/download', 'download')->name('download');
            });

            // Leases Routes
            Route::prefix('leases')->name('leases.')->controller(CustomerController::class)->group(function () {
                Route::get('/', 'leases')->name('index');
                Route::get('/{lease}', 'showLease')->name('show');
            });

            // Billings Routes
            Route::prefix('billings')->name('billings.')->controller(BillingController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
                Route::post('/{id}/pay', 'pay')->name('pay');
                Route::get('/{id}/download', 'download')->name('download');
                Route::get('/{id}/preview', 'preview')->name('preview');
                Route::get('/leases/{leaseId}/billing-history', 'leaseHistory')->name('lease-history');
                Route::post('/export', 'export')->name('export');
            });

            // Print billing
            Route::get('/billing/{id}/print', [FinanceController::class, 'printBilling'])->name('billing.print');

            // Invoices Routes
            Route::prefix('invoices')->name('invoices.')->controller(CustomerController::class)->group(function () {
                Route::get('/', 'invoices')->name('index');
                Route::get('/{id}', 'showInvoice')->name('show');
                Route::get('/{id}/download', 'downloadInvoice')->name('download');
            });

            // Design Requests Routes
            Route::prefix('design-requests')->name('design-requests.')->controller(CustomerDesignRequestController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{designRequest}', 'show')->name('show');
            });

            // Contracts Routes
            Route::prefix('contracts')->name('contracts.')->controller(CustomerContractController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{contract}', 'show')->name('show');
                Route::post('/{contract}/approve', 'approve')->name('approve');
                Route::get('/{contract}/download', 'downloadPdf')->name('download');
            });

            // Payments Routes
            Route::prefix('payments')->name('payments.')->controller(PaymentController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create/{lease}', 'create')->name('create');
                Route::post('/store/{billingId}', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
            });

            // Support Tickets Routes
            Route::prefix('support')->name('support.')->controller(App\Http\Controllers\SupportController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{ticket}', 'show')->name('show');
            });

            // Tickets (legacy)
            Route::get('/tickets', [CustomerController::class, 'tickets'])->name('tickets');
        });
    });

    // Add these routes to your web.php file
    Route::get('/kenya-fibre/networks', [App\Http\Controllers\FiberNetworkController::class, 'index'])
        ->name('fiber.networks.index');

    Route::get('/kenya-fibre/networks/{id}', [App\Http\Controllers\FiberNetworkController::class, 'show'])
        ->name('fiber.networks.show');

    Route::post('/kenya-fibre/networks/{id}/status', [App\Http\Controllers\FiberNetworkController::class, 'updateStatus'])
        ->name('fiber.networks.status');

    Route::get('/kenya-fibre/dashboard', [App\Http\Controllers\KenyaFibreDashboardController::class, 'dashboard'])
        ->name('kenya-fibre.dashboard');

    // Customer quotations view (outside the main customer group for policy-based access)
    Route::get('/customer/quotations/{quotation}', [QuotationController::class, 'show'])
        ->name('customer.quotations.show')
        ->middleware('can:view,quotation');

    // ==========================
    // Finance Routes
    // ==========================
    Route::prefix('finance')->middleware(['can:financeOrDebtManager'])->name('finance.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/financial-reports', [FinanceController::class, 'financialReports'])->name('financial-reports');
        Route::get('/reports', [FinanceController::class, 'reports'])->name('reports');
        Route::get('/transactions', [FinanceController::class, 'transactions'])->name('transactions');
        Route::get('/billing/{id}/print', [FinanceController::class, 'printBilling'])->name('billing.print');

        // Transactions Management
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [FinanceController::class, 'transactions'])->name('index');
            Route::get('/create', [FinanceController::class, 'createTransaction'])->name('create');
            Route::post('/', [FinanceController::class, 'storeTransaction'])->name('store');
            Route::get('/{id}', [FinanceController::class, 'showTransaction'])->name('show');
            Route::get('/{id}/edit', [FinanceController::class, 'editTransaction'])->name('edit');
            Route::put('/{id}', [FinanceController::class, 'updateTransaction'])->name('update');
            Route::delete('/{id}', [FinanceController::class, 'destroyTransaction'])->name('destroy');
            Route::post('/{id}/complete', [FinanceController::class, 'completeTransaction'])->name('complete');
        });

        // SAP Assignment
        Route::prefix('sap-assignment')->name('sap-assignment.')->group(function () {
            Route::get('/', [CustomerSapController::class, 'index'])->name('index');
            Route::get('/{id}/edit', [CustomerSapController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CustomerSapController::class, 'update'])->name('update');
            Route::get('/bulk', [CustomerSapController::class, 'bulk'])->name('bulk');
            Route::post('/bulk', [CustomerSapController::class, 'bulkStore'])->name('bulk-store');
        });

        // Payment Followups
        Route::prefix('payments')->name('payments.')->controller(PaymentFollowupController::class)->group(function () {
            Route::get('/', 'index')->name('followups');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}/remind', 'remind')->name('remind');
            Route::post('/{id}/paid', 'markPaid')->name('paid');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Debt Management Routes
        Route::prefix('debt')->name('debt.')->group(function () {
            Route::get('dashboard', [DebtManagementController::class, 'dashboard'])->name('dashboard');
            Route::get('overdue-invoices', [DebtManagementController::class, 'overdueInvoices'])->name('overdue-invoices');
            Route::get('aging-report', [DebtManagementController::class, 'agingReport'])->name('aging.report');
            Route::get('collection-report', [DebtManagementController::class, 'collectionReport'])->name('collection.report');
            Route::get('customers', [DebtManagementController::class, 'customers'])->name('customers');
            Route::get('customer/{id}', [DebtManagementController::class, 'customerDebt'])->name('customer.debt');
            Route::get('/invoice/{id}/details', [DebtManagementController::class, 'invoiceDetails'])->name('invoice.details');
            Route::get('/reports/aging', [DebtManagementController::class, 'agingReport'])->name('reports.aging');
            Route::get('/collection', [DebtManagementController::class, 'collectionReport'])->name('collection');
            Route::put('/payments/{payment}', [DebtManagementController::class, 'paymentUpdate'])->name('payments.update');

            // Payment Management Routes
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [DebtManagementController::class, 'paymentIndex'])->name('index');
                Route::get('/{payment}/edit', [DebtManagementController::class, 'paymentEdit'])->name('edit');
                Route::put('/{payment}', [DebtManagementController::class, 'paymentUpdate'])->name('update');
                Route::post('/{payment}/verify', [DebtManagementController::class, 'paymentVerify'])->name('verify');
                Route::get('/search', [DebtManagementController::class, 'paymentSearch'])->name('search');
            });

            // Actions
            Route::post('send-reminders', [DebtManagementController::class, 'sendReminders'])->name('send.reminders');
            Route::post('create-payment-plan', [DebtManagementController::class, 'createPaymentPlan'])->name('create.payment.plan');
            Route::post('update-status', [DebtManagementController::class, 'updateStatus'])->name('update.status');
            Route::post('write-off', [DebtManagementController::class, 'writeOff'])->name('write.off');
            Route::post('invoice/{id}/send-reminder', [DebtManagementController::class, 'sendReminder'])->name('invoice.send-reminder');
            Route::post('invoice/{id}/create-payment-plan', [DebtManagementController::class, 'createPaymentPlan'])->name('invoice.create-payment-plan');
            Route::get('invoices/payment-plan-eligible', [DebtManagementController::class, 'getInvoicesForPaymentPlan'])->name('invoices.payment-plan-eligible');
            Route::get('payment-plan/{id}', [DebtManagementController::class, 'getPaymentPlanDetails'])->name('payment-plan.details');
            Route::post('payment-plan/{id}/cancel', [DebtManagementController::class, 'cancelPaymentPlan'])->name('payment-plan.cancel');
            Route::post('installment/{id}/payment', [DebtManagementController::class, 'recordInstallmentPayment'])->name('installment.payment');
        });

        // Billing Routes
        Route::controller(FinanceBillingController::class)->prefix('billing')->name('billing.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/create-single', 'createSingle')->name('createSingle');
            Route::post('/store-single', 'storeSingle')->name('storeSingle');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::get('/{id}/download', 'download')->name('download');
            Route::get('/{id}/preview', 'preview')->name('preview');
            Route::post('/{id}/submit-kra', 'submitKra')->name('submit-kra');
            Route::post('/{id}/submit-tevin', 'submitToTevinKra')->name('submit-tevin');
            Route::get('/{id}/kra-status', 'checkKraStatus')->name('kra-status');
            Route::post('/{id}/retry-kra', 'retryKraSubmission')->name('retry-kra');
            Route::post('/{id}/mark-paid', 'markPaid')->name('mark-paid');
            Route::post('/{id}/send-reminder', 'sendReminder')->name('send-reminder');
            Route::post('/{id}/duplicate', 'duplicate')->name('duplicate');
        });

        // Contract Routes
        Route::controller(FinanceBillingController::class)->prefix('contracts')->name('contracts.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Bulk Operations
        Route::post('/billing/run-process', [FinanceBillingController::class, 'runProcess'])->name('billing.run-process');

        // Customer KRA PIN Management
        Route::post('/customer/{userId}/update-kra-pin', [FinanceBillingController::class, 'updateKraPin'])->name('customer.update-kra-pin');

        // Automated billing
        Route::get('/auto-billing', [FinanceController::class, 'autoBilling'])->name('auto-billing');

        // Financial Parameters
        Route::prefix('financial-parameters')->name('financial-parameters.')->group(function () {
            Route::get('/', [FinancialParameterController::class, 'index'])->name('index');
            Route::get('/create', [FinancialParameterController::class, 'create'])->name('create');
            Route::post('/', [FinancialParameterController::class, 'store'])->name('store');
            Route::get('/{financialParameter}/edit', [FinancialParameterController::class, 'edit'])->name('edit');
            Route::put('/{financialParameter}', [FinancialParameterController::class, 'update'])->name('update');
            Route::delete('/{financialParameter}', [FinancialParameterController::class, 'destroy'])->name('destroy');
            Route::get('/api/current-rates', [FinancialParameterController::class, 'getCurrentRates'])->name('current-rates');
        });
    });

    // Additional debt routes outside the main finance group
    Route::prefix('finance/debt')->name('finance.debt.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DebtManagementController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports/currency', [DebtManagementController::class, 'currencyReport'])->name('currency-report');
        Route::get('/overdue-invoices', [DebtManagementController::class, 'overdueInvoices'])->name('overdue-invoices');
        Route::get('/customer/{id}', [DebtManagementController::class, 'customerDebt'])->name('customer');
    });

    // Customer Portal Routes
    Route::prefix('customer')->name('customer.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [StatementController::class, 'index'])->name('dashboard');
        Route::get('/statements', [StatementController::class, 'statements'])->name('statements');
        Route::get('/statements/create', [StatementController::class, 'create'])->name('statements.create');
        Route::post('/statements/generate', [StatementController::class, 'generate'])->name('statements.generate');
        Route::post('/statements/download', [StatementController::class, 'download'])->name('statements.download');
        Route::get('/statements/{id}', [StatementController::class, 'show'])->name('statements.show');
    });

    // AI Analytics Routes - Single definition
Route::prefix('finance/ai-analytics')->name('finance.ai.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AiAnalyticsController::class, 'dashboard'])->name('dashboard');
    Route::get('/customer/{id}', [AiAnalyticsController::class, 'customerIntelligence'])->name('customer');
    Route::get('/predictive', [AiAnalyticsController::class, 'predictiveAnalytics'])->name('predictive');
    Route::get('/recommendations', [AiAnalyticsController::class, 'recommendations'])->name('recommendations');
    Route::match(['get', 'post'], '/report', [AiAnalyticsController::class, 'generateReport'])->name('report');
});

// Financial Analytics Routes
    Route::prefix('finance/financial-analytics')->name('finance.financial-analytics.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [FinancialAnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::match(['get', 'post'], '/report', [FinancialAnalyticsController::class, 'generateReport'])->name('report');
        Route::get('/kpis', [FinancialAnalyticsController::class, 'kpis'])->name('kpis');
        Route::get('/trends', [FinancialAnalyticsController::class, 'trends'])->name('trends');
        Route::get('/trends/data', [FinancialAnalyticsController::class, 'getTrendData'])->name('trends.data');
        Route::get('/benchmarking', [FinancialAnalyticsController::class, 'benchmarking'])->name('benchmarking');
        Route::get('/forecasting', [FinancialAnalyticsController::class, 'forecasting'])->name('forecasting');
        Route::get('/export', [FinancialAnalyticsController::class, 'export'])->name('export');
    });

    // ==========================
    // Designer Routes
    // ==========================
    Route::prefix('designer')->middleware(['can:accountManagerOrDesigner'])->name('designer.')->group(function () {
        Route::get('/dashboard', [DesignerController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [DesignerController::class, 'profile'])->name('profile');
        Route::post('/design-requests/{designRequest}/update-status', [DesignerController::class, 'updateStatus'])->name('design-requests.update-status');

        // Certificate Routes
        Route::post('/requests/{id}/certificate/conditional', [DesignerController::class, 'storeConditionalCertificate'])->name('requests.certificate.conditional');
        Route::post('/requests/{id}/certificate/acceptance', [DesignerController::class, 'storeAcceptanceCertificate'])->name('requests.certificate.acceptance');
        Route::get('/requests/assign-ict', [DesignerController::class, 'assignICTIndex'])->name('requests.assignictindex');
        Route::post('/requests/assign-ict', [DesignerController::class, 'assignICTRequest'])->name('requests.assignict');
        Route::get('/api/ict-engineer/{id}', [DesignerController::class, 'getICTEngineerDetails']);

        // Design Requests
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [DesignerController::class, 'requests'])->name('index');
            Route::get('/{designRequest}', [DesignerController::class, 'showRequest'])->name('show');
            Route::put('/{designRequest}', [DesignerController::class, 'updateDesign'])->name('update');
            Route::put('/{designRequest}/specifications', [DesignItemController::class, 'updateDesignSpecifications'])->name('update-specifications');
            Route::put('/{designRequest}/update-status', [DesignerController::class, 'updateRequestStatus'])->name('update-status');
            Route::post('/{designRequest}/upload-design', [DesignerController::class, 'uploadDesign'])->name('upload-design');
        });

        Route::get('/colocation/create', [DesignerController::class, 'createColocation'])->name('colocation.create');

        // Darkfire Items Management
        Route::get('/darkfire-items', [DarkfireController::class, 'index'])->name('darkfire-items');
        Route::get('/darkfire-items/{table}/create', [DarkfireController::class, 'create'])->name('darkfire-items.create');
        Route::post('/darkfire-items/{table}', [DarkfireController::class, 'store'])->name('darkfire-items.store');
        Route::get('/darkfire-items/{table}/{id}/edit', [DarkfireController::class, 'edit'])->name('darkfire-items.edit');
        Route::put('/darkfire-items/{table}/{id}', [DarkfireController::class, 'update'])->name('darkfire-items.update');
        Route::delete('/darkfire-items/{table}/{id}', [DarkfireController::class, 'destroy'])->name('darkfire-items.destroy');
        Route::patch('/darkfire-items/{table}/{id}/toggle', [DarkfireController::class, 'toggleAvailability'])->name('darkfire-items.toggle');

        // Quotations
        Route::prefix('quotations')->name('quotations.')->group(function () {
            Route::get('/', [QuotationController::class, 'index'])->name('index');
            Route::get('/create', [QuotationController::class, 'create'])->name('create');
            Route::post('/', [QuotationController::class, 'store'])->name('store');
            Route::get('/{quotation}', [QuotationController::class, 'show'])->name('show');
            Route::get('/{quotation}/edit', [QuotationController::class, 'edit'])->name('edit');
            Route::put('/{quotation}', [QuotationController::class, 'update'])->name('update');
            Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->name('destroy');
            Route::post('/{quotation}/send-for-approval', [QuotationController::class, 'sendForApproval'])->name('send-for-approval');
            Route::get('/{quotation}/preview', [QuotationController::class, 'preview'])->name('preview');
        });

        // Design Items
        Route::post('/design-items', [DesignItemController::class, 'storeDesignItems'])->name('design-items.store');
        Route::delete('/design-items/{designItem}', [DesignItemController::class, 'destroyDesignItem'])->name('design-items.destroy');

        // Colocation
        Route::post('/colocation', [DesignerController::class, 'storeColocation'])->name('colocation.store');
        Route::delete('/colocation/{colocationService}', [DesignerController::class, 'destroyColocation'])->name('colocation.destroy');
    });

    // ==========================
    // Surveyor Routes
    // ==========================
    Route::prefix('surveyor')->middleware(['can:isSurveyor'])->name('surveyor.')->group(function () {
        Route::get('/dashboard', [SurveyorController::class, 'dashboard'])->name('dashboard');

        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/', [SurveyorController::class, 'assignments'])->name('index');
            Route::get('/{id}', [SurveyorController::class, 'showAssignment'])->name('show');
            Route::post('/{id}/complete', [SurveyorController::class, 'completeAssignment'])->name('complete');
            Route::post('/{id}/update-status', [SurveyorController::class, 'updateSurveyStatus'])->name('update-status');
        });

        Route::prefix('routes')->name('routes.')->group(function () {
            Route::get('/', [SurveyorController::class, 'routes'])->name('index');
            Route::post('/', [SurveyorController::class, 'storeRoute'])->name('store');
            Route::get('/{id}', [SurveyorController::class, 'showRoute'])->name('show');
            Route::get('/{id}/segments/create', [SurveyorController::class, 'createSegment'])->name('route-segments.create');
            Route::post('/{id}/segments', [SurveyorController::class, 'storeSegment'])->name('route-segments.store');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/create', [SurveyorController::class, 'createReport'])->name('create');
            Route::post('/', [SurveyorController::class, 'storeReport'])->name('store');
            Route::get('/', [SurveyorController::class, 'reports'])->name('index');
        });

        Route::get('/availability', [SurveyorController::class, 'availability'])->name('availability');
        Route::post('/availability', [SurveyorController::class, 'updateAvailability'])->name('availability.update');
        Route::get('/profile', [SurveyorController::class, 'profile'])->name('profile');
        Route::put('/profile', [SurveyorController::class, 'updateProfile'])->name('profile.update');
        Route::post('/status', [SurveyorController::class, 'updateStatus'])->name('status.update');
        Route::get('/design-requests/{id}', [SurveyorController::class, 'showDesignRequest'])->name('design-requests.show');
    });

    // ==========================
    // Technician Routes
    // ==========================
    Route::prefix('technician')->middleware(['can:isTechnician'])->name('technician.')->group(function () {
        Route::get('/dashboard', [TechnicianController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [TechnicianController::class, 'profile'])->name('profile');
        Route::put('/profile', [TechnicianController::class, 'updateProfile'])->name('profile.update');

        Route::prefix('work-orders')->name('work-orders.')->group(function () {
            Route::get('/', [TechnicianController::class, 'workOrders'])->name('index');
            Route::get('/{id}', [TechnicianController::class, 'showWorkOrder'])->name('show');
            Route::put('/{id}/status', [TechnicianController::class, 'updateWorkOrderStatus'])->name('update-status');
            Route::put('/{id}/complete', [TechnicianController::class, 'completeWorkOrder'])->name('complete');
            Route::put('/{id}/start', [TechnicianController::class, 'startWorkOrder'])->name('start');
        });

        Route::prefix('equipment')->name('equipment.')->group(function () {
            Route::get('/', [TechnicianController::class, 'equipment'])->name('index');
            Route::get('/{id}', [TechnicianController::class, 'showEquipment'])->name('show');
            Route::post('/{id}/checkout', [TechnicianController::class, 'checkoutEquipment'])->name('checkout');
            Route::post('/{id}/return', [TechnicianController::class, 'returnEquipment'])->name('return');
            Route::put('/{id}/status', [TechnicianController::class, 'updateEquipmentStatus'])->name('update-status');
        });

        Route::get('/maintenance-requests', [TechnicianController::class, 'maintenanceRequests'])->name('maintenance-requests');
        Route::get('/maintenance-requests/{id}', [TechnicianController::class, 'showMaintenanceRequest'])->name('maintenance-requests.show');
    });

    // ==========================
    // Maintenance Routes
    // ==========================
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/dashboard', [MaintenanceController::class, 'dashboard'])->name('dashboard')->middleware(['can:view-maintenance']);

        Route::prefix('work-orders')->name('work-orders.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'workOrders'])->name('index');
            Route::get('/create', [MaintenanceController::class, 'createWorkOrder'])->name('create');
            Route::post('/', [MaintenanceController::class, 'storeWorkOrder'])->name('store');
            Route::get('/{id}', [MaintenanceController::class, 'showWorkOrder'])->name('show');
            Route::put('/{id}/status', [MaintenanceController::class, 'updateWorkOrderStatus'])->name('update-status')->middleware(['can:update-work-order-status']);
            Route::put('/{id}/complete', [MaintenanceController::class, 'completeWorkOrder'])->name('complete')->middleware(['can:complete-work-orders']);
        });

        Route::prefix('equipment')->name('equipment.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'equipment'])->name('index');
            Route::get('/create', [MaintenanceController::class, 'createEquipment'])->name('create');
            Route::post('/', [MaintenanceController::class, 'storeEquipment'])->name('store');
            Route::get('/{id}', [MaintenanceController::class, 'showEquipment'])->name('show');
            Route::get('/{id}/edit', [MaintenanceController::class, 'editEquipment'])->name('edit');
            Route::put('/{id}', [MaintenanceController::class, 'updateEquipment'])->name('update');
        });

        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
            Route::post('/', [MaintenanceController::class, 'store'])->name('store');
            Route::get('/{id}', [MaintenanceController::class, 'show'])->name('show');
        });

        Route::get('/api/stats', [MaintenanceController::class, 'getStats'])->name('api.stats');
        Route::get('/api/open-requests', [MaintenanceController::class, 'getOpenRequests'])->name('api.open-requests');
        Route::get('/reports', [MaintenanceController::class, 'reports'])->name('reports');
    });

    // ==========================
    // Account Manager Routes
    // ==========================
    Route::prefix('account-manager')->middleware(['can:isAccountManager'])->name('account-manager.')->group(function () {
        Route::get('/dashboard', [AccountManagerController::class, 'dashboard'])->name('dashboard');

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [AccountManagerController::class, 'myCustomers'])->name('index');
            Route::get('/{customer}', [AccountManagerController::class, 'customerDetail'])->name('show');
            Route::get('/{customer}/approved-quotations', [LeaseController::class, 'getApprovedQuotations'])->name('approved-quotations');
        });

        Route::get('/reports/performance', [AccountManagerController::class, 'performanceReport'])->name('reports.performance');

        Route::prefix('users/{user}')->name('documents.')->group(function () {
            Route::get('/approve', [DocumentApprovalController::class, 'showCustomerDocuments'])->name('approve');
            Route::post('/bulk-approve', [DocumentApprovalController::class, 'bulkApproveCustomerDocuments'])->name('bulk-approve');
        });

        Route::prefix('customers/{customer}/documents/manage')->name('customers.documents.')->group(function () {
            Route::get('/', [AccountManagerManualDocumentController::class, 'index'])->name('manage');
            Route::get('/upload', [AccountManagerManualDocumentController::class, 'create'])->name('upload');
            Route::post('/upload', [AccountManagerManualDocumentController::class, 'store'])->name('store');
            Route::get('/{document}/download', [AccountManagerManualDocumentController::class, 'download'])->name('download');
            Route::delete('/{document}', [AccountManagerManualDocumentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('documents')->name('documents.')->group(function () {
            Route::post('/{document}/approve', [DocumentApprovalController::class, 'approveDocument'])->name('approve-single');
            Route::post('/{document}/reject', [DocumentApprovalController::class, 'rejectDocument'])->name('reject');
            Route::get('/{document}/view', [CustomerDocumentController::class, 'accountManagerView'])->name('view');
            Route::get('/{document}/download', [CustomerDocumentController::class, 'accountManagerDownload'])->name('download');
        });

        Route::post('/design-requests/{designRequest}/update-status', [AccountManagerController::class, 'updateStatus'])->name('design-requests.update-status');

        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [SupportTicketController::class, 'index'])->name('index');
            Route::get('/create', [SupportTicketController::class, 'create'])->name('create');
            Route::post('/', [SupportTicketController::class, 'store'])->name('store');
            Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('show');
            Route::patch('/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('update-status');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentFollowupController::class, 'index'])->name('index');
            Route::get('/create', [PaymentFollowupController::class, 'create'])->name('create');
            Route::post('/', [PaymentFollowupController::class, 'store'])->name('store');
            Route::post('/{followup}/remind', [PaymentFollowupController::class, 'markReminded'])->name('remind');
            Route::post('/{followup}/paid', [PaymentFollowupController::class, 'markPaid'])->name('paid');
        });

        // Design Requests Management
        Route::prefix('design-requests')->name('design-requests.')->group(function () {
            Route::get('/', [DesignRequestController::class, 'index'])->name('index');
            Route::get('/create', [DesignRequestController::class, 'create'])->name('create');
            Route::post('/', [DesignRequestController::class, 'store'])->name('store');
            Route::get('/{designRequest}', [DesignRequestController::class, 'show'])->name('show');
            Route::get('/{designRequest}/edit', [DesignRequestController::class, 'edit'])->name('edit');
            Route::put('/{designRequest}', [DesignRequestController::class, 'update'])->name('update');
            Route::delete('/{designRequest}', [DesignRequestController::class, 'destroy'])->name('destroy');
            Route::get('/{designRequest}/assign-designer', [DesignRequestController::class, 'assignDesignerForm'])->name('assign-designer');
            Route::post('/{designRequest}/assign-designer', [DesignRequestController::class, 'assignDesigner'])->name('assign-designer.store');
            Route::delete('/{designRequest}/unassign-designer', [DesignRequestController::class, 'unassignDesigner'])->name('unassign-designer');
            Route::get('/{designRequest}/assign-surveyor', [DesignRequestController::class, 'assignSurveyorForm'])->name('assign-surveyor');
            Route::post('/{designRequest}/assign-surveyor', [DesignRequestController::class, 'assignSurveyor'])->name('assign-surveyor.store');
            Route::delete('/{designRequest}/unassign-surveyor', [DesignRequestController::class, 'unassignSurveyor'])->name('unassign-surveyor');
            Route::post('/{designRequest}/update-status', [DesignRequestController::class, 'updateStatus'])->name('update-status');
            Route::post('/{designRequest}/update-survey-status', [DesignRequestController::class, 'updateSurveyStatus'])->name('update-survey-status');
            Route::post('/{designRequest}/complete', [DesignRequestController::class, 'completeDesign'])->name('complete');
        });

        // Quotations Management
        Route::prefix('quotations')->name('quotations.')->group(function () {
            Route::get('/', [QuotationController::class, 'index'])->name('index');
            Route::get('/create', [QuotationController::class, 'create'])->name('create');
            Route::post('/', [QuotationController::class, 'store'])->name('store');
            Route::get('/{quotation}', [QuotationController::class, 'show'])->name('show');
            Route::get('/{quotation}/edit', [QuotationController::class, 'edit'])->name('edit');
            Route::put('/{quotation}', [QuotationController::class, 'update'])->name('update');
            Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->name('destroy');
            Route::post('/{quotation}/approve', [QuotationController::class, 'approve'])->name('approve');
            Route::post('/{quotation}/reject', [QuotationController::class, 'reject'])->name('reject');
            Route::post('/{quotation}/send', [QuotationController::class, 'send'])->name('send');
            Route::post('/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('duplicate');
            Route::get('/{quotation}/download', [QuotationController::class, 'download'])->name('download');
            Route::get('/{quotation}/print', [QuotationController::class, 'print'])->name('print');
        });

        // Leases
        Route::patch('/{lease}/approve', [LeaseController::class, 'approve'])->name('leases.approve');
        Route::get('/leases', [LeaseController::class, 'indexForAccountManager'])->name('leases.index');
        Route::get('/leases/create', [LeaseController::class, 'createForAccountManager'])->name('leases.create');
        Route::post('/leases', [LeaseController::class, 'storeForAccountManager'])->name('leases.store');
        Route::get('/leases/{lease}', [LeaseController::class, 'showForAccountManager'])->name('leases.show');
        Route::get('/leases/{lease}/edit', [LeaseController::class, 'editForAccountManager'])->name('leases.edit');
        Route::put('/leases/{lease}', [LeaseController::class, 'updateForAccountManager'])->name('leases.update');
        Route::delete('/leases/{lease}', [LeaseController::class, 'destroyForAccountManager'])->name('leases.destroy');
        Route::get('/{lease}/pdf', [LeaseController::class, 'generatePdf'])->name('leases.pdf');
        Route::get('/customers/{customer}/approved-quotations', [LeaseController::class, 'getApprovedQuotations'])->name('customers.approved-quotations');
    });

    // ==========================
    // Finance Leases Routes
    // ==========================
    Route::prefix('finance')->name('leases.finance.')->group(function () {
        Route::get('/leases', [LeaseController::class, 'financeIndex'])->name('index');
        Route::get('/leases/{id}', [LeaseController::class, 'financeShow'])->name('show');
        Route::get('/leases/{id}/edit', [LeaseController::class, 'financeEdit'])->name('edit');
        Route::put('/leases/{id}', [LeaseController::class, 'financeUpdate'])->name('update');
        Route::post('/leases/{id}/mark-billed', [LeaseController::class, 'markBilled'])->name('mark-billed');
        Route::post('/leases/{id}/add-note', [LeaseController::class, 'addNote'])->name('add-note');
        Route::put('/leases/{id}/update-currency', [LeaseController::class, 'updateCurrency'])->name('update-currency');
        Route::put('/leases/{id}/update-billing', [LeaseController::class, 'updateBilling'])->name('update-billing');
        Route::get('/leases/export/finance', [LeaseController::class, 'exportFinance'])->name('export.finance');
    });

    // Data conversion Routes
    // Custom routes FIRST
    Route::get('/conversion-data/summary-view', [ConversionDataController::class, 'customers'])
        ->name('conversion-data.summary-view');

    Route::get('/conversion-data/summary', [ConversionDataController::class, 'summary'])
        ->name('conversion-data.summary');

    Route::get('/conversion-data/summary/report', [ConversionDataController::class, 'summaryReport'])
        ->name('conversion-data.summary-report');

    Route::get('/conversion-data/summary/pdf', [ConversionDataController::class, 'downloadSummaryPdf'])
        ->name('conversion-data.summary.pdf');

    // Export routes
    Route::get('/conversion-data/export/excel', [ConversionDataController::class, 'exportExcel'])
        ->name('conversion-data.export.excel');
    Route::get('/conversion-data/export/csv', [ConversionDataController::class, 'exportCsv'])
        ->name('conversion-data.export.csv');
    Route::get('/conversion-data/export/pdf', [ConversionDataController::class, 'exportPdf'])
        ->name('conversion-data.export.pdf');
    Route::get('/conversion-data/export/{format}', [ConversionDataController::class, 'export'])
        ->name('conversion-data.export');

    // Bulk operations
    Route::post('/conversion-data/bulk-delete', [ConversionDataController::class, 'bulkDelete'])
        ->name('conversion-data.bulk-delete');

    // Duplicate route
    Route::post('/conversion-data/{id}/duplicate', [ConversionDataController::class, 'duplicate'])
        ->name('conversion-data.duplicate');

    // Resource route LAST
    Route::resource('conversion-data', ConversionDataController::class);

    // ==========================
    // Commercial Routes
    // ==========================
    Route::get('/commercial-routes', [CommercialRouteController::class, 'index']);
    Route::get('/commercial-routes/summary', [CommercialRouteController::class, 'summary']);
    Route::get('/commercial-routes/capex', [CommercialRouteController::class, 'capexRoutes']);

    // ==========================
    // API Routes for AJAX (should be minimal in web.php)
    // ==========================
    Route::prefix('api')->group(function () {
        Route::prefix('quotations')->group(function () {
            Route::get('/status-counts', [QuotationController::class, 'statusCounts']);
            Route::get('/recent', [QuotationController::class, 'recentQuotations']);
            Route::post('/{quotation}/quick-approve', [QuotationController::class, 'quickApprove']);
            Route::post('/{quotation}/add-comment', [QuotationController::class, 'addComment']);
            Route::get('/{quotation}/activity', [QuotationController::class, 'getActivity']);
        });
    });

    // ==========================
    // Design Items API Routes
    // ==========================
    Route::prefix('api/design-items')->group(function () {
        Route::get('/', [DesignItemController::class, 'index']);
        Route::post('/', [DesignItemController::class, 'store']);
        Route::get('/{id}', [DesignItemController::class, 'showDesignItem']);
        Route::put('/{id}', [DesignItemController::class, 'update']);
        Route::delete('/{id}', [DesignItemController::class, 'destroy']);
        Route::get('/customer/{customerId}', [DesignItemController::class, 'getByCustomer']);
        Route::get('/designer/{designerId}', [DesignItemController::class, 'getByDesigner']);
        Route::get('/{id}/calculate-total-cost', [DesignItemController::class, 'calculateTotalCost']);
        Route::get('/stats/technology', [DesignItemController::class, 'getTechnologyStats']);
        Route::get('/stats/link-class', [DesignItemController::class, 'getLinkClassStats']);
        Route::get('/search', [DesignItemController::class, 'search']);
    });

    // PDF Routes
    Route::get('/leases/{lease}/acceptance-pdf', function (Lease $lease) {
        $pdf = Pdf::loadView('leases.acceptance', [
            'lease' => $lease,
            'customerName' => $lease->customer->name,
            'customerCompany' => $lease->customer->company,
        ]);

        $filename = 'Acceptance-Certificate-Lease-' . $lease->id . '.pdf';

        return $pdf->download($filename);
    });

    Route::get('/leases/{lease}/generate-pdf', [LeaseController::class, 'generateAcceptancePdf']);

    // Customer show route
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customer.show');

    // Kenya Dark Fibre Dashboard Routes
    Route::middleware(['auth'])->prefix('kenya-fibre')->name('kenya.fibre.')->group(function () {
        Route::get('/dashboard', [KenyaFibreDashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/networks', [KenyaFibreDashboardController::class, 'getNetworkData']);
        Route::get('/api/nodes', [KenyaFibreDashboardController::class, 'getNodeData']);
        Route::get('/api/stats', [KenyaFibreDashboardController::class, 'getStats']);
        Route::get('/network/{id}', [KenyaFibreDashboardController::class, 'getNetworkDetail']);
        Route::post('/network/{id}/status', [KenyaFibreDashboardController::class, 'updateNetworkStatus']);
    });

});

// ==========================
// Chat Web Routes (for Blade views)
// ==========================
Route::middleware(['auth'])->group(function () {
    // Chat routes
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/create', [ChatController::class, 'create'])->name('create');
        Route::post('/start', [ChatController::class, 'startConversation'])->name('start');
        Route::get('/search/users', [ChatController::class, 'searchUsers'])->name('search.users');
        Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/{conversationId}/messages', [ChatController::class, 'getConversation'])->name('messages');
        Route::post('/{conversationId}/messages', [ChatController::class, 'store'])->name('store');
        Route::post('/{conversationId}/read', [ChatController::class, 'markAsRead'])->name('read');
        Route::get('/download/{messageId}', [ChatController::class, 'downloadFile'])->name('download');
        Route::get('/{conversationId}', [ChatController::class, 'show'])->name('show');
        Route::get('/profile/{userId}', [ChatController::class, 'startFromProfile'])->name('start-from-profile');
    });
});
// Email Routes
Route::prefix('finance/emails')->name('finance.emails.')->middleware(['auth'])->group(function () {
    Route::get('/settings', [MailController::class, 'settings'])->name('settings');
    Route::post('/test', [MailController::class, 'testEmail'])->name('test');
    Route::post('/billing/{billingId}/reminder', [MailController::class, 'sendBillingReminder'])->name('send-reminder');
    Route::post('/billing/{billingId}/invoice', [MailController::class, 'sendInvoiceEmail'])->name('send-invoice');
    Route::post('/payment/{transactionId}/receipt', [MailController::class, 'sendPaymentReceipt'])->name('send-receipt');
    Route::post('/overdue-notices', [MailController::class, 'sendOverdueNotices'])->name('send-overdue-notices');
    Route::post('/due-reminders', [MailController::class, 'sendDueReminders'])->name('send-due-reminders');
});

Route::prefix('finance/sync')->name('finance.sync.')->middleware(['auth'])->group(function () {
    Route::post('/to-settings', [FinancialSyncController::class, 'syncToSettings'])->name('to-settings');
    Route::post('/to-parameters', [FinancialSyncController::class, 'syncToParameters'])->name('to-parameters');
    Route::get('/status', [FinancialSyncController::class, 'getStatus'])->name('status');
});
// Include API routes
require __DIR__.'/api.php';
