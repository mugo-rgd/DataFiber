<?php

namespace App\Http\Controllers;

use App\Models\BillingLineItem;
use App\Models\ConsolidatedBilling;
use App\Models\LeaseBilling;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Lease;
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
    $export = $request->get('export'); // Check for export parameter

    list($startDate, $endDate) = $this->getDateRange($period, $request->start_date, $request->end_date);

    try {
        $reportData = $this->generateReport($reportType, $startDate, $endDate);

        // Ensure aging report data is properly structured
        if ($reportType === 'aging_report') {
            if (!isset($reportData['aging_report_ksh'])) {
                $reportData['aging_report_ksh'] = collect();
            }
            if (!isset($reportData['aging_report_usd'])) {
                $reportData['aging_report_usd'] = collect();
            }

            \Log::info('Aging Report Data', [
                'ksh_count' => $reportData['aging_report_ksh']->count(),
                'usd_count' => $reportData['aging_report_usd']->count(),
            ]);
        }

        $reportData['report_type'] = $reportType;
        $reportData['start_date'] = $startDate;
        $reportData['end_date'] = $endDate;

        // Handle export if requested
        if ($export === 'csv') {
            return $this->exportToCsv($reportData, $reportType, $startDate, $endDate);
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

/**
 * Export report data to CSV
 */
private function exportToCsv($reportData, $reportType, $startDate, $endDate)
{
    $filename = "financial_report_{$reportType}_{$startDate}_to_{$endDate}.csv";

    $callback = function() use ($reportData, $reportType) {
        $file = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fwrite($file, "\xEF\xBB\xBF");

        // Header information
        fputcsv($file, ['Financial Report - ' . strtoupper(str_replace('_', ' ', $reportType))]);
        fputcsv($file, ['Generated: ' . now()->toDateTimeString()]);
        fputcsv($file, ['Period: ' . ($reportData['start_date'] ?? 'N/A') . ' to ' . ($reportData['end_date'] ?? 'N/A')]);
        fputcsv($file, []);

        // Export based on report type
        switch($reportType) {
            case 'aging_report':
                $this->exportAgingReportToCsv($file, $reportData);
                break;
            case 'debt_aging':
                $this->exportDebtAgingToCsv($file, $reportData);
                break;
            default:
                $this->exportGenericToCsv($file, $reportData);
                break;
        }

        fclose($file);
    };

    return response()->stream($callback, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}

/**
 * Export Aging Report to CSV
 */
private function exportAgingReportToCsv($file, $reportData)
{
    // Debug: Log what we have
    \Log::info('Exporting Aging Report - Data keys: ' . json_encode(array_keys($reportData)));

    // KSH Aging
    fputcsv($file, ['ACCOUNTS RECEIVABLE AGING REPORT - KSH']);
    fputcsv($file, ['Customer', 'Current (KSH)', '1-30 Days (KSH)', '31-60 Days (KSH)', '61-90+ Days (KSH)', 'Total Outstanding (KSH)']);

    $kshData = $reportData['aging_report_ksh'] ?? collect();
    \Log::info('KSH Data count for export: ' . $kshData->count());

    if ($kshData->count() > 0) {
        foreach ($kshData as $row) {
            // Handle both object and array formats
            if (is_object($row)) {
                $customerName = $row->customer_name ?? 'Unknown';
                $current = $row->current ?? 0;
                $days30 = $row->days_30 ?? 0;
                $days60 = $row->days_60 ?? 0;
                $days90Plus = $row->days_90_plus ?? 0;
            } else {
                $customerName = $row['customer_name'] ?? 'Unknown';
                $current = $row['current'] ?? 0;
                $days30 = $row['days_30'] ?? 0;
                $days60 = $row['days_60'] ?? 0;
                $days90Plus = $row['days_90_plus'] ?? 0;
            }

            $total = $current + $days30 + $days60 + $days90Plus;

            fputcsv($file, [
                $customerName,
                number_format($current, 2),
                number_format($days30, 2),
                number_format($days60, 2),
                number_format($days90Plus, 2),
                number_format($total, 2)
            ]);
        }
    } else {
        fputcsv($file, ['No KSH aging data available for the selected period.', '', '', '', '', '']);
    }

    fputcsv($file, []);
    fputcsv($file, []);

    // USD Aging
    fputcsv($file, ['ACCOUNTS RECEIVABLE AGING REPORT - USD']);
    fputcsv($file, ['Customer', 'Current (USD)', '1-30 Days (USD)', '31-60 Days (USD)', '61-90+ Days (USD)', 'Total Outstanding (USD)']);

    $usdData = $reportData['aging_report_usd'] ?? collect();
    \Log::info('USD Data count for export: ' . $usdData->count());

    if ($usdData->count() > 0) {
        foreach ($usdData as $row) {
            // Handle both object and array formats
            if (is_object($row)) {
                $customerName = $row->customer_name ?? 'Unknown';
                $current = $row->current ?? 0;
                $days30 = $row->days_30 ?? 0;
                $days60 = $row->days_60 ?? 0;
                $days90Plus = $row->days_90_plus ?? 0;
            } else {
                $customerName = $row['customer_name'] ?? 'Unknown';
                $current = $row['current'] ?? 0;
                $days30 = $row['days_30'] ?? 0;
                $days60 = $row['days_60'] ?? 0;
                $days90Plus = $row['days_90_plus'] ?? 0;
            }

            $total = $current + $days30 + $days60 + $days90Plus;

            fputcsv($file, [
                $customerName,
                number_format($current, 2),
                number_format($days30, 2),
                number_format($days60, 2),
                number_format($days90Plus, 2),
                number_format($total, 2)
            ]);
        }
    } else {
        fputcsv($file, ['No USD aging data available for the selected period.', '', '', '', '', '']);
    }
}

/**
 * Export Debt Aging Report to CSV
 */
private function exportDebtAgingToCsv($file, $reportData)
{
    fputcsv($file, ['DETAILED DEBT AGING ANALYSIS']);
    fputcsv($file, ['Customer', 'Currency', 'Total Due', 'Current', '1-30 Days', '31-60 Days', '61-90 Days', '>90 Days', 'Risk Level']);

    $detailedData = $reportData['detailed_aging'] ?? collect();

    if ($detailedData->count() > 0) {
        foreach ($detailedData as $row) {
            if (is_object($row)) {
                $currency = $row->currency ?? 'USD';
                $currencySymbol = $currency == 'KSH' ? 'KSH' : '$';
                $customerName = $row->customer_name ?? 'Unknown';
                $totalDue = $row->total_due ?? 0;
                $current = $row->current ?? 0;
                $days30 = $row->days_30 ?? 0;
                $days60 = $row->days_60 ?? 0;
                $days90 = $row->days_90 ?? 0;
                $daysOver90 = $row->days_over_90 ?? 0;
                $riskLevel = $row->risk_level ?? 'low';
            } else {
                $currency = $row['currency'] ?? 'USD';
                $currencySymbol = $currency == 'KSH' ? 'KSH' : '$';
                $customerName = $row['customer_name'] ?? 'Unknown';
                $totalDue = $row['total_due'] ?? 0;
                $current = $row['current'] ?? 0;
                $days30 = $row['days_30'] ?? 0;
                $days60 = $row['days_60'] ?? 0;
                $days90 = $row['days_90'] ?? 0;
                $daysOver90 = $row['days_over_90'] ?? 0;
                $riskLevel = $row['risk_level'] ?? 'low';
            }

            fputcsv($file, [
                $customerName,
                $currency,
                $currencySymbol . ' ' . number_format($totalDue, 2),
                $currencySymbol . ' ' . number_format($current, 2),
                $currencySymbol . ' ' . number_format($days30, 2),
                $currencySymbol . ' ' . number_format($days60, 2),
                $currencySymbol . ' ' . number_format($days90, 2),
                $currencySymbol . ' ' . number_format($daysOver90, 2),
                ucfirst($riskLevel)
            ]);
        }
    } else {
        fputcsv($file, ['No debt aging data available for the selected period.', '', '', '', '', '', '', '', '']);
    }
}

/**
 * Export Generic Report to CSV (fallback)
 */
private function exportGenericToCsv($file, $reportData)
{
    fputcsv($file, ['Report Data Export']);
    fputcsv($file, ['Key', 'Value']);

    foreach ($reportData as $key => $value) {
        if (is_scalar($value)) {
            fputcsv($file, [$key, $value]);
        } elseif ($value instanceof \Illuminate\Support\Collection) {
            fputcsv($file, [$key, 'Collection with ' . $value->count() . ' items']);
        } else {
            fputcsv($file, [$key, gettype($value)]);
        }
    }
}

/**
 * Export Aging Report
 */
private function exportAgingReport($file, $reportData)
{
    // KSH Aging
    fputcsv($file, ['ACCOUNTS RECEIVABLE AGING REPORT - KSH']);
    fputcsv($file, ['Customer', 'Current (KSH)', '1-30 Days (KSH)', '31-60 Days (KSH)', '61-90+ Days (KSH)', 'Total Outstanding (KSH)']);

    foreach (($reportData['aging_report_ksh'] ?? []) as $row) {
        fputcsv($file, [
            $row->customer_name ?? 'Unknown',
            number_format($row->current ?? 0, 2),
            number_format($row->days_30 ?? 0, 2),
            number_format($row->days_60 ?? 0, 2),
            number_format($row->days_90_plus ?? 0, 2),
            number_format(($row->current ?? 0) + ($row->days_30 ?? 0) + ($row->days_60 ?? 0) + ($row->days_90_plus ?? 0), 2)
        ]);
    }

    fputcsv($file, []);
    fputcsv($file, []);

    // USD Aging
    fputcsv($file, ['ACCOUNTS RECEIVABLE AGING REPORT - USD']);
    fputcsv($file, ['Customer', 'Current (USD)', '1-30 Days (USD)', '31-60 Days (USD)', '61-90+ Days (USD)', 'Total Outstanding (USD)']);

    foreach (($reportData['aging_report_usd'] ?? []) as $row) {
        fputcsv($file, [
            $row->customer_name ?? 'Unknown',
            number_format($row->current ?? 0, 2),
            number_format($row->days_30 ?? 0, 2),
            number_format($row->days_60 ?? 0, 2),
            number_format($row->days_90_plus ?? 0, 2),
            number_format(($row->current ?? 0) + ($row->days_30 ?? 0) + ($row->days_60 ?? 0) + ($row->days_90_plus ?? 0), 2)
        ]);
    }
}

/**
 * Export Debt Aging Report
 */
private function exportDebtAgingReport($file, $reportData)
{
    fputcsv($file, ['DETAILED DEBT AGING ANALYSIS']);
    fputcsv($file, ['Customer', 'Currency', 'Total Due', 'Current', '1-30 Days', '31-60 Days', '61-90 Days', '>90 Days', 'Risk Level']);

    foreach (($reportData['detailed_aging'] ?? []) as $row) {
        $currencySymbol = ($row->currency ?? 'USD') == 'KSH' ? 'KSH' : '$';
        fputcsv($file, [
            $row->customer_name ?? 'Unknown',
            $row->currency ?? 'USD',
            $currencySymbol . ' ' . number_format($row->total_due ?? 0, 2),
            $currencySymbol . ' ' . number_format($row->current ?? 0, 2),
            $currencySymbol . ' ' . number_format($row->days_30 ?? 0, 2),
            $currencySymbol . ' ' . number_format($row->days_60 ?? 0, 2),
            $currencySymbol . ' ' . number_format($row->days_90 ?? 0, 2),
            $currencySymbol . ' ' . number_format($row->days_over_90 ?? 0, 2),
            ucfirst($row->risk_level ?? 'low')
        ]);
    }
}

/**
 * Export Financial Summary
 */
private function exportFinancialSummary($file, $reportData)
{
    fputcsv($file, ['FINANCIAL SUMMARY - KSH']);
    fputcsv($file, ['Metric', 'Amount (KSH)']);
    fputcsv($file, ['Total Revenue', number_format($reportData['total_revenue_ksh'] ?? 0, 2)]);
    fputcsv($file, ['Pending Invoices', $reportData['pending_invoices_ksh'] ?? 0]);
    fputcsv($file, ['Pending Amount', number_format($reportData['pending_amount_ksh'] ?? 0, 2)]);
    fputcsv($file, ['Overdue Invoices', $reportData['overdue_invoices_ksh'] ?? 0]);
    fputcsv($file, ['Overdue Amount', number_format($reportData['overdue_amount_ksh'] ?? 0, 2)]);

    fputcsv($file, []);
    fputcsv($file, ['FINANCIAL SUMMARY - USD']);
    fputcsv($file, ['Metric', 'Amount (USD)']);
    fputcsv($file, ['Total Revenue', number_format($reportData['total_revenue_usd'] ?? 0, 2)]);
    fputcsv($file, ['Pending Invoices', $reportData['pending_invoices_usd'] ?? 0]);
    fputcsv($file, ['Pending Amount', number_format($reportData['pending_amount_usd'] ?? 0, 2)]);
    fputcsv($file, ['Overdue Invoices', $reportData['overdue_invoices_usd'] ?? 0]);
    fputcsv($file, ['Overdue Amount', number_format($reportData['overdue_amount_usd'] ?? 0, 2)]);
}

/**
 * Export Revenue Analysis
 */
private function exportRevenueAnalysis($file, $reportData)
{
    fputcsv($file, ['TOP CUSTOMERS BY REVENUE - KSH']);
    fputcsv($file, ['Customer', 'Revenue (KSH)', 'Invoice Count']);

    foreach (($reportData['revenue_by_customer_ksh'] ?? []) as $row) {
        fputcsv($file, [
            $row->customer_name ?? 'Unknown',
            number_format($row->revenue ?? 0, 2),
            $row->invoice_count ?? 0
        ]);
    }

    fputcsv($file, []);
    fputcsv($file, ['TOP CUSTOMERS BY REVENUE - USD']);
    fputcsv($file, ['Customer', 'Revenue (USD)', 'Invoice Count']);

    foreach (($reportData['revenue_by_customer_usd'] ?? []) as $row) {
        fputcsv($file, [
            $row->customer_name ?? 'Unknown',
            number_format($row->revenue ?? 0, 2),
            $row->invoice_count ?? 0
        ]);
    }
}

/**
 * Export Generic Report (fallback)
 */
private function exportGenericReport($file, $reportData)
{
    fputcsv($file, ['Report Data']);
    foreach ($reportData as $key => $value) {
        if (is_scalar($value)) {
            fputcsv($file, [$key, $value]);
        }
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
   /**
 * Get financial metrics for dashboard
 */
private function getFinancialMetrics(): array
{
    try {
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // ============ USD METRICS ============

        // Total Revenue USD
        $totalRevenueUsd = ConsolidatedBilling::where('status', 'paid')
            ->where('currency', 'USD')
            ->sum('paid_amount');

        // Monthly Revenue USD
        $monthlyRevenueUsd = ConsolidatedBilling::where('status', 'paid')
            ->where('currency', 'USD')
            ->whereBetween('payment_date', [$startOfMonth, $today])
            ->sum('paid_amount');

        // Previous Month Revenue USD
        $lastMonthRevenueUsd = ConsolidatedBilling::where('status', 'paid')
            ->where('currency', 'USD')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('paid_amount');

        // Revenue change percentage USD
        $revenueChangeUsd = $lastMonthRevenueUsd > 0
            ? (($monthlyRevenueUsd - $lastMonthRevenueUsd) / $lastMonthRevenueUsd) * 100
            : 0;

        // Pending Invoices USD
        $pendingInvoicesUsd = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('total_amount', '>', DB::raw('COALESCE(paid_amount, 0)'))
            ->count();

        $pendingAmountUsd = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('total_amount', '>', DB::raw('COALESCE(paid_amount, 0)'))
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)'));

        // Overdue Payments USD
        $overduePaymentsUsd = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $today)
            ->where('total_amount', '>', DB::raw('COALESCE(paid_amount, 0)'))
            ->count();

        $overdueAmountUsd = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $today)
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)'));

        // Paid Invoices USD
        $paidInvoicesUsd = ConsolidatedBilling::where('status', 'paid')
            ->where('currency', 'USD')
            ->count();

        // Invoiced Amount USD
        $invoicedAmountUsd = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'paid', 'partial'])
            ->where('currency', 'USD')
            ->sum('total_amount');

        // ============ KSH METRICS ============

        // Total Revenue KSH
        $totalRevenueKsh = ConsolidatedBilling::where('status', 'paid')
            ->where('currency', 'KSH')
            ->sum('paid_amount');

        // Monthly Revenue KSH
        $monthlyRevenueKsh = ConsolidatedBilling::where('status', 'paid')
            ->where('currency', 'KSH')
            ->whereBetween('payment_date', [$startOfMonth, $today])
            ->sum('paid_amount');

        // Previous Month Revenue KSH
        $lastMonthRevenueKsh = ConsolidatedBilling::where('status', 'paid')
            ->where('currency', 'KSH')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('paid_amount');

        // Revenue change percentage KSH
        $revenueChangeKsh = $lastMonthRevenueKsh > 0
            ? (($monthlyRevenueKsh - $lastMonthRevenueKsh) / $lastMonthRevenueKsh) * 100
            : 0;

        // Pending Invoices KSH
        $pendingInvoicesKsh = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('total_amount', '>', DB::raw('COALESCE(paid_amount, 0)'))
            ->count();

        $pendingAmountKsh = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('total_amount', '>', DB::raw('COALESCE(paid_amount, 0)'))
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)'));

        // Overdue Payments KSH
        $overduePaymentsKsh = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $today)
            ->where('total_amount', '>', DB::raw('COALESCE(paid_amount, 0)'))
            ->count();

        $overdueAmountKsh = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $today)
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)'));

        // Paid Invoices KSH
        $paidInvoicesKsh = ConsolidatedBilling::where('status', 'paid')
            ->where('currency', 'KSH')
            ->count();

        // Invoiced Amount KSH
        $invoicedAmountKsh = ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'paid', 'partial'])
            ->where('currency', 'KSH')
            ->sum('total_amount');

        // ============ COMBINED METRICS (USD Equivalent for comparison) ============

        // Exchange rate (you can make this dynamic)
        $exchangeRate = 130; // 1 USD = 130 KSH

        $totalRevenueCombined = $totalRevenueUsd + ($totalRevenueKsh / $exchangeRate);
        $pendingAmountCombined = $pendingAmountUsd + ($pendingAmountKsh / $exchangeRate);
        $overdueAmountCombined = $overdueAmountUsd + ($overdueAmountKsh / $exchangeRate);
        $invoicedAmountCombined = $invoicedAmountUsd + ($invoicedAmountKsh / $exchangeRate);
        $monthlyRevenueCombined = $monthlyRevenueUsd + ($monthlyRevenueKsh / $exchangeRate);

        // ============ CUSTOMER METRICS ============

        // Active Customers
        $activeCustomers = User::where('role', 'customer')
            ->where('status', 'active')
            ->count();

        // New Customers this month
        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->count();

        // Customer change
        $lastMonthCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();

        $customerChange = $lastMonthCustomers > 0
            ? $newCustomers - $lastMonthCustomers
            : $newCustomers;

        // ============ COLLECTION METRICS ============

        // Collection Rate USD
        $totalBilledUsd = ConsolidatedBilling::where('currency', 'USD')->sum('total_amount');
        $totalCollectedUsd = ConsolidatedBilling::where('currency', 'USD')->where('status', 'paid')->sum('paid_amount');
        $collectionRateUsd = $totalBilledUsd > 0 ? ($totalCollectedUsd / $totalBilledUsd) * 100 : 0;

        // Collection Rate KSH
        $totalBilledKsh = ConsolidatedBilling::where('currency', 'KSH')->sum('total_amount');
        $totalCollectedKsh = ConsolidatedBilling::where('currency', 'KSH')->where('status', 'paid')->sum('paid_amount');
        $collectionRateKsh = $totalBilledKsh > 0 ? ($totalCollectedKsh / $totalBilledKsh) * 100 : 0;

        // Overall Collection Rate
        $totalBilledCombined = $totalBilledUsd + ($totalBilledKsh / $exchangeRate);
        $totalCollectedCombined = $totalCollectedUsd + ($totalCollectedKsh / $exchangeRate);
        $collectionRateCombined = $totalBilledCombined > 0 ? ($totalCollectedCombined / $totalBilledCombined) * 100 : 0;

        // Average Payment Days
        $paidInvoicesWithDates = ConsolidatedBilling::where('status', 'paid')
            ->whereNotNull('payment_date')
            ->whereNotNull('billing_date')
            ->get();

        $totalDays = 0;
        $countWithDates = 0;
        foreach ($paidInvoicesWithDates as $invoice) {
            $billingDate = Carbon::parse($invoice->billing_date);
            $paymentDate = Carbon::parse($invoice->payment_date);
            $daysDiff = $billingDate->diffInDays($paymentDate);
            $totalDays += $daysDiff;
            $countWithDates++;
        }
        $avgPaymentDays = $countWithDates > 0 ? round($totalDays / $countWithDates) : 0;

        // Payment trend
        $lastMonthPaidInvoices = ConsolidatedBilling::where('status', 'paid')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->get();

        $lastMonthTotalDays = 0;
        $lastMonthCount = 0;
        foreach ($lastMonthPaidInvoices as $invoice) {
            if ($invoice->billing_date && $invoice->payment_date) {
                $lastMonthTotalDays += Carbon::parse($invoice->billing_date)->diffInDays(Carbon::parse($invoice->payment_date));
                $lastMonthCount++;
            }
        }
        $lastMonthAvgDays = $lastMonthCount > 0 ? round($lastMonthTotalDays / $lastMonthCount) : 0;

        $paymentTrend = $avgPaymentDays <= $lastMonthAvgDays ? 'positive' : 'negative';
        $trendIcon = $avgPaymentDays <= $lastMonthAvgDays ? 'arrow-down' : 'arrow-up';
        $trendColor = $avgPaymentDays <= $lastMonthAvgDays ? 'success' : 'danger';

        // ============ BUILD METRICS ARRAY ============

        return [
            // USD Metrics
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
                    'value' => $overduePaymentsUsd,
                    'amount' => $overdueAmountUsd,
                    'formatted_amount' => '$' . number_format($overdueAmountUsd, 2),
                ],
                'paid_invoices' => ['value' => $paidInvoicesUsd],
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

            // KSH Metrics
            'ksh' => [
                'total_revenue' => [
                    'value' => $totalRevenueKsh,
                    'formatted' => 'KSH ' . number_format($totalRevenueKsh, 2),
                    'change' => round($revenueChangeKsh, 1),
                ],
                'pending_invoices' => [
                    'value' => $pendingInvoicesKsh,
                    'amount' => $pendingAmountKsh,
                    'formatted_amount' => 'KSH ' . number_format($pendingAmountKsh, 2),
                ],
                'overdue_payments' => [
                    'value' => $overduePaymentsKsh,
                    'amount' => $overdueAmountKsh,
                    'formatted_amount' => 'KSH ' . number_format($overdueAmountKsh, 2),
                ],
                'paid_invoices' => ['value' => $paidInvoicesKsh],
                'monthly_revenue' => [
                    'value' => $monthlyRevenueKsh,
                    'formatted' => 'KSH ' . number_format($monthlyRevenueKsh, 2),
                ],
                'invoiced_amount' => [
                    'value' => $invoicedAmountKsh,
                    'formatted' => 'KSH ' . number_format($invoicedAmountKsh, 2),
                ],
                'collection_rate' => ['value' => round($collectionRateKsh, 1)],
            ],

            // Combined Metrics (USD Equivalent)
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

            // Common Metrics
            'active_customers' => ['value' => $activeCustomers, 'change' => $customerChange],
            'new_customers' => ['value' => $newCustomers],
            'avg_payment_days' => [
                'value' => $avgPaymentDays,
                'trend' => $paymentTrend,
                'trend_icon' => $trendIcon,
                'trend_color' => $trendColor,
                'subtitle' => $paymentTrend == 'positive' ? 'Faster payments' : 'Slower payments',
            ],
        ];

    } catch (\Exception $e) {
        \Log::error('Error getting financial metrics: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        // Return empty metrics
        $emptyCurrency = [
            'total_revenue' => ['value' => 0, 'formatted' => '$0.00', 'change' => 0],
            'pending_invoices' => ['value' => 0, 'amount' => 0, 'formatted_amount' => '$0.00'],
            'overdue_payments' => ['value' => 0, 'amount' => 0, 'formatted_amount' => '$0.00'],
            'paid_invoices' => ['value' => 0],
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
        ];
    }
}

    /**
     * Get revenue trends for charts with currency separation
     */
    /**
 * Get revenue trends for charts
 */
private function getRevenueTrends(): array
{
    try {
        $months = [];
        $revenues = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            $revenue = ConsolidatedBilling::where('status', 'paid')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('paid_amount');

            $revenues[] = $revenue;
        }

        return [
            'months' => $months,
            'revenues' => $revenues,
        ];

    } catch (\Exception $e) {
        \Log::error('Error getting revenue trends: ' . $e->getMessage());
        return [
            'months' => [],
            'revenues' => [],
        ];
    }
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
        // Get all pending invoices
        $invoices = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->where('users.role', 'customer')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue', 'partial'])
            ->select(
                'consolidated_billings.*',
                'users.name as customer_name'
            )
            ->get();

        $debtKsh = [
            'total_receivables' => 0,
            'current' => 0,
            'days_30' => 0,
            'days_60' => 0,
            'days_90' => 0,
            'days_over_90' => 0,
            'overdue' => 0,
        ];

        $debtUsd = [
            'total_receivables' => 0,
            'current' => 0,
            'days_30' => 0,
            'days_60' => 0,
            'days_90' => 0,
            'days_over_90' => 0,
            'overdue' => 0,
        ];

        $detailedAging = [];
        $today = Carbon::now();

        foreach ($invoices as $invoice) {
            // Calculate remaining amount
            $remainingAmount = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);

            if ($remainingAmount <= 0) {
                continue;
            }

            $dueDate = Carbon::parse($invoice->due_date);

            // Calculate days overdue (positive number for overdue invoices)
            if ($dueDate->lt($today)) {
                $daysOverdue = $dueDate->diffInDays($today);
            } else {
                $daysOverdue = 0;
            }

            // Prepare aging data
            $agingData = [
                'customer_name' => $invoice->customer_name,
                'currency' => $invoice->currency,
                'total_due' => $remainingAmount,
                'current' => 0,
                'days_30' => 0,
                'days_60' => 0,
                'days_90' => 0,
                'days_over_90' => 0,
            ];

            // Categorize by days overdue
            if ($daysOverdue == 0) {
                $agingData['current'] = $remainingAmount;
                if ($invoice->currency === 'KSH') {
                    $debtKsh['current'] += $remainingAmount;
                } else {
                    $debtUsd['current'] += $remainingAmount;
                }
            } elseif ($daysOverdue <= 30) {
                $agingData['days_30'] = $remainingAmount;
                if ($invoice->currency === 'KSH') {
                    $debtKsh['days_30'] += $remainingAmount;
                    $debtKsh['overdue'] += $remainingAmount;
                } else {
                    $debtUsd['days_30'] += $remainingAmount;
                    $debtUsd['overdue'] += $remainingAmount;
                }
            } elseif ($daysOverdue <= 60) {
                $agingData['days_60'] = $remainingAmount;
                if ($invoice->currency === 'KSH') {
                    $debtKsh['days_60'] += $remainingAmount;
                    $debtKsh['overdue'] += $remainingAmount;
                } else {
                    $debtUsd['days_60'] += $remainingAmount;
                    $debtUsd['overdue'] += $remainingAmount;
                }
            } elseif ($daysOverdue <= 90) {
                $agingData['days_90'] = $remainingAmount;
                if ($invoice->currency === 'KSH') {
                    $debtKsh['days_90'] += $remainingAmount;
                    $debtKsh['overdue'] += $remainingAmount;
                } else {
                    $debtUsd['days_90'] += $remainingAmount;
                    $debtUsd['overdue'] += $remainingAmount;
                }
            } else {
                $agingData['days_over_90'] = $remainingAmount;
                if ($invoice->currency === 'KSH') {
                    $debtKsh['days_over_90'] += $remainingAmount;
                    $debtKsh['overdue'] += $remainingAmount;
                } else {
                    $debtUsd['days_over_90'] += $remainingAmount;
                    $debtUsd['overdue'] += $remainingAmount;
                }
            }

            // Calculate risk level based on aging
            if ($daysOverdue > 90) {
                $agingData['risk_level'] = 'critical';
            } elseif ($daysOverdue > 60) {
                $agingData['risk_level'] = 'high';
            } elseif ($daysOverdue > 30) {
                $agingData['risk_level'] = 'medium';
            } elseif ($daysOverdue > 0) {
                $agingData['risk_level'] = 'medium';
            } else {
                $agingData['risk_level'] = 'low';
            }

            $detailedAging[] = (object) $agingData;
        }

        // Calculate KSH totals
        $debtKsh['total_receivables'] = $debtKsh['current'] + $debtKsh['days_30'] + $debtKsh['days_60'] + $debtKsh['days_90'] + $debtKsh['days_over_90'];
        $debtKsh['current_percentage'] = $debtKsh['total_receivables'] > 0 ? ($debtKsh['current'] / $debtKsh['total_receivables']) * 100 : 0;
        $debtKsh['overdue_percentage'] = $debtKsh['total_receivables'] > 0 ? ($debtKsh['overdue'] / $debtKsh['total_receivables']) * 100 : 0;
        $debtKsh['bad_debt_provision'] = $debtKsh['days_over_90'] * 0.5;
        $debtKsh['bad_debt_percentage'] = $debtKsh['total_receivables'] > 0 ? ($debtKsh['bad_debt_provision'] / $debtKsh['total_receivables']) * 100 : 0;

        // Calculate USD totals
        $debtUsd['total_receivables'] = $debtUsd['current'] + $debtUsd['days_30'] + $debtUsd['days_60'] + $debtUsd['days_90'] + $debtUsd['days_over_90'];
        $debtUsd['current_percentage'] = $debtUsd['total_receivables'] > 0 ? ($debtUsd['current'] / $debtUsd['total_receivables']) * 100 : 0;
        $debtUsd['overdue_percentage'] = $debtUsd['total_receivables'] > 0 ? ($debtUsd['overdue'] / $debtUsd['total_receivables']) * 100 : 0;
        $debtUsd['bad_debt_provision'] = $debtUsd['days_over_90'] * 0.5;
        $debtUsd['bad_debt_percentage'] = $debtUsd['total_receivables'] > 0 ? ($debtUsd['bad_debt_provision'] / $debtUsd['total_receivables']) * 100 : 0;

        // Calculate collection metrics
        $collectionMetricsKsh = $this->calculateCollectionMetrics('KSH');
        $collectionMetricsUsd = $this->calculateCollectionMetrics('USD');

        // Debug logging
        \Log::info('Debt Aging Report - KSH', $debtKsh);
        \Log::info('Debt Aging Report - USD', $debtUsd);

        return [
            'debt_summary_ksh' => $debtKsh,
            'debt_summary_usd' => $debtUsd,
            'collection_metrics_ksh' => $collectionMetricsKsh,
            'collection_metrics_usd' => $collectionMetricsUsd,
            'detailed_aging' => collect($detailedAging),
        ];

    } catch (\Exception $e) {
        \Log::error('Error generating debt aging report: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

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

        $emptyMetrics = [
            'average_collection_period' => 0,
            'collection_efficiency' => 0,
            'dsr' => 0,
            'recovery_rate' => 0,
        ];

        return [
            'debt_summary_ksh' => $emptyDebt,
            'debt_summary_usd' => $emptyDebt,
            'collection_metrics_ksh' => $emptyMetrics,
            'collection_metrics_usd' => $emptyMetrics,
            'detailed_aging' => collect(),
        ];
    }
}

/**
 * Calculate collection metrics for a specific currency
 */
private function calculateCollectionMetrics(string $currency): array
{
    try {
        $today = Carbon::now();
        $yearStart = Carbon::now()->startOfYear();

        // Get total sales for the year
        $totalSales = DB::table('consolidated_billings')
            ->where('status', 'paid')
            ->where('currency', $currency)
            ->whereBetween('billing_date', [$yearStart, $today])
            ->sum('total_amount');

        // Get current receivables
        $receivables = DB::table('consolidated_billings')
            ->where('currency', $currency)
            ->whereIn('status', ['pending', 'sent', 'overdue', 'partial'])
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)'));

        // Calculate DSR (Days Sales Outstanding)
        $averageDailySales = $totalSales / 365;
        $dsr = $averageDailySales > 0 ? $receivables / $averageDailySales : 0;

        // Calculate collection efficiency
        $totalBilled = DB::table('consolidated_billings')
            ->where('currency', $currency)
            ->whereBetween('billing_date', [$yearStart, $today])
            ->sum('total_amount');

        $totalCollected = DB::table('consolidated_billings')
            ->where('currency', $currency)
            ->where('status', 'paid')
            ->whereBetween('payment_date', [$yearStart, $today])
            ->sum('paid_amount');

        $collectionEfficiency = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;

        // Calculate recovery rate
        $totalDue = DB::table('consolidated_billings')
            ->where('currency', $currency)
            ->whereIn('status', ['pending', 'sent', 'overdue', 'partial', 'paid'])
            ->sum('total_amount');

        $recoveryRate = $totalDue > 0 ? ($totalCollected / $totalDue) * 100 : 0;

        return [
            'average_collection_period' => round($dsr),
            'collection_efficiency' => round($collectionEfficiency, 1),
            'dsr' => round($dsr, 1),
            'recovery_rate' => round($recoveryRate, 1),
        ];

    } catch (\Exception $e) {
        \Log::error('Error calculating collection metrics: ' . $e->getMessage());
        return [
            'average_collection_period' => 0,
            'collection_efficiency' => 0,
            'dsr' => 0,
            'recovery_rate' => 0,
        ];
    }
}

/**
 * Calculate risk level based on aging data
 */
private function calculateRiskLevel(array $agingData): string
{
    if ($agingData['days_over_90'] > 0) {
        return 'critical';
    }

    if ($agingData['days_90'] > 0) {
        return 'high';
    }

    if ($agingData['days_60'] > 0) {
        return 'medium';
    }

    if ($agingData['days_30'] > 0) {
        return 'medium';
    }

    return 'low';
}
    /**
     * Generate cash flow report with currency separation
     */
    private function generateCashFlowReport($startDate, $endDate): array
    {
        try {
            $exchangeRate = $this->getExchangeRate(); // Use the helper method

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

            // Operating cash flow
            $operatingKsh = $cashFromCustomersKsh - $cashToSuppliersKsh - $cashForExpensesKsh;
            $operatingUsd = $cashFromCustomersUsd - $cashToSuppliersUsd - $cashForExpensesUsd;

            // Net cash flow
            $netCashFlowKsh = $operatingKsh;
            $netCashFlowUsd = $operatingUsd;

            return [
                'cash_flow_summary_ksh' => [
                    'operating' => $operatingKsh,
                    'investing' => 0,
                    'financing' => 0,
                    'net_cash_flow' => $netCashFlowKsh,
                ],
                'cash_flow_summary_usd' => [
                    'operating' => $operatingUsd,
                    'investing' => 0,
                    'financing' => 0,
                    'net_cash_flow' => $netCashFlowUsd,
                ],
                'cash_flow_details_ksh' => [
                    'cash_from_customers' => $cashFromCustomersKsh,
                    'cash_to_suppliers' => $cashToSuppliersKsh,
                    'cash_for_expenses' => $cashForExpensesKsh,
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
                'cash_flow_details_usd' => [
                    'cash_from_customers' => $cashFromCustomersUsd,
                    'cash_to_suppliers' => $cashToSuppliersUsd,
                    'cash_for_expenses' => $cashForExpensesUsd,
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
                'cash_flow_summary' => [
                    'operating' => $this->convertCurrency($operatingKsh, 'KSH', 'USD') + $operatingUsd,
                    'investing' => 0,
                    'financing' => 0,
                    'net_cash_flow' => $this->convertCurrency($netCashFlowKsh, 'KSH', 'USD') + $netCashFlowUsd,
                ],
                'exchange_rate' => $exchangeRate,
            ];
        } catch (\Exception $e) {
            Log::error('Error generating cash flow report: ' . $e->getMessage());
            return $this->getEmptyCashFlowReport();
        }
    }

    /**
     * Get empty cash flow report structure
     */
    private function getEmptyCashFlowReport(): array
    {
        $empty = ['operating' => 0, 'investing' => 0, 'financing' => 0, 'net_cash_flow' => 0];
        $emptyDetails = [
            'cash_from_customers' => 0, 'cash_to_suppliers' => 0, 'cash_for_expenses' => 0,
            'interest_paid' => 0, 'taxes_paid' => 0, 'equipment_purchase' => 0,
            'infrastructure_investment' => 0, 'property_purchase' => 0, 'investment_income' => 0,
            'asset_sales' => 0, 'loan_proceeds' => 0, 'equity_issuance' => 0,
            'dividends_paid' => 0, 'debt_repayment' => 0,
        ];

        return [
            'cash_flow_summary_ksh' => $empty,
            'cash_flow_summary_usd' => $empty,
            'cash_flow_details_ksh' => $emptyDetails,
            'cash_flow_details_usd' => $emptyDetails,
            'cash_flow_summary' => $empty,
            'exchange_rate' => 130,
        ];
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
    try {
        // ============ REVENUE (Paid Invoices) ============
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

        // ============ PENDING INVOICES (Not paid, regardless of date) ============
        $pendingAmountKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
            ->where('currency', 'KSH')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $pendingAmountUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
            ->where('currency', 'USD')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $pendingInvoicesKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
            ->where('currency', 'KSH')
            ->where(DB::raw('total_amount - COALESCE(paid_amount, 0)'), '>', 0)
            ->count();

        $pendingInvoicesUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
            ->where('currency', 'USD')
            ->where(DB::raw('total_amount - COALESCE(paid_amount, 0)'), '>', 0)
            ->count();

        // ============ OVERDUE INVOICES ============
        $today = date('Y-m-d');
        $overdueAmountKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $today)
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $overdueAmountUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $today)
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

        $overdueInvoicesKsh = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $today)
            ->where(DB::raw('total_amount - COALESCE(paid_amount, 0)'), '>', 0)
            ->count();

        $overdueInvoicesUsd = DB::table('consolidated_billings')
            ->whereIn('status', ['pending', 'sent', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $today)
            ->where(DB::raw('total_amount - COALESCE(paid_amount, 0)'), '>', 0)
            ->count();

        // ============ AVG INVOICE VALUE ============
        $avgValueKsh = $pendingInvoicesKsh > 0 ? $pendingAmountKsh / $pendingInvoicesKsh : 0;
        $avgValueUsd = $pendingInvoicesUsd > 0 ? $pendingAmountUsd / $pendingInvoicesUsd : 0;

        // ============ REVENUE BY CURRENCY ============
        $revenueByCurrency = DB::table('consolidated_billings')
            ->select(
                'currency',
                DB::raw('SUM(COALESCE(paid_amount, 0)) as total_revenue'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('AVG(COALESCE(paid_amount, 0)) as avg_invoice_amount')
            )
            ->where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('currency')
            ->get();

        // ============ REVENUE BY SERVICE TYPE ============
        $revenueByTypeKsh = DB::table('billing_line_items')
            ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
            ->where('consolidated_billings.status', 'paid')
            ->where('consolidated_billings.currency', 'KSH')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->select(
                'billing_line_items.billing_cycle',
                DB::raw('SUM(billing_line_items.amount) as revenue'),
                DB::raw('COUNT(DISTINCT consolidated_billings.id) as count')
            )
            ->groupBy('billing_line_items.billing_cycle')
            ->get();

        $revenueByTypeUsd = DB::table('billing_line_items')
            ->join('consolidated_billings', 'billing_line_items.consolidated_billing_id', '=', 'consolidated_billings.id')
            ->where('consolidated_billings.status', 'paid')
            ->where('consolidated_billings.currency', 'USD')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->select(
                'billing_line_items.billing_cycle',
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
            ->where('status', 'paid')
            ->where('currency', 'KSH')
            ->whereNotNull('payment_date')
            ->whereBetween('payment_date', [$startDate, $endDate])
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
            ->where('status', 'paid')
            ->where('currency', 'USD')
            ->whereNotNull('payment_date')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy(DB::raw('YEAR(payment_date)'), DB::raw('MONTH(payment_date)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // ============ TOP CUSTOMERS ============
        $topCustomersKsh = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->where('consolidated_billings.status', 'paid')
            ->where('consolidated_billings.currency', 'KSH')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
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
            ->where('consolidated_billings.status', 'paid')
            ->where('consolidated_billings.currency', 'USD')
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->select(
                'users.name',
                DB::raw('SUM(consolidated_billings.paid_amount) as total_spent'),
                DB::raw('COUNT(consolidated_billings.id) as invoices_count')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // ============ MOST DELAYED INVOICES ============
        $mostDelayedKsh = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'partial', 'overdue'])
            ->where('consolidated_billings.currency', 'KSH')
            ->where('consolidated_billings.due_date', '<', $today)
            ->where(DB::raw('consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)'), '>', 0)
            ->select(
                'consolidated_billings.billing_number',
                'users.name as customer_name',
                'consolidated_billings.total_amount',
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
            ->where(DB::raw('consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)'), '>', 0)
            ->select(
                'consolidated_billings.billing_number',
                'users.name as customer_name',
                'consolidated_billings.total_amount',
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
            ->where('consolidated_billings.due_date', '<=', now()->addDays(7))
            ->where(DB::raw('consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)'), '>', 0)
            ->select(
                'consolidated_billings.billing_number',
                'users.name as customer_name',
                'consolidated_billings.total_amount',
                'consolidated_billings.due_date',
                DB::raw('DATEDIFF(consolidated_billings.due_date, NOW()) as days_until_due')
            )
            ->orderBy('days_until_due', 'asc')
            ->get();

        $upcomingDueUsd = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'partial'])
            ->where('consolidated_billings.currency', 'USD')
            ->where('consolidated_billings.due_date', '>=', $today)
            ->where('consolidated_billings.due_date', '<=', now()->addDays(7))
            ->where(DB::raw('consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)'), '>', 0)
            ->select(
                'consolidated_billings.billing_number',
                'users.name as customer_name',
                'consolidated_billings.total_amount',
                'consolidated_billings.due_date',
                DB::raw('DATEDIFF(consolidated_billings.due_date, NOW()) as days_until_due')
            )
            ->orderBy('days_until_due', 'asc')
            ->get();

        return [
            // KSH Summary
            'total_revenue_ksh' => $totalRevenueKsh,
            'pending_amount_ksh' => $pendingAmountKsh,
            'pending_invoices_ksh' => $pendingInvoicesKsh,
            'overdue_amount_ksh' => $overdueAmountKsh,
            'overdue_invoices_ksh' => $overdueInvoicesKsh,
            'avg_invoice_value_ksh' => $avgValueKsh,

            // USD Summary
            'total_revenue_usd' => $totalRevenueUsd,
            'pending_amount_usd' => $pendingAmountUsd,
            'pending_invoices_usd' => $pendingInvoicesUsd,
            'overdue_amount_usd' => $overdueAmountUsd,
            'overdue_invoices_usd' => $overdueInvoicesUsd,
            'avg_invoice_value_usd' => $avgValueUsd,

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

        return [
            'total_revenue_ksh' => 0,
            'total_revenue_usd' => 0,
            'pending_amount_ksh' => 0,
            'pending_amount_usd' => 0,
            'pending_invoices_ksh' => 0,
            'pending_invoices_usd' => 0,
            'overdue_amount_ksh' => 0,
            'overdue_amount_usd' => 0,
            'overdue_invoices_ksh' => 0,
            'overdue_invoices_usd' => 0,
            'avg_invoice_value_ksh' => 0,
            'avg_invoice_value_usd' => 0,
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
        \Log::info('=== Starting Aging Report Generation ===');

        $invoices = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->leftJoin('billing_line_items', 'consolidated_billings.id', '=', 'billing_line_items.consolidated_billing_id')
            ->where('users.role', 'customer')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue', 'partial'])
            ->select(
                'consolidated_billings.id',
                'consolidated_billings.user_id',
                'consolidated_billings.currency',
                'consolidated_billings.due_date',
                'consolidated_billings.total_amount',
                'consolidated_billings.paid_amount',
                'users.name as customer_name',
                DB::raw('COALESCE(billing_line_items.amount, consolidated_billings.total_amount) as amount')
            )
            ->get();

        $agingDataKsh = [];
        $agingDataUsd = [];
        $today = Carbon::now();

        foreach ($invoices as $invoice) {
            $remainingAmount = floatval($invoice->total_amount) - floatval($invoice->paid_amount ?? 0);

            if ($remainingAmount <= 0) {
                continue;
            }

            $dueDate = Carbon::parse($invoice->due_date);

            // Calculate days overdue (positive number for overdue invoices)
            if ($dueDate->lt($today)) {
                $daysOverdue = $dueDate->diffInDays($today);
            } else {
                $daysOverdue = 0; // Not overdue yet
            }

            // Determine aging bucket based on days overdue
            if ($daysOverdue == 0) {
                $agingBucket = 'current';
            } elseif ($daysOverdue <= 30) {
                $agingBucket = 'days_30';
            } elseif ($daysOverdue <= 60) {
                $agingBucket = 'days_60';
            } else {
                $agingBucket = 'days_90_plus';
            }

            // Debug logging
            \Log::info('Aging calculation', [
                'customer' => $invoice->customer_name,
                'due_date' => $invoice->due_date,
                'days_overdue' => $daysOverdue,
                'bucket' => $agingBucket,
                'amount' => $remainingAmount
            ]);

            if ($invoice->currency === 'KSH') {
                if (!isset($agingDataKsh[$invoice->customer_name])) {
                    $agingDataKsh[$invoice->customer_name] = [
                        'customer_name' => $invoice->customer_name,
                        'current' => 0,
                        'days_30' => 0,
                        'days_60' => 0,
                        'days_90_plus' => 0,
                    ];
                }
                $agingDataKsh[$invoice->customer_name][$agingBucket] += $remainingAmount;
            } else {
                if (!isset($agingDataUsd[$invoice->customer_name])) {
                    $agingDataUsd[$invoice->customer_name] = [
                        'customer_name' => $invoice->customer_name,
                        'current' => 0,
                        'days_30' => 0,
                        'days_60' => 0,
                        'days_90_plus' => 0,
                    ];
                }
                $agingDataUsd[$invoice->customer_name][$agingBucket] += $remainingAmount;
            }
        }

        $kshCollection = collect($agingDataKsh)->values();
        $usdCollection = collect($agingDataUsd)->values();

        \Log::info('Final KSH Collection', ['data' => $kshCollection->toArray()]);
        \Log::info('Final USD Collection (first 3)', ['data' => $usdCollection->take(3)->toArray()]);

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
 * Get aging bucket based on days overdue
 */
private function getAgingBucket($daysOverdue): string
{
    // $daysOverdue is negative if due date is in the future
    // Positive if due date is in the past (overdue)

    if ($daysOverdue <= 0) {
        return 'current';  // Not overdue yet
    } elseif ($daysOverdue <= 30) {
        return 'days_30';   // 1-30 days overdue
    } elseif ($daysOverdue <= 60) {
        return 'days_60';   // 31-60 days overdue
    } else {
        return 'days_90_plus'; // 61+ days overdue
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

    /**
 * Display financial reports page
 */
public function financialReports(Request $request)
{
    try {
        $reportType = $request->get('report_type', 'financial_summary');
        $period = $request->get('period', 'this_month');

        list($startDate, $endDate) = $this->getDateRange($period, $request->start_date, $request->end_date);

        // Get report data
        $reportData = $this->generateReport($reportType, $startDate, $endDate);

        // Get financial parameters for reference
        $financialParameters = DB::table('financial_parameters')
            ->where('effective_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->orderBy('parameter_name')
            ->get();

        // Get settings
        $settings = DB::table('settings')->get();

        $reportData['report_type'] = $reportType;
        $reportData['start_date'] = $startDate;
        $reportData['end_date'] = $endDate;

        return view('finance.financial-reports', compact(
            'reportData',
            'reportType',
            'period',
            'startDate',
            'endDate',
            'financialParameters',
            'settings'
        ));

    } catch (\Exception $e) {
        Log::error('Financial Reports Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Unable to load financial reports: ' . $e->getMessage());
    }
}

/**
 * Export financial reports
 */
public function exportFinancialReport(Request $request)
{
    try {
        $reportType = $request->get('report_type', 'financial_summary');
        $format = $request->get('format', 'pdf');
        $period = $request->get('period', 'this_month');

        list($startDate, $endDate) = $this->getDateRange($period, $request->start_date, $request->end_date);

        $reportData = $this->generateReport($reportType, $startDate, $endDate);

        if ($format === 'csv') {
            return $this->exportToCsv($reportData, $reportType, $startDate, $endDate);
        }

        // For PDF export (requires barryvdh/laravel-dompdf)
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = Pdf::loadView('finance.reports.pdf', compact('reportData', 'reportType', 'startDate', 'endDate'));
            return $pdf->download("financial_report_{$reportType}_{$startDate}_to_{$endDate}.pdf");
        }

        return redirect()->back()->with('error', 'PDF export not available. Please install barryvdh/laravel-dompdf');

    } catch (\Exception $e) {
        Log::error('Export Financial Report Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Unable to export report: ' . $e->getMessage());
    }
}
}
