<?php

namespace App\Http\Controllers;

use App\Models\DesignRequest;
use App\Models\MaintenanceEquipment;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceWorkOrder;
use App\Models\SurveyRoute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
public function dashboard()
{
    $user = Auth::user();

    // Role-based dashboard routing using direct role checks
    if ($user->role === 'customer') {
        return $this->customerDashboard();
    } elseif ($user->role === 'designer') {
        return $this->designerDashboard();
    } elseif ($user->role === 'surveyor') {
        return $this->surveyorDashboard();
    } elseif ($user->role === 'admin') {
        return $this->adminDashboard();
    } else {
        return $this->technicianDashboard();
    }
}

private function technicianDashboard()
{
    $technician = Auth::user();

    $stats = [
        'assigned_work_orders' => MaintenanceWorkOrder::where('assigned_technician', $technician->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count(),

        'completed_this_week' => MaintenanceWorkOrder::where('assigned_technician', $technician->id)
            ->where('status', 'completed')
            ->where('actual_end', '>=', now()->startOfWeek())
            ->count(),

        'pending_approval' => MaintenanceWorkOrder::where('assigned_technician', $technician->id)
            ->where('status', 'completed')
            ->whereNull('actual_end')
            ->count(),

        'critical_priority' => MaintenanceWorkOrder::where('assigned_technician', $technician->id)
            ->whereHas('maintenanceRequest', function($query) {
                $query->where('priority', 'critical');
            })
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count(),
    ];

    $assignedWorkOrders = MaintenanceWorkOrder::with(['maintenanceRequest.designRequest.customer'])
        ->where('assigned_technician', $technician->id)
        ->whereIn('status', ['assigned', 'in_progress'])
        ->orderBy('scheduled_start')
        ->get();

    $availableEquipment = MaintenanceEquipment::available()->get();

    return view('technician.dashboard', compact('stats', 'assignedWorkOrders', 'availableEquipment', 'technician'));
}
private function customerDashboard()
{
    $customer = Auth::user();

    $customerStats = [
        'open_requests' => MaintenanceRequest::whereHas('designRequest', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->whereIn('status', ['open', 'assigned', 'in_progress'])->count(),

        'resolved_requests' => MaintenanceRequest::whereHas('designRequest', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->where('status', 'resolved')->count(),

        'in_progress' => MaintenanceRequest::whereHas('designRequest', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->where('status', 'in_progress')->count(),

        'total_requests' => MaintenanceRequest::whereHas('designRequest', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->count(),
    ];

    $customerRequests = MaintenanceRequest::with(['designRequest', 'workOrders'])
        ->whereHas('designRequest', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('maintenance.customer-dashboard', compact('customerStats', 'customerRequests'));
}

private function designerDashboard()
{
    $networkHealth = SurveyRoute::withCount(['maintenanceRequests' => function($query) {
        $query->whereIn('status', ['open', 'assigned', 'in_progress']);
    }])->get()->map(function($route) {
        $route->health_percentage = max(0, 100 - ($route->maintenance_requests_count * 10));
        $route->health_color = $route->health_percentage >= 80 ? 'success' :
                              ($route->health_percentage >= 60 ? 'warning' : 'danger');
        $route->open_issues = $route->maintenance_requests_count;
        return $route;
    });

    $pendingAssignments = MaintenanceRequest::where('status', 'open')
        ->where('priority', 'high')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return view('maintenance.designer-dashboard', compact('networkHealth', 'pendingAssignments'));
}

private function surveyorDashboard()
{
    $assignedWorkOrders = MaintenanceWorkOrder::with(['maintenanceRequest.designRequest'])
        ->where('assigned_technician', Auth::id())
        ->whereIn('status', ['assigned', 'in_progress'])
        ->orderBy('scheduled_start')
        ->get();

    $recentSurveys = SurveyRoute::where('surveyor_id', Auth::id())
        ->withCount('maintenanceRequests')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return view('maintenance.surveyor-dashboard', compact('assignedWorkOrders', 'recentSurveys'));
}

// private function adminDashboard()
// {
//     // Full maintenance statistics
//     $stats = $this->getMaintenanceStats();

//     $criticalRequests = MaintenanceRequest::with(['designRequest.customer'])
//         ->where('priority', 'critical')
//         ->whereIn('status', ['open', 'assigned'])
//         ->orderBy('created_at', 'desc')
//         ->get();

//     $equipmentStatus = MaintenanceEquipment::select('status', DB::raw('count(*) as count'))
//         ->groupBy('status')
//         ->get();

//     return view('maintenance.admin-dashboard', compact('stats', 'criticalRequests', 'equipmentStatus'));
// }

// Update the index method to be role-aware
public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');
        $type = $request->get('type', 'all');

        // Build the query with relationships
        $query = MaintenanceRequest::with([
            'designRequest.customer',
            'reporter',
            'workOrders',
            'equipment',
            'requestedBy'
        ]);

        // Apply role-based filters
        if ($user->role === 'customer') {
            $query->whereHas('designRequest', function($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        } elseif ($user->role === 'surveyor') {
            // Surveyors see requests related to their survey routes
            $query->whereHas('workOrders.surveyRoute', function($q) use ($user) {
                $q->where('surveyor_id', $user->id);
            });
        } elseif ($user->role === 'technician') {
            // FIXED: Technicians see requests where they have work orders assigned
            $query->whereHas('workOrders', function($q) use ($user) {
                $q->where('assigned_technician', $user->id);
            });
        } elseif ($user->role === 'designer') {
            // Designers see requests related to their design requests
            $query->whereHas('designRequest', function($q) use ($user) {
                $q->where('designer_id', $user->id);
            });
        }
        // Admin and finance can see all requests (no additional filtering)

        // Apply status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply priority filter
        if ($priority !== 'all') {
            $query->where('priority', $priority);
        }

        // Apply maintenance type filter
        if ($type !== 'all') {
            $query->where('maintenance_type', $type);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('equipment', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('designRequest.customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('maintenance.requests-index', compact('requests', 'status', 'priority', 'type'));
    }

    public function equipment(Request $request)
{
    $status = $request->get('status', 'all');
    $search = $request->get('search', '');

    // Build the query
    $query = MaintenanceEquipment::with(['maintenanceRequests', 'currentWorkOrder']);

    // Apply status filter
    if ($status !== 'all') {
        $query->where('status', $status);
    }

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    $equipment = $query->orderBy('name')->paginate(20);

    return view('maintenance.equipment-index', compact('equipment', 'status', 'search'));
}

public function showEquipment($id)
{
    $equipment = MaintenanceEquipment::with(['maintenanceRequests.workOrders', 'maintenanceRequests.requestedBy'])
        ->findOrFail($id);

    return view('maintenance.equipment-show', compact('equipment'));
}

public function createEquipment()
{
    return view('maintenance.equipment-create');
}


public function storeEquipment(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'model' => 'nullable|string|max:255',
        'serial_number' => 'nullable|string|max:255|unique:maintenance_equipment,serial_number',
        'description' => 'nullable|string',
        'location' => 'nullable|string|max:255',
        'status' => 'required|in:available,in_use,maintenance,retired',
        'purchase_date' => 'nullable|date',
        'last_calibration' => 'nullable|date',
        'next_calibration' => 'nullable|date',
    ]);

    MaintenanceEquipment::create($validated);

    return redirect()->route('maintenance.equipment.index')
        ->with('success', 'Equipment added successfully!');
}

public function create()
{
    // Get available equipment for the form
    $availableEquipment = MaintenanceEquipment::where('status', 'available')->get();

    // Get design requests that might need maintenance
    $designRequests = DesignRequest::where('status', 'completed')->get();

    return view('maintenance.requests.create', compact('availableEquipment', 'designRequests'));
}

private function getMaintenanceStats()
    {
        return [
            // Request Statistics
            'total_requests' => MaintenanceRequest::count(),
            'open_requests' => MaintenanceRequest::whereIn('status', ['open', 'assigned', 'in_progress'])->count(),
            'critical_requests' => MaintenanceRequest::where('priority', 'critical')
                ->whereIn('status', ['open', 'assigned', 'in_progress'])
                ->count(),
            'resolved_this_week' => MaintenanceRequest::where('status', 'resolved')
                ->where('resolved_at', '>=', now()->startOfWeek())
                ->count(),

            // Work Order Statistics
            'total_work_orders' => MaintenanceWorkOrder::count(),
            'pending_work_orders' => MaintenanceWorkOrder::where('status', 'pending')->count(),
            'active_work_orders' => MaintenanceWorkOrder::where('status', 'in_progress')->count(),
            'completed_work_orders' => MaintenanceWorkOrder::where('status', 'completed')->count(),

            // Performance Metrics
            'avg_resolution_time' => MaintenanceRequest::where('status', 'resolved')
                ->whereNotNull('resolved_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, reported_at, resolved_at)) as avg_time'))
                ->first()->avg_time ?? 0,

            'total_downtime' => MaintenanceRequest::where('status', 'resolved')
                ->sum('downtime_minutes'),

            // Equipment Statistics
            'total_equipment' => MaintenanceEquipment::count(),
            'available_equipment' => MaintenanceEquipment::where('status', 'available')->count(),
            'equipment_in_use' => MaintenanceEquipment::where('status', 'in_use')->count(),
            'equipment_needing_calibration' => MaintenanceEquipment::where('next_calibration', '<=', now()->addDays(30))
                ->where('status', '!=', 'retired')
                ->count(),

            // Cost Statistics
            'total_repair_cost' => MaintenanceRequest::where('status', 'resolved')
                ->sum('repair_cost'),
            'avg_repair_cost' => MaintenanceRequest::where('status', 'resolved')
                ->where('repair_cost', '>', 0)
                ->avg('repair_cost'),

            // Issue Type Breakdown
            'issue_types' => MaintenanceRequest::select('issue_type', DB::raw('count(*) as count'))
                ->groupBy('issue_type')
                ->get()
                ->pluck('count', 'issue_type'),

            // Priority Breakdown
            'priority_breakdown' => MaintenanceRequest::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get()
                ->pluck('count', 'priority'),
        ];
    }

    /**
     * Admin Dashboard with comprehensive maintenance overview
     */
    private function adminDashboard()
    {
        // Full maintenance statistics
        $stats = $this->getMaintenanceStats();

        $criticalRequests = MaintenanceRequest::with(['designRequest.customer', 'workOrders.technician'])
            ->where('priority', 'critical')
            ->whereIn('status', ['open', 'assigned'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $equipmentStatus = MaintenanceEquipment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Recent activity
        $recentWorkOrders = MaintenanceWorkOrder::with(['maintenanceRequest.designRequest', 'technician'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Technician performance
        $technicianPerformance = DB::table('maintenance_work_orders')
            ->join('users', 'maintenance_work_orders.assigned_technician', '=', 'users.id')
            ->where('maintenance_work_orders.status', 'completed')
            ->select(
                'users.name',
                'users.id as user_id',
                DB::raw('COUNT(maintenance_work_orders.id) as completed_orders'),
                DB::raw('AVG(maintenance_work_orders.actual_duration_minutes) as avg_completion_time'),
                DB::raw('SUM(maintenance_work_orders.labor_cost) as total_labor_cost')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('completed_orders', 'desc')
            ->get();

        return view('maintenance.admin-dashboard', compact(
            'stats',
            'criticalRequests',
            'equipmentStatus',
            'recentWorkOrders',
            'technicianPerformance'
        ));
    }

    private function notifyCriticalIssue(MaintenanceRequest $maintenanceRequest)
{
    // Get admins and designers to notify
    $usersToNotify = \App\Models\User::whereIn('role', ['admin', 'designer'])
        ->where('is_active', true)
        ->get();

    // In a real application, you would send emails or notifications here
    // For now, we'll just log it
    Log::info('Critical maintenance issue reported', [
        'request_number' => $maintenanceRequest->request_number,
        'title' => $maintenanceRequest->title,
        'priority' => $maintenanceRequest->priority,
        'users_notified' => $usersToNotify->pluck('email')->toArray()
    ]);

    // You can implement actual notifications like:
    // Notification::send($usersToNotify, new CriticalMaintenanceIssue($maintenanceRequest));
}

/**
 * Notify customer about their maintenance request
 */
private function notifyCustomer(MaintenanceRequest $maintenanceRequest)
{
    $customer = $maintenanceRequest->designRequest->customer;

    if ($customer) {
        Log::info('Customer notified about maintenance request', [
            'request_number' => $maintenanceRequest->request_number,
            'customer_email' => $customer->email,
            'customer_name' => $customer->name
        ]);

        // You can implement actual customer notification like:
        // $customer->notify(new MaintenanceRequestCreated($maintenanceRequest));
    }
}

/**
 * Notify maintenance team about new request
 */
private function notifyMaintenanceTeam(MaintenanceRequest $maintenanceRequest)
{
    $maintenanceTeam = \App\Models\User::whereIn('role', ['admin', 'technician', 'designer'])
        ->where('is_active', true)
        ->get();

    Log::info('Maintenance team notified about new request', [
        'request_number' => $maintenanceRequest->request_number,
        'team_members_notified' => $maintenanceTeam->pluck('email')->toArray()
    ]);

    // You can implement actual team notifications like:
    // Notification::send($maintenanceTeam, new NewMaintenanceRequest($maintenanceRequest));
}

// In your MaintenanceController
public function store(Request $request)
{
    $validated = $request->validate([
        'design_request_id' => 'required|exists:design_requests,id',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,medium,high,critical',
        'issue_type' => 'required|in:fibre_cut,equipment_failure,signal_degradation,power_issue,environmental,preventive_maintenance,other',
        'location' => 'required|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    try {
        $maintenanceRequest = MaintenanceRequest::create([
            'design_request_id' => $validated['design_request_id'],
            'reported_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'issue_type' => $validated['issue_type'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'reported_at' => now(),
        ]);

        // Notify relevant users based on priority
        if ($maintenanceRequest->priority === 'critical') {
            $this->notifyCriticalIssue($maintenanceRequest);
        }

        // Notify customer
        $this->notifyCustomer($maintenanceRequest);

        // Notify designers/admins for new requests
        $this->notifyMaintenanceTeam($maintenanceRequest);

        return redirect()->route('maintenance.requests.show', $maintenanceRequest->id)
            ->with('success', 'Maintenance request created successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create maintenance request: ' . $e->getMessage());
    }
}

public function reports(Request $request)
{
    $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // Set default date range if not provided
    if (!$startDate || !$endDate) {
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subMonths(3)->format('Y-m-d'); // Default to last 3 months
    }

    try {
        // Maintenance Requests Statistics
        $requestsStats = [
            'total' => MaintenanceRequest::count(),
            'open' => MaintenanceRequest::whereIn('status', ['open', 'assigned'])->count(),
            'in_progress' => MaintenanceRequest::where('status', 'in_progress')->count(),
            'completed' => MaintenanceRequest::where('status', 'completed')->count(),
            'critical' => MaintenanceRequest::where('priority', 'critical')->count(),
        ];

        // Work Order Statistics
        $workOrderStats = [
            'total' => MaintenanceWorkOrder::count(),
            'assigned' => MaintenanceWorkOrder::where('status', 'assigned')->count(),
            'in_progress' => MaintenanceWorkOrder::where('status', 'in_progress')->count(),
            'completed' => MaintenanceWorkOrder::where('status', 'completed')->count(),
            'overdue' => MaintenanceWorkOrder::where('due_date', '<', now())
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count(),
        ];

        // Equipment Statistics
        $equipmentStats = [
            'total' => MaintenanceEquipment::count(),
            'available' => MaintenanceEquipment::where('status', 'available')->count(),
            'in_use' => MaintenanceEquipment::where('status', 'in_use')->count(),
            'maintenance' => MaintenanceEquipment::where('status', 'maintenance')->count(),
            'calibration_due' => MaintenanceEquipment::where('next_calibration', '<=', now()->addDays(30))->count(),
        ];

        // Monthly completion trend
        $completionTrend = MaintenanceRequest::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('YEAR(updated_at) as year, MONTH(updated_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Technician performance
        $technicianPerformance = MaintenanceWorkOrder::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->with('technician')
            ->selectRaw('assigned_technician, COUNT(*) as completed_orders, AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_completion_time')
            ->groupBy('assigned_technician')
            ->get();

        // Priority distribution
        $priorityDistribution = MaintenanceRequest::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        // Maintenance type distribution
        $typeDistribution = MaintenanceRequest::selectRaw('maintenance_type, COUNT(*) as count')
            ->groupBy('maintenance_type')
            ->get();

        return view('maintenance.reports', compact(
            'requestsStats',
            'workOrderStats',
            'equipmentStats',
            'completionTrend',
            'technicianPerformance',
            'priorityDistribution',
            'typeDistribution',
            'period',
            'startDate',
            'endDate'
        ));

    } catch (\Exception $e) {
        Log::error('Maintenance reports error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Unable to generate reports. Please try again.');
    }
}

public function workOrders(Request $request)
{
    $status = $request->get('status', 'all');
    $priority = $request->get('priority', 'all');
    $technician = $request->get('technician', 'all');

    // Build the query with relationships
    $query = MaintenanceWorkOrder::with([
        'maintenanceRequest.equipment',
        'maintenanceRequest.designRequest.customer',
        'technician',
        'assignedBy'
    ]);

    // Apply status filter
    if ($status !== 'all') {
        $query->where('status', $status);
    }

    // Apply priority filter (through maintenance request)
    if ($priority !== 'all') {
        $query->whereHas('maintenanceRequest', function($q) use ($priority) {
            $q->where('priority', $priority);
        });
    }

    // Apply technician filter
    if ($technician !== 'all') {
        $query->where('assigned_technician', $technician);
    }

    // Search functionality
    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhereHas('maintenanceRequest', function($q) use ($search) {
                  $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
              })
              ->orWhereHas('technician', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }

    $workOrders = $query->orderBy('created_at', 'desc')->paginate(20);

    // Get technicians for filter dropdown
    $technicians = User::where('role', 'technician')
        ->where('status', 'active')
        ->orderBy('name')
        ->get();

    return view('maintenance.work-orders-index', compact(
        'workOrders',
        'status',
        'priority',
        'technician',
        'technicians'
    ));
}

public function storeWorkOrder(Request $request)
{
    $validated = $request->validate([
        'maintenance_request_id' => 'required|exists:maintenance_requests,id',
        'assigned_technician' => 'required|exists:users,id',
        'due_date' => 'nullable|date',
        'instructions' => 'nullable|string',
        'estimated_hours' => 'nullable|numeric|min:0.5',
    ]);

    try {
        $workOrder = MaintenanceWorkOrder::create([
            'maintenance_request_id' => $validated['maintenance_request_id'],
            'assigned_technician' => $validated['assigned_technician'],
            'due_date' => $validated['due_date'],
            'instructions' => $validated['instructions'],
            'estimated_hours' => $validated['estimated_hours'],
            'status' => 'assigned',
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
        ]);

        // Update maintenance request status
        MaintenanceRequest::where('id', $validated['maintenance_request_id'])
            ->update(['status' => 'assigned']);

        return redirect()->route('maintenance.work-orders.index')
            ->with('success', 'Work order created successfully!');

    } catch (\Exception $e) {
        Log::error('Work order creation failed: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create work order. Please try again.');
    }
}
public function showWorkOrder($id)
{
    $workOrder = MaintenanceWorkOrder::with([
        'maintenanceRequest.equipment',
        'maintenanceRequest.designRequest.customer',
        'technician',
        'assignedBy'
    ])->findOrFail($id);

    return view('maintenance.work-orders-show', compact('workOrder'));
}

public function createWorkOrder()
{
    $maintenanceRequests = MaintenanceRequest::where('status', 'open')
        ->with('equipment', 'designRequest.customer')
        ->get();

    $technicians = User::where('role', 'technician')
        ->where('status', 'active')  // Changed from 'status' to 'is_active'
        ->orderBy('name')
        ->get();

    return view('maintenance.work-orders-create', compact('maintenanceRequests', 'technicians'));
}

// In MaintenanceController.php

public function editWorkOrder($id)
{
    $workOrder = MaintenanceWorkOrder::with(['maintenanceRequest', 'technician'])->findOrFail($id);
    $technicians = User::where('role', 'technician')
        ->where('status', 'active') 
        ->orderBy('name')
        ->get();

    return view('maintenance.work-orders-edit', compact('workOrder', 'technicians'));
}

public function updateWorkOrder(Request $request, $id)
{
    $workOrder = MaintenanceWorkOrder::findOrFail($id);

    $validated = $request->validate([
        'assigned_technician' => 'required|exists:users,id',
        'status' => 'required|in:assigned,in_progress,completed,cancelled',
        'due_date' => 'nullable|date',
        'instructions' => 'nullable|string',
        'estimated_hours' => 'nullable|numeric|min:0.5',
        'technician_notes' => 'nullable|string',
        'actual_hours' => 'nullable|numeric|min:0.1',
        'completion_notes' => 'nullable|string',
    ]);

    try {
        $workOrder->update($validated);

        // Update completed_at timestamp if status changed to completed
        if ($validated['status'] == 'completed' && $workOrder->completed_at === null) {
            $workOrder->update(['completed_at' => now()]);
        }

        return redirect()->route('maintenance.work-orders.show', $workOrder->id)
            ->with('success', 'Work order updated successfully!');

    } catch (\Exception $e) {
        Log::error('Work order update failed: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update work order. Please try again.');
    }
}

public function updateWorkOrderStatus(Request $request, $id)
{
    $workOrder = MaintenanceWorkOrder::findOrFail($id);

    $request->validate([
        'status' => 'required|in:assigned,in_progress,completed,cancelled',
    ]);

    try {
        $workOrder->update([
            'status' => $request->status,
            'completed_at' => $request->status == 'completed' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Work order status updated successfully!');

    } catch (\Exception $e) {
        Log::error('Work order status update failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to update work order status.');
    }
}
}
