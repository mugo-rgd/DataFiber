<?php
// File: app/Console/Commands/ProcessDailyBilling.php

namespace App\Console\Commands;

use App\Models\Lease;
use App\Models\LeaseBilling;
use App\Models\DesignRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ProcessDailyBilling extends Command
{
    protected $signature = 'billing:process-daily';
    protected $description = 'Process daily billing for active leases and completed design requests';

    public function handle()
    {
        $this->processLeaseBillings();
        $this->processDesignRequests();
        $this->markOverdueBillings();

        $this->info('Daily billing process completed successfully.');
    }

    protected function processLeaseBillings()
    {
        if (!Schema::hasTable('leases')) {
            $this->error('Leases table does not exist.');
            return;
        }

        // Get active leases that need billing
        $activeLeases = Lease::where('status', 'active')
                            ->where(function($query) {
                                $query->where('next_billing_date', '<=', now()->toDateString())
                                      ->orWhereNull('next_billing_date');
                            })
                            ->get();

        $processedCount = 0;
        $totalRevenue = 0;

        foreach ($activeLeases as $lease) {
            try {
                // Get the customer ID
                $customerId = $lease->customer_id;

                if (!$customerId) {
                    $this->error("Lease #{$lease->id} has no customer assigned.");
                    continue;
                }

                // Use monthly_cost from the leases table and cast to float
                $amount = (float) $lease->monthly_cost;

                if ($amount <= 0) {
                    $this->error("Lease #{$lease->id} has invalid monthly cost: $" . number_format($amount, 2));
                    continue;
                }

                // Build the billing data
                $leaseBillingData = [
                    // REQUIRED fields
                    'lease_id' => $lease->id,
                    'billing_number' => 'LEASE-' . date('YmdHis') . '-' . $lease->id,
                    'billing_date' => now()->toDateString(),
                    'due_date' => now()->addDays(15)->toDateString(),
                    'billing_cycle' => $lease->billing_cycle,
                    'period_start' => now()->startOfMonth()->toDateString(),
                    'period_end' => now()->endOfMonth()->toDateString(),
                    'customer_id' => $customerId,

                    // Amount fields - using monthly_cost (already decimal types)
                    'total_amount' => $lease->monthly_cost, // Keep as decimal for database
                    'amount' => $lease->monthly_cost, // Keep as decimal for database
                    'currency' => $lease->currency,
                    'status' => 'pending',
                    'description' => $this->generateBillingDescription($lease),
                    'user_id' => $customerId,
                ];

                $this->info("Creating billing for {$lease->lease_number} with amount: $" . number_format($amount, 2));

                $leaseBilling = LeaseBilling::create($leaseBillingData);

                // Update lease's next billing date based on billing cycle
                $updateData = [
                    'next_billing_date' => $this->calculateNextBillingDate($lease),
                    'last_billed_at' => now()
                ];

                $lease->update($updateData);

                $this->info("✅ Billing {$leaseBilling->billing_number} created for $" . number_format($amount, 2));
                $processedCount++;
                $totalRevenue += $amount;

            } catch (\Exception $e) {
                $this->error("❌ Failed to process lease #{$lease->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$processedCount} lease billings. Total revenue: $" . number_format($totalRevenue, 2));
    }

    /**
     * Generate descriptive billing description based on lease details
     */
    protected function generateBillingDescription(Lease $lease)
    {
        $description = "{$lease->service_type} service - {$lease->start_location} to {$lease->end_location}";

        if ($lease->bandwidth) {
            $description .= " ({$lease->bandwidth})";
        }

        if ($lease->distance_km) {
            $description .= " - " . number_format((float) $lease->distance_km, 2) . " km";
        }

        $description .= " - {$lease->billing_cycle} billing";

        return $description;
    }

    /**
     * Calculate next billing date based on billing cycle
     */
    protected function calculateNextBillingDate(Lease $lease)
    {
        switch ($lease->billing_cycle) {
            case 'monthly':
                return now()->addMonth()->toDateString();
            case 'quarterly':
                return now()->addMonths(3)->toDateString();
            case 'annually':
                return now()->addYear()->toDateString();
            case 'one_time':
                return null;
            default:
                return now()->addMonth()->toDateString();
        }
    }

    protected function processDesignRequests()
    {
        if (!class_exists('App\Models\DesignRequest') || !Schema::hasTable('design_requests')) {
            return;
        }

        if (!Schema::hasColumn('design_requests', 'billed_at')) {
            $this->info('Design requests billing skipped: billed_at column missing.');
            return;
        }

        $billableRequests = DesignRequest::where('status', 'completed')
                            ->whereNull('billed_at')
                            ->get();

        foreach ($billableRequests as $designRequest) {
            try {
                $customerId = $designRequest->customer_id;

                if (!$customerId) {
                    $this->error("Design request #{$designRequest->id} has no customer assigned.");
                    continue;
                }

                // Calculate design request amount and cast to float for number_format
                $subtotal = (float) $designRequest->unit_cost * (float) $designRequest->cores_required;
                $taxAmount = $subtotal * ((float) ($designRequest->tax_rate ?? 0) / 100);
                $totalAmount = $subtotal + $taxAmount;

                if ($totalAmount <= 0) {
                    $this->error("Design request #{$designRequest->id} has invalid amount: $" . number_format($totalAmount, 2));
                    continue;
                }

                // Prepare billing data
                $billingData = [
                    'billing_number' => 'DESIGN-' . date('YmdHis') . '-' . $designRequest->id,
                    'billing_date' => now()->toDateString(),
                    'due_date' => now()->addDays(30)->toDateString(),
                    'billing_cycle' => 'one_time',
                    'period_start' => now()->toDateString(),
                    'period_end' => now()->toDateString(),
                    'customer_id' => $customerId,
                    'total_amount' => $totalAmount, // Will be cast to decimal by Eloquent
                    'amount' => $totalAmount, // Will be cast to decimal by Eloquent
                    'currency' => 'USD',
                    'status' => 'pending',
                    'description' => "Design services: " . $designRequest->title,
                    'user_id' => $customerId,
                ];

                $leaseBilling = LeaseBilling::create($billingData);

                $designRequest->update(['billed_at' => now()]);
                $this->info("Design billing {$leaseBilling->billing_number} created for $" . number_format($totalAmount, 2));

            } catch (\Exception $e) {
                $this->error("Failed to process design request #{$designRequest->id}: " . $e->getMessage());
            }
        }
    }

    protected function markOverdueBillings()
    {
        if (!Schema::hasTable('lease_billings')) {
            return;
        }

        $overdueCount = LeaseBilling::where('status', 'pending')
                            ->where('due_date', '<', now()->toDateString())
                            ->update(['status' => 'overdue']);

        if ($overdueCount > 0) {
            $this->info("Marked {$overdueCount} billings as overdue.");
        }
    }
}
