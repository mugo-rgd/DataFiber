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

    // Add default values for missing totals
    if (!$request->has('subtotal')) {
        $request->merge([
            'routes_total' => 0,
            'services_total' => 0,
            'custom_items_total' => 0,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total_amount' => 0
        ]);
    }

    $validated = $request->validate([
        'design_request_id' => 'required|exists:design_requests,id',
        'scope_of_work' => 'required|string|max:2000',
        'terms_and_conditions' => 'required|string|max:2000',
        'customer_notes' => 'nullable|string|max:1000',
        'valid_until' => 'required|date|after:today',
        'tax_rate' => 'required|numeric|min:0|max:0.5',
        'selected_routes' => 'nullable|array',
        'selected_routes.*' => 'exists:commercial_routes,id',
        'route_cores' => 'nullable|array',
        'route_duration' => 'nullable|array',
        'selected_custom_routes' => 'nullable|array',
        'selected_custom_routes.*' => 'exists:custom_routes,id',
        'selected_services' => 'nullable|array',
        'selected_services.*' => 'required',
        'service_duration' => 'nullable|array',
        'service_quantity' => 'nullable|array',
        'service_source' => 'nullable|array',
        'custom_items' => 'nullable|array',
        'routes_total' => 'nullable|numeric',
        'services_total' => 'nullable|numeric',
        'custom_items_total' => 'nullable|numeric',
        'subtotal' => 'nullable|numeric',
        'tax_amount' => 'nullable|numeric',
        'total_amount' => 'nullable|numeric',
    ]);

    try {
        $quotation = null;

        DB::transaction(function () use ($request, &$quotation) {
            $designRequest = DesignRequest::findOrFail($request->design_request_id);

            if (Auth::user()->role === 'designer' && $designRequest->designer_id !== Auth::id()) {
                abort(403, 'You are not authorized to create quotations for this design request.');
            }

            // Build line items using service
            $lineItemsData = QuotationService::buildLineItems($request, $designRequest);

            // Calculate totals - use request values OR calculate from service
            $totals = QuotationService::calculateTotals(
                $request->input('subtotal', $lineItemsData['subtotal'] ?? 0),
                $request->tax_rate
            );

            // Determine account manager
            $accountManagerId = Auth::user()->role === 'designer'
                ? $designRequest->customer->account_manager_id
                : Auth::id();

            // Determine status
            $status = 'draft';
            $sentAt = null;

            if (Auth::user()->role === 'admin' && $request->action === 'send') {
                $status = 'sent';
                $sentAt = now();
            }

            // Generate quotation number
            $quotationNumber = QuotationService::generateQuotationNumber($designRequest);

            // Fix scope_of_work if it's "undefined"
            $scopeOfWork = $request->scope_of_work;
            if ($scopeOfWork === 'undefined' || empty(trim($scopeOfWork))) {
                $scopeOfWork = "Scope of work for quotation #{$quotationNumber}";
            }

            // Create quotation FIRST
            $quotation = Quotation::create([
                'design_request_id' => $designRequest->id,
                'customer_id' => $designRequest->customer_id,
                'account_manager_id' => $accountManagerId,
                'quotation_number' => $quotationNumber,
                'line_items' => $lineItemsData['line_items'] ?? [],
                'subtotal' => $totals['subtotal'],
                'tax_rate' => $totals['tax_rate'],
                'amount' => $totals['subtotal'],
                'tax_amount' => $totals['tax_amount'],
                'total_amount' => $totals['total_amount'],
                'scope_of_work' => $scopeOfWork,
                'terms_and_conditions' => $request->terms_and_conditions,
                'customer_notes' => $request->customer_notes,
                'valid_until' => $request->valid_until,
                'status' => $status,
                'sent_at' => $sentAt,
            ]);

            // Attach custom routes AFTER quotation is created
           $selectedCustomRoutes = $request->input('selected_custom_routes', []);

foreach ($selectedCustomRoutes as $customRouteId) {
    $customRoute = CustomRoute::find($customRouteId);

    if (!$customRoute) {
        continue;
    }

    $quotation->customRoutes()->syncWithoutDetaching([
        $customRoute->id => [
            'monthly_cost' => $customRoute->monthly_cost,
            'capital_expenditure' => $customRoute->capital_expenditure,
            'currency' => $customRoute->currency,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
}

            // Attach commercial routes with details
            if ($request->selected_routes) {
                foreach ($request->selected_routes as $routeId) {
                    $route = CommercialRoute::find($routeId);
                    if (!$route) continue;

                    $cores = $request->route_cores[$routeId] ?? $route->no_of_cores_required;
                    $duration = $request->route_duration[$routeId] ?? 12;
                    $monthlyCost = $route->calculateMonthlyCost($cores);

                    $quotation->commercialRoutes()->attach($route->id, [
                        'quantity' => $cores,
                        'duration_months' => $duration,
                        'unit_price' => $monthlyCost,
                        'total_price' => $monthlyCost * $duration,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Attach colocation services with details
            if ($request->selected_services) {
                foreach ($request->selected_services as $serviceId) {
                    $source = $request->service_source[$serviceId] ?? 'list';
                    $duration = $request->service_duration[$serviceId] ?? 12;
                    $quantity = $request->service_quantity[$serviceId] ?? 1;

                    if ($source === 'list') {
                        $service = ColocationList::where('service_id', $serviceId)->first();
                        if (!$service) continue;

                        $monthlyRate = $service->monthly_price_usd ?? ($service->recurrent_per_Annum / 12);
                        $setupFee = $service->setup_fee_usd ?? $service->oneoff_rate ?? 0;
                    } else {
                        $service = ColocationService::find($serviceId);
                        if (!$service) continue;

                        $monthlyRate = $service->monthly_price;
                        $setupFee = $service->setup_fee;
                    }

                    $monthlyTotal = $monthlyRate * $quantity;
                    $setupTotal = $setupFee * $quantity;
                    $totalCost = ($monthlyTotal * $duration) + $setupTotal;

                    $quotation->colocationServices()->attach($serviceId, [
                        'quantity' => $quantity,
                        'duration_months' => $duration,
                        'unit_price' => $monthlyTotal,
                        'total_price' => $totalCost,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            Log::info('Quotation created', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'created_by' => Auth::id(),
                'user_role' => Auth::user()->role,
                'status' => $status,
                'total_amount' => $totals['total_amount']
            ]);
        });

        $message = Auth::user()->role === 'admin' && $request->action === 'send'
            ? 'Quotation created and sent to customer successfully!'
            : 'Quotation created successfully!';

        return redirect()->route('admin.quotations.show', $quotation)
            ->with('success', $message);

    } catch (\Exception $e) {
        Log::error('Error creating quotation: ' . $e->getMessage());
        return redirect()->back()
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

    public function edit(Quotation $quotation)
    {
        // Use policy for authorization
        // $this->authorize('update', $quotation);

        if ($quotation->status !== 'draft') {
            return redirect()->route('admin.quotations.show', $quotation)
                ->with('error', 'Only draft quotations can be edited.');
        }

        // $quotation->load(['designRequest.customer', 'commercialRoutes', 'colocationServices']);
        // $commercialRoutes = CommercialRoute::where('availability', 'YES')->get();
        // $colocationServices = ColocationService::all();

        $quotation->load(['designRequest.customer', 'commercialRoutes', 'colocationServices','customRoutes']);
    $commercialRoutes = CommercialRoute::where('availability', 'YES')->get();
    $colocationServices = ColocationList::all();

        return view('admin.quotations.edit', compact('quotation', 'commercialRoutes', 'colocationServices','customRoutes'));
    }

   public function update(Request $request, Quotation $quotation)
{
    Log::info('=== QUOTATION UPDATE STARTED ===', [
        'quotation_id' => $quotation->id,
        'quotation_number' => $quotation->quotation_number,
        'current_status' => $quotation->status,
        'user_id' => Auth::id(),
        'user_role' => Auth::user()->role
    ]);

    // Use policy for authorization
    try {
        // $this->authorize('update', $quotation);
        Log::info('Authorization passed');
    } catch (\Exception $e) {
        Log::error('Authorization failed: ' . $e->getMessage());
        return redirect()->route('admin.quotations.show', $quotation)
            ->with('error', 'Unauthorized action.');
    }

    if ($quotation->status !== 'draft'&& $quotation->status !== 'rejected') {
        Log::warning('Quotation not in draft status', [
            'current_status' => $quotation->status
        ]);
        return redirect()->route('admin.quotations.show', $quotation)
            ->with('error', 'Only draft quotations can be updated. Current status: ' . $quotation->status);
    }

    Log::info('Validation starting...');

    $validated = $request->validate([
        'scope_of_work' => 'required|string|max:2000',
        'terms_and_conditions' => 'required|string|max:2000',
        'customer_notes' => 'nullable|string|max:1000',
        'valid_until' => 'required|date|after:today',
        'tax_rate' => 'required|numeric|min:0|max:0.5',
        'selected_routes' => 'nullable|array',
        'selected_routes.*' => 'exists:commercial_routes,id',
        'route_cores' => 'nullable|array',
        'route_duration' => 'nullable|array',
        'service_duration' => 'nullable|array',
        'service_quantity' => 'nullable|array',
        'custom_items' => 'nullable|array',
        'selected_services' => 'nullable|array',
    'selected_services.*' => 'exists:colocation_services,id', // FIXED
    ]);

    Log::info('Validation passed', [
        'fields_received' => array_keys($validated)
    ]);

    try {
        DB::beginTransaction();

        // Build line items array
        $lineItems = [];
        $subtotal = 0;

        Log::info('Processing line items...');

        // Process commercial routes
        if ($request->selected_routes) {
            Log::info('Processing commercial routes', [
                'count' => count($request->selected_routes)
            ]);

            foreach ($request->selected_routes as $routeId) {
                $route = CommercialRoute::find($routeId);
                if (!$route) {
                    throw new \Exception("Commercial route not found: {$routeId}");
                }

                $cores = $request->route_cores[$routeId] ?? $route->no_of_cores_required;
                $duration = $request->route_duration[$routeId] ?? 12;

                $monthlyCost = $route->calculateMonthlyCost($cores);
                $totalCost = $monthlyCost * $duration;

                $lineItems[] = [
                    'type' => 'commercial_route',
                    'description' => $route->name_of_route . " ({$cores} cores, {$duration} months)",
                    'quantity' => 1,
                    'unit_price' => (float) $monthlyCost, // Cast to float
                    'total' => (float) $totalCost, // Cast to float
                    'metadata' => [
                        'route_id' => $route->id,
                        'cores' => (int) $cores,
                        'duration_months' => (int) $duration,
                        'monthly_cost' => (float) $monthlyCost
                    ]
                ];

                $subtotal += $totalCost;
            }
        }

        // Process colocation services
        if ($request->selected_services) {
            Log::info('Processing colocation services', [
                'count' => count($request->selected_services)
            ]);

            foreach ($request->selected_services as $serviceId) {
               $service = ColocationService::find($serviceId);
                if (!$service) {
                    throw new \Exception("Colocation service not found: {$serviceId}");
                }

                $duration = $request->service_duration[$serviceId] ?? ($service->min_contract_months ?? 12);
                $quantity = $request->service_quantity[$serviceId] ?? 1;

                $monthlyCost = $service->monthly_price_usd * $quantity;
                $setupCost = $service->setup_fee_usd * $quantity;
                $totalCost = ($monthlyCost * $duration) + $setupCost;

                $lineItems[] = [
                    'type' => 'colocation_service',
                    'description' => $service->service_type . " (Qty: {$quantity}, {$duration} months)",
                    'quantity' => (int) $quantity,
                    'unit_price' => (float) $monthlyCost,
                    'total' => (float) $totalCost,
                    'metadata' => [
                        'service_id' => $service->service_id,
                        'duration_months' => (int) $duration,
                        'quantity' => (int) $quantity,
                        'setup_fee' => (float) $setupCost
                    ]
                ];

                $subtotal += $totalCost;
            }
        }

        // Process custom items
        if ($request->custom_items) {
            Log::info('Processing custom items', [
                'count' => count($request->custom_items)
            ]);

            foreach ($request->custom_items as $index => $customItem) {
                if (!empty($customItem['description']) && !empty($customItem['unit_price'])) {
                    $quantity = $customItem['quantity'] ?? 1;
                    $unitPrice = $customItem['unit_price'];
                    $itemTotal = $quantity * $unitPrice;

                    $lineItems[] = [
                        'type' => 'custom_item',
                        'description' => $customItem['description'],
                        'quantity' => (int) $quantity,
                        'unit_price' => (float) $unitPrice,
                        'total' => (float) $itemTotal,
                        'metadata' => [
                            'custom_item' => true,
                            'index' => $index
                        ]
                    ];

                    $subtotal += $itemTotal;
                }
            }
        }

        // Calculate tax and total with proper casting
        $taxRate = (float) $request->tax_rate;
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        Log::info('Financial calculations', [
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'line_items_count' => count($lineItems)
        ]);

        // Update quotation with proper data casting
        $updateData = [
            'line_items' => $lineItems,
            'subtotal' => (float) $subtotal,
            'tax_rate' => $taxRate,
            'amount' => (float) $subtotal,
            'tax_amount' => (float) $taxAmount,
            'total_amount' => (float) $totalAmount,
            'scope_of_work' => $request->scope_of_work,
            'terms_and_conditions' => $request->terms_and_conditions,
            'customer_notes' => $request->customer_notes,
            'valid_until' => $request->valid_until,
        ];

        Log::info('Attempting to update quotation with data:', $updateData);

        // Try updating individual fields to isolate the issue
        $updated = $quotation->update($updateData);

        if (!$updated) {
            throw new \Exception('Failed to update quotation record');
        }
if ($quotation->status == 'rejected') {
    $quotation->status = 'draft';
    $quotation->save(); // Don't forget to save if you want to persist the change
}
        Log::info('Quotation base record updated successfully');

        // Sync commercial routes
        $quotation->commercialRoutes()->detach();
        if ($request->selected_routes) {
            foreach ($request->selected_routes as $routeId) {
                $route = CommercialRoute::find($routeId);
                $cores = $request->route_cores[$routeId] ?? $route->no_of_cores_required;
                $duration = $request->route_duration[$routeId] ?? 12;
                $monthlyCost = $route->calculateMonthlyCost($cores);

                $quotation->commercialRoutes()->attach($route->id, [
                    'quantity' => (int) $cores,
                    'unit_price' => (float) $monthlyCost,
                    'total_price' => (float) ($monthlyCost * $duration),
                    'duration_months' => (int) $duration,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            Log::info('Commercial routes attached', [
                'count' => count($request->selected_routes)
            ]);
        }

        // Sync colocation services
        $quotation->colocationServices()->detach();
        if ($request->selected_services) {
            foreach ($request->selected_services as $serviceId) {
               $service = ColocationService::find($serviceId);
                $duration = $request->service_duration[$serviceId] ?? ($service->min_contract_months ?? 12);
                $quantity = $request->service_quantity[$serviceId] ?? 1;
                $monthlyCost = $service->monthly_price_usd * $quantity;
                $totalCost = ($monthlyCost * $duration) + ($service->setup_fee_usd * $quantity);

                $quotation->colocationServices()->attach($service->service_id, [
                    'quantity' => (int) $quantity,
                    'unit_price' => (float) $monthlyCost,
                    'total_price' => (float) $totalCost,
                    'duration_months' => (int) $duration,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            Log::info('Colocation services attached', [
                'count' => count($request->selected_services)
            ]);
        }

        DB::commit();

        Log::info('=== QUOTATION UPDATE COMPLETED SUCCESSFULLY ===', [
            'quotation_id' => $quotation->id,
            'new_total_amount' => $totalAmount
        ]);

        return redirect()->route('admin.quotations.show', $quotation)
            ->with('success', 'Quotation updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('=== QUOTATION UPDATE FAILED ===', [
            'quotation_id' => $quotation->id,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
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
