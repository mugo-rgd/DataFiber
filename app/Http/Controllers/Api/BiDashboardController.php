<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractReportSnapshot;
use App\Models\DebtAgingSnapshot;
use App\Models\ExecutiveInsight;
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

class BiDashboardController extends Controller
{
    private function resolveSnapshotDate(Request $request): string
    {
        $date = $request->date
            ? Carbon::parse($request->date)->toDateString()
            : now()->toDateString();

        $snapshot = ExecutiveKpiSnapshot::whereDate('snapshot_date', '<=', $date)
            ->latest('snapshot_date')
            ->first();

        return $snapshot
            ? Carbon::parse($snapshot->snapshot_date)->toDateString()
            : $date;
    }

    public function executiveSummary(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'kpis' => ExecutiveKpiSnapshot::whereDate('snapshot_date', $snapshotDate)->first(),
            'insights' => ExecutiveInsight::whereDate('snapshot_date', $snapshotDate)
                ->orderByRaw("FIELD(severity, 'critical', 'warning', 'info')")
                ->get(),
            'forecasts' => RevenueForecast::whereDate('forecast_date', $snapshotDate)->get(),
        ]);
    }

    public function revenue(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        $periodStart = Carbon::parse($snapshotDate)->startOfMonth()->toDateString();
        $periodEnd = Carbon::parse($snapshotDate)->endOfMonth()->toDateString();

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'data' => RevenueReportSnapshot::whereDate('period_start', $periodStart)
                ->whereDate('period_end', $periodEnd)
                ->get(),
        ]);
    }

    public function debtAging(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'data' => DebtAgingSnapshot::with('customer:id,name,email')
                ->whereDate('snapshot_date', $snapshotDate)
                ->get(),
        ]);
    }

    public function quotations(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'data' => QuotationPipelineSnapshot::whereDate('snapshot_date', $snapshotDate)->get(),
        ]);
    }

    public function contracts(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'data' => ContractReportSnapshot::whereDate('snapshot_date', $snapshotDate)->get(),
        ]);
    }

    public function leases(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'data' => LeaseReportSnapshot::whereDate('snapshot_date', $snapshotDate)->get(),
        ]);
    }

    public function fibreUtilization(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'data' => FiberUtilizationSnapshot::whereDate('snapshot_date', $snapshotDate)->get(),
        ]);
    }

    public function slaNetwork(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'data' => SlaNetworkSnapshot::with([
                    'customer:id,name,email',
                    'lease:id,lease_number',
                ])
                ->whereDate('snapshot_date', $snapshotDate)
                ->get(),
        ]);
    }

    public function topCustomers(Request $request)
    {
        $snapshotDate = $this->resolveSnapshotDate($request);

        return response()->json([
            'snapshot_date' => $snapshotDate,
            'data' => TopCustomerSnapshot::with('customer:id,name,email')
                ->whereDate('snapshot_date', $snapshotDate)
                ->get(),
        ]);
    }
}
