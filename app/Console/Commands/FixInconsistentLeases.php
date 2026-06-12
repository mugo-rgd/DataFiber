<?php

namespace App\Console\Commands;

use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixInconsistentLeases extends Command
{
    protected $signature = 'leases:fix-inconsistent
                            {--admin-id=1 : Admin user ID to set as approver}
                            {--dry-run : Preview changes without applying}';

    protected $description = 'Fix leases that are active but missing approval data';

    public function handle()
    {
        $adminId = $this->option('admin-id');
        $dryRun = $this->option('dry-run');

        // Find all inconsistent leases
        $leases = Lease::where('status', 'active')
            ->whereNull('approved_at')
            ->get();

        if ($leases->isEmpty()) {
            $this->info('✅ No inconsistent leases found.');
            return 0;
        }

        $this->warn("⚠️  Found {$leases->count()} inconsistent lease(s).");

        if ($dryRun) {
            $this->table(
                ['ID', 'Lease Number', 'Customer', 'Created At', 'Status'],
                $leases->map(fn($l) => [
                    $l->id,
                    $l->lease_number,
                    $l->customer->name ?? 'N/A',
                    $l->created_at->format('Y-m-d H:i:s'),
                    $l->status
                ])
            );
            $this->info('Dry run completed. No changes were made.');
            $this->info('Run without --dry-run to apply fixes.');
            return 0;
        }

        if (!$this->confirm('Do you wish to fix these leases?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $fixed = 0;
        $errors = [];

        foreach ($leases as $lease) {
            try {
                $startDate = $lease->start_date instanceof Carbon
                    ? $lease->start_date
                    : Carbon::parse($lease->start_date);

                // Calculate next billing date based on billing cycle
                switch ($lease->billing_cycle) {
                    case 'monthly':
                        $nextBillingDate = $startDate->copy()->addMonth();
                        break;
                    case 'quarterly':
                        $nextBillingDate = $startDate->copy()->addMonths(3);
                        break;
                    case 'annually':
                        $nextBillingDate = $startDate->copy()->addYear();
                        break;
                    default:
                        $nextBillingDate = $startDate->copy()->addMonth();
                }

                $lease->update([
                    'approved_at' => $lease->created_at,
                    'approved_by' => $adminId,
                    'activated_at' => $lease->created_at,
                    'sent_at' => $lease->created_at,
                    'next_billing_date' => $nextBillingDate,
                    'last_billed_at' => null,
                    'rejection_reason' => null,
                    'rejected_at' => null,
                    'rejected_by' => null,
                ]);

                $fixed++;
                $this->info("✓ Fixed lease #{$lease->lease_number} (ID: {$lease->id})");

            } catch (\Exception $e) {
                $errors[] = "Lease #{$lease->lease_number}: " . $e->getMessage();
                $this->error("✗ Failed to fix lease #{$lease->lease_number}: " . $e->getMessage());
            }
        }

        $this->newLine();

        if ($fixed > 0) {
            $this->info("✅ Successfully fixed {$fixed} out of {$leases->count()} lease(s).");
        }

        if (!empty($errors)) {
            $this->warn("\n⚠️  Errors encountered:");
            foreach ($errors as $error) {
                $this->error($error);
            }
        }

        return 0;
    }
}
