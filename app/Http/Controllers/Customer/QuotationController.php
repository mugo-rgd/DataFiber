<?php
// app/Http/Controllers/Customer/QuotationController.php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(): View
    {
        $quotations = Quotation::with(['designRequest', 'accountManager'])
            ->whereHas('designRequest', function($query) {
                $query->where('customer_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('customer.quotations.index', compact('quotations'));
    }

    public function show(Quotation $quotation): View
    {

if (Gate::denies('view', $quotation)) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->load(['designRequest', 'accountManager']);

        return view('customer.quotations.show', compact('quotation'));
    }

    public function approve(Request $request, Quotation $quotation): RedirectResponse
    {

         if (Gate::denies('customerApprove', $quotation)) {
            abort(403, 'Unauthorized action.');
        }

        if (!$quotation->canBeApprovedByCustomer()) {
            return redirect()->back()->with('error', 'This quotation cannot be approved at this time.');
        }

        $quotation->approveByCustomer();

        return redirect()->route('customer.quotations.show', $quotation)
            ->with('success', 'Quotation approved successfully! Our team will begin work shortly.');
    }

    public function reject(Request $request, Quotation $quotation): RedirectResponse
    {
       if (Gate::denies('customerReject', $quotation)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000'
        ]);

        if (!$quotation->canBeRejectedByCustomer()) {
            return redirect()->back()->with('error', 'This quotation cannot be rejected at this time.');
        }

        $quotation->rejectByCustomer($request->rejection_reason);

        return redirect()->route('customer.quotations.show', $quotation)
            ->with('success', 'Quotation has been rejected. Our team will contact you to discuss alternatives.');
    }
public function customerIndex()
{
    $user = auth()->user();
    $quotations = Quotation::where('customer_id', $user->id)->latest()->get();

    return view('customer.quotations.index', [
        'quotations' => $quotations
    ]);
}
    // In Customer\QuotationController
public function downloadPdf(Quotation $quotation)
{
    if (Gate::denies('customerView', $quotation)) {
        abort(403, 'Unauthorized action.');
    }

    // Ensure customer can only download their own quotations
    if ($quotation->customer_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    // Check if quotation is in a downloadable state
    if (!in_array($quotation->status, ['sent', 'approved'])) {
        return redirect()->back()
            ->with('error', 'This quotation is not available for download.');
    }

    // TODO: Implement actual PDF generation
    // For now, return a placeholder response or implement basic PDF

    // Option 1: Return placeholder (remove this in production)
    // return response()->streamDownload(function () use ($quotation) {
    //     echo "QUOTATION PDF\n";
    //     echo "================\n";
    //     echo "Quotation #: {$quotation->quotation_number}\n";
    //     echo "Customer: {$quotation->customer->name}\n";
    //     echo "Amount: {$quotation->formatted_total_amount}\n";
    //     echo "Status: {$quotation->status}\n";
    //     echo "Valid Until: {$quotation->valid_until->format('M j, Y')}\n";
    //     echo "Generated on: " . now()->format('Y-m-d H:i:s') . "\n";
    //     echo "\nScope of Work:\n{$quotation->scope_of_work}\n";
    //     echo "\nTerms & Conditions:\n{$quotation->terms_and_conditions}\n";
    // }, "quotation-{$quotation->quotation_number}.txt");

    // Option 2: Implement with DomPDF (install first: composer require barryvdh/laravel-dompdf)

    $pdf = Pdf::loadView('customer.quotations.pdf', compact('quotation'));
    return $pdf->download("quotation-{$quotation->quotation_number}.pdf");

}
}
