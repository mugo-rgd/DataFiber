<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lease;
use App\Models\Billing;
use App\Models\DesignRequest;
use App\Models\LeaseBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingAdminController extends Controller
{
    /**
     * Display marketing admin dashboard
     */
    public function dashboard()
{
    $stats = [
        'total_customers' => [
            'title' => 'Total Customers',
            'value' => User::where('role', 'customer')->count(),
            'color' => 'primary',
            'icon' => 'users'
        ],
        'new_customers_this_month' => [
            'title' => 'New Customers This Month',
            'value' => User::where('role', 'customer')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'color' => 'success',
            'icon' => 'user-plus'
        ],
        'active_leases' => [
            'title' => 'Active Leases',
            'value' => Lease::where('status', 'active')->count(),
            'color' => 'info',
            'icon' => 'network-wired'
        ],
        'revenue_this_month' => [
            'title' => 'Revenue This Month',
            'value' => LeaseBilling::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'color' => 'success',
            'icon' => 'dollar-sign'
        ],
        'pending_design_requests' => [
            'title' => 'Pending Design Requests',
            'value' => DesignRequest::where('status', 'pending')->count(),
            'color' => 'warning',
            'icon' => 'drafting-compass'
        ],
        'customer_growth_rate' => [
            'title' => 'Customer Growth Rate',
            'value' => $this->calculateCustomerGrowthRate(),
            'color' => 'info',
            'icon' => 'chart-line'
        ]
    ];

    // Customer acquisition chart data
    $customerAcquisition = $this->getCustomerAcquisitionData();

    // Revenue trends
    $revenueTrends = $this->getRevenueTrends();

    return view('admin.dashboard', compact('stats', 'customerAcquisition', 'revenueTrends'));
}

    /**
     * Display marketing analytics
     */
    public function analytics()
    {
        $analytics = [
            'customer_demographics' => $this->getCustomerDemographics(),
            'service_popularity' => $this->getServicePopularity(),
            'geographic_distribution' => $this->getGeographicDistribution(),
            'conversion_rates' => $this->getConversionRates(),
        ];

        return view('marketing-admin.index', compact('analytics'));
    }

    /**
     * Display campaigns
     */
    public function campaigns()
    {
        $campaigns = [
            // This would typically come from a campaigns table
            [
                'name' => 'Q1 Fiber Promotion',
                'status' => 'active',
                'budget' => 50000,
                'leads_generated' => 245,
                'conversions' => 45,
            ],
        ];

        return view('marketing-admin.campaigns', compact('campaigns'));
    }

    /**
     * Display customer insights
     */
    public function customerInsights()
    {
        $insights = [
            'customer_satisfaction' => $this->getCustomerSatisfaction(),
            'churn_risk' => $this->getChurnRiskAnalysis(),
            'lifetime_value' => $this->getCustomerLifetimeValue(),
            'segmentation' => $this->getCustomerSegmentation(),
        ];

        return view('marketing-admin.customer-insights', compact('insights'));
    }

    /**
     * Display marketing reports
     */
    public function reports()
    {
        $reports = [
            'marketing_performance' => $this->generateMarketingPerformanceReport(),
            'lead_generation' => $this->generateLeadGenerationReport(),
            'campaign_effectiveness' => $this->generateCampaignEffectivenessReport(),
        ];

        return view('marketing-admin.reports', compact('reports'));
    }

    /**
     * Calculate customer growth rate
     */
    private function calculateCustomerGrowthRate()
    {
        $currentMonth = User::where('role', 'customer')
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastMonth = User::where('role', 'customer')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        if ($lastMonth === 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    /**
     * Get customer acquisition data
     */
    private function getCustomerAcquisitionData()
    {
        return DB::table('users')
            ->where('role', 'customer')
            ->select(DB::raw('MONTH(created_at) as month, COUNT(*) as count'))
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
    }

    /**
     * Get revenue trends
     */
    private function getRevenueTrends()
    {
        return DB::table('lease_billings')
            ->where('status', 'paid')
            ->select(DB::raw('MONTH(created_at) as month, SUM(amount) as revenue'))
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->toArray();
    }

    /**
     * Get customer demographics
     */
    private function getCustomerDemographics()
    {
        return [
            'company_size' => [
                'small' => 45,
                'medium' => 35,
                'large' => 20,
            ],
            'industry' => [
                'telecom' => 30,
                'enterprise' => 25,
                'education' => 20,
                'healthcare' => 15,
                'other' => 10,
            ],
        ];
    }

    /**
     * Get service popularity
     */
   private function getServicePopularity()
{
    try {
        return DB::table('leases')
            ->join('colocation_list', function($join) {
                $join->on('leases.service_type', '=', 'colocation_list.service_category')
                     ->whereRaw('leases.service_type COLLATE utf8mb4_unicode_ci = colocation_list.service_category COLLATE utf8mb4_unicode_ci');
            })
            ->select('colocation_list.service_category', DB::raw('COUNT(*) as count'))
            ->groupBy('colocation_list.service_category')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    } catch (\Exception $e) {
        // Fallback: try without collation specification
        try {
            return DB::table('leases')
                ->join('colocation_list', 'leases.service_type', '=', 'colocation_list.service_category')
                ->select('colocation_list.service_category', DB::raw('COUNT(*) as count'))
                ->groupBy('colocation_list.service_category')
                ->orderBy('count', 'desc')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            // Final fallback: just count leases by service_type
            logger('Service popularity query failed: ' . $e->getMessage());
            return DB::table('leases')
                ->select('service_type as service_category', DB::raw('COUNT(*) as count'))
                ->whereNotNull('service_type')
                ->groupBy('service_type')
                ->orderBy('count', 'desc')
                ->get()
                ->toArray();
        }
    }
}
    /**
     * Get geographic distribution
     */
    private function getGeographicDistribution()
    {
        return DB::table('users')
            ->where('role', 'customer')
            ->join('company_profiles', 'users.id', '=', 'company_profiles.user_id')
            ->select('company_profiles.town', DB::raw('COUNT(*) as count'))
            ->groupBy('company_profiles.town')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get conversion rates
     */
    private function getConversionRates()
    {
        return [
            'lead_to_customer' => 18.5,
            'inquiry_to_quotation' => 65.2,
            'quotation_to_lease' => 42.8,
        ];
    }

    /**
     * Get customer satisfaction
     */
    private function getCustomerSatisfaction()
    {
        // This would typically come from surveys or feedback
        return [
            'satisfied' => 78,
            'neutral' => 15,
            'unsatisfied' => 7,
        ];
    }

    /**
     * Get churn risk analysis
     */
    private function getChurnRiskAnalysis()
    {
        return [
            'low_risk' => 65,
            'medium_risk' => 25,
            'high_risk' => 10,
        ];
    }

    /**
     * Get customer lifetime value
     */
    private function getCustomerLifetimeValue()
    {
        return DB::table('lease_billings')
            ->join('users', 'lease_billings.customer_id', '=', 'users.id')
            ->where('lease_billings.status', 'paid')
            ->select('users.id', 'users.name', DB::raw('SUM(lease_billings.total_amount) as total_value'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_value', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get customer segmentation
     */
    private function getCustomerSegmentation()
    {
        return [
            'premium' => 15,
            'standard' => 60,
            'basic' => 25,
        ];
    }

    /**
     * Generate marketing performance report
     */
    private function generateMarketingPerformanceReport()
    {
        return [
            'period' => 'Last 30 Days',
            'new_leads' => 156,
            'conversions' => 28,
            'conversion_rate' => 17.9,
            'cost_per_acquisition' => 1250,
            'roi' => 3.2,
        ];
    }

    /**
     * Generate lead generation report
     */
    private function generateLeadGenerationReport()
    {
        return [
            'website' => 45,
            'referral' => 25,
            'partnership' => 15,
            'direct' => 10,
            'other' => 5,
        ];
    }

    /**
     * Generate campaign effectiveness report
     */
    private function generateCampaignEffectivenessReport()
    {
        return [
            'digital_ads' => ['leads' => 89, 'conversions' => 12, 'cost' => 15000],
            'email_marketing' => ['leads' => 45, 'conversions' => 8, 'cost' => 5000],
            'events' => ['leads' => 32, 'conversions' => 6, 'cost' => 20000],
            'referral_program' => ['leads' => 28, 'conversions' => 10, 'cost' => 8000],
        ];
    }

    public function accountManagers()
{
    $accountManagers = User::where('role', 'account_manager')->get();
    return view('marketing-admin.account-managers', compact('accountManagers'));
}

public function performance()
{
    return view('marketing-admin.performance');
}

public function targets()
{
    return view('marketing-admin.targets');
}

public function commissions()
{
    return view('marketing-admin.commissions');
}

public function salesPipeline()
{
    return view('marketing-admin.sales-pipeline');
}
}
