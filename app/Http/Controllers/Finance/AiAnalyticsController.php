<?php
// app/Http\Controllers/Finance/AiAnalyticsController.php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\AiAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiAnalyticsController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->aiService = new AiAnalyticsService();
    }

    /**
     * Display AI-powered analytics dashboard
     */
    public function dashboard()
    {
        // Get AI insights
        $insights = $this->aiService->getDebtInsights('30d');

        // Get real-time metrics
        $metrics = $this->getRealTimeMetrics();

        // Get top debtors
        $topDebtors = $this->getTopDebtors(5);

        // Get aging analysis
        $agingAnalysis = $this->getAgingAnalysisData();

        // Get collection trends
        $collectionTrends = $this->getCollectionTrends();

        return view('finance.ai-analytics.dashboard', compact(
            'insights', 'metrics', 'topDebtors', 'agingAnalysis', 'collectionTrends'
        ));
    }

    /**
     * Get real-time metrics
     */
    private function getRealTimeMetrics()
    {
        try {
            $result = DB::table('consolidated_billings')
                ->select([
                    DB::raw('COALESCE(SUM(total_amount - paid_amount), 0) as total_outstanding'),
                    DB::raw('COALESCE(SUM(CASE WHEN due_date < NOW() AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as overdue_amount'),
                    DB::raw('COALESCE(COUNT(CASE WHEN due_date < NOW() AND total_amount > paid_amount THEN 1 END), 0) as overdue_count'),
                    DB::raw('COALESCE((SUM(paid_amount) / NULLIF(SUM(total_amount), 0)) * 100, 0) as collection_rate'),
                    DB::raw('COALESCE(SUM(CASE WHEN status = "paid" AND DATE(payment_date) = CURDATE() THEN paid_amount ELSE 0 END), 0) as today_collections')
                ])
                ->first();

            return [
                'total_outstanding' => floatval($result->total_outstanding ?? 0),
                'overdue_amount' => floatval($result->overdue_amount ?? 0),
                'overdue_count' => intval($result->overdue_count ?? 0),
                'collection_rate' => floatval($result->collection_rate ?? 0),
                'today_collections' => floatval($result->today_collections ?? 0),
                'overdue_percentage' => $result->total_outstanding > 0 ?
                    round(($result->overdue_amount / $result->total_outstanding) * 100, 1) : 0
            ];

        } catch (\Exception $e) {
            return [
                'total_outstanding' => 0,
                'overdue_amount' => 0,
                'overdue_count' => 0,
                'collection_rate' => 0,
                'today_collections' => 0,
                'overdue_percentage' => 0
            ];
        }
    }

    /**
     * Get top debtors
     */
    private function getTopDebtors($limit = 5)
    {
        try {
            return DB::table('consolidated_billings as cb')
                ->join('users as u', 'cb.user_id', '=', 'u.id')
                ->select([
                    'u.id',
                    'u.name',
                    'u.email',
                    DB::raw('COALESCE(SUM(cb.total_amount - cb.paid_amount), 0) as outstanding'),
                    DB::raw('COUNT(CASE WHEN cb.due_date < NOW() AND cb.total_amount > cb.paid_amount THEN 1 END) as overdue_invoices'),
                    DB::raw('MAX(DATEDIFF(NOW(), cb.due_date)) as max_days_overdue')
                ])
                ->whereRaw('cb.total_amount > cb.paid_amount')
                ->groupBy('u.id', 'u.name', 'u.email')
                ->orderByDesc('outstanding')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'email' => $item->email,
                        'outstanding' => floatval($item->outstanding),
                        'overdue_invoices' => intval($item->overdue_invoices),
                        'max_days_overdue' => intval($item->max_days_overdue),
                        'risk_level' => $this->determineRiskLevel($item->max_days_overdue, $item->outstanding)
                    ];
                });

        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Determine risk level
     */
    private function determineRiskLevel($daysOverdue, $amount)
    {
        if ($daysOverdue > 90 || $amount > 10000) return 'critical';
        if ($daysOverdue > 60 || $amount > 5000) return 'high';
        if ($daysOverdue > 30 || $amount > 1000) return 'medium';
        return 'low';
    }

    /**
     * Get aging analysis data
     */
    private function getAgingAnalysisData()
    {
        try {
            $result = DB::table('consolidated_billings')
                ->select([
                    DB::raw('COALESCE(SUM(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 0 AND 30 AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as current'),
                    DB::raw('COALESCE(SUM(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 31 AND 60 AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as days_31_60'),
                    DB::raw('COALESCE(SUM(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 61 AND 90 AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as days_61_90'),
                    DB::raw('COALESCE(SUM(CASE WHEN DATEDIFF(NOW(), due_date) > 90 AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as days_over_90'),
                    DB::raw('COALESCE(COUNT(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 0 AND 30 AND total_amount > paid_amount THEN 1 END), 0) as current_count'),
                    DB::raw('COALESCE(COUNT(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 31 AND 60 AND total_amount > paid_amount THEN 1 END), 0) as days_31_60_count'),
                    DB::raw('COALESCE(COUNT(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 61 AND 90 AND total_amount > paid_amount THEN 1 END), 0) as days_61_90_count'),
                    DB::raw('COALESCE(COUNT(CASE WHEN DATEDIFF(NOW(), due_date) > 90 AND total_amount > paid_amount THEN 1 END), 0) as days_over_90_count')
                ])
                ->whereRaw('total_amount > paid_amount')
                ->first();

            return [
                'current' => floatval($result->current ?? 0),
                'days_31_60' => floatval($result->days_31_60 ?? 0),
                'days_61_90' => floatval($result->days_61_90 ?? 0),
                'days_over_90' => floatval($result->days_over_90 ?? 0),
                'current_count' => intval($result->current_count ?? 0),
                'days_31_60_count' => intval($result->days_31_60_count ?? 0),
                'days_61_90_count' => intval($result->days_61_90_count ?? 0),
                'days_over_90_count' => intval($result->days_over_90_count ?? 0),
                'total' => floatval(($result->current ?? 0) + ($result->days_31_60 ?? 0) + ($result->days_61_90 ?? 0) + ($result->days_over_90 ?? 0))
            ];

        } catch (\Exception $e) {
            return $this->getDefaultAgingAnalysis();
        }
    }

    /**
     * Default aging analysis
     */
    private function getDefaultAgingAnalysis()
    {
        return [
            'current' => 0, 'days_31_60' => 0, 'days_61_90' => 0, 'days_over_90' => 0,
            'current_count' => 0, 'days_31_60_count' => 0, 'days_61_90_count' => 0, 'days_over_90_count' => 0,
            'total' => 0
        ];
    }

       /**
     * Get customer intelligence
     */
    public function customerIntelligence($id)
    {
        $customer = \App\Models\User::with(['consolidatedBillings' => function($query) {
            $query->orderBy('due_date', 'desc')->limit(10);
        }])->findOrFail($id);

        // Get AI predictions
        $paymentProbability = $this->aiService->predictPaymentProbability($id);

        // Get collection strategy
        $collectionStrategy = $this->aiService->generateCollectionStrategy($id);

        // Get customer metrics
        $customerMetrics = $this->getCustomerMetrics($id);

        return view('finance.ai-analytics.customer', compact(
            'customer', 'paymentProbability', 'collectionStrategy', 'customerMetrics'
        ));
    }

    /**
     * Get customer metrics
     */
    private function getCustomerMetrics($customerId)
    {
        try {
            $result = DB::table('consolidated_billings')
                ->where('user_id', $customerId)
                ->select([
                    DB::raw('COALESCE(SUM(total_amount - paid_amount), 0) as total_outstanding'),
                    DB::raw('COALESCE(SUM(CASE WHEN due_date < NOW() AND total_amount > paid_amount THEN total_amount - paid_amount ELSE 0 END), 0) as overdue_amount'),
                    DB::raw('COALESCE(COUNT(CASE WHEN due_date < NOW() AND total_amount > paid_amount THEN 1 END), 0) as overdue_count'),
                    DB::raw('COALESCE(COUNT(*), 0) as total_invoices'),
                    DB::raw('COALESCE(SUM(paid_amount), 0) as total_paid'),
                    DB::raw('COALESCE(MAX(DATEDIFF(NOW(), due_date)), 0) as max_days_overdue'),
                    DB::raw('COALESCE(AVG(DATEDIFF(NOW(), due_date)), 0) as avg_days_overdue')
                ])
                ->first();

            return [
                'total_outstanding' => floatval($result->total_outstanding ?? 0),
                'overdue_amount' => floatval($result->overdue_amount ?? 0),
                'overdue_count' => intval($result->overdue_count ?? 0),
                'total_invoices' => intval($result->total_invoices ?? 0),
                'total_paid' => floatval($result->total_paid ?? 0),
                'max_days_overdue' => intval($result->max_days_overdue ?? 0),
                'avg_days_overdue' => floatval($result->avg_days_overdue ?? 0),
                'payment_rate' => $result->total_invoices > 0 ?
                    round(($result->total_paid / ($result->total_paid + $result->total_outstanding)) * 100, 1) : 0
            ];

        } catch (\Exception $e) {
            return [
                'total_outstanding' => 0,
                'overdue_amount' => 0,
                'overdue_count' => 0,
                'total_invoices' => 0,
                'total_paid' => 0,
                'max_days_overdue' => 0,
                'avg_days_overdue' => 0,
                'payment_rate' => 0
            ];
        }
    }

    /**
     * Generate AI report
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'period' => 'in:7d,30d,90d,1y',
            'format' => 'in:html,json,pdf'
        ]);

        $period = $validated['period'] ?? '30d';
        $format = $validated['format'] ?? 'html';

        // Get comprehensive data
        $insights = $this->aiService->getDebtInsights($period);
        $metrics = $this->getRealTimeMetrics();
        $topDebtors = $this->getTopDebtors(10);
        $agingAnalysis = $this->getAgingAnalysisData();
        $collectionTrends = $this->getCollectionTrends();

        $reportData = [
            'period' => $period,
            'generated_at' => now()->toDateTimeString(),
            'insights' => $insights,
            'metrics' => $metrics,
            'top_debtors' => $topDebtors,
            'aging_analysis' => $agingAnalysis,
            'collection_trends' => $collectionTrends,
            'summary' => $this->generateReportSummary($insights, $metrics)
        ];

        if ($format === 'json') {
            return response()->json($reportData);
        }

        if ($format === 'pdf') {
            // You can implement PDF generation here
            return response()->json(['message' => 'PDF export coming soon'], 200);
        }

        return view('finance.ai-analytics.report', compact('reportData'));
    }

    /**
     * Generate report summary
     */
    private function generateReportSummary($insights, $metrics)
    {
        return [
            'executive_summary' => "Total outstanding debt: $" . number_format($metrics['total_outstanding'], 2) .
                " with " . $metrics['overdue_percentage'] . "% overdue. Collection rate: " .
                number_format($metrics['collection_rate'], 1) . "%.",
            'key_issues' => isset($insights['risk_analysis']) ? $insights['risk_analysis'] : [],
            'priority_actions' => isset($insights['recommendations']) ?
                array_slice($insights['recommendations'], 0, 3) : []
        ];
    }

// Add this method to your controller
private function calculateTrend($data)
{
    if (count($data) < 2) {
        return ['direction' => 'stable', 'percentage' => 0, 'message' => 'Insufficient data'];
    }

    $firstHalf = array_slice($data, 0, floor(count($data) / 2));
    $secondHalf = array_slice($data, floor(count($data) / 2));

    $firstAvg = array_sum($firstHalf) / count($firstHalf);
    $secondAvg = array_sum($secondHalf) / count($secondHalf);

    if ($firstAvg == 0) {
        return ['direction' => 'up', 'percentage' => 100, 'message' => 'Starting from zero'];
    }

    $percentage = round((($secondAvg - $firstAvg) / $firstAvg) * 100, 1);

    return [
        'direction' => $percentage >= 0 ? 'up' : 'down',
        'percentage' => abs($percentage),
        'message' => $percentage >= 0 ? 'Improving trend' : 'Declining trend'
    ];
}

// Update your getCollectionTrends method to include trend calculation:
private function getCollectionTrends()
{
    try {
        $trends = DB::table('consolidated_billings')
            ->select([
                DB::raw('DATE(payment_date) as date'),
                DB::raw('COALESCE(SUM(paid_amount), 0) as amount'),
                DB::raw('COUNT(*) as count')
            ])
            ->where('status', 'paid')
            ->where('payment_date', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('payment_date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $amounts = $trends->pluck('amount')->toArray();

        return [
            'labels' => $trends->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'amounts' => $amounts,
            'counts' => $trends->pluck('count')->toArray(),
            'total_collected' => $trends->sum('amount'),
            'average_daily' => $trends->avg('amount') ?? 0,
            'trend' => $this->calculateTrend($amounts) // Add trend here
        ];

    } catch (\Exception $e) {
        return [
            'labels' => [],
            'amounts' => [],
            'counts' => [],
            'total_collected' => 0,
            'average_daily' => 0,
            'trend' => ['direction' => 'stable', 'percentage' => 0, 'message' => 'No data']
        ];
    }
}
}
