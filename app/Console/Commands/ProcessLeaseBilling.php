<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lease;
use App\Models\ConsolidatedBilling;
use App\Models\BillingLineItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessLeaseBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leases:process-billing
                        {--date= : Process billing for specific date (YYYY-MM-DD)}
                        {--customer= : Process billing for specific customer ID}
                        {--force : Process even if no billing is due}
                        {--skip-duplicate-check : Skip checking for existing billing records}
                        {--json : Output results as JSON}
                        {--dry-run : Simulate processing without creating records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process billing cycles for active leases and consolidate by customer';

    /**
     * Define billing cycle intervals in months
     */
    protected $billingIntervals = [
        'monthly' => 1,
        'quarterly' => 3,
        'annually' => 12,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Parse options
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::now()->startOfDay();

        $customerId = $this->option('customer');
        $forceMode = $this->option('force');
        $skipDuplicateCheck = $this->option('skip-duplicate-check');
        $jsonOutput = $this->option('json');
        $dryRun = $this->option('dry-run');

        // Display header
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║           LEASE BILLING PROCESSOR                         ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->info("📅 Processing date: {$date->format('l, F j, Y')}");

        if ($customerId) {
            $this->info("👤 Filtering for customer ID: {$customerId}");
        }

        if ($forceMode) {
            $this->warn("⚠️  Running in FORCE mode - will check all leases regardless of due date");
        }

        if ($skipDuplicateCheck) {
            $this->warn("⚠️  Running with SKIP DUPLICATE CHECK - may create duplicate billing records");
        }

        if ($dryRun) {
            $this->warn("🔍 Running in DRY RUN mode - no records will be created");
        }

        $this->newLine();

        // Initialize counters
        $processedBills = 0;
        $processedLineItems = 0;
        $errorCount = 0;
        $skippedLeases = 0;
        $customersProcessed = [];
        $totalKsh = 0;
        $totalUsd = 0;

        try {
            // Build query for active leases using Eloquent (returns Lease models, not stdClass)
            $query = Lease::whereIn('billing_cycle', ['monthly', 'quarterly', 'annually'])
                ->where('status', 'active')
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->with(['customer']); // Eager load customer relationship

            // Apply customer filter if specified
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $leases = $query->orderBy('customer_id')
                ->orderBy('next_billing_date')
                ->get();

            $this->info("📊 Found {$leases->count()} active leases to process.");

            // Group leases by customer
            $leasesByCustomer = $leases->groupBy('customer_id');
            $this->info("👥 Found {$leasesByCustomer->count()} customers with active leases.");
            $this->newLine();

            $progressBar = $this->output->createProgressBar($leasesByCustomer->count());
            $progressBar->start();

            foreach ($leasesByCustomer as $custId => $customerLeases) {
                $progressBar->advance();

                // Get customer info
                $customer = User::find($custId);
                $customerName = $customer ? $customer->name : "Customer #{$custId}";

                try {
                    // Find leases due for billing for this customer
                    $leasesDue = [];
                    $totalAmount = 0;
                    $lineItemsData = [];

                    /** @var Lease $lease */
                    foreach ($customerLeases as $lease) {
                        // Validate lease data
                        if (!$this->validateLease($lease)) {
                            $this->newLine();
                            $this->warn("    ⚠️  Lease #{$lease->id} failed validation, skipping");
                            $skippedLeases++;
                            continue;
                        }

                        if ($this->shouldBillLease($lease, $date) || $forceMode) {
                            // Calculate billing period and amount
                            $periodDates = $this->calculateBillingPeriod($lease, $date);
                            $amount = $this->calculateBillingAmount($lease);

                            // Check for existing billing for this period (skip if flag is set)
                            if (!$skipDuplicateCheck) {
                                $existingBilling = BillingLineItem::where('lease_id', $lease->id)
                                    ->where(function($query) use ($periodDates) {
                                        // Check for overlapping periods
                                        $query->whereBetween('period_start', [$periodDates['start'], $periodDates['end']])
                                            ->orWhereBetween('period_end', [$periodDates['start'], $periodDates['end']])
                                            ->orWhere(function($q) use ($periodDates) {
                                                $q->where('period_start', '<=', $periodDates['start'])
                                                  ->where('period_end', '>=', $periodDates['end']);
                                            });
                                    })
                                    ->first();

                                if ($existingBilling) {
                                    $this->newLine();
                                    $this->line("    ⏸️  Lease #{$lease->id} already has billing for period {$periodDates['start']->format('Y-m-d')} to {$periodDates['end']->format('Y-m-d')}");
                                    $skippedLeases++;
                                    continue;
                                }
                            } else {
                                $this->newLine();
                                $this->warn("    ⚠️  Skipping duplicate check for lease #{$lease->id}");
                            }

                            $leasesDue[] = $lease;
                            $totalAmount += $amount;

                            $lineItemsData[] = [
                                'lease' => $lease,
                                'amount' => $amount,
                                'period_start' => $periodDates['start'],
                                'period_end' => $periodDates['end'],
                                'description' => $this->generateLineItemDescription($lease, $periodDates),
                            ];

                            if (!$dryRun) {
                                $this->newLine();
                                $this->line("    ✓ Lease #{$lease->id}: {$lease->lease_number}");
                                $this->line("      {$lease->billing_cycle} - {$this->formatCurrency($amount, $lease->currency)}");
                                $this->line("      Period: {$this->generateBillingPeriodString($periodDates)}");
                            }
                        } else {
                            $skippedLeases++;
                        }
                    }

                    // If no leases due for this customer, skip
                    if (empty($leasesDue)) {
                        continue;
                    }

                    // Check currency consistency
                    $currencies = array_unique(array_map(fn($lease) => $lease->currency, $leasesDue));

                    if (count($currencies) > 1) {
                        $this->newLine();
                        $this->warn("    ⚠️  Multiple currencies detected for customer #{$custId}");

                        if (!$dryRun) {
                            // Process leases separately by currency
                            $result = $this->processLeasesSeparatelyByCurrency(
                                $leasesDue,
                                $lineItemsData,
                                $date,
                                $dryRun
                            );

                            if ($result) {
                                $processedBills += count($currencies);
                                $processedLineItems += count($leasesDue);
                                $customersProcessed[] = $custId;
                            } else {
                                $errorCount++;
                            }
                        } else {
                            // Dry run - just show what would happen
                            $this->newLine();
                            $this->line("    📝 DRY RUN: Would create separate billings by currency:");
                            foreach ($currencies as $currency) {
                                $currencyLeases = array_filter($leasesDue, fn($l) => $l->currency === $currency);
                                $currencyTotal = array_sum(array_column(
                                    array_filter($lineItemsData, function($item, $idx) use ($leasesDue, $currency) {
                                        return $leasesDue[$idx]->currency === $currency;
                                    }, ARRAY_FILTER_USE_BOTH),
                                    'amount'
                                ));
                                $this->line("      • {$currency}: " . count($currencyLeases) . " leases - {$this->formatCurrency($currencyTotal, $currency)}");
                            }
                        }
                    } else {
                        // All leases have same currency
                        $currency = $currencies[0];

                        if (!$dryRun) {
                            // Create consolidated billing
                            $result = $this->createConsolidatedBilling(
                                $custId,
                                $leasesDue,
                                $lineItemsData,
                                $totalAmount,
                                $date,
                                $customerName
                            );

                            if ($result) {
                                $processedBills++;
                                $processedLineItems += count($leasesDue);
                                $customersProcessed[] = $custId;

                                // Track totals
                                if ($currency === 'KSH' || $currency === 'KES') {
                                    $totalKsh += $totalAmount;
                                } else {
                                    $totalUsd += $totalAmount;
                                }
                            } else {
                                $errorCount++;
                            }
                        } else {
                            // Dry run output
                            $this->newLine();
                            $this->line("    📝 DRY RUN: Would create consolidated billing for {$customerName}");
                            $this->line("      • Leases: " . count($leasesDue));
                            $this->line("      • Total: {$this->formatCurrency($totalAmount, $currency)}");
                            $this->line("      • Due date: " . $this->calculateDueDate($date)->format('Y-m-d'));
                        }
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    $this->newLine();
                    $this->error("    ❌ Error processing customer #{$custId}: " . $e->getMessage());
                    Log::error("Error processing customer #{$custId}: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            $progressBar->finish();
            $this->newLine(2);

            // Prepare results
            $results = [
                'success' => $errorCount === 0,
                'summary' => [
                    'customers_processed' => count($customersProcessed),
                    'consolidated_bills' => $processedBills,
                    'line_items' => $processedLineItems,
                    'skipped_leases' => $skippedLeases,
                    'errors' => $errorCount,
                    'total_leases_checked' => $leases->count(),
                ],
                'currency_summary' => [
                    'ksh' => [
                        'total' => $totalKsh,
                        'formatted' => 'KSh ' . number_format($totalKsh, 2)
                    ],
                    'usd' => [
                        'total' => $totalUsd,
                        'formatted' => '$' . number_format($totalUsd, 2)
                    ]
                ],
                'processed_at' => now()->toIso8601String(),
                'processing_date' => $date->format('Y-m-d'),
                'dry_run' => $dryRun,
                'force_mode' => $forceMode,
                'skip_duplicate_check' => $skipDuplicateCheck,
            ];

            // Display summary
            $this->displaySummary($results);

            // JSON output if requested
            if ($jsonOutput) {
                $this->output->write(json_encode($results, JSON_PRETTY_PRINT));
                return $errorCount === 0 ? Command::SUCCESS : Command::FAILURE;
            }

            return $errorCount === 0 ? Command::SUCCESS : Command::FAILURE;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Critical error: ' . $e->getMessage());
            Log::critical('Lease billing processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($jsonOutput) {
                $this->output->write(json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'processed_at' => now()->toIso8601String()
                ], JSON_PRETTY_PRINT));
            }

            return Command::FAILURE;
        }
    }

    /**
     * Create consolidated billing for a customer
     */
    protected function createConsolidatedBilling(
        int $customerId,
        array $leases,
        array $lineItemsData,
        float $totalAmount,
        Carbon $billingDate,
        string $customerName
    ): bool {
        try {
            return DB::transaction(function () use ($customerId, $leases, $lineItemsData, $totalAmount, $billingDate, $customerName) {

                /** @var Lease $firstLease */
                $firstLease = $leases[0];
                $currency = $firstLease->currency;

                // Get customer
                $customer = User::find($customerId);
                if (!$customer) {
                    throw new \Exception("Customer #{$customerId} not found");
                }

                // Generate billing number and due date
                $billingNumber = $this->generateConsolidatedBillingNumber($customerId);
                $dueDate = $this->calculateDueDate($billingDate, $customer->payment_terms ?? 7);

                // Create metadata
                $metadata = [
                    'customer_name' => $customerName,
                    'customer_email' => $customer->email,
                    'lease_count' => count($leases),
                    'processed_at' => now()->toIso8601String(),
                    'billing_cycle_summary' => $this->getBillingCycleSummary($leases),
                    'lease_ids' => array_map(fn(Lease $lease) => $lease->id, $leases),
                ];

                // Create consolidated billing
                $consolidatedBilling = ConsolidatedBilling::create([
                    'billing_number' => $billingNumber,
                    'user_id' => $customerId,
                    'billing_date' => $billingDate,
                    'due_date' => $dueDate,
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'currency' => $currency,
                    'description' => "Consolidated billing for {$customerName} - " .
                                   count($leases) . " lease(s) - {$billingDate->format('F Y')}",
                    'status' => 'pending',
                    'metadata' => json_encode($metadata),
                ]);

                $this->newLine();
                $this->line("    ✅ Created consolidated billing #{$consolidatedBilling->id}");
                $this->line("      Invoice: {$billingNumber}");
                $this->line("      Total: {$this->formatCurrency($totalAmount, $currency)}");
                $this->line("      Due: {$dueDate->format('Y-m-d')}");

                // Create line items and update leases
                foreach ($lineItemsData as $index => $itemData) {
                    /** @var Lease $lease */
                    $lease = $leases[$index];

                    // Create billing line item
                    BillingLineItem::create([
                        'consolidated_billing_id' => $consolidatedBilling->id,
                        'lease_id' => $lease->id,
                        'amount' => $itemData['amount'],
                        'currency' => $lease->currency,
                        'billing_cycle' => $lease->billing_cycle,
                        'period_start' => $itemData['period_start'],
                        'period_end' => $itemData['period_end'],
                        'description' => $itemData['description'],
                    ]);

                    // Calculate next billing date
                    $nextBillingDate = $this->calculateNextBillingDate($lease, $billingDate);

                    // Update lease
                    $lease->update([
                        'next_billing_date' => $nextBillingDate,
                        'last_billed_at' => $billingDate,
                        'updated_at' => now(),
                    ]);

                    $this->line("      • {$lease->lease_number}: {$this->formatCurrency($itemData['amount'], $lease->currency)}");
                }

                // Log success
                Log::info("Consolidated billing created", [
                    'billing_id' => $consolidatedBilling->id,
                    'billing_number' => $billingNumber,
                    'customer_id' => $customerId,
                    'total_amount' => $totalAmount,
                    'currency' => $currency,
                ]);

                return true;

            }, 5); // 5 retry attempts for deadlock

        } catch (\Exception $e) {
            $this->newLine();
            $this->error("    ❌ Failed to create billing for customer #{$customerId}: " . $e->getMessage());
            Log::error("Failed to create consolidated billing", [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process leases separately by currency
     */
    protected function processLeasesSeparatelyByCurrency(
        array $leases,
        array $lineItemsData,
        Carbon $billingDate,
        bool $dryRun = false
    ): bool {
        // Group by currency
        $leasesByCurrency = [];
        $lineItemsByCurrency = [];

        foreach ($leases as $index => $lease) {
            $currency = $lease->currency;
            if (!isset($leasesByCurrency[$currency])) {
                $leasesByCurrency[$currency] = [];
                $lineItemsByCurrency[$currency] = [];
            }
            $leasesByCurrency[$currency][] = $lease;
            $lineItemsByCurrency[$currency][] = $lineItemsData[$index];
        }

        $allSuccess = true;

        foreach ($leasesByCurrency as $currency => $currencyLeases) {
            /** @var Lease $firstLease */
            $firstLease = $currencyLeases[0];
            $customerId = $firstLease->customer_id;
            $customer = User::find($customerId);
            $customerName = $customer ? $customer->name : "Customer #{$customerId}";
            $totalAmount = array_sum(array_column($lineItemsByCurrency[$currency], 'amount'));

            $this->newLine();
            $this->line("    Processing {$currency} billing for {$customerName}");

            if (!$dryRun) {
                $result = $this->createConsolidatedBilling(
                    $customerId,
                    $currencyLeases,
                    $lineItemsByCurrency[$currency],
                    $totalAmount,
                    $billingDate,
                    "{$customerName} ({$currency})"
                );

                if (!$result) {
                    $allSuccess = false;
                }
            } else {
                $this->line("      📝 DRY RUN: Would create {$currency} billing");
                $this->line("      • Leases: " . count($currencyLeases));
                $this->line("      • Total: {$this->formatCurrency($totalAmount, $currency)}");
            }
        }

        return $allSuccess;
    }

    /**
     * Determine if a lease should be billed today
     */
    protected function shouldBillLease(Lease $lease, Carbon $today): bool
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

        // Check if lease is on hold
        if ($lease->billing_hold_until && $today->lt($lease->billing_hold_until)) {
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

        // Get the most recent billing
        $lastBilling = BillingLineItem::where('lease_id', $lease->id)
            ->orderBy('period_end', 'desc')
            ->first();

        if (!$lastBilling) {
            return false;
        }

        $lastBillingEnd = Carbon::parse($lastBilling->period_end);

        // Calculate when next billing should occur based on cycle
        switch ($lease->billing_cycle) {
            case 'monthly':
                $nextBillingDate = $lastBillingEnd->copy()->addMonth();
                break;
            case 'quarterly':
                $nextBillingDate = $lastBillingEnd->copy()->addMonths(3);
                break;
            case 'annually':
                $nextBillingDate = $lastBillingEnd->copy()->addYear();
                break;
            default:
                $nextBillingDate = $lastBillingEnd->copy()->addMonth();
        }

        // Ensure next billing date doesn't exceed lease end
        if ($nextBillingDate->gt($lease->end_date)) {
            return $today->gte($lease->end_date);
        }

        return $today->gte($nextBillingDate);
    }

    /**
     * Calculate billing amount based on cycle
     */
    protected function calculateBillingAmount(Lease $lease): float
    {
        $monthlyCost = $lease->monthly_cost ?? 0;

        // Add installation fee for first billing
        $hasPreviousBillings = BillingLineItem::where('lease_id', $lease->id)->exists();

        if (!$hasPreviousBillings && $lease->installation_fee > 0) {
            $monthlyCost += $lease->installation_fee;
        }

        switch ($lease->billing_cycle) {
            case 'monthly':
                return $monthlyCost;
            case 'quarterly':
                return $monthlyCost * 3;
            case 'annually':
                return $monthlyCost * 12;
            default:
                return $monthlyCost;
        }
    }

    /**
     * Calculate billing period dates
     */
    protected function calculateBillingPeriod(Lease $lease, Carbon $billingDate): array
    {
        $billingCycle = $lease->billing_cycle;
        $lastBilledAt = $lease->last_billed_at ? Carbon::parse($lease->last_billed_at) : null;

        if ($lastBilledAt) {
            // Period starts from day after last billed
            $periodStart = $lastBilledAt->copy()->startOfDay();
        } else {
            // First billing - start from lease start date
            $periodStart = Carbon::parse($lease->start_date)->startOfDay();
        }

        // Calculate period end based on cycle
        switch ($billingCycle) {
            case 'monthly':
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
                break;
            case 'quarterly':
                $periodEnd = $periodStart->copy()->addMonths(3)->subDay()->endOfDay();
                break;
            case 'annually':
                $periodEnd = $periodStart->copy()->addYear()->subDay()->endOfDay();
                break;
            default:
                $periodEnd = $periodStart->copy()->addMonth()->subDay()->endOfDay();
        }

        // Ensure period end doesn't exceed lease end date
        $leaseEndDate = Carbon::parse($lease->end_date)->endOfDay();
        if ($periodEnd->gt($leaseEndDate)) {
            $periodEnd = $leaseEndDate;
        }

        return [
            'start' => $periodStart,
            'end' => $periodEnd,
        ];
    }

    /**
     * Calculate next billing date
     */
    protected function calculateNextBillingDate(Lease $lease, Carbon $currentBillingDate): Carbon
    {
        $billingCycle = $lease->billing_cycle;
        $monthsToAdd = $this->billingIntervals[$billingCycle] ?? 1;

        $nextBillingDate = $currentBillingDate->copy()->addMonths($monthsToAdd);

        // Ensure next billing date doesn't exceed lease end date
        $leaseEndDate = Carbon::parse($lease->end_date);
        if ($nextBillingDate->gt($leaseEndDate)) {
            return $leaseEndDate;
        }

        return $nextBillingDate;
    }

    /**
     * Calculate due date based on payment terms
     */
    protected function calculateDueDate(Carbon $billingDate, int $paymentTerms = 7): Carbon
    {
        return $billingDate->copy()->addDays($paymentTerms);
    }

    /**
     * Format currency with symbol
     */
    protected function formatCurrency($amount, $currency = 'USD'): string
    {
        if ($currency === 'USD') {
            return '$' . number_format($amount, 2);
        } elseif ($currency === 'KSH' || $currency === 'KES') {
            return 'KSh ' . number_format($amount, 2);
        } else {
            return $currency . ' ' . number_format($amount, 2);
        }
    }

    /**
     * Generate line item description
     */
    protected function generateLineItemDescription(Lease $lease, array $periodDates): string
    {
        $leaseNumber = $lease->lease_number;
        $title = $lease->title ?: "Lease";
        $serviceType = ucfirst(str_replace('_', ' ', $lease->service_type ?? 'Service'));
        $cycleName = ucfirst($lease->billing_cycle);
        $periodStart = $periodDates['start']->format('M d, Y');
        $periodEnd = $periodDates['end']->format('M d, Y');

        return "{$cycleName} - {$serviceType}: {$title} ({$leaseNumber}) - Period: {$periodStart} to {$periodEnd}";
    }

    /**
     * Generate billing period string
     */
    protected function generateBillingPeriodString(array $periodDates): string
    {
        $start = $periodDates['start']->format('M d, Y');
        $end = $periodDates['end']->format('M d, Y');
        return "{$start} to {$end}";
    }

    /**
     * Generate consolidated billing number
     */
    protected function generateConsolidatedBillingNumber(int $customerId): string
    {
        $timestamp = Carbon::now()->format('YmdHis');
        $customerCode = str_pad($customerId, 6, '0', STR_PAD_LEFT);
        $random = mt_rand(100, 999);

        return "CON-INV-{$customerCode}-{$timestamp}-{$random}";
    }

    /**
     * Get billing cycle summary for metadata
     */
    protected function getBillingCycleSummary(array $leases): array
    {
        $summary = [];
        foreach ($leases as $lease) {
            $cycle = $lease->billing_cycle;
            if (!isset($summary[$cycle])) {
                $summary[$cycle] = 0;
            }
            $summary[$cycle]++;
        }
        return $summary;
    }

    /**
     * Validate lease data before processing
     */
    protected function validateLease(Lease $lease): bool
    {
        $errors = [];

        if (!$lease->monthly_cost || $lease->monthly_cost <= 0) {
            $errors[] = "Invalid monthly cost: {$lease->monthly_cost}";
        }

        if (!in_array($lease->billing_cycle, ['monthly', 'quarterly', 'annually'])) {
            $errors[] = "Invalid billing cycle: {$lease->billing_cycle}";
        }

        if (!in_array($lease->currency, ['USD', 'KSH', 'KES'])) {
            $errors[] = "Invalid currency: {$lease->currency}";
        }

        if (!empty($errors)) {
            Log::warning("Lease #{$lease->id} validation failed", ['errors' => $errors]);
            return false;
        }

        return true;
    }

    /**
     * Display processing summary
     */
    protected function displaySummary(array $results): void
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                    PROCESSING SUMMARY                    ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Customers Processed', $results['summary']['customers_processed']],
                ['Consolidated Bills Created', $results['summary']['consolidated_bills']],
                ['Billing Line Items', $results['summary']['line_items']],
                ['Skipped Leases', $results['summary']['skipped_leases']],
                ['Errors', $results['summary']['errors']],
                ['Total Leases Checked', $results['summary']['total_leases_checked']],
            ]
        );

        $this->newLine();
        $this->info('💰 Currency Summary:');
        $this->line("   KSH: {$results['currency_summary']['ksh']['formatted']}");
        $this->line("   USD: {$results['currency_summary']['usd']['formatted']}");

        if ($results['dry_run']) {
            $this->newLine();
            $this->warn('🔍 DRY RUN - No records were created');
        }

        if ($results['force_mode'] && $results['skip_duplicate_check']) {
            $this->newLine();
            $this->warn('⚠️  FORCE MODE + SKIP DUPLICATE CHECK enabled - duplicate billing records may have been created');
        }

        $this->newLine();
        $this->line("📅 Processed on: {$results['processed_at']}");
        $this->line("📆 Billing date: {$results['processing_date']}");
    }
}
