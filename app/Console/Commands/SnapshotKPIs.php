<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KPIService;

class SnapshotKPIs extends Command
{
    protected $signature = 'kpi:snapshot {--details : Show detailed breakdown}';
    protected $description = 'Take monthly snapshot of KPIs for historical tracking';

    protected $kpiService;

    public function __construct(KPIService $kpiService)
    {
        parent::__construct();
        $this->kpiService = $kpiService;
    }

    public function handle()
    {
        $this->info('Taking KPI snapshot...');

        $saved = $this->kpiService->saveMonthlySnapshot();

        $this->newLine();
        $this->info('✓ KPI snapshot completed successfully!');
        $this->info('✓ Saved records for ' . count($saved) . ' account managers.');
        $this->newLine();

        if ($this->option('details')) {
            // Show detailed breakdown
            foreach ($saved as $record) {
                $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                $this->line("📊 Account Manager: {$record->accountManager->name}");
                $this->newLine();

                $this->line("💰 Financial Metrics:");
                $this->line("   • Total MRR (USD): $" . number_format($record->total_mrr_usd, 2));
                $this->line("   • Total MRR (KSH): KSh " . number_format($record->total_mrr_ksh, 2));
                $this->line("   • Total MRR (Combined): $" . number_format($record->total_mrr_combined, 2));
                $this->line("   • Total TCV (Combined): $" . number_format($record->total_tcv_combined, 2));
                $this->newLine();

                $this->line("👥 Portfolio Metrics:");
                $this->line("   • Total Customers: {$record->total_customers}");
                $this->line("   • Total Leases: {$record->total_leases}");
                $this->line("   • Active Leases: {$record->active_leases}");
                $this->line("   • Terminated Leases: {$record->terminated_leases}");
                $this->newLine();

                $this->line("📏 Utilization Metrics:");
                $this->line("   • Total Distance: " . number_format($record->total_distance_km, 2) . " km");
                $this->line("   • Total Cores: {$record->total_cores_leased}");
                $this->newLine();

                $this->line("📋 Contract Health:");
                $this->line("   • Avg Contract Term: {$record->avg_contract_term_years} years");
                $this->line("   • Upcoming Renewals (90 days): {$record->upcoming_renewals_90days}");
                $this->line("   • Renewal Revenue at Risk: $" . number_format($record->renewal_revenue_at_risk, 2));
                $this->newLine();

                $this->line("⭐ Performance:");
                $this->line("   • Churn Rate: {$record->churn_rate}%");
                $this->line("   • Performance Score: {$record->performance_score}/100");
                $this->line("   • Rating: {$record->performance_rating}");
                $this->newLine();
            }
        } else {
            // Show summary table
            $headers = ['Account Manager', 'MRR (USD)', 'MRR (KSH)', 'Customers', 'Leases', 'Renewals', 'Rating'];
            $rows = [];

            foreach ($saved as $record) {
                $rows[] = [
                    $record->accountManager->name,
                    '$' . number_format($record->total_mrr_usd, 2),
                    'KSh ' . number_format($record->total_mrr_ksh, 2),
                    $record->total_customers,
                    $record->total_leases,
                    $record->upcoming_renewals_90days,
                    $record->performance_rating,
                ];
            }

            $this->table($headers, $rows);
        }

        $this->newLine();
        $this->info('✅ Snapshot saved successfully!');

        return Command::SUCCESS;
    }
}
