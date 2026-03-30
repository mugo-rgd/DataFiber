<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AutomatedBillingService;

class GenerateAutoBillings extends Command
{
    protected $signature = 'billing:generate';
    protected $description = 'Generate automated billings for customers due for billing';

     public function handle(AutomatedBillingService $billingService)
{
    $this->info('Starting automated billing generation...');

    $result = $billingService->generateInvoices();

    $this->info("Successfully generated {$result['generated']} invoices.");

    if (!empty($result['errors'])) {
        $this->error('Errors encountered:');
        foreach ($result['errors'] as $error) {
            $this->error("- {$error}");
        }
    }

    // Process overdue invoices - extract the processed count from array
    $overdueResult = $billingService->processOverdueInvoices();
    $overdueCount = $overdueResult['processed'] ?? 0;

    $this->info("Marked {$overdueCount} invoices as overdue.");

    // Show stats
    $stats = $billingService->getBillingStats();
    $this->info("\nBilling Statistics:");
    $this->info("- Customers due today: {$stats['due_today']}");
    $this->info("- Pending generation: {$stats['pending_generation']}");
    $this->info("- Auto-billing enabled: {$stats['auto_billing_customers']}");
}
}
