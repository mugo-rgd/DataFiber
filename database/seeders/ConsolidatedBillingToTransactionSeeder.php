<?php
// database/seeders/ConsolidatedBillingToTransactionSeeder.php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\ConsolidatedBilling;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ConsolidatedBillingToTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all consolidated billings with status 'pending' or 'sent' or 'paid'
        $billings = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'paid', 'partial'])
            ->get();

        $this->command->info('Found ' . $billings->count() . ' billings to process...');

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($billings as $billing) {
            // Check if transaction already exists for this billing
            $existingTransaction = Transaction::where('reference', $billing->billing_number)
                ->where('type', 'invoice')
                ->first();

            if ($existingTransaction) {
                $this->command->warn("Transaction already exists for billing: {$billing->billing_number}");
                $skippedCount++;
                continue;
            }

            // Determine direction (invoice is always 'in' for money coming in)
            $direction = 'in';

            // Determine transaction type
            $type = 'invoice';

            // Create description
            $description = $billing->description ?? "Invoice {$billing->billing_number}";

            // Get previous balance for this user
            $previousTransaction = Transaction::where('user_id', $billing->user_id)
                ->where('transaction_date', '<', $billing->billing_date)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $previousBalance = $previousTransaction ? $previousTransaction->balance : 0;

            // Calculate new balance (adding invoice amount)
            $newBalance = $previousBalance + $billing->total_amount;

            // Create transaction
            try {
                Transaction::create([
                    'user_id' => $billing->user_id,
                    'transaction_number' => $this->generateTransactionNumber(),
                    'transaction_date' => $billing->billing_date,
                    'type' => $type,
                    'description' => $description,
                    'amount' => $billing->total_amount,
                    'currency' => $billing->currency ?? 'USD',
                    'direction' => $direction,
                    'balance' => $newBalance,
                    'reference' => $billing->billing_number,
                    'status' => 'completed',
                    'created_at' => $billing->created_at,
                    'updated_at' => $billing->updated_at,
                ]);

                $createdCount++;
                $this->command->info("Created transaction for billing: {$billing->billing_number}");

            } catch (\Exception $e) {
                $this->command->error("Error creating transaction for billing {$billing->billing_number}: " . $e->getMessage());
            }
        }

        $this->command->info("Completed! Created: {$createdCount}, Skipped: {$skippedCount}");
    }

    /**
     * Generate a unique transaction number
     */
    private function generateTransactionNumber(): string
    {
        $prefix = 'TXN';
        $date = now()->format('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$date}{$random}";
    }
}
