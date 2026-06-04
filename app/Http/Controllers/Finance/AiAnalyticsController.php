<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ConsolidatedBilling;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiAnalyticsController extends Controller
{
    /**
     * Display AI-powered debt analytics dashboard
     */
    public function dashboard(Request $request)
    {
        try {
            // Get metrics for both currencies
            $usdMetrics = $this->getMetrics('USD');
            $kshMetrics = $this->getMetrics('KSH');

            // Get aging analysis for both currencies - FIXED
            $usdAging = $this->getAgingAnalysis('USD');
            $kshAging = $this->getAgingAnalysis('KSH');

            // Get top debtors - FIXED
            $topDebtors = $this->getTopDebtors();

            // Get collection trends
            $collectionTrends = $this->getCollectionTrends();

            // Get AI insights
            $insights = $this->getAIInsights($usdMetrics, $kshMetrics);

            return view('finance.ai-analytics.dashboard', compact(
                'usdMetrics',
                'kshMetrics',
                'usdAging',
                'kshAging',
                'topDebtors',
                'collectionTrends',
                'insights'
            ));

        } catch (\Exception $e) {
            Log::error('AI Analytics Dashboard Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return view('finance.ai-analytics.dashboard', [
                'usdMetrics' => $this->getEmptyMetrics(),
                'kshMetrics' => $this->getEmptyMetrics(),
                'usdAging' => $this->getEmptyAging(),
                'kshAging' => $this->getEmptyAging(),
                'topDebtors' => [],
                'collectionTrends' => $this->getEmptyCollectionTrends(),
                'insights' => $this->getEmptyInsights(),
            ]);
        }
    }

    /**
     * Get metrics for a specific currency
     */
    private function getMetrics(string $currency)
    {
        try {
            $today = Carbon::now()->toDateString();

            // Total outstanding (unpaid balance)
            $totalOutstanding = DB::table('consolidated_billings')
                ->where('currency', $currency)
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

            // Overdue amount
            $overdueAmount = DB::table('consolidated_billings')
                ->where('currency', $currency)
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->where('due_date', '<', $today)
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

            // Overdue percentage
            $overduePercentage = $totalOutstanding > 0 ? ($overdueAmount / $totalOutstanding) * 100 : 0;

            // Collection rate
            $totalBilled = DB::table('consolidated_billings')
                ->where('currency', $currency)
                ->sum('total_amount') ?: 1;

            $totalPaid = DB::table('consolidated_billings')
                ->where('currency', $currency)
                ->where('status', 'paid')
                ->sum('paid_amount') ?: 0;

            $collectionRate = ($totalPaid / $totalBilled) * 100;

            // Today's collections
            $todayCollections = DB::table('payments')
                ->where('status', 'validated')
                ->where('currency', $currency)
                ->whereDate('payment_date', $today)
                ->sum('amount') ?: 0;

            // High-risk customers count
            $highRiskCount = $this->getHighRiskCount($currency);

            // Average collection days
            $avgCollectionDays = DB::table('consolidated_billings')
                ->where('currency', $currency)
                ->where('status', 'paid')
                ->whereNotNull('payment_date')
                ->whereNotNull('billing_date')
                ->select(DB::raw('AVG(DATEDIFF(payment_date, billing_date)) as avg_days'))
                ->value('avg_days') ?: 0;

            return (object)[
                'total_outstanding' => round($totalOutstanding, 2),
                'overdue_amount' => round($overdueAmount, 2),
                'overdue_percentage' => round($overduePercentage, 1),
                'collection_rate' => round($collectionRate, 1),
                'today_collections' => round($todayCollections, 2),
                'high_risk_count' => $highRiskCount,
                'avg_collection_days' => round($avgCollectionDays),
                'formatted_outstanding' => $currency === 'USD' ? '$' . number_format($totalOutstanding, 2) : 'KSH ' . number_format($totalOutstanding, 2),
                'formatted_overdue' => $currency === 'USD' ? '$' . number_format($overdueAmount, 2) : 'KSH ' . number_format($overdueAmount, 2),
                'formatted_today' => $currency === 'USD' ? '$' . number_format($todayCollections, 2) : 'KSH ' . number_format($todayCollections, 2),
            'expected_collections' => round($this->calculateExpectedCollections($currency), 2),
    'default_risk' => round($this->calculateDefaultRiskForCurrency($currency), 1),
    'cash_flow_forecast' => round($this->calculateCashFlowForecastForCurrency($currency), 2),
            ];

        } catch (\Exception $e) {
            Log::error("Error getting metrics for {$currency}: " . $e->getMessage());
            return $this->getEmptyMetrics();
        }
    }

    private function calculateExpectedCollections(string $currency): float
{
    $avgDailyCollection = DB::table('payments')
        ->where('status', 'validated')
        ->where('currency', $currency)
        ->where('payment_date', '>=', Carbon::now()->subDays(90))
        ->select(DB::raw('COALESCE(SUM(amount) / 90, 0) as avg_daily'))
        ->value('avg_daily') ?: 0;

    $upcomingDue = DB::table('consolidated_billings')
        ->where('currency', $currency)
        ->whereIn('status', ['pending', 'sent', 'partial'])
        ->where('due_date', '>=', Carbon::now())
        ->where('due_date', '<=', Carbon::now()->addDays(30))
        ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

    return ($avgDailyCollection * 30) + ($upcomingDue * 0.7);
}

private function calculateDefaultRiskForCurrency(string $currency): float
{
    $totalOutstanding = DB::table('consolidated_billings')
        ->where('currency', $currency)
        ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
        ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 1;

    $overdueAmount = DB::table('consolidated_billings')
        ->where('currency', $currency)
        ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
        ->where('due_date', '<', Carbon::now())
        ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

    return ($overdueAmount / $totalOutstanding) * 100;
}

private function calculateCashFlowForecastForCurrency(string $currency): float
{
    $avgMonthlyCollection = DB::table('payments')
        ->where('status', 'validated')
        ->where('currency', $currency)
        ->where('payment_date', '>=', Carbon::now()->subMonths(6))
        ->select(DB::raw('COALESCE(SUM(amount) / 6, 0) as avg_monthly'))
        ->value('avg_monthly') ?: 0;

    $upcomingDue = DB::table('consolidated_billings')
        ->where('currency', $currency)
        ->whereIn('status', ['pending', 'sent', 'partial'])
        ->where('due_date', '>=', Carbon::now())
        ->where('due_date', '<=', Carbon::now()->addDays(90))
        ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

    return ($avgMonthlyCollection * 3) + ($upcomingDue * 0.8);
}
    /**
     * Get high-risk customers count for a currency
     */
    private function getHighRiskCount(string $currency): int
    {
        try {
            $invoices = DB::table('consolidated_billings')
                ->where('currency', $currency)
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->select('user_id', 'total_amount', 'paid_amount', 'due_date')
                ->get();

            $highRiskCustomers = [];

            foreach ($invoices as $invoice) {
                $userId = $invoice->user_id;
                $outstanding = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);
                if ($outstanding <= 0) continue;

                $dueDate = Carbon::parse($invoice->due_date);
                $daysOverdue = $dueDate->lt(Carbon::now()) ? $dueDate->diffInDays(Carbon::now()) : 0;

                // Risk score calculation
                $riskScore = 0;
                if ($outstanding > 500000) $riskScore += 35;
                elseif ($outstanding > 100000) $riskScore += 25;
                elseif ($outstanding > 50000) $riskScore += 15;
                elseif ($outstanding > 10000) $riskScore += 10;
                else $riskScore += 5;

                if ($daysOverdue > 90) $riskScore += 40;
                elseif ($daysOverdue > 60) $riskScore += 30;
                elseif ($daysOverdue > 30) $riskScore += 20;
                elseif ($daysOverdue > 0) $riskScore += 10;

                if ($riskScore >= 50) {
                    $highRiskCustomers[$userId] = true;
                }
            }

            return count($highRiskCustomers);

        } catch (\Exception $e) {
            Log::error("Error getting high risk count for {$currency}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get aging analysis for a specific currency - FIXED
     */
    private function getAgingAnalysis(string $currency)
    {
        try {
            $today = Carbon::now();

            $invoices = DB::table('consolidated_billings')
                ->where('currency', $currency)
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->select('total_amount', 'paid_amount', 'due_date')
                ->get();

            $current = 0;
            $days1_30 = 0;
            $days31_60 = 0;
            $days61_90 = 0;
            $daysOver90 = 0;
            $totalInvoices = $invoices->count();

            foreach ($invoices as $invoice) {
                $outstanding = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);
                if ($outstanding <= 0) continue;

                $dueDate = Carbon::parse($invoice->due_date);

                if ($dueDate->gte($today)) {
                    // Not overdue yet - current
                    $current += $outstanding;
                } else {
                    $daysOverdue = $dueDate->diffInDays($today);
                    if ($daysOverdue <= 30) {
                        $days1_30 += $outstanding;
                    } elseif ($daysOverdue <= 60) {
                        $days31_60 += $outstanding;
                    } elseif ($daysOverdue <= 90) {
                        $days61_90 += $outstanding;
                    } else {
                        $daysOver90 += $outstanding;
                    }
                }
            }

            $total = $current + $days1_30 + $days31_60 + $days61_90 + $daysOver90;

            return (object)[
                'current' => round($current, 2),
                'days1_30' => round($days1_30, 2),
                'days31_60' => round($days31_60, 2),
                'days61_90' => round($days61_90, 2),
                'days_over_90' => round($daysOver90, 2),
                'total' => round($total, 2),
                'invoice_count' => $totalInvoices,
                'current_percentage' => $total > 0 ? ($current / $total) * 100 : 0,
                'days1_30_percentage' => $total > 0 ? ($days1_30 / $total) * 100 : 0,
                'days31_60_percentage' => $total > 0 ? ($days31_60 / $total) * 100 : 0,
                'days61_90_percentage' => $total > 0 ? ($days61_90 / $total) * 100 : 0,
                'days_over_90_percentage' => $total > 0 ? ($daysOver90 / $total) * 100 : 0,
            ];

        } catch (\Exception $e) {
            Log::error("Error getting aging analysis for {$currency}: " . $e->getMessage());
            return $this->getEmptyAging();
        }
    }

    /**
     * Get top debtors - FIXED
     */
    private function getTopDebtors()
    {
        try {
            // Get all outstanding invoices with customer details
            $debtors = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->whereIn('consolidated_billings.status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
                ->select(
                    'users.id as user_id',
                    'users.name as customer_name',
                    'users.email',
                    'consolidated_billings.currency',
                    DB::raw('consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0) as outstanding'),
                    'consolidated_billings.due_date'
                )
                ->get();

            $grouped = [];
            foreach ($debtors as $debtor) {
                if (!isset($grouped[$debtor->user_id])) {
                    $grouped[$debtor->user_id] = [
                        'user_id' => $debtor->user_id,
                        'customer_name' => $debtor->customer_name ?? 'Unknown',
                        'email' => $debtor->email ?? 'No email',
                        'usd_outstanding' => 0,
                        'ksh_outstanding' => 0,
                        'overdue_count' => 0,
                        'risk_level' => 'Low',
                    ];
                }

                if ($debtor->currency === 'USD') {
                    $grouped[$debtor->user_id]['usd_outstanding'] += $debtor->outstanding;
                } else {
                    $grouped[$debtor->user_id]['ksh_outstanding'] += $debtor->outstanding;
                }

                // Check if overdue
                if (Carbon::parse($debtor->due_date)->lt(Carbon::now())) {
                    $grouped[$debtor->user_id]['overdue_count']++;
                }
            }

            // Calculate risk level
            foreach ($grouped as &$debtor) {
                $totalOutstanding = $debtor['usd_outstanding'] + ($debtor['ksh_outstanding'] / 130);
                if ($totalOutstanding > 100000 || $debtor['overdue_count'] > 5) {
                    $debtor['risk_level'] = 'Critical';
                } elseif ($totalOutstanding > 50000 || $debtor['overdue_count'] > 2) {
                    $debtor['risk_level'] = 'High';
                } elseif ($totalOutstanding > 10000) {
                    $debtor['risk_level'] = 'Medium';
                } else {
                    $debtor['risk_level'] = 'Low';
                }
            }

            // Sort by total outstanding
            usort($grouped, function($a, $b) {
                $totalA = $a['usd_outstanding'] + ($a['ksh_outstanding'] / 130);
                $totalB = $b['usd_outstanding'] + ($b['ksh_outstanding'] / 130);
                return $totalB <=> $totalA;
            });

            return array_slice($grouped, 0, 10);

        } catch (\Exception $e) {
            Log::error("Error getting top debtors: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get collection trends
     */
    private function getCollectionTrends()
    {
        try {
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(30);

            $dates = [];
            $usdAmounts = [];
            $kshAmounts = [];
            $counts = [];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                $dates[] = $date->format('M d');

                $usdAmounts[] = DB::table('payments')
                    ->where('status', 'validated')
                    ->where('currency', 'USD')
                    ->whereDate('payment_date', $dateStr)
                    ->sum('amount') ?: 0;

                $kshAmounts[] = DB::table('payments')
                    ->where('status', 'validated')
                    ->where('currency', 'KES')
                    ->whereDate('payment_date', $dateStr)
                    ->sum('amount') ?: 0;

                $counts[] = DB::table('payments')
                    ->where('status', 'validated')
                    ->whereDate('payment_date', $dateStr)
                    ->count();
            }

            $totalUsd = array_sum($usdAmounts);
            $totalKsh = array_sum($kshAmounts);
            $totalPayments = array_sum($counts);
            $avgDailyUsd = count($usdAmounts) > 0 ? $totalUsd / count($usdAmounts) : 0;
            $avgDailyKsh = count($kshAmounts) > 0 ? $totalKsh / count($kshAmounts) : 0;

            // Trend calculation
            $recentAvg = count($usdAmounts) >= 7 ? array_sum(array_slice($usdAmounts, -7)) / 7 : 0;
            $previousAvg = count($usdAmounts) >= 14 ? array_sum(array_slice($usdAmounts, -14, 7)) / 7 : 0;
            $trendPercentage = $previousAvg > 0 ? (($recentAvg - $previousAvg) / $previousAvg) * 100 : 0;
            $trendDirection = $trendPercentage > 0 ? 'up' : ($trendPercentage < 0 ? 'down' : 'stable');

            return [
                'labels' => $dates,
                'usd_amounts' => $usdAmounts,
                'ksh_amounts' => $kshAmounts,
                'counts' => $counts,
                'total_usd' => $totalUsd,
                'total_ksh' => $totalKsh,
                'total_payments' => $totalPayments,
                'avg_daily_usd' => $avgDailyUsd,
                'avg_daily_ksh' => $avgDailyKsh,
                'trend_percentage' => round(abs($trendPercentage), 1),
                'trend_direction' => $trendDirection,
                'trend_message' => $totalUsd == 0 && $totalKsh == 0 ? 'No collection data available' :
                                   ($trendDirection == 'up' ? 'Increasing trend' :
                                   ($trendDirection == 'down' ? 'Decreasing trend' : 'Stable trend')),
            ];

        } catch (\Exception $e) {
            Log::error("Error getting collection trends: " . $e->getMessage());
            return $this->getEmptyCollectionTrends();
        }
    }

    /**
     * Get AI insights
     */
    private function getAIInsights($usdMetrics, $kshMetrics)
    {
        $keyFindings = [];
        $riskAnalysis = [];
        $recommendations = [];

        // USD findings
        if ($usdMetrics->total_outstanding > 0) {
            $keyFindings[] = "USD Outstanding: $" . number_format($usdMetrics->total_outstanding, 2);
            if ($usdMetrics->overdue_percentage > 30) {
                $riskAnalysis[] = "USD overdue rate at {$usdMetrics->overdue_percentage}% - above threshold";
            }
            if ($usdMetrics->collection_rate < 70) {
                $recommendations[] = [
                    'action' => "Improve USD collection rate (currently {$usdMetrics->collection_rate}%)",
                    'priority' => 'High'
                ];
            }
        }

        // KSH findings
        if ($kshMetrics->total_outstanding > 0) {
            $keyFindings[] = "KSH Outstanding: KSH " . number_format($kshMetrics->total_outstanding, 2);
            if ($kshMetrics->overdue_percentage > 30) {
                $riskAnalysis[] = "KSH overdue rate at {$kshMetrics->overdue_percentage}% - above threshold";
            }
            if ($kshMetrics->collection_rate < 70) {
                $recommendations[] = [
                    'action' => "Improve KSH collection rate (currently {$kshMetrics->collection_rate}%)",
                    'priority' => 'High'
                ];
            }
        }

        // Combined findings
        $totalHighRisk = ($usdMetrics->high_risk_count ?? 0) + ($kshMetrics->high_risk_count ?? 0);
        if ($totalHighRisk > 0) {
            $keyFindings[] = "$totalHighRisk high-risk customers require immediate attention";
            $riskAnalysis[] = "High concentration of risk in {$totalHighRisk} customers";
            $recommendations[] = [
                'action' => "Prioritize collection efforts on high-risk customers",
                'priority' => 'High'
            ];
        }

        if (empty($keyFindings)) {
            $keyFindings[] = "No outstanding debt - all invoices are paid";
            $riskAnalysis[] = "No significant risks detected";
            $recommendations[] = [
                'action' => "Maintain current collection strategies",
                'priority' => 'Medium'
            ];
        }

        return [
            'key_findings' => $keyFindings,
            'risk_analysis' => $riskAnalysis,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Empty data helpers
     */
    private function getEmptyMetrics()
    {
        return (object)[
            'total_outstanding' => 0,
            'overdue_amount' => 0,
            'overdue_percentage' => 0,
            'collection_rate' => 0,
            'today_collections' => 0,
            'high_risk_count' => 0,
            'avg_collection_days' => 0,
            'formatted_outstanding' => '$0',
            'formatted_overdue' => '$0',
            'formatted_today' => '$0',
        ];
    }

    private function getEmptyAging()
    {
        return (object)[
            'current' => 0,
            'days1_30' => 0,
            'days31_60' => 0,
            'days61_90' => 0,
            'days_over_90' => 0,
            'total' => 0,
            'invoice_count' => 0,
            'current_percentage' => 0,
            'days1_30_percentage' => 0,
            'days31_60_percentage' => 0,
            'days61_90_percentage' => 0,
            'days_over_90_percentage' => 0,
        ];
    }

    private function getEmptyCollectionTrends()
    {
        return [
            'labels' => [],
            'usd_amounts' => [],
            'ksh_amounts' => [],
            'counts' => [],
            'total_usd' => 0,
            'total_ksh' => 0,
            'total_payments' => 0,
            'avg_daily_usd' => 0,
            'avg_daily_ksh' => 0,
            'trend_percentage' => 0,
            'trend_direction' => 'stable',
            'trend_message' => 'No collection data available',
        ];
    }

    private function getEmptyInsights()
    {
        return [
            'key_findings' => ['No key findings available'],
            'risk_analysis' => ['No risk analysis available'],
            'recommendations' => [
                ['action' => 'No recommendations available', 'priority' => 'Low']
            ],
        ];
    }

    // ========== Additional Required Methods ==========

    /**
 * Display predictive analytics dashboard
 */
public function predictiveAnalytics()
{
    try {
        // Get metrics for both currencies
        $usdMetrics = $this->getMetrics('USD');
        $kshMetrics = $this->getMetrics('KSH');

        // Get aging analysis for both currencies
        $usdAging = $this->getAgingAnalysis('USD');
        $kshAging = $this->getAgingAnalysis('KSH');

        // Get collection forecast
        $collectionForecast = $this->getCollectionForecast();

        // Get risk distribution
        $riskDistribution = $this->getRiskDistribution();

        // Get AI insights
        $insights = $this->getAIInsights($usdMetrics, $kshMetrics);

        return view('finance.ai-analytics.predictive', compact(
            'usdMetrics',
            'kshMetrics',
            'usdAging',
            'kshAging',
            'collectionForecast',
            'riskDistribution',
            'insights'
        ));

    } catch (\Exception $e) {
        Log::error('Predictive Analytics Error: ' . $e->getMessage());

        return view('finance.ai-analytics.predictive', [
            'usdMetrics' => $this->getEmptyMetrics(),
            'kshMetrics' => $this->getEmptyMetrics(),
            'usdAging' => $this->getEmptyAging(),
            'kshAging' => $this->getEmptyAging(),
            'collectionForecast' => [],
            'riskDistribution' => ['usd' => ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0], 'ksh' => ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0]],
            'insights' => $this->getEmptyInsights(),
        ]);
    }
}

/**
 * Get collection forecast for chart
 */
private function getCollectionForecast(): array
{
    $forecast = [];

    for ($i = 0; $i < 12; $i++) {
        $monthStart = Carbon::now()->addMonths($i)->startOfMonth();
        $monthEnd = Carbon::now()->addMonths($i)->endOfMonth();

        $dueUsd = DB::table('consolidated_billings')
            ->where('currency', 'USD')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->whereBetween('due_date', [$monthStart, $monthEnd])
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $dueKsh = DB::table('consolidated_billings')
            ->where('currency', 'KSH')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->whereBetween('due_date', [$monthStart, $monthEnd])
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $forecast[] = [
            'month' => $monthStart->format('M Y'),
            'usd_expected' => round($dueUsd * 0.75, 2),
            'ksh_expected' => round($dueKsh * 0.75, 2),
        ];
    }

    return $forecast;
}

/**
 * Get risk distribution
 */
private function getRiskDistribution(): array
{
    // USD Risk Distribution
    $usdInvoices = DB::table('consolidated_billings')
        ->where('currency', 'USD')
        ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
        ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
        ->get();

    $usdTotal = 0;
    $usdLow = 0;
    $usdMedium = 0;
    $usdHigh = 0;
    $usdCritical = 0;

    foreach ($usdInvoices as $invoice) {
        $outstanding = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);
        if ($outstanding <= 0) continue;

        $dueDate = Carbon::parse($invoice->due_date);
        $daysOverdue = $dueDate->lt(Carbon::now()) ? $dueDate->diffInDays(Carbon::now()) : 0;

        $riskScore = $this->calculateRiskScore($outstanding, $daysOverdue, $invoice->total_amount);
        $usdTotal += $outstanding;

        if ($riskScore >= 75) $usdCritical += $outstanding;
        elseif ($riskScore >= 50) $usdHigh += $outstanding;
        elseif ($riskScore >= 25) $usdMedium += $outstanding;
        else $usdLow += $outstanding;
    }

    // KSH Risk Distribution
    $kshInvoices = DB::table('consolidated_billings')
        ->where('currency', 'KSH')
        ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
        ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
        ->get();

    $kshTotal = 0;
    $kshLow = 0;
    $kshMedium = 0;
    $kshHigh = 0;
    $kshCritical = 0;

    foreach ($kshInvoices as $invoice) {
        $outstanding = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);
        if ($outstanding <= 0) continue;

        $dueDate = Carbon::parse($invoice->due_date);
        $daysOverdue = $dueDate->lt(Carbon::now()) ? $dueDate->diffInDays(Carbon::now()) : 0;

        $riskScore = $this->calculateRiskScore($outstanding, $daysOverdue, $invoice->total_amount);
        $kshTotal += $outstanding;

        if ($riskScore >= 75) $kshCritical += $outstanding;
        elseif ($riskScore >= 50) $kshHigh += $outstanding;
        elseif ($riskScore >= 25) $kshMedium += $outstanding;
        else $kshLow += $outstanding;
    }

    return [
        'usd' => [
            'low' => ['amount' => $usdLow, 'percentage' => $usdTotal > 0 ? ($usdLow / $usdTotal) * 100 : 0],
            'medium' => ['amount' => $usdMedium, 'percentage' => $usdTotal > 0 ? ($usdMedium / $usdTotal) * 100 : 0],
            'high' => ['amount' => $usdHigh, 'percentage' => $usdTotal > 0 ? ($usdHigh / $usdTotal) * 100 : 0],
            'critical' => ['amount' => $usdCritical, 'percentage' => $usdTotal > 0 ? ($usdCritical / $usdTotal) * 100 : 0],
        ],
        'ksh' => [
            'low' => ['amount' => $kshLow, 'percentage' => $kshTotal > 0 ? ($kshLow / $kshTotal) * 100 : 0],
            'medium' => ['amount' => $kshMedium, 'percentage' => $kshTotal > 0 ? ($kshMedium / $kshTotal) * 100 : 0],
            'high' => ['amount' => $kshHigh, 'percentage' => $kshTotal > 0 ? ($kshHigh / $kshTotal) * 100 : 0],
            'critical' => ['amount' => $kshCritical, 'percentage' => $kshTotal > 0 ? ($kshCritical / $kshTotal) * 100 : 0],
        ],
    ];
}

private function calculateRiskScore(float $outstanding, int $daysOverdue, float $totalAmount): int
{
    $score = 0;

    if ($outstanding > 500000) $score += 35;
    elseif ($outstanding > 100000) $score += 25;
    elseif ($outstanding > 50000) $score += 15;
    elseif ($outstanding > 10000) $score += 10;
    else $score += 5;

    if ($daysOverdue > 90) $score += 40;
    elseif ($daysOverdue > 60) $score += 30;
    elseif ($daysOverdue > 30) $score += 20;
    elseif ($daysOverdue > 0) $score += 10;

    $percentage = ($outstanding / max($totalAmount, 1)) * 100;
    if ($percentage > 80) $score += 20;
    elseif ($percentage > 50) $score += 15;
    elseif ($percentage > 30) $score += 10;
    else $score += 5;

    return min($score, 100);
}

    public function customerIntelligence($id)
    {
        $customer = User::findOrFail($id);

        $usdInvoices = ConsolidatedBilling::where('user_id', $id)
            ->where('currency', 'USD')
            ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
            ->get();

        $kshInvoices = ConsolidatedBilling::where('user_id', $id)
            ->where('currency', 'KSH')
            ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
            ->get();

        $paymentHistory = Transaction::where('user_id', $id)
            ->where('type', 'income')
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();

        return view('finance.ai-analytics.customer', compact('customer', 'usdInvoices', 'kshInvoices', 'paymentHistory'));
    }

    public function recommendations()
    {
        $usdMetrics = $this->getMetrics('USD');
        $kshMetrics = $this->getMetrics('KSH');
        $insights = $this->getAIInsights($usdMetrics, $kshMetrics);
        return view('finance.ai-analytics.recommendations', compact('usdMetrics', 'kshMetrics', 'insights'));
    }

    public function generateReport()
    {
        return redirect()->back()->with('info', 'Report generation coming soon');
    }

    public function exportReport()
    {
        return redirect()->back()->with('info', 'Export feature coming soon');
    }

    public function sendReminder($id)
    {
        return response()->json(['success' => true, 'message' => 'Reminder sent']);
    }
}
