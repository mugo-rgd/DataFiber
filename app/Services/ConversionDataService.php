<?php

namespace App\Services;

use App\Models\ConversionData;
use Illuminate\Support\Facades\DB;

class ConversionDataService
{
    public function getCustomerSummary()
    {
        return ConversionData::select([
                'customer_name',
                DB::raw('COUNT(*) as total_links'),
                DB::raw('SUM(monthly_link_value_usd) as total_monthly_value'),
                DB::raw('SUM(total_contract_value_usd) as total_contract_value'),
                DB::raw('AVG(contract_duration_yrs) as avg_contract_duration')
            ])
            ->groupBy('customer_name')
            ->orderBy('total_contract_value', 'desc')
            ->get();
    }

    public function getRevenueByLinkClass()
    {
        return ConversionData::select([
                'link_class',
                DB::raw('SUM(monthly_link_value_usd) as monthly_revenue'),
                DB::raw('SUM(total_contract_value_usd) as total_revenue'),
                DB::raw('COUNT(*) as link_count')
            ])
            ->whereNotNull('link_class')
            ->groupBy('link_class')
            ->get();
    }

    public function getTopRoutes($limit = 10)
    {
        return ConversionData::select([
                'route_name',
                DB::raw('COUNT(DISTINCT customer_name) as customer_count'),
                DB::raw('SUM(monthly_link_value_usd) as monthly_value'),
                DB::raw('SUM(total_contract_value_usd) as contract_value')
            ])
            ->groupBy('route_name')
            ->orderBy('contract_value', 'desc')
            ->limit($limit)
            ->get();
    }

    public function calculateForecast($years = 5)
    {
        $monthlyRevenue = ConversionData::sum('monthly_link_value_usd');
        $annualRevenue = $monthlyRevenue * 12;

        return [
            'current_monthly' => $monthlyRevenue,
            'current_annual' => $annualRevenue,
            'forecast' => array_map(function($year) use ($annualRevenue) {
                return [
                    'year' => date('Y') + $year,
                    'revenue' => $annualRevenue * (1 + (0.1 * $year)) // 10% growth per year
                ];
            }, range(1, $years))
        ];
    }
}
