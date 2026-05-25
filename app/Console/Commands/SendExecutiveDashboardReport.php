<?php

namespace App\Console\Commands;

use App\Mail\ExecutiveDashboardReportMail;
use App\Models\ContractReportSnapshot;
use App\Models\DebtAgingSnapshot;
use App\Models\ExecutiveKpiSnapshot;
use App\Models\FiberUtilizationSnapshot;
use App\Models\LeaseReportSnapshot;
use App\Models\QuotationPipelineSnapshot;
use App\Models\RevenueReportSnapshot;
use App\Models\SlaNetworkSnapshot;
use App\Models\TopCustomerSnapshot;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendExecutiveDashboardReport extends Command
{
    protected $signature = 'reports:email-executive {--date=}';

    protected $description = 'Email executive dashboard report with PDF attachment';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : now()->toDateString();

        Artisan::call('reports:generate-executive', [
            '--date' => $date,
        ]);

        $latestKpi = ExecutiveKpiSnapshot::whereDate('snapshot_date', '<=', $date)
            ->latest('snapshot_date')
            ->first();

        if (!$latestKpi) {
            $this->error('No executive KPI snapshot found.');
            return Command::FAILURE;
        }

        $snapshotDate = Carbon::parse($latestKpi->snapshot_date)->toDateString();
        $periodStart = Carbon::parse($snapshotDate)->startOfMonth()->toDateString();
        $periodEnd = Carbon::parse($snapshotDate)->endOfMonth()->toDateString();

        $debtAging = DebtAgingSnapshot::with('customer:id,name,email')
            ->whereDate('snapshot_date', $snapshotDate)
            ->orderByDesc('total_outstanding')
            ->get();

        $revenue = RevenueReportSnapshot::whereDate('period_start', $periodStart)
            ->whereDate('period_end', $periodEnd)
            ->orderByDesc('billed_amount')
            ->get();

        $topCustomers = TopCustomerSnapshot::with('customer:id,name,email')
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
            'revenue_ksh' => $revenue->where('currency', 'KSH')->sum('billed_amount'),
            'revenue_usd' => $revenue->where('currency', 'USD')->sum('billed_amount'),
            'paid_ksh' => $revenue->where('currency', 'KSH')->sum('paid_amount'),
            'paid_usd' => $revenue->where('currency', 'USD')->sum('paid_amount'),
            'outstanding_ksh' => $revenue->where('currency', 'KSH')->sum('outstanding_amount'),
            'outstanding_usd' => $revenue->where('currency', 'USD')->sum('outstanding_amount'),
        ];

        $data = compact(
            'latestKpi',
            'snapshotDate',
            'periodStart',
            'periodEnd',
            'debtAging',
            'revenue',
            'topCustomers',
            'quotations',
            'contracts',
            'leases',
            'fiberUtilization',
            'slaNetwork',
            'summary'
        );

        $data['kpis'] = $latestKpi;

        Storage::makeDirectory('executive-reports');

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

        $relativePath = 'executive-reports/executive-dashboard-' . $snapshotDate . '.pdf';
        $absolutePath = storage_path('app/' . $relativePath);

        $pdf->save($absolutePath);

        $recipients = config('executive_reports.recipients', []);

        if (empty($recipients)) {
            $this->error('No executive report recipients configured.');
            return Command::FAILURE;
        }

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(
                new ExecutiveDashboardReportMail($data, $absolutePath)
            );
        }

        $this->info('Executive dashboard report emailed successfully.');

        return Command::SUCCESS;
    }
}
