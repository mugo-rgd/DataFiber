<?php

namespace App\Mail;

use App\Models\LeaseBilling;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class BillingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $billing;
    public $customer;
    public $invoiceUrl;

    public function __construct(LeaseBilling $billing)
    {
        $this->billing = $billing;
        $this->customer = $billing->customer;

        // Generate the invoice URL
        $this->invoiceUrl = url('/finance/billing/' . $billing->id);
    }

    public function build()
    {
        // Get the customer name for subject line
        $customerName = $this->customer ? $this->customer->name : 'Customer';
        $invoiceNumber = $this->billing->billing_number ?? 'N/A';

        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->replyTo('support@darkfibre-crm.figtreealliance.com', 'DarkFibre CRM Support')
                    ->subject("Invoice #{$invoiceNumber} Payment Due - DarkFibre CRM")
                    ->view('emails.billing_reminder')
                    ->with([
                        'billing' => $this->billing,
                        'customer' => $this->customer,
                        'invoiceUrl' => $this->invoiceUrl,
                        'due_date_formatted' => $this->billing->due_date ?
                            \Carbon\Carbon::parse($this->billing->due_date)->format('F d, Y') : 'N/A',
                        'total_amount_formatted' => number_format($this->billing->total_amount ?? 0, 2),
                    ])
                    ->attachData($this->generateInvoicePDF(), "Invoice-{$invoiceNumber}.pdf", [
                        'mime' => 'application/pdf',
                    ])
                    ->withSwiftMessage(function ($message) {
                        // Add headers to avoid spam filters
                        $headers = $message->getHeaders();

                        // Essential anti-spam headers
                        $headers->addTextHeader('Precedence', 'bulk');
                        $headers->addTextHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
                        $headers->addTextHeader('Auto-Submitted', 'auto-generated');

                        // List-Unsubscribe header (helps with deliverability)
                        $headers->addTextHeader(
                            'List-Unsubscribe',
                            '<mailto:support@darkfibre-crm.figtreealliance.com?subject=Unsubscribe%20Billing%20Reminders>'
                        );

                        // Add message ID for tracking
                        $messageId = time() . '.' . ($this->billing->id ?? '0') . '@darkfibre-crm.figtreealliance.com';
                        $headers->addIdHeader('Message-ID', $messageId);

                        // Add priority headers
                        $headers->addTextHeader('X-Priority', '3'); // Normal priority
                        $headers->addTextHeader('X-MSMail-Priority', 'Normal');
                        $headers->addTextHeader('Importance', 'Normal');

                        // Custom headers for tracking
                        $headers->addTextHeader('X-CRM-Billing-ID', $this->billing->id ?? '0');
                        $headers->addTextHeader('X-CRM-Invoice-Number', $this->billing->billing_number ?? 'N/A');
                    });
    }

    /**
     * Generate PDF for the invoice
     */
    private function generateInvoicePDF()
    {
        try {
            // Check if dompdf is installed
            if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                throw new \Exception('PDF generation package not installed. Run: composer require barryvdh/laravel-dompdf');
            }

            // Load the invoice PDF view
            $pdf = Pdf::loadView('finance.billing.pdf', [
                'billing' => $this->billing,
                'customer' => $this->customer,
                'line_items' => $this->billing->lineItems ?? [],
                'company' => [
                    'name' => 'DarkFibre CRM',
                    'address' => 'Nairobi, Kenya',
                    'phone' => '+254 XXX XXX XXX',
                    'email' => 'billing@darkfibre-crm.figtreealliance.com',
                    'website' => 'https://darkfibre-crm.figtreealliance.com',
                ]
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            return $pdf->output();

        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return null; // Return null if PDF generation fails
        }
    }
}
