<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lease;
use App\Models\BillingLineItem;
use Carbon\Carbon;

class DiagnoseLeaseBilling extends Command
{
    protected $signature = 'leases:diagnose {--lease= : Specific lease ID to diagnose}';
    protected $description = 'Diagnose why leases are not billing';

    public function handle()
    {
        $leaseId = $this->option('lease');

        $query = Lease::where('status', 'active');
        if ($leaseId) {
            $query->where('id', $leaseId);
        }

        $leases = $query->limit(5)->get();

        foreach ($leases as $lease) {
            $this->newLine();
            $this->info("═══════════════════════════════════════════════════════");
            $this->info("Lease: {$lease->lease_number} (ID: {$lease->id})");
            $this->info("═══════════════════════════════════════════════════════");

            $this->line("\n📋 Lease Details:");
            $this->line("   • Status: {$lease->status}");
            $this->line("   • Start Date: {$lease->start_date->format('Y-m-d')}");
            $this->line("   • End Date: {$lease->end_date->format('Y-m-d')}");
            $this->line("   • Monthly Cost: {$lease->monthly_cost}");
            $this->line("   • Billing Cycle: {$lease->billing_cycle}");
            $this->line("   • Next Billing Date: " . ($lease->next_billing_date ?? 'NULL'));
            $this->line("   • Last Billed At: " . ($lease->last_billed_at ?? 'NULL'));

            // Get existing billing line items
            $lineItems = BillingLineItem::where('lease_id', $lease->id)
                ->orderBy('period_start', 'desc')
                ->get();

            $this->line("\n💰 Existing Billing Line Items (" . $lineItems->count() . "):");
            foreach ($lineItems as $item) {
                $this->line("   • Period: {$item->period_start} to {$item->period_end}");
                $this->line("     Amount: {$item->amount} {$item->currency}");
                $this->line("     Created: {$item->created_at}");
            }

            // Calculate what the next billing period should be
            $today = Carbon::now()->startOfDay();
            $today = Carbon::create(2026, 4, 9, 0, 0, 0); // Force to today's date

            $lastBilledAt = $lease->last_billed_at
                ? Carbon::parse($lease->last_billed_at)
                : null;

            $this->line("\n📅 Next Billing Calculation:");

            if ($lastBilledAt) {
                $this->line("   • Last billed: {$lastBilledAt->format('Y-m-d')}");

                // Calculate next billing date based on cycle
                switch ($lease->billing_cycle) {
                    case 'monthly':
                        $expectedNextBilling = $lastBilledAt->copy()->addMonth();
                        break;
                    case 'quarterly':
                        $expectedNextBilling = $lastBilledAt->copy()->addMonths(3);
                        break;
                    case 'annually':
                        $expectedNextBilling = $lastBilledAt->copy()->addYear();
                        break;
                    default:
                        $expectedNextBilling = $lastBilledAt->copy()->addMonth();
                }

                $this->line("   • Expected next billing: {$expectedNextBilling->format('Y-m-d')}");
                $this->line("   • Today's date: {$today->format('Y-m-d')}");
                $this->line("   • Should bill today: " . ($expectedNextBilling->lte($today) ? 'YES' : 'NO'));

                // Calculate next period dates
                $nextPeriodStart = $lastBilledAt->copy()->startOfDay();
                switch ($lease->billing_cycle) {
                    case 'monthly':
                        $nextPeriodEnd = $nextPeriodStart->copy()->addMonth()->subDay();
                        break;
                    case 'quarterly':
                        $nextPeriodEnd = $nextPeriodStart->copy()->addMonths(3)->subDay();
                        break;
                    case 'annually':
                        $nextPeriodEnd = $nextPeriodStart->copy()->addYear()->subDay();
                        break;
                }

                $this->line("\n   • Next period would be:");
                $this->line("     Start: {$nextPeriodStart->format('Y-m-d')}");
                $this->line("     End: {$nextPeriodEnd->format('Y-m-d')}");

                // Check if this period already exists
                $existingForNextPeriod = BillingLineItem::where('lease_id', $lease->id)
                    ->whereDate('period_start', $nextPeriodStart->format('Y-m-d'))
                    ->whereDate('period_end', $nextPeriodEnd->format('Y-m-d'))
                    ->exists();

                $this->line("   • Period already billed: " . ($existingForNextPeriod ? 'YES' : 'NO'));

            } else {
                $this->line("   • No previous billing found");
                $this->line("   • First billing would be from start date: {$lease->start_date->format('Y-m-d')}");
            }

            // Check if lease should be billed according to the shouldBillLease logic
            $shouldBill = $this->simulateShouldBillLease($lease, $today);
            $this->line("\n✅ Should bill according to logic: " . ($shouldBill ? 'YES' : 'NO'));

            if (!$shouldBill && $lease->next_billing_date) {
                $nextBillingDate = Carbon::parse($lease->next_billing_date);
                $this->line("   • Next billing date in DB: {$nextBillingDate->format('Y-m-d')}");
                $this->line("   • Days until next billing: {$today->diffInDays($nextBillingDate, false)} days");
            }
        }

        return Command::SUCCESS;
    }

    private function simulateShouldBillLease(Lease $lease, Carbon $today): bool
    {
        // Basic validation
        if ($lease->status !== 'active' ||
            $today->lt($lease->start_date) ||
            $today->gt($lease->end_date)) {
            return false;
        }

        // Check if lease has been terminated
        if ($lease->terminated_at) {
            return false;
        }

        // If next_billing_date is set, use it
        if ($lease->next_billing_date) {
            $nextBillingDate = Carbon::parse($lease->next_billing_date)->startOfDay();
            return $nextBillingDate->lte($today);
        }

        // No next_billing_date - check if this is first billing
        $hasPreviousBillings = BillingLineItem::where('lease_id', $lease->id)->exists();

        if (!$hasPreviousBillings) {
            // First billing - check if lease started
            $daysSinceStart = $lease->start_date->diffInDays($today);
            $billingDay = min($lease->start_date->day, $today->daysInMonth);

            // Bill on the day matching the start date day of month
            $isBillingDay = $today->day == $billingDay;
            $isAfterStart = $daysSinceStart >= 0;

            return $isBillingDay && $isAfterStart;
        }

        return false;
    }
}
