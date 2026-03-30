<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CustomerSupportTicket;
use App\Models\DesignRequest;
use App\Models\PaymentFollowup;
use App\Models\Lease;
use App\Models\LeaseBilling;
use App\Models\Transaction;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Activitylog\ActivityLogger;
use Illuminate\Support\Facades\Hash; // Add this

/** @var \App\Models\User $user */
/**
 * Helper function declaration for Intelephense
 */

class AccountManagerController extends Controller
{
    /**
     * Display account manager dashboard with comprehensive stats and analytics
     */
  public function dashboard()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (!$user->isAccountManager()) {
        abort(403, 'Unauthorized access. Account manager role required.');
    }

    // Comprehensive statistics
    $stats = $this->getAccountManagerStats($user);

    // Recent activities
    $recentActivities = $this->getRecentActivities($user);

    // Performance metrics
    $performanceMetrics = $this->getPerformanceMetrics($user);

    // Upcoming deadlines
    $upcomingDeadlines = $this->getUpcomingDeadlines($user);

    // Customer alerts
    $customerAlerts = $this->getCustomerAlerts($user);

    // Charts data
    $charts = $this->getDashboardCharts($user);

    // Add recent tickets data
    $recentTickets = CustomerSupportTicket::where('account_manager_id', $user->id)
        ->with('customer')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    // Add upcoming payments data
    $upcomingPayments = PaymentFollowup::where('account_manager_id', $user->id)
        ->with('customer')
        ->whereIn('status', ['pending', 'reminded'])
        ->orderBy('due_date', 'asc')
        ->limit(5)
        ->get();

    return view('account-manager.dashboard', compact(
        'stats',
        'recentActivities',
        'performanceMetrics',
        'upcomingDeadlines',
        'customerAlerts',
        'charts',
        'recentTickets',
        'upcomingPayments'
    ));
}

    /**
     * Get comprehensive account manager statistics
     */
 private function getAccountManagerStats($user)
{
    return Cache::remember("account_manager_stats_{$user->id}", 300, function() use ($user) {
        $ticketQuery = CustomerSupportTicket::where('account_manager_id', $user->id);
        $paymentQuery = PaymentFollowup::where('account_manager_id', $user->id);

        return [
            'total_customers' => $user->managedCustomers()->count(),
            'active_customers' => $user->managedCustomers()->where('status', 'active')->count(),
            'open_tickets' => $ticketQuery->whereIn('status', ['open', 'in_progress'])->count(),
            'high_priority_tickets' => $ticketQuery->where('priority', 'high')
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),
            'pending_payments' => $paymentQuery->whereIn('status', ['pending', 'reminded'])->count(),
            'overdue_payments' => $paymentQuery->where('due_date', '<', now())
                ->whereIn('status', ['pending', 'reminded'])
                ->count(),
            'satisfaction_score' => $this->calculateSatisfactionScore($user->id),
            'collection_rate' => $this->calculateCollectionRate($user->id),
            'revenue_managed' => $this->getManagedRevenue($user->id),
            'active_leases' => $user->managedLeases()->where('leases.status', 'active')->count(), // FIXED: Added table prefix
            'pending_documents' => $this->getPendingDocumentsCount($user->id),
        ];
    });
}

    /**
     * Calculate customer satisfaction score with caching
     */
    private function calculateSatisfactionScore($accountManagerId)
    {
        return Cache::remember("satisfaction_score_{$accountManagerId}", 300, function() use ($accountManagerId) {
            try {
                $totalTickets = CustomerSupportTicket::where('account_manager_id', $accountManagerId)->count();
                $resolvedTickets = CustomerSupportTicket::where('account_manager_id', $accountManagerId)
                    ->where('status', 'resolved')
                    ->count();

                if ($totalTickets > 0) {
                    $resolutionRate = ($resolvedTickets / $totalTickets) * 100;
                    return min(100, max(0, round($resolutionRate)));
                }

                return 100;
            } catch (\Exception $e) {
                Log::error("Error calculating satisfaction score: " . $e->getMessage());
                return 0;
            }
        });
    }

    /**
     * Calculate collection rate for managed customers
     */
    private function calculateCollectionRate($accountManagerId)
    {
        return Cache::remember("collection_rate_{$accountManagerId}", 300, function() use ($accountManagerId) {
            try {
                $totalBilled = LeaseBilling::whereHas('customer', function($query) use ($accountManagerId) {
                    $query->where('account_manager_id', $accountManagerId);
                })->sum('total_amount');

                $totalCollected = LeaseBilling::whereHas('customer', function($query) use ($accountManagerId) {
                    $query->where('account_manager_id', $accountManagerId);
                })->where('status', 'paid')->sum('total_amount');

                return $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 2) : 100;
            } catch (\Exception $e) {
                Log::error("Error calculating collection rate: " . $e->getMessage());
                return 0;
            }
        });
    }

    /**
     * Get total revenue managed by account manager
     */
    private function getManagedRevenue($accountManagerId)
    {
        return LeaseBilling::whereHas('customer', function($query) use ($accountManagerId) {
            $query->where('account_manager_id', $accountManagerId);
        })->where('status', 'paid')->sum('total_amount');
    }

    /**
     * Get count of pending documents for managed customers
     */
    private function getPendingDocumentsCount($accountManagerId)
    {
        return Document::whereHas('user', function($query) use ($accountManagerId) {
            $query->where('account_manager_id', $accountManagerId);
        })->where('status', 'pending')->count();
    }

    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities($user)
    {
        $activities = [];

        // Recent tickets
        $recentTickets = CustomerSupportTicket::with(['customer:id,name,email'])
            ->where('account_manager_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentTickets as $ticket) {
            $activities[] = [
                'type' => 'ticket',
                'icon' => 'ticket-alt',
                'color' => $this->getTicketPriorityColor($ticket->priority),
                'title' => "New Support Ticket: {$ticket->title}",
                'description' => "From: {$ticket->customer->name}",
                'time' => $ticket->created_at->diffForHumans(),
                'link' => route('account-manager.tickets.show', $ticket->id)
            ];
        }

        // Recent payments
        $recentPayments = PaymentFollowup::with(['customer:id,name,email'])
            ->where('account_manager_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentPayments as $payment) {
            $activities[] = [
                'type' => 'payment',
                'icon' => 'credit-card',
                'color' => $payment->status === 'paid' ? 'success' : 'warning',
                'title' => "Payment {$payment->status}: $" . number_format($payment->amount ?? 0.00, 2),
                'description' => "Customer: {$payment->customer->name}",
                'time' => $payment->created_at->diffForHumans(),
                'link' => route('account-manager.payments.index')
            ];
        }

        // Sort by time and return latest 10
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get performance metrics for dashboard
     */
    private function getPerformanceMetrics($user)
    {
        return [
            [
                'label' => 'Ticket Resolution Rate',
                'value' => $this->calculateSatisfactionScore($user->id),
                'target' => 90,
                'percentage' => $this->calculateSatisfactionScore($user->id),
                'color' => 'success',
                'unit' => '%'
            ],
            [
                'label' => 'Collection Rate',
                'value' => $this->calculateCollectionRate($user->id),
                'target' => 95,
                'percentage' => $this->calculateCollectionRate($user->id),
                'color' => 'info',
                'unit' => '%'
            ],
            [
                'label' => 'Customer Satisfaction',
                'value' => 88, // This would come from customer feedback system
                'target' => 90,
                'percentage' => 97.8,
                'color' => 'warning',
                'unit' => '%'
            ]
        ];
    }

    /**
     * Get upcoming deadlines
     */
    private function getUpcomingDeadlines($user)
    {
        return PaymentFollowup::with(['customer:id,name,email'])
            ->where('account_manager_id', $user->id)
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->whereIn('status', ['pending', 'reminded'])
            ->orderBy('due_date')
            ->take(10)
            ->get();
    }

    /**
     * Get customer alerts and notifications
     */
    private function getCustomerAlerts($user)
    {
        $alerts = [];

        // Overdue payments
        $overdueCount = PaymentFollowup::where('account_manager_id', $user->id)
            ->where('due_date', '<', now())
            ->whereIn('status', ['pending', 'reminded'])
            ->count();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => 'Overdue Payments',
                'message' => "You have {$overdueCount} overdue payments requiring immediate attention.",
                'link' => route('account-manager.payments.index')
            ];
        }

        // High priority tickets
        $highPriorityCount = CustomerSupportTicket::where('account_manager_id', $user->id)
            ->where('priority', 'high')
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        if ($highPriorityCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'exclamation-circle',
                'title' => 'High Priority Tickets',
                'message' => "You have {$highPriorityCount} high priority tickets awaiting resolution.",
                'link' => route('account-manager.tickets.index')
            ];
        }

        // Pending documents
        $pendingDocsCount = $this->getPendingDocumentsCount($user->id);
        if ($pendingDocsCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'file-alt',
                'title' => 'Pending Documents',
                'message' => "There are {$pendingDocsCount} documents awaiting approval.",
                'link' => route('account-manager.documents.approve', ['user' => $user->id])
            ];
        }

        return $alerts;
    }
/**
 * Display a listing of account managers (Admin view)
 */
public function index(Request $request)
{
    // // Check if user is admin
    // // if (!in_array(auth()->user()->role, ['admin', 'system_admin'])) {
    // //     // abort(403, 'Unauthorized access. Admin access required.');
    // }

    $query = User::where('role', 'account_manager');

    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%");
        });
    }

    // Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Get managers with customer count
    $managers = $query->withCount('managedCustomers')
        ->orderBy('name')
        ->paginate(15)
        ->withQueryString();

    // Get statistics
    $stats = [
        'total_managers' => User::where('role', 'account_manager')->count(),
        'active_managers' => User::where('role', 'account_manager')->where('status', 'active')->count(),
        'inactive_managers' => User::where('role', 'account_manager')->where('status', 'inactive')->count(),
        'total_customers_managed' => User::where('role', 'customer')->whereNotNull('account_manager_id')->count(),
        'avg_customers_per_manager' => User::where('role', 'account_manager')
            ->withCount('managedCustomers')
            ->get()
            ->avg('managed_customers_count') ?? 0,
    ];

    return view('admin.account-managers.index', compact('managers', 'stats'));
}
/**
 * Show form to create a new account manager
 */
public function create()
{
    // if (!in_array(auth()->user()->role, ['admin', 'system_admin'])) {
    //     abort(403, 'Unauthorized access. Admin access required.');
    // }

    return view('admin.account-managers.create');
}

/**
 * Store a newly created account manager
 */
public function store(Request $request)
{
    // if (!in_array(auth()->user()->role, ['admin', 'system_admin'])) {
    //     abort(403, 'Unauthorized access. Admin access required.');
    // }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'phone' => 'nullable|string|max:20',
        'company_name' => 'nullable|string|max:255',
        'password' => 'required|string|min:8|confirmed',
        'status' => 'required|in:active,inactive',
    ]);

    try {
        $manager = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'password' => Hash::make($request->password),
            'role' => 'account_manager',
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.account-managers.index')
            ->with('success', 'Account manager created successfully.');

    } catch (\Exception $e) {
        return back()->withInput()
            ->with('error', 'Failed to create account manager: ' . $e->getMessage());
    }
}
/**
 * Display account manager details
 */
public function show($id)
{
    // if (!in_array(auth()->user()->role, ['admin', 'system_admin'])) {
    //     abort(403, 'Unauthorized access. Admin access required.');
    // }

   $manager = User::where('role', 'account_manager')
        ->with(['managedCustomers' => function($query) {
            $query->select(
                'id',
                'name',
                'email',
                'company_name',
                'status',
                'account_manager_id',  // Include this to verify the relationship
                'assigned_at',
                'assignment_notes',
                'phone',
                'created_at'
            )
            ->where('role', 'customer')  // Explicitly filter for customers
            ->orderBy('name');
        }])
        ->withCount(['managedCustomers' => function($query) {
            $query->where('role', 'customer');  // Count only customers
        }])
        ->findOrFail($id);

    // Debug log to verify data
    \Log::info('Manager ' . $id . ' customers:', [
        'count' => $manager->managedCustomers->count(),
        'customers' => $manager->managedCustomers->toArray()
    ]);

    return view('admin.account-managers.show', compact('manager'));}
/**
 * Show form to edit account manager
 */
public function edit($id)
{
    // if (!in_array(auth()->user()->role, ['admin', 'system_admin'])) {
    //     abort(403, 'Unauthorized access. Admin access required.');
    // }

    $manager = User::where('role', 'account_manager')->findOrFail($id);

    return view('admin.account-managers.edit', compact('manager'));
}

/**
 * Update account manager
 */
public function update(Request $request, $id)
{
    // if (!in_array(auth()->user()->role, ['admin', 'system_admin'])) {
    //     abort(403, 'Unauthorized access. Admin access required.');
    // }

    $manager = User::where('role', 'account_manager')->findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        'phone' => 'nullable|string|max:20',
        'company_name' => 'nullable|string|max:255',
        'status' => 'required|in:active,inactive',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    try {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $manager->update($data);

        return redirect()->route('admin.account-managers.index')
            ->with('success', 'Account manager updated successfully.');

    } catch (\Exception $e) {
        return back()->withInput()
            ->with('error', 'Failed to update account manager: ' . $e->getMessage());
    }
}
/**
 * Get customers assigned to a specific account manager (for AJAX)
 */
public function getManagerCustomers($id)
{
    // if (!in_array(auth()->user()->role, ['admin', 'system_admin','account_manager'])) {
    //     return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    // }

    try {
        $manager = User::where('role', 'account_manager')->findOrFail($id);

        $customers = $manager->managedCustomers()
            ->select('id', 'name', 'email', 'status', 'assigned_at', 'company_name')
            ->orderBy('name')
            ->get();

        // Make sure assigned_at is a Carbon instance
        foreach ($customers as $customer) {
            if ($customer->assigned_at) {
                $customer->assigned_at = \Carbon\Carbon::parse($customer->assigned_at);
            }
        }

        // Render the view
        $html = view('admin.account-managers.partials.customer-list', [
            'customers' => $customers,
            'manager' => $manager
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $customers->count(),
            'message' => $customers->count() . ' customers found'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error loading customers: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to load customers: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Display analytics for account managers
 */
public function analytics()
{
    // if (!in_array(auth()->user()->role, ['admin', 'system_admin'])) {
    //     abort(403, 'Unauthorized access. Admin access required.');
    // }

    $managers = User::where('role', 'account_manager')
        ->withCount(['managedCustomers'])
        ->with(['managedCustomers' => function($query) {
            $query->select('users.id', 'users.status', 'users.created_at');
        }])
        ->get()
        ->map(function($manager) {
            return [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'status' => $manager->status,
                'total_customers' => $manager->managed_customers_count,
                'active_customers' => $manager->managedCustomers->where('status', 'active')->count(),
                'inactive_customers' => $manager->managedCustomers->where('status', 'inactive')->count(),
                'joined_at' => $manager->created_at->format('Y-m-d'),
                'customers_added_this_month' => $manager->managedCustomers()
                    ->where('assigned_at', '>=', now()->startOfMonth())
                    ->count(),
            ];
        });

    $summary = [
        'total_managers' => $managers->count(),
        'total_customers_managed' => $managers->sum('total_customers'),
        'avg_customers_per_manager' => round($managers->avg('total_customers'), 1),
        'active_managers' => $managers->where('status', 'active')->count(),
        'inactive_managers' => $managers->where('status', 'inactive')->count(),
    ];

    return view('admin.account-managers.analytics', compact('managers', 'summary'));
}
    /**
     * Get dashboard charts data
     */
    private function getDashboardCharts($user)
    {
        $revenueData = $this->getRevenueTrends($user->id);
        $ticketData = $this->getTicketStats($user->id);

        return [
            [
                'id' => 'revenueChart',
                'title' => 'Revenue Trends - Last 6 Months',
                'type' => 'line',
                'labels' => $revenueData['months'],
                'dataset' => [
                    'label' => 'Monthly Revenue',
                    'data' => $revenueData['revenues'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)'
                ]
            ],
            [
                'id' => 'ticketChart',
                'title' => 'Ticket Status Distribution',
                'type' => 'doughnut',
                'labels' => ['Open', 'In Progress', 'Resolved', 'Closed'],
                'dataset' => [
                    'label' => 'Tickets',
                    'data' => [
                        $ticketData['open'],
                        $ticketData['in_progress'],
                        $ticketData['resolved'],
                        $ticketData['closed']
                    ],
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get revenue trends for charts
     */
    private function getRevenueTrends($accountManagerId)
    {
        $months = [];
        $revenues = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $revenue = LeaseBilling::whereHas('customer', function($query) use ($accountManagerId) {
                $query->where('account_manager_id', $accountManagerId);
            })
            ->where('status', 'paid')
            ->whereYear('paid_at', $date->year)
            ->whereMonth('paid_at', $date->month)
            ->sum('total_amount');

            $revenues[] = $revenue ?? 0;
        }

        return [
            'months' => $months,
            'revenues' => $revenues
        ];
    }

    /**
     * Get ticket statistics for charts
     */
    private function getTicketStats($accountManagerId)
    {
        return [
            'open' => CustomerSupportTicket::where('account_manager_id', $accountManagerId)
                ->where('status', 'open')->count(),
            'in_progress' => CustomerSupportTicket::where('account_manager_id', $accountManagerId)
                ->where('status', 'in_progress')->count(),
            'resolved' => CustomerSupportTicket::where('account_manager_id', $accountManagerId)
                ->where('status', 'resolved')->count(),
            'closed' => CustomerSupportTicket::where('account_manager_id', $accountManagerId)
                ->where('status', 'closed')->count(),
        ];
    }

    /**
     * Get ticket priority color
     */
    private function getTicketPriorityColor($priority)
    {
        return match($priority) {
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Display list of customers managed by current account manager with advanced filtering
     */
   public function myCustomers(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (!$user->isAccountManager()) {
        abort(403, 'Unauthorized access. Account manager role required.');
    }

    $query = $user->managedCustomers();

    // Apply filters
    if ($request->has('status') && $request->status !== 'all') {
        $query->where('status', $request->status); // FIXED: account_status → status
    }

    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%");
        });
    }

    // FIXED: Correct method chaining with counts
    $customers = $query->withCount([
            'supportTickets as open_tickets_count' => function($query) {
                $query->whereIn('status', ['open', 'in_progress']);
            },
            'paymentFollowups as pending_payments_count' => function($query) {
                $query->whereIn('status', ['pending', 'reminded']);
            },
            'leases as active_leases_count' => function($query) {
                $query->where('status', 'active');
            }
        ])
        ->with(['leases' => function($query) {
            $query->where('status', 'active')->select(['id', 'customer_id', 'start_location', 'end_location', 'bandwidth']); // FIXED: location → start_location, end_location
        }])
        ->paginate(15)
        ->appends($request->query());

    $customerStats = [
        'total' => $user->managedCustomers()->count(),
        'active' => $user->managedCustomers()->where('status', 'active')->count(), // FIXED: account_status → status
        'inactive' => $user->managedCustomers()->where('status', 'inactive')->count(), // FIXED: account_status → status
        'suspended' => $user->managedCustomers()->where('status', 'suspended')->count(), // FIXED: account_status → status
    ];

    return view('account-manager.customers.index', compact('customers', 'customerStats'));
}

    /**
     * Display detailed view of a specific customer with comprehensive information
     */
    public function customerDetail(User $customer, Request $request)
    {
      $currentUser = Auth::user();

    // Authorization logic
    $isAdmin = in_array($currentUser->role, ['admin', 'system_admin']);
    $isAssignedAccountManager = $currentUser->role === 'account_manager' &&
                               $customer->account_manager_id === $currentUser->id &&
                               $customer->role === 'customer';

    if (!$isAdmin && !$isAssignedAccountManager) {
        abort(403, 'Unauthorized access to customer details.');
    }
        // Load customer relationships with optimized queries
        $customer->load([
            'supportTickets' => function($query) {
                $query->latest()->take(10);
            },
            'paymentFollowups' => function($query) {
                $query->latest()->take(10);
            },
            'leases' => function($query) {
                $query->with(['billings' => function($q) {
                    $q->latest()->take(5);
                }]);
            },
            'companyProfile',
            'documents'
        ]);

        // Comprehensive statistics
        $stats = [
            'total_tickets' => $customer->supportTickets->count(),
            'open_tickets' => $customer->supportTickets->whereIn('status', ['open', 'in_progress'])->count(),
            'total_payments' => $customer->paymentFollowups->count(),
            'pending_payments' => $customer->paymentFollowups->whereIn('status', ['pending', 'reminded'])->count(),
            'overdue_payments' => $customer->paymentFollowups->where('due_date', '<', now())
                ->whereIn('status', ['pending', 'reminded'])
                ->count(),
            'active_leases' => $customer->leases->where('status', 'active')->count(),
            'total_revenue' => $customer->leases->sum('monthly_rent'), // Adjust based on your revenue calculation
            'profile_completion' => $customer->profile_completion_percentage,
        ];

        // Recent activity timeline
        $recentActivity = $this->getCustomerRecentActivity($customer);

        // Financial overview
        $financialOverview = $this->getCustomerFinancialOverview($customer);

        return view('account-manager.customers.detail', compact(
            'customer',
            'stats',
            'recentActivity',
            'financialOverview'
        ));
    }


    /**
     * Get customer recent activity timeline
     */
    private function getCustomerRecentActivity($customer)
    {
        $activities = [];

        // Recent tickets
        foreach ($customer->supportTickets->take(5) as $ticket) {
            $activities[] = [
                'type' => 'ticket',
                'icon' => 'ticket-alt',
                'title' => "Support Ticket: {$ticket->title}",
                'description' => "Status: " . ucfirst($ticket->status),
                'time' => $ticket->created_at->diffForHumans(),
                'color' => $this->getTicketStatusColor($ticket->status)
            ];
        }

        // Recent payments
        foreach ($customer->paymentFollowups->take(5) as $payment) {
            $activities[] = [
                'type' => 'payment',
                'icon' => 'credit-card',
                'title' => "Payment: $" . number_format($payment->amount, 2),
                'description' => "Due: " . $payment->due_date->format('M d, Y'),
                'time' => $payment->created_at->diffForHumans(),
                'color' => $payment->status === 'paid' ? 'success' : 'warning'
            ];
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get customer financial overview
     */
    private function getCustomerFinancialOverview($customer)
    {
        return [
            'total_billed' => LeaseBilling::where('customer_id', $customer->id)->sum('total_amount'),
            'total_paid' => LeaseBilling::where('customer_id', $customer->id)->where('status', 'paid')->sum('total_amount'),
            'pending_amount' => LeaseBilling::where('customer_id', $customer->id)->where('status', 'pending')->sum('total_amount'),
            'overdue_amount' => LeaseBilling::where('customer_id', $customer->id)->where('status', 'overdue')->sum('total_amount'),
            'average_payment_time' => $this->calculateAveragePaymentTime($customer->id),
        ];
    }

    /**
     * Calculate average payment time for customer
     */
    private function calculateAveragePaymentTime($customerId)
    {
        $paidBillings = LeaseBilling::where('customer_id', $customerId)
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->get();

        if ($paidBillings->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($paidBillings as $billing) {
            $totalDays += $billing->billing_date->diffInDays($billing->paid_at);
        }

        return round($totalDays / $paidBillings->count(), 1);
    }

    /**
     * Get ticket status color
     */
    private function getTicketStatusColor($status)
    {
        return match($status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'info'
        };
    }

    /**
     * Export customer report as PDF
     */
    public function exportCustomerReport(User $customer)
    {
        $currentUser = Auth::user();

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) && !in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $customer->load([
            'supportTickets',
            'paymentFollowups',
            'leases.billings',
            'companyProfile'
        ]);

        $stats = [
            'total_tickets' => $customer->supportTickets->count(),
            'open_tickets' => $customer->supportTickets->whereIn('status', ['open', 'in_progress'])->count(),
            'total_payments' => $customer->paymentFollowups->count(),
            'pending_payments' => $customer->paymentFollowups->whereIn('status', ['pending', 'reminded'])->count(),
        ];

        $pdf = Pdf::loadView('account-manager.customers.report-pdf', compact('customer', 'stats'));

        return $pdf->download("customer-report-{$customer->id}.pdf");
    }

    /**
     * Send customer summary email
     */
    public function sendCustomerSummary(User $customer, Request $request)
    {
        $currentUser = Auth::user();


        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) && !in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'email_subject' => 'required|string|max:255',
            'email_message' => 'required|string',
        ]);

        try {
            // Load customer data for email
            $customer->load(['supportTickets', 'paymentFollowups', 'leases']);

            // Send email (implement your email logic here)
            // Mail::to($customer->email)->send(new CustomerSummaryEmail($customer, $request->all()));

            // Log the activity
            // if (function_exists('activity')) {
            //     activity()
            //         ->performedOn($customer)
            //         ->causedBy($currentUser)
            //         ->withProperties(['email_subject' => $request->email_subject])
            //         ->log('customer_summary_sent');
            // }

            return redirect()->back()->with('success', 'Customer summary email sent successfully.');

        } catch (\Exception $e) {
            Log::error("Failed to send customer summary: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Update customer notes
     */
    public function updateCustomerNotes(User $customer, Request $request)
    {
        $currentUser = Auth::user();

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) && !in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer->update([
            'assignment_notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Customer notes updated successfully.');
    }

    /**
     * Display customer communication history
     */
    public function customerCommunication(User $customer)
    {
        $currentUser = Auth::user();

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) && !in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        // This would typically come from a communications table
        $communications = []; // Placeholder for communication history

        return view('account-manager.customers.communication', compact('customer', 'communications'));
    }

    /**
     * Get customer performance report
     */
    public function customerPerformanceReport(User $customer)
    {
        $currentUser = Auth::user();

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) && !in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $performanceData = [
            'ticket_resolution_time' => $this->calculateAverageResolutionTime($customer->id),
            'payment_punctuality' => $this->calculatePaymentPunctuality($customer->id),
            'lease_utilization' => $this->calculateLeaseUtilization($customer->id),
            'satisfaction_trend' => $this->getSatisfactionTrend($customer->id),
        ];

        return view('account-manager.customers.performance', compact('customer', 'performanceData'));
    }

    /**
     * Calculate average ticket resolution time
     */
    private function calculateAverageResolutionTime($customerId)
    {
        $resolvedTickets = CustomerSupportTicket::where('customer_id', $customerId)
            ->where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedTickets->isEmpty()) {
            return 0;
        }

        $totalHours = 0;
        foreach ($resolvedTickets as $ticket) {
            $totalHours += $ticket->created_at->diffInHours($ticket->resolved_at);
        }

        return round($totalHours / $resolvedTickets->count(), 1);
    }

    /**
     * Calculate payment punctuality percentage
     */
    private function calculatePaymentPunctuality($customerId)
    {
        $paidBillings = LeaseBilling::where('customer_id', $customerId)
            ->where('status', 'paid')
            ->get();

        if ($paidBillings->isEmpty()) {
            return 100;
        }

        $onTimePayments = 0;
        foreach ($paidBillings as $billing) {
            if ($billing->paid_at <= $billing->due_date) {
                $onTimePayments++;
            }
        }

        return round(($onTimePayments / $paidBillings->count()) * 100, 1);
    }

    /**
     * Calculate lease utilization percentage
     */
    private function calculateLeaseUtilization($customerId)
    {
        // This would depend on your lease utilization metrics
        return 85.5; // Placeholder
    }

    /**
     * Get customer satisfaction trend
     */
    private function getSatisfactionTrend($customerId)
    {
        // This would come from customer feedback system
        return [
            'current' => 88,
            'previous' => 85,
            'trend' => 'improving'
        ];
    }

    // The existing methods for customer assignment remain the same...
    // Only enhanced the existing methods with better error handling and caching

    /**
     * Display form for assigning customers to account managers (Admin only)
     */
    public function assignCustomersForm()
    {
        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized action. Admin access required.');
        }

        $accountManagers = User::accountManagers()->get(['id', 'name', 'email']);
        $unassignedCustomers = User::customers()->withoutAccountManager()->get(['id', 'name', 'email', 'company_name']);
        $assignedCustomers = User::customers()->withAccountManager()->with(['accountManager:id,name'])->get(['id', 'name', 'email', 'company_name', 'account_manager_id']);

        return view('admin.customers.assign', compact('accountManagers', 'unassignedCustomers', 'assignedCustomers'));
    }

    /**
     * Assign customers to account manager (Admin only)
     */
    public function assignCustomers(Request $request)
    {
        $request->validate([
            'account_manager_id' => 'required|exists:users,id',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:users,id,role,customer',
            'assignment_notes' => 'nullable|string|max:500',
        ], [
            'account_manager_id.required' => 'Please select an account manager.',
            'customer_ids.required' => 'Please select at least one customer.',
            'customer_ids.*.exists' => 'One or more selected customers are invalid.',
        ]);

        $accountManager = User::accountManagers()->findOrFail($request->account_manager_id);

        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized action. Admin access required.');
        }

        DB::transaction(function () use ($request, $accountManager) {
            User::whereIn('id', $request->customer_ids)
                ->where('role', 'customer')
                ->update([
                    'account_manager_id' => $request->account_manager_id,
                    'assigned_at' => now(),
                    'assignment_notes' => $request->assignment_notes,
                ]);

            // Clear relevant caches
            Cache::forget("satisfaction_score_{$request->account_manager_id}");
            Cache::forget("collection_rate_{$request->account_manager_id}");
            Cache::forget("account_manager_stats_{$request->account_manager_id}");
        });

        return redirect()->back()->with('success',
            count($request->customer_ids) . ' customers assigned to ' . $accountManager->name . ' successfully!'
        );
    }

    /**
     * Unassign customer from account manager (Admin only)
     */
    public function unassignCustomer(User $customer)
    {
        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized action. Admin access required.');
        }

        if (!$customer->isCustomer()) {
            return redirect()->back()->with('error', 'Selected user is not a customer.');
        }

        $previousManagerId = $customer->account_manager_id;

        $customer->update([
            'account_manager_id' => null,
            'assigned_at' => null,
            'assignment_notes' => null,
        ]);

        if ($previousManagerId) {
            Cache::forget("satisfaction_score_{$previousManagerId}");
            Cache::forget("collection_rate_{$previousManagerId}");
            Cache::forget("account_manager_stats_{$previousManagerId}");
        }

        return redirect()->back()->with('success', 'Customer unassigned successfully!');
    }

    /**
 * Display performance report for account managers with comprehensive analytics
 */
public function performanceReport(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (!$user->isAccountManager() && !in_array($user->role, ['admin', 'system_admin'])) {
        abort(403, 'Unauthorized access. Account manager or admin role required.');
    }

    // Get filter parameters
    $dateRange = $request->get('dateRange', 30);
    $accountManagerId = $request->get('accountManager', $user->isAccountManager() ? $user->id : 'all');
    $startDate = $request->get('startDate');
    $endDate = $request->get('endDate');

    // Calculate date range
    $dateRangeData = $this->calculateDateRange($dateRange, $startDate, $endDate);
    $startDate = $dateRangeData['start'];
    $endDate = $dateRangeData['end'];

    // Get account managers for filter dropdown
    $accountManagers = User::accountManagers()->get(['id', 'name', 'email']);

    // Calculate performance metrics
    $performanceData = $this->getPerformanceReportData($accountManagerId, $startDate, $endDate);

    // Get comparison data for previous period
    $previousPeriodData = $this->getPreviousPeriodData($accountManagerId, $startDate, $endDate);

    // Calculate growth percentages
    $growthMetrics = $this->calculateGrowthMetrics($performanceData, $previousPeriodData);

    // Charts data
    $charts = $this->getPerformanceCharts($accountManagerId, $startDate, $endDate);

    // Manager performance comparison (for admin view)
    $managerPerformance = $this->getManagerPerformanceComparison($startDate, $endDate);

    return view('account-manager.reports.performance-report', compact(
        'performanceData',
        'growthMetrics',
        'charts',
        'managerPerformance',
        'accountManagers',
        'dateRange',
        'startDate',
        'endDate',
        'accountManagerId'
    ));
}

/**
 * Calculate date range based on filter parameters
 */
private function calculateDateRange($dateRange, $startDate, $endDate)
{
    if ($dateRange === 'custom' && $startDate && $endDate) {
        return [
            'start' => Carbon::parse($startDate)->startOfDay(),
            'end' => Carbon::parse($endDate)->endOfDay()
        ];
    }

    $days = $dateRange === 'custom' ? 30 : (int)$dateRange;

    return [
        'start' => now()->subDays($days)->startOfDay(),
        'end' => now()->endOfDay()
    ];
}

/**
 * Get comprehensive performance report data
 */
/**
 * Get comprehensive performance report data
 */
private function getPerformanceReportData($accountManagerId, $startDate, $endDate)
{
    return Cache::remember("performance_report_{$accountManagerId}_{$startDate->timestamp}_{$endDate->timestamp}", 300, function() use ($accountManagerId, $startDate, $endDate) {

        $managerQuery = function($query) use ($accountManagerId) {
            if ($accountManagerId !== 'all') {
                $query->where('account_manager_id', $accountManagerId);
            }
        };

        // Revenue metrics
        $totalRevenue = LeaseBilling::whereHas('customer', $managerQuery)
            ->where('lease_billings.status', 'paid') // FIXED: Added table prefix
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Customer metrics
        $newCustomers = User::where('role', 'customer')
            ->when($accountManagerId !== 'all', function($query) use ($accountManagerId) {
                $query->where('account_manager_id', $accountManagerId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalCustomers = User::where('role', 'customer')
            ->when($accountManagerId !== 'all', function($query) use ($accountManagerId) {
                $query->where('account_manager_id', $accountManagerId);
            })
            ->count();

        // Ticket metrics
        $ticketQuery = CustomerSupportTicket::when($accountManagerId !== 'all', function($query) use ($accountManagerId) {
            $query->where('account_manager_id', $accountManagerId);
        })->whereBetween('created_at', [$startDate, $endDate]);

        $totalTickets = $ticketQuery->count();
        $resolvedTickets = $ticketQuery->where('status', 'resolved')->count();
        $conversionRate = $totalTickets > 0 ? ($resolvedTickets / $totalTickets) * 100 : 0;

        // Deal/Lease metrics
        $newLeases = Lease::whereHas('customer', $managerQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $activeLeases = Lease::whereHas('customer', $managerQuery)
            ->where('leases.status', 'active') // FIXED: Added table prefix
            ->count();

        // Payment metrics
        $paymentQuery = PaymentFollowup::when($accountManagerId !== 'all', function($query) use ($accountManagerId) {
            $query->where('account_manager_id', $accountManagerId);
        })->whereBetween('due_date', [$startDate, $endDate]);

        $totalPayments = $paymentQuery->count();
        $collectedPayments = $paymentQuery->where('status', 'paid')->count();
        $collectionRate = $totalPayments > 0 ? ($collectedPayments / $totalPayments) * 100 : 0;

        // Average deal size
        $averageDealSize = $newLeases > 0 ? ($totalRevenue / $newLeases) : 0;

        return [
            'totalRevenue' => $totalRevenue,
            'newCustomers' => $newCustomers,
            'totalCustomers' => $totalCustomers,
            'totalTickets' => $totalTickets,
            'resolvedTickets' => $resolvedTickets,
            'conversionRate' => $conversionRate,
            'newLeases' => $newLeases,
            'activeLeases' => $activeLeases,
            'totalPayments' => $totalPayments,
            'collectedPayments' => $collectedPayments,
            'collectionRate' => $collectionRate,
            'averageDealSize' => $averageDealSize,
        ];
    });
}

/**
 * Get data for previous period comparison
 */
private function getPreviousPeriodData($accountManagerId, $startDate, $endDate)
{
    $periodLength = $startDate->diffInDays($endDate);
    $previousStartDate = $startDate->copy()->subDays($periodLength);
    $previousEndDate = $startDate->copy()->subDay();

    return $this->getPerformanceReportData($accountManagerId, $previousStartDate, $previousEndDate);
}

/**
 * Calculate growth metrics compared to previous period
 */
private function calculateGrowthMetrics($currentData, $previousData)
{
    $growth = [];

    foreach ($currentData as $key => $currentValue) {
        $previousValue = $previousData[$key] ?? 0;

        if ($previousValue > 0) {
            $growth[$key] = (($currentValue - $previousValue) / $previousValue) * 100;
        } else {
            $growth[$key] = $currentValue > 0 ? 100 : 0;
        }
    }

    return $growth;
}

/**
 * Get charts data for performance report
 */
private function getPerformanceCharts($accountManagerId, $startDate, $endDate)
{
    return [
        'revenueTrend' => $this->getRevenueTrendChart($accountManagerId, $startDate, $endDate),
        'dealStatus' => $this->getDealStatusChart($accountManagerId, $startDate, $endDate),
        'servicesPerformance' => $this->getServicesPerformanceChart($accountManagerId, $startDate, $endDate),
        'ticketMetrics' => $this->getTicketMetricsChart($accountManagerId, $startDate, $endDate),
    ];
}

/**
 * Get revenue trend chart data
 */
/**
 * Get revenue trend chart data
 */
private function getRevenueTrendChart($accountManagerId, $startDate, $endDate)
{
    $months = [];
    $revenues = [];

    $currentDate = $startDate->copy();
    $monthsDifference = $startDate->diffInMonths($endDate);
    $periods = min($monthsDifference, 12); // Limit to 12 periods max

    for ($i = 0; $i <= $periods; $i++) {
        $periodStart = $currentDate->copy();
        $periodEnd = $currentDate->copy()->addMonth()->subDay();

        $managerQuery = function($query) use ($accountManagerId) {
            if ($accountManagerId !== 'all') {
                $query->where('account_manager_id', $accountManagerId);
            }
        };

        $revenue = LeaseBilling::whereHas('customer', $managerQuery)
            ->where('lease_billings.status', 'paid') // FIXED: Added table prefix
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->sum('total_amount');

        $months[] = $periodStart->format('M Y');
        $revenues[] = $revenue;

        $currentDate->addMonth();
    }

    return [
        'labels' => $months,
        'data' => $revenues
    ];
}

/**
 * Get deal status distribution chart data
 */
private function getDealStatusChart($accountManagerId, $startDate, $endDate)
{
    $managerQuery = function($query) use ($accountManagerId) {
        if ($accountManagerId !== 'all') {
            $query->where('account_manager_id', $accountManagerId);
        }
    };

    $leases = Lease::whereHas('customer', $managerQuery)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

    $statusCounts = $leases->groupBy('status')->map->count();

    return [
        'labels' => $statusCounts->keys()->map(fn($status) => ucfirst($status))->toArray(),
        'data' => $statusCounts->values()->toArray()
    ];
}

/**
 * Get services performance chart data
 */
private function getServicesPerformanceChart($accountManagerId, $startDate, $endDate)
{
    // This would typically come from your services/leases data
    // For now, using placeholder data based on lease types or services
    $managerQuery = function($query) use ($accountManagerId) {
        if ($accountManagerId !== 'all') {
            $query->where('account_manager_id', $accountManagerId);
        }
    };

    $leases = Lease::whereHas('customer', $managerQuery)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

    // Group by service type (using bandwidth as example)
    $serviceGroups = $leases->groupBy('bandwidth')->map->count();

    return [
        'labels' => $serviceGroups->keys()->map(fn($bw) => $bw . ' Mbps')->toArray(),
        'data' => $serviceGroups->values()->toArray()
    ];
}

/**
 * Get ticket metrics chart data
 */
private function getTicketMetricsChart($accountManagerId, $startDate, $endDate)
{
    $tickets = CustomerSupportTicket::when($accountManagerId !== 'all', function($query) use ($accountManagerId) {
            $query->where('account_manager_id', $accountManagerId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

    $statusCounts = $tickets->groupBy('status')->map->count();
    $priorityCounts = $tickets->groupBy('priority')->map->count();

    return [
        'status' => [
            'labels' => $statusCounts->keys()->map(fn($status) => ucfirst(str_replace('_', ' ', $status)))->toArray(),
            'data' => $statusCounts->values()->toArray()
        ],
        'priority' => [
            'labels' => $priorityCounts->keys()->map(fn($priority) => ucfirst($priority))->toArray(),
            'data' => $priorityCounts->values()->toArray()
        ]
    ];
}


/**
 * Get manager performance comparison (for admin view)
 */
private function getManagerPerformanceComparison($startDate, $endDate)
{
    $managers = User::accountManagers()->withCount([
        'managedCustomers as active_customers_count' => function($query) {
            $query->where('users.status', 'active'); // FIXED: Added table prefix
        },
        'managedLeases as active_leases_count' => function($query) {
            $query->where('leases.status', 'active'); // FIXED: Added table prefix
        }
    ])->get();

    $performanceData = [];

    foreach ($managers as $manager) {
        $managerQuery = function($query) use ($manager) {
            $query->where('account_manager_id', $manager->id);
        };

        $revenue = LeaseBilling::whereHas('customer', $managerQuery)
            ->where('lease_billings.status', 'paid') // FIXED: Added table prefix
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');

        $resolvedTickets = CustomerSupportTicket::where('account_manager_id', $manager->id)
            ->where('status', 'resolved')
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->count();

        $totalTickets = CustomerSupportTicket::where('account_manager_id', $manager->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $conversionRate = $totalTickets > 0 ? ($resolvedTickets / $totalTickets) * 100 : 0;

        $newCustomers = $manager->managedCustomers()
            ->whereBetween('users.created_at', [$startDate, $endDate]) // FIXED: Added table prefix
            ->count();

        $newLeases = $manager->managedLeases()
            ->whereBetween('leases.created_at', [$startDate, $endDate]) // FIXED: Added table prefix
            ->count();

        $avgDealSize = $manager->managedLeases()
            ->whereBetween('leases.created_at', [$startDate, $endDate]) // FIXED: Added table prefix
            ->avg('monthly_cost') ?? 0;

        $performanceData[] = [
            'id' => $manager->id,
            'name' => $manager->name,
            'email' => $manager->email,
            'total_revenue' => $revenue,
            'active_customers' => $manager->active_customers_count,
            'active_leases' => $manager->active_leases_count,
            'deals_closed' => $newLeases,
            'new_customers' => $newCustomers,
            'conversion_rate' => $conversionRate,
            'avg_deal_size' => $avgDealSize,
        ];
    }

    return $performanceData;
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
/**
 * Get customers assigned to a specific account manager (for AJAX)
 */

}
