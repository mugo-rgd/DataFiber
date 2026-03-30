<?php
// app/Mail/PaymentStatementMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentStatementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $statement;

    // Remove type hint to accept any model
    public function __construct($statement)
    {
        $this->statement = $statement;
    }

    public function build()
    {
        $email = $this->subject('Payment Statement - ' . $this->statement->statement_number)
            ->view('emails.payment-statement')
            ->with([
                'customerName' => $this->statement->customer->name ?? 'Customer',
                'statementNumber' => $this->statement->statement_number,
                'period' => $this->formatPeriod(),
                'closingBalance' => $this->statement->closing_balance
            ]);

        // Attach PDF if exists
        if (isset($this->statement->file_path) && Storage::disk('public')->exists($this->statement->file_path)) {
            $email->attach(Storage::disk('public')->path($this->statement->file_path), [
                'as' => 'statement_' . $this->statement->statement_number . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }

    private function formatPeriod()
    {
        if (isset($this->statement->period_start) && isset($this->statement->period_end)) {
            return $this->statement->period_start->format('d/m/Y') . ' - ' . $this->statement->period_end->format('d/m/Y');
        }

        if (isset($this->statement->period)) {
            return $this->statement->period;
        }

        return now()->format('F Y');
    }
}
