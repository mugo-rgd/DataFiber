<?php

namespace App\Http\Controllers;

use App\Exports\ConversionDataExport;
use App\Models\ConversionData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ConversionDataController extends Controller
{
    public function index(Request $request)
{
    $query = ConversionData::query();

    // Search functionality
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('customer_name', 'LIKE', "%{$search}%")
              ->orWhere('route_name', 'LIKE', "%{$search}%")
              ->orWhere('links_name', 'LIKE', "%{$search}%")
              ->orWhere('link_class', 'LIKE', "%{$search}%")
              ->orWhere('customer_ref', 'LIKE', "%{$search}%")
              ->orWhere('customer_id', 'LIKE', "%{$search}%");
        });
    }

    // Filter by customer - only if value is not empty
    if ($request->filled('customer')) {
        $query->where('customer_name', $request->customer);
    }

    // Filter by link class - only if value is not empty
    if ($request->filled('link_class')) {
        $query->where('link_class', $request->link_class);
    }

    // Filter by contract duration - only if value is numeric and not empty
    if ($request->filled('min_duration') && is_numeric($request->min_duration)) {
        $query->where('contract_duration_yrs', '>=', $request->min_duration);
    }

    // Filter by cores leased - only if value is numeric and not empty
    if ($request->filled('min_cores') && is_numeric($request->min_cores)) {
        $query->where('cores_leased', '>=', $request->min_cores);
    }

    // Filter by distance - only if value is numeric and not empty
    if ($request->filled('min_distance') && is_numeric($request->min_distance)) {
        $query->where('distance_km', '>=', $request->min_distance);
    }

    // Filter by minimum contract value
    if ($request->filled('min_value_usd') && is_numeric($request->min_value_usd)) {
        $query->where('total_contract_value_usd', '>=', $request->min_value_usd);
    }

    // Filter for no duration
    if ($request->has('has_duration') && $request->has_duration === 'false') {
        $query->whereNull('contract_duration_yrs')
              ->orWhere('contract_duration_yrs', 0);
    }

    // Filter for created after date (for recent filter)
    if ($request->filled('created_after')) {
        $query->whereDate('created_at', '>=', $request->created_after);
    }

    // Order by - safe defaults
    $orderBy = $request->get('order_by', 'created_at');
    $orderDirection = $request->get('order_dir', 'desc');

    // Validate order by column exists in model
    $validColumns = [
        'customer_name', 'route_name', 'link_class', 'cores_leased', 'distance_km',
        'monthly_link_value_usd', 'monthly_link_kes', 'contract_duration_yrs',
        'total_contract_value_usd', 'total_contract_value_kes', 'created_at',
        'updated_at', 'customer_ref'
    ];

    if (!in_array($orderBy, $validColumns)) {
        $orderBy = 'created_at';
    }

    $query->orderBy($orderBy, $orderDirection);

    // Pagination
    $perPage = $request->get('per_page', 25); // Reduced from 50 to 25 for better UX
    $data = $query->paginate($perPage);

    // Get unique values for filters
    $customers = ConversionData::select('customer_name')
        ->distinct()
        ->orderBy('customer_name')
        ->pluck('customer_name');

    $linkClasses = ConversionData::select('link_class')
        ->distinct()
        ->whereNotNull('link_class')
        ->orderBy('link_class')
        ->pluck('link_class');

    // Calculate comprehensive totals for summary
    $totalContracts = ConversionData::count();
    $totalMonthlyUsd = ConversionData::sum('monthly_link_value_usd') ?? 0;
    $totalMonthlyKes = ConversionData::sum('monthly_link_value_kes') ?? 0;
    $totalContractUsd = ConversionData::sum('total_contract_value_usd') ?? 0;
    $totalContractKes = ConversionData::sum('total_contract_value_kes') ?? 0;
    $totalDistance = ConversionData::sum('distance_km') ?? 0;
    $totalCores = ConversionData::sum('cores_leased') ?? 0;
    $totalCustomers = ConversionData::distinct('customer_name')->count('customer_name');

    $totals = [
        'total_contracts' => $totalContracts,
        'total_customers' => $totalCustomers,
        'total_monthly_value_usd' => $totalMonthlyUsd,
        'total_monthly_value_kes' => $totalMonthlyKes,
        'total_contract_value_usd' => $totalContractUsd,
        'total_contract_value_kes' => $totalContractKes,
        'avg_monthly_usd' => $totalContracts > 0 ? $totalMonthlyUsd / $totalContracts : 0,
        'avg_monthly_kes' => $totalContracts > 0 ? $totalMonthlyKes / $totalContracts : 0,
        'avg_contract_usd' => $totalContracts > 0 ? $totalContractUsd / $totalContracts : 0,
        'avg_contract_kes' => $totalContracts > 0 ? $totalContractKes / $totalContracts : 0,
        'total_distance' => $totalDistance,
        'avg_distance' => $totalContracts > 0 ? $totalDistance / $totalContracts : 0,
        'total_cores' => $totalCores,
        'avg_cores' => $totalContracts > 0 ? $totalCores / $totalContracts : 0,
    ];

    // Get top customers by contract value (for summary modal)
    $topCustomers = ConversionData::select('customer_name', 'customer_id')
        ->selectRaw('SUM(total_contract_value_usd) as total_contract_value_usd')
        ->selectRaw('SUM(total_contract_value_kes) as total_contract_value_kes')
        ->selectRaw('COUNT(*) as contract_count')
        ->whereNotNull('total_contract_value_usd')
        ->groupBy('customer_name', 'customer_id')
        ->orderByDesc('total_contract_value_usd')
        ->limit(10)
        ->get();

    // Get link class distribution (for summary modal and chart)
    $linkClassSummary = ConversionData::select('link_class')
        ->selectRaw('COUNT(*) as count')
        ->selectRaw('SUM(total_contract_value_usd) as total_value_usd')
        ->whereNotNull('link_class')
        ->groupBy('link_class')
        ->orderByDesc('count')
        ->get();

    // Get duration distribution (for summary modal)
    $durationSummary = ConversionData::selectRaw('
        CASE
            WHEN contract_duration_yrs IS NULL THEN "No Duration"
            WHEN contract_duration_yrs = 1 THEN "1 year"
            WHEN contract_duration_yrs = 2 THEN "2 years"
            WHEN contract_duration_yrs = 3 THEN "3 years"
            WHEN contract_duration_yrs = 4 THEN "4 years"
            WHEN contract_duration_yrs = 5 THEN "5 years"
            WHEN contract_duration_yrs BETWEEN 6 AND 10 THEN "6-10 years"
            WHEN contract_duration_yrs > 10 THEN "10+ years"
            ELSE "Other"
        END as duration_range,
        COUNT(*) as count,
        SUM(total_contract_value_usd) as total_value_usd
    ')
        ->groupBy('duration_range')
        ->orderByRaw('
            CASE duration_range
                WHEN "No Duration" THEN 1
                WHEN "1 year" THEN 2
                WHEN "2 years" THEN 3
                WHEN "3 years" THEN 4
                WHEN "4 years" THEN 5
                WHEN "5 years" THEN 6
                WHEN "6-10 years" THEN 7
                WHEN "10+ years" THEN 8
                ELSE 9
            END
        ')
        ->get();

    return view('conversion-data.index', compact(
        'data',
        'customers',
        'linkClasses',
        'totals',
        'topCustomers',
        'linkClassSummary',
        'durationSummary'
    ));
}

// In Controller
public function customers()
{
    // Single optimized query with pagination
    $customerData = ConversionData::select([
            'customer_name',
            'customer_ref'
        ])
        ->selectRaw('SUM(COALESCE(total_contract_value_usd, 0)) as total_contract_value_usd')
        ->selectRaw('SUM(COALESCE(total_contract_value_kes, 0)) as total_contract_value_kes')
        ->selectRaw('SUM(distance_km) as distance_km')
        ->selectRaw('COUNT(*) as contract_count')
        ->groupBy('customer_name', 'customer_ref')
        ->havingRaw('SUM(COALESCE(total_contract_value_usd, 0)) > 0 OR SUM(COALESCE(total_contract_value_kes, 0)) > 0')
        ->orderByDesc('total_contract_value_usd')
        ->orderByDesc('total_contract_value_kes')
        ->paginate(request('per_page', 50));

    // Separate query for totals
    $totals = ConversionData::selectRaw('
            COUNT(*) as total_contracts,
            SUM(COALESCE(total_contract_value_usd, 0)) as total_usd,
            SUM(COALESCE(total_contract_value_kes, 0)) as total_kes,
            SUM(distance_km) as total_distance
        ')->first();

    // Get top 10 customers for charts (USD only)
    // Get top 10 customers for charts (USD only)
    $topCustomers = ConversionData::select([
            'customer_name',
            'customer_ref'
        ])
        ->selectRaw('SUM(COALESCE(total_contract_value_usd, 0)) as total_contract_value_usd')
        ->selectRaw('SUM(COALESCE(total_contract_value_kes, 0)) as total_contract_value_kes')
        ->selectRaw('COUNT(*) as contract_count')
        ->groupBy('customer_name', 'customer_ref')
        ->havingRaw('SUM(COALESCE(total_contract_value_usd, 0)) > 0')
        ->orderByDesc('total_contract_value_usd')
        ->limit(10)
        ->get();

    // Debug: Check top customers
    // dd($topCustomers); // Uncomment to see top customers data

    // Get currency distribution for entire dataset - FIXED: escaped 'both' keyword
    $currencyDistribution = ConversionData::selectRaw('
            COUNT(DISTINCT CASE WHEN total_contract_value_usd > 0 AND (total_contract_value_kes = 0 OR total_contract_value_kes IS NULL) THEN CONCAT(customer_name, customer_ref) END) as usd_only,
            COUNT(DISTINCT CASE WHEN total_contract_value_kes > 0 AND (total_contract_value_usd = 0 OR total_contract_value_usd IS NULL) THEN CONCAT(customer_name, customer_ref) END) as kes_only,
            COUNT(DISTINCT CASE WHEN total_contract_value_usd > 0 AND total_contract_value_kes > 0 THEN CONCAT(customer_name, customer_ref) END) as `both`
        ')->first();

    return view('conversion-data.summary-view', [
        'customers' => $customerData,
        'totalContracts' => $totals->total_contracts ?? 0,
        'totalValueUSD' => $totals->total_usd ?? 0,
        'totalValueKES' => $totals->total_kes ?? 0,
        'totalDistanceKM' => $totals->total_distance ?? 0,
        'topCustomers' => $topCustomers,
        'currencyDistribution' => $currencyDistribution
    ]);
}
public function bulkDelete(Request $request)
{
    $validated = $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:conversion_data,id',
    ]);

    try {
        $count = ConversionData::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$count} record(s).",
            'count' => $count
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete records: ' . $e->getMessage()
        ], 500);
    }
}


    public function show($id)
    {
        $item = ConversionData::findOrFail($id);
        return view('conversion-data.show', compact('item'));
    }

    public function create()
    {
        return view('conversion-data.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'customer_ref' => 'nullable|string|max:50',
        'customer_id' => 'nullable|string|max:20',
        'customer_name' => 'required|string|max:255',
        'route_name' => 'required|string|max:255',
        'links_name' => 'required|string',
        'cores_leased' => 'nullable|integer|min:0',
        'bandwidth' => 'nullable|string|max:50',
        'distance_km' => 'nullable|numeric|min:0',
        'price_per_core_per_km_per_month_usd' => 'nullable|numeric|min:0',
        'monthly_link_value_usd' => 'nullable|numeric|min:0',
        'monthly_link_kes' => 'nullable|numeric|min:0',
        'link_class' => 'nullable|string|max:50',
        'contract_duration_yrs' => 'nullable|integer|min:0',
        'total_contract_value_usd' => 'nullable|numeric|min:0',
        'total_contract_value_kes' => 'nullable|numeric|min:0',
    ]);

    ConversionData::create($validated);

    return redirect()->route('conversion-data.index')
        ->with('success', 'Fibre link created successfully!');
}


    public function edit($id)
    {
        $item = ConversionData::findOrFail($id);
        return view('conversion-data.edit', compact('item'));
    }

    public function update(Request $request, $id)
{
    $item = ConversionData::findOrFail($id);

    $validated = $request->validate([
        'customer_ref' => 'nullable|string|max:50',
        'customer_id' => 'nullable|string|max:20',
        'customer_name' => 'required|string|max:255',
        'route_name' => 'required|string|max:255',
        'links_name' => 'required|string',
        'cores_leased' => 'nullable|integer|min:0',
        'bandwidth' => 'nullable|string|max:50',
        'distance_km' => 'nullable|numeric|min:0',
        'price_per_core_per_km_per_month_usd' => 'nullable|numeric|min:0',
        'monthly_link_value_usd' => 'nullable|numeric|min:0',
        'monthly_link_value_kes' => 'nullable|numeric|min:0',
        'link_class' => 'nullable|string|max:50',
        'contract_duration_yrs' => 'nullable|integer|min:0',
        'total_contract_value_usd' => 'nullable|numeric|min:0',
        'total_contract_value_kes' => 'nullable|numeric|min:0',
    ]);

    $item->update($validated);

    return redirect()->route('conversion-data.index')
        ->with('success', 'Fibre link updated successfully!');
}
        public function destroy($id)
    {
        $item = ConversionData::findOrFail($id);
        $item->delete();

        return redirect()->route('conversion-data.index')
            ->with('success', 'Record deleted successfully.');
    }

    // API Endpoints for data analysis
    public function apiSummary()
    {
        $summary = [
            'total_records' => ConversionData::count(),
            'total_customers' => ConversionData::distinct('customer_name')->count(),
            'total_monthly_value_usd' => ConversionData::sum('monthly_link_value_usd'),
            'total_contract_value_usd' => ConversionData::sum('total_contract_value_usd'),
            'link_class_distribution' => ConversionData::select('link_class', DB::raw('COUNT(*) as count'))
                ->whereNotNull('link_class')
                ->groupBy('link_class')
                ->get(),
            'top_customers' => ConversionData::select('customer_name', DB::raw('SUM(total_contract_value_usd) as total_contract_value'))
                ->whereNotNull('total_contract_value_usd')
                ->groupBy('customer_name')
                ->orderBy('total_contract_value', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json($summary);
    }


      public function apiCustomerAnalysis($customerName)
    {
        $data = ConversionData::where('customer_name', $customerName)->get();

        $analysis = [
            'customer_name' => $customerName,
            'total_links' => $data->count(),
            'total_monthly_value_usd' => $data->sum('monthly_link_value_usd'),
            'total_contract_value_usd' => $data->sum('total_contract_value_usd'),
            'link_class_summary' => $data->groupBy('link_class')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'monthly_value_usd' => $group->sum('monthly_link_value_usd'),
                ];
            }),
            'contract_duration_summary' => $data->groupBy('contract_duration_yrs')->map(function($group) {
                return $group->count();
            }),
        ];

        return response()->json($analysis);
    }


public function downloadSummaryPdf()
{
    // Get the summary data
    $data = app(self::class)->summaryReportData();

    // Add PDF-specific flag to the data
    $pdfData = array_merge($data, [
        'isPdf' => true,
        'generatedAt' => now()->format('Y-m-d H:i:s'),
        'generatedBy' => auth()->user()->name ?? 'System'
    ]);

    // Configure PDF
    $pdf = Pdf::loadView('conversion-data.summary-report', $pdfData)
        ->setPaper('a4', 'portrait')
        ->setOption('defaultFont', 'sans-serif')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true); // If you have external images


    return $pdf->download(
        'fibre-summary-report-' . now()->format('Y-m-d') . '.pdf'
    );
}

private function summaryReportData(): array
{
    /* =======================
     |  Base Aggregates
     ======================= */
    $totalContracts = ConversionData::count();
    $totalCustomers = ConversionData::distinct('customer_name')->count();

    $totalMonthlyUsd = ConversionData::sum('monthly_link_value_usd') ?? 0;
    $totalMonthlyKes = ConversionData::sum('monthly_link_value_kes') ?? 0;
    $totalContractUsd = ConversionData::sum('total_contract_value_usd') ?? 0;
    $totalContractKes = ConversionData::sum('total_contract_value_kes') ?? 0;

    /* =======================
     |  Summary Block
     ======================= */
    $summary = [
        'total_contracts' => $totalContracts,
        'total_customers' => $totalCustomers,
        'total_monthly_value_usd' => $totalMonthlyUsd,
        'total_monthly_value_kes' => $totalMonthlyKes,
        'total_contract_value_usd' => $totalContractUsd,
        'total_contract_value_kes' => $totalContractKes,
        'avg_monthly_usd' => $totalContracts > 0 ? $totalMonthlyUsd / $totalContracts : 0,
        'avg_monthly_kes' => $totalContracts > 0 ? $totalMonthlyKes / $totalContracts : 0,
    ];

    /* =======================
     |  Link Class Distribution
     ======================= */
    $linkClassDistribution = ConversionData::whereNotNull('link_class')
        ->select('link_class', DB::raw('COUNT(*) as count'))
        ->groupBy('link_class')
        ->orderBy('count', 'desc')
        ->get()
        ->map(fn ($row) => [
            'label' => $row->link_class,
            'count' => (int) $row->count,
        ]);

    /* =======================
     |  Top Customers
     ======================= */
    $topCustomers = ConversionData::whereNotNull('total_contract_value_usd')
        ->selectRaw('
            customer_name,
            SUM(total_contract_value_usd) as total_contract_value_usd,
            SUM(total_contract_value_kes) as total_contract_value_kes
        ')
        ->groupBy('customer_name')
        ->orderByDesc('total_contract_value_usd')
        ->limit(10)
        ->get();

/* =======================
 |  Contract Duration Distribution
 ======================= */
$contractDurationDistribution = ConversionData::whereNotNull('contract_duration_yrs')
    ->selectRaw("
        CASE
            WHEN contract_duration_yrs = 1 THEN '1 year'
            WHEN contract_duration_yrs = 2 THEN '2 years'
            WHEN contract_duration_yrs = 3 THEN '3 years'
            WHEN contract_duration_yrs = 4 THEN '4 years'
            WHEN contract_duration_yrs = 5 THEN '5 years'
            WHEN contract_duration_yrs BETWEEN 6 AND 10 THEN '6–10 years'
            WHEN contract_duration_yrs > 10 THEN '10+ years'
            ELSE 'Other'
        END as label,
        COUNT(*) as count
    ")
    ->groupBy('label')
    ->orderByRaw("
        CASE label
            WHEN '1 year' THEN 1
            WHEN '2 years' THEN 2
            WHEN '3 years' THEN 3
            WHEN '4 years' THEN 4
            WHEN '5 years' THEN 5
            WHEN '6–10 years' THEN 6
            WHEN '10+ years' THEN 7
            ELSE 8
        END
    ")
    ->get()
    ->map(fn ($row) => [
        'label' => $row->label,
        'count' => (int) $row->count,
    ]);

$noDurationCount = ConversionData::whereNull('contract_duration_yrs')->count();

    /* =======================
     |  Monthly Trends (Last 12 Months)
     ======================= */
    $monthlyTrends = ConversionData::where('created_at', '>=', now()->subMonths(12))
        ->selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            SUM(monthly_link_value_usd) as monthly_usd,
            SUM(monthly_link_value_kes) as monthly_kes,
            COUNT(*) as contracts
        ')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    /* =======================
     |  Detailed Statistics
     ======================= */
    $detailedStats = [
        'avg_cores_leased' => ConversionData::avg('cores_leased') ?? 0,
        'total_cores_leased' => ConversionData::sum('cores_leased') ?? 0,
        'avg_distance' => ConversionData::avg('distance_km') ?? 0,
        'total_distance' => ConversionData::sum('distance_km') ?? 0,
        'avg_contract_duration' => ConversionData::avg('contract_duration_yrs') ?? 0,
        'max_contract_duration' => ConversionData::max('contract_duration_yrs') ?? 0,
        'min_contract_duration' => ConversionData::min('contract_duration_yrs') ?? 0,
        'contracts_with_pricing' => ConversionData::whereNotNull('monthly_link_value_usd')->count(),
    ];

    /* =======================
     |  Return Unified Dataset
     ======================= */
    return compact(
        'summary',
        'linkClassDistribution',
        'topCustomers',
        'contractDurationDistribution',
        'monthlyTrends',
        'detailedStats',
        'noDurationCount'
    );
}



public function summaryReport()
{
    $cacheKey = 'conversion.summary.report';

    $data = Cache::remember($cacheKey, now()->addMinutes(30), function () {

        // ========================
        // Core Summary
        // ========================
        $summary = ConversionData::query()
            ->selectRaw('
                COUNT(*) as total_contracts,
                COUNT(DISTINCT customer_name) as total_customers,
                COALESCE(SUM(monthly_link_value_usd), 0) as total_monthly_value_usd,
                COALESCE(SUM(monthly_link_value_kes), 0) as total_monthly_value_kes,
                COALESCE(SUM(total_contract_value_usd), 0) as total_contract_value_usd,
                COALESCE(SUM(total_contract_value_kes), 0) as total_contract_value_kes
            ')
            ->first()
            ->toArray();

        // Pre-computed averages (Blade stays dumb)
        $summary['avg_monthly_usd'] = $summary['total_contracts'] > 0
            ? $summary['total_monthly_value_usd'] / $summary['total_contracts']
            : 0;

        $summary['avg_monthly_kes'] = $summary['total_contracts'] > 0
            ? $summary['total_monthly_value_kes'] / $summary['total_contracts']
            : 0;

        // ========================
        // Link Class Distribution
        // ========================
        $linkClassDistribution = ConversionData::query()
            ->whereNotNull('link_class')
            ->select('link_class', DB::raw('COUNT(*) as count'))
            ->groupBy('link_class')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->link_class,
                'count' => (int) $row->count,
            ]);

        // ========================
        // Top Customers
        // ========================
        $topCustomers = ConversionData::query()
            ->whereNotNull('total_contract_value_usd')
            ->select(
                'customer_name',
                DB::raw('SUM(total_contract_value_usd) as total_contract_value_usd'),
                DB::raw('SUM(total_contract_value_kes) as total_contract_value_kes')
            )
            ->groupBy('customer_name')
            ->orderByDesc('total_contract_value_usd')
            ->limit(10)
            ->get();

        // ========================
        // Contract Duration Distribution (Ranges)
        // ========================
        $contractDurationDistribution = ConversionData::query()
            ->whereNotNull('contract_duration_yrs')
            ->selectRaw('
                CASE
                    WHEN contract_duration_yrs = 1 THEN "1 year"
                    WHEN contract_duration_yrs = 2 THEN "2 years"
                    WHEN contract_duration_yrs = 3 THEN "3 years"
                    WHEN contract_duration_yrs = 4 THEN "4 years"
                    WHEN contract_duration_yrs = 5 THEN "5 years"
                    WHEN contract_duration_yrs BETWEEN 6 AND 10 THEN "6–10 years"
                    WHEN contract_duration_yrs > 10 THEN "10+ years"
                    ELSE "Other"
                END as label,
                COUNT(*) as count
            ')
            ->groupBy('label')
            ->orderByRaw('
                MIN(contract_duration_yrs)
            ')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->label,
                'count' => (int) $row->count,
            ]);

        $noDurationCount = ConversionData::whereNull('contract_duration_yrs')->count();

        // ========================
        // Monthly Trends (Last 12 Months)
        // ========================
        $monthlyTrends = ConversionData::query()
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COALESCE(SUM(monthly_link_value_usd), 0) as monthly_usd,
                COALESCE(SUM(monthly_link_value_kes), 0) as monthly_kes,
                COUNT(*) as contracts
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ========================
        // Detailed Statistics
        // ========================
        $detailedStats = ConversionData::query()
            ->selectRaw('
                COALESCE(AVG(cores_leased), 0) as avg_cores_leased,
                COALESCE(SUM(cores_leased), 0) as total_cores_leased,
                COALESCE(AVG(distance_km), 0) as avg_distance,
                COALESCE(SUM(distance_km), 0) as total_distance,
                COALESCE(AVG(contract_duration_yrs), 0) as avg_contract_duration,
                COALESCE(MAX(contract_duration_yrs), 0) as max_contract_duration,
                COALESCE(MIN(contract_duration_yrs), 0) as min_contract_duration,
                SUM(CASE WHEN monthly_link_value_usd IS NOT NULL THEN 1 ELSE 0 END) as contracts_with_pricing
            ')
            ->first()
            ->toArray();

        return compact(
            'summary',
            'linkClassDistribution',
            'topCustomers',
            'contractDurationDistribution',
            'monthlyTrends',
            'detailedStats',
            'noDurationCount'
        );
    });

    return view('conversion-data.summary-report', $data);
}

// In ConversionDataController.php

public function exportExcel(Request $request)
{
    return $this->export($request, 'excel');
}

public function exportCsv(Request $request)
{
    return $this->export($request, 'csv');
}

public function exportPdf(Request $request)
{
    return $this->export($request, 'pdf');
}

public function export(Request $request, $format = 'excel')
{
    // Apply the same filters as the index method
    $query = ConversionData::query();

    // Apply search filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('customer_name', 'LIKE', "%{$search}%")
              ->orWhere('route_name', 'LIKE', "%{$search}%")
              ->orWhere('links_name', 'LIKE', "%{$search}%")
              ->orWhere('link_class', 'LIKE', "%{$search}%")
              ->orWhere('customer_ref', 'LIKE', "%{$search}%");
        });
    }

    // Apply other filters (same as index method)
    if ($request->filled('customer')) {
        $query->where('customer_name', $request->customer);
    }

    if ($request->filled('link_class')) {
        $query->where('link_class', $request->link_class);
    }

    if ($request->filled('min_duration') && is_numeric($request->min_duration)) {
        $query->where('contract_duration_yrs', '>=', $request->min_duration);
    }

    if ($request->filled('min_cores') && is_numeric($request->min_cores)) {
        $query->where('cores_leased', '>=', $request->min_cores);
    }

    if ($request->filled('min_distance') && is_numeric($request->min_distance)) {
        $query->where('distance_km', '>=', $request->min_distance);
    }

    // Apply selected IDs filter (for bulk export)
    if ($request->filled('selected_ids')) {
        $selectedIds = explode(',', $request->selected_ids);
        $query->whereIn('id', $selectedIds);
    }

    $data = $query->orderBy('created_at', 'desc')->get();

    // Generate filename with timestamp
    $filename = 'fibre-links-export-' . now()->format('Y-m-d-H-i-s');

    switch ($format) {
        case 'excel':
            return Excel::download(new ConversionDataExport($data), $filename . '.xlsx');

        case 'csv':
            return Excel::download(new ConversionDataExport($data), $filename . '.csv');

        case 'pdf':
            $pdf = Pdf::loadView('conversion-data.export-pdf', [
                'data' => $data,
                'filters' => $request->all()
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');

        default:
            return redirect()->route('conversion-data.index')
                ->with('error', 'Invalid export format specified.');
    }
}

}
