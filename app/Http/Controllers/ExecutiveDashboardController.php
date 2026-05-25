<?php

namespace App\Http\Controllers;

use App\Models\ContractReportSnapshot;
use App\Models\DebtAgingSnapshot;
use App\Models\ExecutiveKpiSnapshot;
use App\Models\FiberUtilizationSnapshot;
use App\Models\LeaseReportSnapshot;
use App\Models\QuotationPipelineSnapshot;
use App\Models\RevenueForecast;
use App\Models\RevenueReportSnapshot;
use App\Models\SlaNetworkSnapshot;
use App\Models\TopCustomerSnapshot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ExecutiveDashboardExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use App\Models\ExecutiveInsight;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'technical_admin','accountmanager_admin','system_admin', 'executive', 'finance', 'management'])) {
        abort(403, 'You are not authorized to access the executive dashboard.');
    }
        $requestedDate = $request->date
            ? Carbon::parse($request->date)->toDateString()
            : now()->toDateString();

        $latestKpi = ExecutiveKpiSnapshot::whereDate('snapshot_date', '<=', $requestedDate)
            ->latest('snapshot_date')
            ->first();

        if (!$latestKpi) {
            return view('executive.dashboard', [
                'kpis' => null,
                'message' => 'No executive snapshot found. Run php artisan reports:generate-executive first.',
                'snapshotDate' => null,
                'periodStart' => null,
                'periodEnd' => null,
                'debtAging' => collect(),
                'revenue' => collect(),
                'topCustomers' => collect(),
                'quotations' => collect(),
                'contracts' => collect(),
                'leases' => collect(),
                'fiberUtilization' => collect(),
                'slaNetwork' => collect(),
                'summary' => [],
            ]);
        }

        $snapshotDate = Carbon::parse($latestKpi->snapshot_date)->toDateString();
        $periodStart = Carbon::parse($snapshotDate)->startOfMonth()->toDateString();
        $periodEnd = Carbon::parse($snapshotDate)->endOfMonth()->toDateString();

        $debtAging = DebtAgingSnapshot::with(['customer:id,name,email'])
            ->whereDate('snapshot_date', $snapshotDate)
            ->orderByDesc('total_outstanding')
            ->get();


        $revenue = RevenueReportSnapshot::whereDate('period_start', $periodStart)
            ->whereDate('period_end', $periodEnd)
            ->orderByDesc('billed_amount')
            ->get();

        $topCustomers = TopCustomerSnapshot::with(['customer:id,name,email'])
            ->whereDate('snapshot_date', $snapshotDate)
            ->orderByDesc('revenue')
            ->get();

        $quotations = QuotationPipelineSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->orderByDesc('pipeline_value')
            ->get();

        $contracts = ContractReportSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->orderByDesc('contract_value')
            ->get();

        $leases = LeaseReportSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->orderByDesc('monthly_revenue')
            ->get();

        $fiberUtilization = FiberUtilizationSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->orderByDesc('utilization_percent')
            ->get();

        $slaNetwork = SlaNetworkSnapshot::with([
                'customer:id,name,email',
                'lease:id,lease_number',
            ])
            ->whereDate('snapshot_date', $snapshotDate)
            ->orderBy('uptime_percent')
            ->get();

        $summary = [
            'debt_ksh' => $debtAging->where('currency', 'KSH')->sum('total_outstanding'),
            'debt_usd' => $debtAging->where('currency', 'USD')->sum('total_outstanding'),

            'revenue_ksh' => $revenue->where('currency', 'KSH')->sum('billed_amount'),
            'revenue_usd' => $revenue->where('currency', 'USD')->sum('billed_amount'),

            'paid_ksh' => $revenue->where('currency', 'KSH')->sum('paid_amount'),
            'paid_usd' => $revenue->where('currency', 'USD')->sum('paid_amount'),

            'outstanding_ksh' => $revenue->where('currency', 'KSH')->sum('outstanding_amount'),
            'outstanding_usd' => $revenue->where('currency', 'USD')->sum('outstanding_amount'),

            'quotation_ksh' => $quotations->where('currency', 'KSH')->sum('pipeline_value'),
            'quotation_usd' => $quotations->where('currency', 'USD')->sum('pipeline_value'),

            'top_customer_count' => $topCustomers->count(),
            'network_count' => $fiberUtilization->count(),
            'sla_breaches' => $slaNetwork->sum('sla_breaches'),
        ];



        if ($request->export === 'pdf') {
    $pdf = Pdf::loadView('executive.dashboard-pdf', [
        'kpis' => $latestKpi,
        'snapshotDate' => $snapshotDate,
        'periodStart' => $periodStart,
        'periodEnd' => $periodEnd,
        'debtAging' => $debtAging,
        'revenue' => $revenue,
        'topCustomers' => $topCustomers,
        'quotations' => $quotations,
        'contracts' => $contracts,
        'leases' => $leases,
        'fiberUtilization' => $fiberUtilization,
        'slaNetwork' => $slaNetwork,
        'summary' => $summary,
    ])->setPaper('a4', 'landscape');

    return $pdf->download('executive-dashboard-' . $snapshotDate . '.pdf');
}

if ($request->export === 'excel') {
    return Excel::download(
        new ExecutiveDashboardExport(
            $latestKpi,
            $snapshotDate,
            $periodStart,
            $periodEnd,
            $debtAging,
            $revenue,
            $topCustomers,
            $quotations,
            $contracts,
            $leases,
            $fiberUtilization,
            $slaNetwork,
            $summary
        ),
        'executive-dashboard-' . $snapshotDate . '.xlsx'
    );
}
if ($request->export === 'csv') {
    $filename = 'executive-dashboard-' . $snapshotDate . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function () use ($debtAging, $revenue, $topCustomers) {
        $file = fopen('php://output', 'w');

        fputcsv($file, ['EXECUTIVE DASHBOARD']);

        fputcsv($file, []);
        fputcsv($file, ['DEBT AGING']);
        fputcsv($file, [
            'Customer',
            'Currency',
            'Current',
            '1-30',
            '31-60',
            '61-90',
            '91-120',
            '120+',
            'Total',
        ]);

        foreach ($debtAging as $row) {
            fputcsv($file, [
                $row->customer->name ?? 'N/A',
                $row->currency,
                $row->current_amount,
                $row->days_1_30,
                $row->days_31_60,
                $row->days_61_90,
                $row->days_91_120,
                $row->days_120_plus,
                $row->total_outstanding,
            ]);
        }

        fputcsv($file, []);
        fputcsv($file, ['REVENUE DETAILS']);
        fputcsv($file, [
            'Billing ID',
            'Lease ID',
            'Service Type',
            'Currency',
            'Billed',
            'Paid',
            'Outstanding',
        ]);

        foreach ($revenue as $row) {
            fputcsv($file, [
                $row->billing_id,
                $row->lease_id,
                $row->service_type,
                $row->currency,
                $row->billed_amount,
                $row->paid_amount,
                $row->outstanding_amount,
            ]);
        }

        fputcsv($file, []);
        fputcsv($file, ['TOP CUSTOMERS']);
        fputcsv($file, [
            'Customer',
            'Currency',
            'Revenue',
            'Outstanding',
            'Contribution %',
            'Risk',
        ]);

        foreach ($topCustomers as $row) {
            fputcsv($file, [
                $row->customer->name ?? 'N/A',
                $row->currency,
                $row->revenue,
                $row->outstanding_amount,
                $row->revenue_contribution_percent,
                strtoupper($row->risk_level),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

$insightDate = ExecutiveInsight::whereDate('snapshot_date', '<=', $snapshotDate)
    ->latest('snapshot_date')
    ->value('snapshot_date');

$insights = ExecutiveInsight::whereDate('snapshot_date', $insightDate)
    ->orderByRaw("FIELD(severity, 'critical', 'warning', 'info')")
    ->latest()
    ->get();
     $revenueForecasts = RevenueForecast::whereDate('forecast_date', $insightDate)
    ->orderBy('currency')
    ->get();

            return view('executive.dashboard', [
            'kpis' => $latestKpi,
            'snapshotDate' => $snapshotDate,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'debtAging' => $debtAging,
            'revenue' => $revenue,
            'topCustomers' => $topCustomers,
            'quotations' => $quotations,
            'contracts' => $contracts,
            'leases' => $leases,
            'fiberUtilization' => $fiberUtilization,
            'slaNetwork' => $slaNetwork,
            'summary' => $summary,
            'message' => null,
'insights' => $insights,
'insightDate' => $insightDate,
'revenueForecasts' => $revenueForecasts,
        ]);
    }

    public function refresh(Request $request)
{
      if (!in_array(auth()->user()->role, ['admin', 'technical_admin','accountmanager_admin','system_admin', 'executive', 'finance', 'management'])) {
        abort(403, 'You are not authorized to access the executive dashboard.');
    }
    $date = $request->date ?: now()->toDateString();

    Artisan::call('reports:generate-executive', [
        '--date' => $date,
    ]);

    return redirect()
        ->route('executive.dashboard', ['date' => $date])
        ->with('success', 'Executive dashboard refreshed successfully.');
}



public function gis()
{
    // Get nodes
    $nodes = DB::table('fiber_nodes')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get()
        ->map(function ($node) {
            return [
                'id' => $node->id,
                'latitude' => (float) $node->latitude,
                'longitude' => (float) $node->longitude,
                'node_name' => $node->node_name ?? 'Node',
                'node_type' => $node->node_type ?? 'Unknown',
                'region' => $node->region ?? 'N/A'
            ];
        });

    // Get segments
    $segments = DB::table('fiber_segments')
        ->whereNotNull('source_lat')
        ->whereNotNull('source_lon')
        ->whereNotNull('dest_lat')
        ->whereNotNull('dest_lon')
        ->get()
        ->map(function ($segment) {
            return [
                'id' => $segment->id,
                'source_lat' => (float) $segment->source_lat,
                'source_lon' => (float) $segment->source_lon,
                'dest_lat' => (float) $segment->dest_lat,
                'dest_lon' => (float) $segment->dest_lon,
                'network_id' => $segment->network_id ?? 'N/A',
                'segment_id' => $segment->segment_id ?? 'N/A',
                'source_name' => $segment->source_name ?? 'N/A',
                'destination_name' => $segment->destination_name ?? 'N/A',
                'distance_km' => (float) ($segment->distance_km ?? 0),
                'fiber_cores' => (int) ($segment->fiber_cores ?? 0),
                'link_type' => $segment->link_type ?? 'N/A',
                'status' => $segment->status ?? 'Active'
            ];
        });

    // Get stations with utilization calculation
    $stations = DB::table('fibre_stations')
        ->select(
            'id',
            'name',
            'lat',
            'lng',
            'owner',
            'darkFibreCores',
            'usedCores',
            'availableCores',
            'fibreStatus',
            'area'
        )
        ->whereNotNull('lat')
        ->whereNotNull('lng')
        ->get()
        ->map(function ($station) {
            $total = (float) ($station->darkFibreCores ?? 0);
            $used = (float) ($station->usedCores ?? 0);

            $available = max($total - $used, 0);
            $utilizationPercent = $total > 0 ? round(($used / $total) * 100, 2) : 0;

            return [
                'id' => $station->id,
                'name' => $station->name ?? 'Unknown Station',
                'lat' => (float) $station->lat,
                'lng' => (float) $station->lng,
                'owner' => $station->owner ?? 'N/A',
                'darkFibreCores' => $total,
                'usedCores' => $used,
                'availableCores' => $available,
                'utilizationPercent' => $utilizationPercent,
                'fibreStatus' => $station->fibreStatus ?? 'Active',
                'area' => $station->area ?? 'N/A'
            ];
        });

    return view('executive.gis', [
        'nodes' => $nodes->values()->toArray(),
        'segments' => $segments->values()->toArray(),
        'stations' => $stations->values()->toArray(),
    ]);
}
}
