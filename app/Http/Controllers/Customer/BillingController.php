<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ConsolidatedBilling;
use App\Models\BillingLineItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\TevinDeviceService;
use App\Jobs\ProcessTevinInvoice;

class BillingController extends Controller
{
    protected $tevinService;

    public function __construct(TevinDeviceService $tevinService)
    {
        $this->tevinService = $tevinService;
    }

    /**
     * Submit invoice to TEVIN device
     */
    public function submitToKRA($billingId)
    {
        $billing = ConsolidatedBilling::findOrFail($billingId);

        try {
            // Option 1: Direct synchronous call
            $result = $this->tevinService->submitInvoice($billing);

            return response()->json([
                'success' => true,
                'control_code' => $result['control_code'],
                'qr_code_url' => $result['qr_code']
            ]);

        } catch (\App\Services\TevinApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'context' => $e->getContext()
            ], 400);
        }
    }

    /**
     * Better: Queue the submission for async processing
     */
    public function queueForSubmission($billingId)
    {
        $billing = ConsolidatedBilling::findOrFail($billingId);

        // Dispatch to queue
        ProcessTevinInvoice::dispatch($billing);

        return response()->json([
            'success' => true,
            'message' => 'Invoice queued for KRA submission'
        ]);
    }

    /**
     * Check device status
     */
    public function deviceStatus()
    {
        try {
            $status = $this->tevinService->getDeviceStatus();
            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display a listing of the consolidated billings.
     */
    /**
 * Display a listing of the consolidated billings.
 */
/**
 * Display a listing of the customer's billings.
 */
public function index()
{
    $user = Auth::user();


    // Get all billings for the logged-in customer
    $billings = ConsolidatedBilling::where('user_id', $user->id)
        ->with(['lineItems.lease'])
        ->orderBy('billing_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    // Calculate statistics for the customer
    $totalBillings = ConsolidatedBilling::where('user_id', $user->id)->count();

    $pendingBillings = ConsolidatedBilling::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'sent'])
        ->count();

    $overdueBillings = ConsolidatedBilling::where('user_id', $user->id)
        ->where('status', 'pending')
        ->where('due_date', '<', now())
        ->count();

    $totalAmountDue = ConsolidatedBilling::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'sent'])
        ->sum('total_amount');

    return view('customer.billings.index', compact(
        'billings',
        'totalBillings',
        'pendingBillings',
        'overdueBillings',
        'totalAmountDue'
    ));
}

/**
 * Check if billing is overdue
 *//**
 * Check if billing is overdue
 */
public static function isOverdue($billing): bool
{
    return $billing->status === 'pending' && $billing->due_date < now();
}

    /**
     * Display the specified consolidated billing.
     */
    public function show($id)
    {
        $user = Auth::user();

        $billing = ConsolidatedBilling::where('user_id', $user->id)
            ->with(['lineItems.lease', 'user'])
            ->findOrFail($id);

        return view('customer.billings.show', compact('billing'));
    }

    /**
     * Process payment for a billing.
     */
    public function pay(Request $request, $id)
    {
        $user = Auth::user();

        $billing = ConsolidatedBilling::where('user_id', $user->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'payment_method' => 'required|in:mpesa,bank_transfer,credit_card,debit_card',
        ]);

        // Here you would integrate with your payment gateway
        // For now, we'll simulate a successful payment

        try {
            // Update billing status to paid
            $billing->update([
                'status' => 'paid',
                'metadata' => array_merge($billing->metadata ?? [], [
                    'payment_method' => $request->payment_method,
                    'paid_at' => now()->toIso8601String(),
                    'transaction_id' => 'TRX-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                ]),
            ]);

            // Update all related line items status if needed
            // (Optional, depends on your business logic)

            return redirect()->route('customer.billings.show', $billing->id)
                ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Download billing invoice as PDF.
     */
    public function download($id)
    {
        $user = Auth::user();

        $billing = ConsolidatedBilling::where('user_id', $user->id)
            ->with(['lineItems.lease', 'user'])
            ->findOrFail($id);

        // Generate PDF
        $pdf = Pdf::loadView('customer.billings.pdf', compact('billing'));

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('defaultFont', 'Helvetica');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        // Download PDF with custom filename
        $filename = "invoice-{$billing->billing_number}-" . now()->format('Y-m-d') . ".pdf";

        return $pdf->download($filename);
    }
    /**
     * Stream PDF for preview in browser.
     */
    public function preview($id)
    {
        $user = Auth::user();

        $billing = ConsolidatedBilling::where('user_id', $user->id)
            ->with(['lineItems.lease', 'user'])
            ->findOrFail($id);

        // Generate PDF
        $pdf = Pdf::loadView('customer.billings.pdf', compact('billing'));

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('defaultFont', 'Helvetica');

        // Stream PDF in browser
        return $pdf->stream("invoice-{$billing->billing_number}.pdf");
    }

    /**
     * Calculate billing statistics for a user.
     */
    private function calculateBillingStats($userId)
    {
        $today = Carbon::today();

        // Get all billings for this user
        $allBillings = ConsolidatedBilling::where('user_id', $userId)->get();

        // Calculate stats
        $stats = [
            'total' => $allBillings->count(),
            'paid' => $allBillings->where('status', 'paid')->count(),
            'pending' => $allBillings->where('status', 'pending')->count(),
            'overdue' => $allBillings->filter(function ($billing) use ($today) {
                return $billing->status === 'pending' &&
                       $billing->due_date &&
                       $billing->due_date < $today;
            })->count(),
            'total_amount' => $allBillings->sum('total_amount'),
            'paid_amount' => $allBillings->where('status', 'paid')->sum('total_amount'),
            'pending_amount' => $allBillings->where('status', 'pending')->sum('total_amount'),
        ];

        return $stats;
    }

    /**
     * Get billing history for a specific lease.
     */
    public function leaseHistory($leaseId)
    {
        $user = Auth::user();

        // Verify the lease belongs to this user
        $lease = \App\Models\Lease::where('customer_id', $user->id)
            ->findOrFail($leaseId);

        // Get all line items for this lease
        $lineItems = BillingLineItem::whereHas('lease', function ($query) use ($leaseId) {
                $query->where('id', $leaseId);
            })
            ->with(['consolidatedBilling'])
            ->orderBy('period_start', 'desc')
            ->paginate(10);

        return view('customer.billings.lease-history', compact('lease', 'lineItems'));
    }


/**
 * Export billings to PDF.
 */
public function export(Request $request)
{
    $user = Auth::user();

    $query = ConsolidatedBilling::where('user_id', $user->id);

    if ($request->filled('date_from')) {
        $query->whereDate('billing_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('billing_date', '<=', $request->date_to);
    }

    $billings = $query->orderBy('billing_date', 'desc')->get();

    $pdf = Pdf::loadView('customer.billings.export_pdf', compact('billings'));
    $pdf->setPaper('A4', 'landscape');

    return $pdf->download('billings-export.pdf');
}
}
