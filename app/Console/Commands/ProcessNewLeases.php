<?php
// app/Console/Commands/ProcessNewLeases.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lease;
use Carbon\Carbon;

class ProcessNewLeases extends Command
{
    protected $signature = 'leases:process-new
                        {--hours=24 : Process leases created in the last X hours}
                        {--dry-run : Simulate processing without creating records}';

    protected $description = 'Process billing for newly created leases';

    public function handle()
    {
        $hours = $this->option('hours');
        $dryRun = $this->option('dry-run');

        $since = Carbon::now()->subHours($hours);

        $this->info("🔍 Checking for leases created since {$since->format('Y-m-d H:i:s')}");

        // FIXED: Use 'billings' relationship which checks the lease_billings table
        $newLeases = Lease::where('created_at', '>=', $since)
            ->where('status', 'active')
            ->whereDoesntHave('billings') // Checks lease_billings table
            ->get();

        if ($newLeases->isEmpty()) {
            $this->info("✅ No new leases found that need billing.");
            return Command::SUCCESS;
        }

        $this->info("📊 Found {$newLeases->count()} new leases to process:");

        $tableData = $newLeases->map(function($lease) {
            return [
                $lease->id,
                $lease->lease_number,
                $lease->customer->name ?? 'Unknown',
                $lease->billing_cycle,
                $this->formatCurrency($lease->monthly_cost, $lease->currency),
                $lease->created_at->format('Y-m-d H:i'),
            ];
        })->toArray();

        $this->table(
            ['ID', 'Lease #', 'Customer', 'Cycle', 'Monthly Cost', 'Created'],
            $tableData
        );

        if ($dryRun) {
            $this->warn("🔍 DRY RUN - No billings will be created");
            return Command::SUCCESS;
        }

        // Process each new lease
        $processed = 0;
        $errors = 0;

        foreach ($newLeases as $lease) {
            $this->line("Processing lease #{$lease->id} - {$lease->lease_number}...");

            try {
                // Call your main billing command for this specific customer
                $exitCode = \Artisan::call('leases:process-billing', [
                    '--customer' => $lease->customer_id,
                    '--force' => true,
                ]);

                $output = \Artisan::output();

                if ($exitCode === Command::SUCCESS) {
                    $processed++;
                    $this->info("  ✅ Successfully processed");
                } else {
                    $errors++;
                    $this->error("  ❌ Failed with exit code: {$exitCode}");
                    $this->line($output);
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("  ❌ Error: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("📋 Summary:");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $processed],
                ['Errors', $errors],
                ['Total', $newLeases->count()]
            ]
        );

        return $errors === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Format currency helper
     */
    private function formatCurrency($amount, $currency = 'USD'): string
    {
        if ($currency === 'USD') {
            return '$' . number_format($amount, 2);
        }
        return 'KSh ' . number_format($amount, 2);
    }
}
