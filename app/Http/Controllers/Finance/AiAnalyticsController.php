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
            $currency = $request->get('currency', 'all');

            // Get metrics for both currencies
            $metrics = $this->getMetrics($currency);

            // Get aging analysis
            $agingAnalysis = $this->getAgingAnalysis($currency);

            // Get top debtors
            $topDebtors = $this->getTopDebtors($currency);

            // Get AI insights
            $insights = $this->generateAIInsights($metrics, $agingAnalysis, $topDebtors);

            // Get collection trends
            $collectionTrends = $this->getCollectionTrends($currency);

            return view('finance.ai-analytics.dashboard', compact(
                'metrics',
                'agingAnalysis',
                'topDebtors',
                'insights',
                'collectionTrends'
            ));

        } catch (\Exception $e) {
            Log::error('AI Analytics Dashboard Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Return empty data structure to avoid errors
            return view('finance.ai-analytics.dashboard', [
                'metrics' => $this->getEmptyMetrics(),
                'agingAnalysis' => $this->getEmptyAgingAnalysis(),
                'topDebtors' => [],
                'insights' => $this->getEmptyInsights(),
                'collectionTrends' => $this->getEmptyCollectionTrends()
            ]);
        }
    }

    /**
     * Customer intelligence
     */
    public function customerIntelligence($id)
    {
        try {
            $customer = User::findOrFail($id);

            $invoices = ConsolidatedBilling::where('user_id', $id)
                ->whereIn('status', ['pending', 'sent', 'overdue', 'partial'])
                ->get();

            $outstandingUsd = $invoices->where('currency', 'USD')->sum(function($inv) {
                return floatval($inv->total_amount) - floatval($inv->paid_amount ?? 0);
            });

            $outstandingKsh = $invoices->where('currency', 'KSH')->sum(function($inv) {
                return floatval($inv->total_amount) - floatval($inv->paid_amount ?? 0);
            });

            $paymentHistory = Transaction::where('user_id', $id)
                ->where('type', 'income')
                ->orderBy('transaction_date', 'desc')
                ->limit(10)
                ->get();

            return view('finance.ai-analytics.customer', compact('customer', 'invoices', 'outstandingUsd', 'outstandingKsh', 'paymentHistory'));

        } catch (\Exception $e) {
            Log::error('Customer intelligence error: ' . $e->getMessage());
            return redirect()->route('finance.ai.dashboard')->with('error', 'Customer not found');
        }
    }

    /**
     * Predictive analytics
     */
    public function predictiveAnalytics()
    {
        return view('finance.ai-analytics.predictive');
    }

    /**
     * Recommendations
     */
    public function recommendations()
    {
        return view('finance.ai-analytics.recommendations');
    }

    /**
     * Generate report
     */
    public function generateReport(Request $request)
    {
        // Implement PDF report generation
        return redirect()->back()->with('info', 'Report generation feature coming soon');
    }

    // ========== Private Helper Methods ==========

    private function getMetrics($currency = 'all')
    {
        try {
            $query = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'overdue', 'partial']);

            if ($currency === 'USD') {
                $query->where('currency', 'USD');
            } elseif ($currency === 'KSH') {
                $query->where('currency', 'KSH');
            }

            $invoices = $query->get();

            // USD metrics
            $usdInvoices = $invoices->where('currency', 'USD');
            $totalOutstandingUsd = $usdInvoices->sum(function($inv) {
                return floatval($inv->total_amount) - floatval($inv->paid_amount ?? 0);
            });

            $overdueUsd = $usdInvoices->filter(function($inv) {
                return Carbon::parse($inv->due_date)->lt(now());
            });
            $overdueAmountUsd = $overdueUsd->sum(function($inv) {
                return floatval($inv->total_amount) - floatval($inv->paid_amount ?? 0);
            });
            $overdueCountUsd = $overdueUsd->count();
            $overduePercentageUsd = $totalOutstandingUsd > 0 ? ($overdueAmountUsd / $totalOutstandingUsd) * 100 : 0;

            // KSH metrics
            $kshInvoices = $invoices->where('currency', 'KSH');
            $totalOutstandingKsh = $kshInvoices->sum(function($inv) {
                return floatval($inv->total_amount) - floatval($inv->paid_amount ?? 0);
            });

            $overdueKsh = $kshInvoices->filter(function($inv) {
                return Carbon::parse($inv->due_date)->lt(now());
            });
            $overdueAmountKsh = $overdueKsh->sum(function($inv) {
                return floatval($inv->total_amount) - floatval($inv->paid_amount ?? 0);
            });
            $overdueCountKsh = $overdueKsh->count();
            $overduePercentageKsh = $totalOutstandingKsh > 0 ? ($overdueAmountKsh / $totalOutstandingKsh) * 100 : 0;

            // Collection rates
            $totalBilledUsd = ConsolidatedBilling::where('currency', 'USD')->sum('total_amount');
            $totalPaidUsd = ConsolidatedBilling::where('currency', 'USD')->where('status', 'paid')->sum('paid_amount');
            $collectionRateUsd = $totalBilledUsd > 0 ? ($totalPaidUsd / $totalBilledUsd) * 100 : 0;

            $totalBilledKsh = ConsolidatedBilling::where('currency', 'KSH')->sum('total_amount');
            $totalPaidKsh = ConsolidatedBilling::where('currency', 'KSH')->where('status', 'paid')->sum('paid_amount');
            $collectionRateKsh = $totalBilledKsh > 0 ? ($totalPaidKsh / $totalBilledKsh) * 100 : 0;

            // Today's collections
            $today = now()->toDateString();
            $todayCollectionsUsd = Transaction::where('type', 'income')
                ->where('currency', 'USD')
                ->whereDate('transaction_date', $today)
                ->sum('amount');
            $todayCollectionsKsh = Transaction::where('type', 'income')
                ->where('currency', 'KSH')
                ->whereDate('transaction_date', $today)
                ->sum('amount');

            return [
                'total_outstanding_usd' => $totalOutstandingUsd,
                'total_outstanding_ksh' => $totalOutstandingKsh,
                'overdue_amount_usd' => $overdueAmountUsd,
                'overdue_amount_ksh' => $overdueAmountKsh,
                'overdue_count_usd' => $overdueCountUsd,
                'overdue_count_ksh' => $overdueCountKsh,
                'overdue_percentage_usd' => round($overduePercentageUsd, 1),
                'overdue_percentage_ksh' => round($overduePercentageKsh, 1),
                'collection_rate_usd' => round($collectionRateUsd, 1),
                'collection_rate_ksh' => round($collectionRateKsh, 1),
                'today_collections_usd' => $todayCollectionsUsd,
                'today_collections_ksh' => $todayCollectionsKsh,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting metrics: ' . $e->getMessage());
            return $this->getEmptyMetrics();
        }
    }

    private function getAgingAnalysis($currency = 'all')
    {
        try {
            $query = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'overdue', 'partial']);

            if ($currency === 'USD') {
                $query->where('currency', 'USD');
            } elseif ($currency === 'KSH') {
                $query->where('currency', 'KSH');
            }

            $invoices = $query->get();
            $today = Carbon::now();

            $analysis = [
                'current_usd' => 0, 'current_ksh' => 0, 'current_count' => 0,
                'days_31_60_usd' => 0, 'days_31_60_ksh' => 0, 'days_31_60_count' => 0,
                'days_61_90_usd' => 0, 'days_61_90_ksh' => 0, 'days_61_90_count' => 0,
                'days_over_90_usd' => 0, 'days_over_90_ksh' => 0, 'days_over_90_count' => 0,
                'total_usd' => 0, 'total_ksh' => 0,
            ];

            foreach ($invoices as $invoice) {
                $outstanding = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);
                if ($outstanding <= 0) continue;

                $dueDate = Carbon::parse($invoice->due_date);
                $daysOverdue = $dueDate->lt($today) ? $dueDate->diffInDays($today) : 0;

                if ($invoice->currency === 'USD') {
                    $analysis['total_usd'] += $outstanding;
                    if ($daysOverdue == 0) {
                        $analysis['current_usd'] += $outstanding;
                        $analysis['current_count']++;
                    } elseif ($daysOverdue <= 30) {
                        $analysis['days_31_60_usd'] += $outstanding;
                        $analysis['days_31_60_count']++;
                    } elseif ($daysOverdue <= 60) {
                        $analysis['days_61_90_usd'] += $outstanding;
                        $analysis['days_61_90_count']++;
                    } else {
                        $analysis['days_over_90_usd'] += $outstanding;
                        $analysis['days_over_90_count']++;
                    }
                } else {
                    $analysis['total_ksh'] += $outstanding;
                    if ($daysOverdue == 0) {
                        $analysis['current_ksh'] += $outstanding;
                        $analysis['current_count']++;
                    } elseif ($daysOverdue <= 30) {
                        $analysis['days_31_60_ksh'] += $outstanding;
                        $analysis['days_31_60_count']++;
                    } elseif ($daysOverdue <= 60) {
                        $analysis['days_61_90_ksh'] += $outstanding;
                        $analysis['days_61_90_count']++;
                    } else {
                        $analysis['days_over_90_ksh'] += $outstanding;
                        $analysis['days_over_90_count']++;
                    }
                }
            }

            return $analysis;
        } catch (\Exception $e) {
            Log::error('Error getting aging analysis: ' . $e->getMessage());
            return $this->getEmptyAgingAnalysis();
        }
    }

    private function getTopDebtors($currency = 'all')
    {
        try {
            $query = ConsolidatedBilling::with('user')
                ->whereIn('status', ['pending', 'sent', 'overdue', 'partial']);

            if ($currency === 'USD') {
                $query->where('currency', 'USD');
            } elseif ($currency === 'KSH') {
                $query->where('currency', 'KSH');
            }

            $invoices = $query->get();
            $debtors = [];

            foreach ($invoices as $invoice) {
                $userId = $invoice->user_id;
                $outstanding = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);
                if ($outstanding <= 0) continue;

                if (!isset($debtors[$userId])) {
                    $debtors[$userId] = [
                        'id' => $userId,
                        'name' => $invoice->user->name ?? 'Unknown',
                        'email' => $invoice->user->email ?? '',
                        'outstanding_usd' => 0,
                        'outstanding_ksh' => 0,
                        'overdue_invoices' => 0,
                        'risk_level' => 'low',
                    ];
                }

                if ($invoice->currency === 'USD') {
                    $debtors[$userId]['outstanding_usd'] += $outstanding;
                } else {
                    $debtors[$userId]['outstanding_ksh'] += $outstanding;
                }

                if (Carbon::parse($invoice->due_date)->lt(now())) {
                    $debtors[$userId]['overdue_invoices']++;
                }
            }

            foreach ($debtors as &$debtor) {
                $totalOutstanding = $debtor['outstanding_usd'] + ($debtor['outstanding_ksh'] / 130);
                if ($totalOutstanding > 50000 || $debtor['overdue_invoices'] > 5) {
                    $debtor['risk_level'] = 'critical';
                } elseif ($totalOutstanding > 10000 || $debtor['overdue_invoices'] > 2) {
                    $debtor['risk_level'] = 'high';
                } elseif ($totalOutstanding > 5000) {
                    $debtor['risk_level'] = 'medium';
                }
            }

            usort($debtors, function($a, $b) {
                $totalA = $a['outstanding_usd'] + ($a['outstanding_ksh'] / 130);
                $totalB = $b['outstanding_usd'] + ($b['outstanding_ksh'] / 130);
                return $totalB <=> $totalA;
            });

            return array_slice($debtors, 0, 5);
        } catch (\Exception $e) {
            Log::error('Error getting top debtors: ' . $e->getMessage());
            return [];
        }
    }

    private function generateAIInsights($metrics, $agingAnalysis, $topDebtors)
    {
        $totalOutstanding = $metrics['total_outstanding_usd'] + ($metrics['total_outstanding_ksh'] / 130);
        $overdueAmount = $metrics['overdue_amount_usd'] + ($metrics['overdue_amount_ksh'] / 130);
        $overduePercentage = $totalOutstanding > 0 ? ($overdueAmount / $totalOutstanding) * 100 : 0;
        $collectionRate = ($metrics['collection_rate_usd'] + $metrics['collection_rate_ksh']) / 2;

        $alerts = [];
        $keyFindings = [];
        $riskAnalysis = [];
        $recommendations = [];

        $keyFindings[] = "Total outstanding debt is $" . number_format($totalOutstanding, 2);
        $keyFindings[] = "Overdue amount represents " . number_format($overduePercentage, 1) . "% of total debt";

        $totalInvoices = $agingAnalysis['current_count'] + $agingAnalysis['days_31_60_count'] +
                        $agingAnalysis['days_61_90_count'] + $agingAnalysis['days_over_90_count'];
        $avgDelay = $totalInvoices > 0 ? ($agingAnalysis['days_31_60_count'] * 45 + $agingAnalysis['days_61_90_count'] * 75 + $agingAnalysis['days_over_90_count'] * 120) / $totalInvoices : 0;
        $keyFindings[] = "Average collection delay is " . number_format($avgDelay, 1) . " days";
        $keyFindings[] = "Collection success rate is " . number_format($collectionRate, 1) . "%";

        if (count($topDebtors) > 0) {
            $alerts[] = "Monitor top " . count($topDebtors) . " debtors closely";
        }
        if ($overduePercentage > 30) {
            $alerts[] = "Overdue rate exceeds 30% - immediate action required";
        }
        if ($collectionRate < 70) {
            $alerts[] = "Collection rate below 70% - review collection strategy";
        }

        if (empty($alerts)) {
            $alerts[] = "All metrics are within acceptable ranges";
        }

        if (count($topDebtors) > 0) {
            $riskAnalysis[] = "High concentration in top " . count($topDebtors) . " debtors";
        }
        if (($agingAnalysis['days_over_90_usd'] + $agingAnalysis['days_over_90_ksh']) > 0) {
            $riskAnalysis[] = "Aging debt over 90 days needs immediate attention";
        }
        if ($collectionRate < 85) {
            $riskAnalysis[] = "Collection rate below optimal target of 85%";
        }

        if (empty($riskAnalysis)) {
            $riskAnalysis[] = "No major risks detected at this time";
        }

        $recommendations[] = "Implement automated payment reminders for overdue accounts";
        $recommendations[] = "Offer payment plans for debts over $5,000";
        $recommendations[] = "Prioritize collection efforts on debts over 60 days";
        $recommendations[] = "Review and update credit policies for repeat offenders";

        if ($overduePercentage > 30) {
            $recommendations[] = "Escalate collection efforts for severely overdue accounts";
        }

        return [
            'alerts' => $alerts,
            'key_findings' => $keyFindings,
            'risk_analysis' => $riskAnalysis,
            'recommendations' => $recommendations,
        ];
    }

    private function getCollectionTrends($currency = 'all')
    {
        try {
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(30);

            $dates = [];
            $amountsUsd = [];
            $amountsKsh = [];
            $counts = [];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                $dates[] = $date->format('M d');

                $amountsUsd[] = Transaction::where('type', 'income')
                    ->where('currency', 'USD')
                    ->whereDate('transaction_date', $dateStr)
                    ->sum('amount');

                $amountsKsh[] = Transaction::where('type', 'income')
                    ->where('currency', 'KSH')
                    ->whereDate('transaction_date', $dateStr)
                    ->sum('amount');

                $counts[] = Transaction::where('type', 'income')
                    ->whereDate('transaction_date', $dateStr)
                    ->count();
            }

            $totalUsd = array_sum($amountsUsd);
            $totalKsh = array_sum($amountsKsh);
            $averageDailyUsd = count($amountsUsd) > 0 ? $totalUsd / count($amountsUsd) : 0;
            $averageDailyKsh = count($amountsKsh) > 0 ? $totalKsh / count($amountsKsh) : 0;

            $recentAvg = count($amountsUsd) >= 7 ? array_sum(array_slice($amountsUsd, -7)) / 7 : 0;
            $previousAvg = count($amountsUsd) >= 14 ? array_sum(array_slice($amountsUsd, -14, 7)) / 7 : 0;
            $trendDirection = $recentAvg > $previousAvg ? 'up' : ($recentAvg < $previousAvg ? 'down' : 'stable');
            $trendPercentage = $previousAvg > 0 ? (($recentAvg - $previousAvg) / $previousAvg) * 100 : 0;

            return [
                'labels' => $dates,
                'amounts_usd' => $amountsUsd,
                'amounts_ksh' => $amountsKsh,
                'counts' => $counts,
                'total_collected_usd' => $totalUsd,
                'total_collected_ksh' => $totalKsh,
                'average_daily_usd' => $averageDailyUsd,
                'average_daily_ksh' => $averageDailyKsh,
                'trend' => [
                    'direction' => $trendDirection,
                    'percentage' => round(abs($trendPercentage), 1),
                    'message' => $totalUsd == 0 ? 'Insufficient data' : ($trendDirection == 'up' ? 'Increasing trend' : ($trendDirection == 'down' ? 'Decreasing trend' : 'Stable trend'))
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting collection trends: ' . $e->getMessage());
            return $this->getEmptyCollectionTrends();
        }
    }

    // Empty data helpers
    private function getEmptyMetrics()
    {
        return [
            'total_outstanding_usd' => 0, 'total_outstanding_ksh' => 0,
            'overdue_amount_usd' => 0, 'overdue_amount_ksh' => 0,
            'overdue_count_usd' => 0, 'overdue_count_ksh' => 0,
            'overdue_percentage_usd' => 0, 'overdue_percentage_ksh' => 0,
            'collection_rate_usd' => 0, 'collection_rate_ksh' => 0,
            'today_collections_usd' => 0, 'today_collections_ksh' => 0,
        ];
    }

    private function getEmptyAgingAnalysis()
    {
        return [
            'current_usd' => 0, 'current_ksh' => 0, 'current_count' => 0,
            'days_31_60_usd' => 0, 'days_31_60_ksh' => 0, 'days_31_60_count' => 0,
            'days_61_90_usd' => 0, 'days_61_90_ksh' => 0, 'days_61_90_count' => 0,
            'days_over_90_usd' => 0, 'days_over_90_ksh' => 0, 'days_over_90_count' => 0,
            'total_usd' => 0, 'total_ksh' => 0,
        ];
    }

    private function getEmptyInsights()
    {
        return [
            'alerts' => ['No data available for the selected period'],
            'key_findings' => ['No data available for the selected period'],
            'risk_analysis' => ['Unable to analyze risk due to insufficient data'],
            'recommendations' => ['Please ensure there is billing data to generate recommendations'],
        ];
    }

    private function getEmptyCollectionTrends()
    {
        return [
            'labels' => [], 'amounts_usd' => [], 'amounts_ksh' => [], 'counts' => [],
            'total_collected_usd' => 0, 'total_collected_ksh' => 0,
            'average_daily_usd' => 0, 'average_daily_ksh' => 0,
            'trend' => ['direction' => 'stable', 'percentage' => 0, 'message' => 'No data available']
        ];
    }
}
