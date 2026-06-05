<?php
// app/Http/Controllers/Finance/DebtManagementController.php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan;
use App\Models\PaymentPlanInstallment;
use Illuminate\Http\Request;
use App\Models\ConsolidatedBilling;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DebtManagementController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('permission:view_debt_dashboard');
    }

    /**
     * Get currency summary for dashboard
     */
    private function getCurrencySummary()
    {
        return DB::table('consolidated_billings')
            ->select(
                'currency',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total_amount) as total_billed'),
                DB::raw('SUM(COALESCE(paid_amount, 0)) as total_paid'),
                DB::raw('SUM(total_amount - COALESCE(paid_amount, 0)) as outstanding'),
                DB::raw('COUNT(CASE WHEN due_date < CURDATE() AND COALESCE(paid_amount, 0) < total_amount THEN 1 END) as overdue_count'),
                DB::raw('SUM(CASE WHEN due_date < CURDATE() AND COALESCE(paid_amount, 0) < total_amount THEN total_amount - COALESCE(paid_amount, 0) ELSE 0 END) as overdue_amount')
            )
            ->groupBy('currency')
            ->get();
    }

    /**
     * Get overdue summary for a specific currency
     */
    private function getOverdueSummary($currency = 'all')
    {
        $query = DB::table('consolidated_billings')
            ->select(
                DB::raw("COUNT(CASE WHEN due_date < CURDATE() AND COALESCE(paid_amount, 0) < total_amount THEN 1 END) as overdue_invoices"),
                DB::raw("SUM(CASE WHEN due_date < CURDATE() AND COALESCE(paid_amount, 0) < total_amount THEN total_amount - COALESCE(paid_amount, 0) ELSE 0 END) as total_overdue"),
                DB::raw("AVG(CASE WHEN due_date < CURDATE() AND COALESCE(paid_amount, 0) < total_amount THEN DATEDIFF(CURDATE(), due_date) END) as avg_days_overdue"),
                DB::raw("COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_invoices"),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as total_paid")
            );

        if ($currency !== 'all') {
            $query->where('currency', $currency);
        }

        $result = $query->first();

        // Calculate collection rate
        $totalBilled = DB::table('consolidated_billings')
            ->when($currency !== 'all', function($q) use ($currency) {
                return $q->where('currency', $currency);
            })
            ->sum('total_amount');

        $result->collection_rate = $totalBilled > 0 ? ($result->total_paid / $totalBilled) * 100 : 0;

        return $result;
    }

    /**
     * Get aging analysis with proper currency separation
     */
    private function getAgingAnalysis($currency = 'all')
    {
        if ($currency == 'all') {
            // Get USD aging separately
            $usdResults = DB::table('consolidated_billings')
                ->select(
                    DB::raw("CASE
                        WHEN DATEDIFF(CURDATE(), due_date) <= 30 THEN '0-30 days'
                        WHEN DATEDIFF(CURDATE(), due_date) <= 60 THEN '31-60 days'
                        WHEN DATEDIFF(CURDATE(), due_date) <= 90 THEN '61-90 days'
                        ELSE '90+ days'
                    END as age_bucket"),
                    DB::raw('COUNT(*) as invoice_count'),
                    DB::raw('SUM(total_amount) as total_amount'),
                    DB::raw('SUM(COALESCE(paid_amount, 0)) as paid_amount'),
                    DB::raw('SUM(total_amount - COALESCE(paid_amount, 0)) as outstanding')
                )
                ->whereRaw('due_date < CURDATE()')
                ->whereRaw('COALESCE(paid_amount, 0) < total_amount')
                ->where('currency', 'USD')
                ->groupBy('age_bucket')
                ->get();

            // Get KSH aging separately
            $kshResults = DB::table('consolidated_billings')
                ->select(
                    DB::raw("CASE
                        WHEN DATEDIFF(CURDATE(), due_date) <= 30 THEN '0-30 days'
                        WHEN DATEDIFF(CURDATE(), due_date) <= 60 THEN '31-60 days'
                        WHEN DATEDIFF(CURDATE(), due_date) <= 90 THEN '61-90 days'
                        ELSE '90+ days'
                    END as age_bucket"),
                    DB::raw('COUNT(*) as invoice_count'),
                    DB::raw('SUM(total_amount) as total_amount'),
                    DB::raw('SUM(COALESCE(paid_amount, 0)) as paid_amount'),
                    DB::raw('SUM(total_amount - COALESCE(paid_amount, 0)) as outstanding')
                )
                ->whereRaw('due_date < CURDATE()')
                ->whereRaw('COALESCE(paid_amount, 0) < total_amount')
                ->where('currency', 'KSH')
                ->groupBy('age_bucket')
                ->get();

            // Create maps for quick lookup
            $usdMap = [];
            $kshMap = [];

            foreach ($usdResults as $item) {
                $usdMap[$item->age_bucket] = $item;
            }

            foreach ($kshResults as $item) {
                $kshMap[$item->age_bucket] = $item;
            }

            // Merge results for all buckets
            $buckets = ['0-30 days', '31-60 days', '61-90 days', '90+ days'];
            $merged = [];

            foreach ($buckets as $bucket) {
                $usd = $usdMap[$bucket] ?? null;
                $ksh = $kshMap[$bucket] ?? null;

                $merged[] = (object)[
                    'age_bucket' => $bucket,
                    'invoice_count' => ($usd->invoice_count ?? 0) + ($ksh->invoice_count ?? 0),
                    // USD amounts
                    'usd_amount' => $usd->total_amount ?? 0,
                    'usd_paid' => $usd->paid_amount ?? 0,
                    'usd_outstanding' => $usd->outstanding ?? 0,
                    // KSH amounts
                    'ksh_amount' => $ksh->total_amount ?? 0,
                    'ksh_paid' => $ksh->paid_amount ?? 0,
                    'ksh_outstanding' => $ksh->outstanding ?? 0,
                ];
            }

            return collect($merged);

        } else {
            // Single currency view
            $results = DB::table('consolidated_billings')
                ->select(
                    DB::raw("CASE
                        WHEN DATEDIFF(CURDATE(), due_date) <= 30 THEN '0-30 days'
                        WHEN DATEDIFF(CURDATE(), due_date) <= 60 THEN '31-60 days'
                        WHEN DATEDIFF(CURDATE(), due_date) <= 90 THEN '61-90 days'
                        ELSE '90+ days'
                    END as age_bucket"),
                    DB::raw('COUNT(*) as invoice_count'),
                    DB::raw('SUM(total_amount) as total_amount'),
                    DB::raw('SUM(COALESCE(paid_amount, 0)) as paid_amount'),
                    DB::raw('SUM(total_amount - COALESCE(paid_amount, 0)) as outstanding')
                )
                ->whereRaw('due_date < CURDATE()')
                ->whereRaw('COALESCE(paid_amount, 0) < total_amount')
                ->where('currency', $currency)
                ->groupBy('age_bucket')
                ->get();

            $buckets = ['0-30 days', '31-60 days', '61-90 days', '90+ days'];
            $formattedResults = [];

            foreach ($buckets as $bucket) {
                $found = $results->firstWhere('age_bucket', $bucket);
                $formattedResults[] = (object)[
                    'age_bucket' => $bucket,
                    'invoice_count' => $found->invoice_count ?? 0,
                    'total_amount' => $found->total_amount ?? 0,
                    'paid_amount' => $found->paid_amount ?? 0,
                    'outstanding' => $found->outstanding ?? 0,
                ];
            }

            return collect($formattedResults);
        }
    }

    /**
     * Get payment trend for charts
     */
    private function getPaymentTrend($currency = 'all')
    {
        $query = DB::table('consolidated_billings')
            ->select(
                DB::raw('DATE_FORMAT(billing_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total_billed'),
                DB::raw('SUM(COALESCE(paid_amount, 0)) as total_paid'),
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_invoices'),
                DB::raw('COUNT(CASE WHEN due_date < CURDATE() AND COALESCE(paid_amount, 0) < total_amount THEN 1 END) as overdue_invoices')
            )
            ->where('billing_date', '>=', now()->subMonths(6));

        if ($currency !== 'all') {
            $query->where('currency', $currency);
        }

        return $query->groupBy(DB::raw('DATE_FORMAT(billing_date, "%Y-%m")'))
            ->orderBy('month')
            ->get();
    }

    /**
     * Display customer debt details
     */
    public function customerDebt($id, Request $request)
    {
        $currency = $request->get('currency', 'all');

        $customer = User::withCount(['leases' => function($query) {
            $query->where('status', 'active');
        }])->with('accountManager')->findOrFail($id);

        // Calculate summary with currency filter
        $summaryQuery = DB::table('consolidated_billings')
            ->select(
                'currency',
                DB::raw('SUM(total_amount - COALESCE(paid_amount, 0)) as total_outstanding'),
                DB::raw('COUNT(CASE WHEN due_date < NOW() AND COALESCE(paid_amount, 0) < total_amount THEN 1 END) as overdue_count'),
                DB::raw('AVG(CASE WHEN due_date < NOW() AND COALESCE(paid_amount, 0) < total_amount THEN DATEDIFF(NOW(), due_date) END) as avg_days_overdue'),
                DB::raw('MAX(CASE WHEN due_date < NOW() AND COALESCE(paid_amount, 0) < total_amount THEN DATEDIFF(NOW(), due_date) END) as max_days_overdue'),
                DB::raw('SUM(COALESCE(paid_amount, 0)) as total_paid'),
                DB::raw('COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_invoices'),
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('SUM(total_amount) as total_billed'),
                DB::raw('CASE
                    WHEN SUM(total_amount) > 0 THEN (SUM(COALESCE(paid_amount, 0)) / SUM(total_amount)) * 100
                    ELSE 0
                END as payment_rate'),
                DB::raw('MAX(CASE WHEN status = "paid" THEN updated_at END) as last_payment_date')
            )
            ->where('user_id', $id);

        if ($currency !== 'all') {
            $summaryQuery->where('currency', $currency);
        }

        $summary = $summaryQuery->groupBy('currency')->get();

        // Get overdue invoices
        $overdueQuery = ConsolidatedBilling::where('user_id', $id)
            ->where('due_date', '<', now())
            ->whereRaw('COALESCE(paid_amount, 0) < total_amount');

        if ($currency !== 'all') {
            $overdueQuery->where('currency', $currency);
        }

        $overdueInvoices = $overdueQuery->orderBy('due_date')
            ->get()
            ->map(function($invoice) {
                $invoice->days_overdue = now()->diffInDays($invoice->due_date);
                return $invoice;
            });

        // Get payment history
        $paymentHistory = ConsolidatedBilling::where('user_id', $id)
            ->where('status', 'paid')
            ->where('paid_amount', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        return view('finance.debt.customer', compact(
            'customer', 'summary', 'overdueInvoices', 'paymentHistory', 'currency'
        ));
    }

    /**
     * Get overdue invoices for AJAX or partial view
     */
    public function overdueInvoices(Request $request)
    {
        try {
            $currency = $request->get('currency', 'all');

            $query = ConsolidatedBilling::with('user')
                ->where('due_date', '<', now())
                ->whereRaw('COALESCE(paid_amount, 0) < total_amount')
                ->orderBy('due_date', 'asc');

            if ($currency !== 'all') {
                $query->where('currency', $currency);
            }

            $overdueBillings = $query->get();

            if ($request->ajax()) {
                return view('finance.debt.partials.overdue-invoices-rows', [
                    'overdueBillings' => $overdueBillings
                ])->render();
            }

            return view('finance.debt.overdue-invoices', [
                'overdueBillings' => $overdueBillings
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in overdueInvoices: ' . $e->getMessage());

            if ($request->ajax()) {
                return '<td><td colspan="8" class="text-center py-5 text-danger">Error: ' . $e->getMessage() . '</tr>';
            }

            return view('finance.debt.overdue-invoices', [
                'overdueBillings' => collect(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Main dashboard
     */
    public function dashboard(Request $request)
    {
        $currency = $request->get('currency', 'all');

        // Initialize variables
        $collectionRateUsd = 0;
        $collectionRateKsh = 0;
        $avgDaysOverdue = 0;
        $totalOutstandingUsd = 0;
        $totalOutstandingKsh = 0;
        $totalOverdueUsd = 0;
        $totalOverdueKsh = 0;
        $collectionRate = 0;

        if ($currency == 'all') {
            // Get data for both currencies separately
            $usdSummary = $this->getOverdueSummary('USD');
            $kshSummary = $this->getOverdueSummary('KSH');

            // Get aging analysis for both currencies (merged)
            $agingAnalysis = $this->getAgingAnalysis('all');

            // Calculate overall metrics
            $totalOverdueUsd = $usdSummary->total_overdue ?? 0;
            $totalOverdueKsh = $kshSummary->total_overdue ?? 0;
            $totalInvoices = ($usdSummary->overdue_invoices ?? 0) + ($kshSummary->overdue_invoices ?? 0);

            // Calculate collection rates
            $totalBilledUsd = DB::table('consolidated_billings')->where('currency', 'USD')->sum('total_amount') ?: 1;
            $totalPaidUsd = DB::table('consolidated_billings')->where('currency', 'USD')->whereIn('status', ['paid', 'partial'])->sum('paid_amount') ?: 0;
            $totalBilledKsh = DB::table('consolidated_billings')->where('currency', 'KSH')->sum('total_amount') ?: 1;
            $totalPaidKsh = DB::table('consolidated_billings')->where('currency', 'KSH')->whereIn('status', ['paid', 'partial'])->sum('paid_amount') ?: 0;

            $collectionRateUsd = ($totalPaidUsd / $totalBilledUsd) * 100;
            $collectionRateKsh = ($totalPaidKsh / $totalBilledKsh) * 100;
            $collectionRate = ($collectionRateUsd + $collectionRateKsh) / 2;

            // Calculate average days overdue
            $avgDaysOverdue = max($usdSummary->avg_days_overdue ?? 0, $kshSummary->avg_days_overdue ?? 0);

            // Calculate total outstanding
            $totalOutstandingUsd = DB::table('consolidated_billings')
                ->where('currency', 'USD')
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

            $totalOutstandingKsh = DB::table('consolidated_billings')
                ->where('currency', 'KSH')
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

            $overdueSummary = (object)[
                'overdue_invoices' => $totalInvoices,
            ];

            $topDebtors = $this->getTopDebtors(10, 'all');
            $paymentTrend = $this->getPaymentTrend($currency);
            $currencySummary = $this->getCurrencySummary();

            $overdueInvoices = ConsolidatedBilling::with('user')
                ->where('due_date', '<', now())
                ->whereRaw('COALESCE(paid_amount, 0) < total_amount')
                ->orderBy('due_date', 'asc')
                ->take(10)
                ->get();

        } else {
            // Single currency view
            $singleSummary = $this->getOverdueSummary($currency);
            $agingAnalysis = $this->getAgingAnalysis($currency);
            $topDebtors = $this->getTopDebtors(10, $currency);
            $paymentTrend = $this->getPaymentTrend($currency);
            $currencySummary = $this->getCurrencySummary();

            $collectionRate = $singleSummary->collection_rate ?? 0;
            $avgDaysOverdue = $singleSummary->avg_days_overdue ?? 0;

            $totalOutstanding = DB::table('consolidated_billings')
                ->where('currency', $currency)
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

            $totalOutstandingUsd = $currency == 'USD' ? $totalOutstanding : 0;
            $totalOutstandingKsh = $currency == 'KSH' ? $totalOutstanding : 0;
            $totalOverdueUsd = $currency == 'USD' ? ($singleSummary->total_overdue ?? 0) : 0;
            $totalOverdueKsh = $currency == 'KSH' ? ($singleSummary->total_overdue ?? 0) : 0;

            if ($currency == 'USD') {
                $collectionRateUsd = $collectionRate;
                $collectionRateKsh = 0;
            } else {
                $collectionRateUsd = 0;
                $collectionRateKsh = $collectionRate;
            }

            $overdueInvoices = ConsolidatedBilling::with('user')
                ->where('due_date', '<', now())
                ->where('currency', $currency)
                ->whereRaw('COALESCE(paid_amount, 0) < total_amount')
                ->orderBy('due_date', 'asc')
                ->take(10)
                ->get();

            $overdueSummary = (object)[
                'overdue_invoices' => $singleSummary->overdue_invoices ?? 0,
            ];
        }

        return view('finance.debt.dashboard', compact(
            'overdueSummary',
            'agingAnalysis',
            'topDebtors',
            'paymentTrend',
            'currencySummary',
            'overdueInvoices',
            'currency',
            'collectionRate',
            'collectionRateUsd',
            'collectionRateKsh',
            'avgDaysOverdue',
            'totalOutstandingUsd',
            'totalOutstandingKsh',
            'totalOverdueUsd',
            'totalOverdueKsh'
        ));
    }

    /**
     * Get top debtors
     */
    private function getTopDebtors($limit = 10, $currency = 'all')
    {
        $query = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(DISTINCT consolidated_billings.id) as overdue_invoices'),
                DB::raw('SUM(consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)) as total_outstanding'),
                DB::raw('MAX(DATEDIFF(CURDATE(), consolidated_billings.due_date)) as max_days_overdue')
            )
            ->whereRaw('consolidated_billings.due_date < CURDATE()')
            ->whereRaw('COALESCE(consolidated_billings.paid_amount, 0) < consolidated_billings.total_amount');

        if ($currency !== 'all') {
            $query->where('consolidated_billings.currency', $currency);
        }

        return $query->groupBy('users.id', 'users.name', 'users.email')
            ->having('total_outstanding', '>', 0)
            ->orderBy('total_outstanding', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Bulk send reminders
     */
    public function bulkSendReminder(Request $request)
    {
        $ids = $request->input('ids', []);
        $sent = 0;

        foreach ($ids as $id) {
            $billing = ConsolidatedBilling::find($id);
            if ($billing && $billing->user && $billing->user->email) {
                // Send email logic here
                // Mail::to($billing->user->email)->send(new PaymentReminder($billing));
                $sent++;
            }
        }

        return response()->json(['success' => true, 'sent' => $sent]);
    }

    /**
     * Send reminder for a single invoice
     */
    public function sendReminder($id)
    {
        try {
            $invoice = ConsolidatedBilling::with('user')->findOrFail($id);

            if ($invoice->status === 'paid') {
                return response()->json(['success' => false, 'message' => 'Invoice is already paid.'], 422);
            }

            // Calculate days overdue
            $daysOverdue = now()->diffInDays($invoice->due_date);

            // Determine reminder type
            $reminderType = $this->determineReminderType($daysOverdue);

            // Send the reminder (email logic here)
            // Mail::to($invoice->user->email)->send(new PaymentReminderMail($invoice, $reminderType));

            return response()->json(['success' => true, 'message' => 'Reminder sent successfully.']);

        } catch (\Exception $e) {
            \Log::error('Failed to send reminder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send reminder.'], 500);
        }
    }

    /**
     * Determine reminder type based on days overdue
     */
    private function determineReminderType($daysOverdue)
    {
        if ($daysOverdue <= 7) return 'friendly';
        if ($daysOverdue <= 30) return 'standard';
        if ($daysOverdue <= 60) return 'urgent';
        return 'final_notice';
    }

    /**
     * Invoice details page
     */
    public function invoiceDetails($id)
    {
        $invoice = ConsolidatedBilling::findOrFail($id);
        return view('finance.debt.invoice-details', compact('invoice'));
    }

    /**
     * Aging report
     */
    public function agingReport(Request $request)
    {
        $asOfDate = $request->get('as_of_date', now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $status = $request->get('status');
        $currency = $request->get('currency', 'all');

        // Build base query
        $baseQuery = DB::table('consolidated_billings as cb')
            ->join('users as u', 'cb.user_id', '=', 'u.id')
            ->select(
                'cb.*',
                'u.name as customer_name',
                DB::raw('DATEDIFF("' . $asOfDate . '", cb.due_date) as days_overdue'),
                DB::raw('cb.total_amount - COALESCE(cb.paid_amount, 0) as outstanding')
            )
            ->whereRaw('cb.total_amount > COALESCE(cb.paid_amount, 0)')
            ->whereIn('cb.status', ['pending', 'sent', 'overdue', 'payment_plan']);

        // Apply filters
        if ($customerId) {
            $baseQuery->where('cb.user_id', $customerId);
        }

        if ($status) {
            $baseQuery->where('cb.status', $status);
        }

        if ($currency !== 'all') {
            $baseQuery->where('cb.currency', $currency);
        }

        // Get all invoices for detailed view
        $invoices = $baseQuery->orderBy('cb.due_date')->get();

        // Calculate summary
        $summary = DB::table(DB::raw("({$baseQuery->toSql()}) as filtered"))
            ->mergeBindings($baseQuery)
            ->select(
                DB::raw('SUM(outstanding) as total_outstanding'),
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('SUM(CASE WHEN currency = "USD" THEN outstanding ELSE 0 END) as total_outstanding_usd'),
                DB::raw('SUM(CASE WHEN currency = "KSH" THEN outstanding ELSE 0 END) as total_outstanding_ksh'),
                DB::raw('SUM(CASE WHEN days_overdue <= 30 THEN outstanding ELSE 0 END) as current_amount'),
                DB::raw('SUM(CASE WHEN days_overdue > 30 AND days_overdue <= 60 THEN outstanding ELSE 0 END) as days_31_60_amount'),
                DB::raw('SUM(CASE WHEN days_overdue > 60 AND days_overdue <= 90 THEN outstanding ELSE 0 END) as days_61_90_amount'),
                DB::raw('SUM(CASE WHEN days_overdue > 90 THEN outstanding ELSE 0 END) as days_90_plus_amount')
            )
            ->first();

        // Get aging by customer
        $agingByCustomer = DB::table(DB::raw("({$baseQuery->toSql()}) as filtered"))
            ->mergeBindings($baseQuery)
            ->select(
                'user_id as customer_id',
                'customer_name',
                DB::raw('COUNT(*) as invoices_count'),
                DB::raw('SUM(outstanding) as total_amount'),
                DB::raw('SUM(CASE WHEN currency = "USD" THEN outstanding ELSE 0 END) as total_amount_usd'),
                DB::raw('SUM(CASE WHEN currency = "KSH" THEN outstanding ELSE 0 END) as total_amount_ksh')
            )
            ->groupBy('user_id', 'customer_name')
            ->orderBy('total_amount', 'desc')
            ->get();

        // Get all customers for filter dropdown
        $customers = User::where('role', 'customer')
            ->orderBy('name')
            ->get();

        return view('finance.debt.reports.aging', compact(
            'invoices', 'summary', 'agingByCustomer', 'customers'
        ));
    }

    /**
     * Export overdue invoices to CSV
     */
    public function export(Request $request)
    {
        try {
            $currency = $request->get('currency', 'all');

            $query = ConsolidatedBilling::with('user')
                ->where('due_date', '<', now())
                ->whereRaw('COALESCE(paid_amount, 0) < total_amount');

            if ($currency !== 'all') {
                $query->where('currency', $currency);
            }

            $billings = $query->orderBy('due_date', 'asc')->get();

            $csvData = [];
            $csvData[] = [
                'Invoice #', 'Customer', 'Currency', 'Amount', 'Due Date', 'Overdue Days', 'Status'
            ];

            foreach ($billings as $billing) {
                $dueDate = Carbon::parse($billing->due_date);
                $daysOverdue = $dueDate->isPast() ? $dueDate->diffInDays(now()) : 0;
                $customerName = $billing->user->name ?? 'Unknown Customer';

                $formattedAmount = $billing->currency == 'USD'
                    ? '$' . number_format($billing->total_amount, 2)
                    : 'KSH ' . number_format($billing->total_amount, 2);

                $status = $billing->status == 'partial' ? 'Partially Paid' : 'Overdue';

                $csvData[] = [
                    $billing->billing_number ?? 'CONS-' . $billing->id,
                    $customerName,
                    $billing->currency,
                    $formattedAmount,
                    $dueDate->format('Y-m-d'),
                    $daysOverdue,
                    $status
                ];
            }

            $filename = 'overdue_invoices_' . date('Y-m-d_His') . '.csv';
            $handle = fopen('php://temp', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }

            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);

            return response($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    /**
     * Customers page
     */
    public function customers()
    {
        $billings = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->leftJoin('company_profiles', 'users.id', '=', 'company_profiles.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                DB::raw('COALESCE(users.company_name, users.name) as customer_name'),
                DB::raw('SUM(CASE WHEN consolidated_billings.currency = "USD" THEN consolidated_billings.total_amount ELSE 0 END) as total_amount_usd'),
                DB::raw('SUM(CASE WHEN consolidated_billings.currency = "USD" THEN COALESCE(consolidated_billings.paid_amount, 0) ELSE 0 END) as total_paid_usd'),
                DB::raw('SUM(CASE WHEN consolidated_billings.currency = "USD" THEN consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0) ELSE 0 END) as balance_usd'),
                DB::raw('SUM(CASE WHEN consolidated_billings.currency = "KSH" THEN consolidated_billings.total_amount ELSE 0 END) as total_amount_ksh'),
                DB::raw('SUM(CASE WHEN consolidated_billings.currency = "KSH" THEN COALESCE(consolidated_billings.paid_amount, 0) ELSE 0 END) as total_paid_ksh'),
                DB::raw('SUM(CASE WHEN consolidated_billings.currency = "KSH" THEN consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0) ELSE 0 END) as balance_ksh'),
                DB::raw('COUNT(consolidated_billings.id) as billing_count'),
                DB::raw('MAX(consolidated_billings.due_date) as last_due_date')
            )
            ->whereNotIn('consolidated_billings.status', ['paid','cancelled', 'tev_duplicate', 'tev_failed'])
            ->where(function($query) {
                $query->whereNull('consolidated_billings.paid_amount')
                      ->orWhereColumn('consolidated_billings.total_amount', '>', 'consolidated_billings.paid_amount');
            })
            ->groupBy('users.id', 'users.name', 'users.email', 'company_profiles.phone_number', 'users.company_name')
            ->havingRaw('(SUM(CASE WHEN consolidated_billings.currency = "USD" THEN consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0) ELSE 0 END) > 0
                      OR SUM(CASE WHEN consolidated_billings.currency = "KSH" THEN consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0) ELSE 0 END) > 0)')
            ->orderByRaw('(SUM(CASE WHEN consolidated_billings.currency = "USD" THEN consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0) ELSE 0 END) +
                      SUM(CASE WHEN consolidated_billings.currency = "KSH" THEN (consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)) / 130 ELSE 0 END)) DESC')
            ->get();

        $summary = [
            'total_customers' => $billings->count(),
            'total_balance_usd' => $billings->sum('balance_usd'),
            'total_balance_ksh' => $billings->sum('balance_ksh'),
            'total_invoices' => $billings->sum('billing_count')
        ];

        return view('finance.debt.customers', compact('billings', 'summary'));
    }

    /**
     * Display collection performance report
     */
    public function collectionReport(Request $request)
    {
        // Get date filters
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $period = $request->get('period', 'monthly');

        // Collection summary with proper currency separation
        $collectionSummary = $this->getCollectionSummary($startDate, $endDate);

        // Collection performance by collector
        $collectorPerformance = $this->getCollectorPerformance($startDate, $endDate);

        // Aging collection analysis
        $agingCollection = $this->getAgingCollectionAnalysis();

        // Collection trend
        $collectionTrend = $this->getCollectionTrend($startDate, $endDate, $period);

        // Top performing customers
        $topPerformingCustomers = $this->getTopPerformingCustomers($startDate, $endDate);

        // Problematic customers
        $problematicCustomers = $this->getProblematicCustomers();

        return view('finance.debt.collection-report', compact(
            'collectionSummary',
            'collectorPerformance',
            'agingCollection',
            'collectionTrend',
            'topPerformingCustomers',
            'problematicCustomers',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Get collection summary with proper currency separation
     */
    private function getCollectionSummary($startDate, $endDate)
    {
        // USD Collections - Include BOTH 'paid' AND 'partial' status
        $usdCollected = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'USD')
            ->where('paid_amount', '>', 0)
            ->sum('paid_amount');

        // KSH Collections - Include BOTH 'paid' AND 'partial' status
        $kshCollected = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'KSH')
            ->where('paid_amount', '>', 0)
            ->sum('paid_amount');

        // USD Invoiced (all invoices in period)
        $usdInvoiced = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->where('currency', 'USD')
            ->sum('total_amount');

        // KSH Invoiced
        $kshInvoiced = DB::table('consolidated_billings')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->where('currency', 'KSH')
            ->sum('total_amount');

        // If no invoices in period, use outstanding amounts
        if ($usdInvoiced == 0) {
            $usdInvoiced = DB::table('consolidated_billings')
                ->where('currency', 'USD')
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->sum('total_amount') ?: 1;
        }

        if ($kshInvoiced == 0) {
            $kshInvoiced = DB::table('consolidated_billings')
                ->where('currency', 'KSH')
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->sum('total_amount') ?: 1;
        }

        $collectionRateUsd = ($usdCollected / $usdInvoiced) * 100;
        $collectionRateKsh = ($kshCollected / $kshInvoiced) * 100;

        // Overdue Collected (payments on invoices that were overdue)
        $usdOverdueCollected = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'USD')
            ->where('due_date', '<', $startDate)
            ->where('paid_amount', '>', 0)
            ->sum('paid_amount');

        $kshOverdueCollected = DB::table('consolidated_billings')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partial'])
            ->where('currency', 'KSH')
            ->where('due_date', '<', $startDate)
            ->where('paid_amount', '>', 0)
            ->sum('paid_amount');

        // Calculate average collection period
        $averageCollectionPeriod = $this->calculateAverageCollectionPeriod($startDate, $endDate);

        return [
            'total_collected_usd' => $usdCollected,
            'total_collected_ksh' => $kshCollected,
            'total_invoiced_usd' => $usdInvoiced,
            'total_invoiced_ksh' => $kshInvoiced,
            'collection_rate_usd' => round($collectionRateUsd, 1),
            'collection_rate_ksh' => round($collectionRateKsh, 1),
            'overdue_collected_usd' => $usdOverdueCollected,
            'overdue_collected_ksh' => $kshOverdueCollected,
            'average_collection_period' => $averageCollectionPeriod,
        ];
    }

    /**
     * Calculate average collection period
     */
    private function calculateAverageCollectionPeriod($startDate, $endDate)
    {
        $paidInvoices = DB::table('consolidated_billings')
            ->whereIn('status', ['paid', 'partial'])
            ->whereNotNull('payment_date')
            ->whereNotNull('due_date')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $totalDays = 0;
        $count = 0;

        foreach ($paidInvoices as $invoice) {
            $dueDate = Carbon::parse($invoice->due_date);
            $paymentDate = Carbon::parse($invoice->payment_date);

            // Only calculate if payment is after due date (overdue)
            if ($paymentDate->gt($dueDate)) {
                $totalDays += $dueDate->diffInDays($paymentDate);
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : 0;
    }

    /**
     * Get collector performance with proper data
     */
    private function getCollectorPerformance($startDate, $endDate)
    {
        try {
            // Get all account managers with their assigned customers
            $accountManagers = DB::table('users')
                ->where('role', 'account_manager')
                ->get();

            $results = [];

            foreach ($accountManagers as $manager) {
                // Get customers assigned to this manager
                $assignedCustomers = DB::table('users')
                    ->where('account_manager_id', $manager->id)
                    ->where('role', 'customer')
                    ->pluck('id')
                    ->toArray();

                $assignedCount = count($assignedCustomers);

                // Get USD collections from assigned customers
                $usdCollected = 0;
                if (!empty($assignedCustomers)) {
                    $usdCollected = DB::table('consolidated_billings')
                        ->whereIn('user_id', $assignedCustomers)
                        ->whereBetween('payment_date', [$startDate, $endDate])
                        ->whereIn('status', ['paid', 'partial'])
                        ->where('currency', 'USD')
                        ->where('paid_amount', '>', 0)
                        ->sum('paid_amount');
                }

                // Get KSH collections from assigned customers
                $kshCollected = 0;
                if (!empty($assignedCustomers)) {
                    $kshCollected = DB::table('consolidated_billings')
                        ->whereIn('user_id', $assignedCustomers)
                        ->whereBetween('payment_date', [$startDate, $endDate])
                        ->whereIn('status', ['paid', 'partial'])
                        ->where('currency', 'KSH')
                        ->where('paid_amount', '>', 0)
                        ->sum('paid_amount');
                }

                // Get total USD billed for assigned customers
                $totalUsdBilled = DB::table('consolidated_billings')
                    ->whereIn('user_id', $assignedCustomers)
                    ->where('currency', 'USD')
                    ->sum('total_amount') ?: 1;

                // Get total KSH billed for assigned customers
                $totalKshBilled = DB::table('consolidated_billings')
                    ->whereIn('user_id', $assignedCustomers)
                    ->where('currency', 'KSH')
                    ->sum('total_amount') ?: 1;

                $collectionRateUsd = ($usdCollected / $totalUsdBilled) * 100;
                $collectionRateKsh = ($kshCollected / $totalKshBilled) * 100;

                $results[] = [
                    'id' => $manager->id,
                    'name' => $manager->name,
                    'total_assigned' => $assignedCount,
                    'collected_amount_usd' => $usdCollected,
                    'collected_amount_ksh' => $kshCollected,
                    'collection_rate_usd' => round($collectionRateUsd, 1),
                    'collection_rate_ksh' => round($collectionRateKsh, 1),
                ];
            }

            // Filter out managers with no activity for cleaner display
            $activeResults = array_filter($results, function($item) {
                return $item['collected_amount_usd'] > 0 || $item['collected_amount_ksh'] > 0 || $item['total_assigned'] > 0;
            });

            // Sort by total collected value
            usort($activeResults, function($a, $b) {
                $aTotal = $a['collected_amount_usd'] + ($a['collected_amount_ksh'] / 130);
                $bTotal = $b['collected_amount_usd'] + ($b['collected_amount_ksh'] / 130);
                return $bTotal <=> $aTotal;
            });

            // Convert to collections for view
            $usdCollectors = collect($activeResults)->map(function($item) {
                return (object)[
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'total_assigned' => $item['total_assigned'],
                    'collected_amount' => $item['collected_amount_usd'],
                    'collection_rate' => $item['collection_rate_usd'],
                ];
            })->values();

            $kshCollectors = collect($activeResults)->map(function($item) {
                return (object)[
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'total_assigned' => $item['total_assigned'],
                    'collected_amount' => $item['collected_amount_ksh'],
                    'collection_rate' => $item['collection_rate_ksh'],
                ];
            })->values();

            return [
                'usd' => $usdCollectors,
                'ksh' => $kshCollectors,
            ];

        } catch (\Exception $e) {
            \Log::error('Error in getCollectorPerformance: ' . $e->getMessage());
            return ['usd' => collect(), 'ksh' => collect()];
        }
    }

    /**
     * Get aging collection analysis
     */
    private function getAgingCollectionAnalysis()
    {
        $now = now();

        return [
            // USD Aging
            'current_usd' => DB::table('consolidated_billings')
                ->where('currency', 'USD')
                ->where('due_date', '>=', $now)
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            '1_30_days_usd' => DB::table('consolidated_billings')
                ->where('currency', 'USD')
                ->where('due_date', '<', $now)
                ->where('due_date', '>=', $now->copy()->subDays(30))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            '31_60_days_usd' => DB::table('consolidated_billings')
                ->where('currency', 'USD')
                ->where('due_date', '<', $now->copy()->subDays(30))
                ->where('due_date', '>=', $now->copy()->subDays(60))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            '61_90_days_usd' => DB::table('consolidated_billings')
                ->where('currency', 'USD')
                ->where('due_date', '<', $now->copy()->subDays(60))
                ->where('due_date', '>=', $now->copy()->subDays(90))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            'over_90_days_usd' => DB::table('consolidated_billings')
                ->where('currency', 'USD')
                ->where('due_date', '<', $now->copy()->subDays(90))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            // KSH Aging
            'current_ksh' => DB::table('consolidated_billings')
                ->where('currency', 'KSH')
                ->where('due_date', '>=', $now)
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            '1_30_days_ksh' => DB::table('consolidated_billings')
                ->where('currency', 'KSH')
                ->where('due_date', '<', $now)
                ->where('due_date', '>=', $now->copy()->subDays(30))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            '31_60_days_ksh' => DB::table('consolidated_billings')
                ->where('currency', 'KSH')
                ->where('due_date', '<', $now->copy()->subDays(30))
                ->where('due_date', '>=', $now->copy()->subDays(60))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            '61_90_days_ksh' => DB::table('consolidated_billings')
                ->where('currency', 'KSH')
                ->where('due_date', '<', $now->copy()->subDays(60))
                ->where('due_date', '>=', $now->copy()->subDays(90))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),

            'over_90_days_ksh' => DB::table('consolidated_billings')
                ->where('currency', 'KSH')
                ->where('due_date', '<', $now->copy()->subDays(90))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')),
        ];
    }

    /**
     * Get collection trend for charts
     */
    private function getCollectionTrend($startDate, $endDate, $period)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Get payment data
        $payments = DB::table('consolidated_billings')
            ->select(
                DB::raw('DATE(payment_date) as payment_date'),
                DB::raw("SUM(CASE WHEN currency = 'USD' THEN paid_amount ELSE 0 END) as total_collected_usd"),
                DB::raw("SUM(CASE WHEN currency = 'KSH' THEN paid_amount ELSE 0 END) as total_collected_ksh"),
                DB::raw('COUNT(*) as payment_count')
            )
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partial'])
            ->where('paid_amount', '>', 0)
            ->groupBy(DB::raw('DATE(payment_date)'))
            ->orderBy(DB::raw('DATE(payment_date)'))
            ->get();

        $trendData = [];

        if ($period === 'daily') {
            // Group by day
            $currentDate = clone $start;
            while ($currentDate <= $end) {
                $dateKey = $currentDate->format('Y-m-d');
                $displayKey = $currentDate->format('M d');

                $payment = $payments->firstWhere('payment_date', $dateKey);

                $trendData[] = [
                    'period' => $displayKey,
                    'total_collected_usd' => $payment ? (float) $payment->total_collected_usd : 0,
                    'total_collected_ksh' => $payment ? (float) $payment->total_collected_ksh : 0,
                    'payment_count' => $payment ? (int) $payment->payment_count : 0,
                ];

                $currentDate->addDay();
            }
        } elseif ($period === 'weekly') {
            // Group by week
            $currentDate = clone $start;
            while ($currentDate <= $end) {
                $weekStart = clone $currentDate;
                $weekEnd = clone $currentDate;
                $weekEnd->addDays(6);
                $weekEnd->setTime(23, 59, 59);

                $displayKey = 'Week of ' . $weekStart->format('M d');

                $weeklyUsd = 0;
                $weeklyKsh = 0;
                $weeklyCount = 0;

                foreach ($payments as $payment) {
                    $paymentDate = Carbon::parse($payment->payment_date);
                    if ($paymentDate->between($weekStart, $weekEnd)) {
                        $weeklyUsd += $payment->total_collected_usd;
                        $weeklyKsh += $payment->total_collected_ksh;
                        $weeklyCount += $payment->payment_count;
                    }
                }

                $trendData[] = [
                    'period' => $displayKey,
                    'total_collected_usd' => $weeklyUsd,
                    'total_collected_ksh' => $weeklyKsh,
                    'payment_count' => $weeklyCount,
                ];

                $currentDate->addWeek();
            }
        } else {
            // Group by month
            $currentDate = clone $start;
            $currentDate->startOfMonth();

            while ($currentDate <= $end) {
                $monthStart = clone $currentDate;
                $monthEnd = clone $currentDate;
                $monthEnd->endOfMonth();

                $displayKey = $currentDate->format('M Y');

                $monthlyUsd = 0;
                $monthlyKsh = 0;
                $monthlyCount = 0;

                foreach ($payments as $payment) {
                    $paymentDate = Carbon::parse($payment->payment_date);
                    if ($paymentDate->between($monthStart, $monthEnd)) {
                        $monthlyUsd += $payment->total_collected_usd;
                        $monthlyKsh += $payment->total_collected_ksh;
                        $monthlyCount += $payment->payment_count;
                    }
                }

                $trendData[] = [
                    'period' => $displayKey,
                    'total_collected_usd' => $monthlyUsd,
                    'total_collected_ksh' => $monthlyKsh,
                    'payment_count' => $monthlyCount,
                ];

                $currentDate->addMonth();
            }
        }

        return $trendData;
    }

    /**
     * Get top performing customers (customers who paid the most)
     */
    private function getTopPerformingCustomers($startDate, $endDate)
    {
        // Get USD top performers
        $usdTopPerformers = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.company_name',
                DB::raw("'USD' as currency"),
                DB::raw('SUM(consolidated_billings.paid_amount) as payments_sum_amount'),
                DB::raw('COUNT(consolidated_billings.id) as payments_count')
            )
            ->where('users.role', 'customer')
            ->where('consolidated_billings.currency', 'USD')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->where('consolidated_billings.paid_amount', '>', 0)
            ->groupBy('users.id', 'users.name', 'users.email', 'users.company_name')
            ->orderByDesc('payments_sum_amount')
            ->limit(10)
            ->get();

        // Get KSH top performers
        $kshTopPerformers = DB::table('consolidated_billings')
            ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.company_name',
                DB::raw("'KSH' as currency"),
                DB::raw('SUM(consolidated_billings.paid_amount) as payments_sum_amount'),
                DB::raw('COUNT(consolidated_billings.id) as payments_count')
            )
            ->where('users.role', 'customer')
            ->where('consolidated_billings.currency', 'KSH')
            ->whereIn('consolidated_billings.status', ['paid', 'partial'])
            ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
            ->where('consolidated_billings.paid_amount', '>', 0)
            ->groupBy('users.id', 'users.name', 'users.email', 'users.company_name')
            ->orderByDesc('payments_sum_amount')
            ->limit(10)
            ->get();

        // Merge both collections
        return $usdTopPerformers->concat($kshTopPerformers);
    }

    /**
     * Get problematic customers (customers with highest overdue amounts)
     */
    private function getProblematicCustomers()
    {
        // Get USD problematic customers
        $usdProblematic = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.company_name',
                DB::raw('COUNT(DISTINCT consolidated_billings.id) as overdue_invoices_count'),
                DB::raw('SUM(consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)) as total_overdue_usd'),
                DB::raw('0 as total_overdue_ksh')
            )
            ->join('consolidated_billings', 'users.id', '=', 'consolidated_billings.user_id')
            ->where('users.role', 'customer')
            ->where('consolidated_billings.currency', 'USD')
            ->where('consolidated_billings.due_date', '<', now())
            ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue', 'partial'])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.company_name')
            ->having('total_overdue_usd', '>', 0)
            ->orderByDesc('total_overdue_usd')
            ->limit(20)
            ->get();

        // Get KSH problematic customers
        $kshProblematic = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.company_name',
                DB::raw('COUNT(DISTINCT consolidated_billings.id) as overdue_invoices_count'),
                DB::raw('0 as total_overdue_usd'),
                DB::raw('SUM(consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)) as total_overdue_ksh')
            )
            ->join('consolidated_billings', 'users.id', '=', 'consolidated_billings.user_id')
            ->where('users.role', 'customer')
            ->where('consolidated_billings.currency', 'KSH')
            ->where('consolidated_billings.due_date', '<', now())
            ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)')
            ->whereIn('consolidated_billings.status', ['pending', 'sent', 'overdue', 'partial'])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.company_name')
            ->having('total_overdue_ksh', '>', 0)
            ->orderByDesc('total_overdue_ksh')
            ->limit(20)
            ->get();

        // Add last payment date to USD customers
        foreach ($usdProblematic as $customer) {
            $lastPayment = DB::table('consolidated_billings')
                ->where('user_id', $customer->id)
                ->where('currency', 'USD')
                ->whereIn('status', ['paid', 'partial'])
                ->whereNotNull('payment_date')
                ->orderByDesc('payment_date')
                ->first();

            $customer->last_payment_date = $lastPayment ? $lastPayment->payment_date : null;
        }

        // Add last payment date to KSH customers
        foreach ($kshProblematic as $customer) {
            $lastPayment = DB::table('consolidated_billings')
                ->where('user_id', $customer->id)
                ->where('currency', 'KSH')
                ->whereIn('status', ['paid', 'partial'])
                ->whereNotNull('payment_date')
                ->orderByDesc('payment_date')
                ->first();

            $customer->last_payment_date = $lastPayment ? $lastPayment->payment_date : null;
        }

        // Merge and sort by total overdue (combining both currencies with approximate conversion)
        $merged = $usdProblematic->concat($kshProblematic);

        return $merged->sortByDesc(function($customer) {
            return ($customer->total_overdue_usd ?? 0) + (($customer->total_overdue_ksh ?? 0) / 130);
        })->values();
    }

    /**
     * Payment index
     */
    public function paymentIndex(Request $request)
    {
        $query = ConsolidatedBilling::with(['user', 'billingLineItems.lease.customer'])
            ->whereIn('status', ['paid', 'partial', 'pending', 'overdue'])
            ->orderBy('billing_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('billing_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('billing_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('billing_date', '<=', $request->date_to);
        }

        $payments = $query->paginate(20);
        return view('finance.debt.payments.index', compact('payments'));
    }

    /**
     * Payment edit
     */
    public function paymentEdit(ConsolidatedBilling $payment)
    {
        $payment->load(['user', 'billingLineItems.lease.customer']);
        return view('finance.debt.payments.edit', compact('payment'));
    }

    /**
     * Payment verify
     */
    public function paymentVerify(ConsolidatedBilling $payment)
    {
        try {
            $payment->update([
                'kra_status' => 'verified',
                'metadata' => array_merge(
                    (array) $payment->metadata,
                    [
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                    ]
                ),
            ]);

            return back()->with('success', 'Payment verified successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to verify payment: ' . $e->getMessage());
        }
    }

    /**
     * Payment search
     */
    public function paymentSearch(Request $request)
    {
        $query = ConsolidatedBilling::with(['user'])
            ->where('billing_number', 'like', "%{$request->q}%")
            ->orWhereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('email', 'like', "%{$request->q}%");
            })
            ->limit(10)
            ->get();

        return response()->json($query);
    }

    /**
     * Payment update
     */
    public function paymentUpdate(Request $request, $id)
    {
        try {
            $payment = ConsolidatedBilling::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:draft,pending,sent,paid,partial,overdue,cancelled',
                'paid_amount' => 'required|numeric|min:0|max:' . $payment->total_amount,
                'payment_date' => 'required|date',
                'payment_method' => 'nullable|string|max:50',
                'payment_reference' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $paidAmountUSD = (float) $validated['paid_amount'];

            $updateData = [
                'status' => $validated['status'],
                'paid_amount' => $paidAmountUSD,
                'payment_date' => $validated['payment_date'],
                'updated_at' => now(),
            ];

            if (!empty($validated['payment_method'])) {
                $metadata = $payment->metadata ?? [];
                $metadata['payment_method'] = $validated['payment_method'];
                $metadata['payment_reference'] = $validated['payment_reference'];
                $metadata['notes'] = $validated['notes'];
                $updateData['metadata'] = json_encode($metadata);
            }

            DB::table('consolidated_billings')
                ->where('id', $payment->id)
                ->update($updateData);

            DB::commit();

            return redirect()->route('finance.debt.payments.index')
                ->with('success', 'Payment updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update payment: ' . $e->getMessage());
        }
    }

    /**
     * Get invoices for payment plan
     */
    public function getInvoicesForPaymentPlan()
    {
        $invoices = ConsolidatedBilling::with('user')
            ->whereIn('status', ['pending', 'sent', 'overdue'])
            ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
            ->orderBy('due_date')
            ->get()
            ->map(function($invoice) {
                $outstanding = $invoice->total_amount - ($invoice->paid_amount ?? 0);
                return [
                    'id' => $invoice->id,
                    'billing_number' => $invoice->billing_number,
                    'customer_name' => $invoice->user->name ?? 'Unknown',
                    'currency' => $invoice->currency,
                    'total_amount' => $invoice->total_amount,
                    'outstanding' => $outstanding,
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                    'days_overdue' => $invoice->due_date && $invoice->due_date->isPast() ? $invoice->due_date->diffInDays(now()) : 0,
                ];
            });

        return response()->json($invoices);
    }

    /**
     * Get payment plan data for an invoice
     */
    public function getPaymentPlanData($id)
    {
        $invoice = ConsolidatedBilling::with('user')->findOrFail($id);

        $outstanding = $invoice->total_amount - ($invoice->paid_amount ?? 0);
        $daysOverdue = 0;

        if ($invoice->due_date && $invoice->due_date->isPast()) {
            $daysOverdue = $invoice->due_date->diffInDays(now());
        }

        return response()->json([
            'id' => $invoice->id,
            'billing_number' => $invoice->billing_number,
            'customer_name' => $invoice->user->name ?? 'Unknown',
            'currency' => $invoice->currency,
            'total_amount' => $invoice->total_amount,
            'outstanding' => $outstanding,
            'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
            'days_overdue' => $daysOverdue,
        ]);
    }

    /**
     * Create a payment plan
     */
    public function createPaymentPlan(Request $request, $id)
    {
        $request->validate([
            'installment_count' => 'required|integer|min:1|max:36',
            'frequency' => 'required|in:weekly,biweekly,monthly,quarterly',
            'down_payment' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $invoice = ConsolidatedBilling::with('user')->findOrFail($id);

            if ($invoice->status === 'paid') {
                throw new \Exception('Invoice is already paid.');
            }

            $outstandingAmount = $invoice->total_amount - ($invoice->paid_amount ?? 0);
            $downPayment = $request->down_payment ?? 0;

            if ($downPayment > $outstandingAmount) {
                throw new \Exception('Down payment cannot exceed outstanding amount.');
            }

            $remainingAmount = $outstandingAmount - $downPayment;
            $installmentCount = $request->installment_count;
            $installmentAmount = round($remainingAmount / $installmentCount, 2);

            $startDate = Carbon::parse($request->start_date);
            $endDate = $this->calculateEndDate($startDate, $installmentCount, $request->frequency);

            $paymentPlan = PaymentPlan::create([
                'consolidated_billing_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'total_amount' => $outstandingAmount,
                'down_payment' => $downPayment,
                'installment_count' => $installmentCount,
                'installment_amount' => $installmentAmount,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'frequency' => $request->frequency,
                'status' => 'active',
                'notes' => $request->notes,
                'metadata' => [
                    'created_by' => auth()->id(),
                    'created_at' => now()->toISOString(),
                    'original_invoice_number' => $invoice->billing_number,
                ]
            ]);

            $this->createInstallments($paymentPlan, $startDate, $installmentCount, $installmentAmount, $request->frequency);

            $invoice->update([
                'status' => 'payment_plan',
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'payment_plan_id' => $paymentPlan->id,
                    'payment_plan_created_at' => now()->toISOString(),
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment plan created successfully.',
                'payment_plan' => $paymentPlan->load('installments')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create payment plan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Calculate end date based on frequency
     */
    private function calculateEndDate($startDate, $installmentCount, $frequency)
    {
        $endDate = clone $startDate;

        switch ($frequency) {
            case 'weekly':
                $endDate->addWeeks($installmentCount - 1);
                break;
            case 'biweekly':
                $endDate->addWeeks(($installmentCount - 1) * 2);
                break;
            case 'quarterly':
                $endDate->addMonths(($installmentCount - 1) * 3);
                break;
            default:
                $endDate->addMonths($installmentCount - 1);
                break;
        }

        return $endDate;
    }

    /**
     * Create installments for payment plan
     */
    private function createInstallments($paymentPlan, $startDate, $installmentCount, $installmentAmount, $frequency)
    {
        $installments = [];

        for ($i = 1; $i <= $installmentCount; $i++) {
            switch ($frequency) {
                case 'weekly':
                    $dueDate = clone $startDate;
                    $dueDate->addWeeks($i - 1);
                    break;
                case 'biweekly':
                    $dueDate = clone $startDate;
                    $dueDate->addWeeks(($i - 1) * 2);
                    break;
                case 'quarterly':
                    $dueDate = clone $startDate;
                    $dueDate->addMonths(($i - 1) * 3);
                    break;
                default:
                    $dueDate = clone $startDate;
                    $dueDate->addMonths($i - 1);
                    break;
            }

            $installments[] = [
                'payment_plan_id' => $paymentPlan->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'due_date' => $dueDate,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        PaymentPlanInstallment::insert($installments);
    }

    /**
     * Get payment plan details
     */
    public function getPaymentPlanDetails($id)
    {
        $paymentPlan = PaymentPlan::with(['installments', 'invoice', 'customer'])
            ->findOrFail($id);

        return response()->json($paymentPlan);
    }

    /**
     * Cancel a payment plan
     */
    public function cancelPaymentPlan($id)
    {
        DB::beginTransaction();

        try {
            $paymentPlan = PaymentPlan::findOrFail($id);

            if ($paymentPlan->status !== 'active') {
                throw new \Exception('Only active payment plans can be cancelled.');
            }

            $paymentPlan->installments()
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            $paymentPlan->update(['status' => 'cancelled']);

            $paymentPlan->invoice->update(['status' => 'overdue']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment plan cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
