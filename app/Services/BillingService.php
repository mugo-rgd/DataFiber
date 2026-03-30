<?php

namespace App\Services;

use App\Models\LeaseBilling;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BillingService
{
    public function generateBillingNumber(): string
    {
        $date = now()->format('Ymd');
        $lastBilling = LeaseBilling::where('billing_number', 'like', "INV-{$date}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBilling) {
            $lastNumber = intval(substr($lastBilling->billing_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "INV-{$date}-{$newNumber}";
    }

    public function generateTransactionId(): string
    {
        $prefix = 'TXN-';
        $year = date('Y');

        do {
            $random = Str::upper(Str::random(8));
            $transactionId = "{$prefix}{$year}-{$random}";
        } while (Transaction::where('reference_number', $transactionId)->exists());

        return $transactionId;
    }

    public function markBillingAsPaid(LeaseBilling $billing, array $paymentData, $userId)
    {
        return DB::transaction(function () use ($billing, $paymentData, $userId) {
            // Update billing status
            $billing->update([
                'status' => 'paid',
                'paid_at' => $paymentData['payment_date']
            ]);

            // Record payment transaction
            return Transaction::create([
                'type' => 'income',
                'amount' => $billing->total_amount,
                'description' => 'Payment for billing ' . $billing->billing_number,
                'transaction_date' => $paymentData['payment_date'],
                'payment_method' => $paymentData['payment_method'],
                'category' => 'invoice_payment',
                'status' => 'completed',
                'customer_id' => $billing->customer_id,
                'billing_id' => $billing->id,
                'reference_number' => $paymentData['reference_number'] ?? $this->generateTransactionId(),
                'notes' => $paymentData['notes'] ?? null,
                'created_by' => $userId
            ]);
        });
    }

    public function calculateCollectionRate(): float
    {
        $totalBilled = LeaseBilling::sum('total_amount');
        $totalCollected = LeaseBilling::where('status', 'paid')->sum('total_amount');

        return $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 2) : 0;
    }

    public function getAgingReport()
    {
        return LeaseBilling::with('customer')
            ->whereIn('status', ['pending', 'overdue'])
            ->selectRaw('customer_id,
                SUM(CASE WHEN due_date >= CURDATE() THEN total_amount ELSE 0 END) as current,
                SUM(CASE WHEN due_date < CURDATE() AND due_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN total_amount ELSE 0 END) as days_30,
                SUM(CASE WHEN due_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND due_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN total_amount ELSE 0 END) as days_60,
                SUM(CASE WHEN due_date < DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN total_amount ELSE 0 END) as days_90_plus')
            ->groupBy('customer_id')
            ->get();
    }

    public function getScheduledBillings($limit = 10)
    {
        return LeaseBilling::with(['customer', 'lease'])
            ->where('due_date', '>=', now())
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->limit($limit)
            ->get();
    }
}
