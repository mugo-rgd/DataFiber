<?php
// app/Services/ContractGenerationService.php
namespace App\Services;

use App\Models\Quotation;
use App\Models\Contract;
use App\Models\ContractApproval;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContractGenerationService
{
    public function generateContract(Quotation $quotation): Contract
    {
        $contractContent = $this->generateContractContent($quotation);

        $contract = Contract::create([
            'quotation_id' => $quotation->id,
            'contract_number' => $this->generateContractNumber(),
            'contract_content' => $contractContent,
            'status' => 'draft'
        ]);

           ContractApproval::create([
    'contract_id' => $contract->id,
    'approved_by' => null, // Use NULL for system actions
    'notes' => 'Contract automatically generated from approved quotation'
]);

        // Notify admin
        $this->notifyAdminContractGenerated($contract);

        return $contract;
    }

    protected function generateContractNumber(): string
    {
        return 'CONTRACT-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6));
    }

    protected function generateContractContent(Quotation $quotation): string
    {
        $designRequest = $quotation->designRequest;
        $customer = $quotation->customer;

        return view('contracts.templates.master-agreement', [
            'quotation' => $quotation,
            'designRequest' => $designRequest,
            'customer' => $customer,
            'generatedDate' => now()->format('F j, Y')
        ])->render();
    }

    public function notifyAdminContractGenerated(Contract $contract): void
    {
        $adminEmail = 'accountmanager_admin@kplc.co.ke'; // Replace with actual admin email

        Mail::send('emails.contract-generated', ['contract' => $contract], function ($message) use ($adminEmail, $contract) {
            $message->to($adminEmail)
                   ->subject("New Contract Generated - {$contract->contract_number}")
                   ->priority(1);
        });
    }

    public function notifyAdminCustomerApproved(Contract $contract): void
    {
        $adminEmail = 'accountmanager_admin@kplc.co.ke';

        Mail::send('emails.customer-approved', ['contract' => $contract], function ($message) use ($adminEmail, $contract) {
            $message->to($adminEmail)
                   ->subject("Contract Approved by Customer - {$contract->contract_number}")
                   ->priority(1);
        });
    }

  // In your ContractGenerationService
public function generatePdf(Contract $contract)
{
    // Load all necessary relationships
    $contract->load([
        'quotation.customer',
        'quotation.designRequest'
    ]);

    $pdf = Pdf::loadView('contracts.pdf.master-agreement', compact('contract'));

    return $pdf->setPaper('a4')->setOption('defaultFont', 'DejaVu Sans');
}

    ///

    /**
     * Notify customer when contract is sent for approval
     */
    public function notifyCustomerContractSent(Contract $contract): void
    {
        $customer = $contract->quotation->customer;

        // Send email notification
        Mail::send('emails.contract-sent', ['contract' => $contract, 'customer' => $customer],
            function ($message) use ($customer, $contract) {
                $message->to($customer->email)
                       ->subject("Contract Ready for Review - {$contract->contract_number}")
                       ->priority(1);
            });

        // If you have notification system, you can also use:
        // $customer->notify(new ContractSent($contract));

        Log::info("Contract sent notification sent to customer", [
            'customer_id' => $customer->id,
            'customer_email' => $customer->email,
            'contract_id' => $contract->id,
            'contract_number' => $contract->contract_number
        ]);
    }

    /**
     * Notify customer when contract is rejected by admin/account manager
     */
    public function notifyCustomerContractRejected(Contract $contract, string $rejectionReason = null): void
    {
        $customer = $contract->quotation->customer;

        Mail::send('emails.contract-rejected', [
            'contract' => $contract,
            'customer' => $customer,
            'rejectionReason' => $rejectionReason
        ], function ($message) use ($customer, $contract) {
            $message->to($customer->email)
                   ->subject("Contract Revision Required - {$contract->contract_number}")
                   ->priority(1);
        });

        Log::info("Contract rejected notification sent to customer", [
            'customer_id' => $customer->id,
            'contract_id' => $contract->id,
            'rejection_reason' => $rejectionReason
        ]);
    }

    /**
     * Notify customer when contract is approved by admin/account manager
     */
    public function notifyCustomerContractApproved(Contract $contract): void
    {
        $customer = $contract->quotation->customer;

        Mail::send('emails.contract-approved', ['contract' => $contract, 'customer' => $customer],
            function ($message) use ($customer, $contract) {
                $message->to($customer->email)
                       ->subject("Contract Approved - {$contract->contract_number}")
                       ->priority(1);
            });

        Log::info("Contract approved notification sent to customer", [
            'customer_id' => $customer->id,
            'contract_id' => $contract->id
        ]);
    }

      /**
     * Notify admin when customer rejects contract
     */
    public function notifyAdminCustomerRejected(Contract $contract, string $rejectionReason = null): void
    {
        $adminEmail = 'accountmanager_admin@kplc.co.ke';

        Mail::send('emails.customer-rejected', [
            'contract' => $contract,
            'rejectionReason' => $rejectionReason
        ], function ($message) use ($adminEmail, $contract) {
            $message->to($adminEmail)
                   ->subject("Contract Rejected by Customer - {$contract->contract_number}")
                   ->priority(1);
        });
    }
}
