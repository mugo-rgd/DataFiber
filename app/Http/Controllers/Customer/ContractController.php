<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Services\ContractGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractGenerationService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index()
    {
        $contracts = Contract::with(['quotation', 'approvals'])
            ->whereHas('quotation', function ($query) {
                $query->where('customer_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('customer.contracts.index', compact('contracts'));
    }

    public function show($contractId)
    {
        $contract = Contract::with(['quotation', 'approvals'])
            ->whereHas('quotation', function ($query) {
                $query->where('customer_id', Auth::id());
            })
            ->findOrFail($contractId);

        return view('customer.contracts.show', compact('contract'));
    }

    public function approve(Request $request, $contractId)
    {
        $contract = Contract::with('quotation')
            ->whereHas('quotation', function ($query) {
                $query->where('customer_id', Auth::id());
            })
            ->findOrFail($contractId);

        if (!$contract->canBeApprovedByCustomer()) {
            return redirect()->back()->with('error', 'This contract cannot be approved at this time.');
        }

        $contract->approveByCustomer();

        // Notify admin
        $this->contractService->notifyAdminCustomerApproved($contract);

        return redirect()->route('customer.contracts.show', $contract->id)
            ->with('success', 'Contract approved successfully! Sent to admin for final approval.');
    }

    public function downloadPdf($contractId)
    {
        $contract = Contract::with('quotation')
            ->whereHas('quotation', function ($query) {
                $query->where('customer_id', Auth::id());
            })
            ->findOrFail($contractId);

        $pdf = $this->contractService->generatePdf($contract);

        return $pdf->download("contract-{$contract->contract_number}.pdf");
    }

public function sendToCustomer(Contract $contract)
{
    // Update contract status
    $contract->update(['status' => 'sent']);

    // Notify customer
    $contractService = app(ContractGenerationService::class);
    $contractService->notifyCustomerContractSent($contract);

    return redirect()->back()->with('success', 'Contract sent to customer successfully.');
}

public function approveContract(Contract $contract)
{
    // Update contract status
    $contract->update(['status' => 'approved']);

    // Notify customer
    $contractService = app(ContractGenerationService::class);
    $contractService->notifyCustomerContractApproved($contract);

    return redirect()->back()->with('success', 'Contract approved successfully.');
}

public function rejectContract(Contract $contract, Request $request)
{
    // Update contract status
    $contract->update(['status' => 'rejected']);

    // Notify customer with reason
    $contractService = app(ContractGenerationService::class);
    $contractService->notifyCustomerContractRejected($contract, $request->rejection_reason);

    return redirect()->back()->with('success', 'Contract rejected and customer notified.');
}

public function notifyCustomerContractSent(Contract $contract): void
{
    $customer = $contract->quotation->customer;

    // Check if customer wants email notifications
    if ($customer->notification_preferences['contract_updates'] ?? true) {
        Mail::send('emails.contract-sent', ['contract' => $contract, 'customer' => $customer],
            function ($message) use ($customer, $contract) {
                $message->to($customer->email)
                       ->subject("Contract Ready for Review - {$contract->contract_number}");
            });
    }

    // Always log the notification
    Log::info("Contract sent notification processed", [
        'customer_id' => $customer->id,
        'contract_id' => $contract->id,
        'email_sent' => $customer->notification_preferences['contract_updates'] ?? true
    ]);
}

// public function sendContractToCustomer(Request $request, Contract $contract)
// {
//     // Authorize action
//     if (!Gate::allows('sendToCustomer', $contract)) {
//         abort(403, 'You are not authorized to send this contract to customer.');
//     }

//     $contract->update([
//         'status' => Contract::STATUS_SENT_TO_CUSTOMER, // or Contract::STATUS_PENDING_APPROVAL
//         'sent_to_customer_at' => now(),
//     ]);

//     // Notify customer if service exists
//     if ($this->contractService) {
//         $this->contractService->notifyCustomerContractSent($contract);
//     }

//     return redirect()->route('admin.contracts.show', $contract)
//         ->with('success', 'Contract sent to customer successfully!');
// }

public function sendContractToCustomer(Request $request, Contract $contract)
{
    // Simple role-based authorization
    $allowedRoles = ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin'];

    if (!in_array(Auth::user()->role, $allowedRoles)) {
        abort(403, 'You are not authorized to send this contract to customer.');
    }

    $contract->update([
        'status' => 'sent_to_customer',
        'sent_to_customer_at' => now(),
    ]);

    // Notify customer if service exists
    if ($this->contractService) {
        $this->contractService->notifyCustomerContractSent($contract);
    }

    return redirect()->route('admin.contracts.show', $contract)
        ->with('success', 'Contract sent to customer successfully!');
}
}
