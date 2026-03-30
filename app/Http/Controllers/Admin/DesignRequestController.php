<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcceptanceCertificate;
use App\Models\ColocationList;
use App\Models\DesignRequest;
use App\Models\User;
use App\Models\ColocationService;
use App\Models\ColocationSite;
use App\Models\ConditionalCertificate;
use App\Models\Contract;
use App\Models\Lease;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DesignRequestController extends Controller
{

    /**
     * Display a listing of design requests
     */
    public function index()
    {
        $pendingRequests = DesignRequest::with(['customer', 'designer', 'quotations'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $assignedRequests = DesignRequest::with(['customer', 'designer', 'quotations'])
            ->where('status', 'assigned')
            ->orderBy('assigned_at', 'desc')
            ->get();

        $inDesignRequests = DesignRequest::with(['customer', 'designer', 'quotations'])
            ->where('status', 'in_design')
            ->orderBy('assigned_at', 'desc')
            ->get();

        $designedRequests = DesignRequest::with(['customer', 'designer', 'quotations'])
            ->where('status', 'designed')
            ->orderBy('design_completed_at', 'desc')
            ->get();

        $designers = User::where('role', 'designer')->get();
        $surveyors = User::where('role', 'surveyor')->get();

        return view('admin.design-requests.index', compact(
            'pendingRequests',
            'assignedRequests',
            'inDesignRequests',
            'designedRequests',
            'designers',
            'surveyors'
        ));
    }

    /**
     * Show the form for creating a new design request
     */
    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $designers = User::where('role', 'designer')->get();
        $serviceCategories = ColocationService::distinct()->pluck('service_category');
        // $colocationServices = ColocationService::where('is_active', true)->get();
 $colocationServices = ColocationList::where('fibrestatus', 'Active')->get();
        return view('admin.design-requests.create', compact(
            'serviceCategories',
            'colocationServices',
            'customers',
            'designers'
        ));
    }

    /**
     * Store a newly created design request
     */
    public function store(Request $request): RedirectResponse
    {
        // COMPREHENSIVE REQUEST ANALYSIS
        Log::info('=== DESIGN REQUEST STORE - FULL REQUEST ANALYSIS ===');
        Log::info('All request keys:', array_keys($request->all()));

        // Debug colocation sites specifically
        Log::info('Raw colocation_sites from request:', [
            'exists' => $request->has('colocation_sites'),
            'data' => $request->colocation_sites,
            'type' => gettype($request->colocation_sites)
        ]);

        // ✅ ADD THIS DEEP DEBUG TO SEE EXACT REQUEST STRUCTURE
        Log::info('=== DEEP REQUEST ANALYSIS ===');
        Log::info('Full request data:', $request->all());
        // Check for nested array structure
        if ($request->has('colocation_sites')) {
            Log::info('colocation_sites structure analysis:', [
                'type' => gettype($request->colocation_sites),
                'is_array' => is_array($request->colocation_sites),
                'first_element' => $request->colocation_sites[0] ?? 'NOT_SET',
                'keys' => array_keys($request->colocation_sites)
            ]);

            foreach ($request->colocation_sites as $index => $site) {
                Log::info("Site {$index} structure:", [
                    'type' => gettype($site),
                    'is_array' => is_array($site),
                    'site_data' => $site
                ]);
            }
        }

        // FILTER OUT EMPTY SITES BEFORE VALIDATION
        $filteredRequest = $request; // Use original request

        // ✅ ADD THIS CRITICAL DEBUG
        Log::info('🎯 IMMEDIATE POST-FILTER CHECK', [
            'has_colocation_sites' => $filteredRequest->has('colocation_sites'),
            'filtered_sites_data' => $filteredRequest->colocation_sites,
            'filtered_sites_count' => count($filteredRequest->colocation_sites ?? [])
        ]);

        // Validate the FILTERED request
        $validated = $filteredRequest->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'customer_id' => 'required|exists:users,id',
            'technical_requirements' => 'required|string',
            'designer_id' => 'nullable|exists:users,id',
            'cores_required' => 'nullable|integer|min:1',
            'technology_type' => 'nullable|string|max:255',
            'link_class' => 'nullable|string|max:255',
            'route_points' => 'nullable|json',
            'distance' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
            'colocation_sites' => 'nullable|array',
            'colocation_sites.*.site_name' => 'required|string|max:255',
            'colocation_sites.*.service_type' => 'required|string|in:shelter_space,rack,cage,suites',
        ]);

        // ✅ ADD THIS DEBUG TO SEE VALIDATION RESULTS
        Log::info('🎯 POST-VALIDATION CHECK', [
            'validated_has_sites' => isset($validated['colocation_sites']),
            'validated_sites_count' => count($validated['colocation_sites'] ?? []),
            'validated_sites' => $validated['colocation_sites'] ?? 'NOT_SET'
        ]);
        Log::info('=== AFTER VALIDATION ANALYSIS ===');
        Log::info('Validated colocation_sites:', $validated['colocation_sites'] ?? ['NOT_PRESENT']);

        try {
            DB::transaction(function () use ($validated, $request) {
                // Create design request
                $designRequest = $this->createDesignRequest($validated, $request);

                Log::info('=== BEFORE COLOCATION PROCESSING ===');
                Log::info('Design Request ID:', ['id' => $designRequest->id]);

                // Handle colocation sites with enhanced debugging
                $this->processColocationSites($designRequest, $validated, $request);

                Log::info('=== AFTER COLOCATION PROCESSING ===');
            });

            return $this->redirectToSuccessPage($request, 'Design request created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to create design request: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create design request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified design request
     */
    public function show(DesignRequest $designRequest)
    {
        $designRequest->load(['customer', 'surveyor', 'designer', 'quotations', 'colocationSites']);

        $surveyors = User::where('role', 'surveyor')->where('status', 'active')->get();
        $designers = User::where('role', 'designer')->where('status', 'active')->get();

        return view('admin.design-requests.show', compact('designRequest', 'surveyors', 'designers'));
    }

    /**
     * Show the form for editing the specified design request
     */
    public function edit(DesignRequest $designRequest)
    {
        if (!$this->canModifyDesignRequest($designRequest)) {
            return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                ->with('error', 'This design request cannot be edited because it is no longer in pending status.');
        }

        $designers = User::where('role', 'designer')->get();
        $surveyors = User::where('role', 'surveyor')->get();
        $customers = User::where('role', 'customer')->get();

        return view('admin.design-requests.edit', compact(
            'designRequest',
            'designers',
            'surveyors',
            'customers'
        ));
    }

    /**
     * Update the specified design request
     */
    public function update(Request $request, DesignRequest $designRequest): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'customer_id' => 'sometimes|required|exists:users,id',
            'designer_id' => 'nullable|exists:users,id',
            'surveyor_id' => 'nullable|exists:users,id',
            'technical_requirements' => 'sometimes|required|string',
            'cores_required' => 'nullable|integer|min:1',
            'technology_type' => 'nullable|string|max:255',
            'link_class' => 'nullable|string|max:255',
            'status' => 'sometimes|required|in:pending,assigned,in_design,designed,quoted,approved,in_progress,completed,rejected,cancelled',
            'survey_status' => 'sometimes|required|in:not_required,requested,assigned,in_progress,completed,failed,cancelled',
            'route_points' => 'sometimes|nullable|json',
            'distance' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        try {
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                    ->with('error', 'This design request cannot be modified because it is no longer in pending status.');
            }

            DB::transaction(function () use ($designRequest, $validated, $request) {
                $updateData = $this->prepareUpdateData($designRequest, $validated, $request);
                $designRequest->update($updateData);
            });

            return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                ->with('success', 'Design request updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update design request: ' . $e->getMessage(), [
                'design_request_id' => $designRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update design request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified design request
     */
    public function destroy(DesignRequest $designRequest): RedirectResponse
    {
        try {
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                    ->with('error', 'This design request cannot be deleted because it is no longer in pending status.');
            }

            DB::transaction(function () use ($designRequest) {
                // Delete related colocation sites first
                $designRequest->colocationSites()->delete();
                $designRequest->delete();
            });

            return redirect()->route('admin.design-requests.index')
                ->with('success', 'Design request deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete design request: ' . $e->getMessage(), [
                'design_request_id' => $designRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete design request: ' . $e->getMessage());
        }
    }

    /**
     * Assign designer to design request
     */
    public function assignDesigner(Request $request, DesignRequest $designRequest): RedirectResponse
    {
        $request->validate([
            'designer_id' => 'required|exists:users,id'
        ]);

        try {
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                    ->with('error', 'This design request cannot be assigned because it is no longer in pending status.');
            }

            $designRequest->update([
                'designer_id' => $request->designer_id,
                'assigned_at' => now(),
                'status' => 'assigned'
            ]);

            return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                ->with('success', 'Designer assigned successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to assign designer: ' . $e->getMessage(), [
                'design_request_id' => $designRequest->id,
                'designer_id' => $request->designer_id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to assign designer: ' . $e->getMessage());
        }
    }

    /**
     * Show form to assign designer
     */
    public function assignDesignerForm(DesignRequest $designRequest)
    {
        try {
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                    ->with('error', 'This design request cannot be modified because it is no longer in pending status.');
            }

            $designers = User::where('role', 'designer')->get();

            return view('admin.design-requests.assign-designer', compact('designRequest', 'designers'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Design request not found');
        }
    }

    /**
     * Unassign designer from design request
     */
    public function unassignDesigner(DesignRequest $designRequest): RedirectResponse
    {
        try {
            if ($designRequest->status !== 'assigned') {
                return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                    ->with('error', 'This design request cannot be unassigned because it is not in assigned status.');
            }

            $designRequest->update([
                'designer_id' => null,
                'assigned_at' => null,
                'status' => 'pending'
            ]);

            return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                ->with('success', 'Designer unassigned successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to unassign designer: ' . $e->getMessage(), [
                'design_request_id' => $designRequest->id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to unassign designer: ' . $e->getMessage());
        }
    }

    /**
     * Assign surveyor to design request
     */
    public function assignSurveyor(Request $request, DesignRequest $designRequest): RedirectResponse
    {
        $validated = $request->validate([
            'surveyor_id' => 'required|exists:users,id',
            'survey_requirements' => 'nullable|string',
            'survey_estimated_hours' => 'nullable|numeric|min:0',
            'survey_scheduled_at' => 'nullable|date|after:today',
        ]);

        try {
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                    ->with('error', 'This design request cannot be modified because it is no longer in pending status.');
            }

            $designRequest->update([
                'surveyor_id' => $validated['surveyor_id'],
                'survey_requirements' => $validated['survey_requirements'] ?? null,
                'survey_estimated_hours' => $validated['survey_estimated_hours'] ?? null,
                'survey_scheduled_at' => $validated['survey_scheduled_at'] ?? null,
                'survey_status' => 'assigned',
                'survey_requested_at' => now(),
            ]);

            return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                ->with('success', 'Surveyor assigned successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to assign surveyor: ' . $e->getMessage(), [
                'design_request_id' => $designRequest->id,
                'surveyor_id' => $validated['surveyor_id']
            ]);

            return redirect()->back()
                ->with('error', 'Failed to assign surveyor: ' . $e->getMessage());
        }
    }

    /**
     * Show form to assign surveyor
     */
    public function assignSurveyorForm(DesignRequest $designRequest)
    {
        try {
            // Check if design request can be modified
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('admin.design-requests.show', $designRequest->request_number)
                    ->with('error', 'This design request cannot be modified because it is no longer in pending status.');
            }

            $surveyors = User::where('role', 'surveyor')->get();

            return view('admin.design-requests.assign-surveyor', compact('designRequest', 'surveyors'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Design request not found');
        }
    }

    /**
     * Generate KML file for design request route
     */
    public function generateKML(DesignRequest $designRequest)
    {
        $kml = $designRequest->generateKML();

        if (!$kml) {
            return response()->json(['error' => 'No route points available'], 404);
        }

        return response($kml, 200, [
            'Content-Type' => 'application/vnd.google-earth.kml+xml',
            'Content-Disposition' => 'attachment; filename="route-' . $designRequest->request_number . '.kml"'
        ]);
    }

    /**
     * Approve design request quotation
     */
    public function approve(Request $request, DesignRequest $designRequest): RedirectResponse
    {
        if (Gate::denies('approve', $designRequest)) {
            abort(403, 'Unauthorized action.');
        }

        if (!$designRequest->canBeApproved()) {
            return redirect()->back()->with('error', 'This quotation cannot be approved.');
        }

        $designRequest->approveQuote();

        return redirect()->route('admin.design-requests.show', $designRequest->request_number)
            ->with('success', 'Quotation approved successfully! Work will begin soon.');
    }

    // =========================================================================
    // PRIVATE HELPER METHODS
    // =========================================================================

    /**
     * Create design request from validated data
     */
    private function createDesignRequest(array $validated, Request $request): DesignRequest
    {
        $requestNumber = 'DR-' . Carbon::now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
        $status = $validated['designer_id'] ? 'assigned' : 'pending';

        $designRequestData = [
            'customer_id' => $validated['customer_id'],
            'request_number' => $requestNumber,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'technical_requirements' => $validated['technical_requirements'],
            'designer_id' => $validated['designer_id'],
            'cores_required' => $request->input('cores_required', 1),
            'technology_type' => $request->input('technology_type', 'Dark Fibre'),
            'link_class' => $request->input('link_class', 'Standard'),
            'status' => $status,
            'requested_at' => now(),
            'assigned_at' => $validated['designer_id'] ? now() : null,
        ];

        // Handle route data
        $this->processRouteData($designRequestData, $validated, $request);

        return DesignRequest::create($designRequestData);
    }

    /**
     * Process route data for design request
     */
    private function processRouteData(array &$designRequestData, array $validated, Request $request): void
    {
        $hasMapRoute = !empty($validated['route_points']);

        if ($hasMapRoute) {
            $routePoints = json_decode($validated['route_points'], true);
            $pointCount = count($routePoints);
            $totalDistance = $this->calculateTotalDistance($routePoints);

            $designRequestData['route_points'] = $routePoints;
            $designRequestData['point_count'] = $pointCount;
            $designRequestData['total_distance'] = $totalDistance;
            $designRequestData['distance'] = $totalDistance;
        } else {
            $designRequestData['route_points'] = null;
            $designRequestData['point_count'] = 0;
            $designRequestData['total_distance'] = null;
            $designRequestData['distance'] = $request->input('distance', 0);
            $designRequestData['terms'] = $request->input('terms', '');
        }
    }

    /**
     * Process colocation sites
     */
    private function processColocationSites(DesignRequest $designRequest, array $validated, Request $request): void
    {
        Log::info('=== PROCESS COLOCATION SITES - START ===');

        $sitesToProcess = $validated['colocation_sites'] ?? [];

        Log::info('Sites received for processing:', [
            'count' => count($sitesToProcess),
            'sites' => $sitesToProcess,
            'design_request_id' => $designRequest->id
        ]);

        if (empty($sitesToProcess)) {
            Log::info('No colocation sites to process');
            return;
        }

        $createdCount = 0;

        foreach ($sitesToProcess as $index => $siteData) {
            try {
                Log::info("Creating site {$index}:", $siteData);

                // DIRECT CREATE - no complex extraction needed
                $colocationSite = ColocationSite::create([
                    'design_request_id' => $designRequest->id,
                    'site_name' => trim($siteData['site_name']),
                    'service_type' => trim($siteData['service_type']),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                Log::info("✅ SUCCESS: Created colocation site", [
                    'id' => $colocationSite->id,
                    'site_name' => $colocationSite->site_name,
                    'service_type' => $colocationSite->service_type
                ]);

                $createdCount++;

            } catch (\Exception $e) {
                Log::error("❌ FAILED to create site {$index}: " . $e->getMessage(), [
                    'site_data' => $siteData,
                    'error' => $e->getFile() . ':' . $e->getLine()
                ]);
            }
        }

        Log::info("=== PROCESS COLOCATION SITES - COMPLETE ===", [
            'design_request_id' => $designRequest->id,
            'sites_attempted' => count($sitesToProcess),
            'sites_created' => $createdCount
        ]);

        // FINAL VERIFICATION - Check database directly
        $finalDBCount = ColocationSite::where('design_request_id', $designRequest->id)->count();
        Log::info("FINAL DATABASE VERIFICATION: {$finalDBCount} sites in database for DR {$designRequest->id}");

        if ($finalDBCount === 0 && count($sitesToProcess) > 0) {
            Log::error("CRITICAL: No sites saved to database despite processing!");
        }
    }

    /**
     * Prepare update data for design request
     */
    private function prepareUpdateData(DesignRequest $designRequest, array $validated, Request $request): array
    {
        $updateData = $validated;

        // Handle route points update
        if ($request->has('route_points') && !empty($validated['route_points'])) {
            $routePoints = json_decode($validated['route_points'], true);
            $updateData['route_points'] = $routePoints;
            $updateData['point_count'] = count($routePoints);
            $updateData['total_distance'] = $this->calculateTotalDistance($routePoints);
            $updateData['distance'] = $updateData['total_distance'];
            $updateData['terms'] = null;
        }

        // Handle designer assignment
        if ($request->has('designer_id')) {
            if ($validated['designer_id'] && $validated['designer_id'] != $designRequest->designer_id) {
                $updateData['assigned_at'] = now();
                if ($designRequest->status === 'pending') {
                    $updateData['status'] = 'assigned';
                }
            } elseif (!$validated['designer_id'] && $designRequest->designer_id) {
                $updateData['assigned_at'] = null;
                $updateData['status'] = 'pending';
            }
        }

        return $updateData;
    }

    /**
     * Redirect to appropriate success page based on user role
     */
    private function redirectToSuccessPage(Request $request, string $message): RedirectResponse
    {
        $route = Auth::user()->hasRole('admin')
            ? 'admin.design-requests.index'
            : 'customer.design-requests.index';

        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Calculate total distance from route points using Haversine formula
     */
    private function calculateTotalDistance(array $routePoints): float
    {
        if (count($routePoints) < 2) {
            return 0;
        }

        $totalDistance = 0;
        $earthRadius = 6371; // Earth's radius in kilometers

        for ($i = 1; $i < count($routePoints); $i++) {
            $point1 = $routePoints[$i - 1];
            $point2 = $routePoints[$i];

            $lat1 = deg2rad($point1['lat']);
            $lon1 = deg2rad($point1['lng']);
            $lat2 = deg2rad($point2['lat']);
            $lon2 = deg2rad($point2['lng']);

            $deltaLat = $lat2 - $lat1;
            $deltaLon = $lon2 - $lon1;

            $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
                 cos($lat1) * cos($lat2) *
                 sin($deltaLon / 2) * sin($deltaLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            $distance = $earthRadius * $c;
            $totalDistance += $distance;
        }

        return round($totalDistance, 2);
    }

    /**
     * Check if design request can be modified (only pending status allows modifications)
     */
    private function canModifyDesignRequest(DesignRequest $designRequest): bool
    {
        return $designRequest->status === 'pending';
    }

    // In controller
public function getDocumentDetails($type, $id)
{
    switch($type) {
        case 'quotation':
            $document = Quotation::findOrFail($id);
            $view = 'documents.partials.quotation-details';
            break;
        case 'conditional_certificate':
            $document = ConditionalCertificate::findOrFail($id);
            $view = 'documents.partials.conditional-certificate-details';
            break;
        case 'acceptance_certificate':
            $document = AcceptanceCertificate::findOrFail($id);
            $view = 'documents.partials.acceptance-certificate-details';
            break;
        case 'contract':
            $document = Contract::findOrFail($id);
            $view = 'documents.partials.contract-details';
            break;
        case 'lease':
            $document = Lease::findOrFail($id);
            $view = 'documents.partials.lease-details';
            break;
        default:
            abort(404);
    }

    return view($view, compact('document'));
}
// In DesignRequestController or a dedicated DocumentsController

public function listDocuments($requestId)
{
    $designRequest = DesignRequest::with([
        'customer',
        'quotation',
        'conditionalCertificate',
        'acceptanceCertificate',
        'lease',
        'quotation.contract' // Contract through quotation
    ])->findOrFail($requestId);

    return view('design-requests.documents', compact('designRequest'));
}
}
