<?php

namespace App\Helpers;

use Illuminate\Console\Command;
use App\Models\Lease;
use Carbon\Carbon;

class InitializeLeaseBillingDates extends Command
{
    protected $signature = 'leases:init-billing-dates';
    protected $description = 'Initialize next_billing_date for all active leases';

    public function handle()
    {
        $this->info('Initializing billing dates for active leases...');

        $activeLeases = Lease::where('status', 'active')
            ->whereIn('billing_cycle', ['monthly', 'quarterly', 'annually'])
            ->get();

        $this->info("Found {$activeLeases->count()} active leases.");

        $updated = 0;
        $skipped = 0;

        foreach ($activeLeases as $lease) {
            try {
                // Skip if already has next_billing_date
                if ($lease->next_billing_date) {
                    $this->line("Lease #{$lease->id} already has next billing date: {$lease->next_billing_date}");
                    $skipped++;
                    continue;
                }

                // Calculate first billing date from start_date
                $startDate = Carbon::parse($lease->start_date);
                $today = Carbon::now();

                // Determine interval based on billing cycle
                $interval = 1; // Default monthly
                if ($lease->billing_cycle === 'quarterly') $interval = 3;
                if ($lease->billing_cycle === 'annually') $interval = 12;

                // Calculate next billing date
                $monthsSinceStart = $startDate->diffInMonths($today);
                $intervalsPassed = floor($monthsSinceStart / $interval);
                $nextBillingDate = $startDate->copy()->addMonths(($intervalsPassed + 1) * $interval);

                // Update lease
                $lease->next_billing_date = $nextBillingDate;
                $lease->save();

                $this->info("Lease #{$lease->id}: Next billing date set to {$nextBillingDate->format('Y-m-d')}");
                $updated++;

            } catch (\Exception $e) {
                $this->error("Error processing lease #{$lease->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed! Updated: {$updated}, Skipped: {$skipped}");

        return Command::SUCCESS;
    }
}
