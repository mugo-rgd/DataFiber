<?php
// app/Http/Controllers/Finance/DebtManagementController.php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
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
    $result->collection_invoices = 0; // For backward compatibility

    return $result;
}

    private function getAgingAnalysis($currency = 'all')
{
    $query = DB::table('consolidated_billings')
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
        ->whereRaw('COALESCE(paid_amount, 0) < total_amount');

    if ($currency !== 'all') {
        $query->where('currency', $currency);
    }

    return $query->groupBy('age_bucket')
        ->orderByRaw("
            CASE age_bucket
                WHEN '0-30 days' THEN 1
                WHEN '31-60 days' THEN 2
                WHEN '61-90 days' THEN 3
                ELSE 4
            END
        ")
        ->get();
}

    private function getTopDebtors($limit = 10, $currency = 'all')
{
    $query = DB::table('consolidated_billings')
        ->join('users', 'consolidated_billings.user_id', '=', 'users.id')
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'consolidated_billings.currency',
            DB::raw('COUNT(consolidated_billings.id) as overdue_invoices'),
            DB::raw('SUM(consolidated_billings.total_amount - COALESCE(consolidated_billings.paid_amount, 0)) as total_outstanding'),
            DB::raw('MAX(DATEDIFF(CURDATE(), consolidated_billings.due_date)) as max_days_overdue'),
            DB::raw('AVG(DATEDIFF(CURDATE(), consolidated_billings.due_date)) as avg_days_overdue')
        )
        ->whereRaw('consolidated_billings.due_date < CURDATE()')
        ->whereRaw('COALESCE(consolidated_billings.paid_amount, 0) < consolidated_billings.total_amount');

    if ($currency !== 'all') {
        $query->where('consolidated_billings.currency', $currency);
    }

    return $query->groupBy('users.id', 'users.name', 'users.email', 'users.phone', 'consolidated_billings.currency')
        ->having('total_outstanding', '>', 0)
        ->orderBy('total_outstanding', 'desc')
        ->limit($limit)
        ->get();
}

    private function getPaymentTrend($currency = 'all')
{
    $query = DB::table('consolidated_billings')
        ->select(
            DB::raw('DATE_FORMAT(billing_date, "%Y-%m") as month'),
            DB::raw('currency'),
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

    return $query->groupBy(DB::raw('DATE_FORMAT(billing_date, "%Y-%m")'), 'currency')
        ->orderBy('month')
        ->get();
}

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

   public function overdueInvoices(Request $request)
{
    try {
        $currency = $request->get('currency', 'all');

        $query = ConsolidatedBilling::with('user')
            ->where('due_date', '<', now())
            ->whereRaw('COALESCE(paid_amount, 0) < total_amount');

        if ($currency !== 'all') {
            $query->where('currency', $currency);
        }

        $overdueBillings = $query->orderBy('due_date', 'asc')->get();

        return view('finance.debt.partials.overdue-invoices-table', [
            'overdueBillings' => $overdueBillings
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in overdueInvoices: ' . $e->getMessage());
        return view('finance.debt.partials.overdue-invoices-table', [
            'overdueBillings' => collect(),
            'error' => $e->getMessage()
        ]);
    }
}

    ///////////////////////////////////////////////////////////////////////////////////////////
public function dashboard(Request $request)
{
    $currency = $request->get('currency', 'all');

    if ($currency == 'all') {
        // Get data for both currencies separately
        $usdSummary = $this->getOverdueSummary('USD');
        $kshSummary = $this->getOverdueSummary('KSH');

        // Get aging analysis for both currencies
        $usdAging = $this->getAgingAnalysis('USD');
        $kshAging = $this->getAgingAnalysis('KSH');

        // Merge aging analysis for display
        $agingAnalysis = $this->mergeAgingAnalysis($usdAging, $kshAging);

        // Get top debtors for both currencies
        $topDebtors = $this->getTopDebtors(10, 'all');

        $overdueSummary = (object)[
            'overdue_invoices' => ($usdSummary->overdue_invoices ?? 0) + ($kshSummary->overdue_invoices ?? 0),
            'total_overdue_usd' => $usdSummary->total_overdue ?? 0,
            'total_overdue_ksh' => $kshSummary->total_overdue ?? 0,
            'avg_days_overdue' => max($usdSummary->avg_days_overdue ?? 0, $kshSummary->avg_days_overdue ?? 0),
            'collection_rate' => $this->calculateOverallCollectionRate(),
            'paid_invoices' => ($usdSummary->paid_invoices ?? 0) + ($kshSummary->paid_invoices ?? 0),
            'total_paid_usd' => $usdSummary->total_paid ?? 0,
            'total_paid_ksh' => $kshSummary->total_paid ?? 0,
        ];
    } else {
        // Single currency view
        $overdueSummary = $this->getOverdueSummary($currency);
        $agingAnalysis = $this->getAgingAnalysis($currency);
        $topDebtors = $this->getTopDebtors(10, $currency);
    }

    $paymentTrend = $this->getPaymentTrend($currency);
    $currencySummary = $this->getCurrencySummary();

    $overdueInvoices = ConsolidatedBilling::with('user')
        ->where('due_date', '<', now())
        ->whereRaw('COALESCE(paid_amount, 0) < total_amount')
        ->when($currency != 'all', function($query) use ($currency) {
            return $query->where('currency', $currency);
        })
        ->orderBy('due_date', 'asc')
        ->take(10)
        ->get()
        ->map(function($invoice) {
            $invoice->days_overdue = now()->diffInDays($invoice->due_date, false);
            return $invoice;
        });

    return view('finance.debt.dashboard', compact(
        'overdueSummary',
        'agingAnalysis',
        'topDebtors',
        'paymentTrend',
        'currencySummary',
        'overdueInvoices',
        'currency'
    ));
}

private function mergeAgingAnalysis($usdAging, $kshAging)
{
    $merged = [];

    // Process USD aging
    foreach ($usdAging as $bucket) {
        $merged[$bucket->age_bucket] = [
            'age_bucket' => $bucket->age_bucket,
            'invoice_count' => $bucket->invoice_count,
            'usd_amount' => $bucket->total_amount,
            'usd_paid' => $bucket->paid_amount,
            'usd_outstanding' => $bucket->outstanding,
            'ksh_amount' => 0,
            'ksh_paid' => 0,
            'ksh_outstanding' => 0,
            'total_invoices' => $bucket->invoice_count,
        ];
    }

    // Add KSH aging
    foreach ($kshAging as $bucket) {
        if (isset($merged[$bucket->age_bucket])) {
            $merged[$bucket->age_bucket]['ksh_amount'] = $bucket->total_amount;
            $merged[$bucket->age_bucket]['ksh_paid'] = $bucket->paid_amount;
            $merged[$bucket->age_bucket]['ksh_outstanding'] = $bucket->outstanding;
            $merged[$bucket->age_bucket]['total_invoices'] += $bucket->invoice_count;
            $merged[$bucket->age_bucket]['invoice_count'] += $bucket->invoice_count;
        } else {
            $merged[$bucket->age_bucket] = [
                'age_bucket' => $bucket->age_bucket,
                'invoice_count' => $bucket->invoice_count,
                'usd_amount' => 0,
                'usd_paid' => 0,
                'usd_outstanding' => 0,
                'ksh_amount' => $bucket->total_amount,
                'ksh_paid' => $bucket->paid_amount,
                'ksh_outstanding' => $bucket->outstanding,
                'total_invoices' => $bucket->invoice_count,
            ];
        }
    }

    // Sort by age bucket
    $order = ['0-30 days' => 1, '31-60 days' => 2, '61-90 days' => 3, '90+ days' => 4];
    uksort($merged, function($a, $b) use ($order) {
        return ($order[$a] ?? 5) <=> ($order[$b] ?? 5);
    });

    return array_values($merged);
}

private function calculateOverallCollectionRate()
{
    $totalBilled = DB::table('consolidated_billings')->sum('total_amount');
    $totalPaid = DB::table('consolidated_billings')->sum('paid_amount');

    return $totalBilled > 0 ? ($totalPaid / $totalBilled) * 100 : 0;
}


//////////////////////////////////////////////////////////////////////////////////////////////
    private function getRecentActivities()
    {
        // You might want to create an activities table for this
        return collect([]);
    }

    // Add to DebtManagementController.php

public function invoiceDetails($id)
{
    $invoice = ConsolidatedBilling::findOrFail($id);
    return view('finance.debt.invoice-details', compact('invoice'));
}
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

    // Calculate summary with currency breakdown
    $summary = DB::table(DB::raw("({$baseQuery->toSql()}) as filtered"))
        ->mergeBindings($baseQuery)
        ->select(
            // Overall totals
            DB::raw('SUM(outstanding) as total_outstanding'),
            DB::raw('COUNT(*) as total_invoices'),

            // USD totals
            DB::raw('SUM(CASE WHEN currency = "USD" THEN outstanding ELSE 0 END) as total_outstanding_usd'),
            DB::raw('COUNT(CASE WHEN currency = "USD" THEN 1 END) as total_invoices_usd'),

            // KSH totals
            DB::raw('SUM(CASE WHEN currency = "KSH" THEN outstanding ELSE 0 END) as total_outstanding_ksh'),
            DB::raw('COUNT(CASE WHEN currency = "KSH" THEN 1 END) as total_invoices_ksh'),

            // Current (0-30 days) - Overall
            DB::raw('SUM(CASE WHEN days_overdue <= 30 THEN outstanding ELSE 0 END) as current_amount'),
            DB::raw('COUNT(CASE WHEN days_overdue <= 30 THEN 1 END) as current_count'),

            // Current (0-30 days) - USD
            DB::raw('SUM(CASE WHEN days_overdue <= 30 AND currency = "USD" THEN outstanding ELSE 0 END) as current_amount_usd'),
            DB::raw('COUNT(CASE WHEN days_overdue <= 30 AND currency = "USD" THEN 1 END) as current_count_usd'),

            // Current (0-30 days) - KSH
            DB::raw('SUM(CASE WHEN days_overdue <= 30 AND currency = "KSH" THEN outstanding ELSE 0 END) as current_amount_ksh'),
            DB::raw('COUNT(CASE WHEN days_overdue <= 30 AND currency = "KSH" THEN 1 END) as current_count_ksh'),

            // 31-60 days - Overall
            DB::raw('SUM(CASE WHEN days_overdue > 30 AND days_overdue <= 60 THEN outstanding ELSE 0 END) as days_31_60_amount'),
            DB::raw('COUNT(CASE WHEN days_overdue > 30 AND days_overdue <= 60 THEN 1 END) as days_31_60_count'),

            // 31-60 days - USD
            DB::raw('SUM(CASE WHEN days_overdue > 30 AND days_overdue <= 60 AND currency = "USD" THEN outstanding ELSE 0 END) as days_31_60_amount_usd'),
            DB::raw('COUNT(CASE WHEN days_overdue > 30 AND days_overdue <= 60 AND currency = "USD" THEN 1 END) as days_31_60_count_usd'),

            // 31-60 days - KSH
            DB::raw('SUM(CASE WHEN days_overdue > 30 AND days_overdue <= 60 AND currency = "KSH" THEN outstanding ELSE 0 END) as days_31_60_amount_ksh'),
            DB::raw('COUNT(CASE WHEN days_overdue > 30 AND days_overdue <= 60 AND currency = "KSH" THEN 1 END) as days_31_60_count_ksh'),

            // 61-90 days - Overall
            DB::raw('SUM(CASE WHEN days_overdue > 60 AND days_overdue <= 90 THEN outstanding ELSE 0 END) as days_61_90_amount'),
            DB::raw('COUNT(CASE WHEN days_overdue > 60 AND days_overdue <= 90 THEN 1 END) as days_61_90_count'),

            // 61-90 days - USD
            DB::raw('SUM(CASE WHEN days_overdue > 60 AND days_overdue <= 90 AND currency = "USD" THEN outstanding ELSE 0 END) as days_61_90_amount_usd'),
            DB::raw('COUNT(CASE WHEN days_overdue > 60 AND days_overdue <= 90 AND currency = "USD" THEN 1 END) as days_61_90_count_usd'),

            // 61-90 days - KSH
            DB::raw('SUM(CASE WHEN days_overdue > 60 AND days_overdue <= 90 AND currency = "KSH" THEN outstanding ELSE 0 END) as days_61_90_amount_ksh'),
            DB::raw('COUNT(CASE WHEN days_overdue > 60 AND days_overdue <= 90 AND currency = "KSH" THEN 1 END) as days_61_90_count_ksh'),

            // 90+ days - Overall
            DB::raw('SUM(CASE WHEN days_overdue > 90 THEN outstanding ELSE 0 END) as days_90_plus_amount'),
            DB::raw('COUNT(CASE WHEN days_overdue > 90 THEN 1 END) as days_90_plus_count'),

            // 90+ days - USD
            DB::raw('SUM(CASE WHEN days_overdue > 90 AND currency = "USD" THEN outstanding ELSE 0 END) as days_90_plus_amount_usd'),
            DB::raw('COUNT(CASE WHEN days_overdue > 90 AND currency = "USD" THEN 1 END) as days_90_plus_count_usd'),

            // 90+ days - KSH
            DB::raw('SUM(CASE WHEN days_overdue > 90 AND currency = "KSH" THEN outstanding ELSE 0 END) as days_90_plus_amount_ksh'),
            DB::raw('COUNT(CASE WHEN days_overdue > 90 AND currency = "KSH" THEN 1 END) as days_90_plus_count_ksh')
        )
        ->first();

    // Get aging by customer with currency breakdown
    $agingByCustomer = DB::table(DB::raw("({$baseQuery->toSql()}) as filtered"))
        ->mergeBindings($baseQuery)
        ->select(
            'user_id as customer_id',
            'customer_name',
            DB::raw('COUNT(*) as invoices_count'),

            // Total amounts
            DB::raw('SUM(outstanding) as total_amount'),
            DB::raw('SUM(CASE WHEN currency = "USD" THEN outstanding ELSE 0 END) as total_amount_usd'),
            DB::raw('SUM(CASE WHEN currency = "KSH" THEN outstanding ELSE 0 END) as total_amount_ksh'),

            // Current amounts by currency
            DB::raw('SUM(CASE WHEN days_overdue <= 30 THEN outstanding ELSE 0 END) as current_amount'),
            DB::raw('SUM(CASE WHEN days_overdue <= 30 AND currency = "USD" THEN outstanding ELSE 0 END) as current_amount_usd'),
            DB::raw('SUM(CASE WHEN days_overdue <= 30 AND currency = "KSH" THEN outstanding ELSE 0 END) as current_amount_ksh'),

            // 31-60 days by currency
            DB::raw('SUM(CASE WHEN days_overdue > 30 AND days_overdue <= 60 THEN outstanding ELSE 0 END) as days_31_60_amount'),
            DB::raw('SUM(CASE WHEN days_overdue > 30 AND days_overdue <= 60 AND currency = "USD" THEN outstanding ELSE 0 END) as days_31_60_amount_usd'),
            DB::raw('SUM(CASE WHEN days_overdue > 30 AND days_overdue <= 60 AND currency = "KSH" THEN outstanding ELSE 0 END) as days_31_60_amount_ksh'),

            // 61-90 days by currency
            DB::raw('SUM(CASE WHEN days_overdue > 60 AND days_overdue <= 90 THEN outstanding ELSE 0 END) as days_61_90_amount'),
            DB::raw('SUM(CASE WHEN days_overdue > 60 AND days_overdue <= 90 AND currency = "USD" THEN outstanding ELSE 0 END) as days_61_90_amount_usd'),
            DB::raw('SUM(CASE WHEN days_overdue > 60 AND days_overdue <= 90 AND currency = "KSH" THEN outstanding ELSE 0 END) as days_61_90_amount_ksh'),

            // 90+ days by currency
            DB::raw('SUM(CASE WHEN days_overdue > 90 THEN outstanding ELSE 0 END) as days_90_plus_amount'),
            DB::raw('SUM(CASE WHEN days_overdue > 90 AND currency = "USD" THEN outstanding ELSE 0 END) as days_90_plus_amount_usd'),
            DB::raw('SUM(CASE WHEN days_overdue > 90 AND currency = "KSH" THEN outstanding ELSE 0 END) as days_90_plus_amount_ksh')
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

public function sendReminder($id)
{
    // Check permission
    if (!Auth::user()->can('send_payment_reminders')) {
        abort(403, 'Unauthorized access');
    }

    try {
        // Find the invoice
        $invoice = ConsolidatedBilling::with('user')->findOrFail($id);

        // Check if invoice is already paid
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice is already paid.');
        }

        // Calculate days overdue
        $daysOverdue = now()->diffInDays($invoice->due_date);

        // Determine reminder type based on days overdue
        $reminderType = $this->determineReminderType($daysOverdue);

        // Send the reminder
        $this->sendReminderNotification($invoice, $reminderType);

        // Log the action
        $this->logReminderAction($invoice, $reminderType);

        // Update invoice metadata if needed
        $metadata = $invoice->metadata ?? [];
        $reminders = $metadata['reminders'] ?? [];
        $reminders[] = [
            'sent_at' => now()->toISOString(),
            'type' => $reminderType,
            'sent_by' => Auth::id(),
            'days_overdue' => $daysOverdue
        ];

        $invoice->update([
            'metadata' => array_merge($metadata, ['reminders' => $reminders])
        ]);

        return back()->with('success', 'Reminder sent successfully.');

    } catch (\Exception $e) {
        \Log::error('Failed to send reminder: ' . $e->getMessage());
        return back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
    }
}

private function determineReminderType($daysOverdue)
{
    if ($daysOverdue <= 7) return 'friendly';
    if ($daysOverdue <= 30) return 'standard';
    if ($daysOverdue <= 60) return 'urgent';
    return 'final_notice';
}

private function sendReminderNotification($invoice, $reminderType)
{
    $customer = $invoice->user;
    $outstandingAmount = $invoice->total_amount - $invoice->paid_amount;

    // Email notification
    if ($customer->email) {
        \Mail::to($customer->email)->send(new \App\Mail\PaymentReminderMail(
            $invoice,
            $customer,
            $reminderType,
            $outstandingAmount
        ));
    }

    // SMS notification (if configured)
    if (config('services.sms.enabled') && $customer->phone) {
        $this->sendSMSReminder($customer->phone, $invoice, $reminderType);
    }

    // Log notification
    \Log::info("Reminder sent to customer {$customer->id} for invoice {$invoice->billing_number}");
}

private function sendSMSReminder($phone, $invoice, $reminderType)
{
    $messages = [
        'friendly' => "Friendly reminder: Invoice {$invoice->billing_number} is now due. Amount: {$invoice->total_amount} {$invoice->currency}",
        'standard' => "Reminder: Invoice {$invoice->billing_number} is overdue. Please pay to avoid service interruption.",
        'urgent' => "URGENT: Invoice {$invoice->billing_number} is seriously overdue. Immediate payment required.",
        'final_notice' => "FINAL NOTICE: Invoice {$invoice->billing_number} requires immediate payment to avoid collections."
    ];

    // Integrate with your SMS provider (Twilio, etc.)
    // Example: Twilio::sendSMS($phone, $messages[$reminderType]);
}

private function logReminderAction($invoice, $reminderType)
{
    // Log to debt_collection_actions table if it exists
    if (\Schema::hasTable('debt_collection_actions')) {
        DB::table('debt_collection_actions')->insert([
            'consolidated_billing_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'action_type' => 'reminder',
            'action_details' => json_encode([
                'reminder_type' => $reminderType,
                'sent_at' => now()->toISOString(),
                'sent_by' => Auth::id(),
                'method' => 'email_sms'
            ]),
            'assigned_to' => Auth::id(),
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
public function createPaymentPlan(Request $request, $id)
{
    // Check permission
    if (!Auth::user()->can('create_payment_plans')) {
        abort(403, 'Unauthorized access');
    }

    // Validate request
    $validated = $request->validate([
        'installment_count' => 'required|integer|min:1|max:24',
        'start_date' => 'required|date|after_or_equal:today',
        'down_payment' => 'nullable|numeric|min:0',
        'frequency' => 'required|in:weekly,biweekly,monthly,quarterly',
        'notes' => 'nullable|string|max:500',
        'terms' => 'nullable|string'
    ]);

    DB::beginTransaction();

    try {
        // Find the invoice
        $invoice = ConsolidatedBilling::with('user')->findOrFail($id);

        // Check if invoice is eligible for payment plan
        if ($invoice->status === 'paid') {
            throw new \Exception('Invoice is already paid.');
        }

        if ($invoice->status === 'written_off') {
            throw new \Exception('Invoice has been written off.');
        }

        if ($invoice->status === 'payment_plan') {
            throw new \Exception('Invoice already has an active payment plan.');
        }

        // Calculate outstanding amount
        $outstandingAmount = $invoice->total_amount - $invoice->paid_amount;

        // Validate down payment
        $downPayment = $request->down_payment ?? 0;
        if ($downPayment > $outstandingAmount) {
            throw new \Exception('Down payment cannot exceed outstanding amount.');
        }

        // Calculate remaining amount after down payment
        $remainingAmount = $outstandingAmount - $downPayment;

        // Calculate installment amount
        $installmentCount = $request->installment_count;
        $installmentAmount = round($remainingAmount / $installmentCount, 2);

        // Calculate end date based on frequency
        $startDate = Carbon::parse($request->start_date);
        $endDate = $this->calculateEndDate($startDate, $installmentCount, $request->frequency);

        // Create payment plan
        $paymentPlan = \App\Models\PaymentPlan::create([
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
            'terms' => $request->terms ?? $this->defaultTerms(),
            'notes' => $request->notes
        ]);

        // Create installments
        $this->createInstallments($paymentPlan, $startDate, $installmentCount, $installmentAmount, $request->frequency);

        // Update invoice status
        $invoice->update([
            'status' => 'payment_plan',
            'metadata' => array_merge(
                $invoice->metadata ?? [],
                [
                    'payment_plan_id' => $paymentPlan->id,
                    'payment_plan_created_at' => now()->toISOString(),
                    'payment_plan_created_by' => Auth::id()
                ]
            )
        ]);

        // Log the action
        $this->logPaymentPlanAction($invoice, $paymentPlan);

        // Notify customer
        $this->notifyPaymentPlanCreated($invoice, $paymentPlan);

        DB::commit();

        return back()->with('success', 'Payment plan created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Failed to create payment plan: ' . $e->getMessage());
        return back()->with('error', 'Failed to create payment plan: ' . $e->getMessage());
    }
}

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
        case 'monthly':
            $endDate->addMonths($installmentCount - 1);
            break;
        case 'quarterly':
            $endDate->addMonths(($installmentCount - 1) * 3);
            break;
    }

    return $endDate;
}

private function createInstallments($paymentPlan, $startDate, $installmentCount, $installmentAmount, $frequency)
{
    $installments = [];
    $currentDate = clone $startDate;

    for ($i = 1; $i <= $installmentCount; $i++) {
        // Calculate due date for this installment
        $dueDate = clone $currentDate;

        switch ($frequency) {
            case 'weekly':
                $dueDate->addWeeks($i - 1);
                break;
            case 'biweekly':
                $dueDate->addWeeks(($i - 1) * 2);
                break;
            case 'monthly':
                $dueDate->addMonths($i - 1);
                break;
            case 'quarterly':
                $dueDate->addMonths(($i - 1) * 3);
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

    \App\Models\PaymentPlanInstallment::insert($installments);
}

private function defaultTerms()
{
    return "1. All payments must be made on or before the due date.
2. A late fee of 5% may be applied to any overdue installment.
3. Failure to make two consecutive payments may result in plan termination.
4. The creditor reserves the right to take legal action for non-payment.
5. Early payment of the full outstanding amount is accepted without penalty.";
}

private function logPaymentPlanAction($invoice, $paymentPlan)
{
    if (\Schema::hasTable('debt_collection_actions')) {
        DB::table('debt_collection_actions')->insert([
            'consolidated_billing_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'action_type' => 'payment_plan',
            'action_details' => json_encode([
                'payment_plan_id' => $paymentPlan->id,
                'installment_count' => $paymentPlan->installment_count,
                'installment_amount' => $paymentPlan->installment_amount,
                'total_amount' => $paymentPlan->total_amount,
                'created_at' => now()->toISOString(),
                'created_by' => Auth::id()
            ]),
            'assigned_to' => Auth::id(),
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

private function notifyPaymentPlanCreated($invoice, $paymentPlan)
{
    $customer = $invoice->user;

    if ($customer->email) {
        \Mail::to($customer->email)->send(new \App\Mail\PaymentPlanCreatedMail(
            $invoice,
            $paymentPlan,
            $customer
        ));
    }

    // Also send installment schedule
    $installments = $paymentPlan->installments;
    $schedule = $installments->map(function($installment) {
        return [
            'number' => $installment->installment_number,
            'due_date' => $installment->due_date->format('M d, Y'),
            'amount' => $installment->amount
        ];
    });

    \Log::info("Payment plan created for invoice {$invoice->billing_number}", [
        'customer' => $customer->email,
        'installments' => $schedule->toArray()
    ]);
}
// Get invoices eligible for payment plans
public function getInvoicesForPaymentPlan()
{
    $invoices = ConsolidatedBilling::with('user')
        ->whereIn('status', ['pending', 'sent', 'overdue'])
        ->whereRaw('total_amount > paid_amount')
        ->where('due_date', '<', now())
        ->orderBy('due_date')
        ->get();

    return response()->json($invoices);
}

// Get payment plan details
public function getPaymentPlanDetails($id)
{
    $paymentPlan = \App\Models\PaymentPlan::with(['installments', 'invoice.user'])
        ->findOrFail($id);

    return response()->json($paymentPlan);
}

// Cancel payment plan
public function cancelPaymentPlan($id)
{
    DB::beginTransaction();

    try {
        $paymentPlan = \App\Models\PaymentPlan::findOrFail($id);

        // Cancel installments
        $paymentPlan->installments()->update(['status' => 'cancelled']);

        // Update payment plan
        $paymentPlan->update(['status' => 'cancelled']);

        // Update invoice status back to overdue
        $paymentPlan->invoice->update(['status' => 'overdue']);

        // Log the action
        $this->logPaymentPlanCancellation($paymentPlan);

        DB::commit();

        return back()->with('success', 'Payment plan cancelled successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to cancel payment plan: ' . $e->getMessage());
    }
}

// Record payment against installment
public function recordInstallmentPayment($installmentId, Request $request)
{
    $validated = $request->validate([
        'amount' => 'required|numeric|min:0',
        'payment_date' => 'required|date',
        'payment_method' => 'required|string',
        'reference' => 'nullable|string'
    ]);

    DB::beginTransaction();

    try {
        $installment = \App\Models\PaymentPlanInstallment::with('paymentPlan.invoice')->findOrFail($installmentId);

        $paidAmount = $installment->paid_amount + $request->amount;
        $isFullyPaid = $paidAmount >= $installment->amount;

        // Update installment
        $installment->update([
            'paid_amount' => $paidAmount,
            'paid_date' => $isFullyPaid ? $request->payment_date : null,
            'status' => $isFullyPaid ? 'paid' : 'partial'
        ]);

        // Update invoice paid amount
        $invoice = $installment->paymentPlan->invoice;
        $invoice->increment('paid_amount', $request->amount);

        // Check if all installments are paid
        $this->checkPaymentPlanCompletion($installment->paymentPlan);

        // Log payment
        $this->logPayment($installment, $request);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'installment' => $installment
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to record payment: ' . $e->getMessage()
        ], 500);
    }
}

private function checkPaymentPlanCompletion($paymentPlan)
{
    $unpaidInstallments = $paymentPlan->installments()
        ->where('status', '!=', 'paid')
        ->count();

    if ($unpaidInstallments === 0) {
        // All installments paid
        $paymentPlan->update(['status' => 'completed']);

        // Update invoice status
        $invoice = $paymentPlan->invoice;
        if ($invoice->paid_amount >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        }

        // Notify customer
        $this->notifyPaymentPlanCompleted($paymentPlan);
    }
}
  /**
     * Display collection performance report
     *
     * @return \Illuminate\View\View
     */
    public function collectionReport(Request $request)
    {
        // Get date filters
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $period = $request->get('period', 'daily'); // daily, weekly, monthly

        // Collection summary
        $collectionSummary = $this->getCollectionSummary($startDate, $endDate);

        // Collection performance by collector/account manager
        $collectorPerformance = $this->getCollectorPerformance($startDate, $endDate);

        // Aging collection analysis
        $agingCollection = $this->getAgingCollectionAnalysis();

        // Collection trend
        $collectionTrend = $this->getCollectionTrend($startDate, $endDate, $period);

        // Top performing customers (paying on time)
        $topPerformingCustomers = $this->getTopPerformingCustomers($startDate, $endDate);

        // Problematic customers (frequently overdue)
        $problematicCustomers = $this->getProblematicCustomers($startDate, $endDate);

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
     * Get collection summary
     */
    private function getCollectionSummary($startDate, $endDate)
    {
        return [
            'total_collected' => ConsolidatedBilling::whereBetween('payment_date', [$startDate, $endDate])
                ->where('status', 'paid')
                ->sum('paid_amount'),

            'total_invoiced' => ConsolidatedBilling::whereBetween('billing_date', [$startDate, $endDate])
                ->sum('total_amount'),

            'collection_rate' => $this->calculateCollectionRate($startDate, $endDate),

            'average_collection_period' => $this->calculateAverageCollectionPeriod(),

            'overdue_collected' => ConsolidatedBilling::whereBetween('payment_date', [$startDate, $endDate])
                ->where('status', 'paid')
                ->whereHas('billingLineItems', function($q) {
                    $q->where('status', 'overdue');
                })
                ->sum('total_amount'),
        ];
    }

    /**
     * Calculate collection rate
     */
    private function calculateCollectionRate($startDate, $endDate)
    {
        $totalInvoiced = ConsolidatedBilling::whereBetween('billing_date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalCollected = ConsolidatedBilling::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('paid_amount');

        if ($totalInvoiced > 0) {
            return ($totalCollected / $totalInvoiced) * 100;
        }

        return 0;
    }

    /**
     * Calculate average collection period
     */
    private function calculateAverageCollectionPeriod()
    {
        $payments = ConsolidatedBilling::where('status', 'paid')
        ->whereNotNull('billing_number')
        ->get();

        $totalDays = 0;
        $count = 0;

        foreach ($payments as $payment) {
            if ($payment->billing && $payment->billing->due_date) {
                $dueDate = Carbon::parse($payment->billing->due_date);
                $paymentDate = Carbon::parse($payment->payment_date);

                // Days between due date and payment date
                $days = $paymentDate->diffInDays($dueDate);
                $totalDays += $days;
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : 0;
    }

    /**
     * Get collector performance
     */
  private function getCollectorPerformance($startDate, $endDate)
{
    try {
        $results = \DB::select("
            SELECT
                u.id,
                u.name,
                COUNT(DISTINCT c.id) as total_assigned,
                COALESCE(SUM(p.total_amount), 0) as collected_amount,
                COALESCE(SUM(bli.total_amount), 0) as total_billed,
                CASE
                    WHEN COALESCE(SUM(bli.total_amount), 0) > 0
                    THEN ROUND((COALESCE(SUM(p.total_amount), 0) / SUM(bli.total_amount)) * 100, 2)
                    ELSE 0
                END as collection_rate
            FROM users u
            LEFT JOIN customers c ON u.id = c.account_manager_id
            LEFT JOIN payments p ON c.id = p.customer_id
                AND p.payment_date BETWEEN ? AND ?
                AND p.status = 'completed'
            LEFT JOIN billing_line_items bli ON c.id = bli.customer_id
                AND bli.billing_date BETWEEN ? AND ?
            WHERE u.role = 'account_manager'
            GROUP BY u.id, u.name
        ", [$startDate, $endDate, $startDate, $endDate]);

        // Return as arrays for Blade compatibility
        return collect($results)->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'total_assigned' => (int) $item->total_assigned,
                'collected_amount' => (float) $item->collected_amount,
                'collection_rate' => (float) $item->collection_rate
            ];
        });

    } catch (\Exception $e) {
        // Fallback to simpler query if above fails
        return $this->getCollectorPerformanceSimple($startDate, $endDate);
    }
}

public function customers()
{
    // Get all consolidated billings with user/customer information
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
        ->whereNotIn('consolidated_billings.status', ['paid', 'cancelled', 'tev_duplicate', 'tev_failed'])
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
private function getCollectorPerformanceSimple($startDate, $endDate)
{
    $results = \DB::table('users as a')
        ->select([
            'a.id',
            'a.name',
            \DB::raw('COUNT(DISTINCT c.id) as total_assigned'),
            \DB::raw('COALESCE(SUM(consolidated_billings.paid_amount), 0) as collected_amount'),
            \DB::raw('0 as collection_rate')
        ])
        ->leftJoin('users as c', function($join) {
            $join->on('a.id', '=', 'c.account_manager_id')
                 ->where('c.role', 'customer'); // assuming customers have role 'customer'
        })
        ->leftJoin('consolidated_billings', function($join) use ($startDate, $endDate) {
            $join->on('c.id', '=', 'consolidated_billings.user_id')
                ->whereBetween('consolidated_billings.payment_date', [$startDate, $endDate])
                ->where('consolidated_billings.status', 'paid');
        })
        ->where('a.role', 'account_manager')
        ->groupBy('a.id', 'a.name')
        ->get();

    // Convert to arrays
    return $results->map(function($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'total_assigned' => $item->total_assigned,
            'collected_amount' => $item->collected_amount,
            'collection_rate' => $item->collection_rate
        ];
    });
}

    /**
     * Get aging collection analysis
     */
    private function getAgingCollectionAnalysis()
    {
        $now = now();

        return [
            'current' => ConsolidatedBilling::where('status', 'current')
                ->where('due_date', '>=', $now)
                ->sum('total_amount'),

            '1_30_days' => ConsolidatedBilling::where('status', 'overdue')
                ->where('due_date', '>=', $now->subDays(30))
                ->sum('total_amount'),

            '31_60_days' => ConsolidatedBilling::where('status', 'overdue')
                ->where('due_date', '<', $now->subDays(30))
                ->where('due_date', '>=', $now->subDays(60))
                ->sum('total_amount'),

            '61_90_days' => ConsolidatedBilling::where('status', 'overdue')
                ->where('due_date', '<', $now->subDays(60))
                ->where('due_date', '>=', $now->subDays(90))
                ->sum('total_amount'),

            'over_90_days' => ConsolidatedBilling::where('status', 'overdue')
                ->where('due_date', '<', $now->subDays(90))
                ->sum('total_amount'),
        ];
    }

    /**
     * Get collection trend
     */
    private function getCollectionTrend($startDate, $endDate, $period)
    {
        $payments = ConsolidatedBilling::select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('SUM(paid_amount) as total_collected'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Group by period
        $trendData = [];
        foreach ($payments as $payment) {
            $date = Carbon::parse($payment->date);

            if ($period === 'daily') {
                $key = $date->format('Y-m-d');
            } elseif ($period === 'weekly') {
                $key = $date->startOfWeek()->format('Y-m-d');
            } else { // monthly
                $key = $date->format('Y-m');
            }

            if (!isset($trendData[$key])) {
                $trendData[$key] = [
                    'period' => $key,
                    'total_collected' => 0,
                    'payment_count' => 0,
                ];
            }

            $trendData[$key]['total_collected'] += $payment->total_collected;
            $trendData[$key]['payment_count'] += $payment->payment_count;
        }

        return array_values($trendData);
    }

    /**
     * Get top performing customers
     */
    private function getTopPerformingCustomers($startDate, $endDate)
    {
        return User::where('role', 'customer')
            ->whereHas('payments', function($query) use ($startDate, $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate])
                      ->where('status', 'completed');
            })
            ->withSum(['payments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate])
                      ->where('status', 'completed');
            }], 'amount')
            ->withCount(['payments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate])
                      ->where('status', 'completed');
            }])
            ->orderByDesc('payments_sum_amount')
            ->take(10)
            ->get();
    }

    /**
     * Get problematic customers
     */
    private function getProblematicCustomers($startDate, $endDate)
    {
        return User::where('role', 'customer')
            ->whereHas('billings', function($query) {
                $query->where('status', 'overdue');
            })
            ->withCount(['billings' => function($query) {
                $query->where('status', 'overdue');
            }])
            ->withSum(['billings' => function($query) {
                $query->where('status', 'overdue');
            }], 'total_amount')
            ->orderByDesc('billings_sum_total_amount')
            ->take(10)
            ->get();
    }

///******************************************************************************* */
/**
 * Display a listing of payments.
 */
public function paymentIndex(Request $request)
{
    $query = ConsolidatedBilling::with(['user', 'billingLineItems.lease.customer'])
        ->whereIn('status', ['paid','partial', 'pending', 'overdue'])
        ->orderBy('billing_date', 'desc');

    // Search filters
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
 * Show the form for editing a payment.
 */
public function paymentEdit(ConsolidatedBilling $payment)
{
    // // Check if user has finance role
    // if (!auth()->user()->hasRole('finance')) {
    //     abort(403, 'Unauthorized action.');
    // }

    $payment->load(['user', 'billingLineItems.lease.customer']);

    return view('finance.debt.payments.edit', compact('payment'));
}

/**
 * Calculate payment summary.
 */
private function calculatePaymentSummary(ConsolidatedBilling $payment)
{
    $totalKES = $payment->total_amount_kes ?? $payment->total_amount;
    $paidKES = $payment->paid_amount_kes ?? $payment->paid_amount ?? 0;
    $balanceKES = $totalKES - $paidKES;

    // Calculate line item totals
    $lineItemsTotal = $payment->billingLineItems()->sum('amount');
    $lineItemsPaid = $payment->billingLineItems()->sum('paid_amount');

    return [
        'total_kes' => $totalKES,
        'paid_kes' => $paidKES,
        'balance_kes' => $balanceKES,
        'line_items_total' => $lineItemsTotal,
        'line_items_paid' => $lineItemsPaid,
        'payment_percentage' => $totalKES > 0 ? ($paidKES / $totalKES) * 100 : 0,
    ];
}
/**
 * Verify a payment.
 */
public function paymentVerify(ConsolidatedBilling $payment)
{
    // if (!auth()->user()->hasRole('finance')) {
    //     abort(403, 'Unauthorized action.');
    // }

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

        activity()
            ->causedBy(auth()->user())
            ->performedOn($payment)
            ->log('Payment verified by finance team');

        return back()->with('success', 'Payment verified successfully.');

    } catch (\Exception $e) {
        return back()->with('error', 'Failed to verify payment: ' . $e->getMessage());
    }
}

/**
 * Search for payments.
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

/////////////////////////////
/**
 * Update the specified payment.
 */
public function paymentUpdate(Request $request, $id)
{
    \Log::info('=== PAYMENT UPDATE STARTED ===', $request->all());

    try {
        // Find the payment
        $payment = ConsolidatedBilling::findOrFail($id);

        \Log::info('BEFORE UPDATE - Payment:', [
            'id' => $payment->id,
            'billing_number' => $payment->billing_number,
            'current_paid_amount' => $payment->paid_amount,
            'current_status' => $payment->status,
            'current_payment_date' => $payment->payment_date,
            'metadata' => $payment->metadata
        ]);

        $validated = $request->validate([
            'status' => 'required|in:draft,pending,sent,paid,partial,overdue,cancelled',
            'paid_amount' => 'required|numeric|min:0|max:' . $payment->total_amount,
            'paid_amount_kes' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'update_line_items' => 'sometimes|boolean',
        ]);

        \DB::beginTransaction();

        // Calculate amounts
        $paidAmountUSD = (float) $validated['paid_amount'];
        $paidAmountKES = isset($validated['paid_amount_kes']) ? (float) $validated['paid_amount_kes'] : null;

        // Handle currency conversion if needed
        if ($payment->currency === 'USD' && isset($payment->metadata['exchange_rate'])) {
            $exchangeRate = (float) $payment->metadata['exchange_rate'];

            // If KES is provided but USD is not (or USD is 0), calculate USD from KES
            if ($paidAmountKES && $paidAmountKES > 0 && (!$paidAmountUSD || $paidAmountUSD == 0)) {
                $paidAmountUSD = $paidAmountKES / $exchangeRate;
            }
            // If USD is provided but KES is not, calculate KES from USD
            elseif ($paidAmountUSD && $paidAmountUSD > 0 && !$paidAmountKES) {
                $paidAmountKES = $paidAmountUSD * $exchangeRate;
            }
        }

        \Log::info('Calculated amounts:', [
            'paidAmountUSD' => $paidAmountUSD,
            'paidAmountKES' => $paidAmountKES
        ]);

        // Prepare metadata update
        $metadata = $payment->metadata ?? [];
        $metadata = array_merge($metadata, [
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'],
            'notes' => $validated['notes'],
            'updated_by' => auth()->id(),
            'updated_at' => now(),
            'manual_update' => true,
        ]);

        if ($paidAmountKES) {
            $metadata['paid_amount_kes'] = $paidAmountKES;
        }
$calculatedStatus = $validated['status'];

// If user selected 'paid' or we're making a payment, auto-calculate
if ($validated['status'] === 'paid' || $paidAmountUSD > 0) {
    // Check if payment covers the full amount (with a small tolerance)
    if (abs($payment->total_amount - $paidAmountUSD) < 0.01) {
        $calculatedStatus = 'paid';
    } else if ($paidAmountUSD > 0) {
        $calculatedStatus = 'partial';
    } else {
        $calculatedStatus = 'pending';
    }
}
        // TRY DIRECT DB UPDATE INSTEAD OF MODEL UPDATE
        $updateData = [
            'status' => $calculatedStatus,
            'paid_amount' => $paidAmountUSD,
            'payment_date' => $validated['payment_date'],
            'metadata' => json_encode($metadata), // Convert to JSON
            'updated_at' => now(),
        ];

        \Log::info('Update data prepared:', $updateData);

        // Method 1: Try direct DB update
        $updatedRows = \DB::table('consolidated_billings')
            ->where('id', $payment->id)
            ->update($updateData);

        \Log::info('Direct DB update result:', ['updated_rows' => $updatedRows]);

        if ($updatedRows === 0) {
            \Log::warning('No rows were updated with direct DB query');

            // Method 2: Try model update with fillable
            $payment->fill($updateData);
            $saved = $payment->save();
            \Log::info('Model save result:', ['saved' => $saved]);
        }

        // Refresh to get updated values
        $payment->refresh();

        \Log::info('AFTER UPDATE - Payment:', [
            'id' => $payment->id,
            'billing_number' => $payment->billing_number,
            'paid_amount' => $payment->paid_amount,
            'status' => $payment->status,
            'payment_date' => $payment->payment_date,
            'metadata' => $payment->metadata
        ]);

        // Update billing line items
        if ($validated['status'] === 'paid' && $paidAmountUSD > 0) {
            // Fix the method call - updateBillingLineItems expects 3 parameters
            $this->updateBillingLineItems($payment, $paidAmountKES ?? $paidAmountUSD, $paidAmountUSD);
        } else {
            $payment->billingLineItems()->update(['paid_amount' => null, 'updated_at' => now()]);
        }

        // Update tax compliance
        if ($validated['status'] === 'paid') {
            $this->updateTaxComplianceStatus($payment);
        }

        \DB::commit();

        \Log::info('=== PAYMENT UPDATE COMPLETED SUCCESSFULLY ===');

        // Send notification if requested
        if ($request->has('send_notification')) {
            $this->sendPaymentNotification($payment);
        }

        return redirect()->route('finance.debt.payments')
            ->with('success', 'Payment updated successfully.');

    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Payment update failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to update payment: ' . $e->getMessage());
    }
}

/**
 * Update billing line items proportionally based on paid amount.
 */
/**
 * Update billing line items proportionally based on paid amount.
 */
private function updateBillingLineItems(ConsolidatedBilling $payment, $paidAmountKES, $paidAmountUSD)
{
    \Log::info('Updating billing line items:', [
        'payment_id' => $payment->id,
        'paidAmountKES' => $paidAmountKES,
        'paidAmountUSD' => $paidAmountUSD,
        'total_amount' => $payment->total_amount
    ]);

    // Get all line items for this billing
    $lineItems = $payment->billingLineItems()->get();

    if ($lineItems->isEmpty()) {
        \Log::info('No line items found for payment: ' . $payment->id);
        return;
    }

    // Calculate payment ratio based on USD
    $totalAmountUSD = $payment->total_amount;
    $paymentRatioUSD = $totalAmountUSD > 0 ? ($paidAmountUSD / $totalAmountUSD) : 0;

    \Log::info('Payment ratio calculated:', [
        'totalAmountUSD' => $totalAmountUSD,
        'paidAmountUSD' => $paidAmountUSD,
        'paymentRatioUSD' => $paymentRatioUSD
    ]);

    foreach ($lineItems as $item) {
        // Calculate proportional paid amount for this line item
        $itemPaidAmountUSD = $item->amount * $paymentRatioUSD;

        // Update the line item using direct DB update
        $updated = \DB::table('billing_line_items')
            ->where('id', $item->id)
            ->update([
                'paid_amount' => $itemPaidAmountUSD,
                'updated_at' => now(),
            ]);

        \Log::info('Line item updated:', [
            'line_item_id' => $item->id,
            'original_amount' => $item->amount,
            'paid_amount' => $itemPaidAmountUSD,
            'updated' => $updated
        ]);
    }
}

/**
 * Update tax compliance status.
 */
private function updateTaxComplianceStatus(ConsolidatedBilling $payment)
{
    // Update KRA status if payment is marked as paid
    if ($payment->kra_status === 'pending') {
        $payment->update([
            'kra_status' => 'verified',
            'metadata' => array_merge(
                (array) $payment->metadata,
                [
                    'kra_verified_by' => auth()->id(),
                    'kra_verified_at' => now(),
                ]
            ),
        ]);
    }

    // Update TEV status if applicable
    if ($payment->tevin_status === 'pending') {
        $payment->update([
            'tevin_status' => 'committed',
            'tevin_committed_at' => now(),
            'tev_committed_timestamp' => now(),
        ]);
    }
}

/**
 * Send payment notification to customer.
 */
private function sendPaymentNotification(ConsolidatedBilling $payment)
{
    try {
        $customer = $payment->user;

        if ($customer && $customer->email) {
            // Send email notification
            \Mail::to($customer->email)->send(new \App\Mail\PaymentUpdatedNotification($payment));

            // Log the notification
            activity()
                ->causedBy(auth()->user())
                ->performedOn($payment)
                ->log('Payment notification sent to customer');
        }
    } catch (\Exception $e) {
        // Log error but don't fail the whole transaction
        \Log::error('Failed to send payment notification: ' . $e->getMessage());
    }
}
}
