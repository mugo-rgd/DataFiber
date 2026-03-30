<?php

namespace App\Http\Controllers;

use App\Models\ColocationService;
use App\Models\County;
use App\Models\DesignItem;
use App\Models\DesignRequest;
use App\Models\Quotation;
use App\Models\SurveyRoute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Requests\StoreCertificateRequest;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;

class DesignerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $designStats = [
            'pendingRequests' => DesignRequest::pending()->count(),
            'assignedRequests' => DesignRequest::where('designer_id', $user->id)
                ->whereIn('status', ['in_design', 'designed'])
                ->count(),
            'completedDesigns' => DesignRequest::where('designer_id', $user->id)
                ->where('status', 'designed')
                ->count(),
            'quotationsSent' => Quotation::whereHas('designRequest', function($query) use ($user) {
                $query->where('designer_id', $user->id);
            })->count(),
        ];

        $recentRequests = DesignRequest::with('customer')
            ->where('designer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('designer.dashboard', compact('designStats', 'recentRequests'));
    }

    public function requests()
    {
            $designRequest = DesignRequest::where('designer_id', auth()->id())->get();


    // return view('designer.requests', [
    //     'designRequests' => $designRequests, // This is a collection
    //     // NOT 'designRequest' (singular)
    // ]);
    $requests = DesignRequest::with('customer')
            ->where('designer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('designer.requests', compact('requests','designRequest'));
    }

    // In your AccountManagerController or DesignerController
public function updateStatus(DesignRequest $designRequest, Request $request)
{
    $request->validate([
        'status' => 'required|in:pending,assigned,designed,quoted,approved,rejected'
    ]);

    try {
        $designRequest->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update status: ' . $e->getMessage()
        ], 500);
    }
}
    public function show(DesignRequest $designRequest)
    {
    $designRequest->load(['customer', 'designer', 'designItems', 'quotation', 'surveyResults.surveyRoutes']);

    // Get available surveyed routes for this project type
    $availableRoutes = SurveyRoute::approved()
        ->active()
        ->when($designRequest->technical_requirements, function($query) use ($designRequest) {
            // Filter by project requirements
            if (str_contains(strtolower($designRequest->technical_requirements), 'aerial')) {
                $query->where('route_type', 'aerial');
            } elseif (str_contains(strtolower($designRequest->technical_requirements), 'underground')) {
                $query->where('route_type', 'underground');
            }
        })
        ->orderBy('route_name')
        ->get();

          // Load all necessary relationships
        $designRequest->load([
            'customer',
            'designer',
            'designItems',
            'quotation'
        ]);

        // return view('designer.requests.show', compact('designRequest'));
          return view('design-requests.show', compact('designRequest', 'availableRoutes'));


    }

    /**
 * Show a specific design request
 */
public function showRequest(DesignRequest $designRequest)
{
    // Load relationships
    $designRequest->load([
        'customer',
        'designer',
        'quotations',
        'colocationServices'
    ]);

    return view('designer.requests.show', compact('designRequest'));

}

    public function updateDesign(Request $request, DesignRequest $designRequest)
    {
        if ($designRequest->designer_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'design_specifications' => 'required|string',
            'design_notes' => 'nullable|string',
            'estimated_cost' => 'required|numeric|min:0',
        ]);

        $designRequest->update([
            'design_specifications' => $validated['design_specifications'],
            'design_notes' => $validated['design_notes'] ?? null,
            'estimated_cost' => $validated['estimated_cost'],
            'status' => 'designed',
            'design_completed_at' => now(),
        ]);

        return redirect()->route('designer.requests')->with('success', 'Design updated successfully!');
    }

public function createQuotation(DesignRequest $designRequest)
{
    try {
        // Check if quotation already exists
        if ($designRequest->quotation) {
            return redirect()->route('designer.quotations.show', $designRequest->quotation)
                ->with('info', 'Quotation already exists for this design request.');
        }

        // Calculate line items
        $lineItems = $this->calculateLineItems($designRequest);

        // Calculate totals
        $subtotal = collect($lineItems)->sum('amount');
        $taxRate = 0.16; // 16% VAT
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        // Generate unique quotation number
        $quotationNumber = 'QT-' . Carbon::now()->format('YmdHis') . '-' . Str::random(6);

        Log::info('Creating quotation with data:', [
            'design_request_id' => $designRequest->id,
            'quotation_number' => $quotationNumber,
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount
        ]);

        // Create quotation - make sure ALL required fields are included
        $quotation = Quotation::create([
            'design_request_id' => $designRequest->id,
            'quotation_number' => $quotationNumber,
            'line_items' => $lineItems,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'amount' => $totalAmount, // This is the critical missing field
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'scope_of_work' => 'Dark fibre connection services', // Required field
            'terms_and_conditions' => 'Standard terms and conditions apply', // Required field
            'valid_until' => Carbon::now()->addDays(30),
            'status' => 'draft',
        ]);

        Log::info('Quotation created successfully:', ['quotation_id' => $quotation->id]);

        // Update design request status
        $designRequest->update([
            'status' => 'quoted',
            'quoted_at' => now(),
            'quoted_amount' => $totalAmount,
        ]);

        Log::info('Design request updated successfully');

        return redirect()->route('designer.quotations.show', $quotation)
            ->with('success', 'Quotation created successfully!');

    } catch (\Exception $e) {
        Log::error('Failed to create quotation: ' . $e->getMessage(), [
            'design_request_id' => $designRequest->id,
            'exception' => $e
        ]);

        return redirect()->back()
            ->with('error', 'Failed to create quotation: ' . $e->getMessage());
    }
}

private function calculateLineItems(DesignRequest $designRequest)
{
    $lineItems = [];

    // Get design items and calculate costs
    $designItems = DesignItem::where('request_number', $designRequest->request_number)->get();

    foreach ($designItems as $item) {
        $unitPrice = $this->calculateUnitPrice($item);
        $quantity = $item->distance ?? 1; // Use distance or default to 1
        $amount = $unitPrice * $quantity;

        $lineItems[] = [
            'description' => $item->route_name ? "Dark Fibre - {$item->route_name}" : "Dark Fibre Connection",
            'quantity' => $quantity,
            'unit' => 'km',
            'unit_price' => $unitPrice,
            'amount' => $amount,
            'cores' => $item->cores_required ?? 1,
            'technology' => $item->technology_type ?? 'Standard',
            'route_name' => $item->route_name,
        ];
    }

    // If no design items, create a default line item
    if (empty($lineItems)) {
        $unitPrice = $this->calculateUnitPrice(null);
        $quantity = $designRequest->distance ?? 10; // Default distance
        $amount = $unitPrice * $quantity;

        $lineItems[] = [
            'description' => 'Dark Fibre Connection - ' . $designRequest->title,
            'quantity' => $quantity,
            'unit' => 'km',
            'unit_price' => $unitPrice,
            'amount' => $amount,
            'cores' => $designRequest->cores_required ?? 2,
            'technology' => $designRequest->technology_type ?? 'Standard',
            'route_name' => $designRequest->route_name,
        ];
    }

    return $lineItems;
}

private function calculateUnitPrice($designItem = null)
{
    $basePrice = 150; // $ per km

    // Add pricing logic based on specifications
    if ($designItem) {
        // Adjust price based on cores
        $cores = $designItem->cores_required ?? 1;
        if ($cores > 2) {
            $basePrice += ($cores - 2) * 50; // $50 extra per additional core
        }

        // Adjust price based on technology
        $technology = $designItem->technology_type ?? 'Standard';
        if ($technology === 'Premium') {
            $basePrice *= 1.2; // 20% premium
        } elseif ($technology === 'Enterprise') {
            $basePrice *= 1.5; // 50% premium
        }
    }

    return $basePrice;
}

    public function storeQuotation(Request $request, DesignRequest $designRequest)
    {
        if ($designRequest->designer_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'scope_of_work' => 'required|string',
            'terms_and_conditions' => 'required|string',
            'valid_until' => 'required|date|after:today',
        ]);

        $quotation = Quotation::create([
            'design_request_id' => $designRequest->id,
            'quotation_number' => 'QT-' . date('YmdHis'),
            'amount' => $validated['amount'],
            'tax_amount' => $validated['tax_amount'],
            'total_amount' => $validated['amount'] + $validated['tax_amount'],
            'scope_of_work' => $validated['scope_of_work'],
            'terms_and_conditions' => $validated['terms_and_conditions'],
            'valid_until' => $validated['valid_until'],
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $designRequest->update([
            'quoted_amount' => $quotation->total_amount,
            'status' => 'quoted',
            'quoted_at' => now(),
        ]);

        return redirect()->route('designer.requests')->with('success', 'Quotation sent successfully!');
    }

    public function quotations()
    {
        $quotations = Quotation::with('designRequest.customer')
            ->whereHas('designRequest', function($query) {
                $query->where('designer_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('designer.quotations', compact('quotations'));
    }

    public function manage()
    {
        try {
            $pendingRequests = DesignRequest::where('status', 'pending')
                ->with('customer')
                ->get();

            $assignedRequests = DesignRequest::where('status', 'assigned')
                ->with(['customer', 'designer'])
                ->get();

            $designers = User::where('role', 'designer')->get();

        } catch (\Exception $e) {
            // Fallback to empty collections if there's any error
            $pendingRequests = collect();
            $assignedRequests = collect();
            $designers = collect();

            Log::error('Error fetching design requests: ' . $e->getMessage());
        }

        return view('admin.design-requests.manage', compact(
            'pendingRequests',
            'assignedRequests',
            'designers'
        ));
    }

    public function assign(Request $request, DesignRequest $designRequest)
    {
        $request->validate([
            'designer_id' => 'required|exists:users,id'
        ]);

        $designRequest->update([
            'designer_id' => $request->designer_id,
            'status' => 'assigned',
            'assigned_at' => now()
        ]);

        return redirect()->back()->with('success', 'Designer assigned successfully!');
    }

    public function unassign(DesignRequest $designRequest)
    {
        $designRequest->update([
            'designer_id' => null,
            'status' => 'pending',
            'assigned_at' => null
        ]);

        return redirect()->back()->with('success', 'Designer unassigned successfully!');
    }

    public function showDesignRequest(DesignRequest $designRequest)
{
    // Load related models for display
    $designRequest->load([
        'customer',
        'designer',
        'designItems',
        'quotation',
    ]);

    return view('designer.requests.show', compact('designRequest'));
}

public function showQuotation(Quotation $quotation)
{
    // Check if the quotation belongs to the authenticated designer
    if ($quotation->designRequest->designer_id !== Auth::id()) {
        abort(403);
    }

    $quotation->load('designRequest.customer');

    return view('designer.quotations.show', compact('quotation'));
}

/**
 * Store a new colocation service
 */
public function storeColocation(Request $request)
{
    // Map customer_id to user_id if needed
    if ($request->has('customer_id') && !$request->has('user_id')) {
        $request->merge(['user_id' => $request->customer_id]);
    }

    $validated = $request->validate([
        'design_request_id' => 'required|exists:design_requests,id',
        'user_id' => 'required|exists:users,id',
        'service_type' => 'required|in:rack_space,cabinet,cage,private_suite',
        'rack_units' => 'required|integer|min:1',
        'service_area' => 'nullable|numeric|min:0',
        'location_reference' => 'required|string|max:50',
        'power_amps' => 'required|numeric|min:1',
        'network_ports' => 'required|integer|min:0',
        'port_speed' => 'required|string',
        'monthly_price' => 'required|numeric|min:0',
        'contract_months' => 'required|integer|min:1',
        'notes' => 'nullable|string',
    ]);

    // Generate service number
    $serviceNumber = 'COLO-' . date('YmdHis') . '-' . Str::upper(Str::random(6));

    // Calculate end date - ensure contract_months is cast to integer
    $startDate = now();
    $contractMonths = (int) $validated['contract_months'];
        /** @var \Carbon\Carbon $startDate */
$endDate = $startDate->copy()->addMonths($contractMonths);

    try {
        ColocationService::create([
            'service_number' => $serviceNumber,
            'user_id' => (int) $validated['user_id'],
            'design_request_id' => (int) $validated['design_request_id'],
            'service_type' => $validated['service_type'],
            'rack_units' => (int) $validated['rack_units'],
            'service_area' => $validated['service_area'] ?? null,
            'location_reference' => $validated['location_reference'],
            'power_amps' => (float) $validated['power_amps'],
            'power_type' => 'single_phase',
            'power_circuits' => 1,
            'network_ports' => (int) $validated['network_ports'],
            'port_speed' => $validated['port_speed'],
            'monthly_price' => (float) $validated['monthly_price'],
            'setup_fee' => 0,
            'billing_cycle' => 'monthly',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'contract_months' => $contractMonths,
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Colocation service created successfully.',
                'service_number' => $serviceNumber
            ]);
        }

        return redirect()->back()
    ->with('success', 'Colocation service created successfully.');

    } catch (\Exception $e) {
        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create colocation service: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'Failed to create colocation service: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Delete a colocation service
     */
    public function destroyColocation(ColocationService $colocationService)
    {
        // Check if the designer has permission to delete this colocation service
        $designRequest = $colocationService->designRequest;

        if (!$designRequest || $designRequest->designer_id !== Auth::id()) {
            return redirect()->back()
                ->with('error', 'You are not authorized to delete this colocation service.');
        }

        $colocationService->delete();

        return redirect()->back()
            ->with('success', 'Colocation service deleted successfully.');
    }
    // Add these methods to your DesignerController

public function profile()
{
    $user = Auth::user();
    return view('designer.profile', compact('user'));
}

public function updateRequestStatus(Request $request, DesignRequest $designRequest)
{
    if ($designRequest->designer_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'status' => 'required|in:in_design,designed,revision_requested'
    ]);

    $designRequest->update([
        'status' => $request->status,
        'designer_notes' => $request->designer_notes
    ]);

    return redirect()->back()->with('success', 'Request status updated successfully!');
}

public function uploadDesign(Request $request, DesignRequest $designRequest)
{
    if ($designRequest->designer_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'design_files' => 'required|array',
        'design_files.*' => 'file|mimes:pdf,jpg,jpeg,png,ai,eps,dwg|max:10240',
        'design_notes' => 'nullable|string'
    ]);

    // Handle file uploads here
    // You'll need to implement your file upload logic

    $designRequest->update([
        'status' => 'designed',
        'designer_notes' => $request->design_notes,
        'completed_at' => now()
    ]);

    return redirect()->back()->with('success', 'Design files uploaded successfully!');
}
public function storeConditionalCertificate(StoreCertificateRequest $request, $id)
{
    try {
        $designRequest = DesignRequest::findOrFail($id);

        // Handle file uploads
        $supportingDocumentPath = null;
        $inspectionReportPath = null;
        $photosPaths = [];

        if ($request->hasFile('supporting_document')) {
            $supportingDocumentPath = $request->file('supporting_document')->store('certificates/documents', 'public');
        }

        if ($request->hasFile('inspection_report')) {
            $inspectionReportPath = $request->file('inspection_report')->store('certificates/reports', 'public');
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photosPaths[] = $photo->store('certificates/photos', 'public');
            }
        }

        // Create certificate record
        $certificate = Certificate::create([
            'design_request_id' => $designRequest->id,
            'certificate_number' => $request->certificate_number,
            'type' => 'conditional',
            'issued_date' => $request->issued_date,
            'valid_until' => $request->valid_until,
            'inspector_name' => $request->inspector_name,
            'conditions' => $request->conditions,
            'remarks' => $request->remarks,
            'supporting_document' => $supportingDocumentPath,
            'inspection_report' => $inspectionReportPath,
            'photos' => $photosPaths ? json_encode($photosPaths) : null,
            'status' => $request->status,
            'generated_by' => auth()->id(),
        ]);

        // Update design request status if needed
        if ($request->status === 'issued') {
            $designRequest->update(['status' => 'conditionally_certified']);
        }

        return redirect()->back()
            ->with('success', 'Conditional certificate generated successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
    }
}

public function storeAcceptanceCertificate(StoreCertificateRequest $request, $id)
{
    try {
        $designRequest = DesignRequest::findOrFail($id);

        // Handle file uploads
        $acceptanceDocumentPath = null;
        $completionReportPath = null;
        $clientSignaturePath = null;

        if ($request->hasFile('acceptance_document')) {
            $acceptanceDocumentPath = $request->file('acceptance_document')->store('certificates/acceptance', 'public');
        }

        if ($request->hasFile('completion_report')) {
            $completionReportPath = $request->file('completion_report')->store('certificates/reports', 'public');
        }

        if ($request->hasFile('client_signature')) {
            $clientSignaturePath = $request->file('client_signature')->store('certificates/signatures', 'public');
        }

        // Create certificate record
        $certificate = Certificate::create([
            'design_request_id' => $designRequest->id,
            'certificate_number' => $request->certificate_number,
            'type' => 'acceptance',
            'acceptance_date' => $request->acceptance_date,
            'completion_date' => $request->completion_date,
            'accepted_by' => $request->accepted_by,
            'accepted_by_position' => $request->accepted_by_position,
            'warranty_period' => $request->warranty_period,
            'final_amount' => $request->final_amount,
            'terms_conditions' => $request->terms_conditions,
            'client_feedback' => $request->client_feedback,
            'acceptance_document' => $acceptanceDocumentPath,
            'completion_report' => $completionReportPath,
            'client_signature' => $clientSignaturePath,
            'status' => $request->status,
            'generated_by' => auth()->id(),
        ]);

        // Update design request status if needed
        if ($request->status === 'issued') {
            $designRequest->update(['status' => 'accepted']);
        }

        return redirect()->back()
            ->with('success', 'Acceptance certificate generated successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to generate acceptance certificate: ' . $e->getMessage());
    }
}

/**
 * Show ICT requests for assignment
 */
/**
 * Show ALL design requests for assignment to Kenya Power regions
 */
public function assignICTIndex()
{
    // Get ALL design requests with relationships
    $requests = DesignRequest::whereIn('status', ['pending', 'assigned', 'assigned_to_regional', 'ICT assigned'])
        ->with(['customer', 'county'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    // Kenya Power's 8 regions - based on your counties table
    $regions = [
        'Nairobi Region',
        'Coast Region',
        'Western Region',
        'Central Rift Region',
        'North Rift Region',
        'South Rift Region',
        'Mt. Kenya Region',
        'North Eastern Region',
        'Nyanza Region'
    ];

    // Get all regional engineers (not just ICT)
    $regionalEngineers = User::whereIn('role', ['engineer', 'regional_engineer', 'ict_engineer', 'designer'])
        ->where('status', true)
        ->with('county')
        ->orderBy('name')
        ->get();

    // Get all counties
    $counties = County::active()
        ->orderBy('name')
        ->get();

    // Get requests grouped by REGION through county relationship
    $regionRequests = [];

    // Initialize all regions with 0 count
    foreach ($regions as $region) {
        $regionRequests[$region] = 0;
    }

    // First, let's see what counties are actually being used
    $usedCountyIds = DesignRequest::whereIn('status', ['pending', 'assigned', 'assigned_to_regional', 'ICT assigned'])
        ->whereNotNull('county_id')
        ->distinct('county_id')
        ->pluck('county_id');

    \Log::info('Used county IDs: ' . $usedCountyIds->implode(', '));

    if ($usedCountyIds->count() > 0) {
        // Get the counties and their regions
        $usedCounties = County::whereIn('id', $usedCountyIds)->get(['id', 'name', 'region']);

        \Log::info('Used counties with regions:', $usedCounties->toArray());

        // Count requests for each of these counties
        foreach ($usedCounties as $county) {
            $count = DesignRequest::whereIn('status', ['pending', 'assigned', 'assigned_to_regional', 'ICT assigned'])
                ->where('county_id', $county->id)
                ->count();

            if (isset($regionRequests[$county->region])) {
                $regionRequests[$county->region] += $count;
            } else {
                $regionRequests[$county->region] = $count;
            }
        }
    }

    // Also count requests without county for statistics
    $requestsWithoutCounty = DesignRequest::whereIn('status', ['pending', 'assigned', 'assigned_to_regional', 'ICT assigned'])
        ->whereNull('county_id')
        ->count();

    \Log::info("Design requests WITHOUT county_id: " . $requestsWithoutCounty);
    \Log::info("Design requests WITH county_id: " . $usedCountyIds->count());
    \Log::info('Final region counts:', $regionRequests);

    // Statistics
    $totalRequestsCount = DesignRequest::count();
    $pendingRequestsCount = DesignRequest::whereIn('status', ['pending', 'assigned', 'assigned_to_regional'])->count();

    // Count ICT requests (requests not assigned to ICT yet)
    $ictRequestsCount = DesignRequest::where(function($query) {
            $query->whereNull('assigned_ict_engineer_id')
                  ->orWhereNull('ict_engineer_id');
        })
        ->whereIn('status', ['pending', 'assigned', 'assigned_to_regional'])
        ->count();

    return view('designer.ict-assign', compact(
        'requests',
        'regions',
        'regionalEngineers',
        'counties',
        'regionRequests',
        'totalRequestsCount',
        'pendingRequestsCount',
        'ictRequestsCount',
        'requestsWithoutCounty'
    ));
}
private function getRegionCounts()
{
    $regions = [
        'Nairobi Region' => ['Nairobi'],
        'Coast Region' => ['Mombasa', 'Kilifi', 'Kwale', 'Taita Taveta', 'Lamu', 'Tana River'],
        'Mt. Kenya Region' => ['Meru', 'Tharaka Nithi', 'Embu', 'Kirinyaga', 'Nyeri', 'Muranga', 'Kiambu', 'Nyandarua'],
        'North Rift Region' => ['Uasin Gishu', 'Trans Nzoia', 'Elgeyo Marakwet', 'Nandi', 'West Pokot', 'Turkana', 'Baringo', 'Samburu'],
        'South Rift Region' => ['Nakuru', 'Kajiado', 'Narok', 'Kericho', 'Bomet', 'Laikipia'],
        'Western Region' => ['Kakamega', 'Vihiga', 'Bungoma', 'Busia', 'Siaya', 'Kisumu', 'Homa Bay', 'Migori'],
        'North Eastern Region' => ['Garissa', 'Wajir', 'Mandera', 'Marsabit', 'Isiolo'],
        'Central Region' => ['Machakos', 'Makueni', 'Kitui']
    ];

    $counts = [];

    foreach ($regions as $region => $countyNames) {
        // Get county IDs for this region
        $countyIds = County::whereIn('name', $countyNames)
            ->pluck('id')
            ->toArray();

        // Count requests for these counties
        $count = DesignRequest::whereIn('status', ['pending', 'assigned'])
            ->whereIn('county_id', $countyIds)
            ->count();

        $counts[$region] = $count;
    }

    return $counts;
}


/**
 * Assign design request to regional engineer (for all requests)
 */
/**
 * Assign design request to regional ICT engineer
 */
public function assignICTRequest(Request $request)
{
    $validated = $request->validate([
        'request_ids' => 'required|string',
        'engineer_id' => 'required|exists:users,id',
        'county_id' => 'nullable|exists:county,id', // Add county validation
        'assignment_notes' => 'nullable|string',
        'priority' => 'nullable|in:normal,high,urgent',
        'is_bulk' => 'nullable|boolean'
    ]);

    $requestIds = explode(',', $validated['request_ids']);
    $engineerId = $validated['engineer_id'];
    $countyId = $validated['county_id'] ?? null;

    \Log::info('Starting ICT assignment', [
        'request_ids' => $requestIds,
        'engineer_id' => $engineerId,
        'county_id' => $countyId,
        'user_id' => auth()->id()
    ]);

    try {
        $assignedCount = 0;
        $failedCount = 0;
        $engineer = User::find($engineerId);
        $county = $countyId ? County::find($countyId) : null;

        if (!$engineer) {
            return response()->json([
                'success' => false,
                'message' => 'Engineer not found'
            ], 404);
        }

        foreach ($requestIds as $requestId) {
            try {
                // Use DB transaction for each update
                DB::transaction(function () use ($requestId, $engineerId, $countyId, $validated, &$assignedCount, &$failedCount, $engineer, $county) {
                    $designRequest = DesignRequest::lockForUpdate()->find($requestId);

                    if (!$designRequest) {
                        \Log::warning('Design request not found', ['request_id' => $requestId]);
                        $failedCount++;
                        return;
                    }

                    \Log::info('Before update', [
                        'id' => $designRequest->id,
                        'current_county_id' => $designRequest->county_id,
                        'current_ict_engineer_id' => $designRequest->ict_engineer_id
                    ]);

                    // Update using direct property assignment
                    $designRequest->ict_engineer_id = $engineerId;
                    $designRequest->assigned_ict_engineer_id = $engineerId;
                    $designRequest->assigned_to_ict_at = now();

                    // Update county if selected
                    if ($countyId) {
                        $designRequest->county_id = $countyId;
                    }

                    if (!empty($validated['assignment_notes'])) {
                        $designRequest->inspection_notes = $validated['assignment_notes'];
                    }

                    $designRequest->ict_status = 'assigned';
                    $designRequest->status = 'assigned';

                    // Save and check result
                    $saved = $designRequest->save();

                    if ($saved) {
                        // Refresh from database
                        $designRequest->refresh();

                        \Log::info('After update', [
                            'id' => $designRequest->id,
                            'new_county_id' => $designRequest->county_id,
                            'new_ict_engineer_id' => $designRequest->ict_engineer_id,
                            'new_ict_status' => $designRequest->ict_status,
                            'new_status' => $designRequest->status
                        ]);

                        $assignedCount++;

                        // Log success
                        \Log::info('Design request assigned', [
                            'design_request_id' => $designRequest->id,
                            'design_request_number' => $designRequest->request_number,
                            'assigned_to_id' => $engineerId,
                            'assigned_to_name' => $engineer->name,
                            'county_id' => $countyId,
                            'county_name' => $county ? $county->name : 'N/A',
                            'ict_status' => $designRequest->ict_status
                        ]);
                    } else {
                        \Log::error('Failed to save design request', ['id' => $designRequest->id]);
                        $failedCount++;
                    }
                });

            } catch (\Exception $e) {
                \Log::error('Error processing request', [
                    'request_id' => $requestId,
                    'error' => $e->getMessage()
                ]);
                $failedCount++;
            }
        }

        $message = "Successfully assigned {$assignedCount} request(s) to {$engineer->name}";
        if ($county) {
            $message .= " for {$county->name} county";
        }
        if ($failedCount > 0) {
            $message .= ". Failed to assign {$failedCount} request(s)";
        }

        \Log::info('Assignment completed', [
            'assigned_count' => $assignedCount,
            'failed_count' => $failedCount
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'assigned_count' => $assignedCount,
            'failed_count' => $failedCount
        ]);

    } catch (\Exception $e) {
        \Log::error('Failed to assign design requests', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to assign requests: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * API endpoint to get regional engineer details
 */
public function getRegionalEngineerDetails($id)
{
    $engineer = User::findOrFail($id);

    return response()->json([
        'name' => $engineer->name,
        'email' => $engineer->email,
        'phone' => $engineer->phone,
        'role' => $engineer->role,
        'county_id' => $engineer->county_id,
        'specialization' => $engineer->specialization
    ]);
 }
}
