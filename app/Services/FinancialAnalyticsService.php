<?php
// app/Services/FinancialAnalyticsService.php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialAnalyticsService
{
    private const CACHE_TTL = 300; // 5 minutes
    private const VALID_PERIODS = ['7d', '30d', '90d', '1y', 'qtd', 'ytd'];

    // Default cost percentages (adjust based on your business)
    private const DEFAULT_COST_PERCENTAGES = [
        'cost_of_goods' => 0.35,  // 35% cost of goods sold
        'operating_costs' => 0.25, // 25% operating expenses
        'tax_rate' => 0.20,        // 20% tax rate
    ];

    public function __construct()
    {
        // Service can be extended with AI service injection later
    }

    /**
     * Generate comprehensive financial analytics report
     */
    public function generateFinancialReport(string $period = '30d', bool $includePredictions = false): array
    {
        $this->validatePeriod($period);

        $cacheKey = "financial_report_{$period}_" . ($includePredictions ? 'with_predictions' : 'basic') . '_' . date('Ymd');

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($period, $includePredictions) {
            return $this->buildFinancialReport($period, $includePredictions);
        });
    }

    /**
     * Build the financial report
     */
    private function buildFinancialReport(string $period, bool $includePredictions): array
    {
        try {
            $startDate = $this->getStartDate($period);
            $endDate = Carbon::now();

            $report = [
                'period' => $this->getPeriodInfo($period, $startDate, $endDate),
                'executive_summary' => $this->generateExecutiveSummary($startDate, $endDate),
                'financial_metrics' => $this->getFinancialMetrics($startDate, $endDate),
                'trend_analysis' => $this->analyzeTrends($startDate, $endDate),
                'risk_assessment' => $this->assessRisks($startDate, $endDate),
                'recommendations' => $this->generateRecommendations($startDate, $endDate),
                'metadata' => [
                    'generated_at' => now()->toDateTimeString(),
                    'period_days' => $endDate->diffInDays($startDate),
                    'data_source' => 'consolidated_billings',
                    'predictions_included' => $includePredictions,
                    'cache_expires' => now()->addSeconds(self::CACHE_TTL)->toDateTimeString()
                ]
            ];

            if ($includePredictions) {
                $report['predictions'] = $this->generatePredictions($startDate, $endDate, $report);
            }

            return $report;

        } catch (\Exception $e) {
            Log::error('Financial report generation failed', [
                'period' => $period,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Get comprehensive financial metrics
     */
    public function getFinancialMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = "financial_metrics_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($startDate, $endDate) {
            $revenueData = $this->fetchRevenueData($startDate, $endDate);
            $costData = $this->estimateCosts($revenueData['total_revenue']);

            return [
                'revenue_metrics' => $this->calculateRevenueMetrics($revenueData, $startDate, $endDate),
                'cost_metrics' => $costData,
                'profitability_metrics' => $this->calculateProfitabilityMetrics($revenueData, $costData),
                'liquidity_metrics' => $this->calculateLiquidityMetrics($startDate, $endDate, $revenueData),
                'efficiency_metrics' => $this->calculateEfficiencyMetrics($startDate, $endDate, $revenueData),
                'growth_metrics' => $this->calculateGrowthMetrics($startDate, $endDate, $revenueData)
            ];
        });
    }

    /**
     * Fetch revenue data with optimized queries
     */
    private function fetchRevenueData(Carbon $startDate, Carbon $endDate): array
    {
        $revenue = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->select([
                DB::raw('COALESCE(SUM(total_amount), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(paid_amount), 0) as collected_revenue'),
                DB::raw('COUNT(DISTINCT user_id) as active_customers'),
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_invoices'),
                DB::raw('COUNT(CASE WHEN status IN ("pending", "sent", "overdue") AND total_amount > paid_amount THEN 1 END) as pending_invoices'),
                DB::raw('AVG(total_amount) as average_invoice_value'),
                DB::raw('COALESCE(AVG(CASE WHEN status = "paid" THEN DATEDIFF(payment_date, billing_date) END), 0) as avg_collection_days')
            ])
            ->first();

        $previousPeriod = $this->getPreviousPeriod($startDate, $endDate);
        $previousRevenue = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$previousPeriod['start'], $previousPeriod['end']])
            ->sum('total_amount');

        return [
            'total_revenue' => (float) ($revenue->total_revenue ?? 0),
            'collected_revenue' => (float) ($revenue->collected_revenue ?? 0),
            'active_customers' => (int) ($revenue->active_customers ?? 0),
            'total_invoices' => (int) ($revenue->total_invoices ?? 0),
            'paid_invoices' => (int) ($revenue->paid_invoices ?? 0),
            'pending_invoices' => (int) ($revenue->pending_invoices ?? 0),
            'average_invoice_value' => (float) ($revenue->average_invoice_value ?? 0),
            'avg_collection_days' => (float) ($revenue->avg_collection_days ?? 0),
            'previous_period_revenue' => (float) $previousRevenue,
            'outstanding_revenue' => (float) (($revenue->total_revenue ?? 0) - ($revenue->collected_revenue ?? 0)),
            'collection_rate' => ($revenue->total_revenue ?? 0) > 0
                ? (($revenue->collected_revenue ?? 0) / ($revenue->total_revenue ?? 0)) * 100
                : 0
        ];
    }

    /**
     * Estimate costs based on revenue (fallback when cost table doesn't exist)
     */
    private function estimateCosts(float $totalRevenue): array
    {
        $costOfGoods = $totalRevenue * self::DEFAULT_COST_PERCENTAGES['cost_of_goods'];
        $operatingCosts = $totalRevenue * self::DEFAULT_COST_PERCENTAGES['operating_costs'];
        $totalCosts = $costOfGoods + $operatingCosts;
        $taxAmount = $totalCosts * self::DEFAULT_COST_PERCENTAGES['tax_rate'];

        return [
            'cost_of_goods' => $costOfGoods,
            'operating_costs' => $operatingCosts,
            'total_costs' => $totalCosts,
            'tax_amount' => $taxAmount,
            'is_estimated' => true,
            'estimation_notes' => 'Costs estimated based on standard percentages. Update with actual cost data when available.'
        ];
    }

    /**
     * Calculate revenue metrics
     */
    private function calculateRevenueMetrics(array $revenueData, Carbon $startDate, Carbon $endDate): array
    {
        $revenueGrowth = $revenueData['previous_period_revenue'] > 0
            ? (($revenueData['total_revenue'] - $revenueData['previous_period_revenue']) / $revenueData['previous_period_revenue']) * 100
            : ($revenueData['total_revenue'] > 0 ? 100 : 0);

        return [
            'total_revenue' => $revenueData['total_revenue'],
            'collected_revenue' => $revenueData['collected_revenue'],
            'outstanding_revenue' => $revenueData['outstanding_revenue'],
            'collection_rate' => round($revenueData['collection_rate'], 2),
            'active_customers' => $revenueData['active_customers'],
            'total_invoices' => $revenueData['total_invoices'],
            'paid_invoices' => $revenueData['paid_invoices'],
            'pending_invoices' => $revenueData['pending_invoices'],
            'average_invoice_value' => round($revenueData['average_invoice_value'], 2),
            'revenue_growth' => round($revenueGrowth, 2),
            'avg_collection_days' => round($revenueData['avg_collection_days'], 1),
            'ar_turnover' => $this->calculateARTurnover($startDate, $endDate)
        ];
    }

    /**
     * Calculate profitability metrics
     */
    private function calculateProfitabilityMetrics(array $revenueData, array $costData): array
    {
        $grossProfit = $revenueData['collected_revenue'] - $costData['cost_of_goods'];
        $netProfit = $grossProfit - $costData['operating_costs'] - $costData['tax_amount'];

        $grossMargin = $revenueData['collected_revenue'] > 0
            ? ($grossProfit / $revenueData['collected_revenue']) * 100
            : 0;

        $netMargin = $revenueData['collected_revenue'] > 0
            ? ($netProfit / $revenueData['collected_revenue']) * 100
            : 0;

        $roi = $costData['total_costs'] > 0
            ? ($netProfit / $costData['total_costs']) * 100
            : 0;

        return [
            'gross_profit' => round($grossProfit, 2),
            'net_profit' => round($netProfit, 2),
            'gross_margin' => round($grossMargin, 2),
            'net_margin' => round($netMargin, 2),
            'roi' => round($roi, 2),
            'break_even_point' => $this->calculateBreakEvenPoint($costData['total_costs'], $revenueData['average_invoice_value']),
            'profit_per_customer' => $revenueData['active_customers'] > 0
                ? round($netProfit / $revenueData['active_customers'], 2)
                : 0,
            'profit_per_invoice' => $revenueData['total_invoices'] > 0
                ? round($netProfit / $revenueData['total_invoices'], 2)
                : 0
        ];
    }

    /**
     * Calculate liquidity metrics
     */
    private function calculateLiquidityMetrics(Carbon $startDate, Carbon $endDate, array $revenueData): array
    {
        $outstanding = DB::table('consolidated_billings')
            ->whereRaw('total_amount > paid_amount')
            ->sum(DB::raw('total_amount - paid_amount'));

        $collections = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('paid_amount');

        // Estimate current assets and liabilities
        $currentAssets = $collections * 0.8; // 80% of collections are current assets
        $currentLiabilities = $outstanding * 0.6 + ($revenueData['total_revenue'] * 0.1); // 60% of outstanding + 10% of revenue as other liabilities

        $currentRatio = $currentLiabilities > 0
            ? $currentAssets / $currentLiabilities
            : 0;

        $quickAssets = $collections * 0.6; // 60% of collections are quick assets
        $quickRatio = $currentLiabilities > 0
            ? $quickAssets / $currentLiabilities
            : 0;

        $workingCapital = $currentAssets - $currentLiabilities;

        return [
            'current_ratio' => round($currentRatio, 2),
            'quick_ratio' => round($quickRatio, 2),
            'working_capital' => round($workingCapital, 2),
            'cash_position' => round($collections * 0.5, 2), // 50% of collections as cash
            'outstanding_debt' => round($outstanding, 2),
            'days_sales_outstanding' => round($this->calculateDSO($startDate, $endDate), 1),
            'operating_cash_flow' => round($this->calculateOperatingCashFlow($startDate, $endDate), 2)
        ];
    }

    /**
     * Calculate efficiency metrics
     */
    private function calculateEfficiencyMetrics(Carbon $startDate, Carbon $endDate, array $revenueData): array
    {
        $collectionEfficiency = $revenueData['collection_rate'];
        $assetTurnover = $this->calculateAssetTurnover($startDate, $endDate);
        $employeeProductivity = $this->calculateEmployeeProductivity($startDate, $endDate);

        return [
            'collection_efficiency' => round($collectionEfficiency, 2),
            'avg_collection_days' => round($revenueData['avg_collection_days'], 1),
            'asset_turnover' => round($assetTurnover, 2),
            'employee_productivity' => round($employeeProductivity, 2),
            'invoice_processing_time' => round($this->calculateInvoiceProcessingTime($startDate, $endDate), 1),
            'customer_acquisition_cost' => 150, // Placeholder - should be calculated from marketing data
            'customer_lifetime_value' => round($this->calculateCLV($startDate, $endDate), 2),
            'operational_efficiency' => round($this->calculateOperationalEfficiency($startDate, $endDate), 2)
        ];
    }

    /**
     * Calculate growth metrics
     */
    private function calculateGrowthMetrics(Carbon $startDate, Carbon $endDate, array $revenueData): array
    {
        $previousPeriod = $this->getPreviousPeriod($startDate, $endDate);

        $previousCustomers = DB::table('users')
            ->where('role', 'customer')
            ->whereBetween('created_at', [$previousPeriod['start'], $previousPeriod['end']])
            ->count();

        $currentCustomers = $revenueData['active_customers'];

        $customerGrowth = $previousCustomers > 0
            ? (($currentCustomers - $previousCustomers) / $previousCustomers) * 100
            : ($currentCustomers > 0 ? 100 : 0);

        return [
            'revenue_growth' => round($revenueData['previous_period_revenue'] > 0
                ? (($revenueData['total_revenue'] - $revenueData['previous_period_revenue']) / $revenueData['previous_period_revenue']) * 100
                : 0, 2),
            'customer_growth' => round($customerGrowth, 2),
            'profit_growth' => $this->calculateProfitGrowth($startDate, $endDate),
            'market_share_growth' => 0, // Placeholder - requires market data
            'employee_growth' => 0, // Placeholder - requires HR data
            'mrr_growth' => $this->calculateMRRGrowth($startDate, $endDate)
        ];
    }

    /**
     * Generate executive summary
     */
    private function generateExecutiveSummary(Carbon $startDate, Carbon $endDate): array
    {
        $metrics = $this->getFinancialMetrics($startDate, $endDate);

        return [
            'overview' => $this->createOverviewStatement($metrics),
            'key_achievements' => $this->identifyAchievements($metrics),
            'major_challenges' => $this->identifyChallenges($metrics),
            'financial_health' => $this->assessFinancialHealth($metrics),
            'period_highlights' => $this->getPeriodHighlights($startDate, $endDate),
            'next_steps' => $this->suggestNextSteps($metrics)
        ];
    }

    /**
     * Create overview statement
     */
    private function createOverviewStatement(array $metrics): string
    {
        $revenue = $metrics['revenue_metrics'];
        $profitability = $metrics['profitability_metrics'];
        $liquidity = $metrics['liquidity_metrics'];
        $efficiency = $metrics['efficiency_metrics'];

        $profitabilityLevel = $profitability['net_margin'] >= 20 ? 'excellent' :
                            ($profitability['net_margin'] >= 15 ? 'strong' :
                            ($profitability['net_margin'] >= 10 ? 'moderate' : 'weak'));

        $liquidityLevel = $liquidity['current_ratio'] >= 2.0 ? 'very strong' :
                         ($liquidity['current_ratio'] >= 1.5 ? 'adequate' :
                         ($liquidity['current_ratio'] >= 1.0 ? 'tight' : 'concerning'));

        $efficiencyLevel = $efficiency['collection_efficiency'] >= 90 ? 'high' :
                          ($efficiency['collection_efficiency'] >= 80 ? 'good' :
                          ($efficiency['collection_efficiency'] >= 70 ? 'acceptable' : 'needs improvement'));

        return sprintf(
            "The period shows %s financial performance with a net profit margin of %.1f%%. " .
            "Revenue collection stands at %.1f%% efficiency with $%s outstanding. " .
            "Liquidity position is %s (current ratio: %.2f) and operational efficiency is rated as %s. " .
            "Key focus areas include %s.",
            $profitabilityLevel,
            $profitability['net_margin'],
            $efficiency['collection_efficiency'],
            number_format($revenue['outstanding_revenue']),
            $liquidityLevel,
            $liquidity['current_ratio'],
            $efficiencyLevel,
            $this->getFocusAreas($metrics)
        );
    }

    /**
     * Identify achievements
     */
    private function identifyAchievements(array $metrics): array
    {
        $achievements = [];

        if ($metrics['profitability_metrics']['net_margin'] >= 20) {
            $achievements[] = 'Excellent profitability with net margin above 20%';
        }

        if ($metrics['revenue_metrics']['collection_rate'] >= 90) {
            $achievements[] = 'Outstanding collection rate above 90%';
        }

        if ($metrics['growth_metrics']['revenue_growth'] >= 15) {
            $achievements[] = 'Strong revenue growth exceeding 15%';
        }

        if ($metrics['liquidity_metrics']['current_ratio'] >= 2.0) {
            $achievements[] = 'Robust liquidity position with current ratio above 2.0';
        }

        if ($metrics['efficiency_metrics']['avg_collection_days'] <= 30) {
            $achievements[] = 'Efficient collections with average days under 30';
        }

        return $achievements;
    }

    /**
     * Identify challenges
     */
    private function identifyChallenges(array $metrics): array
    {
        $challenges = [];

        if ($metrics['profitability_metrics']['net_margin'] < 10) {
            $challenges[] = 'Profit margins below target (10%)';
        }

        if ($metrics['revenue_metrics']['collection_rate'] < 80) {
            $challenges[] = 'Collection efficiency needs improvement (below 80%)';
        }

        if ($metrics['revenue_metrics']['revenue_growth'] < 5) {
            $challenges[] = 'Revenue growth slowing (below 5%)';
        }

        if ($metrics['liquidity_metrics']['current_ratio'] < 1.0) {
            $challenges[] = 'Liquidity concerns with current ratio below 1.0';
        }

        if ($metrics['revenue_metrics']['outstanding_revenue'] > $metrics['revenue_metrics']['collected_revenue'] * 0.3) {
            $challenges[] = 'High level of outstanding revenue (over 30% of collections)';
        }

        return $challenges;
    }

    /**
     * Assess financial health
     */
    private function assessFinancialHealth(array $metrics): string
    {
        $score = 0;
        $maxScore = 100;

        // Profitability (30 points)
        $netMargin = $metrics['profitability_metrics']['net_margin'];
        if ($netMargin >= 20) $score += 30;
        elseif ($netMargin >= 15) $score += 25;
        elseif ($netMargin >= 10) $score += 20;
        elseif ($netMargin >= 5) $score += 10;

        // Liquidity (25 points)
        $currentRatio = $metrics['liquidity_metrics']['current_ratio'];
        if ($currentRatio >= 2.0) $score += 25;
        elseif ($currentRatio >= 1.5) $score += 20;
        elseif ($currentRatio >= 1.0) $score += 15;
        elseif ($currentRatio >= 0.5) $score += 5;

        // Efficiency (25 points)
        $collectionRate = $metrics['revenue_metrics']['collection_rate'];
        if ($collectionRate >= 90) $score += 25;
        elseif ($collectionRate >= 80) $score += 20;
        elseif ($collectionRate >= 70) $score += 15;
        elseif ($collectionRate >= 60) $score += 10;

        // Growth (20 points)
        $revenueGrowth = $metrics['growth_metrics']['revenue_growth'];
        if ($revenueGrowth >= 20) $score += 20;
        elseif ($revenueGrowth >= 15) $score += 15;
        elseif ($revenueGrowth >= 10) $score += 10;
        elseif ($revenueGrowth >= 5) $score += 5;

        $percentage = ($score / $maxScore) * 100;

        if ($percentage >= 85) return 'Excellent';
        if ($percentage >= 70) return 'Good';
        if ($percentage >= 55) return 'Fair';
        return 'Needs Improvement';
    }

    /**
     * Get focus areas
     */
    private function getFocusAreas(array $metrics): string
    {
        $focusAreas = [];

        if ($metrics['revenue_metrics']['collection_rate'] < 85) {
            $focusAreas[] = 'improving collection efficiency';
        }

        if ($metrics['profitability_metrics']['net_margin'] < 15) {
            $focusAreas[] = 'enhancing profit margins';
        }

        if ($metrics['liquidity_metrics']['current_ratio'] < 1.5) {
            $focusAreas[] = 'strengthening liquidity';
        }

        if ($metrics['revenue_metrics']['outstanding_revenue'] > 0) {
            $focusAreas[] = 'reducing outstanding receivables';
        }

        return count($focusAreas) > 0
            ? implode(', ', $focusAreas)
            : 'maintaining current performance levels';
    }

    /**
     * Suggest next steps
     */
    private function suggestNextSteps(array $metrics): array
    {
        $steps = [];

        if ($metrics['revenue_metrics']['collection_rate'] < 85) {
            $steps[] = 'Implement automated payment reminder system';
        }

        if ($metrics['profitability_metrics']['net_margin'] < 15) {
            $steps[] = 'Conduct cost analysis to identify optimization opportunities';
        }

        if ($metrics['liquidity_metrics']['current_ratio'] < 1.5) {
            $steps[] = 'Review credit terms and payment cycles';
        }

        if ($metrics['revenue_metrics']['outstanding_revenue'] > 0) {
            $steps[] = 'Prioritize collection efforts on aged receivables';
        }

        if ($metrics['growth_metrics']['revenue_growth'] < 10) {
            $steps[] = 'Develop growth strategy for new customer acquisition';
        }

        return $steps;
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(Carbon $startDate, Carbon $endDate): array
    {
        $metrics = $this->getFinancialMetrics($startDate, $endDate);
        $recommendations = [];

        $this->addCollectionRecommendations($recommendations, $metrics);
        $this->addProfitabilityRecommendations($recommendations, $metrics);
        $this->addLiquidityRecommendations($recommendations, $metrics);
        $this->addGrowthRecommendations($recommendations, $metrics);
        $this->addEfficiencyRecommendations($recommendations, $metrics);

        // Sort by priority
        usort($recommendations, function($a, $b) {
            $priorityOrder = ['Critical' => 4, 'High' => 3, 'Medium' => 2, 'Low' => 1];
            return ($priorityOrder[$b['priority']] ?? 0) <=> ($priorityOrder[$a['priority']] ?? 0);
        });

        return $recommendations;
    }

    /**
     * Add collection recommendations
     */
    private function addCollectionRecommendations(array &$recommendations, array $metrics): void
    {
        $collectionRate = $metrics['revenue_metrics']['collection_rate'];
        $outstanding = $metrics['revenue_metrics']['outstanding_revenue'];

        if ($collectionRate < 80) {
            $recommendations[] = [
                'category' => 'Collections',
                'priority' => 'High',
                'title' => 'Improve Collection Efficiency',
                'description' => sprintf('Collection rate is %.1f%% with $%s outstanding',
                    $collectionRate, number_format($outstanding, 0)),
                'actions' => [
                    'Implement automated payment reminders',
                    'Offer multiple payment options',
                    'Establish clear payment terms'
                ],
                'expected_impact' => sprintf('Increase cash flow by $%s monthly',
                    number_format($outstanding * 0.2, 0)),
                'timeline' => '30 days',
                'owner' => 'Collections Team'
            ];
        }
    }

    /**
     * Add profitability recommendations
     */
    private function addProfitabilityRecommendations(array &$recommendations, array $metrics): void
    {
        $netMargin = $metrics['profitability_metrics']['net_margin'];

        if ($netMargin < 15) {
            $recommendations[] = [
                'category' => 'Profitability',
                'priority' => 'High',
                'title' => 'Enhance Profit Margins',
                'description' => sprintf('Net profit margin is %.1f%% below target of 15%%', 15 - $netMargin),
                'actions' => [
                    'Review and optimize cost structure',
                    'Consider value-based pricing adjustments',
                    'Implement cost control measures'
                ],
                'expected_impact' => sprintf('Increase annual profit by $%s',
                    number_format($metrics['revenue_metrics']['collected_revenue'] * 0.05, 0)),
                'timeline' => '60 days',
                'owner' => 'Finance Team'
            ];
        }
    }

    /**
     * Add liquidity recommendations
     */
    private function addLiquidityRecommendations(array &$recommendations, array $metrics): void
    {
        $currentRatio = $metrics['liquidity_metrics']['current_ratio'];

        if ($currentRatio < 1.5) {
            $recommendations[] = [
                'category' => 'Liquidity',
                'priority' => 'Medium',
                'title' => 'Strengthen Liquidity Position',
                'description' => sprintf('Current ratio of %.2f indicates potential liquidity constraints', $currentRatio),
                'actions' => [
                    'Accelerate collection of outstanding invoices',
                    'Negotiate extended payment terms with suppliers',
                    'Maintain adequate cash reserves'
                ],
                'expected_impact' => 'Improved financial stability and reduced risk',
                'timeline' => '45 days',
                'owner' => 'Finance Department'
            ];
        }
    }

    /**
     * Add growth recommendations
     */
    private function addGrowthRecommendations(array &$recommendations, array $metrics): void
    {
        $revenueGrowth = $metrics['growth_metrics']['revenue_growth'];

        if ($revenueGrowth < 10) {
            $recommendations[] = [
                'category' => 'Growth',
                'priority' => 'Medium',
                'title' => 'Accelerate Revenue Growth',
                'description' => sprintf('Revenue growth of %.1f%% is below target of 10%%', $revenueGrowth),
                'actions' => [
                    'Expand into new customer segments',
                    'Develop additional service offerings',
                    'Increase marketing efforts'
                ],
                'expected_impact' => '15-20% revenue growth in next quarter',
                'timeline' => '90 days',
                'owner' => 'Sales & Marketing'
            ];
        }
    }

    /**
     * Add efficiency recommendations
     */
    private function addEfficiencyRecommendations(array &$recommendations, array $metrics): void
    {
        $avgCollectionDays = $metrics['efficiency_metrics']['avg_collection_days'];

        if ($avgCollectionDays > 30) {
            $recommendations[] = [
                'category' => 'Efficiency',
                'priority' => 'Medium',
                'title' => 'Reduce Collection Cycle Time',
                'description' => sprintf('Average collection days is %.1f, target is under 30 days', $avgCollectionDays),
                'actions' => [
                    'Streamline invoicing process',
                    'Implement electronic payment options',
                    'Automate follow-up communications'
                ],
                'expected_impact' => 'Faster cash conversion and improved DSO',
                'timeline' => '30 days',
                'owner' => 'Operations Team'
            ];
        }
    }

    /**
     * Analyze trends
     */
    private function analyzeTrends(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'revenue_trend' => $this->getRevenueTrendData($startDate, $endDate),
            'profit_trend' => $this->getProfitTrendData($startDate, $endDate),
            'collection_trend' => $this->getCollectionTrendData($startDate, $endDate),
            'customer_trend' => $this->getCustomerTrendData($startDate, $endDate),
            'efficiency_trend' => $this->getEfficiencyTrendData($startDate, $endDate)
        ];
    }

    /**
     * Assess risks
     */
    private function assessRisks(Carbon $startDate, Carbon $endDate): array
    {
        $metrics = $this->getFinancialMetrics($startDate, $endDate);

        $risks = [];

        // Liquidity risk
        if ($metrics['liquidity_metrics']['current_ratio'] < 1.0) {
            $risks[] = [
                'type' => 'Liquidity Risk',
                'severity' => 'High',
                'probability' => 'Medium',
                'description' => 'Insufficient current assets to cover short-term liabilities',
                'mitigation' => 'Improve collections, reduce short-term debt'
            ];
        }

        // Collection risk
        if ($metrics['revenue_metrics']['collection_rate'] < 75) {
            $risks[] = [
                'type' => 'Collection Risk',
                'severity' => 'Medium',
                'probability' => 'High',
                'description' => 'Low collection efficiency impacting cash flow',
                'mitigation' => 'Implement stricter credit controls, improve collection process'
            ];
        }

        // Profitability risk
        if ($metrics['profitability_metrics']['net_margin'] < 10) {
            $risks[] = [
                'type' => 'Profitability Risk',
                'severity' => 'Medium',
                'probability' => 'Medium',
                'description' => 'Thin profit margins affecting sustainability',
                'mitigation' => 'Review pricing, optimize costs'
            ];
        }

        // Customer concentration risk
        $topCustomers = $this->getTopCustomers($startDate, $endDate, 3);
        $totalRevenue = $metrics['revenue_metrics']['total_revenue'];

        if (count($topCustomers) > 0) {
            $top3Revenue = array_sum(array_column($topCustomers, 'revenue'));
            $concentration = $totalRevenue > 0 ? ($top3Revenue / $totalRevenue) * 100 : 0;

            if ($concentration > 50) {
                $risks[] = [
                    'type' => 'Customer Concentration Risk',
                    'severity' => 'High',
                    'probability' => 'Low',
                    'description' => sprintf('Top 3 customers account for %.1f%% of revenue', $concentration),
                    'mitigation' => 'Diversify customer base, develop new markets'
                ];
            }
        }

        return $risks;
    }

    /**
     * Generate predictions
     */
    private function generatePredictions(Carbon $startDate, Carbon $endDate, array $report): array
    {
        $metrics = $report['financial_metrics'];

        return [
            'revenue_forecast' => [
                'next_30_days' => $this->forecastNext30DaysRevenue($metrics),
                'next_90_days' => $this->forecastNext90DaysRevenue($metrics),
                'confidence' => 'medium',
                'assumptions' => ['Current growth rate continues', 'No major market changes']
            ],
            'cashflow_forecast' => [
                'next_30_days' => $this->forecastNext30DaysCashflow($metrics),
                'next_90_days' => $this->forecastNext90DaysCashflow($metrics),
                'confidence' => 'medium',
                'assumptions' => ['Collection rate maintains', 'Cost structure remains stable']
            ],
            'risk_forecast' => [
                'high_probability' => $this->identifyHighProbabilityRisks($metrics),
                'emerging_risks' => $this->identifyEmergingRisks($metrics),
                'mitigation_strategies' => $this->suggestMitigationStrategies($metrics)
            ]
        ];
    }

    /**
     * Helper methods for calculations
     */
    private function validatePeriod(string $period): void
    {
        if (!in_array($period, self::VALID_PERIODS)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid period "%s". Valid periods are: %s',
                    $period,
                    implode(', ', self::VALID_PERIODS)
                )
            );
        }
    }

    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            '7d' => Carbon::now()->subDays(7)->startOfDay(),
            '30d' => Carbon::now()->subDays(30)->startOfDay(),
            '90d' => Carbon::now()->subDays(90)->startOfDay(),
            '1y' => Carbon::now()->subYear()->startOfDay(),
            'qtd' => Carbon::now()->startOfQuarter(),
            'ytd' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };
    }

    private function getPeriodInfo(string $period, Carbon $startDate, Carbon $endDate): array
    {
        return [
            'label' => $this->getPeriodLabel($period),
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'days' => $endDate->diffInDays($startDate),
            'months' => $endDate->diffInMonths($startDate)
        ];
    }

    private function getPeriodLabel(string $period): string
    {
        return match($period) {
            '7d' => 'Last 7 Days',
            '30d' => 'Last 30 Days',
            '90d' => 'Last 90 Days',
            '1y' => 'Last Year',
            'qtd' => 'Quarter to Date',
            'ytd' => 'Year to Date',
            default => 'Last 30 Days',
        };
    }

    private function getPreviousPeriod(Carbon $startDate, Carbon $endDate): array
    {
        $duration = $endDate->diffInDays($startDate);

        return [
            'start' => $startDate->copy()->subDays($duration + 1),
            'end' => $startDate->copy()->subDay()
        ];
    }

    /**
     * Financial calculation methods
     */
    private function calculateARTurnover(Carbon $startDate, Carbon $endDate): float
    {
        $avgReceivables = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->avg(DB::raw('GREATEST(total_amount - paid_amount, 0)'));

        $netCreditSales = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount');

        return $avgReceivables > 0 ? $netCreditSales / $avgReceivables : 0;
    }

    private function calculateDSO(Carbon $startDate, Carbon $endDate): float
    {
        $avgReceivables = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->avg(DB::raw('GREATEST(total_amount - paid_amount, 0)'));

        $totalCreditSales = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount');

        $daysInPeriod = max(1, $endDate->diffInDays($startDate));

        return $totalCreditSales > 0 ? ($avgReceivables / ($totalCreditSales / $daysInPeriod)) : 0;
    }

    private function calculateBreakEvenPoint(float $totalCosts, float $averageInvoiceValue): float
    {
        return $averageInvoiceValue > 0 ? ceil($totalCosts / $averageInvoiceValue) : 0;
    }

    private function calculateOperatingCashFlow(Carbon $startDate, Carbon $endDate): float
    {
        $cashIn = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('paid_amount');

        // Estimate cash out as 80% of cash in (simplified)
        return $cashIn * 0.2;
    }

    private function calculateAssetTurnover(Carbon $startDate, Carbon $endDate): float
    {
        $revenue = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Estimate total assets as 2x revenue (simplified)
        $estimatedAssets = $revenue * 2;

        return $estimatedAssets > 0 ? $revenue / $estimatedAssets : 0;
    }

    private function calculateEmployeeProductivity(Carbon $startDate, Carbon $endDate): float
    {
        $revenue = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Assume 5 employees (adjust based on your business)
        $employeeCount = 5;

        return $employeeCount > 0 ? $revenue / $employeeCount : 0;
    }

    private function calculateInvoiceProcessingTime(Carbon $startDate, Carbon $endDate): float
    {
        $avgDays = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->whereNotNull('payment_date')
            ->avg(DB::raw('DATEDIFF(payment_date, billing_date)'));

        return $avgDays ?? 0;
    }

    private function calculateCLV(Carbon $startDate, Carbon $endDate): float
    {
        $avgRevenuePerCustomer = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->select(DB::raw('AVG(total_amount) as avg_revenue'))
            ->groupBy('user_id')
            ->avg('total_amount');

        // Assume 3-year customer lifespan
        $customerLifespan = 36;

        return ($avgRevenuePerCustomer ?? 0) * $customerLifespan;
    }

    private function calculateOperationalEfficiency(Carbon $startDate, Carbon $endDate): float
    {
        // Simplified operational efficiency score
        $collectionRate = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->selectRaw('SUM(paid_amount) / NULLIF(SUM(total_amount), 0) * 100 as rate')
            ->value('rate') ?? 0;

        $avgCollectionDays = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->whereNotNull('payment_date')
            ->avg(DB::raw('DATEDIFF(payment_date, billing_date)')) ?? 0;

        // Score based on collection rate and speed
        $rateScore = min(100, $collectionRate);
        $speedScore = max(0, 100 - ($avgCollectionDays * 2)); // Deduct 2 points per day

        return ($rateScore + $speedScore) / 2;
    }

    private function calculateProfitGrowth(Carbon $startDate, Carbon $endDate): float
    {
        $currentProfit = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum(DB::raw('paid_amount * 0.15')); // Assume 15% profit

        $previousPeriod = $this->getPreviousPeriod($startDate, $endDate);
        $previousProfit = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$previousPeriod['start'], $previousPeriod['end']])
            ->sum(DB::raw('paid_amount * 0.15'));

        return $previousProfit > 0 ? (($currentProfit - $previousProfit) / $previousProfit) * 100 : 0;
    }

    private function calculateMRRGrowth(Carbon $startDate, Carbon $endDate): float
    {
        // Calculate Monthly Recurring Revenue growth
        $currentMRR = $this->calculateMRR($startDate, $endDate);

        $previousPeriod = $this->getPreviousPeriod($startDate, $endDate);
        $previousMRR = $this->calculateMRR($previousPeriod['start'], $previousPeriod['end']);

        return $previousMRR > 0 ? (($currentMRR - $previousMRR) / $previousMRR) * 100 : 0;
    }

    private function calculateMRR(Carbon $startDate, Carbon $endDate): float
    {
        // Estimate MRR based on average monthly revenue
        $totalRevenue = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount');

        $months = max(1, $endDate->diffInMonths($startDate));

        return $totalRevenue / $months;
    }

    /**
     * Trend data methods
     */
    private function getRevenueTrendData(Carbon $startDate, Carbon $endDate): array
    {
        $months = min(6, $endDate->diffInMonths($startDate));
        $trends = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = $endDate->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $endDate->copy()->subMonths($i)->endOfMonth();

            $revenue = DB::table('consolidated_billings')
                ->whereBetween('billing_date', [$monthStart, $monthEnd])
                ->sum('total_amount');

            $trends[] = [
                'period' => $monthStart->format('M Y'),
                'revenue' => $revenue,
                'growth' => $this->calculateMonthlyGrowth($trends, $revenue)
            ];
        }

        return $trends;
    }

    private function getProfitTrendData(Carbon $startDate, Carbon $endDate): array
    {
        $months = min(6, $endDate->diffInMonths($startDate));
        $trends = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = $endDate->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $endDate->copy()->subMonths($i)->endOfMonth();

            $collections = DB::table('consolidated_billings')
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->where('status', 'paid')
                ->sum('paid_amount');

            $profit = $collections * 0.15; // 15% profit margin

            $trends[] = [
                'period' => $monthStart->format('M Y'),
                'profit' => $profit,
                'margin' => 15.0
            ];
        }

        return $trends;
    }

    private function calculateMonthlyGrowth(array $previousTrends, float $currentValue): float
    {
        if (empty($previousTrends)) return 0;

        $lastValue = end($previousTrends)['revenue'] ?? 0;
        return $lastValue > 0 ? (($currentValue - $lastValue) / $lastValue) * 100 : 0;
    }

    /**
     * Get top customers
     */
    private function getTopCustomers(Carbon $startDate, Carbon $endDate, int $limit = 5): array
    {
        return DB::table('consolidated_billings as cb')
            ->join('users as u', 'cb.user_id', '=', 'u.id')
            ->select([
                'u.id',
                'u.name',
                DB::raw('SUM(cb.total_amount) as revenue'),
                DB::raw('COUNT(cb.id) as invoice_count')
            ])
            ->whereBetween('cb.billing_date', [$startDate, $endDate])
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Forecast methods
     */
    private function forecastNext30DaysRevenue(array $metrics): array
    {
        $dailyRevenue = $metrics['revenue_metrics']['total_revenue'] / max(1, $metrics['period']['days'] ?? 30);
        $growthRate = $metrics['growth_metrics']['revenue_growth'] / 100 / 12; // Monthly growth

        return [
            'amount' => $dailyRevenue * 30 * (1 + $growthRate),
            'growth' => $metrics['growth_metrics']['revenue_growth'],
            'drivers' => ['Existing customer base', 'Current growth trend']
        ];
    }

    private function forecastNext90DaysRevenue(array $metrics): array
    {
        $monthlyRevenue = $metrics['revenue_metrics']['total_revenue'] / max(1, ($metrics['period']['months'] ?? 1));
        $growthRate = $metrics['growth_metrics']['revenue_growth'] / 100 / 4; // Quarterly growth

        return [
            'amount' => $monthlyRevenue * 3 * (1 + $growthRate),
            'growth' => $metrics['growth_metrics']['revenue_growth'],
            'drivers' => ['Seasonal factors', 'Market expansion']
        ];
    }

    private function forecastNext30DaysCashflow(array $metrics): array
    {
        $dailyCollections = $metrics['revenue_metrics']['collected_revenue'] / max(1, $metrics['period']['days'] ?? 30);
        $dailyCosts = $metrics['cost_metrics']['total_costs'] / max(1, $metrics['period']['days'] ?? 30);

        return [
            'inflows' => $dailyCollections * 30,
            'outflows' => $dailyCosts * 30,
            'net_cashflow' => ($dailyCollections - $dailyCosts) * 30,
            'key_assumptions' => ['90% collection rate', 'Stable cost structure']
        ];
    }

    private function forecastNext90DaysCashflow(array $metrics): array
    {
        $monthlyCollections = $metrics['revenue_metrics']['collected_revenue'] / max(1, ($metrics['period']['months'] ?? 1));
        $monthlyCosts = $metrics['cost_metrics']['total_costs'] / max(1, ($metrics['period']['months'] ?? 1));

        return [
            'inflows' => $monthlyCollections * 3,
            'outflows' => $monthlyCosts * 3,
            'net_cashflow' => ($monthlyCollections - $monthlyCosts) * 3,
            'key_assumptions' => ['Maintained collection efficiency', 'Controlled cost growth']
        ];
    }

    private function identifyHighProbabilityRisks(array $metrics): array
    {
        $risks = [];

        if ($metrics['revenue_metrics']['collection_rate'] < 80) {
            $risks[] = 'Continued collection inefficiency';
        }

        if ($metrics['liquidity_metrics']['current_ratio'] < 1.5) {
            $risks[] = 'Liquidity constraints';
        }

        if ($metrics['revenue_metrics']['outstanding_revenue'] > $metrics['revenue_metrics']['collected_revenue'] * 0.3) {
            $risks[] = 'Increasing bad debt risk';
        }

        return $risks;
    }

    private function identifyEmergingRisks(array $metrics): array
    {
        return [
            'Market competition intensifying',
            'Potential regulatory changes',
            'Economic downturn impact'
        ];
    }

    private function suggestMitigationStrategies(array $metrics): array
    {
        $strategies = [];

        if ($metrics['revenue_metrics']['collection_rate'] < 85) {
            $strategies[] = 'Implement stricter credit controls';
        }

        if ($metrics['liquidity_metrics']['current_ratio'] < 1.5) {
            $strategies[] = 'Establish emergency credit line';
        }

        if ($metrics['profitability_metrics']['net_margin'] < 15) {
            $strategies[] = 'Diversify revenue streams';
        }

        return $strategies;
    }

    /**
     * Stub methods for unimplemented functionality
     */
    private function getPeriodHighlights(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'largest_invoice' => $this->getLargestInvoice($startDate, $endDate),
            'top_performing_customer' => $this->getTopPerformingCustomer($startDate, $endDate),
            'most_efficient_collection' => $this->getMostEfficientCollection($startDate, $endDate)
        ];
    }

    private function getLargestInvoice(Carbon $startDate, Carbon $endDate): ?array
    {
        $invoice = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->orderByDesc('total_amount')
            ->first(['billing_number', 'total_amount', 'user_id']);

        if (!$invoice) return null;

        $customer = DB::table('users')->where('id', $invoice->user_id)->value('name');

        return [
            'invoice_number' => $invoice->billing_number,
            'amount' => $invoice->total_amount,
            'customer' => $customer
        ];
    }

    private function getTopPerformingCustomer(Carbon $startDate, Carbon $endDate): ?array
    {
        $customer = DB::table('consolidated_billings as cb')
            ->join('users as u', 'cb.user_id', '=', 'u.id')
            ->whereBetween('cb.billing_date', [$startDate, $endDate])
            ->select([
                'u.id',
                'u.name',
                DB::raw('SUM(cb.total_amount) as total_spent'),
                DB::raw('COUNT(cb.id) as invoices_count')
            ])
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total_spent')
            ->first();

        if (!$customer) return null;

        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'total_spent' => $customer->total_spent,
            'invoices_count' => $customer->invoices_count
        ];
    }

    private function getMostEfficientCollection(Carbon $startDate, Carbon $endDate): ?array
    {
        $collection = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->whereNotNull('payment_date')
            ->whereNotNull('billing_date')
            ->select([
                'billing_number',
                'paid_amount',
                DB::raw('DATEDIFF(payment_date, billing_date) as collection_days')
            ])
            ->orderBy('collection_days')
            ->first();

        if (!$collection) return null;

        return [
            'invoice_number' => $collection->billing_number,
            'amount' => $collection->paid_amount,
            'collection_days' => $collection->collection_days
        ];
    }

    // Additional stub methods for trend data
    private function getCollectionTrendData(Carbon $startDate, Carbon $endDate): array { return []; }
    private function getCustomerTrendData(Carbon $startDate, Carbon $endDate): array { return []; }
    private function getEfficiencyTrendData(Carbon $startDate, Carbon $endDate): array { return []; }
}
