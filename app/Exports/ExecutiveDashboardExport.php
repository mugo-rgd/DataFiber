<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExecutiveDashboardExport implements FromView
{
    public function __construct(
        public $kpis,
        public $snapshotDate,
        public $periodStart,
        public $periodEnd,
        public $debtAging,
        public $revenue,
        public $topCustomers,
        public $quotations,
        public $contracts,
        public $leases,
        public $fiberUtilization,
        public $slaNetwork,
        public $summary
    ) {}

    public function view(): View
    {
        return view('executive.dashboard-excel', [
            'kpis' => $this->kpis,
            'snapshotDate' => $this->snapshotDate,
            'periodStart' => $this->periodStart,
            'periodEnd' => $this->periodEnd,
            'debtAging' => $this->debtAging,
            'revenue' => $this->revenue,
            'topCustomers' => $this->topCustomers,
            'quotations' => $this->quotations,
            'contracts' => $this->contracts,
            'leases' => $this->leases,
            'fiberUtilization' => $this->fiberUtilization,
            'slaNetwork' => $this->slaNetwork,
            'summary' => $this->summary,
        ]);
    }
}
