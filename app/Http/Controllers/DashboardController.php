<?php

namespace App\Http\Controllers;

use App\Models\BillingLineItem;
use App\Models\ConsolidatedBilling;
use App\Models\DesignRequest;
use App\Models\User;
use App\Models\SurveyRoute;
use App\Models\RouteSegment;
use App\Models\Lease;
use App\Models\Ticket;
use App\Models\Invoice;
use App\Models\LeaseBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FiberNetwork;
use App\Models\FiberNode;
use App\Models\FiberSegment;
use Illuminate\Http\JsonResponse;


class DashboardController extends Controller
{
      public function index()
{
    $user = Auth::user();

    // Initialize base data
    $data = [
        'user_role' => $user->role,
        'stats' => [],
        'charts' => [],
        'recentActivities' => []
    ];

    // Handle role-specific data
    switch ($user->role) {
        case 'admin':
            $data = array_merge($data, $this->getAdminDashboardData());
            break;
        case 'surveyor':
            $data = array_merge($data, $this->getSurveyorDashboardData());
            break;
        case 'designer':
            $data = array_merge($data, $this->getDesignerDashboardData());
            break;
        case 'finance':
            $data = array_merge($data, $this->getFinanceDashboardData());
            break;
        case 'customer':
            $data = array_merge($data, $this->getCustomerDashboardData());
            break;
        case 'accountmanager_admin':
            $data = array_merge($data, $this->getMarketingAdminDashboardData());
            break;
        default:
            $data = array_merge($data, $this->getGeneralDashboardData());
    }

    return view('dashboard', $data);
}

private function getMarketingAdminDashboardData()
{
    return [
        'stats' => $this->getMarketingAdminStats(),
        'charts' => $this->getMarketingAdminCharts(),
        'recentActivities' => $this->getMarketingAdminActivities(),
        'recentItems' => $this->getMarketingAdminRecentItems(),
        'recentItemsTitle' => 'Recent Marketing Activities',
        'recentItemsLink' => route('marketing-admin.activities'),
        'recentItemsColumns' => ['Campaign', 'Status', 'Budget', 'Created'],
        'notifications' => $this->getMarketingAdminNotifications(),
        'performanceMetrics' => $this->getMarketingAdminPerformanceMetrics()
    ];
}

private function getMarketingAdminStats()
{
    return [
        'total_account_managers' => [
            'title' => 'Account Managers',
            'value' => User::where('role', 'account_manager')->count(),
            'color' => 'primary',
            'icon' => 'user-tie'
        ],
        'total_customers' => [
            'title' => 'Managed Customers',
            'value' => User::where('role', 'customer')->count(),
            'color' => 'success',
            'icon' => 'users'
        ],
        'revenue_managed' => [
            'title' => 'Revenue Managed',
            'value' => LeaseBilling::where('status', 'paid')->sum('total_amount'),
            'color' => 'info',
            'icon' => 'dollar-sign'
        ],
        'conversion_rate' => [
            'title' => 'Conversion Rate',
            'value' => 68.5,
            'color' => 'warning',
            'icon' => 'percent'
        ],
        'active_campaigns' => [
            'title' => 'Active Campaigns',
            'value' => 12,
            'color' => 'danger',
            'icon' => 'bullhorn'
        ],
        'team_performance' => [
            'title' => 'Team Performance',
            'value' => 87,
            'color' => 'success',
            'icon' => 'trophy'
        ]
    ];
}

private function getMarketingAdminCharts()
{
    return [
        [
            'id' => 'revenueChart',
            'title' => 'Monthly Revenue Trend',
            'type' => 'line',
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'dataset' => [
                'label' => 'Revenue ($)',
                'data' => [12000, 19000, 15000, 25000, 22000, 30000],
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'borderColor' => 'rgba(54, 162, 235, 1)'
            ]
        ],
        [
            'id' => 'conversionChart',
            'title' => 'Conversion Rates by Campaign',
            'type' => 'bar',
            'labels' => ['Campaign A', 'Campaign B', 'Campaign C', 'Campaign D'],
            'dataset' => [
                'label' => 'Conversion Rate (%)',
                'data' => [65, 59, 80, 81],
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)'
            ]
        ]
    ];
}

private function getMarketingAdminActivities()
{
    return [
        [
            'icon' => 'user-plus',
            'color' => 'success',
            'text' => 'New account manager <strong>John Smith</strong> was added to the team',
            'time' => '2 hours ago'
        ],
        [
            'icon' => 'chart-line',
            'color' => 'info',
            'text' => 'Monthly revenue target <strong>exceeded by 15%</strong>',
            'time' => '5 hours ago'
        ],
        [
            'icon' => 'bullhorn',
            'color' => 'warning',
            'text' => 'New marketing campaign <strong>"Summer Promotion"</strong> launched',
            'time' => '1 day ago'
        ],
        [
            'icon' => 'trophy',
            'color' => 'primary',
            'text' => 'Team achieved <strong>95% of Q2 targets</strong>',
            'time' => '2 days ago'
        ]
    ];
}

private function getMarketingAdminRecentItems()
{
    // Return recent marketing activities or campaigns
    return collect([
        (object)[
            'campaign' => 'Summer Promotion',
            'status' => 'active',
            'budget' => 5000,
            'created_at' => now()->subDays(2)
        ],
        (object)[
            'campaign' => 'Q2 Enterprise',
            'status' => 'completed',
            'budget' => 15000,
            'created_at' => now()->subDays(10)
        ],
        (object)[
            'campaign' => 'Referral Program',
            'status' => 'planning',
            'budget' => 3000,
            'created_at' => now()->subDays(1)
        ]
    ]);
}

private function getMarketingAdminNotifications()
{
    return [
        [
            'type' => 'info',
            'icon' => 'chart-line',
            'message' => 'Revenue growth is <strong>25% higher</strong> than last month'
        ],
        [
            'type' => 'warning',
            'icon' => 'exclamation-triangle',
            'message' => '2 account managers have <strong>pending performance reviews</strong>'
        ]
    ];
}

private function getMarketingAdminPerformanceMetrics()
{
    return [
        [
            'label' => 'Team Conversion Rate',
            'value' => 68,
            'percentage' => 68,
            'target' => 75,
            'color' => 'info',
            'unit' => '%'
        ],
        [
            'label' => 'Customer Satisfaction',
            'value' => 92,
            'percentage' => 92,
            'target' => 90,
            'color' => 'success',
            'unit' => '%'
        ],
        [
            'label' => 'Revenue Growth',
            'value' => 25,
            'percentage' => 25,
            'target' => 20,
            'color' => 'warning',
            'unit' => '%'
        ]
    ];
}

    private function getAdminDashboardData()
    {
        // Fix: Use correct relationships and models
        $stats = [
            'total_users' => User::count(),
            'total_design_requests' => DesignRequest::count(),
            'pending_surveys' => DesignRequest::where('survey_status', 'assigned')->count(),
            'active_surveyors' => User::where('role', 'surveyor')->where('status', 'active')->count(),
            'completed_routes' => SurveyRoute::where('status', 'completed')->count(),
            'total_revenue' => DesignRequest::sum('quoted_amount') ?? 0,
            // 'active_leases' => Lease::where('status', 'active')->count(),
            // 'pending_tickets' => Ticket::where('status', 'open')->count(),
        ];

        return [
            'stats' => $this->enhanceStatsWithMetadata($stats, 'admin'),
            'recent_requests' => DesignRequest::with(['customer']) // Fixed: use 'customer' instead of 'user'
                ->latest()
                ->take(5)
                ->get(),
            'recent_users' => User::latest()->take(5)->get(),
            'surveyor_performance' => $this->getSurveyorPerformance(),
            'request_status_chart' => $this->getRequestStatusChart(),
            // 'recent_tickets' => Ticket::with(['user'])->latest()->take(5)->get(), // Fixed: Use Ticket model with user relationship
        ];
    }

    private function getSurveyorDashboardData()
    {
        $user = Auth::user();

        $assignedDesignRequests = DesignRequest::where('surveyor_id', $user->id)->get();

        $stats = [
            'pending_assignments' => $assignedDesignRequests->where('survey_status', 'assigned')->count(),
            'in_progress_assignments' => $assignedDesignRequests->where('survey_status', 'in_progress')->count(),
            'completed_this_week' => $assignedDesignRequests->where('survey_status', 'completed')
                ->where('updated_at', '>=', now()->startOfWeek())
                ->count(),
            'total_routes_created' => SurveyRoute::where('surveyor_id', $user->id)->count(),
            'total_segments' => RouteSegment::whereHas('surveyRoute', function($query) use ($user) {
                $query->where('surveyor_id', $user->id);
            })->count(),
            'urgent_priority' => $assignedDesignRequests->where('priority', 'urgent')
                ->whereIn('survey_status', ['assigned', 'in_progress'])
                ->count(),
        ];

        return [
            'stats' => $this->enhanceStatsWithMetadata($stats, 'surveyor'),
            'recent_assignments' => DesignRequest::where('surveyor_id', $user->id)
                ->with('customer') // Fixed: use 'customer' relationship
                ->latest()
                ->take(5)
                ->get(),
            'upcoming_deadlines' => DesignRequest::where('surveyor_id', $user->id)
                ->where('survey_scheduled_at', '>=', now())
                ->where('survey_scheduled_at', '<=', now()->addDays(7))
                ->orderBy('survey_scheduled_at')
                ->take(5)
                ->get(),
        ];
    }

    private function getDesignerDashboardData()
    {
        $user = Auth::user();

        $designerRequests = DesignRequest::where('designer_id', $user->id)->get();

        $stats = [
            'assigned_requests' => $designerRequests->count(),
            'pending_designs' => $designerRequests->where('status', 'in_progress')->count(),
            'completed_designs' => $designerRequests->where('status', 'completed')->count(),
            'quotations_pending' => $designerRequests->whereNotNull('quoted_amount')
                ->whereNull('approved_at')
                ->count(),
            'urgent_priority' => $designerRequests->where('priority', 'urgent')->count(),
        ];

        return [
            'stats' => $this->enhanceStatsWithMetadata($stats, 'designer'),
            'recent_requests' => DesignRequest::where('designer_id', $user->id)
                ->with('customer') // Fixed: use 'customer' relationship
                ->latest()
                ->take(5)
                ->get(),
            'pending_quotations' => DesignRequest::where('designer_id', $user->id)
                ->whereNotNull('quoted_amount')
                ->whereNull('approved_at')
                ->with('customer') // Fixed: use 'customer' relationship
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    private function getFinanceDashboardData()
    {
        $totalRevenue = DesignRequest::sum('quoted_amount') ?? 0;
        $totalRequests = DesignRequest::count();
        $quotedRequests = DesignRequest::whereNotNull('quoted_amount')->count();

        $stats = [
            'total_revenue' => $totalRevenue,
            'pending_invoices' => DesignRequest::whereNotNull('quoted_amount')
                ->whereNull('approved_at')
                ->count(),
            'paid_this_month' => Invoice::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0,
            'outstanding_balance' => Invoice::where('status', 'pending')->sum('amount') ?? 0,
            'average_deal_size' => $quotedRequests > 0 ? $totalRevenue / $quotedRequests : 0,
            'conversion_rate' => $totalRequests > 0 ? round(($quotedRequests / $totalRequests) * 100, 2) : 0,
        ];

        return [
            'stats' => $this->enhanceStatsWithMetadata($stats, 'finance'),
            'recent_transactions' => DesignRequest::whereNotNull('quoted_amount')
                ->with('customer') // Fixed: use 'customer' relationship
                ->latest()
                ->take(5)
                ->get(),
            'pending_invoices_list' => Invoice::with(['user'])->where('status', 'pending')->latest()->take(5)->get(),
            'revenue_chart' => $this->getRevenueChart(),
        ];
    }

    private function getCustomerDashboardData()
    {
        $user = Auth::user();

        $customerRequests = DesignRequest::where('customer_id', $user->id)->get();

        $stats = [
            'total_requests' => $customerRequests->count(),
            'requests_in_progress' => $customerRequests->where('status', 'in_progress')->count(),
            'completed_requests' => $customerRequests->where('status', 'completed')->count(),
            'pending_approval' => $customerRequests->whereNotNull('quoted_amount')
                ->whereNull('approved_at')
                ->count(),
            // 'active_leases' => Lease::where('customer_id', $user->id)->where('status', 'active')->count(),
            // 'open_tickets' => Ticket::where('user_id', $user->id)->where('status', 'open')->count(),
        ];

        return [
            'stats' => $this->enhanceStatsWithMetadata($stats, 'customer'),
            'recent_requests' => $customerRequests->load('customer')->take(5), // Fixed: load customer relationship
            // 'active_leases_list' => Lease::where('customer_id', $user->id)->where('status', 'active')->latest()->take(5)->get(),
            // 'recent_tickets' => Ticket::where('user_id', $user->id)->latest()->take(5)->get(),
        ];
    }

    private function getGeneralDashboardData()
    {
        $stats = [
            'welcome_message' => 'Welcome to Dark Fibre CRM',
        ];

        return [
            'stats' => $this->enhanceStatsWithMetadata($stats, 'general'),
        ];
    }

    /**
     * Enhance stats array with titles, colors, and icons for display
     */
    private function enhanceStatsWithMetadata(array $stats, string $role): array
    {
        $enhancedStats = [];

        foreach ($stats as $key => $value) {
            $enhancedStats[$key] = [
                'value' => $value,
                'title' => $this->getCardTitle($key, $role),
                'color' => $this->getCardColor($key, $role),
                'icon' => $this->getCardIcon($key, $role),
            ];
        }

        return $enhancedStats;
    }

    /**
     * Get card title based on stat key and role
     */
    private function getCardTitle(string $key, string $role): string
    {
        $titles = [
            // Admin titles
            'total_users' => 'Total Users',
            'total_design_requests' => 'Total Requests',
            'pending_surveys' => 'Pending Surveys',
            'active_surveyors' => 'Active Surveyors',
            'completed_routes' => 'Completed Routes',
            'total_revenue' => 'Total Revenue',
            'active_leases' => 'Active Leases',
            'pending_tickets' => 'Pending Tickets',

            // Surveyor titles
            'pending_assignments' => 'Pending Assignments',
            'in_progress_assignments' => 'In Progress',
            'completed_this_week' => 'Completed This Week',
            'total_routes_created' => 'Routes Created',
            'total_segments' => 'Total Segments',
            'urgent_priority' => 'Urgent Priority',

            // Designer titles
            'assigned_requests' => 'Assigned Requests',
            'pending_designs' => 'Pending Designs',
            'completed_designs' => 'Completed Designs',
            'quotations_pending' => 'Pending Quotations',

            // Finance titles
            'pending_invoices' => 'Pending Invoices',
            'paid_this_month' => 'Paid This Month',
            'outstanding_balance' => 'Outstanding Balance',
            'average_deal_size' => 'Average Deal Size',
            'conversion_rate' => 'Conversion Rate',

            // Customer titles
            'total_requests' => 'My Requests',
            'requests_in_progress' => 'In Progress',
            'completed_requests' => 'Completed',
            'pending_approval' => 'Pending Approval',
            // 'active_leases' => 'Active Leases',
            'open_tickets' => 'Open Tickets',

            // General
            'welcome_message' => 'Welcome',
        ];

        return $titles[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Get card color based on stat key and role
     */
    private function getCardColor(string $key, string $role): string
    {
        $colors = [
            // Success colors
            'total_revenue' => 'success',
            'completed_routes' => 'success',
            'completed_this_week' => 'success',
            'completed_designs' => 'success',
            'completed_requests' => 'success',
            'paid_this_month' => 'success',
            'conversion_rate' => 'success',
            'active_leases' => 'success',

            // Primary colors
            'total_users' => 'primary',
            'total_design_requests' => 'primary',
            'assigned_requests' => 'primary',
            'total_requests' => 'primary',
            'average_deal_size' => 'primary',
            'total_routes_created' => 'primary',

            // Warning colors
            'pending_surveys' => 'warning',
            'pending_assignments' => 'warning',
            'pending_designs' => 'warning',
            'requests_in_progress' => 'warning',
            'in_progress_assignments' => 'warning',
            'pending_approval' => 'warning',
            'quotations_pending' => 'warning',
            'pending_invoices' => 'warning',
            'pending_tickets' => 'warning',

            // Danger colors
            'urgent_priority' => 'danger',
            'outstanding_balance' => 'danger',
            'open_tickets' => 'danger',

            // Info colors
            'active_surveyors' => 'info',
            'total_segments' => 'info',

            // Default
            'welcome_message' => 'primary',
        ];

        return $colors[$key] ?? 'secondary';
    }

    /**
     * Get card icon based on stat key and role
     */
    private function getCardIcon(string $key, string $role): string
    {
        $icons = [
            'total_users' => 'users',
            'total_design_requests' => 'clipboard-list',
            'pending_surveys' => 'clock',
            'active_surveyors' => 'user-check',
            'completed_routes' => 'check-circle',
            'total_revenue' => 'dollar-sign',
            'pending_assignments' => 'inbox',
            'in_progress_assignments' => 'sync',
            'completed_this_week' => 'calendar-check',
            'total_routes_created' => 'route',
            'total_segments' => 'map-pin',
            'urgent_priority' => 'exclamation-triangle',
            'assigned_requests' => 'briefcase',
            'pending_designs' => 'pencil-ruler',
            'completed_designs' => 'check-double',
            'quotations_pending' => 'file-invoice-dollar',
            'pending_invoices' => 'receipt',
            'paid_this_month' => 'credit-card',
            'outstanding_balance' => 'money-bill-wave',
            'average_deal_size' => 'chart-line',
            'conversion_rate' => 'percent',
            'total_requests' => 'file-alt',
            'requests_in_progress' => 'spinner',
            'completed_requests' => 'check',
            'pending_approval' => 'hourglass-half',
            'active_leases' => 'network-wired',
            'pending_tickets' => 'ticket-alt',
            'open_tickets' => 'life-ring',
            'welcome_message' => 'hand-wave',
        ];

        return $icons[$key] ?? 'chart-bar';
    }

    // ... keep your existing helper methods for charts and analytics ...
    private function getSurveyorPerformance()
    {
        $surveyors = User::where('role', 'surveyor')->get();

        $performance = [];
        foreach ($surveyors as $surveyor) {
            $totalAssignments = DesignRequest::where('surveyor_id', $surveyor->id)->count();
            $completedSurveys = DesignRequest::where('surveyor_id', $surveyor->id)
                ->where('survey_status', 'completed')
                ->count();

            $performance[] = (object)[
                'name' => $surveyor->name,
                'total_assignments' => $totalAssignments,
                'completed_surveys' => $completedSurveys,
                'completion_rate' => $totalAssignments > 0
                    ? round(($completedSurveys / $totalAssignments) * 100, 2)
                    : 0,
            ];
        }

        return collect($performance);
    }

    private function getRequestStatusChart()
    {
        return [
            'pending' => DesignRequest::where('status', 'pending')->count(),
            'in_progress' => DesignRequest::where('status', 'in_progress')->count(),
            'completed' => DesignRequest::where('status', 'completed')->count(),
            'cancelled' => DesignRequest::where('status', 'cancelled')->count(),
        ];
    }

    private function getRevenueChart()
    {
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenueData[$month->format('M Y')] = DesignRequest::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('quoted_amount') ?? 0;
        }

        return $revenueData;
    }

    public function dashboard()
{
    $user = auth()->user();
    $completionPercentage = $user->profile_completion_percentage;

    return view('customer.dashboard', [
        'completionPercentage' => $completionPercentage,
        'user' => $user
    ]);
}

////////////////GIS DATA
 public function getStats(): JsonResponse
    {
        $stats = [
            'total_networks' => FiberNetwork::count(),
            'total_distance' => FiberNetwork::sum('total_distance_km'),
            'total_monthly_revenue' => FiberNetwork::sum('cost_per_month'),
            'total_nodes' => FiberNode::count(),
            'total_segments' => FiberSegment::count(),

            'by_region' => FiberNetwork::select(
                'region',
                DB::raw('count(*) as network_count'),
                DB::raw('sum(total_distance_km) as total_distance'),
                DB::raw('sum(cost_per_month) as total_revenue')
            )
            ->groupBy('region')
            ->get(),

            'by_link_type' => FiberNetwork::select(
                'link_type',
                DB::raw('count(*) as count'),
                DB::raw('sum(total_distance_km) as total_distance'),
                DB::raw('avg(cost_per_month / total_distance_km) as avg_cost_per_km')
            )
            ->groupBy('link_type')
            ->get(),

            'by_status' => FiberNetwork::select(
                'status',
                DB::raw('count(*) as count'),
                DB::raw('sum(total_distance_km) as total_distance')
            )
            ->groupBy('status')
            ->get(),

            'recent_networks' => FiberNetwork::with('segments')
                ->latest()
                ->take(5)
                ->get(),

            'top_regions_by_revenue' => FiberNetwork::select(
                'region',
                DB::raw('sum(cost_per_month) as total_revenue')
            )
            ->groupBy('region')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get(),

            'fiber_cores_distribution' => FiberNetwork::select(
                'fiber_cores',
                DB::raw('count(*) as count')
            )
            ->groupBy('fiber_cores')
            ->orderBy('fiber_cores')
            ->get()
        ];

        return response()->json($stats);
    }

    public function getNetworkHeatmap(): JsonResponse
    {
        // Get network density by region
        $heatmap = FiberNetwork::select(
            'region',
            DB::raw('count(*) as density'),
            DB::raw('sum(total_distance_km) as total_length')
        )
        ->groupBy('region')
        ->get();

        return response()->json($heatmap);
    }

    public function getCostAnalysis(): JsonResponse
    {
        $analysis = [
            'by_link_type' => FiberNetwork::select(
                'link_type',
                DB::raw('avg(cost_per_month / total_distance_km) as avg_cost_per_km'),
                DB::raw('min(cost_per_month / total_distance_km) as min_cost_per_km'),
                DB::raw('max(cost_per_month / total_distance_km) as max_cost_per_km')
            )
            ->groupBy('link_type')
            ->get(),

            'total_investment' => FiberNetwork::sum('cost_per_month') * 12, // Annual
            'average_roi' => $this->calculateAverageROI(),
            'cost_by_region' => FiberNetwork::select(
                'region',
                DB::raw('sum(cost_per_month) as monthly_cost'),
                DB::raw('avg(cost_per_month / total_distance_km) as avg_cost_per_km')
            )
            ->groupBy('region')
            ->get()
        ];

        return response()->json($analysis);
    }

    private function calculateAverageROI(): float
    {
        // Simplified ROI calculation
        $totalRevenue = FiberNetwork::sum('cost_per_month') * 12;
        $estimatedBuildCost = FiberNetwork::sum('total_distance_km') * 1000000; // Assume 1M KES per km build cost

        if ($estimatedBuildCost == 0) {
            return 0;
        }

        return ($totalRevenue / $estimatedBuildCost) * 100;
    }

}
