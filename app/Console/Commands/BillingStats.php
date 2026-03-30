<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AutomatedBillingService;

class BillingStats extends Command
{
    protected $signature = 'billing:stats';
    protected $description = 'Show automated billing statistics';

    public function handle(AutomatedBillingService $billingService)
    {
        $stats = $billingService->getBillingStats();

        $this->info('=== Automated Billing Statistics ===');
        $this->info("Customers due for billing today: {$stats['due_today']}");
        $this->info("Pending invoice generation: {$stats['pending_generation']}");
        $this->info("Total auto-billing customers: {$stats['auto_billing_customers']}");
        $this->info("Invoices generated today: {$stats['recently_generated']}");
    }
}
