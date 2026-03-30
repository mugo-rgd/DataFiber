<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ConsolidatedBilling;
use App\Models\BillingLineItem;
use App\Models\CommercialRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\FiberNetwork;
use App\Models\FiberNode;
use App\Models\FiberSegment;
use App\Models\FiberPricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class KenyaFibreDashboardController extends Controller
{
    protected $cacheDuration = 300;
    // public function index()
    // {
    //     return view('dashboard.kenya-fibre');
    // }

    public function index()
{
    $stats = [
        'total_networks' => FiberNetwork::count(),
        'total_distance' => FiberNetwork::sum('total_distance_km'),
        'total_nodes' => FiberNode::count(),
        'total_monthly_revenue' => FiberNetwork::sum('cost_per_month'),
        'active_networks' => FiberNetwork::where('status', 'Active')->count(),
        'damaged_networks' => FiberNetwork::where('status', 'Damaged')->count(),
    ];

    // Get regions for filter dropdown
    $regions = FiberNetwork::select('region')
        ->whereNotNull('region')
        ->distinct()
        ->orderBy('region')
        ->get();

    // Get region statistics for legend
    $regionStats = FiberNetwork::select('region',
            DB::raw('count(*) as count'),
            DB::raw('sum(total_distance_km) as total_distance'))
        ->whereNotNull('region')
        ->groupBy('region')
        ->get();

    // Generate network paths for the map
    $networkPaths = $this->generateNetworkPaths();

    return view('kenya-fibre.dashboard', compact(
        'stats',
        'regions',
        'regionStats',
        'networkPaths'
    ));
}

    public function getNetworkData(): JsonResponse
    {
        $networks = FiberNetwork::with('segments')->get();

        $features = [];
        foreach ($networks as $network) {
            $coordinates = [];

            foreach ($network->segments()->orderBy('segment_order')->get() as $segment) {
                if (empty($coordinates)) {
                    $coordinates[] = [$segment->source_lon, $segment->source_lat];
                }
                $coordinates[] = [$segment->dest_lon, $segment->dest_lat];
            }

            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $network->network_id,
                    'name' => $network->network_name,
                    'region' => $network->region,
                    'distance' => round($network->total_distance_km, 2),
                    'fiber_cores' => $network->fiber_cores,
                    'link_type' => $network->link_type,
                    'cost' => number_format($network->cost_per_month, 2),
                    'currency' => $network->currency,
                    'status' => $network->status,
                    'segments_count' => $network->segments->count()
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => $coordinates
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    public function getNodeData(): JsonResponse
    {
        $nodes = FiberNode::all();

        $features = [];
        foreach ($nodes as $node) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $node->node_id,
                    'name' => $node->node_name,
                    'type' => $node->node_type,
                    'region' => $node->region,
                    'address' => $node->address
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$node->longitude, $node->latitude]
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    public function getStats(): JsonResponse
    {
        $stats = [
            'total_networks' => FiberNetwork::count(),
            'total_distance' => round(FiberNetwork::sum('total_distance_km'), 2),
            'total_monthly_revenue' => number_format(FiberNetwork::sum('cost_per_month'), 2),
            'by_region' => FiberNetwork::select('region',
                    DB::raw('count(*) as count'),
                    DB::raw('sum(total_distance_km) as distance')
                )
                ->groupBy('region')
                ->get(),
            'by_link_type' => FiberNetwork::select('link_type',
                    DB::raw('count(*) as count'),
                    DB::raw('sum(total_distance_km) as distance')
                )
                ->groupBy('link_type')
                ->get()
        ];

        return response()->json($stats);
    }

    public function getNetworkDetail($id): JsonResponse
    {
        $network = FiberNetwork::with('segments')
            ->where('network_id', $id)
            ->firstOrFail();

        return response()->json($network);
    }

    public function updateNetworkStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:Active,Damaged,Planned,Decommissioned'
        ]);

        $network = FiberNetwork::where('network_id', $id)->firstOrFail();
        $network->status = $request->status;
        $network->save();

        // Update all segments status
        FiberSegment::where('network_id', $id)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Network status updated successfully'
        ]);
    }

public function getDashboardData()
     {
        try {
            // Use caching to improve performance
            $data = Cache::remember('dashboard_data', $this->cacheDuration, function () {
                return [
                    'overview_metrics' => $this->getOverviewMetrics(),
                    'customer_billing_distribution' => $this->getCustomerBillingDistribution(),
                    'revenue_by_billing_cycle' => $this->getRevenueByBillingCycle(),
                    'recent_consolidated_billings' => $this->getRecentConsolidatedBillings(),
                    'billing_status_overview' => $this->getBillingStatusOverview(),
                    'network_health' => $this->getNetworkHealth(),
                    'revenue_analytics' => $this->getRevenueAnalytics(),
                    'top_customers' => $this->getTopCustomers(),
                    'last_updated' => now()->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Dashboard data error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load dashboard data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

private function getTopCustomers()
{
    try {
        return Cache::remember('top_customers', $this->cacheDuration, function () {
            // Get customers with their consolidated billings
            $customers = User::where('role', 'customer')
                ->where('status', 'active')
                ->select('id', 'name', 'company_name', 'email')
                ->with(['consolidatedBillings' => function ($query) {
                    $query->select('id', 'user_id', 'total_amount', 'status');
                }])
                ->get()
                ->map(function ($customer) {
                    // Calculate totals from related billings
                    $validBillings = $customer->consolidatedBillings
                        ->where('status', '!=', 'cancelled');

                    $totalRevenue = $validBillings->sum('total_amount');
                    $invoiceCount = $validBillings->count();

                    return [
                        'customer' => $customer,
                        'total_revenue' => $totalRevenue,
                        'invoice_count' => $invoiceCount
                    ];
                })
                ->filter(function ($item) {
                    return $item['total_revenue'] > 0;
                })
                ->sortByDesc('total_revenue')
                ->take(10)
                ->values();

            // Calculate total revenue for percentages
            $totalRevenue = ConsolidatedBilling::where('status', '!=', 'cancelled')->sum('total_amount');

            return $customers->map(function ($item) use ($totalRevenue) {
                $customer = $item['customer'];
                $revenue = $item['total_revenue'];
                $percentage = $totalRevenue > 0 ? ($revenue / $totalRevenue) * 100 : 0;

                return [
                    'customer_name' => $customer->company_name ?: $customer->name,
                    'contact_name' => $customer->name,
                    'email' => $customer->email,
                    'total_revenue' => '$' . number_format($revenue, 2),
                    'revenue_value' => $revenue,
                    'invoice_count' => $item['invoice_count'],
                    'revenue_percentage' => round($percentage, 1)
                ];
            });
        });
    } catch (\Exception $e) {
        \Log::error('Top customers error: ' . $e->getMessage());
        return [];
    }
}
   private function getOverviewMetrics()
{
    try {
        $metrics = Cache::remember('overview_metrics', $this->cacheDuration, function () {
            $activeCustomers = User::where('role', 'customer')
                ->where('status', 'active')
                ->count();

            $totalRevenue = BillingLineItem::sum('amount');
            $totalInvoices = BillingLineItem::count();

            // Optionally filter routes (e.g., only available ones)
            $fiberQuery = CommercialRoute::query();

            // Add filters if needed:
            // $fiberQuery->where('availability', 'YES');
            // $fiberQuery->whereNotNull('region');

            $fiberMetrics = $fiberQuery->selectRaw('
                COALESCE(SUM(approx_distance_km), 0) as total_fibre_km,
                COUNT(DISTINCT region) as counties_covered
            ')->first();

            return [
                'active_customers' => $activeCustomers,
                'total_fibre_km' => (float) $fiberMetrics->total_fibre_km,
                'counties_covered' => (int) $fiberMetrics->counties_covered,
                'network_uptime' => $this->calculateNetworkUptime(), // Consider making this dynamic
                'total_revenue' => '$' . number_format($totalRevenue, 2),
                'revenue_millions' => number_format($totalRevenue / 1000000, 2),
                'total_invoices' => $totalInvoices,
            ];
        });

        return $metrics;
    } catch (\Exception $e) {
        \Log::error('Overview metrics error: ' . $e->getMessage());
        return $this->getDefaultMetrics();
    }
}

private function calculateNetworkUptime()
{
    // Your uptime calculation logic here
    return 99.7; // Or calculate from monitoring data
}

    private function getCustomerBillingDistribution()
    {
        try {
            return Cache::remember('customer_billing_distribution', $this->cacheDuration, function () {
                // Single query to get distribution
                $distribution = User::where('role', 'customer')
                    ->where('status', 'active')
                    ->selectRaw('billing_frequency, COUNT(*) as count')
                    ->groupBy('billing_frequency')
                    ->pluck('count', 'billing_frequency')
                    ->toArray();

                return [
                    'monthly' => $distribution['monthly'] ?? 0,
                    'quarterly' => $distribution['quarterly'] ?? 0,
                    'annually' => $distribution['annually'] ?? 0,
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Customer billing distribution error: ' . $e->getMessage());
            return ['monthly' => 0, 'quarterly' => 0, 'annually' => 0];
        }
    }

    private function getRevenueByBillingCycle()
    {
        try {
            return Cache::remember('revenue_by_billing_cycle', $this->cacheDuration, function () {
                // Single query to get revenue by billing cycle
                $revenueData = BillingLineItem::selectRaw('billing_cycle, SUM(amount) as revenue, COUNT(*) as count')
                    ->groupBy('billing_cycle')
                    ->get()
                    ->keyBy('billing_cycle');

                $totalRevenue = BillingLineItem::sum('amount');
                $result = [];

                foreach (['monthly', 'quarterly', 'annually'] as $cycle) {
                    if (isset($revenueData[$cycle])) {
                        $item = $revenueData[$cycle];
                        $percentage = $totalRevenue > 0 ? ($item->revenue / $totalRevenue) * 100 : 0;

                        $result[$cycle] = [
                            'revenue' => '$' . number_format($item->revenue, 2),
                            'count' => $item->count,
                            'percentage' => round($percentage, 1)
                        ];
                    } else {
                        $result[$cycle] = [
                            'revenue' => '$0.00',
                            'count' => 0,
                            'percentage' => 0
                        ];
                    }
                }

                return $result;
            });
        } catch (\Exception $e) {
            \Log::error('Revenue by billing cycle error: ' . $e->getMessage());
            return $this->getDefaultRevenueData();
        }
    }

    private function getRecentConsolidatedBillings()
    {
        try {
            return Cache::remember('recent_consolidated_billings', $this->cacheDuration, function () {
                return ConsolidatedBilling::with(['user:id,name,company_name'])
                    ->select('id', 'billing_number', 'user_id', 'total_amount', 'due_date', 'status', 'description')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($billing) {
                        return [
                            'billing_number' => $billing->billing_number,
                            'customer_name' => $billing->user->company_name ?? $billing->user->name,
                            'total_amount' => '$' . number_format((float)$billing->total_amount, 2),
                            'due_date' => $billing->due_date,
                            'status' => $billing->status,
                            'description' => $billing->description,
                        ];
                    });
            });
        } catch (\Exception $e) {
            \Log::error('Recent consolidated billings error: ' . $e->getMessage());
            return [];
        }
    }

    private function getBillingStatusOverview()
    {
        try {
            return Cache::remember('billing_status_overview', $this->cacheDuration, function () {
                $statusData = ConsolidatedBilling::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get()
                    ->keyBy('status');

                $total = ConsolidatedBilling::count();
                $result = [];

                foreach (['draft', 'pending', 'sent', 'paid', 'overdue', 'cancelled'] as $status) {
                    $count = $statusData[$status]->count ?? 0;
                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;

                    $result[$status] = [
                        'count' => $count,
                        'percentage' => round($percentage, 1)
                    ];
                }

                return $result;
            });
        } catch (\Exception $e) {
            \Log::error('Billing status overview error: ' . $e->getMessage());
            return $this->getDefaultStatusData();
        }
    }

    private function getNetworkHealth()
    {
        try {
            // This is static data - cache it for longer
            return Cache::remember('network_health', $this->cacheDuration * 2, function () {
                return [
                    'operational' => 85,
                    'degraded' => 10,
                    'maintenance' => 3,
                    'down' => 2,
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Network health error: ' . $e->getMessage());
            return ['operational' => 0, 'degraded' => 0, 'maintenance' => 0, 'down' => 0];
        }
    }

    private function getRevenueAnalytics()
    {
        try {
            return Cache::remember('revenue_analytics', $this->cacheDuration, function () {
                // Get data for last 12 months
                $startDate = Carbon::now()->subMonths(11)->startOfMonth();

                $monthlyRevenue = BillingLineItem::selectRaw("
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        SUM(amount) as revenue
                    ")
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->keyBy('month');

                // Generate array for all months
                $formattedData = [];
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $monthKey = $date->format('Y-m');
                    $monthLabel = $date->format('M Y');

                    $revenue = $monthlyRevenue[$monthKey]->revenue ?? 0;
                    $formattedData[$monthLabel] = $revenue / 1000000; // Convert to millions
                }

                return ['monthly' => $formattedData];
            });
        } catch (\Exception $e) {
            \Log::error('Revenue analytics error: ' . $e->getMessage());
            return $this->getDefaultRevenueAnalytics();
        }
    }

    // Default data methods
    private function getDefaultMetrics()
    {
        return [
            'active_customers' => 0,
            'total_fibre_km' => 0,
            'counties_covered' => 0,
            'network_uptime' => 0,
            'total_revenue' => '$0.00',
            'revenue_millions' => '0.00',
            'total_invoices' => 0,
        ];
    }

    private function getDefaultRevenueData()
    {
        return [
            'monthly' => ['revenue' => '$0.00', 'count' => 0, 'percentage' => 0],
            'quarterly' => ['revenue' => '$0.00', 'count' => 0, 'percentage' => 0],
            'annually' => ['revenue' => '$0.00', 'count' => 0, 'percentage' => 0],
        ];
    }

    private function getDefaultStatusData()
    {
        return [
            'draft' => ['count' => 0, 'percentage' => 0],
            'pending' => ['count' => 0, 'percentage' => 0],
            'sent' => ['count' => 0, 'percentage' => 0],
            'paid' => ['count' => 0, 'percentage' => 0],
            'overdue' => ['count' => 0, 'percentage' => 0],
            'cancelled' => ['count' => 0, 'percentage' => 0],
        ];
    }

    private function getDefaultRevenueAnalytics()
    {
        $formattedData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $formattedData[$monthLabel] = 0;
        }
        return ['monthly' => $formattedData];
    }


    ////////////gis data


    // Add this method to clear cache (optional)
    public function clearCache()
    {
        Cache::forget('dashboard_data');
        Cache::forget('overview_metrics');
        Cache::forget('customer_billing_distribution');
        Cache::forget('revenue_by_billing_cycle');
        Cache::forget('recent_consolidated_billings');
        Cache::forget('billing_status_overview');
        Cache::forget('network_health');
        Cache::forget('revenue_analytics');

        return response()->json(['message' => 'Dashboard cache cleared']);
    }

       public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_networks' => FiberNetwork::count(),
            'total_distance' => FiberNetwork::sum('total_distance_km') ?? 0,
            'total_nodes' => FiberNode::count(),
            'total_monthly_revenue' => FiberNetwork::sum('cost_per_month') ?? 0,
        ];

        // Get distinct regions
        $regions = FiberNetwork::select('region')
            ->whereNotNull('region')
            ->distinct()
            ->get();

        // Get region statistics
        $regionStats = FiberNetwork::select('region',
                DB::raw('count(*) as count'),
                DB::raw('sum(total_distance_km) as total_distance'))
            ->whereNotNull('region')
            ->groupBy('region')
            ->get();

        // Get recent networks (latest 50)
        $recent_networks = FiberNetwork::latest()
            ->take(50)
            ->get();

        // Get all nodes with valid coordinates
        $nodes = FiberNode::select('node_id', 'node_name', 'node_type', 'latitude', 'longitude', 'region')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Generate network paths for Google Maps
        $networkPaths = $this->generateNetworkPaths();

        return view('kenya-fibre.dashboard', compact(
            'stats',
            'regions',
            'regionStats',
            'recent_networks',
            'nodes',
            'networkPaths'
        ));
    }

   private function generateNetworkPaths()
{
    $networks = FiberNetwork::with('segments')->get();
    $paths = [];

    foreach ($networks as $network) {
        $coordinates = [];

        // First try to get waypoints from waypoints_json (already cast to array by Laravel)
        if (!empty($network->waypoints_json) && is_array($network->waypoints_json)) {
            $waypoints = $network->waypoints_json; // Already an array, don't use json_decode
            if (count($waypoints) >= 2) {
                foreach ($waypoints as $point) {
                    if (isset($point['lat']) && isset($point['lng']) &&
                        $point['lat'] !== null && $point['lng'] !== null) {
                        $coordinates[] = [
                            'lat' => (float)$point['lat'],
                            'lng' => (float)$point['lng']
                        ];
                    }
                }
            }
        }

        // If no waypoints, build path from segments
        if (empty($coordinates) && $network->segments->count() > 0) {
            $orderedSegments = $network->segments()->orderBy('segment_order')->get();

            foreach ($orderedSegments as $index => $segment) {
                // Add source point for first segment
                if ($index === 0 && $segment->source_lat && $segment->source_lon) {
                    $coordinates[] = [
                        'lat' => (float)$segment->source_lat,
                        'lng' => (float)$segment->source_lon
                    ];
                }

                // Add destination point for each segment
                if ($segment->dest_lat && $segment->dest_lon) {
                    // Check if this is the same as the last point to avoid duplicates
                    $lastPoint = end($coordinates);
                    if (!$lastPoint ||
                        $lastPoint['lat'] != (float)$segment->dest_lat ||
                        $lastPoint['lng'] != (float)$segment->dest_lon) {
                        $coordinates[] = [
                            'lat' => (float)$segment->dest_lat,
                            'lng' => (float)$segment->dest_lon
                        ];
                    }
                }
            }
        }

        // If still no coordinates, try to build from segments without ordering (fallback)
        if (empty($coordinates) && $network->segments->count() > 0) {
            foreach ($network->segments as $segment) {
                if ($segment->source_lat && $segment->source_lon) {
                    $coordinates[] = [
                        'lat' => (float)$segment->source_lat,
                        'lng' => (float)$segment->source_lon
                    ];
                }
                if ($segment->dest_lat && $segment->dest_lon) {
                    $coordinates[] = [
                        'lat' => (float)$segment->dest_lat,
                        'lng' => (float)$segment->dest_lon
                    ];
                }
            }
        }

        // Only add if we have at least 2 points
        if (count($coordinates) >= 2) {
            // Remove any duplicate consecutive points
            $uniqueCoords = [];
            $lastPoint = null;
            foreach ($coordinates as $point) {
                if ($lastPoint === null ||
                    abs($point['lat'] - $lastPoint['lat']) > 0.0001 ||
                    abs($point['lng'] - $lastPoint['lng']) > 0.0001) {
                    $uniqueCoords[] = $point;
                    $lastPoint = $point;
                }
            }

            // Log for debugging (remove in production)
            \Log::info('Network path generated', [
                'network_id' => $network->network_id,
                'name' => $network->network_name,
                'point_count' => count($uniqueCoords),
                'source' => !empty($network->waypoints_json) ? 'waypoints_json' : 'segments'
            ]);

            $paths[] = [
                'network_id' => $network->network_id,
                'name' => $network->network_name,
                'region' => $network->region,
                'distance' => (float)$network->total_distance_km,
                'fiber_cores' => (int)$network->fiber_cores,
                'link_type' => $network->link_type ?? 'Non Premium',
                'status' => $network->status,
                'cost' => (float)$network->cost_per_month,
                'currency' => $network->currency,
                'path' => $uniqueCoords,
                'point_count' => count($uniqueCoords)
            ];
        } else {
            // Log networks that couldn't be mapped
            \Log::warning('Network skipped - insufficient coordinates', [
                'network_id' => $network->network_id,
                'name' => $network->network_name,
                'has_waypoints' => !empty($network->waypoints_json),
                'segments_count' => $network->segments->count()
            ]);
        }
    }

    return $paths;
}

    }
