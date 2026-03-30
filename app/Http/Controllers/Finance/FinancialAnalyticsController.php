<?php
// app/Http\Controllers\Finance\FinancialAnalyticsController.php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FinancialAnalyticsController extends Controller
{
    /**
     * Display trend analysis page
     */
    public function trends(Request $request)
    {
        try {
            $period = $request->input('period', '30d');

            // Get initial data for the default view
            $startDate = match($period) {
                '7d' => Carbon::now()->subDays(7),
                '90d' => Carbon::now()->subDays(90),
                '1y' => Carbon::now()->subYear(),
                'qtd' => Carbon::now()->startOfQuarter(),
                'ytd' => Carbon::now()->startOfYear(),
                default => Carbon::now()->subDays(30)
            };

            $endDate = Carbon::now();

            $initialData = $this->getActualTrendData($startDate, $endDate, 'revenue', 'daily');
            $initialMetrics = $this->calculateTrendMetrics($initialData);
            $initialTrends = $this->formatTrendsForTable($initialData, 'revenue', 'daily');
            $initialChartData = $this->formatChartData($initialData, 'revenue');

            return view('finance.financial-analytics.trends', [
                'initialData' => [
                    'metrics' => $initialMetrics,
                    'trends' => $initialTrends,
                    'chartData' => $initialChartData,
                    'dateRange' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d')
                    ]
                ],
                'period' => $period
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load trend analysis page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Unable to load trend analysis: ' . $e->getMessage());
        }
    }

    /**
     * Display financial analytics dashboard
     */
    public function dashboard()
    {
        try {
            $report = $this->generateFinancialReport('30d', true);
            $quickMetrics = $this->getQuickMetrics();
            $trendData = $this->getDashboardTrends();

            return view('finance.financial-analytics.dashboard', compact(
                'report', 'quickMetrics', 'trendData'
            ));

        } catch (\Exception $e) {
            Log::error('Failed to load analytics dashboard', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Unable to load analytics dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Generate detailed financial report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'period' => 'in:7d,30d,90d,1y,qtd,ytd',
            'format' => 'in:html,json',
            'include_predictions' => 'boolean',
        ]);

        $period = $request->input('period', '30d');
        $format = $request->input('format', 'html');
        $includePredictions = $request->boolean('include_predictions', true);

        try {
            $report = $this->generateFinancialReport($period, $includePredictions);

            if ($format === 'json') {
                return response()->json($report);
            }

            return view('finance.financial-analytics.report', compact('report', 'period'));

        } catch (\Exception $e) {
            Log::error('Failed to generate financial report', [
                'period' => $period,
                'format' => $format,
                'error' => $e->getMessage()
            ]);

            if ($format === 'json') {
                return response()->json([
                    'error' => 'Report generation failed',
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Trend analysis API endpoint
     */
    public function getTrendData(Request $request)
    {
        try {
            $period = $request->input('period', 30); // days
            $metric = $request->input('metric', 'revenue');
            $granularity = $request->input('granularity', 'daily');
            $comparison = $request->input('comparison', 'none');

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
            } else {
                $end = Carbon::now();
                $start = Carbon::now()->subDays($period);
            }

            // Get actual data from consolidated_billings
            $trendData = $this->getActualTrendData($start, $end, $metric, $granularity);

            // Get metrics summary
            $metrics = $this->calculateTrendMetrics($trendData);

            // Get trends for table
            $trends = $this->formatTrendsForTable($trendData, $metric, $granularity);

            // Get chart data
            $chartData = $this->formatChartData($trendData, $metric);

            // If comparison requested, get comparison data
            $comparisonData = null;
            if ($comparison === 'previous_period') {
                $comparisonStart = $start->copy()->subDays($end->diffInDays($start));
                $comparisonEnd = $start->copy()->subDay();
                $comparisonData = $this->getActualTrendData($comparisonStart, $comparisonEnd, $metric, $granularity);
            }

            return response()->json([
                'metrics' => $metrics,
                'trends' => $trends,
                'chartData' => $chartData,
                'comparisonData' => $comparisonData,
                'dateRange' => [
                    'start' => $start->format('Y-m-d'),
                    'end' => $end->format('Y-m-d')
                ],
                'meta' => [
                    'metric' => $metric,
                    'granularity' => $granularity,
                    'comparison' => $comparison
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load trend data', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Unable to load trend data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get actual trend data from database
     */
    private function getActualTrendData($startDate, $endDate, $metric = 'revenue', $granularity = 'daily')
    {
        $query = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate]);

        switch ($granularity) {
            case 'daily':
                $query->select(
                    DB::raw("DATE(billing_date) as period"),
                    DB::raw("SUM(total_amount) as total_amount"),
                    DB::raw("SUM(paid_amount) as paid_amount"),
                    DB::raw("COUNT(*) as invoice_count"),
                    DB::raw("COUNT(DISTINCT user_id) as customer_count")
                )->groupBy(DB::raw("DATE(billing_date)"));
                break;

            case 'weekly':
                $query->select(
                    DB::raw("CONCAT(YEAR(billing_date), '-W', LPAD(WEEK(billing_date), 2, '0')) as period"),
                    DB::raw("SUM(total_amount) as total_amount"),
                    DB::raw("SUM(paid_amount) as paid_amount"),
                    DB::raw("COUNT(*) as invoice_count"),
                    DB::raw("COUNT(DISTINCT user_id) as customer_count")
                )->groupBy(DB::raw("YEAR(billing_date), WEEK(billing_date)"));
                break;

            case 'monthly':
                $query->select(
                    DB::raw("DATE_FORMAT(billing_date, '%Y-%m') as period"),
                    DB::raw("SUM(total_amount) as total_amount"),
                    DB::raw("SUM(paid_amount) as paid_amount"),
                    DB::raw("COUNT(*) as invoice_count"),
                    DB::raw("COUNT(DISTINCT user_id) as customer_count")
                )->groupBy(DB::raw("YEAR(billing_date), MONTH(billing_date)"));
                break;

            case 'quarterly':
                $query->select(
                    DB::raw("CONCAT(YEAR(billing_date), '-Q', QUARTER(billing_date)) as period"),
                    DB::raw("SUM(total_amount) as total_amount"),
                    DB::raw("SUM(paid_amount) as paid_amount"),
                    DB::raw("COUNT(*) as invoice_count"),
                    DB::raw("COUNT(DISTINCT user_id) as customer_count")
                )->groupBy(DB::raw("YEAR(billing_date), QUARTER(billing_date)"));
                break;
        }

        $query->orderBy('period');

        $results = $query->get();

        // Format the results based on metric type
        $formattedData = [];
        foreach ($results as $row) {
            switch ($metric) {
                case 'revenue':
                    $value = floatval($row->total_amount);
                    break;
                case 'profit':
                    // Calculate estimated profit (assuming 15% margin)
                    $collected = floatval($row->paid_amount);
                    $value = $collected * 0.15;
                    break;
                case 'expenses':
                    // Calculate estimated expenses (assuming 85% of collected revenue is expenses)
                    $collected = floatval($row->paid_amount);
                    $value = $collected * 0.85;
                    break;
                case 'margin':
                    $collected = floatval($row->paid_amount);
                    $total = floatval($row->total_amount);
                    $value = $total > 0 ? ($collected / $total) * 100 : 0;
                    break;
                case 'growth':
                    // This will be calculated later
                    $value = floatval($row->total_amount);
                    break;
                default:
                    $value = floatval($row->total_amount);
            }

            $formattedData[] = [
                'period' => $row->period,
                'value' => $value,
                'total_amount' => floatval($row->total_amount),
                'paid_amount' => floatval($row->paid_amount),
                'invoice_count' => intval($row->invoice_count),
                'customer_count' => intval($row->customer_count),
                'unpaid_amount' => floatval($row->total_amount - $row->paid_amount)
            ];
        }

        return $formattedData;
    }

    /**
     * Calculate trend metrics
     */
    private function calculateTrendMetrics($trendData)
    {
        if (empty($trendData)) {
            return [
                'total_revenue' => 0,
                'revenue_growth' => 0,
                'net_profit' => 0,
                'profit_growth' => 0,
                'gross_margin' => 0,
                'margin_growth' => 0,
                'operating_expenses' => 0,
                'expense_growth' => 0,
                'collection_rate' => 0,
                'avg_invoice_value' => 0
            ];
        }

        $totalAmount = array_sum(array_column($trendData, 'total_amount'));
        $paidAmount = array_sum(array_column($trendData, 'paid_amount'));
        $unpaidAmount = array_sum(array_column($trendData, 'unpaid_amount'));
        $invoiceCount = array_sum(array_column($trendData, 'invoice_count'));

        // Calculate collection rate
        $collectionRate = $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0;

        // Calculate average invoice value
        $avgInvoiceValue = $invoiceCount > 0 ? ($totalAmount / $invoiceCount) : 0;

        // Calculate growth rates (comparing first and last periods)
        $firstPeriod = reset($trendData);
        $lastPeriod = end($trendData);

        $revenueGrowth = $firstPeriod['total_amount'] > 0
            ? (($lastPeriod['total_amount'] - $firstPeriod['total_amount']) / $firstPeriod['total_amount']) * 100
            : 0;

        $profitGrowth = $firstPeriod['paid_amount'] > 0
            ? (($lastPeriod['paid_amount'] - $firstPeriod['paid_amount']) / $firstPeriod['paid_amount']) * 100
            : 0;

        // Calculate profit (estimated at 15% of collected revenue)
        $netProfit = $paidAmount * 0.15;

        // Calculate expenses (estimated at 85% of collected revenue)
        $operatingExpenses = $paidAmount * 0.85;

        // Calculate margin (profit as percentage of total revenue)
        $grossMargin = $totalAmount > 0 ? ($netProfit / $totalAmount) * 100 : 0;

        return [
            'total_revenue' => round($totalAmount, 2),
            'revenue_growth' => round($revenueGrowth, 1),
            'net_profit' => round($netProfit, 2),
            'profit_growth' => round($profitGrowth, 1),
            'gross_margin' => round($grossMargin, 1),
            'margin_growth' => round($profitGrowth, 1), // Using profit growth as proxy
            'operating_expenses' => round($operatingExpenses, 2),
            'expense_growth' => round($profitGrowth, 1), // Using profit growth as proxy
            'collection_rate' => round($collectionRate, 1),
            'avg_invoice_value' => round($avgInvoiceValue, 2),
            'total_invoices' => $invoiceCount,
            'outstanding_amount' => round($unpaidAmount, 2)
        ];
    }

    /**
     * Format trends for table display
     */
    private function formatTrendsForTable($trendData, $metric, $granularity)
    {
        $trends = [];

        foreach ($trendData as $index => $data) {
            // Calculate growth from previous period
            $prevValue = $index > 0 ? $trendData[$index - 1]['value'] : $data['value'];
            $growth = $prevValue > 0 ? (($data['value'] - $prevValue) / $prevValue) * 100 : 0;

            // Calculate 7-day moving average (if granularity is daily)
            $ma7 = 0;
            if ($granularity === 'daily' && $index >= 6) {
                $sum = 0;
                for ($i = 0; $i < 7; $i++) {
                    $sum += $trendData[$index - $i]['value'];
                }
                $ma7 = $sum / 7;
            }

            // Calculate 30-day moving average
            $ma30 = 0;
            if ($granularity === 'daily' && $index >= 29) {
                $sum = 0;
                for ($i = 0; $i < 30; $i++) {
                    $sum += $trendData[$index - $i]['value'];
                }
                $ma30 = $sum / 30;
            }

            // Determine trend direction
            if ($growth > 2) {
                $trend = 'up';
            } elseif ($growth < -2) {
                $trend = 'down';
            } else {
                $trend = 'stable';
            }

            // Format period label based on granularity
            $periodLabel = $data['period'];
            if ($granularity === 'weekly') {
                // Convert "2025-W49" to "Week 49, 2025"
                $periodLabel = str_replace(['-W', '-'], [' Week ', ', '], $periodLabel);
            } elseif ($granularity === 'monthly') {
                // Convert "2025-12" to "Dec 2025"
                $date = Carbon::createFromFormat('Y-m', $periodLabel);
                $periodLabel = $date->format('M Y');
            } elseif ($granularity === 'quarterly') {
                // Convert "2025-Q4" to "Q4 2025"
                $periodLabel = str_replace('-', ' ', $periodLabel);
            }

            $trends[] = [
                'period' => $periodLabel,
                'revenue' => round($data['total_amount'], 2),
                'profit' => round($data['paid_amount'] * 0.15, 2),
                'margin' => round(($data['paid_amount'] / max($data['total_amount'], 1)) * 100, 1),
                'expenses' => round($data['paid_amount'] * 0.85, 2),
                'revenue_growth' => round($growth, 1),
                'profit_growth' => round($growth, 1),
                'ma7' => round($ma7, 2),
                'ma30' => round($ma30, 2),
                'trend' => $trend,
                'invoice_count' => $data['invoice_count'],
                'customer_count' => $data['customer_count']
            ];
        }

        return $trends;
    }

    /**
     * Format data for charts
     */
    private function formatChartData($trendData, $metric)
    {
        $labels = [];
        $values = [];
        $paidValues = [];
        $unpaidValues = [];

        foreach ($trendData as $data) {
            $labels[] = $data['period'];
            $values[] = $data['value'];
            $paidValues[] = $data['paid_amount'];
            $unpaidValues[] = $data['unpaid_amount'];
        }

        // Calculate distribution for pie chart
        $totalRevenue = array_sum($values);
        $totalPaid = array_sum($paidValues);
        $totalUnpaid = array_sum($unpaidValues);
        $totalProfit = $totalPaid * 0.15;
        $totalExpenses = $totalPaid * 0.85;

        return [
            'revenue' => [
                'labels' => $labels,
                'values' => $values,
                'paid' => $paidValues,
                'unpaid' => $unpaidValues
            ],
            'distribution' => [
                'labels' => ['Collected Revenue', 'Outstanding', 'Profit', 'Expenses'],
                'values' => [
                    round($totalPaid, 2),
                    round($totalUnpaid, 2),
                    round($totalProfit, 2),
                    round($totalExpenses, 2)
                ]
            ]
        ];
    }

    /**
     * Quick metrics for dashboard
     */
    private function getQuickMetrics()
    {
        $today = Carbon::today();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();

        return [
            'today' => [
                'collections' => DB::table('consolidated_billings')
                    ->whereDate('payment_date', $today)
                    ->where('status', 'paid')
                    ->sum('paid_amount') ?? 0,
                'invoices' => DB::table('consolidated_billings')
                    ->whereDate('billing_date', $today)
                    ->count() ?? 0,
                'overdue' => DB::table('consolidated_billings')
                    ->where('due_date', '<=', $today)
                    ->whereRaw('total_amount > paid_amount')
                    ->whereIn('status', ['pending', 'sent', 'overdue'])
                    ->count() ?? 0
            ],
            'this_week' => [
                'collections' => DB::table('consolidated_billings')
                    ->whereBetween('payment_date', [$thisWeekStart, Carbon::now()])
                    ->where('status', 'paid')
                    ->sum('paid_amount') ?? 0,
                'growth' => $this->calculateWeeklyGrowth()
            ],
            'this_month' => [
                'revenue' => DB::table('consolidated_billings')
                    ->whereBetween('billing_date', [$thisMonthStart, Carbon::now()])
                    ->sum('total_amount') ?? 0,
                'collections' => DB::table('consolidated_billings')
                    ->whereBetween('payment_date', [$thisMonthStart, Carbon::now()])
                    ->where('status', 'paid')
                    ->sum('paid_amount') ?? 0,
                'outstanding' => DB::table('consolidated_billings')
                    ->whereBetween('billing_date', [$thisMonthStart, Carbon::now()])
                    ->whereRaw('total_amount > paid_amount')
                    ->sum(DB::raw('total_amount - paid_amount')) ?? 0
            ],
            'pending_total' => DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent', 'overdue'])
                ->whereRaw('total_amount > paid_amount')
                ->sum(DB::raw('total_amount - paid_amount')) ?? 0
        ];
    }

    /**
     * Calculate weekly growth
     */
    private function calculateWeeklyGrowth(): float
    {
        $currentWeek = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()
            ])
            ->where('status', 'paid')
            ->sum('paid_amount') ?? 0;

        $lastWeek = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ])
            ->where('status', 'paid')
            ->sum('paid_amount') ?? 0;

        return $lastWeek > 0 ? (($currentWeek - $lastWeek) / $lastWeek) * 100 : 0.0;
    }

    /**
     * Dashboard trends
     */
    private function getDashboardTrends()
    {
        $trends = [];

        // Last 7 days daily collections
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $collections = DB::table('consolidated_billings')
                ->whereDate('payment_date', $date)
                ->where('status', 'paid')
                ->sum('paid_amount') ?? 0;

            $trends['daily_collections'][] = [
                'date' => $date->format('D'),
                'amount' => floatval($collections)
            ];
        }

        // Monthly trends for 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = DB::table('consolidated_billings')
                ->whereMonth('billing_date', $month->month)
                ->whereYear('billing_date', $month->year)
                ->sum('total_amount') ?? 0;

            $collections = DB::table('consolidated_billings')
                ->whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->where('status', 'paid')
                ->sum('paid_amount') ?? 0;

            $trends['monthly_revenue'][] = [
                'month' => $month->format('M'),
                'revenue' => floatval($revenue),
                'collections' => floatval($collections)
            ];
        }

        return $trends;
    }

    /**
     * Generate complete financial report
     */
    private function generateFinancialReport($period = '30d', $includePredictions = true)
    {
        $startDate = match($period) {
            '7d' => Carbon::now()->subDays(7),
            '90d' => Carbon::now()->subDays(90),
            '1y' => Carbon::now()->subYear(),
            'qtd' => Carbon::now()->startOfQuarter(),
            'ytd' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subDays(30)
        };

        $endDate = Carbon::now();

        // Get revenue data
        $revenueData = $this->getRevenueKpis($startDate, $endDate);
        $profitabilityData = $this->getProfitabilityKpis($startDate, $endDate);
        $liquidityData = $this->getLiquidityKpis($startDate, $endDate);
        $efficiencyData = $this->getEfficiencyKpis($startDate, $endDate);

        // Get trend data
        $trendData = $this->getActualTrendData($startDate, $endDate, 'revenue', 'daily');

        // Prepare report
        $report = [
            'period' => $period,
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'executive_summary' => $this->generateExecutiveSummary($revenueData, $profitabilityData),
            'financial_metrics' => [
                'revenue_metrics' => $revenueData,
                'profitability_metrics' => $profitabilityData,
                'liquidity_metrics' => $liquidityData,
                'efficiency_metrics' => $efficiencyData
            ],
            'trend_analysis' => [
                'revenue_trend' => $this->calculateTrendMetrics($trendData),
                'data_points' => $trendData
            ],
            'key_insights' => $this->generateKeyInsights($revenueData, $profitabilityData, $liquidityData, $efficiencyData)
        ];

        if ($includePredictions) {
            $report['predictions'] = $this->generatePredictions($trendData);
        }

        return $report;
    }

    /**
     * Revenue KPIs
     */
    private function getRevenueKpis($startDate, $endDate)
    {
        $result = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->select([
                DB::raw('COALESCE(SUM(total_amount), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(paid_amount), 0) as collected_revenue'),
                DB::raw('COUNT(DISTINCT user_id) as active_customers'),
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('AVG(total_amount) as avg_invoice_value')
            ])
            ->first();

        $previousPeriod = $this->getPreviousPeriod($startDate, $endDate);
        $previousRevenue = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$previousPeriod['start'], $previousPeriod['end']])
            ->sum('total_amount') ?? 0;

        $growth = $previousRevenue > 0
            ? (($result->total_revenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        return [
            'total_revenue' => floatval($result->total_revenue ?? 0),
            'collected_revenue' => floatval($result->collected_revenue ?? 0),
            'collection_rate' => ($result->total_revenue ?? 0) > 0
                ? ($result->collected_revenue / $result->total_revenue) * 100
                : 0,
            'active_customers' => intval($result->active_customers ?? 0),
            'total_invoices' => intval($result->total_invoices ?? 0),
            'avg_invoice_value' => floatval($result->avg_invoice_value ?? 0),
            'revenue_growth' => floatval($growth),
            'outstanding_revenue' => floatval(($result->total_revenue ?? 0) - ($result->collected_revenue ?? 0))
        ];
    }

    /**
     * Profitability KPIs
     */
    private function getProfitabilityKpis($startDate, $endDate)
    {
        $revenue = $this->getRevenueKpis($startDate, $endDate);

        // Estimate costs based on revenue
        $costPercentage = 0.35;
        $operatingCostPercentage = 0.25;

        $costOfGoods = $revenue['collected_revenue'] * $costPercentage;
        $operatingCosts = $revenue['collected_revenue'] * $operatingCostPercentage;
        $totalCosts = $costOfGoods + $operatingCosts;

        $grossProfit = $revenue['collected_revenue'] - $costOfGoods;
        $netProfit = $grossProfit - $operatingCosts;

        return [
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'gross_margin' => $revenue['collected_revenue'] > 0
                ? ($grossProfit / $revenue['collected_revenue']) * 100
                : 0,
            'net_margin' => $revenue['collected_revenue'] > 0
                ? ($netProfit / $revenue['collected_revenue']) * 100
                : 0,
            'cost_of_goods' => $costOfGoods,
            'operating_costs' => $operatingCosts,
            'total_costs' => $totalCosts,
            'profit_per_customer' => $revenue['active_customers'] > 0
                ? $netProfit / $revenue['active_customers']
                : 0
        ];
    }

    /**
     * Liquidity KPIs
     */
    private function getLiquidityKpis($startDate, $endDate)
    {
        $collected = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('paid_amount') ?? 0;

        $outstanding = DB::table('consolidated_billings')
            ->whereRaw('total_amount > paid_amount')
            ->whereIn('status', ['pending', 'sent', 'overdue'])
            ->sum(DB::raw('total_amount - paid_amount')) ?? 0;

        $estimatedLiabilities = $outstanding * 0.5;
        $currentAssets = $collected * 0.8;
        $quickAssets = $collected * 0.6;

        return [
            'current_ratio' => $estimatedLiabilities > 0
                ? $currentAssets / $estimatedLiabilities
                : 0,
            'quick_ratio' => $estimatedLiabilities > 0
                ? $quickAssets / $estimatedLiabilities
                : 0,
            'working_capital' => $currentAssets - $estimatedLiabilities,
            'cash_position' => $collected * 0.5,
            'outstanding_debt' => $outstanding,
            'days_sales_outstanding' => $this->calculateDSOSimple($startDate, $endDate)
        ];
    }

    /**
     * Efficiency KPIs
     */
    private function getEfficiencyKpis($startDate, $endDate)
    {
        $revenue = $this->getRevenueKpis($startDate, $endDate);

        $avgCollectionDays = DB::table('consolidated_billings')
            ->where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereNotNull('payment_date')
            ->whereNotNull('billing_date')
            ->avg(DB::raw('DATEDIFF(payment_date, billing_date)')) ?? 0;

        return [
            'collection_efficiency' => $revenue['collection_rate'],
            'avg_collection_days' => round($avgCollectionDays, 1),
            'invoice_processing_time' => $this->calculateInvoiceProcessingTime($startDate, $endDate),
            'customer_acquisition_cost' => 150,
            'customer_lifetime_value' => $this->calculateCLVSimple($startDate, $endDate),
            'employee_productivity' => $revenue['total_revenue'] / 5
        ];
    }

    /**
     * Helper methods
     */
    private function getPreviousPeriod($startDate, $endDate): array
    {
        $duration = $endDate->diffInDays($startDate);
        return [
            'start' => $startDate->copy()->subDays($duration),
            'end' => $startDate->copy()->subDay()
        ];
    }

    private function calculateDSOSimple($startDate, $endDate): float
    {
        $avgReceivablesResult = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->select(DB::raw('AVG(GREATEST(total_amount - paid_amount, 0)) as avg_receivables'))
            ->first();

        $avgReceivables = $avgReceivablesResult->avg_receivables ?? 0;

        $totalSalesResult = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->select(DB::raw('SUM(total_amount) as total_sales'))
            ->first();

        $totalSales = $totalSalesResult->total_sales ?? 0;

        $days = max(1, $endDate->diffInDays($startDate));

        return $totalSales > 0 ? ($avgReceivables / ($totalSales / $days)) : 0.0;
    }

    private function calculateInvoiceProcessingTime($startDate, $endDate): float
    {
        $result = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->whereNotNull('payment_date')
            ->where('status', 'paid')
            ->select(DB::raw('AVG(DATEDIFF(payment_date, billing_date)) as avg_days'))
            ->first();

        return round($result->avg_days ?? 0, 1);
    }

    private function calculateCLVSimple($startDate, $endDate): float
    {
        $result = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->select([
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT user_id) as customer_count')
            ])
            ->first();

        $totalRevenue = $result->total_revenue ?? 0;
        $customerCount = max($result->customer_count ?? 1, 1);

        $avgRevenuePerCustomer = $totalRevenue / $customerCount;
        $customerLifespan = 36;

        return floatval($avgRevenuePerCustomer * $customerLifespan);
    }

    private function generateExecutiveSummary($revenueData, $profitabilityData)
    {
        $collectionRate = $revenueData['collection_rate'] ?? 0;
        $revenueGrowth = $revenueData['revenue_growth'] ?? 0;
        $netMargin = $profitabilityData['net_margin'] ?? 0;

        $performance = 'steady';
        if ($revenueGrowth > 10 && $collectionRate > 90) {
            $performance = 'excellent';
        } elseif ($revenueGrowth > 5 && $collectionRate > 80) {
            $performance = 'good';
        } elseif ($revenueGrowth < 0 || $collectionRate < 70) {
            $performance = 'needs_attention';
        }

        return [
            'overview' => "Financial performance is {$performance}. Collection rate at {$collectionRate}%, revenue growth at {$revenueGrowth}%, and net margin at {$netMargin}%.",
            'performance' => $performance,
            'highlights' => [
                "Total revenue: $" . number_format($revenueData['total_revenue'] ?? 0, 2),
                "Collection rate: " . round($collectionRate, 1) . "%",
                "Revenue growth: " . round($revenueGrowth, 1) . "%",
                "Net margin: " . round($netMargin, 1) . "%"
            ]
        ];
    }

    private function generateKeyInsights($revenueData, $profitabilityData, $liquidityData, $efficiencyData)
    {
        $insights = [];

        if ($revenueData['collection_rate'] < 80) {
            $insights[] = "Collection rate below target (80%). Consider implementing stricter payment terms or follow-up procedures.";
        }

        if ($revenueData['revenue_growth'] < 5) {
            $insights[] = "Revenue growth is below 5%. Evaluate customer acquisition strategies.";
        }

        if ($profitabilityData['net_margin'] < 15) {
            $insights[] = "Net margin below industry average. Review cost structure and pricing strategy.";
        }

        if ($liquidityData['days_sales_outstanding'] > 45) {
            $insights[] = "Collections are taking too long (DSO: " . round($liquidityData['days_sales_outstanding'], 1) . " days). Improve collection processes.";
        }

        if ($efficiencyData['avg_collection_days'] > 30) {
            $insights[] = "Average collection period is " . round($efficiencyData['avg_collection_days'], 1) . " days. Consider offering early payment discounts.";
        }

        return $insights;
    }

    private function generatePredictions($trendData)
    {
        if (empty($trendData) || count($trendData) < 7) {
            return [
                'next_week' => 0,
                'next_month' => 0,
                'confidence' => 'low',
                'message' => 'Insufficient data for accurate predictions'
            ];
        }

        $last7Days = array_slice($trendData, -7);
        $avgDailyRevenue = array_sum(array_column($last7Days, 'total_amount')) / 7;
        $growthRate = 0.03; // 3% weekly growth

        return [
            'next_week' => round($avgDailyRevenue * 7 * (1 + $growthRate), 2),
            'next_month' => round($avgDailyRevenue * 30 * (1 + $growthRate), 2),
            'confidence' => 'medium',
            'assumptions' => [
                'Based on last 7 days average',
                '3% weekly growth rate',
                'No major market changes'
            ]
        ];
    }

    /**
     * Export financial data
     */
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'in:csv,excel,pdf',
            'period' => 'in:7d,30d,90d,1y,qtd,ytd',
        ]);

        $type = $request->input('type', 'csv');
        $period = $request->input('period', '30d');

        try {
            $report = $this->generateFinancialReport($period, false);

            if ($type === 'csv') {
                return $this->exportToCsv($report, $period);
            }

            return back()->with('error', $type . ' export not yet implemented');

        } catch (\Exception $e) {
            Log::error('Failed to export financial data', [
                'type' => $type,
                'period' => $period,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv(array $report, string $period)
    {
        $filename = "financial_analytics_{$period}_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM for UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        // Header
        fputcsv($output, ['Financial Analytics Report - ' . strtoupper($period)]);
        fputcsv($output, ['Generated', date('Y-m-d H:i:s')]);
        fputcsv($output, ['Date Range', $report['date_range']['start'] . ' to ' . $report['date_range']['end']]);
        fputcsv($output, []);

        // Executive Summary
        fputcsv($output, ['EXECUTIVE SUMMARY']);
        fputcsv($output, ['Overview', $report['executive_summary']['overview'] ?? '']);
        fputcsv($output, ['Performance', $report['executive_summary']['performance'] ?? '']);
        fputcsv($output, []);
        fputcsv($output, ['Highlights']);
        foreach ($report['executive_summary']['highlights'] ?? [] as $highlight) {
            fputcsv($output, ['', $highlight]);
        }
        fputcsv($output, []);

        // Financial Metrics
        fputcsv($output, ['FINANCIAL METRICS']);

        // Revenue Metrics
        fputcsv($output, ['', 'REVENUE METRICS']);
        foreach ($report['financial_metrics']['revenue_metrics'] as $key => $value) {
            $formattedKey = ucwords(str_replace('_', ' ', $key));
            $formattedValue = is_numeric($value) ? '$' . number_format($value, 2) : $value;
            fputcsv($output, ['', $formattedKey, $formattedValue]);
        }
        fputcsv($output, []);

        // Profitability Metrics
        fputcsv($output, ['', 'PROFITABILITY METRICS']);
        foreach ($report['financial_metrics']['profitability_metrics'] as $key => $value) {
            $formattedKey = ucwords(str_replace('_', ' ', $key));
            if (str_contains($key, 'margin') || str_contains($key, 'rate')) {
                $formattedValue = number_format($value, 1) . '%';
            } else {
                $formattedValue = is_numeric($value) ? '$' . number_format($value, 2) : $value;
            }
            fputcsv($output, ['', $formattedKey, $formattedValue]);
        }
        fputcsv($output, []);

        // Key Insights
        if (!empty($report['key_insights'])) {
            fputcsv($output, ['KEY INSIGHTS']);
            foreach ($report['key_insights'] as $insight) {
                fputcsv($output, ['', $insight]);
            }
            fputcsv($output, []);
        }

        fclose($output);
        exit;
    }

    // Add these methods to your existing FinancialAnalyticsController class

/**
 * Financial KPIs dashboard
 */
public function kpis()
{
    try {
        $periods = [
            'current' => ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()],
            'previous' => ['start' => Carbon::now()->subMonth()->startOfMonth(), 'end' => Carbon::now()->subMonth()->endOfMonth()],
            'ytd' => ['start' => Carbon::now()->startOfYear(), 'end' => Carbon::now()]
        ];

        $kpis = [];
        foreach ($periods as $key => $period) {
            $kpis[$key] = [
                'revenue' => $this->getRevenueKpis($period['start'], $period['end']),
                'profitability' => $this->getProfitabilityKpis($period['start'], $period['end']),
                'liquidity' => $this->getLiquidityKpis($period['start'], $period['end']),
                'efficiency' => $this->getEfficiencyKpis($period['start'], $period['end'])
            ];
        }

        // Calculate changes and trends
        $comparisons = $this->calculateKpiComparisons($kpis);

        return view('finance.financial-analytics.kpis', compact('kpis', 'comparisons'));

    } catch (\Exception $e) {
        Log::error('Failed to load KPI dashboard', ['error' => $e->getMessage()]);
        return back()->with('error', 'Unable to load KPI dashboard: ' . $e->getMessage());
    }
}

/**
 * Benchmarking against targets
 */
public function benchmarking()
{
    try {
        $metrics = $this->generateFinancialReport('30d', false);

        // Define company targets
        $companyTargets = [
            'collection_rate' => 90,
            'net_margin' => 20,
            'current_ratio' => 2.0,
            'dsos' => 30,
            'revenue_growth' => 15,
            'customer_acquisition_cost' => 100,
            'customer_lifetime_value' => 500
        ];

        $comparison = [];
        foreach ($companyTargets as $metric => $target) {
            $currentValue = $this->extractMetricValue($metrics, $metric);
            $comparison[$metric] = [
                'current' => $currentValue,
                'target' => $target,
                'gap' => $target - $currentValue,
                'achievement' => $target > 0 ? ($currentValue / $target) * 100 : 0,
                'status' => $this->getTargetStatus($currentValue, $target)
            ];
        }

        // Group by category
        $groupedComparison = [
            'Revenue' => array_intersect_key($comparison, array_flip(['collection_rate', 'revenue_growth'])),
            'Profitability' => array_intersect_key($comparison, array_flip(['net_margin'])),
            'Liquidity' => array_intersect_key($comparison, array_flip(['current_ratio', 'dsos'])),
            'Customer' => array_intersect_key($comparison, array_flip(['customer_acquisition_cost', 'customer_lifetime_value']))
        ];

        return view('finance.financial-analytics.benchmarking', compact(
            'groupedComparison', 'metrics'
        ));

    } catch (\Exception $e) {
        Log::error('Failed to load benchmarking', ['error' => $e->getMessage()]);
        return back()->with('error', 'Unable to load benchmarking: ' . $e->getMessage());
    }
}

/**
 * Financial forecasting
 */
public function forecasting()
{
    try {
        $forecasts = [
            'revenue_forecast' => $this->forecastRevenue(),
            'profit_forecast' => $this->forecastProfit(),
            'cashflow_forecast' => $this->forecastCashflow(),
            'customer_forecast' => $this->forecastCustomers(),
            'risk_forecast' => $this->forecastRisks()
        ];

        // Make sure risk_forecast is in the correct format
        if (isset($forecasts['risk_forecast']) && !is_array($forecasts['risk_forecast'])) {
            // Convert to array format if it's not already
            $forecasts['risk_forecast'] = [
                'high_risk' => [
                    'category' => 'Collections',
                    'probability' => 0.3,
                    'impact' => 'High',
                    'mitigation' => 'Implement stricter credit terms'
                ],
                'medium_risk' => [
                    'category' => 'Customer Churn',
                    'probability' => 0.2,
                    'impact' => 'Medium',
                    'mitigation' => 'Improve customer service'
                ],
                'low_risk' => [
                    'category' => 'Market Competition',
                    'probability' => 0.1,
                    'impact' => 'Low',
                    'mitigation' => 'Differentiate service offerings'
                ]
            ];
        }

        // Calculate confidence levels
        foreach ($forecasts as &$forecast) {
            if (is_array($forecast)) {
                $forecast['confidence'] = $this->calculateForecastConfidence($forecast);
            }
        }

        return view('finance.financial-analytics.forecasting', compact('forecasts'));

    } catch (\Exception $e) {
        Log::error('Failed to load forecasting', ['error' => $e->getMessage()]);
        return back()->with('error', 'Unable to load forecasting: ' . $e->getMessage());
    }
}

// ======================================================================
// ADDITIONAL PRIVATE METHODS NEEDED
// ======================================================================

/**
 * Calculate KPI comparisons
 */
private function calculateKpiComparisons(array $kpis): array
{
    $comparisons = [];

    foreach ($kpis['current'] as $category => $metrics) {
        foreach ($metrics as $metric => $value) {
            if (isset($kpis['previous'][$category][$metric]) && is_numeric($value)) {
                $previous = $kpis['previous'][$category][$metric];
                $change = $previous != 0 ? (($value - $previous) / abs($previous)) * 100 : 0;

                $comparisons[$category][$metric] = [
                    'current' => $value,
                    'previous' => $previous,
                    'change' => round($change, 2),
                    'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
                ];
            }
        }
    }

    return $comparisons;
}

/**
 * Extract metric value from report
 */
private function extractMetricValue(array $metrics, string $metric): float
{
    // Map metric names to report structure
    $mapping = [
        'collection_rate' => ['financial_metrics', 'revenue_metrics', 'collection_rate'],
        'net_margin' => ['financial_metrics', 'profitability_metrics', 'net_margin'],
        'current_ratio' => ['financial_metrics', 'liquidity_metrics', 'current_ratio'],
        'dsos' => ['financial_metrics', 'liquidity_metrics', 'days_sales_outstanding'],
        'revenue_growth' => ['financial_metrics', 'revenue_metrics', 'revenue_growth'],
        'customer_acquisition_cost' => ['financial_metrics', 'efficiency_metrics', 'customer_acquisition_cost'],
        'customer_lifetime_value' => ['financial_metrics', 'efficiency_metrics', 'customer_lifetime_value']
    ];

    if (!isset($mapping[$metric])) {
        return 0.0;
    }

    $value = $metrics;
    foreach ($mapping[$metric] as $segment) {
        if (isset($value[$segment])) {
            $value = $value[$segment];
        } else {
            return 0.0;
        }
    }

    return is_numeric($value) ? floatval($value) : 0.0;
}

/**
 * Get target status
 */
private function getTargetStatus(float $current, float $target): string
{
    if ($current >= $target * 1.1) return 'exceeded';
    if ($current >= $target * 0.9) return 'met';
    if ($current >= $target * 0.7) return 'near';
    return 'below';
}

/**
 * Forecast methods
 */
private function forecastRevenue(): array
{
    $last3Months = DB::table('consolidated_billings')
        ->whereBetween('billing_date', [Carbon::now()->subMonths(3), Carbon::now()])
        ->sum('total_amount') ?? 0;

    $monthlyAvg = $last3Months / 3;
    $growthRate = 0.05;

    return [
        'next_month' => $monthlyAvg * (1 + $growthRate),
        'next_quarter' => $monthlyAvg * 3 * (1 + $growthRate),
        'confidence' => 'medium',
        'assumptions' => ['5% monthly growth', 'No major customer churn']
    ];
}

private function forecastProfit(): array
{
    $revenueForecast = $this->forecastRevenue();
    $profitMargin = 0.15;

    return [
        'next_month' => $revenueForecast['next_month'] * $profitMargin,
        'next_quarter' => $revenueForecast['next_quarter'] * $profitMargin,
        'confidence' => 'medium',
        'assumptions' => ['15% net margin', 'Stable costs']
    ];
}

private function forecastCashflow(): array
{
    $lastMonthCollections = DB::table('consolidated_billings')
        ->whereMonth('payment_date', Carbon::now()->subMonth()->month)
        ->whereYear('payment_date', Carbon::now()->subMonth()->year)
        ->where('status', 'paid')
        ->sum('paid_amount') ?? 0;

    return [
        'next_month' => $lastMonthCollections * 1.05,
        'next_quarter' => $lastMonthCollections * 3 * 1.05,
        'confidence' => 'medium',
        'assumptions' => ['5% collection growth', '90% collection rate']
    ];
}

private function forecastCustomers(): array
{
    $last3MonthsNew = DB::table('users')
        ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])
        ->where('role', 'customer')
        ->count() ?? 0;

    $monthlyAvg = $last3MonthsNew / 3;

    return [
        'next_month' => round($monthlyAvg * 1.1),
        'next_quarter' => round($monthlyAvg * 3 * 1.1),
        'churn_rate' => 0.03,
        'confidence' => 'low',
        'assumptions' => ['10% growth', '3% monthly churn']
    ];
}

private function forecastRisks(): array
{
    return [
        'high_risk' => [
            'category' => 'Collections',
            'probability' => 0.3,
            'impact' => 'High',
            'mitigation' => 'Implement stricter credit terms'
        ],
        'medium_risk' => [
            'category' => 'Customer Churn',
            'probability' => 0.2,
            'impact' => 'Medium',
            'mitigation' => 'Improve customer service'
        ],
        'low_risk' => [
            'category' => 'Market Competition',
            'probability' => 0.1,
            'impact' => 'Low',
            'mitigation' => 'Differentiate service offerings'
        ]
    ];
}

/**
 * Calculate forecast confidence
 */
private function calculateForecastConfidence(array $forecast): string
{
    $confidenceFactors = [
        'historical_data_points' => 3,
        'data_quality' => 'high',
        'market_stability' => 'medium',
        'internal_factors' => 'controlled'
    ];

    $score = 0;
    foreach ($confidenceFactors as $factor => $value) {
        if ($value === 'high') $score += 25;
        elseif ($value === 'medium') $score += 15;
        elseif ($value === 'low') $score += 5;
        elseif (is_numeric($value)) $score += min($value * 5, 25);
    }

    if ($score >= 80) return 'high';
    if ($score >= 60) return 'medium';
    return 'low';
}
}
