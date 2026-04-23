<?php

namespace App\Http\Controllers;

use App\Services\KPIService;
use App\Models\User;
use Illuminate\Http\Request;

class KPIDashboardController extends Controller
{
    protected $kpiService;

    public function __construct(KPIService $kpiService)
    {
        $this->kpiService = $kpiService;
        // $this->middleware('auth');
    }

    /**
     * Display the KPI dashboard
     */
    public function index(Request $request)
    {
        $accountManagerId = $request->get('account_manager_id');
        $currency = $request->get('currency'); // USD, KSH, or null for all

        // Get all account managers for filter
        $accountManagers = User::where('role', 'account_manager')
            ->where('status', 'active')
            ->get();

        // Get KPIs
        $kpis = $this->kpiService->getAccountManagerKPIs($accountManagerId, $currency);

        // Get revenue growth data
        $revenueGrowth = $this->kpiService->getRevenueGrowth($accountManagerId, $currency);

        return view('kpi.dashboard', compact(
            'kpis',
            'accountManagers',
            'accountManagerId',
            'revenueGrowth',
            'currency'
        ));
    }

    /**
     * Get detailed KPIs for a specific account manager (AJAX)
     */
    public function show(Request $request, $id)
    {
        $currency = $request->get('currency');
        $kpis = $this->kpiService->getAccountManagerKPIs($id, $currency);

        if (empty($kpis)) {
            return response()->json(['error' => 'Account manager not found'], 404);
        }

        return response()->json(reset($kpis));
    }

    /**
     * Export KPIs to CSV
     */
    public function export(Request $request)
    {
        $accountManagerId = $request->get('account_manager_id');
        $currency = $request->get('currency');
        $kpis = $this->kpiService->getAccountManagerKPIs($accountManagerId, $currency);

        $filename = 'kpi_export_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // Add CSV headers
        fputcsv($handle, [
            'Account Manager',
            'Total Customers',
            'Total Leases',
            'Total MRR (USD)',
            'Total MRR (KSH)',
            'Total MRR (Combined)',
            'Total TCV (Combined)',
            'ARPC',
            'USD Leases Count',
            'KSH Leases Count',
            'USD Revenue %',
            'KSH Revenue %',
            'Total Distance (km)',
            'Total Cores',
            'Churn Rate (%)',
            'Upcoming Renewals',
            'Performance Rating'
        ]);

        // Add data rows
        foreach ($kpis as $kpi) {
            fputcsv($handle, [
                $kpi['account_manager']['name'],
                $kpi['portfolio']['total_customers'],
                $kpi['portfolio']['total_leases'],
                $kpi['financial']['usd']['total_mrr'],
                $kpi['financial']['ksh']['total_mrr'],
                $kpi['financial']['total_mrr'],
                $kpi['financial']['total_tcv'],
                $kpi['financial']['arpc'],
                $kpi['financial']['usd']['leases_count'],
                $kpi['financial']['ksh']['leases_count'],
                $kpi['financial']['breakdown']['usd_revenue_percentage'],
                $kpi['financial']['breakdown']['ksh_revenue_percentage'],
                $kpi['utilization']['total_distance_km'],
                $kpi['utilization']['total_cores_leased'],
                $kpi['customer_health']['churn_rate'],
                $kpi['contract_health']['upcoming_renewals_90days'],
                $kpi['performance_summary']['rating']
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
