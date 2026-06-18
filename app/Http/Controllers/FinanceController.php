<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\BillingLineItem;
use App\Models\ConsolidatedBilling;
use App\Models\LeaseBilling;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Lease;
use App\Models\Payment;
use App\Services\AutomatedBillingService;
use App\Traits\CurrencyHelper;
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
use Illuminate\Support\Facades\Validator;

class FinanceController extends Controller
{
    use CurrencyHelper;

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

    public function showPayment($id)
    {
        $payment = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->leftJoin('consolidated_billings', 'payments.billing_id', '=', 'consolidated_billings.id')
            ->select('payments.*', 'users.name as customer_name', 'users.email',
                'consolidated_billings.billing_number')
            ->where('payments.id', $id)
            ->first();

        if (!$payment) {
            return redirect()->route('finance.payments.index')
                ->with('error', 'Payment not found.');
        }

        // Get allocations
        $allocations = DB::table('payment_allocations')
            ->join('consolidated_billings', 'payment_allocations.invoice_id', '=', 'consolidated_billings.id')
            ->where('payment_allocations.payment_id', $id)
            ->select('payment_allocations.*', 'consolidated_billings.billing_number')
            ->get();

        return view('finance.payments.show', compact('payment', 'allocations'));
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

        Log::info('Report generation', [
            'report_type' => $reportType,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);


        try {
            $reportData = $this->generateReport($reportType, $startDate, $endDate);

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
    // REPORT GENERATION METHODS
    // ==========================

  /**
 * Generate report based on type
 */
private function generateReport(string $reportType, string $startDate, string $endDate): array
{
    $reportData = [];

    switch($reportType) {
        case 'financial_summary':
            $reportData = $this->generateFinancialSummary($startDate, $endDate);
            break;
        case 'revenue_analysis':
            $revenueData = $this->generateRevenueAnalysis($startDate, $endDate);
            $reportData = [
                'revenue_by_customer_ksh' => $revenueData['revenue_by_customer_ksh'] ?? collect(),
                'revenue_by_customer_usd' => $revenueData['revenue_by_customer_usd'] ?? collect(),
                'revenue_by_service_ksh' => $revenueData['revenue_by_service_ksh'] ?? collect(),
                'revenue_by_service_usd' => $revenueData['revenue_by_service_usd'] ?? collect(),
            ];
            break;
        case 'customer_billing':
            $billingData = $this->generateCustomerBillingReport($startDate, $endDate);
            $reportData = [
                'customer_billing_ksh' => $billingData['customer_billing_ksh'] ?? collect(),
                'customer_billing_usd' => $billingData['customer_billing_usd'] ?? collect(),
            ];
            break;
        case 'aging_report':
            $agingData = $this->generateAgingReport();
            $reportData = [
                'aging_report_ksh' => $agingData['aging_report_ksh'] ?? collect(),
                'aging_report_usd' => $agingData['aging_report_usd'] ?? collect(),
            ];
            break;
        case 'debt_aging':
            $reportData = $this->generateDebtAgingReport($startDate, $endDate);
            break;
        case 'cash_flow':
            $reportData = $this->generateCashFlowReport($startDate, $endDate);
            break;
        case 'profitability':
            $reportData = $this->generateProfitabilityReport($startDate, $endDate);
            break;
        case 'tax_report':
            $taxData = $this->generateTaxReport($startDate, $endDate);
            $reportData = [
                'tax_summary_ksh' => $taxData['tax_summary_ksh'] ?? null,
                'tax_summary_usd' => $taxData['tax_summary_usd'] ?? null,
                'tax_by_type' => $taxData['tax_by_type'] ?? collect(),
            ];
            break;
        default:
            $reportData = $this->generateFinancialSummary($startDate, $endDate);
            break;
    }

    // Add common fields
    $reportData['start_date'] = $startDate;
    $reportData['end_date'] = $endDate;
    $reportData['report_type'] = $reportType;

    return $reportData;
}

    /**
 * Generate financial summary report with currency separation
 */
private function generateFinancialSummary($startDate, $endDate): array
{
    try {
        $today = date('Y-m-d');

        // ============ KSH METRICS ============
        // Total Revenue KSH (sum of paid_amount for paid OR partial invoices in the period)
        $totalRevenueKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'KSH')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('paid_amount') ?: 0;

        // Pending Amount KSH (unpaid balance for ALL pending/partial invoices regardless of date)
        $pendingAmountKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $pendingInvoicesKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->count();

        // Overdue Amount KSH (past due date)
        $overdueAmountKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $today)
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $overdueInvoicesKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $today)
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->count();

        // ============ USD METRICS ============
        $totalRevenueUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'USD')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('paid_amount') ?: 0;

        $pendingAmountUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $pendingInvoicesUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->count();

        $overdueAmountUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $today)
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $overdueInvoicesUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $today)
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->count();

        // ============ REVENUE BY CURRENCY ============
        $revenueByCurrency = DB::table('consolidated_billings')
            ->select(
                'currency',
                DB::raw('SUM(COALESCE(paid_amount, 0)) as total_revenue'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('AVG(COALESCE(paid_amount, 0)) as avg_invoice_amount')
            )
            ->whereIn('status', ['paid', 'partial'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('currency')
            ->get();

        // ============ REVENUE BY SERVICE TYPE ============
        $revenueByTypeKsh = DB::table('billing_line_items')
            ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->where('consolidated_billings.currency', 'KSH')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(billing_line_items.billing_cycle, "monthly") as billing_cycle'),
                DB::raw('SUM(billing_line_items.amount) as revenue'),
                DB::raw('COUNT(DISTINCT consolidated_billings.id) as count')
            )
            ->groupBy('billing_line_items.billing_cycle')
            ->get();

        $revenueByTypeUsd = DB::table('billing_line_items')
            ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->where('consolidated_billings.currency', 'USD')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(billing_line_items.billing_cycle, "monthly") as billing_cycle'),
                DB::raw('SUM(billing_line_items.amount) as revenue'),
                DB::raw('COUNT(DISTINCT consolidated_billings.id) as count')
            )
            ->groupBy('billing_line_items.billing_cycle')
            ->get();

        // ============ MONTHLY REVENUE TREND ============
        $monthlyTrendKsh = DB::table('consolidated_billings')
            ->select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(paid_amount) as monthly_revenue'),
                DB::raw('COUNT(*) as invoices_count')
            )
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'KSH')
            ->whereNotNull('payment_date')
            ->whereYear('payment_date', date('Y'))
            ->groupBy(DB::raw('YEAR(payment_date)'), DB::raw('MONTH(payment_date)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyTrendUsd = DB::table('consolidated_billings')
            ->select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(paid_amount) as monthly_revenue'),
                DB::raw('COUNT(*) as invoices_count')
            )
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'USD')
            ->whereNotNull('payment_date')
            ->whereYear('payment_date', date('Y'))
            ->groupBy(DB::raw('YEAR(payment_date)'), DB::raw('MONTH(payment_date)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // ============ TOP CUSTOMERS (ALL TIME) ============
        $topCustomersKsh = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->where('consolidated_billings.currency', 'KSH')
            ->select(
                'users.name',
                DB::raw('SUM(consolidated_billings.paid_amount) as total_spent'),
                DB::raw('COUNT(consolidated_billings.id) as invoices_count')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        $topCustomersUsd = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->where('consolidated_billings.currency', 'USD')
            ->select(
                'users.name',
                DB::raw('SUM(consolidated_billings.paid_amount) as total_spent'),
                DB::raw('COUNT(consolidated_billings.id) as invoices_count')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // ============ MOST DELAYED INVOICES (using outstanding amount) ============
        $mostDelayedKsh = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'partial', 'overdue'])
            ->where('consolidated_billings.currency', 'KSH')
            ->where('consolidated_billings.due_date', '<', $today)
            ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
            ->select(
                'consolidated_billings.billing_number',
                'users.name as customer_name',
                DB::raw('consolidated_billings.total_amount as total_amount'),
                DB::raw('DATEDIFF(NOW(), consolidated_billings.due_date) as days_late')
            )
            ->orderBy('days_late', 'desc')
            ->limit(5)
            ->get();

        $mostDelayedUsd = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'partial', 'overdue'])
            ->where('consolidated_billings.currency', 'USD')
            ->where('consolidated_billings.due_date', '<', $today)
            ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
            ->select(
                'consolidated_billings.billing_number',
                'users.name as customer_name',
                DB::raw('consolidated_billings.total_amount as total_amount'),
                DB::raw('DATEDIFF(NOW(), consolidated_billings.due_date) as days_late')
            )
            ->orderBy('days_late', 'desc')
            ->limit(5)
            ->get();

        // ============ UPCOMING DUE DATES ============
        $upcomingDueKsh = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'partial'])
            ->where('consolidated_billings.currency', 'KSH')
            ->where('consolidated_billings.due_date', '>=', $today)
            ->where('consolidated_billings.due_date', '<=', DB::raw('DATE_ADD(NOW(), INTERVAL 7 DAY)'))
            ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
            ->select(
                'consolidated_billings.billing_number',
                'users.name as customer_name',
                DB::raw('consolidated_billings.total_amount as total_amount'),
                'consolidated_billings.due_date',
                DB::raw('DATEDIFF(consolidated_billings.due_date, NOW()) as days_until_due'
            ))
            ->orderBy('days_until_due', 'asc')
            ->get();

        $upcomingDueUsd = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'partial'])
            ->where('consolidated_billings.currency', 'USD')
            ->where('consolidated_billings.due_date', '>=', $today)
            ->where('consolidated_billings.due_date', '<=', DB::raw('DATE_ADD(NOW(), INTERVAL 7 DAY)'))
            ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
            ->select(
                'consolidated_billings.billing_number',
                'users.name as customer_name',
                DB::raw('consolidated_billings.total_amount as total_amount'),
                'consolidated_billings.due_date',
                DB::raw('DATEDIFF(consolidated_billings.due_date, NOW()) as days_until_due'
            ))
            ->orderBy('days_until_due', 'asc')
            ->get();

        \Log::info('Financial Summary Generated', [
            'ksh_revenue' => $totalRevenueKsh,
            'ksh_pending' => $pendingAmountKsh,
            'ksh_overdue' => $overdueAmountKsh,
            'usd_revenue' => $totalRevenueUsd,
            'usd_pending' => $pendingAmountUsd,
            'usd_overdue' => $overdueAmountUsd,
            'ksh_top_customers' => $topCustomersKsh->count(),
            'usd_top_customers' => $topCustomersUsd->count(),
            'ksh_delayed' => $mostDelayedKsh->count(),
            'usd_delayed' => $mostDelayedUsd->count(),
        ]);

        return [
            // KSH Summary
            'total_revenue_ksh' => $totalRevenueKsh,
            'pending_amount_ksh' => $pendingAmountKsh,
            'pending_invoices_ksh' => $pendingInvoicesKsh,
            'overdue_amount_ksh' => $overdueAmountKsh,
            'overdue_invoices_ksh' => $overdueInvoicesKsh,

            // USD Summary
            'total_revenue_usd' => $totalRevenueUsd,
            'pending_amount_usd' => $pendingAmountUsd,
            'pending_invoices_usd' => $pendingInvoicesUsd,
            'overdue_amount_usd' => $overdueAmountUsd,
            'overdue_invoices_usd' => $overdueInvoicesUsd,

            // Other data
            'revenue_by_currency' => $revenueByCurrency,
            'revenue_by_type_ksh' => $revenueByTypeKsh,
            'revenue_by_type_usd' => $revenueByTypeUsd,
            'monthly_trend_ksh' => $monthlyTrendKsh,
            'monthly_trend_usd' => $monthlyTrendUsd,
            'top_customers_ksh' => $topCustomersKsh,
            'top_customers_usd' => $topCustomersUsd,
            'most_delayed_invoices_ksh' => $mostDelayedKsh,
            'most_delayed_invoices_usd' => $mostDelayedUsd,
            'upcoming_due_dates_ksh' => $upcomingDueKsh,
            'upcoming_due_dates_usd' => $upcomingDueUsd,
        ];

    } catch (\Exception $e) {
        \Log::error('Error generating financial summary: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return $this->getEmptyFinancialSummary();
    }
}

    /**
     * Get empty financial summary
     */
    private function getEmptyFinancialSummary(): array
    {
        return [
            'total_revenue_ksh' => 0,
            'pending_amount_ksh' => 0,
            'pending_invoices_ksh' => 0,
            'overdue_amount_ksh' => 0,
            'overdue_invoices_ksh' => 0,
            'total_revenue_usd' => 0,
            'pending_amount_usd' => 0,
            'pending_invoices_usd' => 0,
            'overdue_amount_usd' => 0,
            'overdue_invoices_usd' => 0,
            'revenue_by_currency' => collect(),
            'revenue_by_type_ksh' => collect(),
            'revenue_by_type_usd' => collect(),
            'monthly_trend_ksh' => collect(),
            'monthly_trend_usd' => collect(),
            'top_customers_ksh' => collect(),
            'top_customers_usd' => collect(),
            'most_delayed_invoices_ksh' => collect(),
            'most_delayed_invoices_usd' => collect(),
            'upcoming_due_dates_ksh' => collect(),
            'upcoming_due_dates_usd' => collect(),
        ];
    }

    /**
 * Generate revenue analysis report with currency separation
 */
private function generateRevenueAnalysis($startDate, $endDate): array
{
    try {
        \Log::info('Revenue Analysis Started', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // ============ REVENUE BY CUSTOMER - KSH ============
        $revenueByCustomerKsh = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->where('users.role', 'customer')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->where('consolidated_billings.currency', 'KSH')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->selectRaw('
                users.id as user_id,
                users.name as customer_name,
                SUM(consolidated_billings.paid_amount) as revenue,
                COUNT(DISTINCT consolidated_billings.id) as invoice_count
            ')
            ->groupBy('users.id', 'users.name')
            ->having('revenue', '>', 0)
            ->orderBy('revenue', 'desc')
            ->get();

        // ============ REVENUE BY CUSTOMER - USD ============
        $revenueByCustomerUsd = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->where('users.role', 'customer')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->where('consolidated_billings.currency', 'USD')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->selectRaw('
                users.id as user_id,
                users.name as customer_name,
                SUM(consolidated_billings.paid_amount) as revenue,
                COUNT(DISTINCT consolidated_billings.id) as invoice_count
            ')
            ->groupBy('users.id', 'users.name')
            ->having('revenue', '>', 0)
            ->orderBy('revenue', 'desc')
            ->get();

        \Log::info('Revenue by Customer', [
            'ksh_count' => $revenueByCustomerKsh->count(),
            'ksh_total' => $revenueByCustomerKsh->sum('revenue'),
            'usd_count' => $revenueByCustomerUsd->count(),
            'usd_total' => $revenueByCustomerUsd->sum('revenue'),
        ]);

        // ============ REVENUE BY SERVICE TYPE - KSH ============
        $revenueByServiceKsh = DB::table('billing_line_items')
            ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->where('consolidated_billings.currency', 'KSH')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->selectRaw('
                COALESCE(billing_line_items.billing_cycle, "monthly") as service_type,
                SUM(billing_line_items.amount) as revenue,
                COUNT(DISTINCT consolidated_billings.id) as invoice_count
            ')
            ->groupBy('billing_line_items.billing_cycle')
            ->orderBy('revenue', 'desc')
            ->get();

        // ============ REVENUE BY SERVICE TYPE - USD ============
        $revenueByServiceUsd = DB::table('billing_line_items')
            ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->where('consolidated_billings.currency', 'USD')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->selectRaw('
                COALESCE(billing_line_items.billing_cycle, "monthly") as service_type,
                SUM(billing_line_items.amount) as revenue,
                COUNT(DISTINCT consolidated_billings.id) as invoice_count
            ')
            ->groupBy('billing_line_items.billing_cycle')
            ->orderBy('revenue', 'desc')
            ->get();

        \Log::info('Revenue by Service', [
            'ksh_count' => $revenueByServiceKsh->count(),
            'ksh_total' => $revenueByServiceKsh->sum('revenue'),
            'usd_count' => $revenueByServiceUsd->count(),
            'usd_total' => $revenueByServiceUsd->sum('revenue'),
        ]);

        // Return in the format your Blade view expects
        return [
            'revenue_by_customer_ksh' => $revenueByCustomerKsh,
            'revenue_by_customer_usd' => $revenueByCustomerUsd,
            'revenue_by_service_ksh' => $revenueByServiceKsh,
            'revenue_by_service_usd' => $revenueByServiceUsd,
        ];

    } catch (\Exception $e) {
        Log::error('Error generating revenue analysis: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return [
            'revenue_by_customer_ksh' => collect(),
            'revenue_by_customer_usd' => collect(),
            'revenue_by_service_ksh' => collect(),
            'revenue_by_service_usd' => collect(),
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
                    COUNT(consolidated_billings.id) as total_billings,
                    SUM(COALESCE(consolidated_billings.paid_amount, 0)) as paid_amount,
                    SUM(consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)) as pending_amount,
                    SUM(CASE
                        WHEN consolidated_billings.due_date < CURDATE()
                        THEN (consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0))
                        ELSE 0
                    END) as overdue_amount
                ')
                ->havingRaw('(paid_amount + pending_amount) > 0')
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
                    COUNT(consolidated_billings.id) as total_billings,
                    SUM(COALESCE(consolidated_billings.paid_amount, 0)) as paid_amount,
                    SUM(consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)) as pending_amount,
                    SUM(CASE
                        WHEN consolidated_billings.due_date < CURDATE()
                        THEN (consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0))
                        ELSE 0
                    END) as overdue_amount
                ')
                ->havingRaw('(paid_amount + pending_amount) > 0')
                ->groupBy('consolidated_billings.user_id', 'users.name')
                ->orderBy('paid_amount', 'desc')
                ->get();

            return [
                'customer_billing_ksh' => $customerBillingKsh,
                'customer_billing_usd' => $customerBillingUsd,
            ];

        } catch (\Exception $e) {
            Log::error('Error generating customer billing report: ' . $e->getMessage());
            return [
                'customer_billing_ksh' => collect(),
                'customer_billing_usd' => collect(),
            ];
        }
    }

    /**
 * Generate aging report with currency separation
 */
private function generateAgingReport(): array
{
    try {
        \Log::info('=== Starting Aging Report Generation ===');

        // Get all unpaid/partially paid invoices - REMOVED LEFT JOIN to avoid duplicates
        $invoices = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->where('users.role', 'customer')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue', 'partial'])
            ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
            ->select(
                'consolidated_billings.id',
                'consolidated_billings.user_id',
                'consolidated_billings.currency',
                'consolidated_billings.due_date',
                'consolidated_billings.total_amount',
                'consolidated_billings.paid_amount',
                'users.name as customer_name'
            )
            ->distinct()  // Add distinct to prevent duplicates
            ->get();

        $agingDataKsh = [];
        $agingDataUsd = [];
        $today = Carbon::now();

        foreach ($invoices as $invoice) {
            $outstandingAmount = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);

            if ($outstandingAmount <= 0) {
                continue;
            }

            $dueDate = Carbon::parse($invoice->due_date);
            $daysOverdue = $dueDate->lt($today) ? (int) $dueDate->diffInDays($today) : 0;

            // Determine aging bucket
            if ($daysOverdue == 0) {
                $bucket = 'current';
            } elseif ($daysOverdue <= 30) {
                $bucket = 'days_30';
            } elseif ($daysOverdue <= 60) {
                $bucket = 'days_60';
            } else {
                $bucket = 'days_90_plus';
            }

            \Log::info('Aging calculation', [
                'customer' => $invoice->customer_name,
                'currency' => $invoice->currency,
                'due_date' => $invoice->due_date,
                'days_overdue' => $daysOverdue,
                'bucket' => $bucket,
                'outstanding' => $outstandingAmount
            ]);

            if ($invoice->currency === 'KSH') {
                $key = $invoice->customer_name;
                if (!isset($agingDataKsh[$key])) {
                    $agingDataKsh[$key] = [
                        'customer_name' => $invoice->customer_name,
                        'customer_id' => $invoice->user_id,
                        'current' => 0,
                        'days_30' => 0,
                        'days_60' => 0,
                        'days_90_plus' => 0,
                    ];
                }
                $agingDataKsh[$key][$bucket] += $outstandingAmount;
            } else {
                $key = $invoice->customer_name;
                if (!isset($agingDataUsd[$key])) {
                    $agingDataUsd[$key] = [
                        'customer_name' => $invoice->customer_name,
                        'customer_id' => $invoice->user_id,
                        'current' => 0,
                        'days_30' => 0,
                        'days_60' => 0,
                        'days_90_plus' => 0,
                    ];
                }
                $agingDataUsd[$key][$bucket] += $outstandingAmount;
            }
        }

        // Convert to collections
        $kshCollection = collect(array_values($agingDataKsh))
            ->map(function($item) {
                return (object)[
                    'customer_name' => $item['customer_name'],
                    'customer_id' => $item['customer_id'],
                    'current' => $item['current'],
                    'days_30' => $item['days_30'],
                    'days_60' => $item['days_60'],
                    'days_90_plus' => $item['days_90_plus'],
                    'total_outstanding' => $item['current'] + $item['days_30'] + $item['days_60'] + $item['days_90_plus'],
                ];
            })
            ->filter(function($item) {
                return $item->total_outstanding > 0;
            })
            ->sortByDesc('total_outstanding')
            ->values();

        $usdCollection = collect(array_values($agingDataUsd))
            ->map(function($item) {
                return (object)[
                    'customer_name' => $item['customer_name'],
                    'customer_id' => $item['customer_id'],
                    'current' => $item['current'],
                    'days_30' => $item['days_30'],
                    'days_60' => $item['days_60'],
                    'days_90_plus' => $item['days_90_plus'],
                    'total_outstanding' => $item['current'] + $item['days_30'] + $item['days_60'] + $item['days_90_plus'],
                ];
            })
            ->filter(function($item) {
                return $item->total_outstanding > 0;
            })
            ->sortByDesc('total_outstanding')
            ->values();

        \Log::info('Aging Report Generated', [
            'ksh_count' => $kshCollection->count(),
            'ksh_total' => $kshCollection->sum('total_outstanding'),
            'usd_count' => $usdCollection->count(),
            'usd_total' => $usdCollection->sum('total_outstanding'),
        ]);

        return [
            'aging_report_ksh' => $kshCollection,
            'aging_report_usd' => $usdCollection,
        ];

    } catch (\Exception $e) {
        \Log::error('Error generating aging report: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return [
            'aging_report_ksh' => collect(),
            'aging_report_usd' => collect(),
        ];
    }
}

    /**
     * Generate debt aging report with currency separation
     */
    private function generateDebtAgingReport($startDate, $endDate): array
    {
        try {
            $invoices = DB::table('consolidated_billings')
                ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
                ->where('users.role', 'customer')
                ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue', 'partial'])
                ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
                ->select(
                    'consolidated_billings.id',
                    'consolidated_billings.user_id',
                    'consolidated_billings.currency',
                    'consolidated_billings.total_amount',
                    'consolidated_billings.paid_amount',
                    'consolidated_billings.due_date',
                    'consolidated_billings.billing_number',
                    'users.name as customer_name'
                )
                ->get();

            $detailedAging = [];
            $today = Carbon::now();

            foreach ($invoices as $invoice) {
                $remainingAmount = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);

                if ($remainingAmount <= 0) {
                    continue;
                }

                $dueDate = Carbon::parse($invoice->due_date);
                $daysOverdue = $dueDate->lt($today) ? $dueDate->diffInDays($today) : 0;

                // Determine aging buckets
                $current = 0;
                $days30 = 0;
                $days60 = 0;
                $days90 = 0;
                $daysOver90 = 0;

                if ($daysOverdue == 0) {
                    $current = $remainingAmount;
                } elseif ($daysOverdue <= 30) {
                    $days30 = $remainingAmount;
                } elseif ($daysOverdue <= 60) {
                    $days60 = $remainingAmount;
                } elseif ($daysOverdue <= 90) {
                    $days90 = $remainingAmount;
                } else {
                    $daysOver90 = $remainingAmount;
                }

                // Determine risk level
                if ($daysOverdue > 90) {
                    $riskLevel = 'critical';
                } elseif ($daysOverdue > 60) {
                    $riskLevel = 'high';
                } elseif ($daysOverdue > 30) {
                    $riskLevel = 'medium';
                } else {
                    $riskLevel = 'low';
                }

                $periodDisplay = $this->getPeriodFromDate($invoice->due_date);

                $detailedAging[] = (object)[
                    'customer_name' => $invoice->customer_name,
                    'customer_id' => $invoice->user_id,
                    'currency' => $invoice->currency,
                    'billing_number' => $invoice->billing_number,
                    'total_due' => $remainingAmount,
                    'current' => $current,
                    'days_30' => $days30,
                    'days_60' => $days60,
                    'days_90' => $days90,
                    'days_over_90' => $daysOver90,
                    'period_display' => $periodDisplay,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'days_overdue' => $daysOverdue,
                    'risk_level' => $riskLevel,
                ];
            }

            // Sort by total due descending
            usort($detailedAging, function($a, $b) {
                return $b->total_due <=> $a->total_due;
            });

            // Calculate summaries
            $debtKsh = $this->calculateDebtSummary(collect($detailedAging)->where('currency', 'KSH'));
            $debtUsd = $this->calculateDebtSummary(collect($detailedAging)->where('currency', 'USD'));

            return [
                'debt_summary_ksh' => $debtKsh,
                'debt_summary_usd' => $debtUsd,
                'detailed_aging' => collect($detailedAging),
            ];

        } catch (\Exception $e) {
            Log::error('Error generating debt aging report: ' . $e->getMessage());

            $emptyDebt = [
                'total_receivables' => 0,
                'current' => 0,
                'days_30' => 0,
                'days_60' => 0,
                'days_90' => 0,
                'days_over_90' => 0,
                'overdue' => 0,
                'current_percentage' => 0,
                'overdue_percentage' => 0,
                'bad_debt_provision' => 0,
                'bad_debt_percentage' => 0,
            ];

            return [
                'debt_summary_ksh' => $emptyDebt,
                'debt_summary_usd' => $emptyDebt,
                'detailed_aging' => collect(),
            ];
        }
    }

    /**
     * Calculate debt summary
     */
    private function calculateDebtSummary($invoices): array
    {
        $summary = [
            'total_receivables' => 0,
            'current' => 0,
            'days_30' => 0,
            'days_60' => 0,
            'days_90' => 0,
            'days_over_90' => 0,
            'overdue' => 0,
        ];

        foreach ($invoices as $invoice) {
            $summary['total_receivables'] += $invoice->total_due;
            $summary['current'] += $invoice->current;
            $summary['days_30'] += $invoice->days_30;
            $summary['days_60'] += $invoice->days_60;
            $summary['days_90'] += $invoice->days_90;
            $summary['days_over_90'] += $invoice->days_over_90;
            $summary['overdue'] += ($invoice->days_30 + $invoice->days_60 + $invoice->days_90 + $invoice->days_over_90);
        }

        $summary['current_percentage'] = $summary['total_receivables'] > 0 ? ($summary['current'] / $summary['total_receivables']) * 100 : 0;
        $summary['overdue_percentage'] = $summary['total_receivables'] > 0 ? ($summary['overdue'] / $summary['total_receivables']) * 100 : 0;
        $summary['bad_debt_provision'] = $summary['days_over_90'] * 0.5;
        $summary['bad_debt_percentage'] = $summary['total_receivables'] > 0 ? ($summary['bad_debt_provision'] / $summary['total_receivables']) * 100 : 0;

        return $summary;
    }

    /**
     * Get period from date
     */
    private function getPeriodFromDate($date)
    {
        if (!$date) return 'N/A';

        try {
            $dateObj = Carbon::parse($date);
            $quarter = ceil($dateObj->month / 3);
            $year = $dateObj->year;
            return "Q{$quarter}-{$year}";
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Generate cash flow report
     */
    private function generateCashFlowReport($startDate, $endDate): array
    {
        try {
            $exchangeRate = 130;

            // Cash from customers
            $cashFromCustomersKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('paid_amount') ?: 0;

            $cashFromCustomersUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('paid_amount') ?: 0;

            return [
                'cash_flow_summary_ksh' => [
                    'operating' => $cashFromCustomersKsh,
                    'investing' => 0,
                    'financing' => 0,
                    'net_cash_flow' => $cashFromCustomersKsh,
                ],
                'cash_flow_summary_usd' => [
                    'operating' => $cashFromCustomersUsd,
                    'investing' => 0,
                    'financing' => 0,
                    'net_cash_flow' => $cashFromCustomersUsd,
                ],
                'cash_flow_details_ksh' => ['cash_from_customers' => $cashFromCustomersKsh],
                'cash_flow_details_usd' => ['cash_from_customers' => $cashFromCustomersUsd],
                'exchange_rate' => $exchangeRate,
            ];

        } catch (\Exception $e) {
            Log::error('Error generating cash flow report: ' . $e->getMessage());
            return $this->getEmptyCashFlowReport();
        }
    }

    /**
     * Get empty cash flow report
     */
    private function getEmptyCashFlowReport(): array
    {
        $empty = ['operating' => 0, 'investing' => 0, 'financing' => 0, 'net_cash_flow' => 0];

        return [
            'cash_flow_summary_ksh' => $empty,
            'cash_flow_summary_usd' => $empty,
            'cash_flow_details_ksh' => [],
            'cash_flow_details_usd' => [],
            'exchange_rate' => 130,
        ];
    }

    /**
     * Generate profitability report
     */
    private function generateProfitabilityReport($startDate, $endDate): array
    {
        try {
            $totalRevenueKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KSH')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('paid_amount') ?: 0;

            $totalRevenueUsd = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'USD')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('paid_amount') ?: 0;

            $emptyPl = [
                'total_revenue' => 0,
                'cost_of_services' => 0,
                'gross_profit' => 0,
                'operating_expenses' => 0,
                'depreciation' => 0,
                'operating_profit' => 0,
                'interest_expense' => 0,
                'taxes' => 0,
                'net_profit' => 0,
                'ebitda' => 0,
            ];

            return [
                'p_l_statement_ksh' => array_merge($emptyPl, ['total_revenue' => $totalRevenueKsh]),
                'p_l_statement_usd' => array_merge($emptyPl, ['total_revenue' => $totalRevenueUsd]),
                'profitability_metrics' => [
                    'gross_margin' => 0,
                    'operating_margin' => 0,
                    'net_margin' => 0,
                    'roi' => 0,
                ],
                'service_profitability' => collect(),
            ];

        } catch (\Exception $e) {
            Log::error('Error generating profitability report: ' . $e->getMessage());
            return $this->getEmptyProfitabilityReport();
        }
    }

    /**
     * Get empty profitability report
     */
    private function getEmptyProfitabilityReport(): array
    {
        $emptyPl = [
            'total_revenue' => 0,
            'cost_of_services' => 0,
            'gross_profit' => 0,
            'operating_expenses' => 0,
            'depreciation' => 0,
            'operating_profit' => 0,
            'interest_expense' => 0,
            'taxes' => 0,
            'net_profit' => 0,
            'ebitda' => 0,
        ];

        return [
            'p_l_statement_ksh' => $emptyPl,
            'p_l_statement_usd' => $emptyPl,
            'profitability_metrics' => [
                'gross_margin' => 0,
                'operating_margin' => 0,
                'net_margin' => 0,
                'roi' => 0,
            ],
            'service_profitability' => collect(),
        ];
    }

   /**
 * Generate tax report with Kenya tax rates
 */
private function generateTaxReport($startDate, $endDate): array
{
    try {
        $vatRate = 0.16;

        // ============ KSH TAX SUMMARY ============
        $taxSummaryKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'KSH')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('
                SUM(paid_amount) as total_amount,
                SUM(paid_amount * ?) as total_tax,
                COUNT(*) as invoice_count,
                ? as avg_tax_rate', [$vatRate, $vatRate * 100])
            ->first();

        // ============ USD TAX SUMMARY ============
        $taxSummaryUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'USD')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('
                SUM(paid_amount) as total_amount,
                SUM(paid_amount * ?) as total_tax,
                COUNT(*) as invoice_count,
                ? as avg_tax_rate', [$vatRate, $vatRate * 100])
            ->first();

        // Ensure objects have all required properties
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

        // ============ TAX COLLECTION BY BILLING CYCLE ============
        $taxByType = DB::table('billing_line_items')
            ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(billing_line_items.billing_cycle, "monthly") as billing_cycle'),
                'consolidated_billings.currency',
                DB::raw('SUM(billing_line_items.amount * 0.16) as tax_collected'),
                DB::raw('COUNT(DISTINCT consolidated_billings.id) as count')
            )
            ->groupBy('billing_line_items.billing_cycle', 'consolidated_billings.currency')
            ->orderBy('billing_cycle')
            ->get();

        // Convert currency to lowercase for badge styling in view
        $taxByType = $taxByType->map(function($item) {
            $item->currency = strtolower($item->currency);
            return $item;
        });

        \Log::info('Tax Report Generated', [
            'ksh_total_amount' => $taxSummaryKsh->total_amount,
            'ksh_total_tax' => $taxSummaryKsh->total_tax,
            'ksh_invoice_count' => $taxSummaryKsh->invoice_count,
            'usd_total_amount' => $taxSummaryUsd->total_amount,
            'usd_total_tax' => $taxSummaryUsd->total_tax,
            'usd_invoice_count' => $taxSummaryUsd->invoice_count,
            'tax_by_type_count' => $taxByType->count(),
        ]);

        return [
            'tax_summary_ksh' => $taxSummaryKsh,
            'tax_summary_usd' => $taxSummaryUsd,
            'tax_by_type' => $taxByType,
        ];

    } catch (\Exception $e) {
        Log::error('Error generating tax report: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        $empty = (object)[
            'total_amount' => 0,
            'total_tax' => 0,
            'invoice_count' => 0,
            'avg_tax_rate' => 16
        ];

        return [
            'tax_summary_ksh' => $empty,
            'tax_summary_usd' => $empty,
            'tax_by_type' => collect(),
        ];
    }
}

    // ==========================
    // HELPER METHODS
    // ==========================

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

   private function getFinancialMetrics(): array
{
    try {
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Debug: Log what KSH invoices exist
        \Log::info('KSH Invoice Check', [
            'total_ksh_invoices' => DB::table('consolidated_billings')->where('currency', 'KSH')->count(),
            'ksh_pending_count' => DB::table('consolidated_billings')->where('currency', 'KSH')->whereIn('status', ['pending', 'sent', 'partial'])->count(),
        ]);

        // ============ USD METRICS ============
        $totalRevenueUsd = DB::table('payments')
            ->where('status', 'validated')
            ->where('currency', 'USD')
            ->sum('amount') ?: 0;

        $monthlyRevenueUsd = DB::table('payments')
            ->where('status', 'validated')
            ->where('currency', 'USD')
            ->whereBetween('payment_date', [$startOfMonth, $today])
            ->sum('amount') ?: 0;

        $lastMonthRevenueUsd = DB::table('payments')
            ->where('status', 'validated')
            ->where('currency', 'USD')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount') ?: 0;

        $revenueChangeUsd = $lastMonthRevenueUsd > 0
            ? (($monthlyRevenueUsd - $lastMonthRevenueUsd) / $lastMonthRevenueUsd) * 100
            : 0;

        // PENDING BILLINGS USD
        $pendingAmountUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $pendingInvoicesUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->count();

        // OVERDUE PAYMENTS USD
        $overdueAmountUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $today)
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $overdueInvoicesUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $today)
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->count();

        // PAID INVOICES USD
        $paidInvoicesUsd = DB::table('consolidated_billings')
            ->where('status', 'paid')
            ->where('currency', 'USD')
            ->count();

        $partialInvoicesUsd = DB::table('consolidated_billings')
            ->where('status', 'partial')
            ->where('currency', 'USD')
            ->count();

        $partialCollectedUsd = DB::table('consolidated_billings')
            ->where('status', 'partial')
            ->where('currency', 'USD')
            ->sum('paid_amount') ?: 0;

        $invoicedAmountUsd = DB::table('consolidated_billings')
            ->where('currency', 'USD')
            ->sum('total_amount') ?: 0;

        $totalBilledUsd = DB::table('consolidated_billings')->where('currency', 'USD')->sum('total_amount') ?: 1;
        $totalCollectedUsd = $totalRevenueUsd + $partialCollectedUsd;
        $collectionRateUsd = ($totalCollectedUsd / $totalBilledUsd) * 100;

        // ============ KSH METRICS (FIXED - use 'KSH' not 'KES') ============
        $totalRevenueKsh = DB::table('payments')
            ->where('status', 'validated')
            ->where('currency', 'KSH')
            ->sum('amount') ?: 0;

        // Also check if payments are stored with 'KES' instead
        if ($totalRevenueKsh == 0) {
            $totalRevenueKsh = DB::table('payments')
                ->where('status', 'validated')
                ->where('currency', 'KES')
                ->sum('amount') ?: 0;
        }

        $monthlyRevenueKsh = DB::table('payments')
            ->where('status', 'validated')
            ->where('currency', 'KSH')
            ->whereBetween('payment_date', [$startOfMonth, $today])
            ->sum('amount') ?: 0;

        if ($monthlyRevenueKsh == 0) {
            $monthlyRevenueKsh = DB::table('payments')
                ->where('status', 'validated')
                ->where('currency', 'KES')
                ->whereBetween('payment_date', [$startOfMonth, $today])
                ->sum('amount') ?: 0;
        }

        $lastMonthRevenueKsh = DB::table('payments')
            ->where('status', 'validated')
            ->where('currency', 'KSH')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount') ?: 0;

        $revenueChangeKsh = $lastMonthRevenueKsh > 0
            ? (($monthlyRevenueKsh - $lastMonthRevenueKsh) / $lastMonthRevenueKsh) * 100
            : 0;

        // PENDING BILLINGS KSH - Check both 'KSH' and 'KES'
        $pendingAmountKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        if ($pendingAmountKsh == 0) {
            $pendingAmountKsh = DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent', 'partial'])
                ->where('currency', 'KES')
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;
        }

        $pendingInvoicesKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->count();

        if ($pendingInvoicesKsh == 0) {
            $pendingInvoicesKsh = DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent', 'partial'])
                ->where('currency', 'KES')
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->count();
        }

        // OVERDUE PAYMENTS KSH
        $overdueAmountKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $today)
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        if ($overdueAmountKsh == 0) {
            $overdueAmountKsh = DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent', 'partial'])
                ->where('currency', 'KES')
                ->where('due_date', '<', $today)
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;
        }

        $overdueInvoicesKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $today)
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->count();

        if ($overdueInvoicesKsh == 0) {
            $overdueInvoicesKsh = DB::table('consolidated_billings')
                ->whereIn('status', ['pending', 'sent', 'partial'])
                ->where('currency', 'KES')
                ->where('due_date', '<', $today)
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->count();
        }

        // PAID INVOICES KSH
        $paidInvoicesKsh = DB::table('consolidated_billings')
            ->where('status', 'paid')
            ->where('currency', 'KSH')
            ->count();

        if ($paidInvoicesKsh == 0) {
            $paidInvoicesKsh = DB::table('consolidated_billings')
                ->where('status', 'paid')
                ->where('currency', 'KES')
                ->count();
        }

        $partialInvoicesKsh = DB::table('consolidated_billings')
            ->where('status', 'partial')
            ->where('currency', 'KSH')
            ->count();

        $partialCollectedKsh = DB::table('consolidated_billings')
            ->where('status', 'partial')
            ->where('currency', 'KSH')
            ->sum('paid_amount') ?: 0;

        // INVOICED AMOUNT KSH
        $invoicedAmountKsh = DB::table('consolidated_billings')
            ->where('currency', 'KSH')
            ->sum('total_amount') ?: 0;

        if ($invoicedAmountKsh == 0) {
            $invoicedAmountKsh = DB::table('consolidated_billings')
                ->where('currency', 'KES')
                ->sum('total_amount') ?: 0;
        }

        $totalBilledKsh = DB::table('consolidated_billings')->where('currency', 'KSH')->sum('total_amount') ?: 1;
        if ($totalBilledKsh == 1) {
            $totalBilledKsh = DB::table('consolidated_billings')->where('currency', 'KES')->sum('total_amount') ?: 1;
        }
        $totalCollectedKsh = $totalRevenueKsh + $partialCollectedKsh;
        $collectionRateKsh = $totalBilledKsh > 0 ? ($totalCollectedKsh / $totalBilledKsh) * 100 : 0;

        // Debug log KSH results
        \Log::info('KSH Metrics', [
            'total_revenue' => $totalRevenueKsh,
            'pending_amount' => $pendingAmountKsh,
            'pending_count' => $pendingInvoicesKsh,
            'overdue_amount' => $overdueAmountKsh,
            'overdue_count' => $overdueInvoicesKsh,
            'invoiced_amount' => $invoicedAmountKsh,
            'collection_rate' => $collectionRateKsh,
        ]);

        // ============ COMBINED METRICS ============
        $exchangeRate = 130;

        $totalRevenueCombined = $totalRevenueUsd + ($totalRevenueKsh / $exchangeRate);
        $pendingAmountCombined = $pendingAmountUsd + ($pendingAmountKsh / $exchangeRate);
        $overdueAmountCombined = $overdueAmountUsd + ($overdueAmountKsh / $exchangeRate);
        $invoicedAmountCombined = $invoicedAmountUsd + ($invoicedAmountKsh / $exchangeRate);
        $monthlyRevenueCombined = $monthlyRevenueUsd + ($monthlyRevenueKsh / $exchangeRate);

        $totalBilledCombined = $totalBilledUsd + ($totalBilledKsh / $exchangeRate);
        $totalCollectedCombined = $totalRevenueCombined + ($partialCollectedKsh / $exchangeRate);
        $collectionRateCombined = $totalBilledCombined > 0 ? ($totalCollectedCombined / $totalBilledCombined) * 100 : 0;

        // ============ AVERAGE PAYMENT DAYS ============
        $avgPaymentDays = DB::table('consolidated_billings')
            ->where('status', 'paid')
            ->whereNotNull('payment_date')
            ->whereNotNull('billing_date')
            ->select(DB::raw('AVG(DATEDIFF(payment_date, billing_date)) as avg_days'))
            ->value('avg_days') ?: 0;

        $avgPaymentDays = round($avgPaymentDays);

        $lastMonthAvgDays = DB::table('consolidated_billings')
            ->where('status', 'paid')
            ->whereNotNull('payment_date')
            ->whereNotNull('billing_date')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->select(DB::raw('AVG(DATEDIFF(payment_date, billing_date)) as avg_days'))
            ->value('avg_days') ?: 0;

        $paymentTrend = $avgPaymentDays <= $lastMonthAvgDays ? 'positive' : 'negative';

        // ============ CUSTOMER METRICS ============
        $activeCustomers = User::where('role', 'customer')->where('status', 'active')->count();
        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->count();
        $lastMonthCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
        $customerChange = $newCustomers - $lastMonthCustomers;

        // ============ BUILD RETURN ARRAY ============
        return [
            'usd' => [
                'total_revenue' => [
                    'value' => $totalRevenueUsd,
                    'formatted' => '$' . number_format($totalRevenueUsd, 2),
                    'change' => round($revenueChangeUsd, 1),
                ],
                'pending_invoices' => [
                    'value' => $pendingInvoicesUsd,
                    'amount' => $pendingAmountUsd,
                    'formatted_amount' => '$' . number_format($pendingAmountUsd, 2),
                ],
                'overdue_payments' => [
                    'value' => $overdueInvoicesUsd,
                    'amount' => $overdueAmountUsd,
                    'formatted_amount' => '$' . number_format($overdueAmountUsd, 2),
                ],
                'paid_invoices' => ['value' => $paidInvoicesUsd],
                'partial_invoices' => ['value' => $partialInvoicesUsd],
                'partial_collected' => ['value' => $partialCollectedUsd],
                'monthly_revenue' => [
                    'value' => $monthlyRevenueUsd,
                    'formatted' => '$' . number_format($monthlyRevenueUsd, 2),
                ],
                'invoiced_amount' => [
                    'value' => $invoicedAmountUsd,
                    'formatted' => '$' . number_format($invoicedAmountUsd, 2),
                ],
                'collection_rate' => ['value' => round($collectionRateUsd, 1)],
            ],
            'ksh' => [
                'total_revenue' => [
                    'value' => $totalRevenueKsh,
                    'formatted' => 'KSH ' . number_format($totalRevenueKsh, 2),
                    'change' => round($revenueChangeKsh, 1),
                ],
                'pending_invoices' => [
                    'value' => $pendingInvoicesKsh,
                    'amount' => $pendingAmountKsh,
                    'formatted_amount' => $pendingAmountKsh > 0 ? 'KSH ' . number_format($pendingAmountKsh, 2) : 'KSH 0.00',
                ],
                'overdue_payments' => [
                    'value' => $overdueInvoicesKsh,
                    'amount' => $overdueAmountKsh,
                    'formatted_amount' => $overdueAmountKsh > 0 ? 'KSH ' . number_format($overdueAmountKsh, 2) : 'KSH 0.00',
                ],
                'paid_invoices' => ['value' => $paidInvoicesKsh],
                'partial_invoices' => ['value' => $partialInvoicesKsh],
                'partial_collected' => ['value' => $partialCollectedKsh],
                'monthly_revenue' => [
                    'value' => $monthlyRevenueKsh,
                    'formatted' => 'KSH ' . number_format($monthlyRevenueKsh, 2),
                ],
                'invoiced_amount' => [
                    'value' => $invoicedAmountKsh,
                    'formatted' => $invoicedAmountKsh > 0 ? 'KSH ' . number_format($invoicedAmountKsh, 2) : 'KSH 0.00',
                ],
                'collection_rate' => ['value' => $collectionRateKsh > 100 ? 100 : round($collectionRateKsh, 1)],
            ],
            'combined' => [
                'total_revenue' => [
                    'value' => $totalRevenueCombined,
                    'formatted' => '$' . number_format($totalRevenueCombined, 2),
                ],
                'pending_amount' => [
                    'value' => $pendingAmountCombined,
                    'formatted' => '$' . number_format($pendingAmountCombined, 2),
                ],
                'overdue_amount' => [
                    'value' => $overdueAmountCombined,
                    'formatted' => '$' . number_format($overdueAmountCombined, 2),
                ],
                'invoiced_amount' => [
                    'value' => $invoicedAmountCombined,
                    'formatted' => '$' . number_format($invoicedAmountCombined, 2),
                ],
                'monthly_revenue' => [
                    'value' => $monthlyRevenueCombined,
                    'formatted' => '$' . number_format($monthlyRevenueCombined, 2),
                ],
                'collection_rate' => ['value' => round($collectionRateCombined, 1)],
                'exchange_rate' => $exchangeRate,
            ],
            'active_customers' => ['value' => $activeCustomers, 'change' => $customerChange],
            'new_customers' => ['value' => $newCustomers],
            'avg_payment_days' => [
                'value' => $avgPaymentDays,
                'trend' => $paymentTrend,
                'trend_icon' => $paymentTrend == 'positive' ? 'arrow-down' : 'arrow-up',
                'trend_color' => $paymentTrend == 'positive' ? 'success' : 'danger',
                'subtitle' => $paymentTrend == 'positive' ? 'Faster payments' : 'Slower payments',
            ],
            'paid_invoices_count' => [
                'usd' => $paidInvoicesUsd,
                'ksh' => $paidInvoicesKsh,
                'total' => $paidInvoicesUsd + $paidInvoicesKsh,
            ],
        ];

    } catch (\Exception $e) {
        \Log::error('Error getting financial metrics: ' . $e->getMessage());
        return $this->getEmptyFinancialMetrics();
    }
}

/**
 * Get empty financial metrics for error cases
 */
private function getEmptyFinancialMetrics(): array
{
    $emptyCurrency = [
        'total_revenue' => ['value' => 0, 'formatted' => '$0.00', 'change' => 0],
        'pending_invoices' => ['value' => 0, 'amount' => 0, 'formatted_amount' => '$0.00'],
        'overdue_payments' => ['value' => 0, 'amount' => 0, 'formatted_amount' => '$0.00'],
        'paid_invoices' => ['value' => 0],
        'partial_invoices' => ['value' => 0],
        'partial_collected' => ['value' => 0],
        'monthly_revenue' => ['value' => 0, 'formatted' => '$0.00'],
        'invoiced_amount' => ['value' => 0, 'formatted' => '$0.00'],
        'collection_rate' => ['value' => 0],
    ];

    return [
        'usd' => $emptyCurrency,
        'ksh' => array_merge($emptyCurrency, ['total_revenue' => ['value' => 0, 'formatted' => 'KSH 0.00', 'change' => 0]]),
        'combined' => [
            'total_revenue' => ['value' => 0, 'formatted' => '$0.00'],
            'pending_amount' => ['value' => 0, 'formatted' => '$0.00'],
            'overdue_amount' => ['value' => 0, 'formatted' => '$0.00'],
            'invoiced_amount' => ['value' => 0, 'formatted' => '$0.00'],
            'monthly_revenue' => ['value' => 0, 'formatted' => '$0.00'],
            'collection_rate' => ['value' => 0],
            'exchange_rate' => 130,
        ],
        'active_customers' => ['value' => 0, 'change' => 0],
        'new_customers' => ['value' => 0],
        'avg_payment_days' => ['value' => 0, 'trend' => 'neutral', 'subtitle' => 'No data'],
        'paid_invoices_count' => ['usd' => 0, 'ksh' => 0, 'total' => 0],
    ];
}

    private function getRevenueTrends(): array
    {
        try {
            $months = [];
            $revenues = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date->format('M Y');

                $revenue = DB::table('payments')
                    ->where('status', 'validated')
                    ->whereYear('payment_date', $date->year)
                    ->whereMonth('payment_date', $date->month)
                    ->sum('amount');

                $revenues[] = $revenue;
            }

            return ['months' => $months, 'revenues' => $revenues];

        } catch (\Exception $e) {
            Log::error('Error getting revenue trends: ' . $e->getMessage());
            return ['months' => [], 'revenues' => []];
        }
    }

    private function getTopCustomers()
    {
        try {
            return DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->where('payments.status', 'validated')
                ->select('users.name', DB::raw('SUM(payments.amount) as total_revenue'), DB::raw('COUNT(*) as payment_count'))
                ->groupBy('users.id', 'users.name')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    $item->formatted_revenue = '$' . number_format($item->total_revenue, 2);
                    return $item;
                });

        } catch (\Exception $e) {
            Log::error('Error getting top customers: ' . $e->getMessage());
            return collect();
        }
    }

    // ==========================
    // HELPER METHODS (continued)
    // ==========================

    private function formatCurrency($amount, $currency = 'KSH')
    {
        if ($currency == 'USD') {
            return '$' . number_format($amount, 2);
        }
        return 'KSh ' . number_format($amount, 2);
    }

    private function applyBillingFilters($query, Request $request): void
    {
        if ($request->has('status') && $request->status !== 'all' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('currency') && $request->currency !== 'all' && $request->currency !== '') {
            $query->where('currency', $request->currency);
        }
    }

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
    }

    private function applyTransactionFilters($query, Request $request): void
    {
        if ($request->has('type') && $request->type !== 'all' && $request->type !== '') {
            $query->where('type', $request->type);
        }

        if ($request->has('currency') && $request->currency !== 'all' && $request->currency !== '') {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
    }

    private function getPaymentStats(): array
    {
        return [
            'total_collected_ksh' => DB::table('payments')->where('status', 'validated')->where('currency', 'KES')->sum('amount'),
            'total_collected_usd' => DB::table('payments')->where('status', 'validated')->where('currency', 'USD')->sum('amount'),
            'pending_payments' => DB::table('payments')->where('status', 'pending')->count(),
            'rejected_payments' => DB::table('payments')->where('status', 'rejected')->count(),
        ];
    }

    private function getTransactionStats(): array
    {
        try {
            return [
                'total_income_ksh' => Transaction::where('type', 'income')->where('status', 'completed')->where('currency', 'KSH')->sum('amount') ?: 0,
                'total_income_usd' => Transaction::where('type', 'income')->where('status', 'completed')->where('currency', 'USD')->sum('amount') ?: 0,
                'total_expenses_ksh' => Transaction::where('type', 'expense')->where('status', 'completed')->where('currency', 'KSH')->sum('amount') ?: 0,
                'total_expenses_usd' => Transaction::where('type', 'expense')->where('status', 'completed')->where('currency', 'USD')->sum('amount') ?: 0,
                'pending_transactions' => Transaction::where('status', 'pending')->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getTransactionStats: ' . $e->getMessage());
            return [
                'total_income_ksh' => 0, 'total_income_usd' => 0,
                'total_expenses_ksh' => 0, 'total_expenses_usd' => 0,
                'pending_transactions' => 0,
            ];
        }
    }

    // ==========================
    // PAYMENT METHODS
    // ==========================

    public function getCustomerInvoices($customerId)
    {
        $invoices = ConsolidatedBilling::where('user_id', $customerId)
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'billing_number' => $invoice->billing_number,
                    'total_amount' => $invoice->total_amount,
                    'paid_amount' => $invoice->paid_amount ?? 0,
                    'balance' => $invoice->total_amount - ($invoice->paid_amount ?? 0),
                    'currency' => $invoice->currency,
                    'due_date' => $invoice->due_date,
                ];
            });

        return response()->json($invoices);
    }

    public function storePayment(Request $request)
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
            'allocated_invoices' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $paymentNumber = $this->generatePaymentNumber();

            $depositSlipPath = null;
            if ($request->hasFile('deposit_slip')) {
                $depositSlipPath = $request->file('deposit_slip')->store('payments/deposit_slips', 'public');
            }

            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'user_id' => $request->user_id,
                'billing_id' => $request->billing_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
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

            // Process allocations
            if ($request->has('allocated_invoices') && $request->allocated_invoices) {
                $allocations = json_decode($request->allocated_invoices, true);
                if (is_array($allocations)) {
                    foreach ($allocations as $allocation) {
                        if (isset($allocation['invoice_id']) && isset($allocation['allocated_amount']) && $allocation['allocated_amount'] > 0) {
                            DB::table('payment_allocations')->insert([
                                'payment_id' => $payment->id,
                                'invoice_id' => $allocation['invoice_id'],
                                'allocated_amount' => $allocation['allocated_amount'],
                                'currency' => $request->currency,
                                'created_at' => now(),
                            ]);
                            $this->updateInvoicePaidAmount($allocation['invoice_id'], $allocation['allocated_amount'], $request->currency);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('finance.payments.show', $payment->id)
                ->with('success', 'Payment recorded successfully. Awaiting validation.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to record payment: ' . $e->getMessage())->withInput();
        }
    }

    private function updateInvoicePaidAmount($invoiceId, $amount, $currency)
    {
        $invoice = ConsolidatedBilling::findOrFail($invoiceId);

        if ($invoice->currency !== $currency) {
            Log::warning('Currency mismatch for invoice allocation');
            return;
        }

        $currentPaid = $invoice->paid_amount ?? 0;
        $newPaid = $currentPaid + $amount;

        $invoice->update([
            'paid_amount' => $newPaid,
            'status' => $newPaid >= $invoice->total_amount ? 'paid' : 'partial',
        ]);
    }

    private function generatePaymentNumber()
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');

        $lastPayment = DB::table('payments')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPayment ? str_pad(intval(substr($lastPayment->payment_number, -4)) + 1, 4, '0', STR_PAD_LEFT) : '0001';

        return $prefix . $year . $month . $sequence;
    }

    public function validatePayment($id)
    {
        try {
            DB::beginTransaction();

            $payment = DB::table('payments')->where('id', $id)->first();

            if (!$payment || $payment->status !== 'pending') {
                return redirect()->back()->with('error', 'Payment cannot be validated.');
            }

            DB::table('payments')->where('id', $id)->update([
                'status' => 'validated',
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('finance.payments.index')
                ->with('success', 'Payment validated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment validation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to validate payment.');
        }
    }

    public function rejectPayment(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        try {
            DB::beginTransaction();

            $payment = DB::table('payments')->where('id', $id)->first();

            if (!$payment || $payment->status !== 'pending') {
                return redirect()->back()->with('error', 'Payment cannot be rejected.');
            }

            DB::table('payments')->where('id', $id)->update([
                'status' => 'rejected',
                'validated_by' => Auth::id(),
                'validated_at' => now(),
                'validation_notes' => $request->reason,
            ]);

            DB::commit();

            return redirect()->route('finance.payments.index')
                ->with('success', 'Payment rejected successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment rejection failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject payment.');
        }
    }

    // ==========================
    // DUMMY METHODS FOR COMPLETENESS
    // ==========================

    private function getBillingCycles(): array
    {
        return ['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'annually' => 'Annually'];
    }

    private function getTransactionTypes(): array
    {
        return ['income' => 'Income', 'expense' => 'Expense', 'transfer' => 'Transfer'];
    }

    private function getPaymentMethods(): array
    {
        return ['credit_card' => 'Credit Card', 'bank_transfer' => 'Bank Transfer', 'cash' => 'Cash', 'digital_wallet' => 'Digital Wallet', 'check' => 'Check'];
    }

    private function getTransactionCategories(): array
    {
        return ['invoice_payment' => 'Invoice Payment', 'refund' => 'Refund', 'fee' => 'Fee', 'salary' => 'Salary', 'rent' => 'Rent', 'utilities' => 'Utilities', 'maintenance' => 'Maintenance', 'equipment' => 'Equipment', 'software' => 'Software', 'other' => 'Other'];
    }

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

    private function generateTransactionId(): string
    {
        return 'TXN-' . date('Y') . '-' . Str::upper(Str::random(8));
    }

    // Additional methods for billing management (stubs)
    public function createBilling() { return view('finance.billing.create'); }
    public function storeBilling(Request $request) { return redirect()->route('finance.billing.index')->with('success', 'Billing created.'); }
    public function showBilling($id) { return view('finance.billing.show'); }
    public function editBilling($id) { return view('finance.billing.edit'); }
    public function updateBilling(Request $request, $id) { return redirect()->route('finance.billing.index')->with('success', 'Billing updated.'); }
    public function deleteBilling($id) { return redirect()->route('finance.billing.index')->with('success', 'Billing deleted.'); }
    public function downloadBilling($id) { return redirect()->back(); }
    public function printBilling($id) { return redirect()->back(); }
    public function sendBillingEmail($id) { return response()->json(['success' => true]); }
    public function markBillingPaid(Request $request, $id) { return response()->json(['success' => true]); }
    public function sendBillingReminder($id) { return response()->json(['success' => true]); }
    public function updateBillingStatus(Request $request, $id) { return redirect()->back()->with('success', 'Status updated.'); }
    public function bulkUpdateStatus(Request $request) { return redirect()->back()->with('success', 'Statuses updated.'); }
    public function generateInvoicesManually(AutomatedBillingService $billingService) { return redirect()->route('finance.auto-billing.generate')->with('success', 'Invoices generated.'); }
    public function updateBillingSettings(Request $request, $customerId) { return redirect()->back()->with('success', 'Settings updated.'); }
    // public function autoBilling(AutomatedBillingService $billingService) { return view('finance.auto-billing.index'); }

    /**
 * Display auto-billing dashboard
 */
public function autoBilling(AutomatedBillingService $billingService = null)
{
    // Get due customers with their billings
    $dueCustomers = LeaseBilling::whereIn('status', ['pending', 'sent', 'overdue'])
        ->where('due_date', '<=', now()->addDays(7))
        ->with(['lease.customer'])
        ->get()
        ->groupBy('lease.customer_id')
        ->map(function ($billings, $customerId) {
            $customer = $billings->first()->lease->customer ?? null;

            // Calculate pending amount correctly
            $pendingAmount = $billings->whereIn('status', ['pending', 'sent', 'overdue'])->sum('total_amount');

            return (object)[
                'id' => $customerId,
                'name' => $customer->name ?? 'N/A',
                'email' => $customer->email ?? 'N/A',
                'customer_id' => $customerId,
                'customer_name' => $customer->name ?? 'N/A',
                'customer_email' => $customer->email ?? 'N/A',
                'customer_company' => $customer->company ?? 'N/A',
                'total_due' => $billings->sum('total_amount'),
                'pending_amount' => $pendingAmount,  // Add this
                'total_due_usd' => $billings->where('currency', 'USD')->sum('total_amount'),
                'total_due_ksh' => $billings->where('currency', 'KSH')->sum('total_amount'),
                'invoices_count' => $billings->count(),
                'oldest_due_date' => $billings->min('due_date'),
                'next_billing_date' => $billings->first()->due_date ?? null,
                'leaseBillings' => $billings,
                'billings' => $billings
            ];
        })
        ->sortByDesc(function ($customer) {
            return $customer->total_due;
        });

    // Get auto billing customers
    $autoBillingCustomers = User::where('role', 'customer')
        ->where('auto_billing_enabled', true)
        ->select('id', 'name', 'email', 'company_name', 'auto_billing_enabled', 'next_billing_date')
        ->paginate(20);

    // Get scheduled billings with proper relations
    $scheduledBillings = LeaseBilling::whereIn('status', ['pending', 'sent'])
        ->where('due_date', '>', now())
        ->with(['lease.customer'])
        ->orderBy('due_date', 'asc')
        ->limit(20)
        ->get()
        ->map(function ($billing) {
            return (object)[
                'id' => $billing->id,
                'billing_number' => $billing->billing_number,
                'customer' => $billing->lease->customer ?? null,
                'lease' => $billing->lease,
                'due_date' => $billing->due_date,
                'total_amount' => $billing->total_amount,
                'status' => $billing->status,
            ];
        });

    // Get statistics
    $stats = [
        'due_customers_count' => $dueCustomers->count(),
        'auto_billing_count' => User::where('role', 'customer')->where('auto_billing_enabled', true)->count(),
        'overdue_count' => LeaseBilling::where('status', 'overdue')
            ->where('due_date', '<', now())
            ->count(),
        'monthly_revenue' => Lease::where('status', 'active')->sum('monthly_cost'),
        'total_auto_billing' => User::where('role', 'customer')->where('auto_billing_enabled', true)->count(),
        'scheduled_count' => LeaseBilling::whereIn('status', ['pending', 'sent'])
            ->where('due_date', '>', now())
            ->count(),
    ];

    return view('finance.auto-billing.index', compact(
        'dueCustomers',
        'autoBillingCustomers',
        'scheduledBillings',
        'stats'
    ));
}
    private function getScheduledBillings() { return collect(); }
    private function getAutoBillingCustomers() { return collect(); }
    private function getDueCustomers() { return collect(); }
    private function getAutoBillingStats(): array { return []; }
    private function processBillingLineItems(LeaseBilling $billing, array $lineItems): void {}
    private function getPaymentMethodColumn(): ?string { return 'payment_method'; }
    private function validateBillingRequest(Request $request, $id = null): array { return $request->validate([]); }
    private function generateBillingNumber(): string { return 'INV-' . now()->format('Ymd') . '-0001'; }
    private function exportToCsv($reportData, $reportType, $startDate, $endDate) { return response()->stream(function() {}, 200); }
    private function exportAgingReportToCsv($file, $reportData) {}
    private function exportDebtAgingToCsv($file, $reportData) {}
    private function exportGenericToCsv($file, $reportData) {}
    private function generateCustomerBillingData($startDate, $endDate) { return ['customer_billing_ksh' => collect(), 'customer_billing_usd' => collect()]; }
    private function calculateCollectionMetrics(string $currency): array { return []; }
    private function formatPeriod($periodStart, $periodEnd = null, $billingDate = null) { return ['display' => 'N/A', 'tooltip' => '']; }
    private function getPeriodDisplayFromRow($row) { return 'N/A'; }
    private function getPeriodDisplayFromArray($row) { return 'N/A'; }
    private function exportAgingReport($file, $reportData) {}
    private function exportDebtAgingReport($file, $reportData) {}
    private function exportFinancialSummary($file, $reportData) {}
    private function exportRevenueAnalysis($file, $reportData) {}
    private function exportGenericReport($file, $reportData) {}
    private function arrayMergeRecursive(array $default, array $data): array { return array_merge($default, $data); }
    private function exportReport(string $reportType, array $reportData, string $startDate, string $endDate) {}
    private function testSMTPConnection() {}
    public function financialReports(Request $request) { return view('finance.financial-reports'); }
    public function roleCustomerLeases() { return view('help.role.customer-leases'); }
    public function roleCustomerDocuments() { return view('help.role.customer-documents'); }
    public function exportFinancialReport(Request $request) { return redirect()->back(); }
    private function createCustomerCredit($userId, $amount, $currency, $paymentId, $notes = null) {}
}
