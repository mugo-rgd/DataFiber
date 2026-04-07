<?php

namespace App\Console\Commands;

use App\Services\FinancialParameterSyncService;
use Illuminate\Console\Command;

class SyncFinancialParameters extends Command
{
    protected $signature = 'finance:sync-parameters {direction?}';

    protected $description = 'Sync financial parameters between financial_parameters and settings tables';

    public function handle()
    {
        $direction = $this->argument('direction');

        if ($direction === 'to-settings') {
            $this->info('Syncing financial parameters to settings...');
            $count = FinancialParameterSyncService::syncAllToSettings();
            $this->info("Synced {$count} financial parameters to settings");
        } elseif ($direction === 'to-parameters') {
            $this->info('Syncing settings to financial parameters...');
            $count = FinancialParameterSyncService::syncAllToFinancialParameters();
            $this->info("Synced {$count} settings to financial parameters");
        } else {
            $this->info('Syncing both ways...');

            $this->info('Syncing financial parameters to settings...');
            $count1 = FinancialParameterSyncService::syncAllToSettings();
            $this->info("Synced {$count1} financial parameters to settings");

            $this->info('Syncing settings to financial parameters...');
            $count2 = FinancialParameterSyncService::syncAllToFinancialParameters();
            $this->info("Synced {$count2} settings to financial parameters");
        }

        return Command::SUCCESS;
    }
}
