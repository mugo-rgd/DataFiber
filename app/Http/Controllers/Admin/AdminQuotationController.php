<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QuotationSentToCustomer;
use App\Models\ColocationList;
use App\Models\DesignRequest;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Models\CommercialRoute;
use App\Models\ColocationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class AdminQuotationController extends Controller
{
     use AuthorizesRequests;
   public function index()
{
    if (Auth::user()->role === 'designer') {
        return redirect()->route('designer.quotations.index');
    }

    // Rest of the authorization and logic
    $this->authorize('viewAny', Quotation::class);
    $quotations = Quotation::with(['designRequest', 'customer', 'accountManager'])
        ->when(Auth::user()->role === 'account_manager', function($query) {
            return $query->where('account_manager_id', Auth::id());
        })
        ->when(request('status'), function($query, $status) {
            return $query->where('status', $status);
        })
        ->latest()
        ->paginate(10);

    // If you want to pass stats to the view, you can add:
    $stats = [
        'total' => $quotations->total(),
        'draft' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
            return $query->where('account_manager_id', Auth::id());
        })->where('status', 'draft')->count(),
        'sent' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
            return $query->where('account_manager_id', Auth::id());
        })->where('status', 'sent')->count(),
        'approved' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
            return $query->where('account_manager_id', Auth::id());
        })->where('status', 'approved')->count(),
    ];

    return view('designer.quotations.index', compact('quotations', 'stats'));
}

    public function create(Request $request)
    {
        if (!Gate::allows('create-quotations')) {
            abort(403, 'Unauthorized action.');
        }

        $designRequestId = $request->input('design_request_id');

        if (!$designRequestId) {
            return redirect()->route('admin.design-requests.index')
                ->with('error', 'Design request ID is required.');
        }

        $designRequest = DesignRequest::with(['customer'])->findOrFail($designRequestId);

        // Check if quotation already exists
        $existingQuotation = Quotation::where('design_request_id', $designRequestId)->first();

        if ($existingQuotation) {
            return redirect()->route('admin.quotations.show', $existingQuotation)
                ->with('info', 'A quotation already exists for this design request.');
        }

        // Get commercial routes and colocation services for the form
        $commercialRoutes = CommercialRoute::where('availability', 'YES')->get();
        $colocationServices = ColocationService::all();

            $defaultTerms = "TERMS AND CONDITIONS:\n\n1. PAYMENT TERMS:\n   • Net 30 days from invoice date\n   • Late payments subject to 1.5% monthly interest\n   • All prices in USD unless specified\n\n2. VALIDITY:\n   • Quotation valid for 30 days from issue date\n   • Prices subject to change after validity period";

        return view('admin.quotations.create', compact('designRequest', 'commercialRoutes', 'colocationServices','defaultTerms'));
    }

    public function store(Request $request)
    {
        if (Gate::allows('create-quotations')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
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
            'selected_services' => 'nullable|array',
            'selected_services.*' => 'exists:colocation_list,service_id',
            'service_duration' => 'nullable|array',
            'service_quantity' => 'nullable|array',
            'custom_items' => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $designRequest = DesignRequest::findOrFail($request->design_request_id);

                // Build line items array
                $lineItems = [];
                $subtotal = 0;

                // Process commercial routes
                if ($request->selected_routes) {
                    foreach ($request->selected_routes as $routeId) {
                        $route = CommercialRoute::find($routeId);
                        $cores = $request->route_cores[$routeId] ?? $route->no_of_cores_required;
                        $duration = $request->route_duration[$routeId] ?? 12;

                        $monthlyCost = $route->calculateMonthlyCost($cores);
                        $totalCost = $monthlyCost * $duration;

                        $lineItems[] = [
                            'type' => 'commercial_route',
                            'description' => $route->name_of_route . " ({$cores} cores, {$duration} months)",
                            'quantity' => 1,
                            'unit_price' => $monthlyCost,
                            'total' => $totalCost,
                            'metadata' => [
                                'route_id' => $route->id,
                                'cores' => $cores,
                                'duration_months' => $duration,
                                'monthly_cost' => $monthlyCost
                            ]
                        ];

                        $subtotal += $totalCost;
                    }
                }

                // Process colocation services
                if ($request->selected_services) {
                    foreach ($request->selected_services as $serviceId) {
                        $service = ColocationService::where('service_number', $serviceId)->first();
                        $duration = $request->service_duration[$serviceId] ?? ($service->min_contract_months ?? 12);
                        $quantity = $request->service_quantity[$serviceId] ?? 1;

                        $monthlyCost = $service->monthly_price_usd * $quantity;
                        $setupCost = $service->setup_fee_usd * $quantity;
                        $totalCost = ($monthlyCost * $duration) + $setupCost;

                        $lineItems[] = [
                            'type' => 'colocation_service',
                            'description' => $service->service_type . " (Qty: {$quantity}, {$duration} months)",
                            'quantity' => $quantity,
                            'unit_price' => $monthlyCost,
                            'total' => $totalCost,
                            'metadata' => [
                                'service_id' => $service->service_id,
                                'duration_months' => $duration,
                                'quantity' => $quantity,
                                'setup_fee' => $setupCost
                            ]
                        ];

                        $subtotal += $totalCost;
                    }
                }

                // Process custom items
                if ($request->custom_items) {
                    foreach ($request->custom_items as $customItem) {
                        if (!empty($customItem['description']) && !empty($customItem['unit_price'])) {
                            $quantity = $customItem['quantity'] ?? 1;
                            $unitPrice = $customItem['unit_price'];
                            $itemTotal = $quantity * $unitPrice;

                            $lineItems[] = [
                                'type' => 'custom_item',
                                'description' => $customItem['description'],
                                'quantity' => $quantity,
                                'unit_price' => $unitPrice,
                                'total' => $itemTotal,
                                'metadata' => [
                                    'custom_item' => true
                                ]
                            ];

                            $subtotal += $itemTotal;
                        }
                    }
                }

                // Calculate tax and total
                $taxRate = $request->tax_rate;
                $taxAmount = $subtotal * $taxRate;
                $totalAmount = $subtotal + $taxAmount;

                // Determine status based on user role and action
                $status = 'draft';
                $sentAt = null;

                if (Auth::user()->role === 'admin' && $request->action === 'send') {
                    $status = 'sent';
                    $sentAt = now();
                }

                // Create quotation
                $quotation = Quotation::create([
                    'design_request_id' => $designRequest->id,
                    'customer_id' => $designRequest->customer_id,
                    'account_manager_id' => Auth::id(),
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
                    'sent_at' => $sentAt,
                ]);

                // Attach commercial routes with details
                if ($request->selected_routes) {
                    foreach ($request->selected_routes as $routeId) {
                        $route = CommercialRoute::find($routeId);
                        $cores = $request->route_cores[$routeId] ?? $route->no_of_cores_required;
                        $duration = $request->route_duration[$routeId] ?? 12;
                        $monthlyCost = $route->calculateMonthlyCost($cores);

                        $quotation->commercialRoutes()->attach($route->id, [
                            'quantity' => $cores,
                            'unit_price' => $monthlyCost,
                            'total_price' => $monthlyCost * $duration,
                            'duration_months' => $duration
                        ]);
                    }
                }

                // Attach colocation services with details
                if ($request->selected_services) {
                    foreach ($request->selected_services as $serviceId) {
                        $service = ColocationService::where('service_id', $serviceId)->first();
                        $duration = $request->service_duration[$serviceId] ?? ($service->min_contract_months ?? 12);
                        $quantity = $request->service_quantity[$serviceId] ?? 1;
                        $monthlyCost = $service->monthly_price_usd * $quantity;
                        $totalCost = ($monthlyCost * $duration) + ($service->setup_fee_usd * $quantity);

                        $quotation->colocationServices()->attach($service->service_id, [
                            'quantity' => $quantity,
                            'unit_price' => $monthlyCost,
                            'total_price' => $totalCost,
                            'duration_months' => $duration
                        ]);
                    }
                }

                // Log the creation
                Log::info('Quotation created', [
                    'quotation_id' => $quotation->id,
                    'quotation_number' => $quotation->quotation_number,
                    'created_by' => Auth::id(),
                    'status' => $status,
                    'total_amount' => $totalAmount
                ]);
            });

            $message = Auth::user()->role === 'admin' && $request->action === 'send'
                ? 'Quotation created and sent to customer successfully!'
                : 'Quotation created successfully!';

            return redirect()->route('admin.quotations.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error creating quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Quotation $quotation)
    {
        if (!Gate::allows('view-quotations')) {
            abort(403, 'Unauthorized action.');
        }

        // Account managers can only view their own quotations
        if (Auth::user()->role === 'account_manager' && $quotation->account_manager_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->load(['designRequest', 'customer', 'accountManager', 'commercialRoutes', 'colocationServices']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        if (!Gate::allows('edit-quotations', $quotation)) {
            abort(403, 'Unauthorized action.');
        }

        if ($quotation->status !== 'draft') {
            return redirect()->route('admin.quotations.show', $quotation)
                ->with('error', 'Only draft quotations can be edited.');
        }

        $quotation->load(['designRequest.customer', 'commercialRoutes', 'colocationServices']);
        $commercialRoutes = CommercialRoute::where('availability', 'YES')->get();
        $colocationServices = ColocationService::all();

        return view('admin.quotations.edit', compact('quotation', 'commercialRoutes', 'colocationServices'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if (!Gate::allows('edit-quotations', $quotation)) {
            abort(403, 'Unauthorized action.');
        }

        if ($quotation->status !== 'draft') {
            return redirect()->route('admin.quotations.show', $quotation)
                ->with('error', 'Only draft quotations can be updated.');
        }

        $request->validate([
            'scope_of_work' => 'required|string|max:2000',
            'terms_and_conditions' => 'required|string|max:2000',
            'customer_notes' => 'nullable|string|max:1000',
            'valid_until' => 'required|date|after:today',
            'tax_rate' => 'required|numeric|min:0|max:0.5',
            'selected_routes' => 'nullable|array',
            'selected_routes.*' => 'exists:commercial_routes,id',
            'route_cores' => 'nullable|array',
            'route_duration' => 'nullable|array',
            'selected_services' => 'nullable|array',
            'selected_services.*' => 'exists:colocation_list,service_id',
            'service_duration' => 'nullable|array',
            'service_quantity' => 'nullable|array',
            'custom_items' => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($request, $quotation) {
                // Build line items array
                $lineItems = [];
                $subtotal = 0;

                // Process commercial routes
                if ($request->selected_routes) {
                    foreach ($request->selected_routes as $routeId) {
                        $route = CommercialRoute::find($routeId);
                        $cores = $request->route_cores[$routeId] ?? $route->no_of_cores_required;
                        $duration = $request->route_duration[$routeId] ?? 12;

                        $monthlyCost = $route->calculateMonthlyCost($cores);
                        $totalCost = $monthlyCost * $duration;

                        $lineItems[] = [
                            'type' => 'commercial_route',
                            'description' => $route->name_of_route . " ({$cores} cores, {$duration} months)",
                            'quantity' => 1,
                            'unit_price' => $monthlyCost,
                            'total' => $totalCost,
                            'metadata' => [
                                'route_id' => $route->id,
                                'cores' => $cores,
                                'duration_months' => $duration,
                                'monthly_cost' => $monthlyCost
                            ]
                        ];

                        $subtotal += $totalCost;
                    }
                }

                // Process colocation services
                if ($request->selected_services) {
                    foreach ($request->selected_services as $serviceId) {
                        $service = ColocationService::where('service_number', $serviceId)->first();
                        $duration = $request->service_duration[$serviceId] ?? ($service->min_contract_months ?? 12);
                        $quantity = $request->service_quantity[$serviceId] ?? 1;

                        $monthlyCost = $service->monthly_price_usd * $quantity;
                        $setupCost = $service->setup_fee_usd * $quantity;
                        $totalCost = ($monthlyCost * $duration) + $setupCost;

                        $lineItems[] = [
                            'type' => 'colocation_service',
                            'description' => $service->service_type . " (Qty: {$quantity}, {$duration} months)",
                            'quantity' => $quantity,
                            'unit_price' => $monthlyCost,
                            'total' => $totalCost,
                            'metadata' => [
                                'service_id' => $service->service_id,
                                'duration_months' => $duration,
                                'quantity' => $quantity,
                                'setup_fee' => $setupCost
                            ]
                        ];

                        $subtotal += $totalCost;
                    }
                }

                // Process custom items
                if ($request->custom_items) {
                    foreach ($request->custom_items as $customItem) {
                        if (!empty($customItem['description']) && !empty($customItem['unit_price'])) {
                            $quantity = $customItem['quantity'] ?? 1;
                            $unitPrice = $customItem['unit_price'];
                            $itemTotal = $quantity * $unitPrice;

                            $lineItems[] = [
                                'type' => 'custom_item',
                                'description' => $customItem['description'],
                                'quantity' => $quantity,
                                'unit_price' => $unitPrice,
                                'total' => $itemTotal,
                                'metadata' => [
                                    'custom_item' => true
                                ]
                            ];

                            $subtotal += $itemTotal;
                        }
                    }
                }

                // Calculate tax and total
                $taxRate = $request->tax_rate;
                $taxAmount = $subtotal * $taxRate;
                $totalAmount = $subtotal + $taxAmount;

                // Update quotation
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
                ]);

                // Sync commercial routes
                $quotation->commercialRoutes()->detach();
                if ($request->selected_routes) {
                    foreach ($request->selected_routes as $routeId) {
                        $route = CommercialRoute::find($routeId);
                        $cores = $request->route_cores[$routeId] ?? $route->no_of_cores_required;
                        $duration = $request->route_duration[$routeId] ?? 12;
                        $monthlyCost = $route->calculateMonthlyCost($cores);

                        $quotation->commercialRoutes()->attach($route->id, [
                            'quantity' => $cores,
                            'unit_price' => $monthlyCost,
                            'total_price' => $monthlyCost * $duration,
                            'duration_months' => $duration
                        ]);
                    }
                }

                // Sync colocation services
                $quotation->colocationServices()->detach();
                if ($request->selected_services) {
                    foreach ($request->selected_services as $serviceId) {
                        $service = ColocationService::where('service_number', $serviceId)->first();
                        $duration = $request->service_duration[$serviceId] ?? ($service->min_contract_months ?? 12);
                        $quantity = $request->service_quantity[$serviceId] ?? 1;
                        $monthlyCost = $service->monthly_price_usd * $quantity;
                        $totalCost = ($monthlyCost * $duration) + ($service->setup_fee_usd * $quantity);

                        $quotation->colocationServices()->attach($service->service_id, [
                            'quantity' => $quantity,
                            'unit_price' => $monthlyCost,
                            'total_price' => $totalCost,
                            'duration_months' => $duration
                        ]);
                    }
                }

                Log::info('Quotation updated', [
                    'quotation_id' => $quotation->id,
                    'quotation_number' => $quotation->quotation_number,
                    'updated_by' => Auth::id(),
                    'total_amount' => $totalAmount
                ]);
            });

            return redirect()->route('admin.quotations.show', $quotation)
                ->with('success', 'Quotation updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Quotation $quotation)
    {
        if (!Gate::allows('edit-quotations', $quotation)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

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

    /**
     * Get quotation statistics for dashboard
     */
    public function getStatistics()
    {
        $stats = [
            'total' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->count(),
            'draft' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->draft()->count(),
            'sent' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->sent()->count(),
            'approved' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->approved()->count(),
            'rejected' => Quotation::when(Auth::user()->role === 'account_manager', function($query) {
                return $query->where('account_manager_id', Auth::id());
            })->rejected()->count(),
        ];

        return response()->json($stats);
    }


    ///////////////
    /**
 * Approve a quotation (Admin only) - Only draft quotations can be approved
 */
public function approve(Quotation $quotation, Request $request)
{

     \Log::info('Approval attempt', [
        'user_id' => auth()->id(),
        'user_roles' => auth()->user()->roles->pluck('name'),
        'route' => request()->route()->getName(),
        'full_url' => request()->fullUrl()
    ]);
    $this->authorize('approve', $quotation);

    // Only allow approval of DRAFT quotations
    if ($quotation->status !== 'draft') {
        return response()->json([
            'success' => false,
            'message' => 'Only draft quotations can be approved. Current status: ' . $quotation->status
        ], 422);
    }

    try {
        DB::transaction(function () use ($quotation, $request) {
            $quotation->markAsApproved(Auth::id(), $request->notes);

            Log::info('Quotation approved from draft', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'approved_by' => Auth::id(),
                'notes' => $request->notes,
                'previous_status' => 'draft',
                'new_status' => 'approved'
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation approved successfully!'
        ]);

    } catch (\Exception $e) {
        Log::error('Error approving quotation: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error approving quotation: ' . $e->getMessage()
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
/**
 * Reject a quotation (Admin only) - Only draft quotations can be rejected
 */
public function reject(Quotation $quotation, Request $request)
{
    if (!Gate::allows('approve-quotations')) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action. Only admins can reject quotations.'
        ], 403);
    }

    // Only allow rejection of DRAFT quotations
    if ($quotation->status !== 'draft') {
        return response()->json([
            'success' => false,
            'message' => 'Only draft quotations can be rejected. Current status: ' . $quotation->status
        ], 422);
    }

    $request->validate([
        'notes' => 'required|string|max:500'
    ]);

    try {
        DB::transaction(function () use ($quotation, $request) {
            $quotation->markAsRejected(Auth::id(), $request->notes);

            Log::info('Quotation rejected from draft', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'rejected_by' => Auth::id(),
                'notes' => $request->notes,
                'previous_status' => 'draft',
                'new_status' => 'rejected'
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation rejected successfully!'
        ]);

    } catch (\Exception $e) {
        Log::error('Error rejecting quotation: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error rejecting quotation: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Send quotation to customer (Admin only) - Only draft quotations can be sent
 */
/**
 * Send quotation to customer (Admin only) - Only APPROVED quotations can be sent
 */
public function send(Quotation $quotation)
{
    if (!Gate::allows('send-quotations')) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action. Only admins can send quotations.'
        ], 403);
    }

    // CHANGED: Only allow sending of APPROVED quotations
    if ($quotation->status !== 'approved') {
        return response()->json([
            'success' => false,
            'message' => 'Only approved quotations can be sent to customers. Current status: ' . $quotation->status
        ], 422);
    }

    try {
        DB::transaction(function () use ($quotation) {
            $quotation->markAsSent();

            Log::info('Quotation sent to customer', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'sent_by' => Auth::id(),
                'customer_id' => $quotation->customer_id,
                'previous_status' => 'approved',
                'new_status' => 'sent'
            ]);

            // TODO: Send email notification to customer
            // Mail::to($quotation->customer->email)->send(new QuotationSent($quotation));
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation sent to customer successfully!'
        ]);

    } catch (\Exception $e) {
        Log::error('Error sending quotation: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error sending quotation: ' . $e->getMessage()
        ], 500);
    }
}

 /**
 * Send quotation to customer
 */
public function sendToCustomer(Request $request, $id)
{
    try {
        $quotation = Quotation::with(['customer', 'designRequest'])->findOrFail($id);

        // Update quotation status
        $quotation->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Send email to customer with better error handling
        $emailSent = false;
        try {
            Mail::to($quotation->customer->email)->send(new QuotationSentToCustomer($quotation));
            $emailSent = true;
            Log::info('Quotation email sent successfully', ['quotation_id' => $quotation->id, 'customer_email' => $quotation->customer->email]);
        } catch (\Exception $e) {
            Log::warning('Failed to send quotation email: ' . $e->getMessage(), [
                'quotation_id' => $quotation->id,
                'customer_email' => $quotation->customer->email,
                'error' => $e->getMessage()
            ]);
            // Continue even if email fails - don't break the whole process
        }

        $message = 'Quotation has been sent to the customer successfully.';
        if ($emailSent) {
            $message .= ' Notification email was sent.';
        } else {
            $message .= ' (Email notification failed, but quotation was marked as sent)';
        }

        return redirect()->route('account-manager.quotations.show', $quotation->id)
            ->with('success', $message);

    } catch (\Exception $e) {
        Log::error('Error sending quotation to customer: ' . $e->getMessage());

        return redirect()->route('account-manager.quotations.show', $id)
            ->with('error', 'Failed to send quotation: ' . $e->getMessage());
    }
}

    /**
 * Update quotation status
 */
public function updateStatus(Request $request, $id)
{
    try {
        $quotation = Quotation::findOrFail($id);

        $request->validate([
            'status' => 'required|in:draft,sent,approved,rejected,expired',
        ]);

        $quotation->update([
            'status' => $request->status,
        ]);

        return redirect()->route('account-manager.quotations.show', $quotation->id)
            ->with('success', 'Quotation status updated successfully.');

    } catch (\Exception $e) {
        Log::error('Error updating quotation status: ' . $e->getMessage());

        return redirect()->route('account-manager.quotations.show', $id)
            ->with('error', 'Failed to update quotation status: ' . $e->getMessage());
    }
}

/**
 * Download PDF
 */
public function downloadPdf($id)
{
    try {
        $quotation = Quotation::with(['customer', 'items'])->findOrFail($id);

        // You'll need to implement PDF generation here
        // For now, return a placeholder response
        return response()->streamDownload(function () use ($quotation) {
            echo "PDF content for quotation #{$quotation->quotation_number}";
        }, "quotation-{$quotation->quotation_number}.pdf");

    } catch (\Exception $e) {
        Log::error('Error downloading quotation PDF: ' . $e->getMessage());

        return redirect()->route('account-manager.quotations.show', $id)
            ->with('error', 'Failed to download quotation PDF: ' . $e->getMessage());
    }

}
}
