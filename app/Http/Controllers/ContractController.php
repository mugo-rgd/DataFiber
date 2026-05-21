<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Lease;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List Contracts
    |--------------------------------------------------------------------------
    */
public function index(Request $request)
{
    $user = Auth::user();

    $contracts = Contract::with([
        'quotation.customer',
        'customer',
        'accountManager',
        'approver',
        'lease',
    ])
    ->when($user->role === 'account_manager', function ($query) use ($user) {
        $query->where('account_manager_id', $user->id);
    })
    ->when($request->filled('status'), function ($query) use ($request) {
        $query->where('status', $request->status);
    })
    ->latest()
    ->paginate(15);

    return view('contracts.index', compact('contracts'));
}

        /*
    |--------------------------------------------------------------------------
    | Final Admin Approval
    |--------------------------------------------------------------------------
    */

   public function approve(Request $request, Contract $contract)
{
    if (!$contract->canBeApprovedByAdmin()) {
        return redirect()
            ->route('contracts.show', $contract)
            ->with('error', 'Customer approval is required before final admin approval.');
    }

    $validated = $request->validate([
        'approval_notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::transaction(function () use ($contract, $validated) {

            $contract->update([
                'status' => Contract::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'admin_approved_at' => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            $contract->load([
                'quotation.customer.companyProfile',
                'customer',
                'accountManager',
                'approver',
            ]);

            $pdf = Pdf::loadView('contracts.pdf.dark-fibre-sla', [
                'contract' => $contract,
            ])->setPaper('a4', 'portrait');

            $fileName = 'contracts/' . $contract->contract_number . '.pdf';

            Storage::disk('public')->put($fileName, $pdf->output());

            $contract->update([
                'pdf_path' => $fileName,
                'pdf_generated_at' => now(),
            ]);
        });

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Contract approved and PDF generated successfully.');

    } catch (\Throwable $e) {
        Log::error('Contract approval/PDF generation failed', [
            'contract_id' => $contract->id,
            'error' => $e->getMessage(),
        ]);

        return redirect()
            ->route('contracts.show', $contract)
            ->with('error', 'Contract approval failed. Please try again.');
    }
}

public function activate(Contract $contract)
{
    if (!$contract->canBeActivated()) {
        return redirect()
            ->route('contracts.show', $contract)
            ->with('error', 'Only approved contracts can be activated.');
    }

    try {
        DB::transaction(function () use ($contract) {

            $contract->load([
                'quotation',
                'quotation.customer',
                'quotation.designRequest',
            ]);

            $quotation = $contract->quotation;

            $contract->update([
                'status' => Contract::STATUS_ACTIVE,
                'design_completed_at' => now(),
            ]);

            $lease = Lease::firstOrCreate(
                [
                    'quotation_id' => $quotation->id,
                ],
                [
                    'lease_number' => 'LSE-' . now()->format('Ym') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'title' => $quotation->title ?? $quotation->designRequest->title ?? 'Dark Fibre Lease',
                    'customer_id' => $quotation->customer_id,
                    'account_manager_id' => $contract->account_manager_id,
                    'quotation_id' => $quotation->id,
                    'design_request_id' => $quotation->design_request_id,
                    'service_type' => 'dark_fibre',
                    'status' => 'active',
                    'currency' => $quotation->currency ?? 'USD',
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addMonths($quotation->contract_term_months ?? 12)->toDateString(),
                    'contract_term_months' => $quotation->contract_term_months ?? 12,
                    'billing_cycle' => 'quarterly',
                    'activated_at' => now(),
                    'monthly_cost' => $quotation->monthly_amount ?? 0,
                    'installation_fee' => $quotation->installation_fee ?? 0,
                    'total_contract_value' => $quotation->total_amount ?? 0,
                    'start_location' => $quotation->start_location,
                    'end_location' => $quotation->end_location,
                    'distance_km' => $quotation->distance_km,
                    'cores_required' => $quotation->cores_required,
                    'technology' => $quotation->technology,
                    'service_level_agreement' => $contract->contract_content,
                    'technical_specifications' => $quotation->technical_specifications,
                    'terms_and_conditions' => $quotation->terms_and_conditions,
                    'notes' => 'Generated automatically from Contract #' . $contract->contract_number,
                ]
            );

            $lease->load([
                'customer.companyProfile',
            ]);

            $pdf = Pdf::loadView('leases.pdf.lease-agreement', [
                'lease' => $lease,
            ])->setPaper('a4', 'portrait');

            $fileName = 'leases/' . $lease->lease_number . '.pdf';

            Storage::disk('public')->put($fileName, $pdf->output());

            $lease->update([
                'pdf_path' => $fileName,
                'pdf_generated_at' => now(),
            ]);
        });

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Contract activated, lease created, and lease PDF generated successfully.');

    } catch (\Throwable $e) {
        Log::error('Contract activation failed', [
            'contract_id' => $contract->id,
            'error' => $e->getMessage(),
        ]);

        return redirect()
            ->route('contracts.show', $contract)
            ->with('error', 'Activation failed: ' . $e->getMessage());
    }
}

    /*
    |--------------------------------------------------------------------------
    | Delete Draft
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Contract $contract
    )
    {
        if(
            $contract->status
            !='draft'
        ){

            return back()
                ->with(
                    'error',
                    'Only drafts removable'
                );
        }

        $contract->delete();

        return back()
            ->with(
                'success',
                'Deleted'
            );
    }

    public function createFromQuotation(Quotation $quotation)
{
    if ($quotation->status !== 'approved') {
        return back()->with(
            'error',
            'Quotation must be fully approved before creating a contract.'
        );
    }

    $existingContract = Contract::where(
        'quotation_id',
        $quotation->id
    )->first();

    if ($existingContract) {
        return redirect()
            ->route(
                'contracts.show',
                $existingContract
            )
            ->with(
                'info',
                'A contract already exists for this quotation.'
            );
    }

    try {

        DB::beginTransaction();

        /*
        STEP 1:
        Create draft contract first
        */

        $contract = Contract::create([

            'quotation_id' => $quotation->id,

            'customer_id' => $quotation->customer_id,

            'account_manager_id' => Auth::id(),

            'contract_content' => 'Generating contract...',

            'status' => Contract::STATUS_DRAFT,

            'customer_approval_status' => 'pending'
        ]);

        /*
        STEP 2:
        Render your SLA blade template
        */

        $contract->update([

            'contract_content' => view(
                'contracts.templates.dark-fibre-sla',
                compact('contract')
            )->render()

        ]);

        DB::commit();

        return redirect()
            ->route(
                'contracts.edit',
                $contract
            )
            ->with(
                'success',
                'Contract draft created successfully.'
            );

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error(
            'Contract creation failed',
            [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]
        );

        return back()->with(
            'error',
            'Failed to create contract draft.'
        );
    }
}

private function buildContractContentFromQuotation(Quotation $quotation): string
{
    return trim("
CONTRACT AGREEMENT

Quotation Number: {$quotation->quotation_number}

Scope of Work:
{$quotation->scope_of_work}

Terms and Conditions:
{$quotation->terms_and_conditions}

Contract Value:
USD " . number_format($quotation->total_amount, 2) . "

This contract is generated from the approved quotation and is subject to customer and final administrative approval.
    ");
}

public function show(Contract $contract)
{
    $contract->load([
        'quotation.customer',
        'customer',
        'accountManager',
        'approver',
        'lease',
    ]);

    return view('contracts.show', compact('contract'));
}

public function edit(Contract $contract)
{
    if ($contract->status !== Contract::STATUS_DRAFT) {
        return redirect()
            ->route('contracts.show', $contract)
            ->with('error', 'Only draft contracts can be edited.');
    }

    $contract->load([
        'quotation.customer',
        'customer',
        'accountManager',
    ]);

    return view('contracts.edit', compact('contract'));
}

public function update(Request $request, Contract $contract)
{
    if ($contract->status !== Contract::STATUS_DRAFT) {
        return redirect()
            ->route('contracts.show', $contract)
            ->with('error', 'Only draft contracts can be updated.');
    }

    $validated = $request->validate([
        'contract_notes' => 'nullable|string|max:5000',
    ]);

    $contract->update([
        'contract_notes' => $validated['contract_notes'] ?? null,
    ]);

    return redirect()
        ->route('contracts.show', $contract)
        ->with('success', 'Contract draft updated successfully.');
}

public function send(Contract $contract)
{
    if ($contract->status !== Contract::STATUS_DRAFT) {
        return redirect()
            ->route('contracts.show', $contract)
            ->with('error', 'Only draft contracts can be sent to the customer.');
    }

    $contract->update([
        'status' => Contract::STATUS_SENT,
        'sent_at' => now(),
        'sent_to_customer_at' => now(),
        'customer_approval_status' => 'pending',
    ]);

    return redirect()
        ->route('contracts.show', $contract)
        ->with('success', 'Contract sent to customer successfully.');
}

public function customerApprove(Contract $contract)
{
    if (Auth::id() !== (int) $contract->customer_id) {
        abort(403, 'Unauthorized action.');
    }

    if (!$contract->canBeApprovedByCustomer()) {
        return redirect()
            ->route('customer.contracts.show', $contract)
            ->with('error', 'This contract cannot be approved at this stage.');
    }

    $contract->update([
        'status' => Contract::STATUS_CUSTOMER_APPROVED,
        'customer_approval_status' => 'approved',
        'customer_approved_at' => now(),
    ]);

    return redirect()
        ->route('customer.contracts.show', $contract)
        ->with('success', 'Contract accepted successfully. It is now awaiting final admin approval.');
}

public function customerReject(Request $request, Contract $contract)
{
    if (Auth::id() !== (int) $contract->customer_id) {
        abort(403, 'Unauthorized action.');
    }

    if (!$contract->canBeRejectedByCustomer()) {
        return redirect()
            ->route('customer.contracts.show', $contract)
            ->with('error', 'This contract cannot be rejected at this stage.');
    }

    $validated = $request->validate([
        'rejection_reason' => 'required|string|min:5|max:1000',
    ]);

    $contract->update([
        'status' => Contract::STATUS_CUSTOMER_REJECTED,
        'customer_approval_status' => 'rejected',
        'customer_rejected_at' => now(),
        'rejection_reason' => $validated['rejection_reason'],
    ]);

    return redirect()
        ->route('customer.contracts.show', $contract)
        ->with('success', 'Contract rejected successfully.');
}

public function customerIndex()
{
    $contracts = Contract::with([
        'quotation',
        'accountManager',
        'lease',
    ])
        ->where('customer_id', Auth::id())
        ->latest()
        ->paginate(10);

    return view('customer.contracts.index', compact('contracts'));
}

public function customerShow(Contract $contract)
{
    if ((int) $contract->customer_id !== (int) Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $contract->load([
        'quotation',
        'accountManager',
        'lease',
    ]);

    return view('customer.contracts.show', compact('contract'));
}
}
