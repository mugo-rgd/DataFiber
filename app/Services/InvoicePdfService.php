<?php

namespace App\Services;

use App\Models\LeaseBilling;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvoicePdfService
{
    protected PDF $pdf;

    public function __construct(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * Generate PDF and return PDF object for download
     */
    public function generatePdf(LeaseBilling $billing)
    {
        $data = $this->prepareInvoiceData($billing);

        $pdf = $this->pdf->loadView('pdf.invoice', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'dejavu sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'chroot' => public_path(),
        ]);

        return $pdf;
    }

    /**
     * Generate invoice record (for custom invoices)
     */
    public function generateInvoice($lease, array $data = [])
    {
        try {
            Log::info('Generating invoice from lease', [
                'lease_id' => $lease->id,
                'customer_id' => $lease->customer_id,
                'data' => $data
            ]);

            // Create the billing record
            $billing = LeaseBilling::create([
                'billing_number' => $this->generateBillingNumber(),
                'lease_id' => $lease->id,
                'amount' => $data['amount'] ?? $lease->monthly_cost,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total_amount' => ($data['amount'] ?? $lease->monthly_cost) + ($data['tax_amount'] ?? 0),
                'billing_date' => $data['invoice_date'] ?? now(),
                'due_date' => $data['due_date'] ?? now()->addDays(30),
                'status' => $data['status'] ?? 'draft',
                'description' => $data['description'] ?? "Lease billing for {$lease->service_type}",
                'notes' => $data['notes'] ?? null,
            ]);

            // Generate PDF
            $this->generateInvoicePdf($billing);

            Log::info('Invoice generated successfully', ['billing_id' => $billing->id]);

            return $billing;

        } catch (\Exception $e) {
            Log::error('Invoice generation failed: ' . $e->getMessage());
            throw new \Exception('Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique billing number
     */
    private function generateBillingNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');

        do {
            $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $billingNumber = "{$prefix}-{$date}-{$random}";
        } while (LeaseBilling::where('billing_number', $billingNumber)->exists());

        return $billingNumber;
    }

    /**
     * Generate PDF and save to storage
     */
    public function generateInvoicePdf(LeaseBilling $billing): string
    {
        $pdf = $this->generatePdf($billing);
        $filename = "invoice_{$billing->billing_number}.pdf";
        $filepath = "invoices/{$filename}";

        // Save the PDF to storage
        Storage::put($filepath, $pdf->output());
        return Storage::path($filepath);
    }

    /**
     * Generate PDF and return content as string
     */
    public function generateAndGetContent(LeaseBilling $billing): string
    {
        $pdf = $this->generatePdf($billing);
        return $pdf->output();
    }

    /**
     * Send invoice via email
     */
    public function sendInvoice(LeaseBilling $billing): bool
    {
        try {
            // Generate PDF content
            $pdfContent = $this->generateAndGetContent($billing);

            // Get customer email
            $customerEmail = $billing->lease->customer->email ?? null;

            if (!$customerEmail) {
                Log::error('No customer email found for billing: ' . $billing->id);
                return false;
            }

            // Send email with invoice attached
            Mail::send('emails.invoice', ['billing' => $billing], function ($message) use ($billing, $pdfContent, $customerEmail) {
                $message->to($customerEmail)
                        ->subject('Invoice ' . $billing->billing_number)
                        ->attachData($pdfContent, "invoice_{$billing->billing_number}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
            });

            // Update billing record
            $billing->update([
                'sent_at' => now(),
                'status' => 'sent'
            ]);

            Log::info("Invoice {$billing->billing_number} sent to {$customerEmail}");
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send invoice: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if this is the first billing for the lease
     */
    public function isFirstBilling(LeaseBilling $billing): bool
    {
        return LeaseBilling::where('lease_id', $billing->lease_id)
            ->where('id', '<', $billing->id)
            ->count() === 0;
    }

    /**
     * Calculate total amount including installation fee for first billing
     */
    public function calculateTotal(LeaseBilling $billing): float
    {
        $total = $billing->amount;

        if ($this->isFirstBilling($billing) && $billing->lease->installation_fee > 0) {
            $total += $billing->lease->installation_fee;
        }

        return $total;
    }

    private function prepareInvoiceData(LeaseBilling $billing): array
    {
        $lease = $billing->lease;
        $customer = $lease->customer;

        return [
            'billing' => $billing,
            'lease' => $lease,
            'customer' => $customer,
            'company' => $this->getCompanyInfo(),
            'bankDetails' => $this->getBankDetails(),
            'isFirstBilling' => $this->isFirstBilling($billing),
            'totalAmount' => $this->calculateTotal($billing),
        ];
    }

    private function getCompanyInfo(): array
    {
        return [
            'name' => config('app.company.name', 'Your Company Name'),
            'address' => config('app.company.address', '123 Business Street'),
            'city' => config('app.company.city', 'Business City'),
            'zip' => config('app.company.zip', '12345'),
            'phone' => config('app.company.phone', '+1 (555) 123-4567'),
            'email' => config('app.company.email', 'billing@company.com'),
            'website' => config('app.company.website', 'www.company.com'),
            'tax_id' => config('app.company.tax_id', 'TAX-123456789'),
        ];
    }

    private function getBankDetails(): array
    {
        return [
            'bank_name' => config('app.bank.name', 'Main Business Bank'),
            'account_name' => config('app.bank.account_name', 'Your Company Name'),
            'account_number' => config('app.bank.account_number', 'XXXX-XXXX-XXXX-XXXX'),
            'routing_number' => config('app.bank.routing_number', 'XXXXXXXXX'),
            'iban' => config('app.bank.iban', 'IBAN-XXXX-XXXX'),
            'swift' => config('app.bank.swift', 'SWIFT-XXX'),
        ];
    }

    /**
     * Get the path to a previously generated invoice PDF
     */
    public function getInvoicePath(LeaseBilling $billing): ?string
    {
        $filename = "invoice_{$billing->billing_number}.pdf";
        $filepath = "invoices/{$filename}";

        return Storage::exists($filepath) ? Storage::path($filepath) : null;
    }

    /**
     * Delete invoice PDF from storage
     */
    public function deleteInvoicePdf(LeaseBilling $billing): bool
    {
        $filename = "invoice_{$billing->billing_number}.pdf";
        $filepath = "invoices/{$filename}";

        return Storage::delete($filepath);
    }
}
