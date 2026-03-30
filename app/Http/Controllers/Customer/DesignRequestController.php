<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ColocationList;
use App\Models\DesignRequest;
use App\Models\FibreStation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache; // Added missing import

class DesignRequestController extends Controller
{
    /**
     * Display a listing of design requests
     */
    public function index()
    {
         $designRequests = DesignRequest::where('customer_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);

        $pendingRequests = DesignRequest::with(['customer', 'designer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $assignedRequests = DesignRequest::with(['customer', 'designer'])
            ->where('status', 'assigned')
            ->orderBy('assigned_at', 'desc')
            ->get();

        $inDesignRequests = DesignRequest::with(['customer', 'designer'])
            ->where('status', 'in_design')
            ->orderBy('assigned_at', 'desc')
            ->get();

        $designedRequests = DesignRequest::with(['customer', 'designer'])
            ->where('status', 'designed')
            ->orderBy('design_completed_at', 'desc')
            ->get();

        $designers = User::where('role', 'designer')->get();
        $surveyors = User::where('role', 'surveyor')->get();

        return view('customer.design-requests.index', compact(
            'pendingRequests',
            'assignedRequests',
            'inDesignRequests',
            'designedRequests',
            'designRequests',
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

               $colocationServices = ColocationList::where('fibrestatus', 'Active')->get();
    $serviceCategories = $colocationServices->pluck('service_category')->unique();
    $fibreStations = FibreStation::where('fibrestatus', 'Available')->get();

        return view('customer.design-requests.create', compact(
            'customers',
            'designers',
            'serviceCategories',
            'colocationServices','fibreStations'
        ));
    }

   public function store(Request $request)
{
       // ✅ ADD COMPREHENSIVE DEBUGGING
    Log::info('=== CUSTOMER STORE METHOD - COLOCATION SITES DEBUG ===');
    Log::info('All request keys:', array_keys($request->all()));
    Log::info('colocation_sites data:', [
        'exists' => $request->has('colocation_sites'),
        'data' => $request->colocation_sites,
        'type' => gettype($request->colocation_sites)
    ]);

    // Validate the request - ADD COLOCATION SITES VALIDATION
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'technical_requirements' => 'required|string',
        'cores_required' => 'nullable|integer|min:1',
        'technology_type' => 'nullable|string|max:255',
        'link_class' => 'nullable|string|max:255',
        'route_points' => 'nullable|json',
        'distance' => 'nullable|numeric|min:0',
        'terms' => 'nullable|string',
        'colocation_sites' => 'nullable|array', // ADD THIS
        'colocation_sites.*.site_name' => 'required|string|max:255', // ADD THIS
        'colocation_sites.*.service_type' => 'required|string|in:shelter_space,rack,cage,suites', // ADD THIS
    ], [
        'route_points.json' => 'The route points must be valid JSON format.',
    ]);

    Log::info('=== AFTER VALIDATION ===');
    Log::info('Validated colocation_sites:', $validated['colocation_sites'] ?? ['NOT_PRESENT']);

    try {
        // Check if we have route points (map-defined) or manual entry
        $hasMapRoute = !empty($validated['route_points']);
        $hasManualEntry = !empty($validated['cores_required']) || !empty($validated['distance']) || !empty($validated['terms']);

        // Validate that at least one method is used
        if (!$hasMapRoute && !$hasManualEntry) {
            return redirect()->back()
                ->with('error', 'Please either define the route on the map OR enter route details manually (cores required, distance, terms).')
                ->withInput();
        }

        // Generate request number
        $requestNumber = 'DR-' . Carbon::now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        // Prepare base data - Use Auth::id() for customer_id
        $designRequestData = [
            'customer_id' => Auth::id(), // AUTO-ASSIGN to logged-in customer
            'request_number' => $requestNumber,
            'title' => $validated['title'],
            'route_name' => $validated['title'],
            'description' => $validated['description'],
            'technical_requirements' => $validated['technical_requirements'],
            'cores_required' => $validated['cores_required'],
            'technology_type' => $validated['technology_type'],
            'link_class' => $validated['link_class'],
            'status' => 'pending', // Always pending for customer submissions
            'requested_at' => now(),
        ];

        // Handle map-defined route
        if ($hasMapRoute) {
            $routePoints = json_decode($validated['route_points'], true);
            $pointCount = count($routePoints);
            $totalDistance = $this->calculateTotalDistance($routePoints);

            $designRequestData['route_points'] = $routePoints;
            $designRequestData['point_count'] = $pointCount;
            $designRequestData['total_distance'] = $totalDistance;
            $designRequestData['distance'] = $totalDistance;

            Log::info('Customer created map-defined route:', [
                'customer_id' => Auth::id(),
                'point_count' => $pointCount,
                'total_distance' => $totalDistance
            ]);
        }
        // Handle manual entry
        else {
            $designRequestData['route_points'] = null;
            $designRequestData['point_count'] = 0;
            $designRequestData['total_distance'] = null;
            $designRequestData['distance'] = $validated['distance'];
            $designRequestData['terms'] = $validated['terms'];

            Log::info('Customer created manual entry route:', [
                'customer_id' => Auth::id(),
                'cores_required' => $validated['cores_required'],
                'distance' => $validated['distance']
            ]);
        }

        // Create design request
        $designRequest = DesignRequest::create($designRequestData);

        Log::info('Design request created successfully:', [
            'design_request_id' => $designRequest->id,
            'request_number' => $designRequest->request_number
        ]);

        // ✅ PROCESS COLOCATION SITES (NEW CODE)
        if (!empty($validated['colocation_sites'])) {
            $this->processColocationSites($designRequest, $validated);
        }

        return redirect()->route('customer.design-requests.show', $designRequest->id)
            ->with('success', 'Design request submitted successfully! We will review your request and get back to you soon.');

    } catch (\Exception $e) {
        Log::error('Failed to create design request: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return redirect()->back()
            ->with('error', 'Failed to create design request: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Process colocation sites for customer requests
 */
private function processColocationSites(DesignRequest $designRequest, array $validated): void
{
    Log::info('=== CUSTOMER PROCESS COLOCATION SITES ===');

    $sitesToProcess = $validated['colocation_sites'] ?? [];

    Log::info('Sites to process:', [
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

            // Create colocation site
            $colocationSite = \App\Models\ColocationSite::create([
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

    Log::info("=== CUSTOMER COLOCATION SITES COMPLETE ===", [
        'design_request_id' => $designRequest->id,
        'sites_attempted' => count($sitesToProcess),
        'sites_created' => $createdCount
    ]);

    // Final verification
    $finalDBCount = \App\Models\ColocationSite::where('design_request_id', $designRequest->id)->count();
    Log::info("FINAL DATABASE COUNT: {$finalDBCount} sites for DR {$designRequest->id}");
}
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
     * Display the specified design request
     */
    public function show($designRequest)
{
    // Try to find by request_number first, then by ID
    $designRequest = DesignRequest::with(['customer', 'surveyor', 'designer', 'colocationSites'])
                   ->where('request_number', $designRequest)
                   ->orWhere('id', $designRequest)
                   ->firstOrFail();

    $surveyors = User::where('role', 'surveyor')->where('status', 'active')->get();
    $designers = User::where('role', 'designer')->where('status', 'active')->get();

    return view('customer.design-requests.show', compact('designRequest', 'surveyors', 'designers'));
}

    /**
     * Show the form for editing the specified design request
     */
    public function edit($id)
    {
        try {
            $designRequest = DesignRequest::with(['customer', 'designer', 'surveyor'])->findOrFail($id);

            // Check if design request can be edited (only pending status allows editing)
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('customer.design-requests.show', $designRequest->id)
                    ->with('error', 'This design request cannot be edited because it is no longer in pending status.');
            }

            $designers = User::where('role', 'designer')->get();
            $surveyors = User::where('role', 'surveyor')->get();
            $customers = User::where('role', 'customer')->get();

            return view('customer.design-requests.edit', compact('designRequest', 'designers', 'surveyors', 'customers'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Design request not found');
        }
    }

    /**
     * Update the specified design request
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'route_name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'customer_id' => 'sometimes|required|exists:users,id',
            'designer_id' => 'nullable|exists:users,id',
            'surveyor_id' => 'nullable|exists:users,id',
            'technical_requirements' => 'sometimes|required|string',
            'cores_required' => 'nullable|integer|min:1',
            'technology_type' => 'nullable|string|max:255',
            'link_class' => 'nullable|string|max:255',
            'status' => 'sometimes|required|in:pending,assigned,in_design,designed,quoted,completed,cancelled',
            'survey_status' => 'sometimes|required|in:not_required,requested,assigned,in_progress,completed,failed,cancelled',
            'route_points' => 'sometimes|nullable|json',
            'distance' => 'nullable|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        try {
            $designRequest = DesignRequest::findOrFail($id);

            // Check if design request can be modified (only pending status allows modifications)
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('customer.design-requests.show', $designRequest->id)
                    ->with('error', 'This design request cannot be modified because it is no longer in pending status.');
            }

            DB::transaction(function () use ($designRequest, $validated, $request) {
                $updateData = $validated;

                // Check if we're updating route type
                $hasMapRoute = !empty($validated['route_points']);
                $hasManualEntry = !empty($validated['cores_required']) || !empty($validated['distance']) || !empty($validated['terms']);

                // Validate that at least one method is provided when updating route info
                if ($request->has('route_points') && !$hasMapRoute && !$hasManualEntry) {
                    throw new \Exception('Please either provide route points OR enter route details manually.');
                }

                // Handle route points update (map-defined route)
                if ($request->has('route_points') && $hasMapRoute) {
                    $routePoints = json_decode($validated['route_points'], true);
                    $pointCount = count($routePoints);
                    $totalDistance = $this->calculateTotalDistance($routePoints);

                    $updateData['route_points'] = $routePoints;
                    $updateData['point_count'] = $pointCount;
                    $updateData['total_distance'] = $totalDistance;
                    $updateData['distance'] = $totalDistance;
                    $updateData['terms'] = null;

                    Log::info('Updated to map-defined route:', [
                        'point_count' => $pointCount,
                        'total_distance' => $totalDistance
                    ]);
                }
                // Handle manual entry update
                elseif ($request->hasAny(['cores_required', 'distance', 'terms']) && $hasManualEntry) {
                    $updateData['route_points'] = null;
                    $updateData['point_count'] = 0;
                    $updateData['total_distance'] = null;

                    Log::info('Updated to manual entry route:', [
                        'cores_required' => $validated['cores_required'],
                        'distance' => $validated['distance'],
                        'terms' => $validated['terms']
                    ]);
                }

                // If assigning a designer, update assigned_at and status
                if ($request->has('designer_id') && $request->designer_id != $designRequest->designer_id) {
                    $updateData['assigned_at'] = now();
                    if ($designRequest->status === 'pending') {
                        $updateData['status'] = 'assigned';
                    }
                }

                // If unassigning designer
                if ($request->has('designer_id') && $request->designer_id === null && $designRequest->designer_id !== null) {
                    $updateData['assigned_at'] = null;
                    $updateData['status'] = 'pending';
                }

                // If assigning a surveyor, update survey dates
                if ($request->has('surveyor_id') && $request->surveyor_id != $designRequest->surveyor_id) {
                    $updateData['survey_requested_at'] = now();
                    if ($designRequest->survey_status === 'not_required') {
                        $updateData['survey_status'] = 'requested';
                    }
                }

                $designRequest->update($updateData);
            });

            return redirect()->route('customer.design-requests.show', $designRequest->id)
                ->with('success', 'Design request updated successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Design request not found.');
        } catch (\Exception $e) {
            Log::error('Failed to update design request: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update design request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified design request
     */
    public function destroy($id)
    {
        try {
            $designRequest = DesignRequest::findOrFail($id);

            // Check if design request can be deleted (only pending status allows deletion)
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('customer.design-requests.show', $designRequest->id)
                    ->with('error', 'This design request cannot be deleted because it is no longer in pending status.');
            }

            DB::transaction(function () use ($designRequest) {
                $designRequest->delete();
            });

            return redirect()->route('customer.design-requests.index')
                ->with('success', 'Design request deleted successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Design request not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete design request: ' . $e->getMessage());
        }
    }

    /**
     * Assign designer to design request
     */
    public function assignDesigner(Request $request, $id)
    {
        $request->validate([
            'designer_id' => 'required|exists:users,id'
        ]);

        try {
            $designRequest = DesignRequest::findOrFail($id);

            // Check if design request can be assigned (only pending status allows assignment)
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('customer.design-requests.show', $designRequest->id)
                    ->with('error', 'This design request cannot be assigned because it is no longer in pending status.');
            }

            DB::transaction(function () use ($designRequest, $request) {
                $designRequest->update([
                    'designer_id' => $request->designer_id,
                    'assigned_at' => now(),
                    'status' => 'assigned'
                ]);
            });

            return redirect()->route('customer.design-requests.show', $designRequest->id)
                ->with('success', 'Designer assigned successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Design request not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to assign designer: ' . $e->getMessage());
        }
    }

    /**
     * Unassign designer from design request
     */
    public function unassignDesigner($id)
    {
        try {
            $designRequest = DesignRequest::findOrFail($id);

            // Check if design request can be unassigned (only assigned status allows unassignment)
            if ($designRequest->status !== 'assigned') {
                return redirect()->route('customer.design-requests.show', $designRequest->id)
                    ->with('error', 'This design request cannot be unassigned because it is not in assigned status.');
            }

            DB::transaction(function () use ($designRequest) {
                $designRequest->update([
                    'designer_id' => null,
                    'assigned_at' => null,
                    'status' => 'pending'
                ]);
            });

            return redirect()->route('customer.design-requests.show', $designRequest->id)
                ->with('success', 'Designer unassigned successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Design request not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to unassign designer: ' . $e->getMessage());
        }
    }

    public function assignSurveyor(Request $request, DesignRequest $designRequest)
    {
        $validated = $request->validate([
            'surveyor_id' => 'required|exists:users,id',
            'survey_requirements' => 'nullable|string',
            'survey_estimated_hours' => 'nullable|numeric|min:0',
            'survey_scheduled_at' => 'nullable|date|after:today',
        ]);

        try {
            // Check if design request can have surveyor assigned
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('customer.design-requests.show', $designRequest)
                    ->with('error', 'This design request cannot be modified because it is no longer in pending status.');
            }

            DB::transaction(function () use ($designRequest, $validated) {
                $designRequest->update([
                    'surveyor_id' => $validated['surveyor_id'],
                    'survey_requirements' => $validated['survey_requirements'] ?? null,
                    'survey_estimated_hours' => $validated['survey_estimated_hours'] ?? null,
                    'survey_scheduled_at' => $validated['survey_scheduled_at'] ?? null,
                    'survey_status' => 'assigned',
                    'survey_requested_at' => now(),
                ]);
            });

            return redirect()->route('customer.design-requests.show', $designRequest)
                ->with('success', 'Surveyor assigned successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to assign surveyor: ' . $e->getMessage());
        }
    }

    /**
     * Update design request status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,assigned,in_design,designed,quoted,completed,cancelled'
        ]);

        try {
            $designRequest = DesignRequest::findOrFail($id);

            $updateData = ['status' => $validated['status']];

            // Set completion dates based on status
            if ($validated['status'] === 'designed' && !$designRequest->design_completed_at) {
                $updateData['design_completed_at'] = now();
            } elseif ($validated['status'] === 'completed' && !$designRequest->completed_at) {
                $updateData['completed_at'] = now();
            }

            $designRequest->update($updateData);

            return redirect()->route('customer.design-requests.show', $designRequest->id)
                ->with('success', 'Status updated successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Design request not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Update survey status
     */
    public function updateSurveyStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'survey_status' => 'required|in:not_required,requested,assigned,in_progress,completed,failed,cancelled'
        ]);

        try {
            $designRequest = DesignRequest::findOrFail($id);

            $updateData = ['survey_status' => $validated['survey_status']];

            // Set survey completion date if completed
            if ($validated['survey_status'] === 'completed' && !$designRequest->survey_completed_at) {
                $updateData['survey_completed_at'] = now();
            }

            $designRequest->update($updateData);

            return redirect()->route('customer.design-requests.show', $designRequest->id)
                ->with('success', 'Survey status updated successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Design request not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update survey status: ' . $e->getMessage());
        }
    }

    /**
     * Show form to assign designer
     */
    public function assignDesignerForm($id)
    {
        try {
            $designRequest = DesignRequest::findOrFail($id);

            // Check if design request can be assigned
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('customer.design-requests.show', $designRequest->id)
                    ->with('error', 'This design request cannot be assigned because it is no longer in pending status.');
            }

            $designers = User::where('role', 'designer')->get();
            return view('customer.design-requests.assign-designer', compact('designRequest', 'designers'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Design request not found');
        }
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
     * Show form to assign surveyor
     */
    public function assignSurveyorForm($id)
    {
        try {
            $designRequest = DesignRequest::findOrFail($id);

            // Check if design request can be modified
            if (!$this->canModifyDesignRequest($designRequest)) {
                return redirect()->route('customer.design-requests.show', $designRequest->id)
                    ->with('error', 'This design request cannot be modified because it is no longer in pending status.');
            }

            $surveyors = User::where('role', 'surveyor')->get();

            return view('customer.design-requests.assign-surveyor', compact('designRequest', 'surveyors'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Design request not found');
        }
    }

    /**
     * Mark design request as completed
     */
    public function completeDesign($id)
    {
        try {
            $designRequest = DesignRequest::findOrFail($id);

            $designRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return redirect()->route('customer.design-requests.show', $designRequest->id)
                ->with('success', 'Design request marked as completed!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Design request not found.');
        } catch (\Exception $e) {
            Log::error('Failed to complete design request: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to complete design request: ' . $e->getMessage());
        }
    }

    /**
     * Check if design request has map-defined route
     */
    public function hasMapRoute(DesignRequest $designRequest): bool
    {
        return !empty($designRequest->route_points) && $designRequest->point_count > 1;
    }

    /**
     * Check if design request has manual entry
     */
    public function hasManualEntry(DesignRequest $designRequest): bool
    {
        return !empty($designRequest->cores_required) || !empty($designRequest->distance) || !empty($designRequest->terms);
    }

    /**
     * Get display distance - prefer calculated from map if available
     */
    public function getDisplayDistance(DesignRequest $designRequest): ?float
    {
        return $designRequest->total_distance ?? $designRequest->distance;
    }

    /**
     * Check if design request can be modified (only pending status allows modifications)
     */
    private function canModifyDesignRequest(DesignRequest $designRequest): bool
    {
        return $designRequest->status === 'pending';
    }

    // Additional helper methods for customer functionality
    // Note: These should probably be in a separate CustomerDesignRequestController

    /**
     * Calculate distance from route points using Haversine formula
     */
    private function calculateRouteDistance(array $routePoints): float
    {
        $totalDistance = 0;

        for ($i = 1; $i < count($routePoints); $i++) {
            $point1 = $routePoints[$i - 1];
            $point2 = $routePoints[$i];

            $lat1 = $point1['lat'];
            $lon1 = $point1['lng'];
            $lat2 = $point2['lat'];
            $lon2 = $point2['lng'];

            $distance = $this->calculateHaversineDistance($lat1, $lon1, $lat2, $lon2);
            $totalDistance += $distance;
        }

        return round($totalDistance, 2);
    }

    /**
     * Haversine distance calculation between two points
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    // Note: Removed duplicate index() method that was at the bottom

    // Optimized API endpoint for fibre stations
    public function getFibreStations()
    {
        try {
            // Note: You'll need to import the FibreStation model and adjust the query as needed
            $stations = Cache::remember('fibre_stations_optimized', 3600, function () {
                // This is a placeholder - adjust based on your actual FibreStation model
                return []; // Replace with actual query
            });

            return response()->json($stations);

        } catch (\Exception $e) {
            Log::error('Failed to fetch fibre stations: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to load substation data'
            ], 500);
        }
    }
}
