<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTevinInvoice;
use App\Models\ConsolidatedBilling;
use Illuminate\Console\Command;

class DispatchTevinInvoiceJob extends Command
{
    protected $signature = 'tevin:process-invoice
                            {billing-id : The ID of the billing to process}
                            {--sync : Run synchronously (not queued)}
                            {--force : Force processing even if already processed}
                            {--queue=tevin-invoices : Specify queue name}';

    protected $description = 'Manually dispatch a TEVIN invoice processing job';

    public function handle()
    {
        $billingId = $this->argument('billing-id');

        $this->info("Looking for billing ID: {$billingId}");

        $billing = ConsolidatedBilling::find($billingId);

        if (!$billing) {
            $this->error("Billing with ID {$billingId} not found.");
            return Command::FAILURE;
        }

        // Check if already processed (unless --force flag is used)
        if (!$this->option('force') && $billing->tevin_control_code) {
            $this->warn("Billing already has a TEVIN control code: {$billing->tevin_control_code}");

            if (!$this->confirm('Process anyway? This may create duplicates.')) {
                $this->info('Cancelled.');
                return Command::FAILURE;
            }
        }

        // Prepare metadata
        $metadata = [
            'triggered_by' => 'manual_command',
            'executed_by' => get_current_user(),
            'timestamp' => now()->toISOString(),
            'force' => $this->option('force')
        ];

        // Dispatch the job
        $job = new ProcessTevinInvoice($billing, $metadata);

        if ($this->option('sync')) {
            $this->info('Running job synchronously...');
            dispatch_sync($job);
            $this->info('Job completed synchronously.');
        } else {
            $queue = $this->option('queue');
            $job->onQueue($queue);
            dispatch($job);
            $this->info("Job dispatched to '{$queue}' queue.");
        }

        $this->info("✅ Job dispatched for billing #{$billing->billing_number} (ID: {$billing->id})");
        $this->line("Check logs for progress: " . storage_path('logs/laravel.log'));

        return Command::SUCCESS;
    }
}
