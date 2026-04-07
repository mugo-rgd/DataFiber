<?php

use App\Http\Controllers\Admin\LeaseSearchController;
use App\Http\Controllers\Api\FibreStationController;
use App\Http\Controllers\ConversionDataController;
use App\Http\Controllers\DesignItemController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ColocationSiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FiberNodeController;
use App\Http\Controllers\FiberNetworkController;
use App\Http\Controllers\FiberSegmentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================
// Rate Limiting Configuration
// ==========================
RateLimiter::for('api', function ($job) {
    return Limit::perMinute(60)->by($job->user()?->id ?: $job->ip());
});

RateLimiter::for('api-sensitive', function ($job) {
    return Limit::perMinute(30)->by($job->user()?->id ?: $job->ip());
});

RateLimiter::for('api-admin', function ($job) {
    return Limit::perMinute(100)->by($job->user()?->id ?: $job->ip());
});

// ==========================
// Authenticated User Route
// ==========================
Route::middleware(['auth:sanctum', 'throttle:api'])->get('/user', function (Request $request) {
    return $request->user();
});

// ==========================
// Protected API Routes (Require Authentication)
// ==========================
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // Colocation Sites
    Route::post('/colocation-sites', [ColocationSiteController::class, 'store'])
        ->middleware('can:create-colocation-sites');

    Route::get('/colocation-sites/design-request/{designRequestId}', [ColocationSiteController::class, 'getByDesignRequest'])
        ->middleware('can:view,designRequestId');

    // Fibre Stations
    Route::get('/fibre-stations', [FibreStationController::class, 'index'])
        ->middleware('can:view-fibre-stations');

    Route::get('/fibre-stations/owners', [FibreStationController::class, 'getOwners'])
        ->middleware('can:view-fibre-stations');

    Route::get('/fibre-stations/areas', [FibreStationController::class, 'getAreas'])
        ->middleware('can:view-fibre-stations');

    // Billing API Routes
    Route::prefix('billing')->middleware('can:manage-billing')->group(function () {
        Route::post('/process', [BillingController::class, 'processBilling']);
        Route::post('/retry-emails', [BillingController::class, 'retryFailedEmails']);
        Route::post('/process-overdue', [BillingController::class, 'processOverdueBillings']);
        Route::get('/statistics', [BillingController::class, 'getBillingStatistics']);
        Route::get('/lease-billings', [BillingController::class, 'getLeaseBillings']);
        Route::get('/lease-billings/{leaseId}', [BillingController::class, 'getLeaseBillings'])
            ->middleware('can:view-lease-billings,leaseId');
        Route::get('/billings/{billingId}', [BillingController::class, 'getBillingDetails'])
            ->middleware('can:view-billing,billingId');
        Route::put('/billings/{billingId}/status', [BillingController::class, 'updateBillingStatus'])
            ->middleware('can:update-billing-status,billingId');
        Route::get('/customers/{customerId}/billings', [BillingController::class, 'getCustomerBillings'])
            ->middleware('can:view-customer-billings,customerId');
    });

    // Search API Routes
    Route::prefix('/api/search')->middleware('can:search-leases')->group(function () {
        Route::get('/leases', [LeaseSearchController::class, 'search'])
            ->name('api.search.leases');
        Route::get('/account-managers', [LeaseSearchController::class, 'getAccountManagers'])
            ->name('api.search.account-managers');
        Route::get('/leases/by-manager', [LeaseSearchController::class, 'searchByAccountManager'])
            ->name('api.search.leases.by-manager');
        Route::get('/leases/advanced', [LeaseSearchController::class, 'advancedSearch'])
            ->name('api.search.leases.advanced');
    });

    // Conversion Data API Routes
    Route::prefix('api')->middleware('can:view-conversion-data')->group(function () {
        Route::get('conversion-data/summary', [ConversionDataController::class, 'apiSummary']);
        Route::get('conversion-data/customer/{customer}/analysis', [ConversionDataController::class, 'apiCustomerAnalysis'])
            ->middleware('can:view-customer,customer');
        Route::get('conversion-data/export', [ConversionDataController::class, 'export']);
    });

    // Customer Unpaid Billings
    Route::get('/customers/{customerId}/unpaid-billings', function($customerId) {
        try {
            if (!auth()->user()->can('view-customer-billings', $customerId)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $billings = \App\Models\LeaseBilling::where('customer_id', $customerId)
                        ->whereIn('status', ['pending', 'overdue'])
                        ->orderBy('due_date', 'asc')
                        ->get(['id', 'billing_number', 'amount', 'due_date']);

            return response()->json([
                'success' => true,
                'data' => $billings
            ]);
        } catch (\Exception $e) {
            \Log::error('Unpaid billings API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    });

    // Fiber Nodes API
    Route::prefix('nodes')->middleware('can:manage-fiber-infrastructure')->group(function () {
        Route::get('/', [FiberNodeController::class, 'index']);
        Route::post('/', [FiberNodeController::class, 'store']);
        Route::get('/region/{region}', [FiberNodeController::class, 'getByRegion']);
        Route::get('/nearby', [FiberNodeController::class, 'getNearby']);
        Route::get('/{id}', [FiberNodeController::class, 'show']);
        Route::put('/{id}', [FiberNodeController::class, 'update']);
        Route::delete('/{id}', [FiberNodeController::class, 'destroy']);
    });

    // Fiber Networks API
    Route::prefix('networks')->middleware('can:manage-fiber-infrastructure')->group(function () {
        Route::get('/', [FiberNetworkController::class, 'index']);
        Route::post('/', [FiberNetworkController::class, 'store']);
        Route::get('/geojson', [FiberNetworkController::class, 'getGeoJSON']);
        Route::get('/stats', [FiberNetworkController::class, 'getStats']);
        Route::get('/region/{region}', [FiberNetworkController::class, 'getByRegion']);
        Route::get('/{id}', [FiberNetworkController::class, 'show']);
        Route::put('/{id}', [FiberNetworkController::class, 'update']);
        Route::delete('/{id}', [FiberNetworkController::class, 'destroy']);
    });

    // Fiber Segments API
    Route::prefix('segments')->middleware('can:manage-fiber-infrastructure')->group(function () {
        Route::get('/', [FiberSegmentController::class, 'index']);
        Route::post('/', [FiberSegmentController::class, 'store']);
        Route::get('/network/{networkId}', [FiberSegmentController::class, 'getByNetwork']);
        Route::get('/{id}', [FiberSegmentController::class, 'show']);
        Route::put('/{id}', [FiberSegmentController::class, 'update']);
        Route::delete('/{id}', [FiberSegmentController::class, 'destroy']);
    });

    // Dashboard API
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])
        ->middleware('can:view-dashboard');
});

// ==========================
// Public API Routes (with rate limiting)
// ==========================
Route::middleware(['throttle:api'])->group(function () {
    // Add any truly public API routes here
});

// ==========================
// OPTIONAL: Development-only routes
// ==========================
if (app()->environment('local')) {
    Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('dev')->group(function () {
        Route::get('/debug/user-permissions', function () {
            return response()->json([
                'user' => auth()->user()->only(['id', 'name', 'role']),
                'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
            ]);
        });
    });
}
