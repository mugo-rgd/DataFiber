<?php
// app/Http/Controllers/TeamPerformanceController.php

namespace App\Http\Controllers;

use App\Services\KPIService;
use App\Models\User;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamPerformanceController extends Controller
{
    protected $kpiService;

    public function __construct(KPIService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    public function index(Request $request)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['accountmanager_admin', 'admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $period = $request->get('period', 'month');
        $currency = $request->get('currency', 'USD');

        // Get all active account managers - REMOVED the incorrect with() relationship
        $accountManagers = User::where('role', 'account_manager')
            ->where('status', 'active')
            ->get();

        // Calculate performance metrics for each manager
        $teamMetrics = [];
        foreach ($accountManagers as $manager) {
            $teamMetrics[$manager->id] = $this->calculateManagerMetrics($manager, $period, $currency);
        }

        // Sort by performance score
        uasort($teamMetrics, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });

        // Get team aggregates
        $teamAggregates = $this->calculateTeamAggregates($teamMetrics, $currency);

        // Get top performers
        $topPerformers = array_slice($teamMetrics, 0, 5, true);

        // Get revenue trend for the team
        $revenueTrend = $this->getTeamRevenueTrend($period, $currency);

        // Get comparison data
        $comparison = $this->getPeriodComparison($period, $currency);

        return view('marketing-admin.performance', compact(
            'teamMetrics',
            'teamAggregates',
            'topPerformers',
            'revenueTrend',
            'comparison',
            'period',
            'currency'
        ));
    }

    protected function calculateManagerMetrics($manager, $period, $currency)
    {
        // Get date range based on period
        $dateRange = $this->getDateRange($period);

        // Get customers assigned to this manager
        $customerIds = User::where('account_manager_id', $manager->id)
            ->where('role', 'customer')
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        // Get leases for these customers within period
        $leasesQuery = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active');

        if ($currency !== 'all') {
            $leasesQuery->where('currency', $currency);
        }

        $leases = $leasesQuery->get();

        // Calculate metrics
        $totalMRR = $leases->sum('monthly_cost');
        $totalLeases = $leases->count();
        $totalCustomers = count($customerIds);
        $totalDistance = $leases->sum('distance_km');
        $totalCores = $leases->sum('cores_required');

        // Calculate new customers added in period
        $newCustomers = User::where('account_manager_id', $manager->id)
            ->where('role', 'customer')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        // Calculate new leases in period
        $newLeases = Lease::whereIn('customer_id', $customerIds)
            ->whereBetween('start_date', [$dateRange['start'], $dateRange['end']])
            ->count();

        // Calculate average lease value
        $avgLeaseValue = $totalLeases > 0 ? $totalMRR / $totalLeases : 0;

        // Calculate performance score
        $performanceScore = $this->calculatePerformanceScore($totalMRR, $totalCustomers, $totalLeases, $newCustomers);

        // Get rating
        $rating = $this->getPerformanceRating($performanceScore);

        // Get revenue by currency breakdown
        $usdRevenue = $leases->where('currency', 'USD')->sum('monthly_cost');
        $kshRevenue = $leases->where('currency', 'KSH')->sum('monthly_cost');

        return [
            'manager_id' => $manager->id,
            'manager_name' => $manager->name,
            'manager_email' => $manager->email,
            'metrics' => [
                'total_mrr' => round($totalMRR, 2),
                'total_customers' => $totalCustomers,
                'total_leases' => $totalLeases,
                'total_distance_km' => round($totalDistance, 2),
                'total_cores' => $totalCores,
                'avg_lease_value' => round($avgLeaseValue, 2),
                'new_customers' => $newCustomers,
                'new_leases' => $newLeases,
                'usd_revenue' => round($usdRevenue, 2),
                'ksh_revenue' => round($kshRevenue, 2),
            ],
            'performance_score' => $performanceScore,
            'performance_rating' => $rating,
            'rank' => 0,
        ];
    }

    protected function calculateTeamAggregates($teamMetrics, $currency)
    {
        $totalMRR = 0;
        $totalCustomers = 0;
        $totalLeases = 0;
        $totalDistance = 0;
        $totalCores = 0;
        $totalNewCustomers = 0;
        $totalNewLeases = 0;

        foreach ($teamMetrics as $metrics) {
            $totalMRR += $metrics['metrics']['total_mrr'];
            $totalCustomers += $metrics['metrics']['total_customers'];
            $totalLeases += $metrics['metrics']['total_leases'];
            $totalDistance += $metrics['metrics']['total_distance_km'];
            $totalCores += $metrics['metrics']['total_cores'];
            $totalNewCustomers += $metrics['metrics']['new_customers'];
            $totalNewLeases += $metrics['metrics']['new_leases'];
        }

        $avgScore = count($teamMetrics) > 0
            ? collect($teamMetrics)->avg('performance_score')
            : 0;

        return [
            'total_mrr' => round($totalMRR, 2),
            'total_customers' => $totalCustomers,
            'total_leases' => $totalLeases,
            'total_distance_km' => round($totalDistance, 2),
            'total_cores' => $totalCores,
            'total_new_customers' => $totalNewCustomers,
            'total_new_leases' => $totalNewLeases,
            'avg_performance_score' => round($avgScore, 2),
            'active_managers' => count($teamMetrics),
            'currency' => $currency,
        ];
    }

    protected function getTeamRevenueTrend($period, $currency)
    {
        $dateRange = $this->getDateRange($period, true);

        $query = Lease::query()
            ->join('users', 'leases.customer_id', '=', 'users.id')
            ->where('leases.status', 'active')
            ->whereBetween('leases.start_date', [$dateRange['start'], $dateRange['end']]);

        if ($currency !== 'all') {
            $query->where('leases.currency', $currency);
        }

        $revenueData = $query->select(
                DB::raw("DATE_FORMAT(leases.start_date, '%Y-%m') as month"),
                DB::raw('SUM(leases.monthly_cost) as revenue'),
                DB::raw('COUNT(DISTINCT users.account_manager_id) as active_managers')
            )
            ->groupBy(DB::raw("DATE_FORMAT(leases.start_date, '%Y-%m')"))
            ->orderBy('month', 'asc')
            ->get();

        return $revenueData;
    }

    protected function getPeriodComparison($period, $currency)
    {
        $currentRange = $this->getDateRange($period);
        $previousRange = $this->getDateRange($period, false, true);

        $currentMetrics = $this->getPeriodMetrics($currentRange, $currency);
        $previousMetrics = $this->getPeriodMetrics($previousRange, $currency);

        $growth = [
            'mrr_growth' => $this->calculateGrowth($currentMetrics['total_mrr'], $previousMetrics['total_mrr']),
            'customer_growth' => $this->calculateGrowth($currentMetrics['total_customers'], $previousMetrics['total_customers']),
            'lease_growth' => $this->calculateGrowth($currentMetrics['total_leases'], $previousMetrics['total_leases']),
        ];

        return [
            'current' => $currentMetrics,
            'previous' => $previousMetrics,
            'growth' => $growth,
        ];
    }

    protected function getPeriodMetrics($dateRange, $currency)
    {
        $query = Lease::query()
            ->join('users', 'leases.customer_id', '=', 'users.id')
            ->where('leases.status', 'active')
            ->whereBetween('leases.start_date', [$dateRange['start'], $dateRange['end']]);

        if ($currency !== 'all') {
            $query->where('leases.currency', $currency);
        }

        $totalMRR = $query->sum('leases.monthly_cost');
        $totalLeases = $query->count();
        $totalCustomers = $query->distinct('leases.customer_id')->count('leases.customer_id');

        return [
            'total_mrr' => round($totalMRR, 2),
            'total_leases' => $totalLeases,
            'total_customers' => $totalCustomers,
        ];
    }

    protected function getDateRange($period, $forTrend = false, $isPrevious = false)
    {
        $now = Carbon::now();

        if ($isPrevious) {
            switch ($period) {
                case 'month':
                    $start = $now->copy()->subMonth()->startOfMonth();
                    $end = $now->copy()->subMonth()->endOfMonth();
                    break;
                case 'quarter':
                    $start = $now->copy()->subQuarter()->startOfQuarter();
                    $end = $now->copy()->subQuarter()->endOfQuarter();
                    break;
                case 'year':
                    $start = $now->copy()->subYear()->startOfYear();
                    $end = $now->copy()->subYear()->endOfYear();
                    break;
                default:
                    $start = $now->copy()->subMonth()->startOfMonth();
                    $end = $now->copy()->subMonth()->endOfMonth();
            }
        } elseif ($forTrend) {
            switch ($period) {
                case 'month':
                    $start = $now->copy()->subMonths(11)->startOfMonth();
                    break;
                case 'quarter':
                    $start = $now->copy()->subQuarters(3)->startOfQuarter();
                    break;
                case 'year':
                    $start = $now->copy()->subYears(2)->startOfYear();
                    break;
                default:
                    $start = $now->copy()->subMonths(11)->startOfMonth();
            }
            $end = $now->copy()->endOfMonth();
        } else {
            switch ($period) {
                case 'month':
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfMonth();
                    break;
                case 'quarter':
                    $start = $now->copy()->startOfQuarter();
                    $end = $now->copy()->endOfQuarter();
                    break;
                case 'year':
                    $start = $now->copy()->startOfYear();
                    $end = $now->copy()->endOfYear();
                    break;
                default:
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfMonth();
            }
        }

        return ['start' => $start, 'end' => $end];
    }

    protected function calculatePerformanceScore($totalMRR, $totalCustomers, $totalLeases, $newCustomers)
    {
        $score = 0;

        // MRR scoring (max 40 points)
        if ($totalMRR >= 100000) $score += 40;
        elseif ($totalMRR >= 50000) $score += 30;
        elseif ($totalMRR >= 10000) $score += 20;
        elseif ($totalMRR >= 5000) $score += 10;
        else $score += 5;

        // Customer count scoring (max 30 points)
        if ($totalCustomers >= 20) $score += 30;
        elseif ($totalCustomers >= 10) $score += 20;
        elseif ($totalCustomers >= 5) $score += 15;
        elseif ($totalCustomers >= 1) $score += 10;
        else $score += 0;

        // Lease count scoring (max 20 points)
        if ($totalLeases >= 50) $score += 20;
        elseif ($totalLeases >= 20) $score += 15;
        elseif ($totalLeases >= 10) $score += 10;
        elseif ($totalLeases >= 1) $score += 5;
        else $score += 0;

        // New customers bonus (max 10 points)
        if ($newCustomers >= 5) $score += 10;
        elseif ($newCustomers >= 3) $score += 7;
        elseif ($newCustomers >= 1) $score += 5;
        else $score += 0;

        return min($score, 100);
    }

    protected function getPerformanceRating($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 65) return 'Good';
        if ($score >= 50) return 'Average';
        if ($score >= 35) return 'Below Average';
        return 'Needs Improvement';
    }

    protected function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    public function export(Request $request)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['accountmanager_admin', 'admin', 'system_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $period = $request->get('period', 'month');
        $currency = $request->get('currency', 'USD');

        $accountManagers = User::where('role', 'account_manager')
            ->where('status', 'active')
            ->get();

        $teamMetrics = [];
        foreach ($accountManagers as $manager) {
            $teamMetrics[] = $this->calculateManagerMetrics($manager, $period, $currency);
        }

        uasort($teamMetrics, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });

        $filename = 'team_performance_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // Headers
        fputcsv($handle, [
            'Rank', 'Account Manager', 'Performance Score', 'Rating',
            'Total MRR', 'Total Customers', 'Total Leases',
            'New Customers', 'New Leases', 'Total Distance (km)',
            'Total Cores', 'Avg Lease Value', 'USD Revenue', 'KSH Revenue'
        ]);

        $rank = 1;
        foreach ($teamMetrics as $metrics) {
            fputcsv($handle, [
                $rank++,
                $metrics['manager_name'],
                $metrics['performance_score'],
                $metrics['performance_rating'],
                $metrics['metrics']['total_mrr'],
                $metrics['metrics']['total_customers'],
                $metrics['metrics']['total_leases'],
                $metrics['metrics']['new_customers'],
                $metrics['metrics']['new_leases'],
                $metrics['metrics']['total_distance_km'],
                $metrics['metrics']['total_cores'],
                $metrics['metrics']['avg_lease_value'],
                $metrics['metrics']['usd_revenue'],
                $metrics['metrics']['ksh_revenue'],
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
