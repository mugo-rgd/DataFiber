<?php

use App\Http\Controllers\Admin\DesignRequestController;
use App\Http\Controllers\Admin\LeaseSearchController;
use App\Http\Controllers\Api\FibreStationController;
use App\Http\Controllers\ConversionDataController;
use App\Http\Controllers\DesignItemController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ColocationSiteController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController as ApiMessageController;
use App\Http\Controllers\FiberNodeController;
use App\Http\Controllers\FiberNetworkController;
use App\Http\Controllers\FiberSegmentController;
use App\Http\Controllers\FiberPricingController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/colocation-sites', [ColocationSiteController::class, 'store']);
Route::get('/colocation-sites/design-request/{designRequestId}', [ColocationSiteController::class, 'getByDesignRequest']);

// Fibre Stations API Routes
Route::get('/fibre-stations', [FibreStationController::class, 'index']);
Route::get('/fibre-stations/owners', [FibreStationController::class, 'getOwners']);
Route::get('/fibre-stations/areas', [FibreStationController::class, 'getAreas']);

// Design Requests KML Route
// Route::get('/design-requests/{designRequest}/kml', [DesignRequestController::class, 'generateKml'])
//     ->middleware('auth')
//     ->name('design-requests.kml');

// Billing API Routes
Route::prefix('billing')->group(function () {
    Route::post('/process', [BillingController::class, 'processBilling']);
    Route::post('/retry-emails', [BillingController::class, 'retryFailedEmails']);
    Route::post('/process-overdue', [BillingController::class, 'processOverdueBillings']);
    Route::get('/statistics', [BillingController::class, 'getBillingStatistics']);
    Route::get('/lease-billings', [BillingController::class, 'getLeaseBillings']);
    Route::get('/lease-billings/{leaseId}', [BillingController::class, 'getLeaseBillings']);
    Route::get('/billings/{billingId}', [BillingController::class, 'getBillingDetails']);
    Route::put('/billings/{billingId}/status', [BillingController::class, 'updateBillingStatus']);
    Route::get('/customers/{customerId}/billings', [BillingController::class, 'getCustomerBillings']);
});

// Quotation API Route
Route::get('/quotations/{id}', function ($id) {
    $quotation = \App\Models\Quotation::with('designRequest')
        ->where('id', $id)
        ->where('status', 'approved')
        ->firstOrFail();

    return response()->json([
        'id' => $quotation->id,
        'quotation_number' => $quotation->quotation_number,
        'design_request_title' => $quotation->designRequest->title ?? 'Untitled',
        'total_amount' => $quotation->total_amount,
        'service_type' => $quotation->service_type,
        'bandwidth' => $quotation->bandwidth,
        'technology' => $quotation->technology,
        'start_location' => $quotation->start_location,
        'end_location' => $quotation->end_location,
        'distance_km' => $quotation->distance_km,
        'monthly_cost' => $quotation->monthly_cost,
        'installation_fee' => $quotation->installation_fee,
        'technical_specifications' => $quotation->technical_specifications,
        'service_level_agreement' => $quotation->service_level_agreement,
        'terms_and_conditions' => $quotation->terms_and_conditions,
        'special_requirements' => $quotation->special_requirements,
        'notes' => $quotation->notes,
        'currency' => $quotation->currency,
    ]);
})->middleware('auth:sanctum');

// Quotation Design Items API Route
Route::get('/api/quotations/{quotationId}/design-items', function($quotationId) {
    try {
        $quotation = \App\Models\Quotation::findOrFail($quotationId);
        $designRequest = $quotation->designRequest;

        if (!$designRequest) {
            return response()->json([], 200);
        }

        $designItems = \App\Models\DesignItem::where('request_number', $designRequest->request_number)
            ->get(['id', 'cores_required', 'distance', 'technology_type', 'terms', 'unit_cost', 'route_name']);

        return response()->json($designItems);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware(['auth:sanctum', 'verified']);

// Billing Line Items API Route
Route::get('/api/billing/{id}/line-items', function($id) {
    $billing = \App\Models\ConsolidatedBilling::with('lineItems')->find($id);

    if (!$billing) {
        return response()->json([], 404);
    }

    return response()->json($billing->lineItems);
});
// Make sure these are in your routes file, NOT in the controller
Route::get('/api/search/leases', [App\Http\Controllers\Admin\LeaseSearchController::class, 'search'])->name('api.search.leases');
Route::get('/api/search/account-managers', [App\Http\Controllers\Admin\LeaseSearchController::class, 'getAccountManagers'])->name('api.search.account-managers');
Route::get('/api/search/leases/by-manager', [App\Http\Controllers\Admin\LeaseSearchController::class, 'searchByAccountManager'])->name('api.search.leases.by-manager');
Route::get('/api/search/leases/advanced', [App\Http\Controllers\Admin\LeaseSearchController::class, 'advancedSearch'])->name('api.search.leases.advanced');
// Conversion Data API Routes
Route::prefix('api')->group(function () {
    Route::get('conversion-data/summary', [ConversionDataController::class, 'apiSummary']);
    Route::get('conversion-data/customer/{customer}/analysis', [ConversionDataController::class, 'apiCustomerAnalysis']);
    Route::get('conversion-data/export', [ConversionDataController::class, 'export']);
});

// ==========================
// Chat API Routes - FIXED
// ==========================


Route::middleware('auth:sanctum')->prefix('')->group(function () {
    // Chat API endpoints - all return JSON
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/search/users', [ChatController::class, 'searchUsers']);
    Route::post('/chat/start', [ChatController::class, 'startConversation']);
    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount']);
    Route::get('/chat/{conversationId}', [ChatController::class, 'show']);

    // Message API endpoints
    Route::post('/messages', [ApiMessageController::class, 'store']);
    Route::post('/chat/{conversationId}/read', [ApiMessageController::class, 'markAsRead']);
});
Route::post('/messages', [ChatController::class, 'sendMessage'])->name('api.messages.send');
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/customers/{customerId}/unpaid-billings', function($customerId) {
    try {
        $billings = \App\Models\LeaseBilling::where('customer_id', $customerId)
                    ->whereIn('status', ['pending', 'overdue'])
                    ->orderBy('due_date', 'asc')
                    ->get(['id', 'billing_number', 'amount', 'due_date']);

        return response()->json([
            'success' => true,
            'data' => $billings
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
})->middleware('auth:sanctum');

// Fiber Nodes API
Route::prefix('nodes')->group(function () {
    Route::get('/', [FiberNodeController::class, 'index']);
    Route::post('/', [FiberNodeController::class, 'store']);
    Route::get('/region/{region}', [FiberNodeController::class, 'getByRegion']);
    Route::get('/nearby', [FiberNodeController::class, 'getNearby']);
    Route::get('/{id}', [FiberNodeController::class, 'show']);
    Route::put('/{id}', [FiberNodeController::class, 'update']);
    Route::delete('/{id}', [FiberNodeController::class, 'destroy']);
});

// Fiber Networks API
Route::prefix('networks')->group(function () {
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
Route::prefix('segments')->group(function () {
    Route::get('/', [FiberSegmentController::class, 'index']);
    Route::post('/', [FiberSegmentController::class, 'store']);
    Route::get('/network/{networkId}', [FiberSegmentController::class, 'getByNetwork']);
    Route::get('/{id}', [FiberSegmentController::class, 'show']);
    Route::put('/{id}', [FiberSegmentController::class, 'update']);
    Route::delete('/{id}', [FiberSegmentController::class, 'destroy']);
});

// Dashboard API
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/fiber/networks/geojson', [App\Http\Controllers\Api\FiberNetworkController::class, 'geojson']);
Route::get('/fiber/nodes/geojson', [App\Http\Controllers\Api\FiberNodeController::class, 'geojson']);
Route::get('/fiber/networks/{id}', [App\Http\Controllers\Api\FiberNetworkController::class, 'show']);
Route::post('/fiber/networks/{id}/status', [App\Http\Controllers\Api\FiberNetworkController::class, 'updateStatus']);
