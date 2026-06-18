<?php

namespace App\Http\Controllers;

use App\Models\CommercialRoute;
use App\Models\DesignRequest;
use App\Models\Lease;
use App\Models\MaintenanceEquipment;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceWorkOrder;
use App\Models\SurveyRoute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
 public function dashboard()
    {
        $user = Auth::user();

        // Role-based dashboard routing
        $adminRoles = ['admin', 'account_manager', 'finance_manager'];
        $techRoles = ['technician', 'maintenance_tech', 'field_tech'];

        if ($user->role === 'customer') {
            return $this->customerDashboard();
        } elseif ($user->role === 'designer') {
            return $this->designerDashboard();
        } elseif ($user->role === 'surveyor') {
            return $this->surveyorDashboard();
        } elseif (in_array($user->role, $adminRoles)) {
            return $this->adminDashboard();
        } elseif (in_array($user->role, $techRoles)) {
            return $this->technicianDashboard();
        } else {
            \Log::warning('Unauthorized maintenance dashboard access', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_email' => $user->email
            ]);
            abort(403, 'You do not have permission to access the maintenance dashboard.');
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

public function index(Request $request)
{
    $user = Auth::user();
    $status = $request->get('status', 'all');
    $priority = $request->get('priority', 'all');
    $type = $request->get('type', 'all');

    // Build the query with relationships - USE LEASE instead of commercialRoute
    $query = MaintenanceRequest::with([
        'lease',              // Changed from commercialRoute to lease
        'lease.customer',     // Eager load the customer through lease
        'reporter',           // Keep this
        'customer',           // Keep if you have customer relationship
        'workOrders'          // Keep this
    ]);

    // Apply role-based filters
    if ($user->role === 'customer') {
        // Filter by customer_id if column exists, or through lease
        if (Schema::hasColumn('maintenance_requests', 'customer_id')) {
            $query->where('customer_id', $user->id);
        } else {
            // Filter through lease relationship
            $query->whereHas('lease', function($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }
    } elseif ($user->role === 'technician') {
        // Technicians see requests where they have work orders assigned
        $query->whereHas('workOrders', function($q) use ($user) {
            $q->where('assigned_technician', $user->id);
        });
    }
    // Admin, finance, designer can see all requests (no additional filtering)

    // Apply status filter
    if ($status !== 'all') {
        $query->where('status', $status);
    }

    // Apply priority filter
    if ($priority !== 'all') {
        $query->where('priority', $priority);
    }

    // Apply issue type filter
    if ($type !== 'all') {
        $query->where('issue_type', $type);
    }

    // Search functionality - Updated to search lease information
    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('request_number', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              // Search lease information
              ->orWhereHas('lease', function($q) use ($search) {
                  $q->where('lease_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
              })
              // Search customer information through lease
              ->orWhereHas('lease.customer', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
              });
        });
    }

    $requests = $query->orderBy('created_at', 'desc')->paginate(20);

    // Get technicians for the assign modal
    $technicians = User::where('role', 'technician')
        ->where('status', 'active')
        ->orderBy('name')
        ->get();

    return view('maintenance.requests-index', compact('requests', 'status', 'priority', 'type', 'technicians'));
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

public function create(Request $request)
{
    // Get only customers assigned to the logged-in account manager
    $customers = User::where('role', 'customer')
        ->where('account_manager_id', Auth::id())
        ->where('status', 'active')
        ->orderBy('name')
        ->get(['id', 'name', 'company_name']);

    // Get selected customer ID from URL parameter
    $selectedCustomerId = $request->get('customer_id');

    // Get leases for selected customer
    $leases = collect();
    $selectedCustomer = null;

    if ($selectedCustomerId) {
        // Verify the customer belongs to this account manager
        $selectedCustomer = $customers->firstWhere('id', $selectedCustomerId);

        if ($selectedCustomer) {
            $leases = Lease::where('customer_id', $selectedCustomerId)
                ->where('status', 'active')
                ->get(['id', 'lease_number', 'title', 'monthly_cost', 'currency']);
        }
    }

    return view('maintenance.requests.create', compact('customers', 'leases', 'selectedCustomerId', 'selectedCustomer'));
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
            'customer_id' => 'required|exists:users,id',
            'lease_id' => 'required|exists:leases,id',
            'priority' => 'required|in:low,medium,high,critical',
            'issue_type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $maintenanceRequest = MaintenanceRequest::create([
                'request_number' => $this->generateRequestNumber(),
                'customer_id' => $validated['customer_id'],
                'lease_id' => $validated['lease_id'],
                'priority' => $validated['priority'],
                'issue_type' => $validated['issue_type'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location' => $validated['location'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'open',
                'reported_by' => Auth::id(),
                'reported_at' => now(),
            ]);

            return redirect()->route('maintenance.requests.show', $maintenanceRequest->id)
                ->with('success', 'Maintenance request created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating maintenance request: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create maintenance request: ' . $e->getMessage());
        }
    }

/**
 * Generate unique request number
 */
private function generateRequestNumber()
    {
        $prefix = 'MR';
        $year = date('Y');
        $month = date('m');

        $lastRequest = MaintenanceRequest::where('request_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRequest) {
            $parts = explode('-', $lastRequest->request_number);
            $lastSeq = (int) end($parts);
            $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newSeq}";
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

    // Fix: Use 'is_active' instead of 'status', or remove the where clause
    $technicians = User::where('role', 'technician')
        // ->where('status', 'active')  // Comment this out if the column doesn't exist
        // Or use: ->where('is_active', true)
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
/**
 * Display the specified maintenance request.
 */
public function show($id)
{
    $maintenanceRequest = MaintenanceRequest::with([
        'commercialRoute',
        'customer',  // Add this
        'reporter',
        'workOrders.technician'
    ])->findOrFail($id);

    // Get technicians for the create work order modal
    $technicians = User::where('role', 'technician')
        ->where('status', 'active')
        ->orderBy('name')
        ->get();

    return view('maintenance.requests.show', compact('maintenanceRequest', 'technicians'));
}

/**
 * Show the form for editing the specified maintenance request.
 */
public function edit($id)
{
    $maintenanceRequest = MaintenanceRequest::with(['commercialRoute', 'customer'])->findOrFail($id);

    // Only allow editing if request is open or assigned
    if (!in_array($maintenanceRequest->status, ['open', 'assigned'])) {
        return redirect()->route('maintenance.requests.show', $maintenanceRequest->id)
            ->with('error', 'This maintenance request cannot be edited because it is already ' . $maintenanceRequest->status);
    }

    // Get commercial routes for the dropdown
    $routes = CommercialRoute::orderBy('region')
        ->orderBy('name_of_route')
        ->get();

    // Get customers for the dropdown
    $customers = User::where('role', 'customer')
        ->where('status', 'active')
        ->orderBy('name')
        ->get(['id', 'name', 'company_name']);

    return view('maintenance.requests.edit', compact('maintenanceRequest', 'routes', 'customers'));
}

/**
 * Update the specified maintenance request.
 */
public function update(Request $request, $id)
{
    $maintenanceRequest = MaintenanceRequest::findOrFail($id);

    // Only allow editing if request is open or assigned
    if (!in_array($maintenanceRequest->status, ['open', 'assigned'])) {
        return redirect()->route('maintenance.requests.show', $maintenanceRequest->id)
            ->with('error', 'This maintenance request cannot be edited because it is already ' . $maintenanceRequest->status);
    }

    $validated = $request->validate([
        'customer_id' => 'nullable|exists:users,id',
        'commercial_route_id' => 'required|exists:commercial_routes,id',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,medium,high,critical',
        'issue_type' => 'required|in:fibre_cut,equipment_failure,signal_degradation,power_issue,environmental,preventive_maintenance,other',
        'location' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    try {
        $maintenanceRequest->update($validated);

        Log::info('Maintenance request updated', [
            'request_id' => $maintenanceRequest->id,
            'request_number' => $maintenanceRequest->request_number,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('maintenance.requests.show', $maintenanceRequest->id)
            ->with('success', 'Maintenance request #' . $maintenanceRequest->request_number . ' updated successfully!');

    } catch (\Exception $e) {
        Log::error('Failed to update maintenance request: ' . $e->getMessage());

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update maintenance request: ' . $e->getMessage());
    }
}

/**
 * Remove the specified maintenance request.
 */
public function destroy($id)
{
    $maintenanceRequest = MaintenanceRequest::findOrFail($id);

    // Only allow deletion if request is open
    if ($maintenanceRequest->status !== 'open') {
        return redirect()->route('maintenance.requests.index')
            ->with('error', 'Only open maintenance requests can be deleted.');
    }

    try {
        $requestNumber = $maintenanceRequest->request_number;
        $maintenanceRequest->delete();

        Log::info('Maintenance request deleted', [
            'request_number' => $requestNumber,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('maintenance.requests.index')
            ->with('success', 'Maintenance request #' . $requestNumber . ' deleted successfully!');

    } catch (\Exception $e) {
        Log::error('Failed to delete maintenance request: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Failed to delete maintenance request: ' . $e->getMessage());
    }
}

public function addCompensation(Request $request, $id)
{
    $maintenanceRequest = MaintenanceRequest::findOrFail($id);

    // Add compensation note to description
    $newDescription = $maintenanceRequest->description . "\n" . $request->compensation_note;
    $maintenanceRequest->update(['description' => $newDescription]);

    // Store compensation amount if you have a column for it
    $maintenanceRequest->compensation_amount = $request->compensation_amount;
    $maintenanceRequest->compensation_currency = $request->compensation_currency;
    $maintenanceRequest->save();

    return response()->json(['success' => true]);
}

/**
 * Get active leases for a specific customer (AJAX endpoint)
 */
public function getCustomerLeases($customerId)
    {
        try {
            $leases = Lease::where('customer_id', $customerId)
                ->where('status', 'active')
                ->select('id', 'lease_number', 'title', 'monthly_cost', 'currency', 'customer_id')
                ->orderBy('lease_number')
                ->get();

            return response()->json([
                'success' => true,
                'leases' => $leases,
                'count' => $leases->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
