<?php

namespace App\Http\Controllers;

use App\Models\ColocationList;
use App\Models\CompanyProfile;
use App\Models\County;
use App\Models\DesignRequest;
use App\Models\Quotation;
use App\Models\User;
use App\Models\CommercialRoute;
use App\Models\ColocationService;
use App\Services\QuotationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Facades\Activity;
use App\Models\CustomRoute;


class QuotationController extends Controller
{
     use AuthorizesRequests; // Add this trait
    public function create(Request $request)
{
    $this->authorize('create', Quotation::class);

    $designRequestId = $request->input('design_request_id');

    if (!$designRequestId) {
        return redirect()->back()
            ->with('error', 'Design request ID is required.');
    }

    $designRequest = DesignRequest::with(['customer'])->findOrFail($designRequestId);

    // Authorization check for designers - now handled by policy
    if (Auth::user()->role === 'designer' && $designRequest->designer_id !== Auth::id()) {
        abort(403, 'You are not authorized to create quotations for this design request.');
    }

    // Check if quotation already exists
    $existingQuotation = Quotation::where('design_request_id', $designRequestId)->first();

    if ($existingQuotation) {
        return redirect()->route('admin.quotations.show', $existingQuotation)
            ->with('info', 'A quotation already exists for this design request.');
    }

    // Get commercial routes grouped by option
    $commercialRoutes = CommercialRoute::available()
        ->orderBy('option')
        ->orderBy('name_of_route')
        ->get()
        ->groupBy('option');

    // Get colocation services
    $colocationServices = ColocationList::where('fibrestatus', 'active')->get();

    $customRoutes = CustomRoute::where('design_request_id', $designRequest->id)
    ->latest()
    ->get();

    $counties = DB::table('county')
    ->where('is_active', 1)
    ->orderBy('name')
    ->get();

    // Get active colocation service instances for this design request
    $colocationInstances = ColocationService::where('design_request_id', $designRequestId)
        ->where('status', 'active')
        ->get();

    // Default terms
    $defaultTerms = "TERMS AND CONDITIONS:\n\n1. PAYMENT TERMS:\n   • Net 30 days from invoice date\n   • Late payments subject to 1.5% monthly interest\n   • All prices in USD unless specified\n\n2. VALIDITY:\n   • Quotation valid for 30 days from issue date\n   • Prices subject to change after validity period\n\n3. FIBRE LEASE SPECIFIC:\n   • Minimum contract period: 12 months\n   • Core assignment subject to availability\n   • Installation timeline: 30-60 days after approval\n   • Monthly billing in advance\n\n4. COLOCATION SPECIFIC:\n   • Minimum contract period: 12 months\n   • Setup fees: One-time payment\n   • Power consumption billed separately\n   • 24/7 access with prior notice";

    return view('admin.quotations.create', compact(
        'designRequest',
        'commercialRoutes',
        'colocationServices',
        'colocationInstances',
        'defaultTerms',
    'customRoutes',
    'counties'
    ));
}

  public function store(Request $request)
{
    $this->authorize('create', Quotation::class);

    $validated = $request->validate([
        'design_request_id' => 'required|exists:design_requests,id',
        'scope_of_work' => 'required|string|max:5000',
        'terms_and_conditions' => 'required|string|max:5000',
        'customer_notes' => 'nullable|string|max:2000',
        'valid_until' => 'required|date|after:today',
        'tax_rate' => 'required|numeric|min:0|max:0.5',

        'selected_routes' => 'nullable|array',
        'selected_routes.*' => 'exists:commercial_routes,id',
        'route_cores' => 'nullable|array',
        'route_duration' => 'nullable|array',

        'selected_custom_routes' => 'nullable|array',
        'selected_custom_routes.*' => 'exists:custom_routes,id',

        'selected_services' => 'nullable|array',
        'service_duration' => 'nullable|array',
        'service_quantity' => 'nullable|array',
        'service_source' => 'nullable|array',

        'custom_items' => 'nullable|array',
        'action' => 'nullable|in:draft,send',
    ]);

    try {
        DB::beginTransaction();

        $designRequest = DesignRequest::with('customer')->findOrFail($request->design_request_id);

        if (Auth::user()->role === 'designer' && $designRequest->designer_id !== Auth::id()) {
            abort(403, 'You are not authorized to create quotations for this design request.');
        }

        $lineItems = [];
        $subtotal = 0;

        /*
        |--------------------------------------------------------------------------
        | Commercial Routes
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('selected_routes', []) as $routeId) {
            $route = CommercialRoute::find($routeId);

            if (!$route) {
                continue;
            }

            $cores = (int) ($request->input("route_cores.$routeId") ?? $route->no_of_cores_required ?? 1);
            $duration = (int) ($request->input("route_duration.$routeId") ?? 12);

            $unitCost = $route->unit_cost_per_core_km_per_month
                ?? $route->unit_cost_per_core_per_km_per_month
                ?? $route->unit_cost
                ?? match ($route->option ?? null) {
                    'Non Premium' => 18,
                    'Premium' => 19,
                    'Metro' => 20,
                    default => 0,
                };

            $distance = (float) ($route->approx_distance_km ?? 0);
            $monthlyCost = (float) $unitCost * $distance * $cores;
            $capex = (float) ($route->capital_expenditure ?? 0);
            $totalCost = ($monthlyCost * $duration) + $capex;

            $lineItems[] = [
                'type' => 'commercial_route',
                'item_id' => $route->id,
                'description' => "{$route->name_of_route} ({$cores} cores, {$duration} months)",
                'quantity' => $cores,
                'unit_price' => $monthlyCost,
                'total' => $totalCost,
                'metadata' => [
                    'route_id' => $route->id,
                    'route_code' => $route->route_code ?? null,
                    'route_name' => $route->name_of_route,
                    'region' => $route->all_region ?? $route->region ?? null,
                    'option' => $route->option ?? null,
                    'link_class' => $route->option ?? null,
                    'technology_type' => $route->tech_type ?? null,
                    'distance_km' => $distance,

                    'start_point' => $route->start_point
                        ?? $route->from_location
                        ?? $route->source_location
                        ?? null,

                    'end_point' => $route->end_point
                        ?? $route->to_location
                        ?? $route->destination_location
                        ?? null,

                    'pickup_points' => trim(
                        (($route->start_point ?? $route->from_location ?? $route->source_location ?? 'N/A')
                        . ' - ' .
                        ($route->end_point ?? $route->to_location ?? $route->destination_location ?? 'N/A'))
                    ),

                    'cores' => $cores,
                    'duration_months' => $duration,
                    'unit_cost_per_core_per_km_per_month' => (float) $unitCost,
                    'monthly_cost' => $monthlyCost,
                    'capital_expenditure' => $capex,
                ],
            ];

            $subtotal += $totalCost;
        }

        /*
        |--------------------------------------------------------------------------
        | Custom Routes
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('selected_custom_routes', []) as $customRouteId) {
            $customRoute = CustomRoute::find($customRouteId);

            if (!$customRoute) {
                continue;
            }

            $duration = (int) ($customRoute->contract_duration_months ?? 12);
            $monthlyCost = (float) $customRoute->monthly_cost;
            $capex = (float) ($customRoute->capital_expenditure ?? 0);
            $totalCost = ($monthlyCost * $duration) + $capex;

            $lineItems[] = [
                'type' => 'custom_route',
                'item_id' => $customRoute->id,
                'description' => "{$customRoute->name_of_route} (Custom Route, {$duration} months)",
                'quantity' => (int) ($customRoute->no_of_cores_required ?? 1),
                'unit_price' => $monthlyCost,
                'total' => $totalCost,
                'metadata' => [
                    'route_id' => $customRoute->id,
                    'route_code' => 'CUSTOM-' . $customRoute->id,
                    'route_name' => $customRoute->name_of_route,
                    'region' => $customRoute->region,
                    'option' => $customRoute->option,
                    'link_class' => $customRoute->option,
                    'technology_type' => $customRoute->tech_type,
                    'distance_km' => (float) ($customRoute->approx_distance_km ?? 0),
                    'pickup_points' => $customRoute->route_description ?? 'Custom route',
                    'cores' => (int) ($customRoute->no_of_cores_required ?? 1),
                    'duration_months' => $duration,
                    'unit_cost_per_core_per_km_per_month' => (float) $customRoute->unit_cost_per_core_per_km_per_month,
                    'monthly_cost' => $monthlyCost,
                    'capital_expenditure' => $capex,
                    'is_custom_route' => true,
                ],
            ];

            $subtotal += $totalCost;
        }

        /*
        |--------------------------------------------------------------------------
        | Colocation Services
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('selected_services', []) as $serviceId) {
            $source = $request->input("service_source.$serviceId", 'list');
            $duration = (int) ($request->input("service_duration.$serviceId") ?? 12);
            $quantity = (int) ($request->input("service_quantity.$serviceId") ?? 1);

            if ($source === 'list') {
                $service = ColocationList::where('service_id', $serviceId)->first();

                if (!$service) {
                    continue;
                }

                $monthlyRate = (float) ($service->monthly_price_usd ?? (($service->recurrent_per_Annum ?? 0) / 12));
                $setupFee = (float) ($service->setup_fee_usd ?? $service->oneoff_rate ?? 0);
                $serviceName = $service->service_type ?? 'Colocation Service';
                $serviceCategory = $service->service_category ?? null;
                $realServiceId = $service->service_id;
            } else {
                $service = ColocationService::find($serviceId);

                if (!$service) {
                    continue;
                }

                $monthlyRate = (float) ($service->monthly_price ?? $service->monthly_price_usd ?? 0);
                $setupFee = (float) ($service->setup_fee ?? $service->setup_fee_usd ?? 0);
                $serviceName = $service->service_type ?? $service->name ?? 'Colocation Service';
                $serviceCategory = $service->service_category ?? null;
                $realServiceId = $service->id;
            }

            $monthlyTotal = $monthlyRate * $quantity;
            $setupTotal = $setupFee * $quantity;
            $totalCost = ($monthlyTotal * $duration) + $setupTotal;

            $lineItems[] = [
                'type' => 'colocation_service',
                'item_id' => $realServiceId,
                'description' => "{$serviceName} (Qty: {$quantity}, {$duration} months)",
                'quantity' => $quantity,
                'unit_price' => $monthlyTotal,
                'total' => $totalCost,
                'metadata' => [
                    'service_id' => $realServiceId,
                    'service_name' => $serviceName,
                    'service_category' => $serviceCategory,
                    'duration_months' => $duration,
                    'quantity' => $quantity,
                    'monthly_rate' => $monthlyRate,
                    'setup_fee' => $setupTotal,
                    'source' => $source,
                ],
            ];

            $subtotal += $totalCost;
        }

        /*
        |--------------------------------------------------------------------------
        | Custom Items
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('custom_items', []) as $index => $customItem) {
            if (empty($customItem['description']) || empty($customItem['unit_price'])) {
                continue;
            }

            $quantity = (int) ($customItem['quantity'] ?? 1);
            $unitPrice = (float) $customItem['unit_price'];
            $totalCost = $quantity * $unitPrice;

            $lineItems[] = [
                'type' => 'custom_item',
                'item_id' => null,
                'description' => $customItem['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $totalCost,
                'metadata' => [
                    'custom_item' => true,
                    'index' => $index,
                ],
            ];

            $subtotal += $totalCost;
        }

        $taxRate = (float) $request->tax_rate;
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        $quotationNumber = QuotationService::generateQuotationNumber($designRequest);

        $accountManagerId = Auth::user()->role === 'designer'
            ? $designRequest->customer->account_manager_id
            : Auth::id();

        $status = Auth::user()->role === 'admin' && $request->action === 'send'
            ? 'sent'
            : 'draft';

        $quotation = Quotation::create([
            'design_request_id' => $designRequest->id,
            'customer_id' => $designRequest->customer_id,
            'account_manager_id' => $accountManagerId,
            'quotation_number' => $quotationNumber,
            'line_items' => $lineItems,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'amount' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'scope_of_work' => $request->scope_of_work,
            'terms_and_conditions' => $request->terms_and_conditions,
            'customer_notes' => $request->customer_notes,
            'valid_until' => $request->valid_until,
            'status' => $status,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sync Pivot Tables
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('selected_routes', []) as $routeId) {
            $routeItem = collect($lineItems)
                ->where('type', 'commercial_route')
                ->firstWhere('item_id', (int) $routeId);

            if (!$routeItem) {
                continue;
            }

            $quotation->commercialRoutes()->attach($routeId, [
                'quantity' => $routeItem['quantity'],
                'duration_months' => $routeItem['metadata']['duration_months'],
                'unit_price' => $routeItem['unit_price'],
                'total_price' => $routeItem['total'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($request->input('selected_custom_routes', []) as $customRouteId) {
            $customItem = collect($lineItems)
                ->where('type', 'custom_route')
                ->firstWhere('item_id', (int) $customRouteId);

            if (!$customItem) {
                continue;
            }

            $quotation->customRoutes()->attach($customRouteId, [
                'monthly_cost' => $customItem['unit_price'],
                'capital_expenditure' => $customItem['metadata']['capital_expenditure'],
                'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($request->input('selected_services', []) as $serviceId) {
            $serviceItem = collect($lineItems)
                ->where('type', 'colocation_service')
                ->firstWhere('item_id', $serviceId);

            if (!$serviceItem) {
                continue;
            }

            $quotation->colocationServices()->attach($serviceId, [
                'quantity' => $serviceItem['quantity'],
                'duration_months' => $serviceItem['metadata']['duration_months'],
                'unit_price' => $serviceItem['unit_price'],
                'total_price' => $serviceItem['total'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        return redirect()
            ->route('admin.quotations.show', $quotation)
            ->with('success', $status === 'sent'
                ? 'Quotation created and sent to customer successfully!'
                : 'Quotation created successfully!'
            );

    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('Failed to create quotation', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return back()
            ->with('error', 'Failed to create quotation: ' . $e->getMessage())
            ->withInput();
    }
}

    public function index()
    {

           // Use policy for authorization
        $this->authorize('viewAny', Quotation::class);
        $userRole = Auth::user()->role;

        $quotations = Quotation::with(['designRequest',
        'customer',
        'accountManager',
        'contract',])
            ->when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })
            ->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })
            ->when(request('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        // Stats calculation
        $stats = [
            'total' => $quotations->total(),
            'draft' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })->where('status', 'draft')->count(),
            'sent' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })->where('status', 'sent')->count(),
            'approved' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })->where('status', 'approved')->count(),

        ];

 $isAdmin = in_array($userRole, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin','account_manager']);
        return view('admin.quotations.index', compact('quotations', 'stats','isAdmin'));
    }

   public function show(Quotation $quotation)
{
     // Check authorization based on user role
    if (auth()->user()->hasRole('customer')) {
        // For customers, check if they own this quotation
        if (auth()->id() !== $quotation->customer_id) {
            abort(403, 'Unauthorized action.');
        }
        $view = 'customer.quotations.show';
    } else {
        // For staff, check if they have permission to view quotations
        // $this->authorize('view', $quotation);
        $view = 'quotations.show';
    }
      $quotation->load([
    'designRequest.customer.customerProfile',
    'accountManager',
    'commercialRoutes',
    'colocationServices',
    'contract',
    'customRoutes',
    ]);
     $customerProfile = $quotation->designRequest->customer->customerProfile ?? null;

      if (Auth::user()->role === 'designer') {
        return view('designer.quotations.show', compact('quotation', 'customerProfile'));
    }

    if (Auth::user()->role === 'customer') {
        return view('customer.quotations.show', compact('quotation', 'customerProfile'));
    }
    return view('admin.quotations.show', compact('quotation', 'customerProfile'));
}

    // public function edit(Quotation $quotation)
    // {
    //     // Use policy for authorization
    //     // $this->authorize('update', $quotation);

    //     if ($quotation->status !== 'draft') {
    //         return redirect()->route('admin.quotations.show', $quotation)
    //             ->with('error', 'Only draft quotations can be edited.');
    //     }

    //     // $quotation->load(['designRequest.customer', 'commercialRoutes', 'colocationServices']);
    //     // $commercialRoutes = CommercialRoute::where('availability', 'YES')->get();
    //     // $colocationServices = ColocationService::all();

    //     $quotation->load(['designRequest.customer', 'commercialRoutes', 'colocationServices','customRoutes']);
    // $commercialRoutes = CommercialRoute::where('availability', 'YES')->get();
    // $colocationServices = ColocationList::all();

    //     return view('admin.quotations.edit', compact('quotation', 'commercialRoutes', 'colocationServices','customRoutes'));
    // }

    public function edit(Quotation $quotation)
{
    $quotation->load([
        'designRequest.customer',
        'commercialRoutes',
        'colocationServices',
        'customRoutes',
    ]);

    $designRequest = $quotation->designRequest;

           $commercialRoutes = CommercialRoute::where('availability', 'YES')->get();

    $colocationServices = ColocationList::all();

    $customRoutes = CustomRoute::where('design_request_id', $designRequest->id)
        ->latest()
        ->get();

    $counties = DB::table('county')
        ->where('is_active', 1)
        ->orderBy('name')
        ->get();

    return view('admin.quotations.edit', compact(
        'quotation',
        'designRequest',
        'commercialRoutes',
        'colocationServices',
        'customRoutes',
        'counties'
    ));
}

   public function update(Request $request, Quotation $quotation)
{
    if (!in_array($quotation->status, ['draft', 'rejected'])) {
        return redirect()
            ->route('admin.quotations.show', $quotation)
            ->with('error', 'Only draft or rejected quotations can be updated. Current status: ' . $quotation->status);
    }

    $validated = $request->validate([
        'scope_of_work' => 'required|string|max:5000',
        'terms_and_conditions' => 'required|string|max:5000',
        'customer_notes' => 'nullable|string|max:2000',
        'valid_until' => 'required|date|after:today',
        'tax_rate' => 'required|numeric|min:0|max:0.5',

        'selected_routes' => 'nullable|array',
        'selected_routes.*' => 'exists:commercial_routes,id',
        'route_cores' => 'nullable|array',
        'route_duration' => 'nullable|array',

        'selected_custom_routes' => 'nullable|array',
        'selected_custom_routes.*' => 'exists:custom_routes,id',

        'selected_services' => 'nullable|array',
        'service_duration' => 'nullable|array',
        'service_quantity' => 'nullable|array',
        'service_source' => 'nullable|array',

        'custom_items' => 'nullable|array',
        'action' => 'nullable|in:draft,send',
    ]);

    try {
        DB::beginTransaction();

        $lineItems = [];
        $subtotal = 0;

        /*
        |--------------------------------------------------------------------------
        | Commercial Routes
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('selected_routes', []) as $routeId) {
            $route = CommercialRoute::find($routeId);

            if (!$route) {
                continue;
            }

            $cores = (int) ($request->input("route_cores.$routeId") ?? $route->no_of_cores_required ?? 1);
            $duration = (int) ($request->input("route_duration.$routeId") ?? 12);

            $unitCost = $route->unit_cost_per_core_km_per_month
                ?? $route->unit_cost_per_core_per_km_per_month
                ?? $route->unit_cost
                ?? match ($route->option ?? null) {
                    'Non Premium' => 18,
                    'Premium' => 19,
                    'Metro' => 20,
                    default => 0,
                };

            $distance = (float) ($route->approx_distance_km ?? 0);
            $monthlyCost = (float) $unitCost * $distance * $cores;
            $capex = (float) ($route->capital_expenditure ?? 0);
            $totalCost = ($monthlyCost * $duration) + $capex;

            $lineItems[] = [
                'type' => 'commercial_route',
                'item_id' => $route->id,
                'description' => "{$route->name_of_route} ({$cores} cores, {$duration} months)",
                'quantity' => $cores,
                'unit_price' => $monthlyCost,
                'total' => $totalCost,
                'metadata' => [
                    'route_id' => $route->id,
                    'route_code' => $route->route_code ?? null,
                    'route_name' => $route->name_of_route,
                    'region' => $route->all_region ?? $route->region ?? null,
                    'option' => $route->option ?? null,
                    'link_class' => $route->option ?? null,
                    'technology_type' => $route->tech_type ?? null,
                    'distance_km' => $distance,

                    'start_point' => $route->start_point
                        ?? $route->from_location
                        ?? $route->source_location
                        ?? null,

                    'end_point' => $route->end_point
                        ?? $route->to_location
                        ?? $route->destination_location
                        ?? null,

                    'pickup_points' => trim(
                        (($route->start_point ?? $route->from_location ?? $route->source_location ?? 'N/A')
                        . ' - ' .
                        ($route->end_point ?? $route->to_location ?? $route->destination_location ?? 'N/A'))
                    ),

                    'cores' => $cores,
                    'duration_months' => $duration,
                    'unit_cost_per_core_per_km_per_month' => (float) $unitCost,
                    'monthly_cost' => $monthlyCost,
                    'capital_expenditure' => $capex,
                ],
            ];

            $subtotal += $totalCost;
        }

        /*
        |--------------------------------------------------------------------------
        | Custom Routes
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('selected_custom_routes', []) as $customRouteId) {
            $customRoute = CustomRoute::find($customRouteId);

            if (!$customRoute) {
                continue;
            }

            $duration = (int) ($customRoute->contract_duration_months ?? 12);
            $monthlyCost = (float) $customRoute->monthly_cost;
            $capex = (float) ($customRoute->capital_expenditure ?? 0);
            $totalCost = ($monthlyCost * $duration) + $capex;

            $lineItems[] = [
                'type' => 'custom_route',
                'item_id' => $customRoute->id,
                'description' => "{$customRoute->name_of_route} (Custom Route, {$duration} months)",
                'quantity' => (int) ($customRoute->no_of_cores_required ?? 1),
                'unit_price' => $monthlyCost,
                'total' => $totalCost,
                'metadata' => [
                    'route_id' => $customRoute->id,
                    'route_code' => 'CUSTOM-' . $customRoute->id,
                    'route_name' => $customRoute->name_of_route,
                    'region' => $customRoute->region,
                    'option' => $customRoute->option,
                    'link_class' => $customRoute->option,
                    'technology_type' => $customRoute->tech_type,
                    'distance_km' => (float) ($customRoute->approx_distance_km ?? 0),
                    'pickup_points' => $customRoute->route_description ?? 'Custom route',
                    'cores' => (int) ($customRoute->no_of_cores_required ?? 1),
                    'duration_months' => $duration,
                    'unit_cost_per_core_per_km_per_month' => (float) $customRoute->unit_cost_per_core_per_km_per_month,
                    'monthly_cost' => $monthlyCost,
                    'capital_expenditure' => $capex,
                    'is_custom_route' => true,
                ],
            ];

            $subtotal += $totalCost;
        }

        /*
        |--------------------------------------------------------------------------
        | Colocation Services
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('selected_services', []) as $serviceId) {
            $source = $request->input("service_source.$serviceId", 'list');
            $duration = (int) ($request->input("service_duration.$serviceId") ?? 12);
            $quantity = (int) ($request->input("service_quantity.$serviceId") ?? 1);

            if ($source === 'list') {
                $service = ColocationList::where('service_id', $serviceId)->first();

                if (!$service) {
                    continue;
                }

                $monthlyRate = (float) ($service->monthly_price_usd ?? (($service->recurrent_per_Annum ?? 0) / 12));
                $setupFee = (float) ($service->setup_fee_usd ?? $service->oneoff_rate ?? 0);
                $serviceName = $service->service_type ?? 'Colocation Service';
                $serviceCategory = $service->service_category ?? null;
                $realServiceId = $service->service_id;
            } else {
                $service = ColocationService::find($serviceId);

                if (!$service) {
                    continue;
                }

                $monthlyRate = (float) ($service->monthly_price ?? $service->monthly_price_usd ?? 0);
                $setupFee = (float) ($service->setup_fee ?? $service->setup_fee_usd ?? 0);
                $serviceName = $service->service_type ?? $service->name ?? 'Colocation Service';
                $serviceCategory = $service->service_category ?? null;
                $realServiceId = $service->id;
            }

            $monthlyTotal = $monthlyRate * $quantity;
            $setupTotal = $setupFee * $quantity;
            $totalCost = ($monthlyTotal * $duration) + $setupTotal;

            $lineItems[] = [
                'type' => 'colocation_service',
                'item_id' => $realServiceId,
                'description' => "{$serviceName} (Qty: {$quantity}, {$duration} months)",
                'quantity' => $quantity,
                'unit_price' => $monthlyTotal,
                'total' => $totalCost,
                'metadata' => [
                    'service_id' => $realServiceId,
                    'service_name' => $serviceName,
                    'service_category' => $serviceCategory,
                    'duration_months' => $duration,
                    'quantity' => $quantity,
                    'monthly_rate' => $monthlyRate,
                    'setup_fee' => $setupTotal,
                    'source' => $source,
                ],
            ];

            $subtotal += $totalCost;
        }

        /*
        |--------------------------------------------------------------------------
        | Custom Items
        |--------------------------------------------------------------------------
        */
        foreach ($request->input('custom_items', []) as $index => $customItem) {
            if (empty($customItem['description']) || empty($customItem['unit_price'])) {
                continue;
            }

            $quantity = (int) ($customItem['quantity'] ?? 1);
            $unitPrice = (float) $customItem['unit_price'];
            $totalCost = $quantity * $unitPrice;

            $lineItems[] = [
                'type' => 'custom_item',
                'item_id' => null,
                'description' => $customItem['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $totalCost,
                'metadata' => [
                    'custom_item' => true,
                    'index' => $index,
                ],
            ];

            $subtotal += $totalCost;
        }

        $taxRate = (float) $request->tax_rate;
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        $status = $quotation->status;

        if ($request->action === 'send' && Auth::user()->role === 'admin') {
            $status = 'sent';
        } elseif ($quotation->status === 'rejected') {
            $status = 'draft';
        }

        $quotation->update([
            'line_items' => $lineItems,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'amount' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'scope_of_work' => $request->scope_of_work,
            'terms_and_conditions' => $request->terms_and_conditions,
            'customer_notes' => $request->customer_notes,
            'valid_until' => $request->valid_until,
            'status' => $status,
            'sent_at' => $status === 'sent' ? now() : $quotation->sent_at,
            'rejection_reason' => null,
            // 'rejection_notes' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sync Pivot Tables
        |--------------------------------------------------------------------------
        */
        $quotation->commercialRoutes()->detach();

        foreach ($request->input('selected_routes', []) as $routeId) {
            $routeItem = collect($lineItems)
                ->where('type', 'commercial_route')
                ->firstWhere('item_id', (int) $routeId);

            if (!$routeItem) {
                continue;
            }

            $quotation->commercialRoutes()->attach($routeId, [
                'quantity' => $routeItem['quantity'],
                'duration_months' => $routeItem['metadata']['duration_months'],
                'unit_price' => $routeItem['unit_price'],
                'total_price' => $routeItem['total'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $quotation->customRoutes()->detach();

        foreach ($request->input('selected_custom_routes', []) as $customRouteId) {
            $customItem = collect($lineItems)
                ->where('type', 'custom_route')
                ->firstWhere('item_id', (int) $customRouteId);

            if (!$customItem) {
                continue;
            }

            $quotation->customRoutes()->attach($customRouteId, [
                'monthly_cost' => $customItem['unit_price'],
                'capital_expenditure' => $customItem['metadata']['capital_expenditure'],
                'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $quotation->colocationServices()->detach();

        foreach ($request->input('selected_services', []) as $serviceId) {
            $serviceItem = collect($lineItems)
                ->where('type', 'colocation_service')
                ->firstWhere('item_id', $serviceId);

            if (!$serviceItem) {
                continue;
            }

            $quotation->colocationServices()->attach($serviceId, [
                'quantity' => $serviceItem['quantity'],
                'duration_months' => $serviceItem['metadata']['duration_months'],
                'unit_price' => $serviceItem['unit_price'],
                'total_price' => $serviceItem['total'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        return redirect()
            ->route('admin.quotations.show', $quotation)
            ->with('success', $status === 'sent'
                ? 'Quotation updated and sent successfully!'
                : 'Quotation updated successfully!'
            );

    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('Failed to update quotation', [
            'quotation_id' => $quotation->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return back()
            ->with('error', 'Failed to update quotation: ' . $e->getMessage())
            ->withInput();
    }
}

 public function customerIndex()
    {
        // Get the authenticated user
        $user = Auth::user();

        $quotations = Quotation::where('customer_id', $user->id)
             ->latest()
            ->paginate(10);

        return view('customer.quotations.index', compact('quotations'));
    }
    public function destroy(Quotation $quotation)
    {
        // Use policy for authorization
        // $this->authorize('delete', $quotation);

        if ($quotation->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft quotations can be deleted.'
            ], 422);
        }

        try {
            $quotationNumber = $quotation->quotation_number;

            DB::transaction(function () use ($quotation) {
                // Detach relationships
                $quotation->commercialRoutes()->detach();
                $quotation->colocationServices()->detach();

                Log::info('Quotation deleted', [
                    'quotation_id' => $quotation->id,
                    'quotation_number' => $quotation->quotation_number,
                    'deleted_by' => Auth::id()
                ]);

                $quotation->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Quotation deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting quotation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting quotation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomerDetails(Quotation $quotation)
{
    $this->authorize('view', $quotation);

    return response()->json([
        'name' => $quotation->customer->name,
        'email' => $quotation->customer->email,
        'phone' => $quotation->customer->phone,
    ]);
}

public function download(Quotation $quotation)
{
      // Load necessary relationships
    $quotation->load([
        'customer',
        'designRequest',
        'accountManager'
    ]);

    // Get line items - check if it's already an array or needs decoding
    $lineItems = $quotation->line_items;

    // If it's a string, decode it. If it's already an array, use it as is.
    if (is_string($lineItems)) {
        $lineItems = json_decode($lineItems, true) ?? [];
    } elseif (is_array($lineItems)) {
        // Already an array, use as is
    } else {
        $lineItems = [];
    }

    $items = collect($lineItems);

    // Group items by type
    $groupedItems = [
        'commercial_routes' => [],
        'colocation_services' => [],
        'custom_items' => []
    ];

    foreach ($items as $item) {
        $type = $item['type'] ?? 'custom_items';
        if (in_array($type, ['commercial_route', 'commercial_routes'])) {
            $groupedItems['commercial_routes'][] = $item;
        } elseif (in_array($type, ['colocation', 'colocation_services'])) {
            $groupedItems['colocation_services'][] = $item;
        } else {
            $groupedItems['custom_items'][] = $item;
        }
    }
if ($quotation->customRoutes && $quotation->customRoutes->count()) {
    foreach ($quotation->customRoutes as $customRoute) {

        $monthlyCost = (float) ($customRoute->pivot->monthly_cost ?? $customRoute->monthly_cost ?? 0);
        $capex = (float) ($customRoute->pivot->capital_expenditure ?? $customRoute->capital_expenditure ?? 0);
        $duration = (int) ($customRoute->contract_duration_months ?? 12);
        $cores = (int) ($customRoute->no_of_cores_required ?? 1);

        $total = ($monthlyCost * $duration) + $capex;

        $groupedItems['commercial_routes'][] = [
            'type' => 'custom_route',
            'item_id' => $customRoute->id,
            'description' => $customRoute->name_of_route . ' (Custom Route)',
            'quantity' => 1,
            'unit_price' => $monthlyCost,
            'total' => $total,
            'metadata' => [
                'route_id' => $customRoute->id,
                'route_name' => $customRoute->name_of_route,
                'route_code' => 'CUSTOM-' . $customRoute->id,
                'cores' => $cores,
                'monthly_cost' => $monthlyCost,
                'duration_months' => $duration,
                'technology_type' => $customRoute->tech_type,
                'distance_km' => $customRoute->approx_distance_km,
                'pickup_points' => $customRoute->route_description ?? 'Custom route',
                'link_class' => $customRoute->option,
                'capital_expenditure' => $capex,
                'is_custom_route' => true,
            ],
        ];
    }
}


    // Calculate totals
    $commercialRoutesTotal = collect($groupedItems['commercial_routes'])->sum('total') ?? 0;
    $colocationServicesTotal = collect($groupedItems['colocation_services'])->sum('total') ?? 0;
    $customItemsTotal = collect($groupedItems['custom_items'])->sum('total') ?? 0;

    // Create customer profile object
    $customerProfile = (object)[
        'name' => $quotation->customer->name ?? 'Customer',
        'company_name' => $quotation->customer->company_name ?? $quotation->customer->name ?? 'Customer',
        'address' => $quotation->customer->address ?? $quotation->customer->physical_address ?? 'Nairobi, Kenya',
        'postal_code' => $quotation->customer->postal_code ?? $quotation->customer->zip_code ?? '00100',
        'city' => $quotation->customer->city ?? 'Nairobi',
        'phone' => $quotation->customer->phone ?? '',
        'email' => $quotation->customer->email ?? '',
        'physical_address' => $quotation->customer->physical_address ?? $quotation->customer->address ?? 'Nairobi',
        'postal_address' => $quotation->customer->postal_address ?? $quotation->customer->address ?? 'P.O. Box, Nairobi',
        'company' => $quotation->customer->company_name ?? $quotation->customer->name ?? 'Customer',
    ];

    // Debug: Check what we have
    \Log::info('Line items data:', [
        'type' => gettype($quotation->line_items),
        'value' => $quotation->line_items,
        'processed' => $lineItems,
        'items_count' => $items->count(),
    ]);

    // Prepare data for PDF
    $data = [
        'quotation' => $quotation,
        'customerProfile' => $customerProfile,
        'groupedItems' => $groupedItems,
        'commercialRoutesTotal' => $commercialRoutesTotal,
        'colocationServicesTotal' => $colocationServicesTotal,
        'customItemsTotal' => $customItemsTotal,
        'company' => [
            'name' => 'THE KENYA POWER & LIGHTING COMPANY LIMITED',
            'address' => 'Stima Plaza, Kolobot Road, Parklands, Nairobi',
            'phone' => '+254 20 320 6000',
            'email' => 'info@kplc.co.ke',
            'logo' => public_path('images/logo.png'),
        ],
        'settings' => [
            'currency' => 'KES',
            'tax_rate' => $quotation->tax_rate ?? 16,
        ]
    ];

    // Generate PDF
    $pdf = Pdf::loadView('customer.quotations.pdf', $data);
    $pdf->setPaper('A4', 'portrait');

    $filename = 'quotation-' . $quotation->id . '-' . date('Y-m-d') . '.pdf';

    return $pdf->download($filename);
}

  public function customerShow(Quotation $quotation)
    {
        // Check if the authenticated user is the customer of this quotation
        if (Auth::id() !== $quotation->customer_id) {
            abort(403, 'Unauthorized action.');
        }

        // Load necessary relationships
        $quotation->load([
            'items',
            'designRequest',
            'accountManager',
            'contract'
        ]);

        return view('customer.quotations.show', compact('quotation'));
    }
    /**
     * Get quotation statistics for dashboard
     */
    public function getStatistics()
    {
        $this->authorize('viewAny', Quotation::class);

        $stats = [
            'total' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })->count(),
            'draft' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })->where('status', 'draft')->count(),
            'sent' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })->where('status', 'sent')->count(),
            'approved' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })->where('status', 'approved')->count(),
            'rejected' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->when(Auth::user()->role === 'designer', function($query) {
                return $query->whereHas('designRequest', function($q) {
                    $q->where('designer_id', Auth::id());
                });
            })->where('status', 'rejected')->count(),
        ];

        return response()->json($stats);
    }

     /**
     * Approve a quotation
     */
   public function approve(Request $request, Quotation $quotation)
{
    try {

        $validated = $request->validate([
            'notes'=>'nullable|string|max:500'
        ]);

        // Admin final approval ONLY after customer approval
        if($quotation->status !== 'customer_approved'){

            return response()->json([
                'success'=>false,
                'message'=>'Customer must approve quotation first.'
            ],422);
        }

        DB::transaction(function() use(
            $quotation,
            $validated
        ){

            $quotation->update([

                'status'=>'approved',

                'approved_by'=>Auth::id(),

                'approved_at'=>now(),

                'approval_notes'=>$validated['notes'] ?? null
            ]);

            activity()
            ->performedOn($quotation)
            ->causedBy(auth()->user())
            ->withProperties([
                'status'=>'approved'
            ])
            ->log('Quotation approved by admin');
        });

        return response()->json([
            'success'=>true,
            'message'=>'Quotation approved successfully'
        ]);

    } catch(\Exception $e){

        Log::error($e);

        return response()->json([
            'success'=>false,
            'message'=>$e->getMessage()
        ],500);
    }
}

    /**
     * Reject a quotation
     */
    public function reject(Request $request, Quotation $quotation)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            if (!auth()->user()->can('reject', $quotation)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to reject quotations.'
                ], 403);
            }

            if ($quotation->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft quotations can be rejected.'
                ], 422);
            }

            DB::transaction(function () use ($quotation, $validated) {
                $quotation->update([
                    'status' => 'rejected',
                    'rejected_by' => auth()->id(),
                    'rejected_at' => now(),
                    'rejection_reason' => $validated['reason'],
                    'updated_at' => now(),
                ]);

                activity()
                    ->performedOn($quotation)
                    ->causedBy(auth()->user())
                    ->withProperties(['status' => 'rejected'])
                    ->log('Quotation rejected');
            });

            return response()->json([
                'success' => true,
                'message' => 'Quotation rejected successfully.',
                'quotation' => $quotation->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Quotation rejection failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject quotation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send quotation to customer
     */
    public function send(
    Request $request,
    Quotation $quotation
)
{
    try{

        $validated=$request->validate([
            'email_notes'=>'nullable|string|max:500'
        ]);

        if($quotation->status!=='draft'){

            return response()->json([
                'success'=>false,
                'message'=>'Only draft quotations can be sent'
            ],422);
        }

        DB::transaction(function() use(
            $quotation,
            $validated
        ){

            $quotation->update([

                'status'=>'sent',

                'sent_at'=>now()

            ]);

            activity()
            ->performedOn($quotation)
            ->causedBy(auth()->user())
            ->withProperties([
                'status'=>'sent'
            ])
            ->log('Quotation sent');

            /*
            Mail::to(
               $quotation->customer->email
            )->send(
                new QuotationMail(
                    $quotation
                )
            );
            */
        });

        return response()->json([
            'success'=>true,
            'message'=>'Quotation sent successfully'
        ]);

    }catch(\Exception $e){

        Log::error($e);

        return response()->json([
            'success'=>false,
            'message'=>$e->getMessage()
        ]);
    }
}

     /**
     * Approve a quotation (customer)
     */
   public function customerApprove(Quotation $quotation)
{
    if (Auth::id() !== $quotation->customer_id) {
        abort(403, 'Unauthorized action.');
    }

    if ($quotation->status !== 'sent' || $quotation->customer_approval_status !== 'pending') {
        return redirect()
            ->route('customer.quotations.index')
            ->with('error', 'Quotation cannot be accepted.');
    }

    $quotation->update([
        'customer_approval_status' => 'approved',
        'customer_approved_at' => now(),
        'status' => 'customer_approved',
    ]);

    return redirect()
        ->route('customer.quotations.index')
        ->with('success', 'Quotation accepted successfully. It is now awaiting final approval.');
}

    /**
     * Reject a quotation (customer)
     */
    public function customerReject(
    Request $request,
    Quotation $quotation
)
{
    if(
        Auth::id() !=
        $quotation->customer_id
    ){
        abort(403);
    }

    $request->validate([
        'rejection_reason'=>
        'required|min:5'
    ]);

    $quotation->update([

        'customer_approval_status'
            =>'rejected',

        'customer_rejected_at'
            =>now(),

        'rejection_reason'
            =>$request->rejection_reason,

        'status'
            =>'customer_rejected'
    ]);

    return back()->with(
        'success',
        'Quotation rejected'
    );
}

public function storeCustomRoute(Request $request)
{
    $validated = $request->validate([
        'design_request_id' => 'required|exists:design_requests,id',
    'name_of_route' => 'required|string|max:255',
    'region' => 'required|string|max:100',
    'option' => 'required|in:Non Premium,Premium,Metro',
    'tech_type' => 'required|in:ADSS,OPGW,UG,OPGW/ADSS',
    'fiber_cores' => 'nullable|integer|min:1',
    'no_of_cores_required' => 'required|integer|min:1',
    'unit_cost_per_core_per_km_per_month' => 'required|numeric|min:0',
    'approx_distance_km' => 'required|numeric|min:0',
    'capital_expenditure' => 'nullable|numeric|min:0',
    'contract_duration_months' => 'required|integer|min:1|max:360',
    'currency' => 'required|in:USD,KES',
    'availability' => 'required|in:YES,NO',
    'route_description' => 'nullable|string',
    'design_notes' => 'nullable|string',
    ]);

    $validated['created_by'] = Auth::id();
$validated['capital_expenditure'] = $validated['capital_expenditure'] ?? 0;

CustomRoute::create($validated);

    return back()->with('success', 'Custom route created successfully.');
}

public function getMonthlyCostAttribute(): float
{
    return (float) $this->unit_cost_per_core_per_km_per_month
        * (float) $this->approx_distance_km
        * (int) $this->no_of_cores_required;
}

public function getTotalContractValueAttribute(): float
{
    return $this->monthly_cost * (int) $this->contract_duration_months
        + (float) $this->capital_expenditure;
}

public function review(Quotation $quotation)
{
    try {
        // Authorize the action
        $this->authorize('review', $quotation);

        // Get the design request
        $designRequest = $quotation->designRequest;

        if (!$designRequest) {
            return redirect()->route('admin.quotations.index')
                ->with('error', 'Design request not found for this quotation.');
        }

        // Get commercial routes grouped by option (MATCHING YOUR CREATE METHOD)
        $commercialRoutes = CommercialRoute::available()
            ->orderBy('option')
            ->orderBy('name_of_route')
            ->get()
            ->groupBy('option');

        // Get colocation services (MATCHING YOUR CREATE METHOD)
        $colocationServices = ColocationList::where('fibrestatus', 'active')->get();

        // Get custom routes for this design request
        $customRoutes = CustomRoute::where('design_request_id', $designRequest->id)
            ->latest()
            ->get();

        // Get counties
        $counties = DB::table('county')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // Get active colocation service instances
        $colocationInstances = ColocationService::where('design_request_id', $designRequest->id)
            ->where('status', 'active')
            ->get();

        // Default terms (MATCHING YOUR CREATE METHOD)
        $defaultTerms = "TERMS AND CONDITIONS:\n\n1. PAYMENT TERMS:\n   • Net 30 days from invoice date\n   • Late payments subject to 1.5% monthly interest\n   • All prices in USD unless specified\n\n2. VALIDITY:\n   • Quotation valid for 30 days from issue date\n   • Prices subject to change after validity period\n\n3. FIBRE LEASE SPECIFIC:\n   • Minimum contract period: 12 months\n   • Core assignment subject to availability\n   • Installation timeline: 30-60 days after approval\n   • Monthly billing in advance\n\n4. COLOCATION SPECIFIC:\n   • Minimum contract period: 12 months\n   • Setup fees: One-time payment\n   • Power consumption billed separately\n   • 24/7 access with prior notice";

        // Log for debugging
        \Log::info('Review page loaded', [
            'quotation_id' => $quotation->id,
            'commercial_routes_count' => $commercialRoutes->count(),
            'colocation_services_count' => $colocationServices->count(),
            'custom_routes_count' => $customRoutes->count()
        ]);

        return view('admin.quotations.review', compact(
            'quotation',
            'designRequest',
            'commercialRoutes',
            'colocationServices',
            'colocationInstances',
            'defaultTerms',
            'customRoutes',
            'counties'
        ));

    } catch (\Exception $e) {
        \Log::error('Error in review method: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return redirect()->route('admin.quotations.index')
            ->with('error', 'Error loading review page: ' . $e->getMessage());
    }
}
}
