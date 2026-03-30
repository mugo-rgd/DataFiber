<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Ticket;
use App\Models\DesignRequest;
use App\Models\NetworkDevice;
use Carbon\Carbon;


class ICTEngineerController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('role:ict_engineer');
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Design Request Statistics (like PreSale Engineer)
        $designStats = [
            'pendingRequests' => DesignRequest::where('ict_engineer_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'assignedRequests' => DesignRequest::where('ict_engineer_id', $user->id)
                ->where('status', 'assigned')
                ->count(),
            'completedDesigns' => DesignRequest::where('ict_engineer_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'quotationsSent' => DesignRequest::where('ict_engineer_id', $user->id)
                ->whereHas('quotation', function($query) {
                    $query->where('status', 'sent');
                })
                ->count(),
            'activeProjects' => DesignRequest::where('ict_engineer_id', $user->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count(),
            'avgCompletionDays' => 5, // This would be calculated from actual data
            'conversionRate' => 75, // This would be calculated from actual data
            'overdueProjects' => DesignRequest::where('ict_engineer_id', $user->id)
                ->where('assigned_to_ict_at', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count()
        ];

        // Network Statistics
        $networkStats = [
            // 'activeNetworks' => NetworkDevice::where('status', 'active')->count(),
            'pendingTickets' => Ticket::where('assigned_to', $user->id)
                ->where('status', 'pending')
                ->count(),
            // 'serversOnline' => NetworkDevice::where('type', 'server')
                // ->where('status', 'active')
                // ->count(),
            'uptimePercentage' => 99.5,
            'usersManaged' => User::where('role', '!=', 'admin')->count(),
            // 'devicesOnline' => NetworkDevice::where('status', 'active')->count(),
            'avgResponseTime' => 150,
            'securityAlerts' => 3
        ];

        // Recent Design Requests (assigned to this ICT Engineer)
        $recentRequests = DesignRequest::where('ict_engineer_id', $user->id)
            ->with(['customer', 'designer'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Recent Tickets
        $recentTickets = Ticket::where('assigned_to', $user->id)
            ->orWhere('created_by', $user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // System Notifications
        $notifications = collect([
            (object)[
                'type' => 'network',
                'title' => 'Network Optimization Required',
                'message' => 'Bandwidth usage exceeding 85% on core switch',
                'created_at' => Carbon::now()->subHours(2)
            ],
            (object)[
                'type' => 'design',
                'title' => 'New Design Request Assigned',
                'message' => 'You have been assigned a new fiber route design',
                'created_at' => Carbon::now()->subHours(1)
            ],
            (object)[
                'type' => 'server',
                'title' => 'Server Backup Completed',
                'message' => 'Nightly backup completed successfully',
                'created_at' => Carbon::now()->subDays(1)
            ]
        ]);

        return view('ictengineer.dashboard', compact(
            'designStats',
            'networkStats',
            'recentRequests',
            'recentTickets',
            'notifications'
        ));
    }

    public function requests()
{
    $user = Auth::user();

    $requests = DesignRequest::where('ict_engineer_id', $user->id)
        ->with(['customer', 'designer', 'conditionalCertificate'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    return view('ictengineer.requests.index', compact('requests'));
}

public function updateStatus(Request $request, $id)
{
    \Log::info('=== UPDATE STATUS START ===');

    // Find the design request manually
    $designRequest = DesignRequest::find($id);

    if (!$designRequest) {
        \Log::error('DesignRequest not found with ID: ' . $id);
        return response()->json([
            'success' => false,
            'message' => 'Design request not found'
        ], 404);
    }

    \Log::info('DesignRequest ID: ' . $designRequest->id);
    \Log::info('Current status: ' . $designRequest->status);
    \Log::info('ICT Engineer ID: ' . $designRequest->ict_engineer_id);
    \Log::info('Auth ID: ' . Auth::id());
    \Log::info('Request data: ', $request->all());

    // Verify the request belongs to this ICT Engineer
    if ($designRequest->ict_engineer_id != Auth::id()) {
        \Log::warning('Unauthorized access attempt');
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access to update this design request.'
        ], 403);
    }

    $validated = $request->validate([
        'technical_status' => 'required|string|in:under_technical_review,technically_approved,technical_revisions_required,ready_for_acceptance,accepted,rejected',
        'technical_notes' => 'nullable|string|max:1000',
    ]);

    \Log::info('Validated data: ', $validated);

    // Update using save() instead of update()
    $designRequest->status = $validated['technical_status'];
    $designRequest->technical_status = $validated['technical_status'];
    $designRequest->technical_notes = $validated['technical_notes'] ?? NULL;
    $designRequest->technical_reviewed_at = now();

    $saved = $designRequest->save();

    \Log::info('Save result: ' . ($saved ? 'true' : 'false'));
    \Log::info('After save - status: ' . $designRequest->status);
    \Log::info('After save - technical_status: ' . $designRequest->technical_status);
    \Log::info('After save - technical_notes: ' . $designRequest->technical_notes);

    // Log the status change
    activity()
        ->performedOn($designRequest)
        ->causedBy(Auth::user())
        ->withProperties(['new_status' => $validated['technical_status']])
        ->log('ICT Engineer updated technical status');

    \Log::info('=== UPDATE STATUS END ===');

    return response()->json([
        'success' => true,
        'message' => 'Status updated successfully',
        'new_status' => $validated['technical_status'], 
    ]);
}
    // Show individual design request
    public function showRequest(DesignRequest $request)
    {
        // Check if request is assigned to this ICT Engineer
       if ($request->ict_engineer_id != Auth::id() && $request->designer_id != Auth::id()) {
    abort(403, 'Unauthorized access to this design request.');
}

        $request->load(['customer', 'designer', 'quotations', 'designItems']);

        return view('ictengineer.requests.show', compact('request'));
    }

public function updateRequest(Request $request, DesignRequest $designRequest)
{
    // Check authorization - using correct field name
    if ($designRequest->ict_engineer_id != Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    // Define validation rules based on what fields are being updated
    $validationRules = [
        'ict_status' => 'sometimes|required|in:pending_assignment,assigned,inspection_scheduled,inspection_completed,certificate_generated,certificate_sent,completed',
        'status' => 'sometimes|required|in:assigned,in_progress,review,completed',
        'inspection_date' => 'nullable|date|required_if:ict_status,inspection_scheduled',
        'inspection_notes' => 'nullable|string|required_if:ict_status,inspection_completed',
        'engineer_notes' => 'nullable|string',
        'notes' => 'nullable|string',
        'technical_specifications' => 'nullable|string',
        'priority' => 'sometimes|nullable|in:low,medium,high'
    ];

    $validated = $request->validate($validationRules);

    // Prepare update data
    $updateData = [
        'updated_at' => now()
    ];

    // Update ict_status if provided
    if ($request->has('ict_status')) {
        $updateData['ict_status'] = $validated['ict_status'];

        // Set timestamps based on ict_status changes
        switch ($validated['ict_status']) {
            case 'assigned':
                $updateData['assigned_to_ict_at'] = now();
                break;
            case 'inspection_completed':
                $updateData['inspection_completed_at'] = now();
                break;
            case 'certificate_generated':
                $updateData['certificate_generated_at'] = now();
                break;
            case 'completed':
                $updateData['design_completed_at'] = now();
                break;
        }
    }

    // Update general status if provided
    if ($request->has('status')) {
        $updateData['status'] = $validated['status'];
    }

    // Update inspection-related fields
    if ($request->has('inspection_date')) {
        $updateData['inspection_date'] = $validated['inspection_date'];
    }

    if ($request->has('inspection_notes')) {
        $updateData['inspection_notes'] = $validated['inspection_notes'];
    }

    // Update notes - handle both engineer_notes and notes fields
    if ($request->has('engineer_notes')) {
        $updateData['engineer_notes'] = $validated['engineer_notes'];
    }

    if ($request->has('notes')) {
        // If you have an ict_notes field
        $updateData['ict_notes'] = $validated['notes'];
    }

    // Update technical specifications
    if ($request->has('technical_specifications')) {
        $updateData['technical_specifications'] = $validated['technical_specifications'];
    }

    // Update priority if provided
    if ($request->has('priority')) {
        $updateData['priority'] = $validated['priority'];
    }

    // Update the design request
    $designRequest->update($updateData);

    return redirect()->route('ictengineer.requests.show', $designRequest->id)
        ->with('success', 'Design request updated successfully!');
}
public function reports()
{
    // Get design requests assigned to the current ICT engineer
    $requests = DesignRequest::where('ict_engineer_id', Auth::id())
        ->with(['customer', 'designer', 'quotations'])
        ->orderBy('created_at', 'desc')
        ->get();

    // Calculate statistics
    $totalRequests = $requests->count();
    $inProgress = $requests->where('ict_status', 'inspection_scheduled')->count() +
                  $requests->where('ict_status', 'inspection_completed')->count();
    $completed = $requests->where('ict_status', 'completed')->count();
    $pending = $requests->where('ict_status', 'assigned')->count();

    // Calculate financial data if you have cost fields
    $totalCost = $requests->sum('estimated_cost') ?? 0;

    return view('ictengineer.reports', compact(
        'requests',
        'totalRequests',
        'inProgress',
        'completed',
        'pending',
        'totalCost'
    ));
}

public function helpdesk()
{
    // Get helpdesk tickets assigned to the current ICT engineer
    $tickets = Ticket::where('assigned_to', Auth::id())
        ->with('customer') // Assuming you have a customer relationship
        ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    // Get ticket statistics
    $openTickets = Ticket::where('assigned_to', Auth::id())
        ->whereIn('status', ['open', 'pending'])
        ->count();

    $highPriorityTickets = Ticket::where('assigned_to', Auth::id())
        ->whereIn('priority', ['high', 'urgent'])
        ->whereIn('status', ['open', 'pending'])
        ->count();

    return view('ictengineer.helpdesk', compact('tickets', 'openTickets', 'highPriorityTickets'));
}
    // Network Monitor
    public function networkMonitor()
    {
        return view('ictengineer.network-monitor');
    }

    // Users Management
    public function users()
    {
        $users = User::where('role', '!=', 'admin')
            ->orderBy('name')
            ->paginate(20);

        return view('ictengineer.users', compact('users'));
    }

    // Tickets Management
    public function tickets()
    {
        $tickets = Ticket::where('assigned_to', Auth::id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ictengineer.helpdesk', compact('tickets'));
    }
//  public function tickets()
// {
//     return redirect()->route('ictengineer.helpdesk');
// }
   public function showTicket(Ticket $ticket)
{
    // Check if ticket is assigned to current user
    if ($ticket->assigned_to != Auth::id()) {
        abort(403, 'Unauthorized access to this ticket.');
    }

    $ticket->load('customer', 'assignee');

    return view('ictengineer.tickets.show', compact('ticket'));
}

public function updateTicket(Request $request, Ticket $ticket)
{
    // Check if ticket is assigned to current user
    if ($ticket->assigned_to != Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $validated = $request->validate([
        'status' => 'required|in:open,pending,resolved,closed',
        'priority' => 'required|in:low,medium,high,urgent',
        'description' => 'nullable|string' // For adding updates/comments
    ]);

    $ticket->update($validated);

    return redirect()->route('ictengineer.tickets.show', $ticket->id)
        ->with('success', 'Ticket updated successfully.');
}

    // County-specific management
    public function county()
    {
        $user = Auth::user();
        $county = $user->county;

        if (!$county) {
            return redirect()->route('ictengineer.dashboard')
                ->with('error', 'No county assigned to your account.');
        }

        // Get county-specific design requests
        $countyRequests = DesignRequest::whereHas('customer', function($query) use ($county) {
            $query->where('county_id', $county->id);
        })->where('ict_engineer_id', $user->id)
          ->with('customer')
          ->paginate(20);

        return view('ictengineer.county', compact('county', 'countyRequests'));
    }

    // Other methods remain the same...
    public function servers()
    {
        $servers = NetworkDevice::where('type', 'server')
            ->orderBy('name')
            ->paginate(20);

        return view('ictengineer.servers', compact('servers'));
    }

    public function equipment()
    {
        $equipment = NetworkDevice::where('type', '!=', 'server')
            ->orderBy('name')
            ->paginate(20);

        return view('ictengineer.equipment', compact('equipment'));
    }


}
