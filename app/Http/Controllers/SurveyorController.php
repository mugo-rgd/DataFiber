<?php

namespace App\Http\Controllers;

use App\Models\DesignRequest;
use App\Models\RouteSegment;
use App\Models\SurveyAssignment;
use App\Models\SurveyRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyorController extends Controller
{

     public function dashboard()
    {
        $user = Auth::user();

        // Get design requests assigned to this surveyor (user ID)
        $assignedDesignRequests = DesignRequest::where('surveyor_id', $user->id)
            ->with(['customer']) // Load customer relationship
            ->get();

        // Calculate statistics
        $pendingAssignments = $assignedDesignRequests
            ->where('survey_status', 'assigned')
            ->count();

        $inProgressAssignments = $assignedDesignRequests
            ->where('survey_status', 'in_progress')
            ->count();

        $completedThisWeek = $assignedDesignRequests
            ->where('survey_status', 'completed')
            ->where('survey_completed_at', '>=', now()->startOfWeek())
            ->count();

        $urgentAssignments = $assignedDesignRequests
            ->where('priority', 'urgent')
            ->whereIn('survey_status', ['assigned', 'in_progress'])
            ->count();

        // Recent assignments (last 5)
        $recentAssignments = $assignedDesignRequests
            ->sortByDesc('created_at')
            ->take(5);

        // Upcoming deadlines (next 7 days)
        $upcomingDeadlines = $assignedDesignRequests
            ->where('survey_scheduled_at', '>=', now())
            ->where('survey_scheduled_at', '<=', now()->addDays(7))
            ->whereIn('survey_status', ['assigned', 'in_progress'])
            ->sortBy('survey_scheduled_at');

        return view('surveyor.dashboard', compact(
            'pendingAssignments',
            'inProgressAssignments',
            'completedThisWeek',
            'urgentAssignments',
            'recentAssignments',
            'upcomingDeadlines',
            'assignedDesignRequests' // Also pass the full collection for debugging
        ));
    }

    public function assignments()
    {
        $user = Auth::user();

        $assignments = DesignRequest::where('surveyor_id', $user->id)
            ->with(['customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('surveyor.assignments', compact('assignments'));
    }
 // app/Http/Controllers/SurveyorController.php

public function showRoute($id)
{
    $surveyRoute = SurveyRoute::with(['designRequest.customer', 'routeSegments'])
        ->where('surveyor_id', Auth::id())
        ->findOrFail($id);

    return view('surveyor.route-show', [
        'surveyRoute' => $surveyRoute
    ]);
}

public function createSegment($id)
{
    $surveyRoute = SurveyRoute::where('surveyor_id', Auth::id())
        ->findOrFail($id);

    return view('surveyor.route-segment-create', [
        'surveyRoute' => $surveyRoute
    ]);
}

public function storeSegment(Request $request, $id)
{
    $surveyRoute = SurveyRoute::where('surveyor_id', Auth::id())
        ->findOrFail($id);

    $validated = $request->validate([
        'segment_name' => 'required|string|max:255',
        'installation_type' => 'required|in:aerial,underground,conduit,direct_burial',
        'distance_km' => 'required|numeric|min:0.001',
        'terrain_type' => 'required|string',
        'complexity' => 'required|in:low,medium,high',
        'pole_count' => 'nullable|integer|min:0',
        'manhole_count' => 'nullable|integer|min:0',
        'splice_count' => 'nullable|integer|min:0',
        'challenges' => 'nullable|string',
        'cost_multiplier' => 'required|numeric|min:1',
    ]);

    // Get the next segment number
    $segmentNumber = $surveyRoute->routeSegments()->count() + 1;

    RouteSegment::create([
        'survey_route_id' => $surveyRoute->id,
        'segment_number' => $segmentNumber,
        'segment_name' => $validated['segment_name'],
        'installation_type' => $validated['installation_type'],
        'distance_km' => $validated['distance_km'],
        'terrain_type' => $validated['terrain_type'],
        'complexity' => $validated['complexity'],
        'pole_count' => $validated['pole_count'] ?? 0,
        'manhole_count' => $validated['manhole_count'] ?? 0,
        'splice_count' => $validated['splice_count'] ?? 0,
        'challenges' => $validated['challenges'],
        'cost_multiplier' => $validated['cost_multiplier'],
    ]);

    return redirect()->route('surveyor.routes.show', $surveyRoute->id)
        ->with('success', 'Route segment added successfully!');
}

public function storeReport(Request $request)
{
    $validated = $request->validate([
        'design_request_id' => 'required|exists:design_requests,id',
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'attachments' => 'nullable|array',
        'attachments.*' => 'file|max:10240', // 10MB max
    ]);

    // Handle report creation here
    // You might want to create a Report model for this

    return redirect()->route('surveyor.assignment.show', $validated['design_request_id'])
        ->with('success', 'Survey report submitted successfully!');
}

public function updateStatus(Request $request, $id)
{
    $designRequest = DesignRequest::where('surveyor_id', Auth::id())
        ->findOrFail($id);

    $validated = $request->validate([
        'survey_status' => 'required|in:assigned,in_progress,completed',
        'survey_notes' => 'nullable|string',
    ]);

    $designRequest->update([
        'survey_status' => $validated['survey_status'],
        'survey_notes' => $validated['survey_notes'],
    ]);

    return redirect()->back()->with('success', 'Survey status updated successfully!');
}

public function completeAssignment($id)
{
    $designRequest = DesignRequest::where('surveyor_id', Auth::id())
        ->findOrFail($id);

    $designRequest->update([
        'survey_status' => 'completed',
        'status' => 'completed',
    ]);

    return redirect()->back()->with('success', 'Assignment marked as completed!');
}

    public function createReport()
    {
        $assignments = \App\Models\SurveyAssignment::where('surveyor_id', Auth::id())
            ->whereIn('status', ['in_progress', 'completed'])
            ->get();

        return view('surveyor.reports-create', compact('assignments'));
    }

  public function routes()
{
    $user = Auth::user();

    $assignedRoutes = DesignRequest::with(['customer'])
        ->where('surveyor_id', $user->id)
        ->whereIn('status', ['pending', 'in_progress'])
        ->orderBy('created_at', 'asc')
        ->get();

    $mapData = [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
        'center' => [
            'lat' => 40.7128, // Default center (NYC)
            'lng' => -74.0060
        ],
        'zoom' => 10
    ];

    return view('surveyor.routes', compact('assignedRoutes', 'mapData'));
}

    public function availability()
    {
        return view('surveyor.availability');
    }

    public function updateAvailability(Request $request)
    {
        // TODO: Add availability update logic

        return redirect()->route('surveyor.dashboard')
            ->with('success', 'Availability updated successfully!');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('surveyor.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->DB::update('update users set votes = 100 where name = ?', ['John']);($request->only('name', 'email'));

        return redirect()->route('surveyor.profile')
            ->with('success', 'Profile updated successfully!');
    }

   public function showDesignRequest($id)
{
    try {
        $designRequest = DesignRequest::with(['customer', 'surveyor'])
            ->where('id', $id)
            ->firstOrFail();

        // Optional: Add authorization to ensure the surveyor can only view their assigned requests
        if (Auth::user()->role === 'surveyor' && $designRequest->surveyor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('surveyor.design-requests.show', compact('designRequest'));

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'Design request not found.');
    }
}

public function createRouteSegment($routeId)
    {
        $surveyRoute = SurveyRoute::where('surveyor_id', Auth::id())
            ->with('routeSegments')
            ->findOrFail($routeId);

        $nextSegmentNumber = $surveyRoute->routeSegments->count() + 1;

        return view('surveyor.route-segments.create', compact('surveyRoute', 'nextSegmentNumber'));
    }

    public function storeRouteSegment(Request $request, $routeId)
    {
        $surveyRoute = SurveyRoute::where('surveyor_id', Auth::id())
            ->findOrFail($routeId);

        $validated = $request->validate([
            'segment_number' => 'required|integer|min:1',
            'segment_name' => 'required|string|max:255',
            'installation_type' => 'required|in:aerial,underground,conduit,direct_burial',
            'distance_km' => 'required|numeric|min:0.001|max:999.999',
            'terrain_type' => 'required|string|max:255',
            'complexity' => 'required|in:low,medium,high',
            'pole_count' => 'nullable|integer|min:0|max:1000',
            'manhole_count' => 'nullable|integer|min:0|max:500',
            'splice_count' => 'nullable|integer|min:0|max:200',
            'obstacles' => 'nullable|array',
            'obstacles.*' => 'string',
            'challenges' => 'nullable|string',
            'cost_multiplier' => 'required|numeric|min:1.00|max:5.00',
            'start_lat' => 'nullable|numeric|min:-90|max:90',
            'start_lng' => 'nullable|numeric|min:-180|max:180',
            'end_lat' => 'nullable|numeric|min:-90|max:90',
            'end_lng' => 'nullable|numeric|min:-180|max:180',
        ]);

        // Create the route segment
        $routeSegment = RouteSegment::create(array_merge($validated, [
            'survey_route_id' => $surveyRoute->id,
        ]));

        // Update route totals
        $this->updateRouteTotals($surveyRoute);

        if ($request->action === 'save_and_new') {
            return redirect()->route('surveyor.route-segments.create', $surveyRoute->id)
                ->with('success', 'Route segment created successfully!');
        }

        return redirect()->route('surveyor.routes.show', $surveyRoute->id)
            ->with('success', 'Route segment created successfully!');
    }

    private function updateRouteTotals(SurveyRoute $surveyRoute)
    {
        $totalDistance = $surveyRoute->routeSegments()->sum('distance_km');
        $totalCost = $surveyRoute->routeSegments()->get()->sum('total_cost');

        $surveyRoute->update([
            'total_distance' => $totalDistance,
            'estimated_cost' => $totalCost,
        ]);
    }


public function updateSurveyStatus(Request $request, $id)
{
    $request->validate([
        'survey_status' => 'required|in:assigned,in_progress,completed',
        'survey_notes' => 'nullable|string'
    ]);

    try {
        $designRequest = DesignRequest::where('surveyor_id', Auth::id())
                                    ->findOrFail($id);

        $updateData = [
            'survey_status' => $request->survey_status
        ];

        // Set completion time if marking as completed
        if ($request->survey_status === 'completed') {
            $updateData['survey_completed_at'] = now();
        }

        // Set progress time if marking as in progress
        if ($request->survey_status === 'in_progress' && $designRequest->survey_status !== 'in_progress') {
            $updateData['survey_started_at'] = now();
        }

        $designRequest->update($updateData);

        return redirect()->route('surveyor.assignment.show', $designRequest->id)
            ->with('success', 'Survey status updated successfully!');

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return redirect()->route('surveyor.assignments')
            ->with('error', 'Assignment not found or you do not have permission to update it.');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to update survey status: ' . $e->getMessage());
    }
}

    // public function showRoute($routeId)
    // {
    //     $surveyRoute = SurveyRoute::where('surveyor_id', Auth::id())
    //         ->with(['designRequest.customer', 'routeSegments'])
    //         ->findOrFail($routeId);

    //     return view('surveyor.routes.show', compact('surveyRoute'));
    // }

    public function showRouteSegments($routeId)
    {
        $surveyRoute = SurveyRoute::where('surveyor_id', Auth::id())
            ->with('routeSegments')
            ->findOrFail($routeId);

        return view('surveyor.route-segments.index', compact('surveyRoute'));
    }

    public function updateRouteStatus(Request $request, $routeId)
{
    $surveyRoute = SurveyRoute::where('surveyor_id', Auth::id())
        ->findOrFail($routeId);

    $validated = $request->validate([
        'status' => 'required|in:draft,in_progress,completed,approved',
        'status_notes' => 'nullable|string',
    ]);

    $surveyRoute->update($validated);

    return redirect()->back()->with('success', 'Route status updated successfully!');
}


// --------------------------------------

public function storeRoute(Request $request)
{
    $validated = $request->validate([
        'design_request_id' => 'required|exists:design_requests,id',
        'route_name' => 'required|string|max:255',
        'route_description' => 'nullable|string',
        'route_type' => 'required|in:underground,aerial,direct_burial,mixed',
        'start_point' => 'required|string|max:255',
        'end_point' => 'required|string|max:255',
        'estimated_distance' => 'required|numeric|min:0.001',
        'fibre_type' => 'required|string',
        'core_count' => 'required|integer|min:1|max:144',
        'terrain_type' => 'nullable|string',
        'complexity' => 'nullable|in:low,medium,high',
        'hazards' => 'nullable|array',
        'special_requirements' => 'nullable|string'
    ]);

    try {
        $surveyRoute = SurveyRoute::create([
            'design_request_id' => $validated['design_request_id'],
            'surveyor_id' => Auth::id(),
            'route_name' => $validated['route_name'],
            'route_description' => $validated['route_description'],
            'total_distance' => $validated['estimated_distance'],
            'estimated_cost' => $this->calculateEstimatedCost($validated),
            'status' => 'draft'
        ]);

        // Update design request status
        $designRequest = DesignRequest::find($validated['design_request_id']);
        $designRequest->update([
            'survey_status' => 'in_progress'
        ]);

        return redirect()
            ->route('surveyor.routes.show', $surveyRoute->id)
            ->with('success', 'Dark fibre survey route created successfully!');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to create survey route: ' . $e->getMessage());
    }
}

private function calculateEstimatedCost($data)
{
    $baseCostPerKm = 1200;
    $distance = $data['estimated_distance'];

    $fibreMultiplier = match($data['fibre_type']) {
        'single_mode' => 1.2,
        'os2' => 1.3,
        'multimode_om4' => 1.1,
        'multimode_om5' => 1.15,
        default => 1.0
    };

    $coreMultiplier = $data['core_count'] / 24;
    $complexityMultiplier = match($data['complexity'] ?? 'medium') {
        'low' => 0.8,
        'high' => 1.5,
        default => 1.0
    };

    return $baseCostPerKm * $distance * $fibreMultiplier * $coreMultiplier * $complexityMultiplier;
}

// app/Http/Controllers/SurveyorController.php

public function showAssignment($id)
{
    // Fix: Use the correct relationship name - 'routeSegments' not 'routeSegments'
    $designRequest = DesignRequest::with(['customer', 'surveyRoute.routeSegments'])
        ->where('surveyor_id', Auth::id())
        ->findOrFail($id);

    return view('surveyor.assignment-show', [
        'designRequest' => $designRequest
    ]);
}
}
