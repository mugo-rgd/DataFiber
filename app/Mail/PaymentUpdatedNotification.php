<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ConsolidatedBilling;

class PaymentUpdatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $summary;

    public function __construct(ConsolidatedBilling $payment)
    {
        $this->payment = $payment;
        $this->summary = $this->calculateSummary($payment);
    }

    public function build()
    {
        return $this->subject('Payment Update - ' . $this->payment->billing_number)
                    ->view('emails.payment-updated')
                    ->with([
                        'payment' => $this->payment,
                        'summary' => $this->summary,
                    ]);
    }

    private function calculateSummary(ConsolidatedBilling $payment)
    {
        $totalKES = $payment->total_amount_kes ?? $payment->total_amount;
        $paidKES = $payment->paid_amount_kes ?? $payment->paid_amount ?? 0;
        $balanceKES = $totalKES - $paidKES;

        return [
            'total_kes' => $totalKES,
            'paid_kes' => $paidKES,
            'balance_kes' => $balanceKES,
            'payment_percentage' => $totalKES > 0 ? ($paidKES / $totalKES) * 100 : 0,
        ];
    }
}
