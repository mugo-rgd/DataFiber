<?php
// app/Http/Controllers/Finance/PaymentController.php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ConsolidatedBilling;
use App\Models\CreditTransaction;
use App\Models\CustomerCredit;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display list of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['customer', 'billing', 'validator', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('customer_id')) {
            $query->where('user_id', $request->customer_id);
        }

        $payments = $query->paginate(20);

        // Get statistics
        $stats = [
            'pending' => Payment::where('status', 'pending')->count(),
            'validated' => Payment::where('status', 'validated')->count(),
            'rejected' => Payment::where('status', 'rejected')->count(),
            'total_amount' => Payment::where('status', 'validated')->sum('amount_kes'),
        ];

        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $paymentMethods = ['Bank Transfer', 'Cheque', 'Cash', 'M-Pesa', 'RTGS', 'EFT', 'Mobile Money'];

       // For the customer payments view (if needed)
    $followups = $payments; // Or create a separate query for followups

    return view('finance.payments.index', compact('payments', 'stats', 'customers', 'paymentMethods', 'followups'));
    }

        /**
     * Show form to create a new payment
     */
    public function create(Request $request)
    {
       
        $customerId = $request->get('customer_id');
        $billingId = $request->get('billing_id');

        $customer = null;
        $billing = null;

        if ($customerId) {
            $customer = User::find($customerId);
        }

        if ($billingId) {
            $billing = ConsolidatedBilling::find($billingId);
            if ($billing) {
                $customer = $billing->user;
            }
        }

        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $paymentMethods = ['Bank Transfer', 'Cheque', 'Cash', 'M-Pesa', 'RTGS', 'EFT', 'Mobile Money'];

        return view('finance.payments.create', compact('customers', 'paymentMethods', 'customer', 'billing'));
    }

    /**
     * Store a new payment
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'amount' => 'required|numeric|min:0.01',
        'currency' => 'required|in:USD,KES',
        'payment_date' => 'required|date',
        'payment_method' => 'required|string|max:50',
        'reference_number' => 'nullable|string|max:100',
        'bank_name' => 'nullable|string|max:100',
        'bank_branch' => 'nullable|string|max:100',
        'deposit_slip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'billing_id' => 'nullable|exists:consolidated_billings,id',
        'notes' => 'nullable|string',
        'excess_distribution' => 'nullable|json',
        'allocated_invoices' => 'nullable|json',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        DB::beginTransaction();

        // Generate payment number
        $paymentNumber = $this->generatePaymentNumber();

        // Store deposit slip if uploaded
        $depositSlipPath = null;
        if ($request->hasFile('deposit_slip')) {
            $depositSlipPath = $request->file('deposit_slip')
                ->store('payments/deposit_slips', 'public');
        }

        // Handle currencies separately - NO conversion
        // Store the amount in its original currency only
        $amount = $request->amount;
        $currency = $request->currency;

        // amount_kes is only populated if payment is in KES, otherwise null
        $amountKes = ($request->currency === 'KES') ? $request->amount : null;
        // amount_usd is only populated if payment is in USD, otherwise null
        $amountUsd = ($request->currency === 'USD') ? $request->amount : null;

        // Create payment record
        $payment = Payment::create([
            'payment_number' => $paymentNumber,
            'user_id' => $request->user_id,
            'billing_id' => $request->billing_id,
            'amount' => $amount,
            'currency' => $currency,
            'amount_kes' => $amountKes,
            'amount_usd' => $amountUsd, // Make sure this column exists in your payments table
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'bank_name' => $request->bank_name,
            'bank_branch' => $request->bank_branch,
            'deposit_slip_path' => $depositSlipPath,
            'status' => 'pending',
            'created_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        // Process invoice allocations (excess distribution)
        $allocations = [];
        $excessData = null;

        if ($request->has('allocated_invoices') && $request->allocated_invoices) {
            $allocations = json_decode($request->allocated_invoices, true);
        }

        if ($request->has('excess_distribution') && $request->excess_distribution) {
            $excessData = json_decode($request->excess_distribution, true);
        }

        // Create payment allocation records
        if (!empty($allocations) && is_array($allocations)) {
            foreach ($allocations as $allocation) {
                if (isset($allocation['invoice_id']) && isset($allocation['allocated_amount']) && $allocation['allocated_amount'] > 0) {
                    PaymentAllocation::create([
                        'payment_id' => $payment->id,
                        'invoice_id' => $allocation['invoice_id'],
                        'allocated_amount' => $allocation['allocated_amount'],
                        'currency' => $currency,
                        'created_at' => now(),
                    ]);

                    // Update the invoice paid amount
                    $this->updateInvoicePaidAmount($allocation['invoice_id'], $allocation['allocated_amount'], $currency);
                }
            }
        }

        // Store excess as customer credit if any
        if ($excessData && isset($excessData['excess']) && $excessData['excess'] > 0) {
            $this->createCustomerCredit([
                'user_id' => $request->user_id,
                'amount' => $excessData['excess'],
                'currency' => $currency,
                'payment_id' => $payment->id,
                'notes' => 'Excess payment from ' . $paymentNumber,
            ]);
        }

        DB::commit();

        return redirect()->route('finance.payments.show', $payment)
            ->with('success', 'Payment recorded successfully. Awaiting validation.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to create payment: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->except('_token')
        ]);
        return redirect()->back()->with('error', 'Failed to record payment: ' . $e->getMessage())->withInput();
    }
}

/**
 * Update invoice paid amount
 */
private function updateInvoicePaidAmount($invoiceId, $amount, $currency)
{
    $invoice = ConsolidatedBilling::findOrFail($invoiceId);

    // Only update if currencies match
    if ($invoice->currency !== $currency) {
        Log::warning('Currency mismatch for invoice allocation', [
            'invoice_id' => $invoiceId,
            'invoice_currency' => $invoice->currency,
            'payment_currency' => $currency
        ]);
        return;
    }

    $currentPaid = $invoice->paid_amount ?? 0;
    $newPaid = $currentPaid + $amount;

    $invoice->update([
        'paid_amount' => $newPaid,
        'status' => $newPaid >= $invoice->total_amount ? 'paid' : 'partial',
        'paid_date' => $newPaid >= $invoice->total_amount ? now() : $invoice->paid_date,
    ]);
}

/**
 * Create customer credit for excess payment
 */
private function createCustomerCredit(array $data)
{
    // Create or update customer credit balance
    $credit = CustomerCredit::updateOrCreate(
        [
            'user_id' => $data['user_id'],
            'currency' => $data['currency'],
            'status' => 'active',
        ],
        [
            'amount' => DB::raw('amount + ' . $data['amount']),
        ]
    );

    // Record credit transaction
    CreditTransaction::create([
        'user_id' => $data['user_id'],
        'credit_id' => $credit->id,
        'payment_id' => $data['payment_id'],
        'amount' => $data['amount'],
        'currency' => $data['currency'],
        'type' => 'deposit',
        'notes' => $data['notes'] ?? 'Excess payment credit',
        'created_at' => now(),
    ]);

    return $credit;
}

/**
 * Generate unique payment number
 */
private function generatePaymentNumber()
{
    $prefix = 'PAY';
    $year = date('Y');
    $month = date('m');

    $lastPayment = Payment::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->orderBy('id', 'desc')
        ->first();

    if ($lastPayment) {
        $lastNumber = intval(substr($lastPayment->payment_number, -4));
        $sequence = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $sequence = '0001';
    }

    return $prefix . $year . $month . $sequence;
}

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        $payment->load(['customer', 'billing', 'validator', 'creator', 'transaction']);

        return view('finance.payments.show', compact('payment'));
    }

    /**
     * Validate payment (mark as validated)
     */
    public function validatePayment(Request $request, Payment $payment)
{
    if ($payment->status !== 'pending') {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'This payment has already been processed.'
            ], 422);
        }
        return redirect()->back()->with('error', 'This payment has already been processed.');
    }

    $validator = Validator::make($request->all(), [
        'validation_notes' => 'nullable|string|max:500',
        'apply_to_billing' => 'boolean',
    ]);

    if ($validator->fails()) {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        return redirect()->back()->withErrors($validator);
    }

    try {
        DB::beginTransaction();

        // Update payment
        $payment->update([
            'status' => 'validated',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'validation_notes' => $request->validation_notes,
        ]);

        // Create transaction record
        $transaction = Transaction::create([
            'user_id' => $payment->user_id,
            'transaction_number' => $this->generateTransactionNumber(),
            'transaction_date' => $payment->payment_date,
            'type' => 'payment',
            'description' => "Offline payment - {$payment->payment_method} - Ref: {$payment->reference_number}",
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'direction' => 'in',
            'balance' => $this->calculateNewBalance($payment->user_id, $payment->amount),
            'reference' => $payment->payment_number,
            'status' => 'completed',
            'created_by' => Auth::id(),
            'completed_at' => now(),
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'notes' => $payment->validation_notes,
        ]);

        // Link transaction to payment
        $payment->update(['transaction_id' => $transaction->id]);

        // If linked to billing, update billing paid amount
        if ($payment->billing_id && $request->apply_to_billing) {
            $billing = $payment->billing;
            $newPaidAmount = ($billing->paid_amount ?? 0) + $payment->amount;
            $newPaidAmountKes = ($billing->paid_amount_kes ?? 0) + ($payment->amount_kes ?? $payment->amount);

            $billing->update([
                'paid_amount' => $newPaidAmount,
                // 'paid_amount_kes' => $newPaidAmountKes,
                'status' => $newPaidAmount >= $billing->total_amount ? 'paid' : 'partial',
                'payment_date' => now(),
            ]);
        }

        DB::commit();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment validated successfully',
                'redirect' => route('finance.payments.show', $payment)
            ]);
        }

        return redirect()->route('finance.payments.show', $payment)
            ->with('success', 'Payment validated successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Failed to validate payment: ' . $e->getMessage());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate payment: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', 'Failed to validate payment: ' . $e->getMessage());
    }
}

    /**
     * Reject payment
     */
    public function rejectPayment(Request $request, Payment $payment)
{
    if ($payment->status !== 'pending') {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'This payment has already been processed.'
            ], 422);
        }
        return redirect()->back()->with('error', 'This payment has already been processed.');
    }

    $validator = Validator::make($request->all(), [
        'rejection_reason' => 'required|string|max:500',
    ]);

    if ($validator->fails()) {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        return redirect()->back()->withErrors($validator);
    }

    try {
        DB::beginTransaction();

        $payment->update([
            'status' => 'rejected',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'validation_notes' => $request->rejection_reason,
        ]);

        DB::commit();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully',
                'redirect' => route('finance.payments.show', $payment)
            ]);
        }

        return redirect()->route('finance.payments.show', $payment)
            ->with('success', 'Payment rejected successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Failed to reject payment: ' . $e->getMessage());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject payment: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', 'Failed to reject payment: ' . $e->getMessage());
    }
}

    /**
     * Download deposit slip
     */
    public function downloadSlip(Payment $payment)
    {
        if (!$payment->deposit_slip_path) {
            return redirect()->back()->with('error', 'No deposit slip available.');
        }

        if (!Storage::disk('public')->exists($payment->deposit_slip_path)) {
            return redirect()->back()->with('error', 'Deposit slip file not found.');
        }

        return Storage::disk('public')->download($payment->deposit_slip_path);
    }

    /**
     * Get customer invoices for dropdown
     */
    public function getCustomerInvoices($customerId)
    {
        $invoices = ConsolidatedBilling::where('user_id', $customerId)
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get(['id', 'billing_number', 'total_amount', 'paid_amount', 'due_date', 'currency']);

        return response()->json($invoices);
    }

        /**
     * Generate unique transaction number
     */
    private function generateTransactionNumber(): string
    {
        return 'TXN-PAY-' . date('YmdHis') . '-' . mt_rand(100, 999);
    }

    /**
     * Calculate new balance for user
     */
    private function calculateNewBalance($userId, $amount): float
    {
        $lastTransaction = Transaction::where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $currentBalance = $lastTransaction ? (float) $lastTransaction->balance : 0;

        return $currentBalance + $amount;
    }

    /**
     * Fetch exchange rate
     */
    private function fetchExchangeRate(): ?float
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get('https://api.frankfurter.app/latest?from=USD&to=KES');

            if ($response->successful()) {
                $data = $response->json();
                return $data['rates']['KES'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch exchange rate: ' . $e->getMessage());
        }

        return 130; // Default fallback rate
    }

     /**
     * Display follow-ups page for pending validations
     */
    public function followups()
    {
        $pendingPayments = Payment::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $recentValidated = Payment::where('status', '!=', 'pending')
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('finance.payments.followups', compact('pendingPayments', 'recentValidated'));
    }


}
