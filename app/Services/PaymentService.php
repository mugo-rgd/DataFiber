<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\CustomerCredit;
use App\Models\ConsolidatedBilling;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    /**
     * Process payment with allocations
     */
    public function processPayment(array $data, array $allocations = [], array $excessData = null): Payment
    {
        return DB::transaction(function () use ($data, $allocations, $excessData) {
            // Create payment
            $payment = Payment::create($data);

            // Process allocations
            if (!empty($allocations)) {
                foreach ($allocations as $allocation) {
                    PaymentAllocation::create([
                        'payment_id' => $payment->id,
                        'invoice_id' => $allocation['invoice_id'],
                        'allocated_amount' => $allocation['allocated_amount'],
                        'currency' => $payment->currency,
                    ]);

                    // Update invoice paid amount
                    $this->updateInvoicePayment($allocation['invoice_id'], $allocation['allocated_amount']);
                }
            }

            // Process excess as customer credit
            if ($excessData && isset($excessData['excess']) && $excessData['excess'] > 0) {
                $this->createCustomerCredit(
                    $data['user_id'],
                    $excessData['excess'],
                    $payment->currency,
                    $payment->id,
                    $excessData['notes'] ?? 'Excess payment from ' . $payment->payment_number
                );
            }

            return $payment;
        });
    }

    /**
     * Update invoice payment status
     */
    protected function updateInvoicePayment(int $invoiceId, float $amount): void
    {
        $invoice = ConsolidatedBilling::findOrFail($invoiceId);
        $currentPaid = $invoice->paid_amount ?? 0;
        $newPaid = $currentPaid + $amount;

        $invoice->update([
            'paid_amount' => $newPaid,
            'status' => $newPaid >= $invoice->total_amount ? 'paid' : 'partial',
            'paid_date' => $newPaid >= $invoice->total_amount ? now() : $invoice->paid_date,
        ]);
    }

    /**
     * Create customer credit
     */
    protected function createCustomerCredit(int $userId, float $amount, string $currency, int $paymentId, string $notes = null): CustomerCredit
    {
        $credit = CustomerCredit::firstOrCreate(
            [
                'user_id' => $userId,
                'currency' => $currency,
                'status' => CustomerCredit::STATUS_ACTIVE,
            ],
            [
                'amount' => 0,
                'notes' => $notes,
            ]
        );

        $credit->addAmount($amount, 'PAY-' . $paymentId, $notes);

        return $credit;
    }

    /**
     * Validate a payment
     */
    public function validatePayment(Payment $payment, int $validatorId, array $validatedData = []): bool
    {
        return DB::transaction(function () use ($payment, $validatorId, $validatedData) {
            $payment->validatePayment($validatorId);

            // If there are additional validations or adjustments, process them here
            if (!empty($validatedData)) {
                $payment->update($validatedData);
            }

            return true;
        });
    }

    /**
     * Reject a payment
     */
    public function rejectPayment(Payment $payment, int $validatorId, string $reason): bool
    {
        return DB::transaction(function () use ($payment, $validatorId, $reason) {
            $payment->rejectPayment($validatorId, $reason);

            // Reverse any allocations if they were made
            if ($payment->allocations()->exists()) {
                foreach ($payment->allocations as $allocation) {
                    $this->reverseInvoicePayment($allocation->invoice_id, $allocation->allocated_amount);
                }
                $payment->allocations()->delete();
            }

            return true;
        });
    }

    /**
     * Reverse invoice payment
     */
    protected function reverseInvoicePayment(int $invoiceId, float $amount): void
    {
        $invoice = ConsolidatedBilling::findOrFail($invoiceId);
        $currentPaid = $invoice->paid_amount ?? 0;
        $newPaid = max(0, $currentPaid - $amount);

        $invoice->update([
            'paid_amount' => $newPaid,
            'status' => $newPaid <= 0 ? 'pending' : ($newPaid >= $invoice->total_amount ? 'paid' : 'partial'),
        ]);
    }
}
