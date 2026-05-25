<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\ConsolidatedBilling;
use App\Models\DebtAgingSnapshot;
use App\Models\ExecutiveKpiSnapshot;
use App\Models\Lease;
use App\Models\Quotation;
use App\Models\RevenueReportSnapshot;
use App\Models\QuotationPipelineSnapshot;
use App\Models\ContractReportSnapshot;
use App\Models\LeaseReportSnapshot;
use App\Models\FiberUtilizationSnapshot;
use App\Models\SlaNetworkSnapshot;
use App\Models\TopCustomerSnapshot;
use App\Models\FiberNetwork;
use App\Models\FiberSegment;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\ExecutiveInsightService;
use App\Services\RevenueForecastService;
use App\Services\FibreCapacityService;

class GenerateExecutiveReports extends Command
{
    protected $signature = 'reports:generate-executive {--date=}';

    protected $description = 'Generate executive dashboard report snapshots';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now();

        $snapshotDate = $date->toDateString();

        $this->generateExecutiveKpis($snapshotDate);
        $this->generateDebtAging($snapshotDate);

        $this->info("Executive reports generated for {$snapshotDate}");
        $this->generateExecutiveKpis($snapshotDate);
$this->generateDebtAging($snapshotDate);
$this->generateRevenueSnapshots($snapshotDate);
$this->generateQuotationPipeline($snapshotDate);
$this->generateContractSnapshots($snapshotDate);
$this->generateLeaseSnapshots($snapshotDate);
$this->generateFiberUtilization($snapshotDate);
$this->generateSlaNetworkSnapshots($snapshotDate);
$this->generateTopCustomers($snapshotDate);

        return Command::SUCCESS;
    }

    private function generateExecutiveKpis(string $snapshotDate): void
    {
        $revenueKsh = ConsolidatedBilling::where('currency', 'KSH')
            ->sum('paid_amount');

        $revenueUsd = ConsolidatedBilling::where('currency', 'USD')
            ->sum('paid_amount');

        $receivableKsh = ConsolidatedBilling::where('currency', 'KSH')
            ->whereRaw('(total_amount - paid_amount) > 0')
            ->selectRaw('SUM(total_amount - paid_amount) as total')
            ->value('total') ?? 0;

        $receivableUsd = ConsolidatedBilling::where('currency', 'USD')
            ->whereRaw('(total_amount - paid_amount) > 0')
            ->selectRaw('SUM(total_amount - paid_amount) as total')
            ->value('total') ?? 0;

        $overdueKsh = ConsolidatedBilling::where('currency', 'KSH')
            ->whereDate('due_date', '<', now())
            ->whereRaw('(total_amount - paid_amount) > 0')
            ->selectRaw('SUM(total_amount - paid_amount) as total')
            ->value('total') ?? 0;

        $overdueUsd = ConsolidatedBilling::where('currency', 'USD')
            ->whereDate('due_date', '<', now())
            ->whereRaw('(total_amount - paid_amount) > 0')
            ->selectRaw('SUM(total_amount - paid_amount) as total')
            ->value('total') ?? 0;

        ExecutiveKpiSnapshot::updateOrCreate(
            ['snapshot_date' => $snapshotDate],
            [
                'revenue_ksh' => $revenueKsh,
                'revenue_usd' => $revenueUsd,

                'accounts_receivable_ksh' => $receivableKsh,
                'accounts_receivable_usd' => $receivableUsd,

                'overdue_ksh' => $overdueKsh,
                'overdue_usd' => $overdueUsd,

                'quotation_pipeline_ksh' => Quotation::where('currency', 'KSH')
                    ->whereIn('status', ['draft', 'sent', 'pending', 'negotiation'])
                    ->sum('total_amount'),

                'quotation_pipeline_usd' => Quotation::where('currency', 'USD')
                    ->whereIn('status', ['draft', 'sent', 'pending', 'negotiation'])
                    ->sum('total_amount'),

                'active_leases' => Lease::where('status', 'active')->count(),

                'active_contracts' => Contract::where('status', 'active')->count(),

                'expiring_contracts_30_days' => Lease::where('status', 'active')
                    ->whereBetween('end_date', [now(), now()->copy()->addDays(30)])
                    ->count(),

                'expiring_contracts_60_days' => Lease::where('status', 'active')
                    ->whereBetween('end_date', [now(), now()->copy()->addDays(60)])
                    ->count(),

                'expiring_contracts_90_days' => Lease::where('status', 'active')
                    ->whereBetween('end_date', [now(), now()->copy()->addDays(90)])
                    ->count(),
            ]
        );
    }

    private function generateDebtAging(string $snapshotDate): void
    {
        DebtAgingSnapshot::where('snapshot_date', $snapshotDate)->delete();

        foreach (['KSH', 'USD'] as $currency) {
            $customers = ConsolidatedBilling::where('currency', $currency)
                ->whereRaw('(total_amount - paid_amount) > 0')
                ->select('user_id')
                ->distinct()
                ->pluck('user_id');

            foreach ($customers as $customerId) {
                $billings = ConsolidatedBilling::where('user_id', $customerId)
                    ->where('currency', $currency)
                    ->whereRaw('(total_amount - paid_amount) > 0')
                    ->get();

                $current = 0;
                $d1_30 = 0;
                $d31_60 = 0;
                $d61_90 = 0;
                $d91_120 = 0;
                $d120Plus = 0;

                foreach ($billings as $billing) {
                    $outstanding = $billing->total_amount - $billing->paid_amount;

                    $days = $billing->due_date
                        ? Carbon::parse($billing->due_date)->diffInDays(now(), false)
                        : 0;

                    if ($days <= 0) {
                        $current += $outstanding;
                    } elseif ($days <= 30) {
                        $d1_30 += $outstanding;
                    } elseif ($days <= 60) {
                        $d31_60 += $outstanding;
                    } elseif ($days <= 90) {
                        $d61_90 += $outstanding;
                    } elseif ($days <= 120) {
                        $d91_120 += $outstanding;
                    } else {
                        $d120Plus += $outstanding;
                    }
                }

                DebtAgingSnapshot::create([
                    'snapshot_date' => $snapshotDate,
                    'customer_id' => $customerId,
                    'currency' => $currency,
                    'current_amount' => $current,
                    'days_1_30' => $d1_30,
                    'days_31_60' => $d31_60,
                    'days_61_90' => $d61_90,
                    'days_91_120' => $d91_120,
                    'days_120_plus' => $d120Plus,
                    'total_outstanding' => $billings->sum(fn ($billing) =>
                        $billing->total_amount - $billing->paid_amount
                    ),
                    'billing_count' => $billings->count(),
                    'overdue_count' => $billings->filter(fn ($billing) =>
                        $billing->due_date &&
                        Carbon::parse($billing->due_date)->isPast() &&
                        ($billing->total_amount - $billing->paid_amount) > 0
                    )->count(),
                ]);
            }
        }
    }

 private function generateRevenueSnapshots(string $snapshotDate): void
{
    $periodStart = Carbon::parse($snapshotDate)->startOfMonth()->toDateString();
    $periodEnd = Carbon::parse($snapshotDate)->endOfMonth()->toDateString();

    RevenueReportSnapshot::where('period_start', $periodStart)
        ->where('period_end', $periodEnd)
        ->delete();

    DB::table('consolidated_billings as cb')
        ->join('billing_line_items as bli', 'bli.consolidated_billing_id', '=', 'cb.id')
        ->leftJoin('leases as les', 'bli.lease_id', '=', 'les.id')
        ->select(
            'cb.id as billing_id',
            'cb.user_id',
            'cb.currency as billing_currency',
            'cb.total_amount',
            'cb.paid_amount as billing_paid_amount',
            'cb.billing_date',

            'bli.lease_id',
            'bli.description',
            'bli.currency as line_currency',
            'bli.amount as line_amount',
            'bli.paid_amount as line_paid_amount',

            'les.service_type'
        )
        ->whereDate('cb.billing_date', '>=', $periodStart)
        ->whereDate('cb.billing_date', '<=', $periodEnd)
        ->orderBy('cb.id')
        ->chunk(200, function ($items) use ($periodStart, $periodEnd) {
            foreach ($items as $item) {
                $lineAmount = (float) ($item->line_amount ?? 0);
                $billingTotal = (float) ($item->total_amount ?? 0);
                $billingPaid = (float) ($item->billing_paid_amount ?? 0);

                if ($item->line_paid_amount !== null) {
                    $linePaid = (float) $item->line_paid_amount;
                } else {
                    $linePaid = $billingTotal > 0
                        ? ($lineAmount / $billingTotal) * $billingPaid
                        : 0;
                }

                $lineOutstanding = max($lineAmount - $linePaid, 0);

                RevenueReportSnapshot::create([
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,

                    'customer_id' => $item->user_id,
                    'lease_id' => $item->lease_id,
                    'billing_id' => $item->billing_id,

                    'currency' => $item->line_currency ?? $item->billing_currency,

                    'billed_amount' => $lineAmount,
                    'paid_amount' => $linePaid,
                    'outstanding_amount' => $lineOutstanding,

                    'service_type' => $item->service_type ?? $item->description,
                    'region' => null,
                ]);
            }
        });
}
private function generateQuotationPipeline(string $snapshotDate): void
{
    QuotationPipelineSnapshot::where('snapshot_date', $snapshotDate)->delete();

    foreach (['KSH', 'USD'] as $currency) {
        $total = Quotation::where('currency', $currency)->count();

        $won = Quotation::where('currency', $currency)
            ->whereIn('status', ['won', 'accepted', 'approved'])
            ->count();

        $conversionRate = $total > 0 ? ($won / $total) * 100 : 0;

        $statuses = Quotation::where('currency', $currency)
            ->select('status', DB::raw('COUNT(*) as quotation_count'), DB::raw('SUM(total_amount) as pipeline_value'))
            ->groupBy('status')
            ->get();

        foreach ($statuses as $row) {
            QuotationPipelineSnapshot::create([
                'snapshot_date' => $snapshotDate,
                'currency' => $currency,
                'stage' => $row->status,
                'status' => $row->status,
                'quotation_count' => $row->quotation_count,
                'pipeline_value' => $row->pipeline_value ?? 0,
                'won_value' => in_array($row->status, ['won', 'accepted', 'approved'])
                    ? ($row->pipeline_value ?? 0)
                    : 0,
                'lost_value' => in_array($row->status, ['lost', 'rejected', 'declined'])
                    ? ($row->pipeline_value ?? 0)
                    : 0,
                'conversion_rate_percent' => $conversionRate,
            ]);
        }
    }
}

private function generateContractSnapshots(string $snapshotDate): void
{
    ContractReportSnapshot::where('snapshot_date', $snapshotDate)->delete();

    foreach (['KSH', 'USD'] as $currency) {
        $statuses = Lease::where('currency', $currency)
            ->select('status', DB::raw('COUNT(*) as contract_count'), DB::raw('SUM(total_contract_value) as contract_value'))
            ->groupBy('status')
            ->get();

        foreach ($statuses as $row) {
            ContractReportSnapshot::create([
                'snapshot_date' => $snapshotDate,
                'currency' => $currency,
                'status' => $row->status,
                'contract_count' => $row->contract_count,
                'contract_value' => $row->contract_value ?? 0,

                'expiring_30_days' => lease::where('currency', $currency)
                    ->where('status', $row->status)
                    ->whereBetween('end_date', [now(), now()->copy()->addDays(30)])
                    ->count(),

                'expiring_60_days' => lease::where('currency', $currency)
                    ->where('status', $row->status)
                    ->whereBetween('end_date', [now(), now()->copy()->addDays(60)])
                    ->count(),

                'expiring_90_days' => lease::where('currency', $currency)
                    ->where('status', $row->status)
                    ->whereBetween('end_date', [now(), now()->copy()->addDays(90)])
                    ->count(),

                'renewal_revenue_at_risk' => lease::where('currency', $currency)
                    ->where('status', $row->status)
                    ->whereBetween('end_date', [now(), now()->copy()->addDays(90)])
                    ->sum('total_contract_value'),
            ]);
        }
    }
}

private function generateLeaseSnapshots(string $snapshotDate): void
{
    LeaseReportSnapshot::where('snapshot_date', $snapshotDate)->delete();

    foreach (['KSH', 'USD'] as $currency) {
        $rows = Lease::where('currency', $currency)
            ->select(
                'status',
                'service_type',
                'county_id',
                DB::raw('COUNT(*) as lease_count'),
                DB::raw('SUM(monthly_cost) as monthly_revenue'),
                DB::raw('SUM(total_contract_value) as contract_value'),
                DB::raw('SUM(distance_km) as leased_distance_km'),
                DB::raw('SUM(cores_required) as leased_cores')
            )
            ->groupBy('status', 'service_type', 'county_id')
            ->get();

        foreach ($rows as $row) {
            LeaseReportSnapshot::create([
                'snapshot_date' => $snapshotDate,
                'currency' => $currency,
                'service_type' => $row->service_type,
                'status' => $row->status,
                'region' => $row->county_id,
                'lease_count' => $row->lease_count,
                'monthly_revenue' => $row->monthly_revenue ?? 0,
                'contract_value' => $row->contract_value ?? 0,
                'leased_distance_km' => $row->leased_distance_km ?? 0,
                'leased_cores' => $row->leased_cores ?? 0,
            ]);
        }
    }
}

private function generateFiberUtilization(string $snapshotDate): void
{
    FiberUtilizationSnapshot::where('snapshot_date', $snapshotDate)->delete();

    $segments = FiberNetwork::select(
            'network_id',
            'network_name',
            'region',
            DB::raw('SUM(total_distance_km) as total_fibre_km'),
            DB::raw('SUM(fiber_cores) as total_cores'),
            DB::raw('SUM(fiber_cores) as used_cores')
        )
        ->groupBy('network_id', 'network_name', 'region')
        ->get();

        $leased_cores = Lease::where('status', 'active')
                    ->sum('cores_required');

    foreach ($segments as $segment) {
        $availableCores = max(($segment->total_cores ?? 0) - ($leased_cores ?? 0), 0);

        $utilization = ($segment->total_cores ?? 0) > 0
            ? (($leased_cores ?? 0) / $segment->total_cores) * 100
            : 0;

        $capacityStatus = $utilization >= 90
            ? 'saturated'
            : ($utilization >= 70 ? 'warning' : 'normal');

        FiberUtilizationSnapshot::create([
            'snapshot_date' => $snapshotDate,
            'network_id' => $segment->network_id,
            'route_name' => $segment->network_name,
            'region' => $segment->region,
            'total_fibre_km' => $segment->total_fibre_km ?? 0,
            'leased_fibre_km' => 0,
            'available_fibre_km' => $segment->total_fibre_km ?? 0,
            'total_cores' => $segment->total_cores ?? 0,
            'used_cores' => $leased_cores ?? 0,
            'available_cores' => $availableCores,
            'utilization_percent' => $utilization,
            'capacity_status' => $capacityStatus,
        ]);
    }
}

private function generateSlaNetworkSnapshots(string $snapshotDate): void
{
    SlaNetworkSnapshot::where('snapshot_date', $snapshotDate)->delete();

    $leases = Lease::where('status', 'active')->get();

    foreach ($leases as $lease) {
        $tickets = SupportTicket::where('lease_id', $lease->id)->get();

        $totalIncidents = $tickets->count();
        $openIncidents = $tickets->whereIn('status', ['open', 'pending', 'in_progress'])->count();
        $resolvedIncidents = $tickets->whereIn('status', ['resolved', 'closed'])->count();

        $downtimeMinutes = $tickets->sum('downtime_minutes');

        $totalMonthMinutes = now()->daysInMonth * 24 * 60;

        $uptime = $totalMonthMinutes > 0
            ? (($totalMonthMinutes - $downtimeMinutes) / $totalMonthMinutes) * 100
            : 100;

        $slaTarget = $lease->sla_target_percent ?? 99.95;

        SlaNetworkSnapshot::create([
            'snapshot_date' => $snapshotDate,
            'lease_id' => $lease->id,
            'customer_id' => $lease->user_id,
            'total_incidents' => $totalIncidents,
            'open_incidents' => $openIncidents,
            'resolved_incidents' => $resolvedIncidents,
            'downtime_minutes' => $downtimeMinutes,
            'uptime_percent' => $uptime,
            'sla_target_percent' => $slaTarget,
            'sla_compliance_percent' => $uptime >= $slaTarget ? 100 : $uptime,
            'mttr_minutes' => $resolvedIncidents > 0
                ? round($downtimeMinutes / $resolvedIncidents)
                : 0,
            'sla_breaches' => $uptime < $slaTarget ? 1 : 0,
        ]);
    }
}

private function generateTopCustomers(string $snapshotDate): void
{
    TopCustomerSnapshot::where('snapshot_date', $snapshotDate)->delete();

    foreach (['KSH', 'USD'] as $currency) {
        $totalRevenue = ConsolidatedBilling::where('currency', $currency)
            ->sum('paid_amount');

        $customers = ConsolidatedBilling::where('currency', $currency)
            ->select(
                'user_id',
                DB::raw('SUM(paid_amount) as revenue'),
                DB::raw('SUM(total_amount - paid_amount) as outstanding_amount')
            )
            ->groupBy('user_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        foreach ($customers as $customer) {
            $contribution = $totalRevenue > 0
                ? ($customer->revenue / $totalRevenue) * 100
                : 0;

            $riskLevel = $contribution >= 30
                ? 'high'
                : ($contribution >= 15 ? 'medium' : 'low');

            TopCustomerSnapshot::create([
                'snapshot_date' => $snapshotDate,
                'customer_id' => $customer->user_id,
                'currency' => $currency,
                'revenue' => $customer->revenue ?? 0,
                'outstanding_amount' => max($customer->outstanding_amount ?? 0, 0),
                'revenue_contribution_percent' => $contribution,
                'active_leases' => Lease::where('customer_id', $customer->user_id)
                    ->where('status', 'active')
                    ->count(),
                'active_contracts' => Contract::where('customer_id', $customer->user_id)
                    ->where('status', 'active')
                    ->count(),
                'leased_km' => Lease::where('customer_id', $customer->user_id)
                    ->where('status', 'active')
                    ->sum('distance_km'),
                'leased_cores' => Lease::where('customer_id', $customer->user_id)
                    ->where('status', 'active')
                    ->sum('cores_required'),
                'risk_level' => $riskLevel,
            ]);
        }
    }

    app(ExecutiveInsightService::class)->generate($snapshotDate);
    app(RevenueForecastService::class)->generate($snapshotDate);
    app(FibreCapacityService::class)->update();
}
}
