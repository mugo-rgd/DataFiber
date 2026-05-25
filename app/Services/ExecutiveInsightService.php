<?php

namespace App\Services;

use App\Models\ContractReportSnapshot;
use App\Models\DebtAgingSnapshot;
use App\Models\ExecutiveInsight;
use App\Models\ExecutiveKpiSnapshot;
use App\Models\FiberUtilizationSnapshot;
use App\Models\SlaNetworkSnapshot;
use App\Models\TopCustomerSnapshot;

class ExecutiveInsightService
{
    public function generate(string $snapshotDate): void
    {
        ExecutiveInsight::whereDate('snapshot_date', $snapshotDate)->delete();

        $kpi = ExecutiveKpiSnapshot::whereDate('snapshot_date', $snapshotDate)->first();

        if (!$kpi) {
            return;
        }

        $this->debtInsights($snapshotDate, $kpi);
        $this->contractInsights($snapshotDate);
        $this->fiberInsights($snapshotDate);
        $this->slaInsights($snapshotDate);
        $this->customerConcentrationInsights($snapshotDate);
    }

    private function add(
        string $snapshotDate,
        string $category,
        string $severity,
        string $title,
        string $message,
        ?float $value = null,
        ?string $currency = null,
        array $metadata = []
    ): void {
        ExecutiveInsight::create([
            'snapshot_date' => $snapshotDate,
            'category' => $category,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'value' => $value,
            'currency' => $currency,
            'metadata' => $metadata,
        ]);
    }

    private function debtInsights(string $snapshotDate, ExecutiveKpiSnapshot $kpi): void
    {
        if ($kpi->overdue_ksh > 0) {
            $this->add(
                $snapshotDate,
                'debt',
                $kpi->overdue_ksh > 10000000 ? 'critical' : 'warning',
                'KSH overdue debt detected',
                'There is outstanding overdue debt in KSH requiring collection follow-up.',
                $kpi->overdue_ksh,
                'KSH'
            );
        }

        if ($kpi->overdue_usd > 0) {
            $this->add(
                $snapshotDate,
                'debt',
                $kpi->overdue_usd > 100000 ? 'critical' : 'warning',
                'USD overdue debt detected',
                'There is outstanding overdue debt in USD requiring collection follow-up.',
                $kpi->overdue_usd,
                'USD'
            );
        }

        $oldDebt = DebtAgingSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->where(function ($q) {
                $q->where('days_91_120', '>', 0)
                  ->orWhere('days_120_plus', '>', 0);
            })
            ->count();

        if ($oldDebt > 0) {
            $this->add(
                $snapshotDate,
                'debt',
                'critical',
                'Old debt exists',
                "{$oldDebt} customer account(s) have debt aged above 90 days.",
                $oldDebt
            );
        }
    }

    private function contractInsights(string $snapshotDate): void
    {
        $expiring = ContractReportSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->sum('expiring_90_days');

        $risk = ContractReportSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->sum('renewal_revenue_at_risk');

        if ($expiring > 0) {
            $this->add(
                $snapshotDate,
                'contracts',
                'warning',
                'Contracts expiring soon',
                "{$expiring} contract(s) are expiring within 90 days.",
                $risk
            );
        }
    }

    private function fiberInsights(string $snapshotDate): void
    {
        $highUtilization = FiberUtilizationSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->where('utilization_percent', '>=', 80)
            ->count();

        if ($highUtilization > 0) {
            $this->add(
                $snapshotDate,
                'fiber',
                'warning',
                'High fibre utilization',
                "{$highUtilization} fibre route(s) have utilization above 80%.",
                $highUtilization
            );
        }

        $lowUtilization = FiberUtilizationSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->where('utilization_percent', '<=', 20)
            ->count();

        if ($lowUtilization > 0) {
            $this->add(
                $snapshotDate,
                'fiber',
                'info',
                'Low fibre utilization',
                "{$lowUtilization} fibre route(s) have low utilization and may represent unused commercial capacity.",
                $lowUtilization
            );
        }
    }

    private function slaInsights(string $snapshotDate): void
    {
        $breaches = SlaNetworkSnapshot::whereDate('snapshot_date', $snapshotDate)
            ->sum('sla_breaches');

        if ($breaches > 0) {
            $this->add(
                $snapshotDate,
                'sla',
                'critical',
                'SLA breaches detected',
                "{$breaches} SLA breach(es) were detected and require operational follow-up.",
                $breaches
            );
        }
    }

    private function customerConcentrationInsights(string $snapshotDate): void
    {
        $topCustomer = TopCustomerSnapshot::with('customer:id,name')
            ->whereDate('snapshot_date', $snapshotDate)
            ->orderByDesc('revenue_contribution_percent')
            ->first();

        if (
            $topCustomer &&
            $topCustomer->revenue_contribution_percent >= 30
        ) {
            $this->add(
                $snapshotDate,
                'customers',
                'warning',
                'High customer concentration',
                ($topCustomer->customer->name ?? 'A customer') .
                ' contributes ' .
                number_format($topCustomer->revenue_contribution_percent, 2) .
                '% of revenue. This may represent concentration risk.',
                $topCustomer->revenue_contribution_percent,
                null,
                [
                    'customer_id' => $topCustomer->customer_id,
                ]
            );
        }
    }
}
