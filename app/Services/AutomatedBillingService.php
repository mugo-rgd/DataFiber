<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\LeaseBilling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AutomatedBillingService
{
    protected InvoicePdfService $invoicePdfService;

    public function __construct(InvoicePdfService $invoicePdfService)
    {
        $this->invoicePdfService = $invoicePdfService;
    }

    public function processDailyBilling()
    {
        $today = Carbon::today();
        Log::info("Starting daily billing process for {$today->format('Y-m-d')}");

        $leases = Lease::with(['customer'])
            ->where('status', 'active')
            ->where('end_date', '>=', $today)
            ->whereIn('billing_cycle', ['monthly', 'quarterly', 'annually'])
            ->get();

        $billedCount = 0;
        $failedCount = 0;

        foreach ($leases as $lease) {
            try {
                if ($this->shouldBillLease($lease, $today)) {
                    $billing = $this->createBillingRecord($lease, $today);
                    if ($billing) {
                        $billedCount++;
                        Log::info("Successfully billed lease {$lease->lease_number}, Invoice: {$billing->billing_number}");
                    }
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Failed to process billing for lease {$lease->id}: " . $e->getMessage());
            }
        }

        Log::info("Daily billing process completed. {$billedCount} leases billed, {$failedCount} failed.");
        return ['billed' => $billedCount, 'failed' => $failedCount];
    }

    private function shouldBillLease(Lease $lease, Carbon $today): bool
    {
        // Check if lease has started
        if ($today->lt($lease->start_date)) {
            return false;
        }

        // Check if lease is already terminated or expired
        if ($lease->terminated_at || $today->gt($lease->end_date)) {
            return false;
        }

        // Check if we already have a billing for the current period
        $currentPeriod = $this->getBillingPeriod($lease, $today);
        $existingBilling = LeaseBilling::where('lease_id', $lease->id)
            ->where('period_start', '<=', $currentPeriod['end']->format('Y-m-d'))
            ->where('period_end', '>=', $currentPeriod['start']->format('Y-m-d'))
            ->exists();

        if ($existingBilling) {
            return false;
        }

        // For FIRST billing of a new lease
        $hasPreviousBilling = LeaseBilling::where('lease_id', $lease->id)->exists();

        if (!$hasPreviousBilling) {
            // First billing - bill immediately if lease has started
            // Bill if lease started today or within the last 3 days
            $daysSinceStart = $lease->start_date->diffInDays($today);
            return $daysSinceStart <= 3;
        }

        // For SUBSEQUENT billings, check billing cycle
        $lastBilling = LeaseBilling::where('lease_id', $lease->id)
            ->orderBy('period_end', 'desc')
            ->first();

        if (!$lastBilling) {
            return false;
        }

        $lastBillingDate = Carbon::parse($lastBilling->period_end);

        switch ($lease->billing_cycle) {
            case 'monthly':
                $nextBillingDate = $lastBillingDate->copy()->addDay();
                return $today >= $nextBillingDate;

            case 'quarterly':
                $nextBillingDate = $lastBillingDate->copy()->addDay();
                $monthsSinceLast = $lastBillingDate->diffInMonths($today);
                return $today >= $nextBillingDate && $monthsSinceLast >= 3;

            case 'annually':
                $nextBillingDate = $lastBillingDate->copy()->addDay();
                $yearsSinceLast = $lastBillingDate->diffInYears($today);
                return $today >= $nextBillingDate && $yearsSinceLast >= 1;
        }

        return false;
    }

    private function isFirstBilling(Lease $lease, $billingDate = null): bool
    {
        $query = LeaseBilling::where('lease_id', $lease->id);

        if ($billingDate) {
            $billingDate = $billingDate instanceof Carbon ? $billingDate : Carbon::parse($billingDate);
            $query->where('billing_date', '<', $billingDate->format('Y-m-d'));
        }

        return $query->count() === 0;
    }

    private function createBillingRecord(Lease $lease, Carbon $billingDate): ?LeaseBilling
    {
        return DB::transaction(function () use ($lease, $billingDate) {
            try {
                // Validate that lease has customer_id
                if (!$lease->customer_id) {
                    Log::error('Cannot create billing: Lease has no customer_id', [
                        'lease_id' => $lease->id
                    ]);
                    throw new \Exception('Lease has no customer associated');
                }

                $billingNumber = $this->generateBillingNumber();
                $periodStart = $this->getPeriodStart($lease, $billingDate);
                $periodEnd = $this->getPeriodEnd($lease, $billingDate);
                $dueDate = $billingDate->copy()->addDays(30);

                // Check if this is the first billing
                $isFirstBilling = !LeaseBilling::where('lease_id', $lease->id)->exists();

                // For first billing, adjust period to start from lease start date
                if ($isFirstBilling) {
                    $periodStart = $lease->start_date;

                    // Ensure period end is appropriate
                    if ($lease->billing_cycle === 'monthly') {
                        $periodEnd = $periodStart->copy()->endOfMonth();
                    } elseif ($lease->billing_cycle === 'quarterly') {
                        $periodEnd = $periodStart->copy()->addMonths(3)->subDay();
                    }

                    // Don't let period end exceed today
                    if ($periodEnd > $billingDate) {
                        $periodEnd = $billingDate;
                    }
                }

                // Calculate amount
                $amount = $lease->monthly_cost;

                // For first billing, include installation fee
                if ($isFirstBilling && $lease->installation_fee > 0) {
                    $amount += $lease->installation_fee;
                }

                $billing = LeaseBilling::create([
                    'lease_id' => $lease->id,
                    'customer_id' => $lease->customer_id, // CRITICAL: Added this line
                    'billing_number' => $billingNumber,
                    'billing_date' => $billingDate->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'amount' => $amount,
                    'currency' => $lease->currency ?? 'USD',
                    'billing_cycle' => $lease->billing_cycle,
                    'period_start' => $periodStart->format('Y-m-d'),
                    'period_end' => $periodEnd->format('Y-m-d'),
                    'status' => 'draft',
                    'is_first_billing' => $isFirstBilling,
                    'metadata' => json_encode([
                        'lease_start_date' => $lease->start_date->format('Y-m-d'),
                        'created_at' => now()->format('Y-m-d H:i:s'),
                    ])
                ]);

                Log::info('Billing record created successfully', [
                    'billing_id' => $billing->id,
                    'lease_id' => $lease->id,
                    'customer_id' => $lease->customer_id,
                    'amount' => $amount
                ]);

                // Send email notification with PDF
                $emailSent = $this->sendBillingEmail($billing, $lease);

                if ($emailSent) {
                    $billing->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                }

                return $billing;

            } catch (\Exception $e) {
                Log::error("Failed to create billing record for lease {$lease->id}: " . $e->getMessage(), [
                    'lease_id' => $lease->id,
                    'customer_id' => $lease->customer_id ?? 'missing',
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    public function createInitialBilling(Lease $lease): ?LeaseBilling
    {
        $today = Carbon::today();

        // Check if lease should start billing
        if ($lease->start_date > $today || $lease->status !== 'active') {
            Log::info('Lease not ready for initial billing', [
                'lease_id' => $lease->id,
                'start_date' => $lease->start_date,
                'status' => $lease->status
            ]);
            return null;
        }

        // Validate that lease has customer_id
        if (!$lease->customer_id) {
            Log::error('Cannot create initial billing: Lease has no customer_id', [
                'lease_id' => $lease->id
            ]);
            throw new \Exception('Lease has no customer associated');
        }

        // Check if billing already exists for current/previous period
        $existingBilling = LeaseBilling::where('lease_id', $lease->id)
            ->whereDate('period_start', '<=', $today)
            ->whereDate('period_end', '>=', $lease->start_date)
            ->first();

        if ($existingBilling) {
            Log::info('Initial billing already exists', [
                'lease_id' => $lease->id,
                'billing_id' => $existingBilling->id
            ]);
            return $existingBilling;
        }

        return $this->createBillingRecord($lease, $today);
    }

    private function getPeriodStart(Lease $lease, Carbon $billingDate): Carbon
    {
        $startDate = Carbon::parse($lease->start_date);

        if ($lease->billing_cycle === 'monthly') {
            return $billingDate->copy()->startOfMonth();
        }

        if ($lease->billing_cycle === 'quarterly') {
            $quarter = ceil($billingDate->month / 3);
            $quarterStartMonth = (($quarter - 1) * 3) + 1;
            return Carbon::create($billingDate->year, $quarterStartMonth, 1);
        }

        return $billingDate->copy()->startOfMonth();
    }

    private function getPeriodEnd(Lease $lease, Carbon $billingDate): Carbon
    {
        $periodStart = $this->getPeriodStart($lease, $billingDate);

        if ($lease->billing_cycle === 'monthly') {
            return $periodStart->copy()->endOfMonth();
        }

        if ($lease->billing_cycle === 'quarterly') {
            return $periodStart->copy()->addMonths(3)->subDay();
        }

        return $periodStart->copy()->endOfMonth();
    }

    private function getBillingPeriod(Lease $lease, Carbon $date): array
    {
        $start = $this->getPeriodStart($lease, $date);
        $end = $this->getPeriodEnd($lease, $date);

        return ['start' => $start, 'end' => $end];
    }

    private function sendBillingEmail(LeaseBilling $billing, Lease $lease): bool
    {
        try {
            $customerEmail = $this->getCustomerEmail($lease->customer_id);

            if (!$customerEmail) {
                Log::warning("No email found for customer {$lease->customer_id}");
                return false;
            }

            // Generate PDF content for email attachment
            $pdfContent = $this->invoicePdfService->generateAndGetContent($billing);
            $pdfFilename = "invoice_{$billing->billing_number}.pdf";

            // Convert dates to Carbon for the email template
            $billingDate = $billing->billing_date ? Carbon::parse($billing->billing_date) : null;
            $periodStart = $billing->period_start ? Carbon::parse($billing->period_start) : null;
            $periodEnd = $billing->period_end ? Carbon::parse($billing->period_end) : null;
            $dueDate = $billing->due_date ? Carbon::parse($billing->due_date) : null;

            Mail::send('emails.lease-billing', [
                'billing' => $billing,
                'lease' => $lease,
                'customer' => $lease->customer,
                'isFirstBilling' => $this->isFirstBilling($lease, $billing->billing_date),
                'billingDate' => $billingDate,
                'periodStart' => $periodStart,
                'periodEnd' => $periodEnd,
                'dueDate' => $dueDate,
            ], function ($message) use ($customerEmail, $billing, $lease, $pdfContent, $pdfFilename) {
                $message->to($customerEmail)
                    ->subject("Invoice {$billing->billing_number} - {$lease->lease_number} - {$lease->customer->name}")
                    ->attachData($pdfContent, $pdfFilename, [
                        'mime' => 'application/pdf',
                    ]);
            });

            // Also save PDF to storage for record keeping
            $this->invoicePdfService->generateInvoicePdf($billing);

            Log::info("Billing email sent for invoice {$billing->billing_number} to {$customerEmail}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send billing email for invoice {$billing->billing_number}: " . $e->getMessage());
            return false;
        }
    }

    private function getCustomerEmail($customerId): ?string
    {
        try {
            $customer = User::find($customerId);
            return $customer ? $customer->email : null;
        } catch (\Exception $e) {
            Log::error("Failed to get customer email for ID {$customerId}: " . $e->getMessage());
            return null;
        }
    }

    private function sendOverdueNotification(LeaseBilling $billing): void
    {
        try {
            $customerEmail = $this->getCustomerEmail($billing->lease->customer_id);

            if (!$customerEmail) {
                return;
            }

            $dueDate = $billing->due_date ? Carbon::parse($billing->due_date) : null;
            $overdueDays = $dueDate ? $dueDate->diffInDays(Carbon::today()) : 0;

            Mail::send('emails.lease-overdue', [
                'billing' => $billing,
                'lease' => $billing->lease,
                'customer' => $billing->lease->customer,
                'overdueDays' => $overdueDays,
            ], function ($message) use ($customerEmail, $billing) {
                $message->to($customerEmail)
                    ->subject("Overdue Invoice: {$billing->billing_number} - {$billing->lease->lease_number}");
            });

            Log::info("Overdue notification sent for invoice {$billing->billing_number}");

        } catch (\Exception $e) {
            Log::error("Failed to send overdue notification for invoice {$billing->billing_number}: " . $e->getMessage());
        }
    }

    /**
     * Get billing statistics for reporting
     */
    public function getBillingStatistics(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $startDate = $startDate ?: Carbon::today()->startOfMonth();
        $endDate = $endDate ?: Carbon::today()->endOfMonth();

        $stats = LeaseBilling::whereBetween('billing_date', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total_invoices')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(CASE WHEN status = "sent" THEN 1 END) as sent_invoices')
            ->selectRaw('COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_invoices')
            ->selectRaw('COUNT(CASE WHEN status = "overdue" THEN 1 END) as overdue_invoices')
            ->first();

        return [
            'total_invoices' => $stats->total_invoices ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'sent_invoices' => $stats->sent_invoices ?? 0,
            'paid_invoices' => $stats->paid_invoices ?? 0,
            'overdue_invoices' => $stats->overdue_invoices ?? 0,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
        ];
    }

    /**
     * Retry failed billing emails
     */
    public function retryFailedBillingEmails(int $hoursOld = 24): array
    {
        $failedBillings = LeaseBilling::with(['lease.customer'])
            ->where('status', 'draft')
            ->where('created_at', '<=', now()->subHours($hoursOld))
            ->get();

        $retriedCount = 0;
        $failedCount = 0;

        foreach ($failedBillings as $billing) {
            try {
                $emailSent = $this->sendBillingEmail($billing, $billing->lease);

                if ($emailSent) {
                    $billing->update([
                        'status' => 'sent',
                        'sent_at' => now()
                    ]);
                    $retriedCount++;
                    Log::info("Retry successful for invoice {$billing->billing_number}");
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Retry failed for invoice {$billing->billing_number}: " . $e->getMessage());
            }
        }

        return ['retried' => $retriedCount, 'failed' => $failedCount];
    }

    public function processOverdueBillings(): array
    {
        $overdueCount = LeaseBilling::where('status', 'sent')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return ['overdue_count' => $overdueCount];
    }

    /**
     * Process overdue invoices
     */
    public function processOverdueInvoices(): array
    {
        $overdueInvoices = LeaseBilling::with(['lease.customer'])
            ->where('status', 'sent')
            ->where('due_date', '<', Carbon::today())
            ->get();

        $processedCount = 0;

        foreach ($overdueInvoices as $invoice) {
            try {
                $invoice->update(['status' => 'overdue']);
                $this->sendOverdueNotification($invoice);
                $processedCount++;
                Log::info("Marked invoice {$invoice->billing_number} as overdue");
            } catch (\Exception $e) {
                Log::error("Failed to process overdue invoice {$invoice->billing_number}: " . $e->getMessage());
            }
        }

        return ['processed' => $processedCount];
    }

    public function getBillingStats(): array
    {
        return [
            'due_today' => User::whereHas('leaseBillings', function ($query) {
                $query->where('due_date', '<=', now()->format('Y-m-d'))
                      ->where('status', 'sent');
            })->count(),
            'pending_generation' => User::where('auto_billing_enabled', true)
                ->whereHas('leaseBillings', function ($query) {
                    $query->where('status', 'draft');
                })->count(),
            'auto_billing_customers' => User::where('auto_billing_enabled', true)->count(),
            'overdue_invoices' => LeaseBilling::where('status', 'overdue')->count(),
            'monthly_recurring_revenue' => LeaseBilling::where('billing_cycle', 'monthly')
                ->whereIn('status', ['sent', 'paid'])
                ->sum('amount'),
            'due_this_month' => User::whereHas('leaseBillings', function ($query) {
                $query->whereBetween('due_date', [
                    now()->startOfMonth()->format('Y-m-d'),
                    now()->endOfMonth()->format('Y-m-d')
                ])->where('status', 'sent');
            })->count(),
        ];
    }

    public function generateInvoices(): array
    {
        $generated = 0;
        $errors = [];

        $customers = User::where('auto_billing_enabled', true)
            ->whereHas('leases')
            ->with(['leases' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        foreach ($customers as $customer) {
            try {
                foreach ($customer->leases as $lease) {
                    $existingBilling = LeaseBilling::where('lease_id', $lease->id)
                        ->where('customer_id', $customer->id)
                        ->whereIn('status', ['draft', 'sent'])
                        ->whereBetween('billing_date', [
                            now()->startOfMonth(),
                            now()->endOfMonth()
                        ])
                        ->first();

                    if ($existingBilling) {
                        continue;
                    }

                    $this->createBillingRecord($lease, now());
                    $generated++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to generate billing for customer {$customer->name}: " . $e->getMessage();
                Log::error("Billing generation failed for customer {$customer->id}: " . $e->getMessage());
            }
        }

        return ['generated' => $generated, 'errors' => $errors];
    }

    private function generateBillingNumber(): string
    {
        $prefix = 'BL-';
        $year = date('Y');
        $month = date('m');

        do {
            $random = Str::upper(Str::random(6));
            $billingNumber = "{$prefix}{$year}{$month}-{$random}";
        } while (LeaseBilling::where('billing_number', $billingNumber)->exists());

        return $billingNumber;
    }
}
