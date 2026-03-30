<?php

namespace App\Services;

use App\Models\LeaseBilling;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    public function getRevenueTrends($months = 6): array
    {
        $monthsData = [];
        $revenues = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthsData[] = $date->format('M Y');

            $revenue = LeaseBilling::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('total_amount');

            $revenues[] = $revenue ?? 0;
        }

        return [
            'months' => $monthsData,
            'revenues' => $revenues
        ];
    }

    public function generateFinancialSummary($startDate, $endDate): array
    {
        // Total Revenue
        $totalRevenue = LeaseBilling::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Pending Billings
        $pendingBillings = LeaseBilling::where('status', 'pending')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->get();

        $pendingAmount = $pendingBillings->sum('total_amount');

        // Overdue Billings
        $overdueBillings = LeaseBilling::where('status', 'overdue')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->get();

        $overdueAmount = $overdueBillings->sum('total_amount');

        // Revenue by Billing Cycle
        $revenueByType = LeaseBilling::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw('billing_cycle, SUM(total_amount) as revenue, COUNT(*) as count')
            ->groupBy('billing_cycle')
            ->get();

        // Monthly Revenue Trend (last 6 months)
        $monthlyTrend = LeaseBilling::where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, SUM(total_amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'pending_invoices' => $pendingBillings->count(),
            'pending_amount' => $pendingAmount,
            'overdue_invoices' => $overdueBillings->count(),
            'overdue_amount' => $overdueAmount,
            'revenue_by_type' => $revenueByType,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    public function getTopCustomers($limit = 10)
    {
        return DB::table('lease_billings')
            ->join('users', 'lease_billings.customer_id', '=', 'users.id')
            ->where('lease_billings.status', 'paid')
            ->select('users.name', 'users.email', DB::raw('SUM(lease_billings.total_amount) as total_revenue'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTransactionStats(): array
    {
        return [
            'total_income' => Transaction::where('type', 'income')->where('status', 'completed')->sum('amount'),
            'total_expenses' => Transaction::where('type', 'expense')->where('status', 'completed')->sum('amount'),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'net_cash_flow' => Transaction::where('status', 'completed')
                ->sum(DB::raw('CASE WHEN type = "income" THEN amount ELSE -amount END'))
        ];
    }

    public function generateRevenueAnalysis($startDate, $endDate): array
    {
        // Revenue by Customer
        $revenueByCustomer = LeaseBilling::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->with('customer')
            ->selectRaw('customer_id, SUM(total_amount) as revenue, COUNT(*) as billing_count')
            ->groupBy('customer_id')
            ->orderBy('revenue', 'desc')
            ->get();

        // Revenue by Billing Cycle
        $revenueByCycle = LeaseBilling::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw('billing_cycle, SUM(total_amount) as revenue, COUNT(*) as count')
            ->groupBy('billing_cycle')
            ->orderBy('revenue', 'desc')
            ->get();

        // Payment methods
        $paymentMethods = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('payment_method')
            ->selectRaw('payment_method, SUM(amount) as amount, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        return [
            'revenue_by_customer' => $revenueByCustomer,
            'revenue_by_service' => $revenueByCycle,
            'payment_methods' => $paymentMethods,
        ];
    }

    public function generateCustomerBillingReport($startDate, $endDate): array
    {
        $customerBilling = LeaseBilling::with('customer')
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->selectRaw('customer_id,
                COUNT(*) as total_billings,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status = "pending" THEN total_amount ELSE 0 END) as pending_amount,
                SUM(CASE WHEN status = "overdue" THEN total_amount ELSE 0 END) as overdue_amount')
            ->groupBy('customer_id')
            ->orderBy('paid_amount', 'desc')
            ->get();

        return [
            'customer_billing' => $customerBilling,
        ];
    }
}
