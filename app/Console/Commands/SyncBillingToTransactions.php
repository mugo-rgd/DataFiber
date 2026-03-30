<?php
// app/Console/Commands/SyncBillingToTransactions.php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\ConsolidatedBilling;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SyncBillingToTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:sync-transactions
                            {--user= : Sync only for specific user ID}
                            {--from= : Start date (Y-m-d)}
                            {--to= : End date (Y-m-d)}
                            {--force : Force sync even if transaction exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync consolidated billings to transactions table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting billing to transactions sync...');

        // Build query
        $query = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'paid', 'partial']);

        // Apply user filter
        if ($userId = $this->option('user')) {
            $query->where('user_id', $userId);
            $this->info("Filtering for user ID: {$userId}");
        }

        // Apply date filters
        if ($fromDate = $this->option('from')) {
            $query->whereDate('billing_date', '>=', Carbon::parse($fromDate));
            $this->info("From date: {$fromDate}");
        }

        if ($toDate = $this->option('to')) {
            $query->whereDate('billing_date', '<=', Carbon::parse($toDate));
            $this->info("To date: {$toDate}");
        }

        $billings = $query->orderBy('user_id')->orderBy('billing_date')->get();

        if ($billings->isEmpty()) {
            $this->warn('No billings found to process.');
            return 0;
        }

        $this->info('Found ' . $billings->count() . ' billings to process.');

        // Group by user for balance calculation
        $billingsByUser = $billings->groupBy('user_id');

        $stats = [
            'created' => 0,
            'skipped' => 0,
            'updated' => 0,
            'errors' => 0
        ];

        $bar = $this->output->createProgressBar($billings->count());
        $bar->start();

        foreach ($billings as $billing) {
            try {
                $result = $this->syncBilling($billing, $this->option('force'));
                $stats[$result]++;
            } catch (\Exception $e) {
                $this->error("\nError processing billing {$billing->billing_number}: " . $e->getMessage());
                $stats['errors']++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display summary
        $this->table(
            ['Created', 'Skipped', 'Updated', 'Errors'],
            [[$stats['created'], $stats['skipped'], $stats['updated'], $stats['errors']]]
        );

        $this->info('Sync completed successfully!');

        return 0;
    }

    /**
     * Sync a single billing to transaction
     */
    private function syncBilling($billing, $force = false)
    {
        // Check if transaction already exists
        $existingTransaction = Transaction::where('reference', $billing->billing_number)
            ->where('type', 'invoice')
            ->first();

        if ($existingTransaction && !$force) {
            return 'skipped';
        }

        // Get previous balance
        $previousTransaction = Transaction::where('user_id', $billing->user_id)
            ->where('transaction_date', '<', $billing->billing_date)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $previousBalance = $previousTransaction ? $previousTransaction->balance : 0;

        // Prepare transaction data
        $transactionData = [
            'user_id' => $billing->user_id,
            'transaction_number' => $this->generateTransactionNumber($billing),
            'transaction_date' => $billing->billing_date,
            'type' => 'invoice',
            'description' => $billing->description ?? "Invoice {$billing->billing_number}",
            'amount' => $billing->total_amount,
            'currency' => $billing->currency ?? 'USD',
            'direction' => 'in',
            'balance' => $previousBalance + $billing->total_amount,
            'reference' => $billing->billing_number,
            'status' => 'completed',
        ];

        if ($existingTransaction && $force) {
            // Update existing
            $existingTransaction->update($transactionData);
            return 'updated';
        } else {
            // Create new
            Transaction::create($transactionData);
            return 'created';
        }
    }

    /**
     * Generate a unique transaction number
     */
    private function generateTransactionNumber($billing): string
    {
        // Try to use parts of the billing number if available
        if (preg_match('/INV-(\d+)/', $billing->billing_number, $matches)) {
            return 'TXN-' . $matches[1] . '-' . date('Ymd');
        }

        // Fallback to generated number
        $prefix = 'TXN';
        $date = now()->format('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$date}{$random}";
    }
}
