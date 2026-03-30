<?php

namespace App\Http\Controllers;

use App\Models\BillingLineItem;
use App\Models\ConsolidatedBilling;
use App\Models\LeaseBilling;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Lease;
use App\Services\AutomatedBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BillingReminder;

class FinanceController extends Controller
{
    /**
     * Display finance dashboard with comprehensive analytics
     */
    public function dashboard()
    {
        $user = Auth::user();
        $financialMetrics = $this->getFinancialMetrics();
        $revenueTrends = $this->getRevenueTrends();

        // Load recent transactions with currency
        $recentTransactions = Transaction::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($transaction) {
                $transaction->formatted_amount = $this->formatCurrency(
                    $transaction->amount,
                    $transaction->currency ?? 'KSH'
                );
                $transaction->formatted_balance = $this->formatCurrency(
                    $transaction->balance ?? 0,
                    $transaction->currency ?? 'KSH'
                );
                return $transaction;
            });

        $topCustomers = $this->getTopCustomers();

        return view('finance.dashboard', compact(
            'financialMetrics',
            'recentTransactions',
            'topCustomers',
            'revenueTrends'
        ));
    }

    /**
     * Display lease billings with advanced filtering
     */
    public function billing(Request $request)
    {
        $query = LeaseBilling::with(['user', 'lease', 'customer']);

        // Apply filters
        $this->applyBillingFilters($query, $request);

        $billings = $query->latest()->paginate(20);
        $customers = User::where('role', 'customer')->get();
        $billingCycles = ['monthly', 'quarterly', 'annually', 'one_time'];

        return view('finance.billing.index', compact('billings', 'customers', 'billingCycles'));
    }

    /**
     * Display payments with comprehensive reporting
     */
    public function payments(Request $request)
    {
        $query = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select('payments.*', 'users.name as customer_name', 'users.email as customer_email');

        $this->applyPaymentFilters($query, $request);

        $payments = $query->orderBy('payments.created_at', 'desc')->paginate(15);
        $paymentStats = $this->getPaymentStats();

        return view('finance.payments.index', compact('payments', 'paymentStats'));
    }

    /**
     * Display transactions with advanced filtering
     */
    public function transactions(Request $request)
    {
        try {
            $query = Transaction::with(['user', 'createdBy']);

            // Apply filters
            $this->applyTransactionFilters($query, $request);

            $transactions = $query->orderBy('transaction_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->appends($request->query());

            $transactionStats = $this->getTransactionStats();

            return view('finance.transactions.index', compact('transactions', 'transactionStats'));

        } catch (\Exception $e) {
            Log::error('Finance transactions error: ' . $e->getMessage());
            return back()->with('error', 'Error loading transactions. Please check your filters.');
        }
    }

    /**
     * Show the form for creating a new transaction
     */
    public function createTransaction()
    {
        $customers = User::where('role', 'customer')->get();
        $billings = LeaseBilling::whereIn('status', ['pending', 'overdue'])->get();
        $transactionTypes = $this->getTransactionTypes();
        $paymentMethods = $this->getPaymentMethods();
        $categories = $this->getTransactionCategories();
        $currencies = ['KSH' => 'KSH (Kenyan Shilling)', 'USD' => 'USD (US Dollar)'];

        return view('finance.transactions.create', compact(
            'customers',
            'billings',
            'transactionTypes',
            'paymentMethods',
            'categories',
            'currencies'
        ));
    }

    /**
     * Store a newly created transaction
     */
    public function storeTransaction(Request $request)
    {
        $validated = $this->validateTransactionRequest($request);

        if (empty($validated['reference_number'])) {
            $validated['reference_number'] = $this->generateTransactionId();
        }

        $validated['created_by'] = Auth::id();
        Transaction::create($validated);

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified transaction
     */
    public function showTransaction($id)
    {
        try {
            $transaction = Transaction::with(['user', 'createdBy'])
                ->findOrFail($id);

            return view('finance.transactions.show', compact('transaction'));

        } catch (\Exception $e) {
            Log::error('Error showing transaction: ' . $e->getMessage());
            return redirect()->route('finance.transactions.index')
                ->with('error', 'Transaction not found.');
        }
    }

    /**
     * Show the form for editing the specified transaction
     */
    public function editTransaction($id)
    {
        try {
            $transaction = Transaction::with(['user'])->findOrFail($id);
            $customers = User::where('role', 'customer')->get();
            $billings = LeaseBilling::whereIn('status', ['pending', 'paid'])->get();
            $transactionTypes = $this->getTransactionTypes();
            $paymentMethods = $this->getPaymentMethods();
            $categories = $this->getTransactionCategories();
            $currencies = ['KSH' => 'KSH (Kenyan Shilling)', 'USD' => 'USD (US Dollar)'];

            return view('finance.transactions.edit', compact(
                'transaction',
                'customers',
                'billings',
                'transactionTypes',
                'paymentMethods',
                'categories',
                'currencies'
            ));

        } catch (\Exception $e) {
            Log::error('Error editing transaction: ' . $e->getMessage());
            return redirect()->route('finance.transactions.index')
                ->with('error', 'Transaction not found.');
        }
    }

    /**
     * Update the specified transaction
     */
    public function updateTransaction(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $validated = $this->validateTransactionRequest($request);

        $transaction->update($validated);

        return redirect()->route('finance.transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction
     */
    public function destroyTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    /**
     * Complete a transaction
     */
    public function completeTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaction marked as completed.');
    }

    /**
     * Generate and display various financial reports
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('report_type', 'financial_summary');
        $period = $request->get('period', 'this_month');

        list($startDate, $endDate) = $this->getDateRange($period, $request->start_date, $request->end_date);

        try {
            $reportData = $this->generateReport($reportType, $startDate, $endDate);

            // Add additional data needed by the blade template
            $reportData['report_type'] = $reportType;
            $reportData['start_date'] = $startDate;
            $reportData['end_date'] = $endDate;

            if ($request->has('export')) {
                return $this->exportReport($reportType, $reportData, $startDate, $endDate);
            }

            return view('finance.reports.reports', compact(
                'reportData',
                'reportType',
                'period',
                'startDate',
                'endDate'
            ));

        } catch (\Exception $e) {
            Log::error('Finance reports error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to generate reports. Please try again.');
        }
    }

    // ==========================
    // BILLING MANAGEMENT
    // ==========================

    /**
     * Show the form for creating a new lease billing
     */
    public function createBilling()
    {
        $leases = Lease::with('customer')->where('status', 'active')->get();
        $customers = User::where('role', 'customer')->where('status', 'active')->get();
        $billingCycles = $this->getBillingCycles();
        $nextBillingNumber = $this->generateBillingNumber();

        return view('finance.billing.create', compact(
            'leases',
            'customers',
            'billingCycles',
            'nextBillingNumber'
        ));
    }

    /**
     * Store a newly created lease billing
     */
    public function storeBilling(Request $request)
    {
        $validated = $this->validateBillingRequest($request);

        if (empty($validated['billing_number'])) {
            $validated['billing_number'] = $this->generateBillingNumber();
        }

        $validated['user_id'] = Auth::id();
        $billing = LeaseBilling::create($validated);

        $this->processBillingLineItems($billing, $request->line_items ?? []);

        return redirect()->route('finance.billing.index')
            ->with('success', 'Lease billing created successfully.');
    }

    /**
     * Display a specific lease billing
     */
    public function showBilling($id)
    {
        $billing = LeaseBilling::with(['user', 'lease', 'customer', 'payments'])->findOrFail($id);
        $paymentHistory = $billing->payments()->orderBy('payment_date', 'desc')->get();

        return view('finance.billing.show', compact('billing', 'paymentHistory'));
    }

    /**
     * Show the form for editing the specified lease billing
     */
    public function editBilling($id)
    {
        $billing = LeaseBilling::with(['user', 'lease', 'customer'])->findOrFail($id);
        $leases = Lease::all();
        $customers = User::where('role', 'customer')->get();
        $billingCycles = $this->getBillingCycles();

        return view('finance.billing.edit', compact('billing', 'leases', 'customers', 'billingCycles'));
    }

    /**
     * Update the specified lease billing
     */
    public function updateBilling(Request $request, $id)
    {
        $billing = LeaseBilling::findOrFail($id);
        $validated = $this->validateBillingRequest($request, $id);

        $billing->update($validated);

        return redirect()->route('finance.billing.show', $billing)
            ->with('success', 'Lease billing updated successfully.');
    }

    /**
     * Remove the specified lease billing
     */
    public function deleteBilling($id)
    {
        $billing = LeaseBilling::findOrFail($id);

        if ($billing->payments()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete billing with associated payments. Please delete payments first.');
        }

        $billing->delete();

        return redirect()->route('finance.billing.index')
            ->with('success', 'Lease billing deleted successfully.');
    }

    /**
     * Download billing as PDF
     */
    public function downloadBilling($id)
    {
        $billing = LeaseBilling::with(['user', 'lease', 'customer'])->findOrFail($id);

        try {
            if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                throw new \Exception('PDF generation is not available. Please install dompdf package.');
            }

            $pdf = Pdf::loadView('finance.billing.pdf', compact('billing'));
            return $pdf->download('billing-' . $billing->billing_number . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print billing
     */
    public function printBilling($id)
    {
        $billing = LeaseBilling::with(['user', 'lease', 'customer'])->findOrFail($id);

        try {
            if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                throw new \Exception('PDF generation is not available. Please install dompdf package.');
            }

            $pdf = Pdf::loadView('finance.billing.pdf', compact('billing'));
            return $pdf->stream('billing-' . $billing->billing_number . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Send billing via email
     */
    public function sendBillingEmail($id)
    {
        $billing = LeaseBilling::with('customer')->findOrFail($id);

        try {
            // Implement email sending logic here
            // Mail::to($billing->customer->email)->send(new BillingEmail($billing));

            $billing->update(['sent_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Billing sent successfully to ' . $billing->customer->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send billing email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark billing as paid and record payment
     */
    public function markBillingPaid(Request $request, $id)
    {
        $billing = LeaseBilling::findOrFail($id);

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:credit_card,bank_transfer,cash,digital_wallet,check',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($billing, $validated) {
            $billing->update([
                'status' => 'paid',
                'paid_at' => $validated['payment_date']
            ]);

            Transaction::create([
                'type' => 'income',
                'amount' => $billing->total_amount,
                'currency' => $billing->currency ?? 'KSH',
                'description' => 'Payment for billing ' . $billing->billing_number,
                'transaction_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'category' => 'invoice_payment',
                'status' => 'completed',
                'user_id' => $billing->customer_id,
                'billing_id' => $billing->id,
                'reference_number' => $validated['reference_number'] ?? $this->generateTransactionId(),
                'notes' => $validated['notes'],
                'created_by' => Auth::id()
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Billing marked as paid and payment recorded.'
        ]);
    }

    /**
     * Send billing reminder
     */
    public function sendBillingReminder($id)
    {
        $billing = LeaseBilling::with('customer')->findOrFail($id);

        if (!$billing->customer || !$billing->customer->email) {
            return response()->json([
                'success' => false,
                'message' => 'Customer email not found.'
            ], 404);
        }

        try {
            $invoiceUrl = url('/finance/billing/' . $billing->id);

            Log::info('Sending billing reminder with invoice link', [
                'billing_id' => $billing->id,
                'billing_number' => $billing->billing_number,
                'customer_email' => $billing->customer->email,
                'customer_name' => $billing->customer->name,
                'amount' => $billing->total_amount,
                'currency' => $billing->currency,
                'due_date' => $billing->due_date,
                'invoice_link' => $invoiceUrl,
                'sent_by' => Auth::id(),
                'sent_at' => now()->toIso8601String()
            ]);

            Mail::to($billing->customer->email)->send(new BillingReminder($billing));

            if (count(Mail::failures()) > 0) {
                Log::error('Email failed for recipients:', Mail::failures());
                throw new \Exception('Email delivery failed for some recipients');
            }

            $billing->update([
                'last_reminder_sent_at' => now(),
                'reminder_count' => DB::raw('COALESCE(reminder_count, 0) + 1')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reminder sent successfully to ' . $billing->customer->email,
                'data' => [
                    'invoice_id' => $billing->id,
                    'billing_number' => $billing->billing_number,
                    'invoice_link' => $invoiceUrl,
                    'sent_at' => now()->toIso8601String()
                ]
            ]);

        } catch (\Swift_TransportException $e) {
            Log::error('SMTP Error:', [
                'error' => $e->getMessage(),
                'billing_id' => $billing->id,
                'customer_email' => $billing->customer->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Email blocked by spam filter.',
                'technical' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('General error sending reminder:', [
                'error' => $e->getMessage(),
                'billing_id' => $billing->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick status update method
     */
    public function updateBillingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,overdue,draft'
        ]);

        $billing = LeaseBilling::findOrFail($id);
        $oldStatus = $billing->status;
        $billing->update(['status' => $request->status]);

        return redirect()->route('finance.billing.index')
            ->with('success', "Billing {$billing->billing_number} status changed from {$oldStatus} to {$request->status}.");
    }

    /**
     * Bulk update billing statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'billings' => 'required|array',
            'billings.*' => 'exists:lease_billings,id',
            'status' => 'required|in:pending,paid,overdue,draft'
        ]);

        $billings = LeaseBilling::whereIn('id', $request->billings)->get();

        foreach ($billings as $billing) {
            $billing->update(['status' => $request->status]);
        }

        return redirect()->route('finance.billing.index')
            ->with('success', "Updated {$billings->count()} billings to {$request->status} status.");
    }

    // ==========================
    // AUTO BILLING & SETTINGS
    // ==========================

    /**
     * Generate invoices manually
     */
    public function generateInvoicesManually(AutomatedBillingService $billingService)
    {
        $result = $billingService->generateInvoices();

        if (empty($result['errors'])) {
            return redirect()->route('finance.auto-billing.generate')
                ->with('success', "Successfully generated {$result['generated']} invoices.");
        }

        return redirect()->route('finance.auto-billing.generate')
            ->with('warning', "Generated {$result['generated']} invoices with errors.")
            ->with('errors', $result['errors']);
    }

    /**
     * Update customer billing settings
     */
    public function updateBillingSettings(Request $request, $customerId)
    {
        $customer = User::where('role', 'customer')->findOrFail($customerId);

        $validated = $request->validate([
            'monthly_rate' => 'required|numeric|min:0',
            'billing_frequency' => 'required|in:monthly,quarterly,annually',
            'lease_start_date' => 'required|date',
            'auto_billing_enabled' => 'boolean',
            'payment_terms' => 'required|integer|min:1',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|in:KSH,USD'
        ]);

        $customer->update($validated);

        return redirect()->back()
            ->with('success', 'Billing settings updated successfully.');
    }

    /**
     * Display auto billing management page
     */
    public function autoBilling(AutomatedBillingService $billingService)
    {
        $stats = $this->getAutoBillingStats();
        $dueCustomers = $this->getDueCustomers();
        $autoBillingCustomers = $this->getAutoBillingCustomers();
        $scheduledBillings = $this->getScheduledBillings();

        return view('finance.auto-billing.index', compact(
            'stats',
            'dueCustomers',
            'autoBillingCustomers',
            'scheduledBillings'
        ));
    }

    // ==========================
    // PRIVATE HELPER METHODS
    // ==========================

    /**
     * Get financial metrics for dashboard with currency separation
     */
    private function getFinancialMetrics(): array
    {
        try {
            $monthStart = now()->startOfMonth();
            $monthEnd = now()->endOfMonth();
            $lastMonthStart = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->subMonth()->endOfMonth();

            // ============ KSH METRICS ============

            $totalRevenueKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->sum('paid_amount') ?: 0;

            $revenueThisMonthKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->whereBetween('billing_date', [$monthStart, $monthEnd])
                ->sum('paid_amount') ?: 0;

            $revenueLastMonthKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->whereBetween('billing_date', [$lastMonthStart, $lastMonthEnd])
                ->sum('paid_amount') ?: 0;

            $pendingAmountKsh = DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent'])
                ->where('currency', 'KSH')
                ->sum('total_amount') ?: 0;

            $pendingInvoicesKsh = DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent'])
                ->where('currency', 'KSH')
                ->count();

            $paidInvoicesKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->count();

            $overdueAmountKsh = DB::table('consolidated_billings')
                ->where(function($query) {
                    $query->where('status', 'overdue')
                        ->orWhere(function($q) {
                            $q->whereIn('status', ['pending', 'sent'])
                                ->where('due_date', '<', now());
                        });
                })
                ->where('currency', 'KSH')
                ->sum('total_amount') ?: 0;

            $overdueCountKsh = DB::table('consolidated_billings')
                ->where(function($query) {
                    $query->where('status', 'overdue')
                        ->orWhere(function($q) {
                            $q->whereIn('status', ['pending', 'sent'])
                                ->where('due_date', '<', now());
                        });
                })
                ->where('currency', 'KSH')
                ->count();

            // ============ USD METRICS ============

            $totalRevenueUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->sum('paid_amount') ?: 0;

            $revenueThisMonthUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->whereBetween('billing_date', [$monthStart, $monthEnd])
                ->sum('paid_amount') ?: 0;

            $revenueLastMonthUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->whereBetween('billing_date', [$lastMonthStart, $lastMonthEnd])
                ->sum('paid_amount') ?: 0;

            $pendingAmountUsd = DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent'])
                ->where('currency', 'USD')
                ->sum('total_amount') ?: 0;

            $pendingInvoicesUsd = DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent'])
                ->where('currency', 'USD')
                ->count();

            $paidInvoicesUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->count();

            $overdueAmountUsd = DB::table('consolidated_billings')
                ->where(function($query) {
                    $query->where('status', 'overdue')
                        ->orWhere(function($q) {
                            $q->whereIn('status', ['pending', 'sent'])
                                ->where('due_date', '<', now());
                        });
                })
                ->where('currency', 'USD')
                ->sum('total_amount') ?: 0;

            $overdueCountUsd = DB::table('consolidated_billings')
                ->where(function($query) {
                    $query->where('status', 'overdue')
                        ->orWhere(function($q) {
                            $q->whereIn('status', ['pending', 'sent'])
                                ->where('due_date', '<', now());
                        });
                })
                ->where('currency', 'USD')
                ->count();

            // ============ COMMON METRICS ============

            $activeCustomers = DB::table('users')
                ->where('role', 'customer')
                ->where('status', 'active')
                ->count();

            $totalCustomers = DB::table('users')
                ->where('role', 'customer')
                ->count();

            $totalBilled = DB::table('consolidated_billings')
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'draft')
                ->sum('total_amount') ?: 1;

            $totalCollected = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->sum('paid_amount') ?: 0;

            $collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;

            $kshTrend = $revenueLastMonthKsh > 0
                ? (($revenueThisMonthKsh - $revenueLastMonthKsh) / $revenueLastMonthKsh) * 100
                : 0;

            $usdTrend = $revenueLastMonthUsd > 0
                ? (($revenueThisMonthUsd - $revenueLastMonthUsd) / $revenueLastMonthUsd) * 100
                : 0;

        } catch (\Exception $e) {
            Log::error('Error getting financial metrics: ' . $e->getMessage());

            $totalRevenueKsh = 0;
            $totalRevenueUsd = 0;
            $revenueThisMonthKsh = 0;
            $revenueThisMonthUsd = 0;
            $revenueLastMonthKsh = 0;
            $revenueLastMonthUsd = 0;
            $pendingAmountKsh = 0;
            $pendingAmountUsd = 0;
            $pendingInvoicesKsh = 0;
            $pendingInvoicesUsd = 0;
            $paidInvoicesKsh = 0;
            $paidInvoicesUsd = 0;
            $overdueAmountKsh = 0;
            $overdueAmountUsd = 0;
            $overdueCountKsh = 0;
            $overdueCountUsd = 0;
            $activeCustomers = 0;
            $totalCustomers = 0;
            $collectionRate = 0;
            $kshTrend = 0;
            $usdTrend = 0;
        }

        return [
            'ksh' => [
                'total_revenue' => [
                    'value' => $totalRevenueKsh,
                    'formatted' => 'KSh ' . number_format($totalRevenueKsh, 2),
                    'title' => 'Total Revenue (KSH)',
                    'icon' => 'fa-coins',
                    'color' => 'success',
                    'trend' => round($kshTrend, 1)
                ],
                'revenue_this_month' => [
                    'value' => $revenueThisMonthKsh,
                    'formatted' => 'KSh ' . number_format($revenueThisMonthKsh, 2),
                    'title' => 'This Month (KSH)',
                    'icon' => 'fa-calendar',
                    'color' => 'info',
                    'trend' => round($kshTrend, 1)
                ],
                'pending_invoices' => [
                    'count' => $pendingInvoicesKsh,
                    'amount' => $pendingAmountKsh,
                    'formatted' => 'KSh ' . number_format($pendingAmountKsh, 2),
                    'title' => 'Pending (KSH)',
                    'icon' => 'fa-clock',
                    'color' => 'warning'
                ],
                'paid_invoices' => [
                    'count' => $paidInvoicesKsh,
                    'title' => 'Paid (KSH)',
                    'icon' => 'fa-check-circle',
                    'color' => 'success'
                ],
                'overdue' => [
                    'count' => $overdueCountKsh,
                    'amount' => $overdueAmountKsh,
                    'formatted' => 'KSh ' . number_format($overdueAmountKsh, 2),
                    'title' => 'Overdue (KSH)',
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'danger'
                ],
            ],
            'usd' => [
                'total_revenue' => [
                    'value' => $totalRevenueUsd,
                    'formatted' => '$' . number_format($totalRevenueUsd, 2),
                    'title' => 'Total Revenue (USD)',
                    'icon' => 'fa-dollar-sign',
                    'color' => 'success',
                    'trend' => round($usdTrend, 1)
                ],
                'revenue_this_month' => [
                    'value' => $revenueThisMonthUsd,
                    'formatted' => '$' . number_format($revenueThisMonthUsd, 2),
                    'title' => 'This Month (USD)',
                    'icon' => 'fa-calendar',
                    'color' => 'info',
                    'trend' => round($usdTrend, 1)
                ],
                'pending_invoices' => [
                    'count' => $pendingInvoicesUsd,
                    'amount' => $pendingAmountUsd,
                    'formatted' => '$' . number_format($pendingAmountUsd, 2),
                    'title' => 'Pending (USD)',
                    'icon' => 'fa-clock',
                    'color' => 'warning'
                ],
                'paid_invoices' => [
                    'count' => $paidInvoicesUsd,
                    'title' => 'Paid (USD)',
                    'icon' => 'fa-check-circle',
                    'color' => 'success'
                ],
                'overdue' => [
                    'count' => $overdueCountUsd,
                    'amount' => $overdueAmountUsd,
                    'formatted' => '$' . number_format($overdueAmountUsd, 2),
                    'title' => 'Overdue (USD)',
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'danger'
                ],
            ],
            'common' => [
                'collection_rate' => [
                    'value' => round($collectionRate, 2),
                    'formatted' => round($collectionRate, 2) . '%',
                    'title' => 'Collection Rate',
                    'icon' => 'fa-percent',
                    'color' => 'primary'
                ],
                'active_customers' => [
                    'value' => $activeCustomers,
                    'title' => 'Active Customers',
                    'icon' => 'fa-users',
                    'color' => 'info'
                ],
                'total_customers' => [
                    'value' => $totalCustomers,
                    'title' => 'Total Customers',
                    'icon' => 'fa-user',
                    'color' => 'secondary'
                ],
            ],
        ];
    }

    /**
     * Get revenue trends for charts with currency separation
     */
    private function getRevenueTrends(): array
    {
        $months = [];
        $revenuesKsh = [];
        $revenuesUsd = [];

        try {
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('M Y');
                $months[] = $monthName;

                $revenueKsh = DB::table('consolidated_billings')
                    ->where('status', 'paid')
                    ->where('currency', 'KSH')
                    ->whereYear('billing_date', $date->year)
                    ->whereMonth('billing_date', $date->month)
                    ->sum('paid_amount') ?: 0;

                $revenueUsd = DB::table('consolidated_billings')
                    ->where('status', 'paid')
                    ->where('currency', 'USD')
                    ->whereYear('billing_date', $date->year)
                    ->whereMonth('billing_date', $date->month)
                    ->sum('paid_amount') ?: 0;

                $revenuesKsh[] = $revenueKsh;
                $revenuesUsd[] = $revenueUsd;
            }
        } catch (\Exception $e) {
            Log::error('Error getting revenue trends: ' . $e->getMessage());
            $revenuesKsh = array_fill(0, 6, 0);
            $revenuesUsd = array_fill(0, 6, 0);
        }

        return [
            'months' => $months,
            'ksh' => $revenuesKsh,
            'usd' => $revenuesUsd,
            'combined' => array_map(function($ksh, $usd) {
                return $ksh + $usd;
            }, $revenuesKsh, $revenuesUsd),
        ];
    }

    /**
     * Get top customers by revenue with currency separation
     */
    private function getTopCustomers()
    {
        try {
            $topKshCustomers = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.status', 'paid')
                ->where('consolidated_billings.currency', 'KSH')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.company_name',
                    DB::raw('SUM(consolidated_billings.paid_amount) as total_revenue'),
                    DB::raw('COUNT(consolidated_billings.id) as invoice_count'),
                    DB::raw("'KSH' as currency")
                )
                ->groupBy('users.id', 'users.name', 'users.email', 'users.company_name')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get();

            $topUsdCustomers = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.status', 'paid')
                ->where('consolidated_billings.currency', 'USD')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.company_name',
                    DB::raw('SUM(consolidated_billings.paid_amount) as total_revenue'),
                    DB::raw('COUNT(consolidated_billings.id) as invoice_count'),
                    DB::raw("'USD' as currency")
                )
                ->groupBy('users.id', 'users.name', 'users.email', 'users.company_name')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get();

            return $topKshCustomers->concat($topUsdCustomers)
                ->sortByDesc('total_revenue')
                ->values()
                ->map(function($item) {
                    $item->formatted_revenue = $item->currency == 'KSH'
                        ? 'KSh ' . number_format($item->total_revenue, 2)
                        : '$' . number_format($item->total_revenue, 2);
                    return $item;
                });

        } catch (\Exception $e) {
            Log::error('Error getting top customers: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Format currency based on type
     */
    private function formatCurrency($amount, $currency = 'KSH')
    {
        if ($currency == 'USD') {
            return '$' . number_format($amount, 2);
        } else {
            return 'KSh ' . number_format($amount, 2);
        }
    }

    /**
     * Get currency symbol
     */
    private function getCurrencySymbol($currency = 'KSH')
    {
        return $currency == 'USD' ? '$' : 'KSh ';
    }

    /**
     * Apply billing filters to query
     */
    private function applyBillingFilters($query, Request $request): void
    {
        if ($request->has('status') && $request->status !== 'all' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('billing_cycle') && $request->billing_cycle !== 'all' && $request->billing_cycle !== '') {
            $query->where('billing_cycle', $request->billing_cycle);
        }

        if ($request->has('currency') && $request->currency !== 'all' && $request->currency !== '') {
            $query->where('currency', $request->currency);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('billing_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('billing_date', '<=', $request->date_to);
        }
    }

    /**
     * Apply payment filters to query
     */
    private function applyPaymentFilters($query, Request $request): void
    {
        if ($request->has('payment_method') && $request->payment_method !== 'all' && $request->payment_method !== '') {
            $query->where('payments.payment_method', $request->payment_method);
        }

        if ($request->has('status') && $request->status !== 'all' && $request->status !== '') {
            $query->where('payments.status', $request->status);
        }

        if ($request->has('currency') && $request->currency !== 'all' && $request->currency !== '') {
            $query->where('payments.currency', $request->currency);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('payments.payment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('payments.payment_date', '<=', $request->date_to);
        }
    }

    /**
     * Apply transaction filters to query
     */
    private function applyTransactionFilters($query, Request $request): void
{
    try {
        // Filter by type (payment, invoice, credit, debit, refund)
        if ($request->has('type') && $request->type !== 'all' && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // Filter by direction (in/out)
        if ($request->has('direction') && $request->direction !== 'all' && $request->direction !== '') {
            $query->where('direction', $request->direction);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all' && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== 'all' && $request->payment_method !== '') {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by currency
        if ($request->has('currency') && $request->currency !== 'all' && $request->currency !== '') {
            $query->where('currency', $request->currency);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
    } catch (\Exception $e) {
        Log::error('Error in applyTransactionFilters: ' . $e->getMessage());
        throw $e;
    }
}

    /**
     * Get payment statistics
     */
    private function getPaymentStats(): array
    {
        return [
            'total_collected_ksh' => DB::table('payments')
                ->where('status', 'completed')
                ->where('currency', 'KSH')
                ->sum('amount'),
            'total_collected_usd' => DB::table('payments')
                ->where('status', 'completed')
                ->where('currency', 'USD')
                ->sum('amount'),
            'pending_payments' => DB::table('payments')
                ->where('status', 'pending')
                ->count(),
            'failed_payments' => DB::table('payments')
                ->where('status', 'failed')
                ->count(),
        ];
    }

    /**
     * Get transaction statistics
     */
    private function getTransactionStats(): array
{
    try {
        return [
            'total_income_ksh' => Transaction::where('direction', 'in')
                ->where('status', 'completed')
                ->where('currency', 'KSH')
                ->sum('amount') ?: 0,

            'total_income_usd' => Transaction::where('direction', 'in')
                ->where('status', 'completed')
                ->where('currency', 'USD')
                ->sum('amount') ?: 0,

            'total_expenses_ksh' => Transaction::where('direction', 'out')
                ->where('status', 'completed')
                ->where('currency', 'KSH')
                ->sum('amount') ?: 0,

            'total_expenses_usd' => Transaction::where('direction', 'out')
                ->where('status', 'completed')
                ->where('currency', 'USD')
                ->sum('amount') ?: 0,

            'pending_transactions' => Transaction::where('status', 'pending')->count(),
        ];
    } catch (\Exception $e) {
        Log::error('Error in getTransactionStats: ' . $e->getMessage());
        return [
            'total_income_ksh' => 0,
            'total_income_usd' => 0,
            'total_expenses_ksh' => 0,
            'total_expenses_usd' => 0,
            'pending_transactions' => 0,
        ];
    }
}

    /**
     * Get billing cycles
     */
    private function getBillingCycles(): array
    {
        return [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'annually' => 'Annually',
            'one_time' => 'One Time'
        ];
    }

    /**
     * Get transaction types
     */
    private function getTransactionTypes(): array
    {
        return [
            'income' => 'Income',
            'expense' => 'Expense',
            'transfer' => 'Transfer'
        ];
    }

    /**
     * Get payment methods
     */
    private function getPaymentMethods(): array
    {
        return [
            'credit_card' => 'Credit Card',
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'digital_wallet' => 'Digital Wallet',
            'check' => 'Check'
        ];
    }

    /**
     * Get transaction categories
     */
    private function getTransactionCategories(): array
    {
        return [
            'invoice_payment' => 'Invoice Payment',
            'refund' => 'Refund',
            'fee' => 'Fee',
            'salary' => 'Salary',
            'rent' => 'Rent',
            'utilities' => 'Utilities',
            'maintenance' => 'Maintenance',
            'equipment' => 'Equipment',
            'software' => 'Software',
            'other' => 'Other'
        ];
    }

    /**
     * Validate billing request
     */
    private function validateBillingRequest(Request $request, $id = null): array
    {
        $rules = [
            'lease_id' => 'required|exists:leases,id',
            'customer_id' => 'required|exists:users,id',
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after:billing_date',
            'total_amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|in:KSH,USD',
            'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,paid,overdue,draft',
            'line_items' => 'nullable|array'
        ];

        if ($id) {
            $rules['billing_number'] = 'required|unique:lease_billings,billing_number,' . $id;
        } else {
            $rules['billing_number'] = 'required|unique:lease_billings';
        }

        return $request->validate($rules);
    }

    /**
     * Validate transaction request
     */
    private function validateTransactionRequest(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:KSH,USD',
            'description' => 'required|string|max:500',
            'transaction_date' => 'required|date',
            'payment_method' => 'required|in:credit_card,bank_transfer,cash,digital_wallet,check',
            'category' => 'required|in:invoice_payment,refund,fee,salary,rent,utilities,maintenance,equipment,software,other',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'user_id' => 'nullable|exists:users,id',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);
    }

    /**
     * Generate unique billing number
     */
    private function generateBillingNumber(): string
    {
        $date = now()->format('Ymd');
        $lastBilling = LeaseBilling::where('billing_number', 'like', "INV-{$date}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBilling) {
            $lastNumber = intval(substr($lastBilling->billing_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "INV-{$date}-{$newNumber}";
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId(): string
    {
        $prefix = 'TXN-';
        $year = date('Y');

        do {
            $random = Str::upper(Str::random(8));
            $transactionId = "{$prefix}{$year}-{$random}";
        } while (Transaction::where('reference_number', $transactionId)->exists());

        return $transactionId;
    }

    /**
     * Get payment method column name
     */
    private function getPaymentMethodColumn(): ?string
{
    return 'payment_method'; // Your table has this column
}

    /**
     * Process billing line items
     */
    private function processBillingLineItems(LeaseBilling $billing, array $lineItems): void
    {
        foreach ($lineItems as $item) {
            if (!empty($item['description']) && !empty($item['amount'])) {
                // Store line items logic here
            }
        }
    }

    /**
     * Get date range based on period
     */
    private function getDateRange(string $period, ?string $startDate, ?string $endDate): array
    {
        if ($startDate && $endDate) {
            return [$startDate, $endDate];
        }

        $endDate = now()->format('Y-m-d');

        $startDate = match ($period) {
            'today' => now()->format('Y-m-d'),
            'this_week' => now()->startOfWeek()->format('Y-m-d'),
            'last_month' => now()->subMonth()->startOfMonth()->format('Y-m-d'),
            'this_quarter' => now()->startOfQuarter()->format('Y-m-d'),
            'this_year' => now()->startOfYear()->format('Y-m-d'),
            'last_year' => now()->subYear()->startOfYear()->format('Y-m-d'),
            default => now()->startOfMonth()->format('Y-m-d'),
        };

        if ($period === 'last_month') {
            $endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
        }

        return [$startDate, $endDate];
    }

    /**
     * Generate report based on type
     */
    private function generateReport(string $reportType, string $startDate, string $endDate): array
    {
        $defaultStructure = [
            'cash_flow_summary' => [
                'operating' => 0,
                'investing' => 0,
                'financing' => 0,
                'net_cash_flow' => 0,
            ],
            'cash_flow_details' => [
                'cash_from_customers' => 0,
                'cash_to_suppliers' => 0,
                'cash_for_expenses' => 0,
                'interest_paid' => 0,
                'taxes_paid' => 0,
                'equipment_purchase' => 0,
                'infrastructure_investment' => 0,
                'property_purchase' => 0,
                'investment_income' => 0,
                'asset_sales' => 0,
                'loan_proceeds' => 0,
                'equity_issuance' => 0,
                'dividends_paid' => 0,
                'debt_repayment' => 0,
            ],
            'profitability_metrics' => [
                'gross_margin' => 0,
                'operating_margin' => 0,
                'net_margin' => 0,
                'roi' => 0,
                'roa' => 0,
                'roe' => 0,
            ],
            'p_l_statement' => [
                'revenue' => 0,
                'cost_of_services' => 0,
                'gross_profit' => 0,
                'operating_expenses' => 0,
                'depreciation' => 0,
                'amortization' => 0,
                'operating_profit' => 0,
                'interest_expense' => 0,
                'interest_income' => 0,
                'taxes' => 0,
                'net_income' => 0,
                'ebitda' => 0,
            ],
            'report_type' => $reportType,
        ];

        $reportData = match($reportType) {
            'cash_flow' => $this->generateCashFlowReport($startDate, $endDate),
            'profitability' => $this->generateProfitabilityReport($startDate, $endDate),
            'financial_summary' => $this->generateFinancialSummary($startDate, $endDate),
            'revenue_analysis' => $this->generateRevenueAnalysis($startDate, $endDate),
            'customer_billing' => $this->generateCustomerBillingReport($startDate, $endDate),
            'aging_report' => $this->generateAgingReport(),
            'debt_aging' => $this->generateDebtAgingReport($startDate, $endDate),
            'tax_report' => $this->generateTaxReport($startDate, $endDate),
            'collection_performance' => $this->generateCollectionPerformanceReport($startDate, $endDate),
            default => $this->generateFinancialSummary($startDate, $endDate)
        };

        return $this->arrayMergeRecursive($defaultStructure, $reportData);
    }

    /**
     * Generate debt aging report with currency separation
     */
    private function generateDebtAgingReport($startDate, $endDate): array
    {
        try {
            $agingReportKsh = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->leftJoin('billing_line_items', 'consolidated_billings.id', '=', 'billing_line_items.consolidated_billing_id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.currency', 'KSH')
                ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue'])
                ->selectRaw('
                    consolidated_billings.user_id,
                    users.name as customer_name,
                    consolidated_billings.currency,
                    SUM(CASE WHEN consolidated_billings.due_date >= CURDATE() THEN billing_line_items.amount ELSE 0 END) as current,
                    SUM(CASE WHEN consolidated_billings.due_date < CURDATE() AND consolidated_billings.due_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN billing_line_items.amount ELSE 0 END) as days_30,
                    SUM(CASE WHEN consolidated_billings.due_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND consolidated_billings.due_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN billing_line_items.amount ELSE 0 END) as days_60,
                    SUM(CASE WHEN consolidated_billings.due_date < DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN billing_line_items.amount ELSE 0 END) as days_90_plus')
                ->groupBy('consolidated_billings.user_id', 'users.name', 'consolidated_billings.currency')
                ->get();

            $agingReportUsd = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->leftJoin('billing_line_items', 'consolidated_billings.id', '=', 'billing_line_items.consolidated_billing_id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.currency', 'USD')
                ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue'])
                ->selectRaw('
                    consolidated_billings.user_id,
                    users.name as customer_name,
                    consolidated_billings.currency,
                    SUM(CASE WHEN consolidated_billings.due_date >= CURDATE() THEN billing_line_items.amount ELSE 0 END) as current,
                    SUM(CASE WHEN consolidated_billings.due_date < CURDATE() AND consolidated_billings.due_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN billing_line_items.amount ELSE 0 END) as days_30,
                    SUM(CASE WHEN consolidated_billings.due_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND consolidated_billings.due_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN billing_line_items.amount ELSE 0 END) as days_60,
                    SUM(CASE WHEN consolidated_billings.due_date < DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN billing_line_items.amount ELSE 0 END) as days_90_plus')
                ->groupBy('consolidated_billings.user_id', 'users.name', 'consolidated_billings.currency')
                ->get();

            $currentKsh = $agingReportKsh->sum('current');
            $days30Ksh = $agingReportKsh->sum('days_30');
            $days60Ksh = $agingReportKsh->sum('days_60');
            $days90PlusKsh = $agingReportKsh->sum('days_90_plus');
            $totalReceivablesKsh = $currentKsh + $days30Ksh + $days60Ksh + $days90PlusKsh;
            $overdueKsh = $days30Ksh + $days60Ksh + $days90PlusKsh;

            $currentUsd = $agingReportUsd->sum('current');
            $days30Usd = $agingReportUsd->sum('days_30');
            $days60Usd = $agingReportUsd->sum('days_60');
            $days90PlusUsd = $agingReportUsd->sum('days_90_plus');
            $totalReceivablesUsd = $currentUsd + $days30Usd + $days60Usd + $days90PlusUsd;
            $overdueUsd = $days30Usd + $days60Usd + $days90PlusUsd;

            $detailedAging = [];

            foreach ($agingReportKsh as $item) {
                $totalDue = $item->current + $item->days_30 + $item->days_60 + $item->days_90_plus;
                $riskLevel = $item->days_90_plus > 0 ? 'critical' :
                            ($item->days_60 > 0 ? 'high' :
                            ($item->days_30 > 0 ? 'medium' : 'low'));

                $detailedAging[] = (object)[
                    'customer_name' => $item->customer_name,
                    'currency' => 'KSH',
                    'total_due' => $totalDue,
                    'current' => $item->current,
                    'days_30' => $item->days_30,
                    'days_60' => $item->days_60,
                    'days_over_90' => $item->days_90_plus,
                    'risk_level' => $riskLevel
                ];
            }

            foreach ($agingReportUsd as $item) {
                $totalDue = $item->current + $item->days_30 + $item->days_60 + $item->days_90_plus;
                $riskLevel = $item->days_90_plus > 0 ? 'critical' :
                            ($item->days_60 > 0 ? 'high' :
                            ($item->days_30 > 0 ? 'medium' : 'low'));

                $detailedAging[] = (object)[
                    'customer_name' => $item->customer_name,
                    'currency' => 'USD',
                    'total_due' => $totalDue,
                    'current' => $item->current,
                    'days_30' => $item->days_30,
                    'days_60' => $item->days_60,
                    'days_over_90' => $item->days_90_plus,
                    'risk_level' => $riskLevel
                ];
            }

            return [
                'ksh' => [
                    'total_receivables' => $totalReceivablesKsh,
                    'current' => $currentKsh,
                    'current_percentage' => $totalReceivablesKsh > 0 ? ($currentKsh / $totalReceivablesKsh) * 100 : 0,
                    'days_30' => $days30Ksh,
                    'days_30_percentage' => $totalReceivablesKsh > 0 ? ($days30Ksh / $totalReceivablesKsh) * 100 : 0,
                    'days_60' => $days60Ksh,
                    'days_60_percentage' => $totalReceivablesKsh > 0 ? ($days60Ksh / $totalReceivablesKsh) * 100 : 0,
                    'days_over_90' => $days90PlusKsh,
                    'over_90_percentage' => $totalReceivablesKsh > 0 ? ($days90PlusKsh / $totalReceivablesKsh) * 100 : 0,
                    'overdue' => $overdueKsh,
                    'overdue_percentage' => $totalReceivablesKsh > 0 ? ($overdueKsh / $totalReceivablesKsh) * 100 : 0,
                ],
                'usd' => [
                    'total_receivables' => $totalReceivablesUsd,
                    'current' => $currentUsd,
                    'current_percentage' => $totalReceivablesUsd > 0 ? ($currentUsd / $totalReceivablesUsd) * 100 : 0,
                    'days_30' => $days30Usd,
                    'days_30_percentage' => $totalReceivablesUsd > 0 ? ($days30Usd / $totalReceivablesUsd) * 100 : 0,
                    'days_60' => $days60Usd,
                    'days_60_percentage' => $totalReceivablesUsd > 0 ? ($days60Usd / $totalReceivablesUsd) * 100 : 0,
                    'days_over_90' => $days90PlusUsd,
                    'over_90_percentage' => $totalReceivablesUsd > 0 ? ($days90PlusUsd / $totalReceivablesUsd) * 100 : 0,
                    'overdue' => $overdueUsd,
                    'overdue_percentage' => $totalReceivablesUsd > 0 ? ($overdueUsd / $totalReceivablesUsd) * 100 : 0,
                ],
                'detailed' => $detailedAging,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating debt aging report: ' . $e->getMessage());

            $empty = [
                'total_receivables' => 0,
                'current' => 0,
                'current_percentage' => 0,
                'days_30' => 0,
                'days_30_percentage' => 0,
                'days_60' => 0,
                'days_60_percentage' => 0,
                'days_over_90' => 0,
                'over_90_percentage' => 0,
                'overdue' => 0,
                'overdue_percentage' => 0,
            ];

            return [
                'ksh' => $empty,
                'usd' => $empty,
                'detailed' => [],
            ];
        }
    }

    /**
     * Generate cash flow report with currency separation
     */
    private function generateCashFlowReport($startDate, $endDate): array
    {
        try {
            $exchangeRate = 130; // 1 USD = 130 KSH

            // Cash from customers (KSH)
            $cashFromCustomersKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('paid_amount') ?: 0;

            // Cash from customers (USD)
            $cashFromCustomersUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('paid_amount') ?: 0;

            // Cash paid to suppliers (KSH)
            $cashToSuppliersKsh = DB::table('transactions')
                ->where('type', 'expense')
                ->whereIn('category', ['inventory', 'supplies', 'equipment'])
                ->where('currency', 'KSH')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount') ?: 0;

            // Cash paid to suppliers (USD)
            $cashToSuppliersUsd = DB::table('transactions')
                ->where('type', 'expense')
                ->whereIn('category', ['inventory', 'supplies', 'equipment'])
                ->where('currency', 'USD')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount') ?: 0;

            // Cash paid for operating expenses (KSH)
            $cashForExpensesKsh = DB::table('transactions')
                ->where('type', 'expense')
                ->whereIn('category', ['utilities', 'rent', 'salaries', 'maintenance', 'software'])
                ->where('currency', 'KSH')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount') ?: 0;

            // Cash paid for operating expenses (USD)
            $cashForExpensesUsd = DB::table('transactions')
                ->where('type', 'expense')
                ->whereIn('category', ['utilities', 'rent', 'salaries', 'maintenance', 'software'])
                ->where('currency', 'USD')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount') ?: 0;

            // Equipment purchases (KSH)
            $equipmentPurchaseKsh = DB::table('transactions')
                ->where('type', 'expense')
                ->where('category', 'equipment')
                ->where('currency', 'KSH')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount') ?: 0;

            // Equipment purchases (USD)
            $equipmentPurchaseUsd = DB::table('transactions')
                ->where('type', 'expense')
                ->where('category', 'equipment')
                ->where('currency', 'USD')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount') ?: 0;

            // Loan proceeds (KSH)
            $loanProceedsKsh = DB::table('transactions')
                ->where('type', 'income')
                ->where('category', 'loan')
                ->where('currency', 'KSH')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount') ?: 0;

            // Loan proceeds (USD)
            $loanProceedsUsd = DB::table('transactions')
                ->where('type', 'income')
                ->where('category', 'loan')
                ->where('currency', 'USD')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount') ?: 0;

            // Operating cash flow
            $operatingKsh = $cashFromCustomersKsh - $cashToSuppliersKsh - $cashForExpensesKsh;
            $operatingUsd = $cashFromCustomersUsd - $cashToSuppliersUsd - $cashForExpensesUsd;

            // Investing cash flow
            $investingKsh = -$equipmentPurchaseKsh;
            $investingUsd = -$equipmentPurchaseUsd;

            // Financing cash flow
            $financingKsh = $loanProceedsKsh;
            $financingUsd = $loanProceedsUsd;

            // Net cash flow
            $netCashFlowKsh = $operatingKsh + $investingKsh + $financingKsh;
            $netCashFlowUsd = $operatingUsd + $investingUsd + $financingUsd;

            return [
                'ksh' => [
                    'operating' => $operatingKsh,
                    'investing' => $investingKsh,
                    'financing' => $financingKsh,
                    'net' => $netCashFlowKsh,
                    'details' => [
                        'cash_from_customers' => $cashFromCustomersKsh,
                        'cash_to_suppliers' => -$cashToSuppliersKsh,
                        'cash_for_expenses' => -$cashForExpensesKsh,
                        'equipment_purchase' => -$equipmentPurchaseKsh,
                        'loan_proceeds' => $loanProceedsKsh,
                    ]
                ],
                'usd' => [
                    'operating' => $operatingUsd,
                    'investing' => $investingUsd,
                    'financing' => $financingUsd,
                    'net' => $netCashFlowUsd,
                    'details' => [
                        'cash_from_customers' => $cashFromCustomersUsd,
                        'cash_to_suppliers' => -$cashToSuppliersUsd,
                        'cash_for_expenses' => -$cashForExpensesUsd,
                        'equipment_purchase' => -$equipmentPurchaseUsd,
                        'loan_proceeds' => $loanProceedsUsd,
                    ]
                ],
                'combined' => [
                    'operating' => $operatingKsh + $operatingUsd,
                    'investing' => $investingKsh + $investingUsd,
                    'financing' => $financingKsh + $financingUsd,
                    'net' => $netCashFlowKsh + $netCashFlowUsd,
                ],
                'exchange_rate' => $exchangeRate,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating cash flow report: ' . $e->getMessage());

            return [
                'ksh' => ['operating' => 0, 'investing' => 0, 'financing' => 0, 'net' => 0, 'details' => []],
                'usd' => ['operating' => 0, 'investing' => 0, 'financing' => 0, 'net' => 0, 'details' => []],
                'combined' => ['operating' => 0, 'investing' => 0, 'financing' => 0, 'net' => 0],
                'exchange_rate' => 130,
            ];
        }
    }

    /**
     * Generate profitability report with currency separation
     */
    private function generateProfitabilityReport($startDate, $endDate): array
    {
        try {
            // Revenue by currency
            $revenueKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->sum('paid_amount') ?: 0;

            $revenueUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->sum('paid_amount') ?: 0;

            // Expenses by currency (simplified)
            $expensesKsh = DB::table('transactions')
                ->where('type', 'expense')
                ->where('currency', 'KSH')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount') ?: $revenueKsh * 0.6;

            $expensesUsd = DB::table('transactions')
                ->where('type', 'expense')
                ->where('currency', 'USD')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount') ?: $revenueUsd * 0.6;

            // Calculate profits
            $grossProfitKsh = $revenueKsh - $expensesKsh;
            $grossProfitUsd = $revenueUsd - $expensesUsd;

            $netProfitKsh = $grossProfitKsh * 0.7; // Simplified - 30% tax/other
            $netProfitUsd = $grossProfitUsd * 0.7;

            // Calculate margins
            $grossMarginKsh = $revenueKsh > 0 ? ($grossProfitKsh / $revenueKsh) * 100 : 0;
            $grossMarginUsd = $revenueUsd > 0 ? ($grossProfitUsd / $revenueUsd) * 100 : 0;

            $netMarginKsh = $revenueKsh > 0 ? ($netProfitKsh / $revenueKsh) * 100 : 0;
            $netMarginUsd = $revenueUsd > 0 ? ($netProfitUsd / $revenueUsd) * 100 : 0;

            return [
                'ksh' => [
                    'revenue' => $revenueKsh,
                    'expenses' => $expensesKsh,
                    'gross_profit' => $grossProfitKsh,
                    'net_profit' => $netProfitKsh,
                    'gross_margin' => $grossMarginKsh,
                    'net_margin' => $netMarginKsh,
                ],
                'usd' => [
                    'revenue' => $revenueUsd,
                    'expenses' => $expensesUsd,
                    'gross_profit' => $grossProfitUsd,
                    'net_profit' => $netProfitUsd,
                    'gross_margin' => $grossMarginUsd,
                    'net_margin' => $netMarginUsd,
                ],
                'combined' => [
                    'revenue' => $revenueKsh + $revenueUsd,
                    'expenses' => $expensesKsh + $expensesUsd,
                    'gross_profit' => $grossProfitKsh + $grossProfitUsd,
                    'net_profit' => $netProfitKsh + $netProfitUsd,
                    'gross_margin' => ($grossMarginKsh + $grossMarginUsd) / 2,
                    'net_margin' => ($netMarginKsh + $netMarginUsd) / 2,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error generating profitability report: ' . $e->getMessage());

            return [
                'ksh' => ['revenue' => 0, 'expenses' => 0, 'gross_profit' => 0, 'net_profit' => 0, 'gross_margin' => 0, 'net_margin' => 0],
                'usd' => ['revenue' => 0, 'expenses' => 0, 'gross_profit' => 0, 'net_profit' => 0, 'gross_margin' => 0, 'net_margin' => 0],
                'combined' => ['revenue' => 0, 'expenses' => 0, 'gross_profit' => 0, 'net_profit' => 0, 'gross_margin' => 0, 'net_margin' => 0],
            ];
        }
    }

    /**
     * Get scheduled billings
     */
    private function getScheduledBillings()
    {
        return ConsolidatedBilling::with(['user' => function($query) {
                $query->where('role', 'customer');
            }])
            ->where('due_date', '>=', now())
            ->whereIn('status', ['pending', 'sent'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Get auto billing customers
     */
    private function getAutoBillingCustomers()
    {
        try {
            return User::where('role', 'customer')
                ->where('auto_billing_enabled', true)
                ->when(in_array('status', Schema::getColumnListing('users')), function($query) {
                    $query->where('status', 'active');
                })
                ->with(['consolidatedBillings' => function($query) {
                    $query->whereIn('status', ['pending', 'sent']);
                }])
                ->paginate(10);
        } catch (\Exception $e) {
            Log::error('Error getting auto billing customers: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get due customers
     */
    private function getDueCustomers()
    {
        return User::where('role', 'customer')
            ->whereHas('consolidatedBillings', function($query) {
                $query->where('due_date', '<=', now())
                      ->whereIn('status', ['pending', 'sent']);
            })
            ->with(['consolidatedBillings' => function($query) {
                $query->whereIn('status', ['pending', 'sent']);
            }])
            ->limit(10)
            ->get();
    }

    /**
     * Get auto billing stats
     */
    private function getAutoBillingStats(): array
    {
        return [
            'due_customers_count' => User::where('role', 'customer')
                ->whereHas('consolidatedBillings', function($query) {
                    $query->where('due_date', '<=', now())
                          ->whereIn('status', ['pending', 'sent']);
                })->count(),

            'auto_billing_count' => User::where('role', 'customer')
                ->where('auto_billing_enabled', true)
                ->whereHas('consolidatedBillings', function($query) {
                    $query->whereIn('status', ['pending', 'sent']);
                })->count(),

            'total_auto_billing' => User::where('role', 'customer')
                ->where('auto_billing_enabled', true)->count(),

            'overdue_count' => ConsolidatedBilling::where('status', 'overdue')->count(),

            'monthly_revenue_ksh' => ConsolidatedBilling::whereIn('status', ['pending', 'sent'])
                ->where('currency', 'KSH')
                ->sum('total_amount'),

            'monthly_revenue_usd' => ConsolidatedBilling::whereIn('status', ['pending', 'sent'])
                ->where('currency', 'USD')
                ->sum('total_amount'),

            'upcoming_billings' => User::where('role', 'customer')
                ->whereHas('consolidatedBillings', function($query) {
                    $query->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()])
                          ->whereIn('status', ['pending', 'sent']);
                })->count(),

            'scheduled_count' => User::where('role', 'customer')
                ->where('next_billing_date', '<=', now())->count(),
        ];
    }

    /**
     * Generate financial summary report with currency separation
     */
    private function generateFinancialSummary($startDate, $endDate): array
    {
        $totalRevenueKsh = DB::table('consolidated_billings')
            ->where('status', 'paid')
            ->where('currency', 'KSH')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('paid_amount') ?: 0;

        $totalRevenueUsd = DB::table('consolidated_billings')
            ->where('status', 'paid')
            ->where('currency', 'USD')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('paid_amount') ?: 0;

        $pendingAmountKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent'])
            ->where('currency', 'KSH')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount') ?: 0;

        $pendingAmountUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent'])
            ->where('currency', 'USD')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount') ?: 0;

        $overdueAmountKsh = DB::table('consolidated_billings')
            ->where(function($query) {
                $query->where('status', 'overdue')
                    ->orWhere(function($q) {
                        $q->whereIn('status', ['pending', 'sent'])
                            ->where('due_date', '<', now());
                    });
            })
            ->where('currency', 'KSH')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount') ?: 0;

        $overdueAmountUsd = DB::table('consolidated_billings')
            ->where(function($query) {
                $query->where('status', 'overdue')
                    ->orWhere(function($q) {
                        $q->whereIn('status', ['pending', 'sent'])
                            ->where('due_date', '<', now());
                    });
            })
            ->where('currency', 'USD')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount') ?: 0;

        $revenueByCurrency = DB::table('consolidated_billings')
            ->select(
                'currency',
                DB::raw('SUM(paid_amount) as total_revenue'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('AVG(paid_amount) as avg_invoice_amount')
            )
            ->where('status', 'paid')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->groupBy('currency')
            ->get();

        $monthlyTrendKsh = DB::table('consolidated_billings')
            ->select(
                DB::raw('YEAR(billing_date) as year'),
                DB::raw('MONTH(billing_date) as month'),
                DB::raw('SUM(paid_amount) as monthly_revenue'),
                DB::raw('COUNT(*) as invoices_count')
            )
            ->where('status', 'paid')
            ->where('currency', 'KSH')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->groupBy(DB::raw('YEAR(billing_date)'), DB::raw('MONTH(billing_date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $monthlyTrendUsd = DB::table('consolidated_billings')
            ->select(
                DB::raw('YEAR(billing_date) as year'),
                DB::raw('MONTH(billing_date) as month'),
                DB::raw('SUM(paid_amount) as monthly_revenue'),
                DB::raw('COUNT(*) as invoices_count')
            )
            ->where('status', 'paid')
            ->where('currency', 'USD')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->groupBy(DB::raw('YEAR(billing_date)'), DB::raw('MONTH(billing_date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return [
            'ksh' => [
                'total_revenue' => $totalRevenueKsh,
                'pending_amount' => $pendingAmountKsh,
                'overdue_amount' => $overdueAmountKsh,
                'monthly_trend' => $monthlyTrendKsh,
            ],
            'usd' => [
                'total_revenue' => $totalRevenueUsd,
                'pending_amount' => $pendingAmountUsd,
                'overdue_amount' => $overdueAmountUsd,
                'monthly_trend' => $monthlyTrendUsd,
            ],
            'combined' => [
                'total_revenue' => $totalRevenueKsh + $totalRevenueUsd,
                'pending_amount' => $pendingAmountKsh + $pendingAmountUsd,
                'overdue_amount' => $overdueAmountKsh + $overdueAmountUsd,
            ],
            'revenue_by_currency' => $revenueByCurrency,
        ];
    }

    /**
     * Generate revenue analysis report with currency separation
     */
    private function generateRevenueAnalysis($startDate, $endDate): array
    {
        try {
            $revenueByCustomerKsh = DB::table('billing_line_items')
                ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.status', 'paid')
                ->where('consolidated_billings.currency', 'KSH')
                ->whereBetween('consolidated_billings.billing_date', [$startDate, $endDate])
                ->selectRaw('
                    users.name as customer_name,
                    consolidated_billings.user_id,
                    SUM(billing_line_items.amount) as revenue,
                    COUNT(DISTINCT consolidated_billings.id) as invoice_count')
                ->groupBy('consolidated_billings.user_id', 'users.name')
                ->orderBy('revenue', 'desc')
                ->get();

            $revenueByCustomerUsd = DB::table('billing_line_items')
                ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.status', 'paid')
                ->where('consolidated_billings.currency', 'USD')
                ->whereBetween('consolidated_billings.billing_date', [$startDate, $endDate])
                ->selectRaw('
                    users.name as customer_name,
                    consolidated_billings.user_id,
                    SUM(billing_line_items.amount) as revenue,
                    COUNT(DISTINCT consolidated_billings.id) as invoice_count')
                ->groupBy('consolidated_billings.user_id', 'users.name')
                ->orderBy('revenue', 'desc')
                ->get();

            $revenueByServiceKsh = DB::table('billing_line_items')
                ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
                ->where('consolidated_billings.status', 'paid')
                ->where('consolidated_billings.currency', 'KSH')
                ->whereBetween('consolidated_billings.billing_date', [$startDate, $endDate])
                ->selectRaw('
                    billing_line_items.billing_cycle,
                    SUM(billing_line_items.amount) as revenue,
                    COUNT(DISTINCT consolidated_billings.id) as count')
                ->groupBy('billing_line_items.billing_cycle')
                ->orderBy('revenue', 'desc')
                ->get();

            $revenueByServiceUsd = DB::table('billing_line_items')
                ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
                ->where('consolidated_billings.status', 'paid')
                ->where('consolidated_billings.currency', 'USD')
                ->whereBetween('consolidated_billings.billing_date', [$startDate, $endDate])
                ->selectRaw('
                    billing_line_items.billing_cycle,
                    SUM(billing_line_items.amount) as revenue,
                    COUNT(DISTINCT consolidated_billings.id) as count')
                ->groupBy('billing_line_items.billing_cycle')
                ->orderBy('revenue', 'desc')
                ->get();

            return [
                'ksh' => [
                    'by_customer' => $revenueByCustomerKsh,
                    'by_service' => $revenueByServiceKsh,
                ],
                'usd' => [
                    'by_customer' => $revenueByCustomerUsd,
                    'by_service' => $revenueByServiceUsd,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error generating revenue analysis: ' . $e->getMessage());
            return [
                'ksh' => ['by_customer' => collect(), 'by_service' => collect()],
                'usd' => ['by_customer' => collect(), 'by_service' => collect()],
            ];
        }
    }

    /**
     * Generate customer billing report with currency separation
     */
    private function generateCustomerBillingReport($startDate, $endDate): array
    {
        try {
            $customerBillingKsh = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.currency', 'KSH')
                ->whereBetween('consolidated_billings.billing_date', [$startDate, $endDate])
                ->selectRaw('
                    consolidated_billings.user_id,
                    users.name as customer_name,
                    COUNT(DISTINCT consolidated_billings.id) as total_billings,
                    SUM(CASE WHEN consolidated_billings.status = "paid" THEN consolidated_billings.paid_amount ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN consolidated_billings.status IN ("pending", "sent") THEN consolidated_billings.total_amount ELSE 0 END) as pending_amount')
                ->groupBy('consolidated_billings.user_id', 'users.name')
                ->orderBy('paid_amount', 'desc')
                ->get();

            $customerBillingUsd = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.currency', 'USD')
                ->whereBetween('consolidated_billings.billing_date', [$startDate, $endDate])
                ->selectRaw('
                    consolidated_billings.user_id,
                    users.name as customer_name,
                    COUNT(DISTINCT consolidated_billings.id) as total_billings,
                    SUM(CASE WHEN consolidated_billings.status = "paid" THEN consolidated_billings.paid_amount ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN consolidated_billings.status IN ("pending", "sent") THEN consolidated_billings.total_amount ELSE 0 END) as pending_amount')
                ->groupBy('consolidated_billings.user_id', 'users.name')
                ->orderBy('paid_amount', 'desc')
                ->get();

            return [
                'ksh' => $customerBillingKsh,
                'usd' => $customerBillingUsd,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating customer billing report: ' . $e->getMessage());
            return [
                'ksh' => collect(),
                'usd' => collect(),
            ];
        }
    }

    /**
     * Generate aging report with currency separation
     */
    private function generateAgingReport(): array
    {
        try {
            $agingReportKsh = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->leftJoin('billing_line_items', 'consolidated_billings.id', '=', 'billing_line_items.consolidated_billing_id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.currency', 'KSH')
                ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue'])
                ->selectRaw('
                    consolidated_billings.user_id,
                    users.name as customer_name,
                    SUM(CASE WHEN consolidated_billings.due_date >= CURDATE() THEN billing_line_items.amount ELSE 0 END) as current,
                    SUM(CASE WHEN consolidated_billings.due_date < CURDATE() AND consolidated_billings.due_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN billing_line_items.amount ELSE 0 END) as days_30,
                    SUM(CASE WHEN consolidated_billings.due_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND consolidated_billings.due_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN billing_line_items.amount ELSE 0 END) as days_60,
                    SUM(CASE WHEN consolidated_billings.due_date < DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN billing_line_items.amount ELSE 0 END) as days_90_plus')
                ->groupBy('consolidated_billings.user_id', 'users.name')
                ->get();

            $agingReportUsd = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->leftJoin('billing_line_items', 'consolidated_billings.id', '=', 'billing_line_items.consolidated_billing_id')
                ->where('users.role', 'customer')
                ->where('consolidated_billings.currency', 'USD')
                ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue'])
                ->selectRaw('
                    consolidated_billings.user_id,
                    users.name as customer_name,
                    SUM(CASE WHEN consolidated_billings.due_date >= CURDATE() THEN billing_line_items.amount ELSE 0 END) as current,
                    SUM(CASE WHEN consolidated_billings.due_date < CURDATE() AND consolidated_billings.due_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN billing_line_items.amount ELSE 0 END) as days_30,
                    SUM(CASE WHEN consolidated_billings.due_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND consolidated_billings.due_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN billing_line_items.amount ELSE 0 END) as days_60,
                    SUM(CASE WHEN consolidated_billings.due_date < DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN billing_line_items.amount ELSE 0 END) as days_90_plus')
                ->groupBy('consolidated_billings.user_id', 'users.name')
                ->get();

            $totalCurrentKsh = $agingReportKsh->sum('current');
            $totalDays30Ksh = $agingReportKsh->sum('days_30');
            $totalDays60Ksh = $agingReportKsh->sum('days_60');
            $totalDays90PlusKsh = $agingReportKsh->sum('days_90_plus');
            $totalOutstandingKsh = $totalCurrentKsh + $totalDays30Ksh + $totalDays60Ksh + $totalDays90PlusKsh;

            $totalCurrentUsd = $agingReportUsd->sum('current');
            $totalDays30Usd = $agingReportUsd->sum('days_30');
            $totalDays60Usd = $agingReportUsd->sum('days_60');
            $totalDays90PlusUsd = $agingReportUsd->sum('days_90_plus');
            $totalOutstandingUsd = $totalCurrentUsd + $totalDays30Usd + $totalDays60Usd + $totalDays90PlusUsd;

            return [
                'ksh' => [
                    'details' => $agingReportKsh,
                    'summary' => [
                        'current' => $totalCurrentKsh,
                        'days_30' => $totalDays30Ksh,
                        'days_60' => $totalDays60Ksh,
                        'days_90_plus' => $totalDays90PlusKsh,
                        'total' => $totalOutstandingKsh,
                    ]
                ],
                'usd' => [
                    'details' => $agingReportUsd,
                    'summary' => [
                        'current' => $totalCurrentUsd,
                        'days_30' => $totalDays30Usd,
                        'days_60' => $totalDays60Usd,
                        'days_90_plus' => $totalDays90PlusUsd,
                        'total' => $totalOutstandingUsd,
                    ]
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error generating aging report: ' . $e->getMessage());

            $empty = [
                'details' => collect(),
                'summary' => [
                    'current' => 0,
                    'days_30' => 0,
                    'days_60' => 0,
                    'days_90_plus' => 0,
                    'total' => 0,
                ]
            ];

            return [
                'ksh' => $empty,
                'usd' => $empty,
            ];
        }
    }

    /**
     * Generate tax report with Kenya tax rates
     */
    private function generateTaxReport($startDate, $endDate): array
    {
        try {
            $vatRate = 0.16;        // 16% VAT
            $withholdingServiceRate = 0.05;  // 5% for services
            $withholdingProfessionalRate = 0.10; // 10% for professional services
            $digitalServiceTaxRate = 0.015; // 1.5% for digital services

            $taxSummaryKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->selectRaw('
                    SUM(total_amount) as total_amount,
                    SUM(total_amount * ?) as total_tax,
                    COUNT(*) as invoice_count,
                    ? as avg_tax_rate', [$vatRate, $vatRate * 100])
                ->first();

            $taxSummaryUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->selectRaw('
                    SUM(total_amount) as total_amount,
                    SUM(total_amount * ?) as total_tax,
                    COUNT(*) as invoice_count,
                    ? as avg_tax_rate', [$vatRate, $vatRate * 100])
                ->first();

            if (!$taxSummaryKsh) {
                $taxSummaryKsh = (object)[
                    'total_amount' => 0,
                    'total_tax' => 0,
                    'invoice_count' => 0,
                    'avg_tax_rate' => 16
                ];
            }

            if (!$taxSummaryUsd) {
                $taxSummaryUsd = (object)[
                    'total_amount' => 0,
                    'total_tax' => 0,
                    'invoice_count' => 0,
                    'avg_tax_rate' => 16
                ];
            }

            return [
                'ksh' => $taxSummaryKsh,
                'usd' => $taxSummaryUsd,
                'combined' => (object)[
                    'total_amount' => $taxSummaryKsh->total_amount + $taxSummaryUsd->total_amount,
                    'total_tax' => $taxSummaryKsh->total_tax + $taxSummaryUsd->total_tax,
                    'invoice_count' => $taxSummaryKsh->invoice_count + $taxSummaryUsd->invoice_count,
                    'avg_tax_rate' => 16,
                ],
                'rates' => [
                    'vat' => $vatRate * 100,
                    'withholding_service' => $withholdingServiceRate * 100,
                    'withholding_professional' => $withholdingProfessionalRate * 100,
                    'digital_service' => $digitalServiceTaxRate * 100,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error generating tax report: ' . $e->getMessage());

            $empty = (object)[
                'total_amount' => 0,
                'total_tax' => 0,
                'invoice_count' => 0,
                'avg_tax_rate' => 16
            ];

            return [
                'ksh' => $empty,
                'usd' => $empty,
                'combined' => $empty,
                'rates' => [
                    'vat' => 16,
                    'withholding_service' => 5,
                    'withholding_professional' => 10,
                    'digital_service' => 1.5,
                ],
            ];
        }
    }

    /**
     * Generate collection performance report
     */
    private function generateCollectionPerformanceReport($startDate, $endDate): array
    {
        try {
            $collectionData = DB::table('consolidated_billings')
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->selectRaw('
                    COUNT(*) as total_billings,
                    SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN currency = "KSH" AND status = "paid" THEN total_amount ELSE 0 END) as paid_amount_ksh,
                    SUM(CASE WHEN currency = "USD" AND status = "paid" THEN total_amount ELSE 0 END) as paid_amount_usd')
                ->first();

            return [
                'data' => $collectionData,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating collection performance report: ' . $e->getMessage());
            return [
                'data' => (object)[
                    'total_billings' => 0,
                    'paid_count' => 0,
                    'paid_amount' => 0,
                    'paid_amount_ksh' => 0,
                    'paid_amount_usd' => 0
                ],
            ];
        }
    }

    /**
     * Array merge recursive helper
     */
    private function arrayMergeRecursive(array $default, array $data): array
    {
        $result = $default;

        foreach ($data as $key => $value) {
            if (is_array($value) && isset($result[$key]) && is_array($result[$key])) {
                $result[$key] = $this->arrayMergeRecursive($result[$key], $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Export report in various formats
     */
    private function exportReport(string $reportType, array $reportData, string $startDate, string $endDate)
    {
        // Implement export functionality (CSV, Excel, PDF)
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Test SMTP connection
     */
    private function testSMTPConnection()
    {
        try {
            $transport = new \Swift_SmtpTransport(
                config('mail.mailers.smtp.host'),
                config('mail.mailers.smtp.port'),
                config('mail.mailers.smtp.encryption')
            );

            $transport->setUsername(config('mail.mailers.smtp.username'));
            $transport->setPassword(config('mail.mailers.smtp.password'));

            $mailer = new \Swift_Mailer($transport);
            $mailer->getTransport()->start();

            Log::info('SMTP Connection Test: SUCCESS', [
                'host' => config('mail.mailers.smtp.host')
            ]);

        } catch (\Exception $e) {
            Log::error('SMTP Connection Test: FAILED', [
                'error' => $e->getMessage(),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port')
            ]);
            throw $e;
        }
    }
}
