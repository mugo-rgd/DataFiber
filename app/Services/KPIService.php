<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lease;
use App\Models\KPIHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KPIService
{
    /**
     * Get all account managers with their KPIs
     */
    public function getAccountManagerKPIs($accountManagerId = null, $currency = null)
    {
        // Get users with role = 'account_manager'
        $query = User::where('role', 'account_manager')
            ->where('status', 'active');

        if ($accountManagerId) {
            $query->where('id', $accountManagerId);
        }

        $managers = $query->get();
        $kpis = [];

        foreach ($managers as $manager) {
            $kpis[$manager->id] = $this->calculateManagerKPIs($manager, $currency);
        }

        return $kpis;
    }

    /**
     * Calculate KPIs for a single account manager
     */
    protected function calculateManagerKPIs($manager, $filterCurrency = null)
    {
        // Get customers assigned to this account manager
        $customerIds = $this->getCustomersForManager($manager->id);
        $customers = User::whereIn('id', $customerIds)
            ->where('role', 'customer')
            ->where('status', 'active')
            ->get();

        // Get all active leases for these customers
        $leasesQuery = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active');

        // Filter by currency if specified
        if ($filterCurrency) {
            $leasesQuery->where('currency', $filterCurrency);
        }

        $leases = $leasesQuery->get();

        // Separate leases by currency for reporting
        $leasesUSD = $leases->where('currency', 'USD');
        $leasesKSH = $leases->where('currency', 'KSH');

        // Calculate financial metrics
        $financialMetrics = $this->calculateFinancialMetrics($leases, $leasesUSD, $leasesKSH, $filterCurrency);

        // Calculate other metrics
        $totalDistance = $leases->sum('distance_km');
        $totalCores = $leases->sum('cores_required');
        $avgLinkDistance = $leases->count() > 0 ? $totalDistance / $leases->count() : 0;

        // Contract metrics
        $upcomingRenewals = $this->getUpcomingRenewals($customerIds, $filterCurrency);
        $churnRate = $this->calculateChurnRate($manager, $customerIds);

        return [
            'account_manager' => [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'phone' => $manager->phone,
            ],
            'filter_currency' => $filterCurrency,
            'financial' => $financialMetrics,
            'portfolio' => [
                'total_customers' => $customers->count(),
                'total_leases' => $leases->count(),
                'active_leases' => $leases->where('status', 'active')->count(),
                'terminated_leases' => $leases->where('status', 'terminated')->count(),
                'customers_by_currency' => $this->getCustomersByCurrency($customerIds, $filterCurrency),
                'draft_leases' => $leases->where('status', 'draft')->count(), // Add this
                'pending_leases' => $leases->where('status', 'pending')->count(), // Add this
            ],
            'utilization' => [
                'total_distance_km' => round($totalDistance, 2),
                'total_cores_leased' => $totalCores,
                'avg_link_distance' => round($avgLinkDistance, 2),
                'distance_by_currency' => $this->getDistanceByCurrency($customerIds, $filterCurrency),
            ],
            'contract_health' => [
                'avg_contract_term_years' => $this->getAvgContractTerm($customerIds, $filterCurrency),
                'short_term_contracts' => $this->getContractCountByTerm($customerIds, 'short', $filterCurrency),
                'mid_term_contracts' => $this->getContractCountByTerm($customerIds, 'mid', $filterCurrency),
                'long_term_contracts' => $this->getContractCountByTerm($customerIds, 'long', $filterCurrency),
                'upcoming_renewals_90days' => $upcomingRenewals['count'],
                'renewal_revenue_at_risk' => $upcomingRenewals['revenue_at_risk'],
                'renewals_list' => $upcomingRenewals['leases'],
            ],
            'customer_health' => [
                'churn_rate' => round($churnRate, 2),
                'technology_mix' => $this->getTechnologyMix($customerIds, $filterCurrency),
                'top_customers_by_revenue' => $this->getTopCustomers($customerIds, $filterCurrency, 5),
            ],
            'performance_summary' => $this->getPerformanceRating(
                $financialMetrics['total_mrr'],
                $customers->count(),
                $churnRate
            ),
        ];
    }

    /**
     * Get customers assigned to a manager
     */
    protected function getCustomersForManager($managerId)
    {
        return User::where('account_manager_id', $managerId)
            ->where('role', 'customer')
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();
    }

    /**
     * Calculate financial metrics with currency separation
     */
    protected function calculateFinancialMetrics($leases, $leasesUSD, $leasesKSH, $filterCurrency = null)
    {
        $metrics = [
            'total_mrr' => 0,
            'total_tcv' => 0,
            'arpc' => 0,
            'usd' => [
                'total_mrr' => $leasesUSD->sum('monthly_cost'),
                'total_tcv' => $leasesUSD->sum('total_contract_value'),
                'leases_count' => $leasesUSD->count(),
            ],
            'ksh' => [
                'total_mrr' => $leasesKSH->sum('monthly_cost'),
                'total_tcv' => $leasesKSH->sum('total_contract_value'),
                'leases_count' => $leasesKSH->count(),
            ],
            'breakdown' => [
                'usd_revenue_percentage' => 0,
                'ksh_revenue_percentage' => 0,
            ]
        ];

        // Calculate totals
        $metrics['total_mrr'] = $metrics['usd']['total_mrr'] + $metrics['ksh']['total_mrr'];
        $metrics['total_tcv'] = $metrics['usd']['total_tcv'] + $metrics['ksh']['total_tcv'];

        // Calculate ARPC (Average Revenue Per Customer)
        $totalCustomers = $leases->groupBy('customer_id')->count();
        $metrics['arpc'] = $totalCustomers > 0 ? $metrics['total_mrr'] / $totalCustomers : 0;

        // Calculate percentages
        if ($metrics['total_mrr'] > 0) {
            $metrics['breakdown']['usd_revenue_percentage'] = round(($metrics['usd']['total_mrr'] / $metrics['total_mrr']) * 100, 2);
            $metrics['breakdown']['ksh_revenue_percentage'] = round(($metrics['ksh']['total_mrr'] / $metrics['total_mrr']) * 100, 2);
        }

        // If filtering by specific currency, show only that currency's data as primary
        if ($filterCurrency === 'USD') {
            $metrics['total_mrr'] = $metrics['usd']['total_mrr'];
            $metrics['total_tcv'] = $metrics['usd']['total_tcv'];
        } elseif ($filterCurrency === 'KSH') {
            $metrics['total_mrr'] = $metrics['ksh']['total_mrr'];
            $metrics['total_tcv'] = $metrics['ksh']['total_tcv'];
        }

        return $metrics;
    }

    /**
     * Get customers grouped by currency
     */
    protected function getCustomersByCurrency($customerIds, $filterCurrency = null)
    {
        $leasesQuery = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active');

        if ($filterCurrency) {
            $leasesQuery->where('currency', $filterCurrency);
        }

        $leases = $leasesQuery->get();

        $customersWithUSD = $leases->where('currency', 'USD')->groupBy('customer_id')->count();
        $customersWithKSH = $leases->where('currency', 'KSH')->groupBy('customer_id')->count();

        // Customers with both currencies
        $customerCurrencies = [];
        foreach ($leases->groupBy('customer_id') as $customerId => $customerLeases) {
            $currencies = $customerLeases->pluck('currency')->unique();
            if ($currencies->count() > 1) {
                $customerCurrencies[] = $customerId;
            }
        }

        return [
            'usd_customers' => $customersWithUSD,
            'ksh_customers' => $customersWithKSH,
            'mixed_currency_customers' => count($customerCurrencies),
        ];
    }

    /**
     * Get distance by currency
     */
    protected function getDistanceByCurrency($customerIds, $filterCurrency = null)
    {
        $query = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active');

        if ($filterCurrency) {
            $query->where('currency', $filterCurrency);
        }

        $distances = $query->select('currency', DB::raw('SUM(distance_km) as total_distance'))
            ->groupBy('currency')
            ->get();

        return [
            'usd_distance' => round($distances->where('currency', 'USD')->first()->total_distance ?? 0, 2),
            'ksh_distance' => round($distances->where('currency', 'KSH')->first()->total_distance ?? 0, 2),
            'total_distance' => round($distances->sum('total_distance'), 2),
        ];
    }

    /**
     * Get upcoming renewals in next 90 days
     */
    protected function getUpcomingRenewals($customerIds, $filterCurrency = null)
    {
        $query = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active')
            ->where('end_date', '<=', Carbon::now()->addDays(90))
            ->where('end_date', '>=', Carbon::now());

        if ($filterCurrency) {
            $query->where('currency', $filterCurrency);
        }

        $renewals = $query->get();

        return [
            'count' => $renewals->count(),
            'revenue_at_risk' => round($renewals->sum('monthly_cost'), 2),
            'leases' => $renewals->map(function($lease) {
                return [
                    'lease_number' => $lease->lease_number,
                    'customer_name' => optional($lease->customer)->name ?? 'Unknown',
                    'end_date' => $lease->end_date->format('Y-m-d'),
                    'monthly_cost' => $lease->monthly_cost,
                    'currency' => $lease->currency,
                    'formatted_cost' => $this->formatCurrency($lease->monthly_cost, $lease->currency),
                ];
            })
        ];
    }

    /**
     * Calculate churn rate (last 12 months)
     */
    protected function calculateChurnRate($manager, $customerIds)
    {
        $twelveMonthsAgo = Carbon::now()->subMonths(12);

        // Customers that had active leases 12 months ago
        $customersAtStart = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active')
            ->where('start_date', '<=', $twelveMonthsAgo)
            ->distinct('customer_id')
            ->count('customer_id');

        // Customers that have no active leases now but had them before
        $activeCustomersNow = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active')
            ->distinct('customer_id')
            ->count('customer_id');

        $churnedCustomers = max(0, $customersAtStart - $activeCustomersNow);

        return $customersAtStart > 0 ? ($churnedCustomers / $customersAtStart) * 100 : 0;
    }

    /**
     * Get average contract term in years
     */
    protected function getAvgContractTerm($customerIds, $filterCurrency = null)
    {
        $query = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active');

        if ($filterCurrency) {
            $query->where('currency', $filterCurrency);
        }

        $avgTerm = $query->avg('contract_term_months');

        return $avgTerm ? round($avgTerm / 12, 1) : 0;
    }

    /**
     * Get contract count by term length
     */
    protected function getContractCountByTerm($customerIds, $type, $filterCurrency = null)
    {
        $query = Lease::whereIn('customer_id', $customerIds)->where('status', 'active');

        if ($filterCurrency) {
            $query->where('currency', $filterCurrency);
        }

        switch ($type) {
            case 'short':
                return $query->where('contract_term_months', '<=', 24)->count();
            case 'mid':
                return $query->whereBetween('contract_term_months', [25, 60])->count();
            case 'long':
                return $query->where('contract_term_months', '>', 60)->count();
            default:
                return 0;
        }
    }

    /**
     * Get technology mix with currency breakdown
     */
    protected function getTechnologyMix($customerIds, $filterCurrency = null)
    {
        $query = Lease::whereIn('customer_id', $customerIds)
            ->where('status', 'active');

        if ($filterCurrency) {
            $query->where('currency', $filterCurrency);
        }

        $techData = $query->select(
                'technology',
                'currency',
                DB::raw('count(*) as count'),
                DB::raw('sum(monthly_cost) as revenue')
            )
            ->groupBy('technology', 'currency')
            ->get();

        $technologies = [];
        foreach ($techData as $item) {
            $tech = $item->technology ?? 'Not Specified';
            if (!isset($technologies[$tech])) {
                $technologies[$tech] = [
                    'technology' => $tech,
                    'total_count' => 0,
                    'usd_count' => 0,
                    'ksh_count' => 0,
                    'total_revenue' => 0,
                    'usd_revenue' => 0,
                    'ksh_revenue' => 0,
                ];
            }

            if ($item->currency === 'USD') {
                $technologies[$tech]['usd_count'] = $item->count;
                $technologies[$tech]['usd_revenue'] = $item->revenue;
            } else {
                $technologies[$tech]['ksh_count'] = $item->count;
                $technologies[$tech]['ksh_revenue'] = $item->revenue;
            }

            $technologies[$tech]['total_count'] += $item->count;
            $technologies[$tech]['total_revenue'] += $item->revenue;
        }

        return array_values($technologies);
    }

    /**
     * Get top customers by revenue
     */
    protected function getTopCustomers($customerIds, $filterCurrency = null, $limit = 5)
    {
        $query = User::whereIn('id', $customerIds)
            ->where('role', 'customer')
            ->where('status', 'active')
            ->with(['leases' => function($q) use ($filterCurrency) {
                $q->where('status', 'active');
                if ($filterCurrency) {
                    $q->where('currency', $filterCurrency);
                }
            }]);

        $customers = $query->get();

        $customerRevenue = [];
        foreach ($customers as $customer) {
            $totalRevenue = $customer->leases->sum('monthly_cost');
            $currency = $filterCurrency ?: ($customer->leases->first()->currency ?? 'USD');

            if ($totalRevenue > 0) {
                $customerRevenue[] = [
                    'id' => $customer->id,
                    'name' => $customer->company_name ?: $customer->name,
                    'total_revenue' => round($totalRevenue, 2),
                    'currency' => $currency,
                    'formatted_revenue' => $this->formatCurrency($totalRevenue, $currency),
                    'leases_count' => $customer->leases->count(),
                ];
            }
        }

        usort($customerRevenue, function($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });

        return array_slice($customerRevenue, 0, $limit);
    }

    /**
     * Generate performance rating
     */
    protected function getPerformanceRating($totalMRR, $customerCount, $churnRate)
    {
        $score = 0;

        // Revenue scoring (assuming $100K+ is excellent)
        if ($totalMRR >= 100000) $score += 40;
        elseif ($totalMRR >= 50000) $score += 30;
        elseif ($totalMRR >= 10000) $score += 20;
        else $score += 10;

        // Customer count scoring
        if ($customerCount >= 20) $score += 30;
        elseif ($customerCount >= 10) $score += 20;
        elseif ($customerCount >= 5) $score += 10;
        else $score += 5;

        // Churn rate scoring (lower is better)
        if ($churnRate <= 5) $score += 30;
        elseif ($churnRate <= 10) $score += 20;
        elseif ($churnRate <= 20) $score += 10;
        else $score += 0;

        if ($score >= 80) $rating = 'Excellent';
        elseif ($score >= 60) $rating = 'Good';
        elseif ($score >= 40) $rating = 'Average';
        else $rating = 'Needs Improvement';

        return [
            'score' => $score,
            'rating' => $rating,
        ];
    }

    /**
     * Get revenue growth with currency separation
     */
    public function getRevenueGrowth($accountManagerId = null, $filterCurrency = null)
    {
        $query = Lease::query()
            ->join('users', 'leases.customer_id', '=', 'users.id')
            ->where('leases.status', 'active');

        if ($accountManagerId) {
            $query->where('users.account_manager_id', $accountManagerId);
        }

        if ($filterCurrency) {
            $query->where('leases.currency', $filterCurrency);
        }

        $monthlyData = $query->select(
                DB::raw("DATE_FORMAT(leases.start_date, '%Y-%m') as month"),
                'leases.currency',
                DB::raw('SUM(leases.monthly_cost) as revenue')
            )
            ->groupBy(DB::raw("DATE_FORMAT(leases.start_date, '%Y-%m')"), 'leases.currency')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Organize data by month
        $months = [];
        foreach ($monthlyData as $data) {
            if (!isset($months[$data->month])) {
                $months[$data->month] = ['usd' => 0, 'ksh' => 0, 'total' => 0];
            }
            $months[$data->month][strtolower($data->currency)] = $data->revenue;
            $months[$data->month]['total'] += $data->revenue;
        }

        // Calculate growth
        $growth = [];
        $previousTotal = null;

        foreach ($months as $month => $revenues) {
            $total = $filterCurrency ? $revenues[strtolower($filterCurrency)] : $revenues['total'];

            $growthRate = null;
            if ($previousTotal && $previousTotal > 0) {
                $growthRate = round((($total - $previousTotal) / $previousTotal) * 100, 2);
            }

            $growth[] = [
                'month' => $month,
                'revenue_usd' => round($revenues['usd'], 2),
                'revenue_ksh' => round($revenues['ksh'], 2),
                'total_revenue' => round($revenues['total'], 2),
                'filtered_revenue' => round($total, 2),
                'growth_rate' => $growthRate,
            ];

            $previousTotal = $total;
        }

        return $growth;
    }

    /**
     * Format currency with symbol
     */
    protected function formatCurrency($amount, $currency)
    {
        $symbol = $currency === 'USD' ? '$' : 'KSh';
        return $symbol . ' ' . number_format($amount, 2);
    }

    /**
     * Save monthly KPI snapshot
     */
    public function saveMonthlySnapshot()
    {
        $kpis = $this->getAccountManagerKPIs(null, null);
        $saved = [];

        foreach ($kpis as $managerId => $kpi) {
            $history = KPIHistory::updateOrCreate(
                [
                    'account_manager_id' => $managerId,
                    'snapshot_date' => Carbon::now()->startOfMonth(),
                ],
                [
                    // USD Metrics
                    'total_mrr_usd' => $kpi['financial']['usd']['total_mrr'],
                    'total_tcv_usd' => $kpi['financial']['usd']['total_tcv'],
                    'arpc_usd' => $kpi['financial']['usd']['total_mrr'] / max(1, $kpi['portfolio']['total_customers']),

                    // KSH Metrics
                    'total_mrr_ksh' => $kpi['financial']['ksh']['total_mrr'],
                    'total_tcv_ksh' => $kpi['financial']['ksh']['total_tcv'],
                    'arpc_ksh' => $kpi['financial']['ksh']['total_mrr'] / max(1, $kpi['portfolio']['total_customers']),

                    // Combined Metrics
                    'total_mrr_combined' => $kpi['financial']['total_mrr'],
                    'total_tcv_combined' => $kpi['financial']['total_tcv'],

                    // Portfolio Metrics
                    'total_customers' => $kpi['portfolio']['total_customers'],
                    'total_leases' => $kpi['portfolio']['total_leases'],
                    'active_leases' => $kpi['portfolio']['active_leases'],
                    'terminated_leases' => $kpi['portfolio']['terminated_leases'],

                    // Utilization Metrics
                    'total_distance_km' => $kpi['utilization']['total_distance_km'],
                    'total_cores_leased' => $kpi['utilization']['total_cores_leased'],

                    // Contract Health
                    'avg_contract_term_years' => $kpi['contract_health']['avg_contract_term_years'],
                    'short_term_contracts' => $kpi['contract_health']['short_term_contracts'],
                    'mid_term_contracts' => $kpi['contract_health']['mid_term_contracts'],
                    'long_term_contracts' => $kpi['contract_health']['long_term_contracts'],
                    'upcoming_renewals_90days' => $kpi['contract_health']['upcoming_renewals_90days'],
                    'renewal_revenue_at_risk' => $kpi['contract_health']['renewal_revenue_at_risk'],

                    // Customer Health
                    'churn_rate' => $kpi['customer_health']['churn_rate'],

                    // Performance
                    'performance_score' => $kpi['performance_summary']['score'],
                    'performance_rating' => $kpi['performance_summary']['rating'],

                    // JSON Data
                    'technology_mix' => json_encode($kpi['customer_health']['technology_mix']),
                    'customer_breakdown' => json_encode($kpi['customer_health']['top_customers_by_revenue']),
                    'snapshot_data' => json_encode($kpi),
                ]
            );

            $saved[] = $history;
        }

        return $saved;
    }
}
