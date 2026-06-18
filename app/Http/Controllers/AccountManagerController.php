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
use App\Models\ConsolidatedBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
                'active_leases' => $user->managedLeases()->where('leases.status', 'active')->count(),
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
                'value' => 88,
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

    // Pending documents - Using correct route name from your routes
    $pendingDocsCount = $this->getPendingDocumentsCount($user->id);
    if ($pendingDocsCount > 0) {
        // Use the correct route that exists
        $alerts[] = [
            'type' => 'info',
            'icon' => 'file-alt',
            'title' => 'Pending Documents',
            'message' => "There are {$pendingDocsCount} documents awaiting approval.",
            'link' => route('account-manager.customers.index') // Link to customers page where documents can be managed
        ];
    }

    return $alerts;
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

    // ==================== CUSTOMER MANAGEMENT METHODS ====================

    /**
     * Display list of customers managed by current account manager
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
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Get customers with counts
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
        ])->get();

        // Add financial data to each customer
        foreach ($customers as $customer) {
            // Get debt summary
            $debtData = DB::table('consolidated_billings')
                ->where('user_id', $customer->id)
                ->selectRaw('
                    SUM(CASE WHEN status IN ("pending", "sent", "partial", "overdue") THEN total_amount ELSE 0 END) as total_debt,
                    SUM(CASE WHEN status = "overdue" THEN total_amount ELSE 0 END) as overdue_debt,
                    SUM(CASE WHEN status IN ("pending", "sent") THEN 1 ELSE 0 END) as pending_invoices,
                    SUM(CASE WHEN status = "overdue" THEN 1 ELSE 0 END) as overdue_invoices
                ')
                ->first();

            $customer->total_debt = $debtData->total_debt ?? 0;
            $customer->overdue_debt = $debtData->overdue_debt ?? 0;
            $customer->pending_invoices = $debtData->pending_invoices ?? 0;
            $customer->overdue_invoices = $debtData->overdue_invoices ?? 0;

            // Get oldest overdue date
            $oldestOverdue = DB::table('consolidated_billings')
                ->where('user_id', $customer->id)
                ->where('status', 'overdue')
                ->where('due_date', '<', now())
                ->orderBy('due_date', 'asc')
                ->select('due_date')
                ->first();

            $customer->oldest_overdue_date = $oldestOverdue ? $oldestOverdue->due_date : null;

            // Count pending documents
            $customer->pending_documents_count = Document::where('user_id', $customer->id)
                ->where('status', 'pending')
                ->count();
        }

        // Paginate the collection
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $customers->forPage($currentPage, $perPage),
            $customers->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $customerStats = [
            'total' => $user->managedCustomers()->count(),
            'active' => $user->managedCustomers()->where('status', 'active')->count(),
            'inactive' => $user->managedCustomers()->where('status', 'inactive')->count(),
            'suspended' => $user->managedCustomers()->where('status', 'suspended')->count(),
        ];

        return view('account-manager.customers.index', compact('customers', 'customerStats'));
    }

   /**
 * Display detailed view of a specific customer - ULTRA OPTIMIZED
 */
public function customerDetail($id)
{
    $currentUser = Auth::user();

    // Single query to get customer basic info
    $customer = DB::table('users')
        ->where('id', $id)
        ->where('role', 'customer')
        ->select('id', 'name', 'email', 'phone', 'company_name', 'status', 'account_manager_id', 'created_at', 'assigned_at')
        ->first();

    if (!$customer) {
        abort(404, 'Customer not found');
    }

    // Check authorization
    $isAdmin = in_array($currentUser->role, ['admin', 'system_admin']);
    $isAssigned = $currentUser->role === 'account_manager' && $customer->account_manager_id == $currentUser->id;

    if (!$isAdmin && !$isAssigned) {
        abort(403, 'Unauthorized access');
    }

    // Get company profile
    $companyProfile = DB::table('company_profiles')->where('user_id', $id)->first();

    // Get debt summary in ONE query
    $debtSummary = DB::selectOne("
        SELECT
            COALESCE(SUM(total_amount), 0) as total_invoiced,
            COALESCE(SUM(paid_amount), 0) as total_paid,
            COALESCE(SUM(CASE WHEN status IN ('pending', 'sent', 'partial', 'overdue') THEN total_amount - COALESCE(paid_amount, 0) ELSE 0 END), 0) as outstanding,
            COALESCE(SUM(CASE WHEN status = 'overdue' THEN total_amount - COALESCE(paid_amount, 0) ELSE 0 END), 0) as overdue_amount,
            COUNT(CASE WHEN status IN ('pending', 'sent') THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'overdue' THEN 1 END) as overdue_count
        FROM consolidated_billings
        WHERE user_id = ?
    ", [$id]);

    // Get recent invoices (LIMIT 10)
    $recentInvoices = DB::select("
        SELECT id, billing_number, billing_date, due_date, total_amount, paid_amount, currency, status
        FROM consolidated_billings
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 10
    ", [$id]);

    // Get overdue invoices (LIMIT 10)
    $overdueInvoices = DB::select("
        SELECT id, billing_number, billing_date, due_date, total_amount, paid_amount, currency, status
        FROM consolidated_billings
        WHERE user_id = ? AND status = 'overdue' AND due_date < NOW()
        ORDER BY due_date ASC
        LIMIT 10
    ", [$id]);

    // Get stats in ONE query
    $stats = DB::selectOne("
        SELECT
            (SELECT COUNT(*) FROM customer_support_tickets WHERE customer_id = ?) as total_tickets,
            (SELECT COUNT(*) FROM customer_support_tickets WHERE customer_id = ? AND status IN ('open', 'in_progress')) as open_tickets,
            (SELECT COUNT(*) FROM payment_followups WHERE customer_id = ?) as total_payments,
            (SELECT COUNT(*) FROM payment_followups WHERE customer_id = ? AND status IN ('pending', 'reminded')) as pending_payments,
            (SELECT COUNT(*) FROM leases WHERE customer_id = ? AND status = 'active') as active_leases,
            (SELECT COUNT(*) FROM documents WHERE user_id = ? AND status = 'pending') as pending_documents
    ", [$id, $id, $id, $id, $id, $id]);

    return view('account-manager.customers.detail-optimized', [
        'customer' => $customer,
        'companyProfile' => $companyProfile,
        'debtSummary' => $debtSummary,
        'recentInvoices' => $recentInvoices,
        'overdueInvoices' => $overdueInvoices,
        'stats' => $stats,
    ]);
}
    /**
 * Send payment reminder to customer
 */
public function sendReminder(Request $request, $id)
{
    $customer = DB::table('users')->where('id', $id)->where('role', 'customer')->first();

    if (!$customer) {
        return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
    }

    $currentUser = Auth::user();
    $isAdmin = in_array($currentUser->role, ['admin', 'system_admin']);
    $isAssigned = $currentUser->role === 'account_manager' && $customer->account_manager_id == $currentUser->id;

    if (!$isAdmin && !$isAssigned) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // TODO: Implement actual email sending here
    // Mail::to($customer->email)->send(new PaymentReminder($customer));

    return response()->json(['success' => true, 'message' => 'Reminder sent successfully to ' . $customer->name]);
}

    /**
     * Send invoice reminder to customer
     */
    public function sendInvoiceReminder(Request $request, $id)
    {
        $customer = User::where('account_manager_id', Auth::id())
            ->where('id', $id)
            ->where('role', 'customer')
            ->firstOrFail();

        // Send invoice reminder logic here
        // Mail::to($customer->email)->send(new InvoiceReminder($customer, $request->all()));

        return response()->json([
            'success' => true,
            'message' => 'Invoice reminder sent successfully'
        ]);
    }

    /**
     * Update customer notes
     */
    public function updateCustomerNotes(User $customer, Request $request)
    {
        $currentUser = Auth::user();

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) &&
            !in_array($currentUser->role, ['admin', 'system_admin'])) {
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

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) &&
            !in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        // Get communications from audit logs or communications table
        $communications = DB::table('audit_logs')
            ->where('user_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('account-manager.customers.communication', compact('customer', 'communications'));
    }

    /**
     * Get customer performance report
     */
    public function customerPerformanceReport(User $customer)
    {
        $currentUser = Auth::user();

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) &&
            !in_array($currentUser->role, ['admin', 'system_admin'])) {
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
     * Export customer report as PDF
     */
    public function exportCustomerReport(User $customer)
    {
        $currentUser = Auth::user();

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) &&
            !in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $customer->load([
            'supportTickets',
            'paymentFollowups',
            'leases',
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

        if (($customer->account_manager_id !== $currentUser->id || !$customer->isCustomer()) &&
            !in_array($currentUser->role, ['admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'email_subject' => 'required|string|max:255',
            'email_message' => 'required|string',
        ]);

        try {
            $customer->load(['supportTickets', 'paymentFollowups', 'leases']);

            // Send email logic here
            // Mail::to($customer->email)->send(new CustomerSummaryEmail($customer, $request->all()));

            return redirect()->back()->with('success', 'Customer summary email sent successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to send customer summary: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    // ==================== PERFORMANCE & REPORTING METHODS ====================

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
        return 85.5;
    }

    /**
     * Get customer satisfaction trend
     */
    private function getSatisfactionTrend($customerId)
    {
        return [
            'current' => 88,
            'previous' => 85,
            'trend' => 'improving'
        ];
    }

    /**
     * Display performance report for account managers
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
        $previousPeriodData = $this->getPreviousPeriodData($accountManagerId, $startDate, $endDate);
        $growthMetrics = $this->calculateGrowthMetrics($performanceData, $previousPeriodData);
        $charts = $this->getPerformanceCharts($accountManagerId, $startDate, $endDate);
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
                ->where('lease_billings.status', 'paid')
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

            // Lease metrics
            $newLeases = Lease::whereHas('customer', $managerQuery)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $activeLeases = Lease::whereHas('customer', $managerQuery)
                ->where('leases.status', 'active')
                ->count();

            // Payment metrics
            $paymentQuery = PaymentFollowup::when($accountManagerId !== 'all', function($query) use ($accountManagerId) {
                $query->where('account_manager_id', $accountManagerId);
            })->whereBetween('due_date', [$startDate, $endDate]);

            $totalPayments = $paymentQuery->count();
            $collectedPayments = $paymentQuery->where('status', 'paid')->count();
            $collectionRate = $totalPayments > 0 ? ($collectedPayments / $totalPayments) * 100 : 0;
            $averageDealSize = $newLeases > 0 ? ($totalRevenue / $newLeases) : 0;

            return compact(
                'totalRevenue', 'newCustomers', 'totalCustomers', 'totalTickets',
                'resolvedTickets', 'conversionRate', 'newLeases', 'activeLeases',
                'totalPayments', 'collectedPayments', 'collectionRate', 'averageDealSize'
            );
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
            $growth[$key] = $previousValue > 0 ? (($currentValue - $previousValue) / $previousValue) * 100 : ($currentValue > 0 ? 100 : 0);
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
    private function getRevenueTrendChart($accountManagerId, $startDate, $endDate)
    {
        $months = [];
        $revenues = [];
        $currentDate = $startDate->copy();
        $periods = min($startDate->diffInMonths($endDate), 12);

        for ($i = 0; $i <= $periods; $i++) {
            $periodStart = $currentDate->copy();
            $periodEnd = $currentDate->copy()->addMonth()->subDay();

            $revenue = LeaseBilling::whereHas('customer', function($query) use ($accountManagerId) {
                if ($accountManagerId !== 'all') $query->where('account_manager_id', $accountManagerId);
            })->where('lease_billings.status', 'paid')
                ->whereBetween('paid_at', [$periodStart, $periodEnd])
                ->sum('total_amount');

            $months[] = $periodStart->format('M Y');
            $revenues[] = $revenue;
            $currentDate->addMonth();
        }

        return ['labels' => $months, 'data' => $revenues];
    }

    /**
     * Get deal status distribution chart data
     */
    private function getDealStatusChart($accountManagerId, $startDate, $endDate)
    {
        $leases = Lease::whereHas('customer', function($query) use ($accountManagerId) {
            if ($accountManagerId !== 'all') $query->where('account_manager_id', $accountManagerId);
        })->whereBetween('created_at', [$startDate, $endDate])->get();

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
        $leases = Lease::whereHas('customer', function($query) use ($accountManagerId) {
            if ($accountManagerId !== 'all') $query->where('account_manager_id', $accountManagerId);
        })->whereBetween('created_at', [$startDate, $endDate])->get();

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
        })->whereBetween('created_at', [$startDate, $endDate])->get();

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
            $query->where('users.status', 'active');
        },
        'managedLeases as active_leases_count' => function($query) {
            $query->where('leases.status', 'active');
        }
    ])->get();

    $performanceData = [];
    foreach ($managers as $manager) {
        $managerQuery = fn($query) => $query->where('account_manager_id', $manager->id);

        // Revenue metrics
        $totalRevenue = LeaseBilling::whereHas('customer', $managerQuery)
            ->where('lease_billings.status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');

        $usdRevenue = LeaseBilling::whereHas('customer', $managerQuery)
            ->where('lease_billings.status', 'paid')
            ->where('currency', 'USD')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');

        $kshRevenue = LeaseBilling::whereHas('customer', $managerQuery)
            ->where('lease_billings.status', 'paid')
            ->where('currency', 'KSH')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Support ticket metrics
        $resolvedTickets = CustomerSupportTicket::where('account_manager_id', $manager->id)
            ->where('status', 'resolved')
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->count();

        $totalTickets = CustomerSupportTicket::where('account_manager_id', $manager->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $conversionRate = $totalTickets > 0 ? ($resolvedTickets / $totalTickets) * 100 : 0;

        // Customer and lease metrics
        $newCustomers = $manager->managedCustomers()
            ->whereBetween('users.created_at', [$startDate, $endDate])
            ->count();

        $newLeases = $manager->managedLeases()
            ->whereBetween('leases.created_at', [$startDate, $endDate])
            ->count();

        $avgDealSize = $manager->managedLeases()
            ->whereBetween('leases.created_at', [$startDate, $endDate])
            ->avg('monthly_cost') ?? 0;

        // Calculate average response time (if you have that data)
        $avgResponseTime = CustomerSupportTicket::where('account_manager_id', $manager->id)
            ->whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours') ?? 0;

        $performanceData[] = [
            'id' => $manager->id,
            'name' => $manager->name,
            'email' => $manager->email,
            'total_revenue' => $totalRevenue,
            'usd_revenue' => $usdRevenue,
            'ksh_revenue' => $kshRevenue,
            'conversion_rate' => round($conversionRate, 2),
            'new_customers' => $newCustomers,
            'new_leases' => $newLeases,
            'avg_deal_size' => round($avgDealSize, 2),
            'active_customers' => $manager->active_customers_count,
            'active_leases' => $manager->active_leases_count,
            'resolved_tickets' => $resolvedTickets,
            'total_tickets' => $totalTickets,
            'avg_response_time_hours' => round($avgResponseTime, 1),
        ];
    }

    // Sort by total revenue descending
    usort($performanceData, function($a, $b) {
        return $b['total_revenue'] <=> $a['total_revenue'];
    });

    return $performanceData;
}

    // ==================== ADMIN ACCOUNT MANAGER MANAGEMENT ====================

    /**
     * Display a listing of account managers (Admin view)
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'account_manager');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $managers = $query->withCount('managedCustomers')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

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
        return view('admin.account-managers.create');
    }

    /**
     * Store a newly created account manager
     */
    public function store(Request $request)
    {
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
            return back()->withInput()->with('error', 'Failed to create account manager: ' . $e->getMessage());
        }
    }

    /**
     * Display account manager details
     */
    public function show($id)
    {
        $manager = User::where('role', 'account_manager')
            ->with(['managedCustomers' => function($query) {
                $query->select('id', 'name', 'email', 'company_name', 'status', 'account_manager_id', 'assigned_at', 'assignment_notes', 'phone', 'created_at')
                    ->where('role', 'customer')
                    ->orderBy('name');
            }])
            ->withCount(['managedCustomers' => function($query) {
                $query->where('role', 'customer');
            }])
            ->findOrFail($id);

        return view('admin.account-managers.show', compact('manager'));
    }

    /**
     * Show form to edit account manager
     */
    public function edit($id)
    {
        $manager = User::where('role', 'account_manager')->findOrFail($id);
        return view('admin.account-managers.edit', compact('manager'));
    }

    /**
     * Update account manager
     */
    public function update(Request $request, $id)
    {
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
            $data = $request->only(['name', 'email', 'phone', 'company_name', 'status']);
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $manager->update($data);

            return redirect()->route('admin.account-managers.index')
                ->with('success', 'Account manager updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update account manager: ' . $e->getMessage());
        }
    }

    /**
     * Delete account manager
     */
    public function destroy($id)
    {
        $manager = User::where('role', 'account_manager')->findOrFail($id);

        if ($manager->managedCustomers()->count() > 0) {
            return back()->with('error', 'Cannot delete manager with assigned customers. Reassign customers first.');
        }

        $manager->delete();
        return redirect()->route('admin.account-managers.index')->with('success', 'Account manager deleted successfully.');
    }

    /**
     * Display analytics for account managers
     */
    public function analytics()
    {
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

    // ==================== CUSTOMER ASSIGNMENT METHODS ====================

    /**
     * Display form for assigning customers to account managers (Admin only)
     */
    public function assignCustomersForm()
    {
        $accountManagers = User::accountManagers()->get(['id', 'name', 'email']);
        $unassignedCustomers = User::customers()->whereNull('account_manager_id')->get(['id', 'name', 'email', 'company_name']);
        $assignedCustomers = User::customers()->whereNotNull('account_manager_id')
            ->with('accountManager:id,name')
            ->get(['id', 'name', 'email', 'company_name', 'account_manager_id']);

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
        ]);

        $accountManager = User::accountManagers()->findOrFail($request->account_manager_id);

        DB::transaction(function () use ($request, $accountManager) {
            User::whereIn('id', $request->customer_ids)
                ->where('role', 'customer')
                ->update([
                    'account_manager_id' => $request->account_manager_id,
                    'assigned_at' => now(),
                    'assignment_notes' => $request->assignment_notes,
                ]);

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
     * Get customers assigned to a specific account manager (for AJAX)
     */
    public function getManagerCustomers($id)
    {
        try {
            $manager = User::where('role', 'account_manager')->findOrFail($id);

            $customers = $manager->managedCustomers()
                ->select('id', 'name', 'email', 'status', 'assigned_at', 'company_name')
                ->orderBy('name')
                ->get();

            foreach ($customers as $customer) {
                if ($customer->assigned_at) {
                    $customer->assigned_at = Carbon::parse($customer->assigned_at);
                }
            }

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
            Log::error('Error loading customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load customers: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== DESIGN REQUEST METHODS ====================

    /**
     * Update design request status
     */
    public function updateStatus(DesignRequest $designRequest, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,designed,quoted,approved,rejected'
        ]);

        try {
            $designRequest->update(['status' => $request->status]);
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
}
