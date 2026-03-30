<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuotationSentToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function build()
    {
        // Check if the custom view exists, otherwise use a simple text email
        $viewExists = view()->exists('emails.quotations.sent-to-customer');

        if ($viewExists) {
            return $this->subject('Quotation #' . $this->quotation->quotation_number . ' - ' . config('app.name'))
                        ->view('emails.quotations.sent-to-customer')
                        ->with(['quotation' => $this->quotation]);
        } else {
            // Fallback to simple text email
            return $this->subject('Quotation #' . $this->quotation->quotation_number . ' - ' . config('app.name'))
                        ->text('emails.quotations.sent-to-customer-text')
                        ->with(['quotation' => $this->quotation]);
        }
    }
}
