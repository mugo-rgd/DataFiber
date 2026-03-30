@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Financial Reports & Analytics
                    </h5>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print Report
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="exportBtn">
                            <i class="fas fa-download me-1"></i>Export Data
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Report Filters -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('finance.reports') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="report_type" class="form-label">Report Type</label>
                                    <select name="report_type" class="form-select" id="report_type" onchange="this.form.submit()">
                                        <option value="financial_summary" {{ $reportType == 'financial_summary' ? 'selected' : '' }}>Financial Summary</option>
                                        <option value="revenue_analysis" {{ $reportType == 'revenue_analysis' ? 'selected' : '' }}>Revenue Analysis</option>
                                        <option value="customer_billing" {{ $reportType == 'customer_billing' ? 'selected' : '' }}>Customer Billing</option>
                                        <option value="aging_report" {{ $reportType == 'aging_report' ? 'selected' : '' }}>Aging Report</option>
                                        <option value="debt_aging" {{ $reportType == 'debt_aging' ? 'selected' : '' }}>Debt Aging Analysis</option>
                                        <option value="cash_flow" {{ $reportType == 'cash_flow' ? 'selected' : '' }}>Cash Flow Statement</option>
                                        <option value="profitability" {{ $reportType == 'profitability' ? 'selected' : '' }}>Profitability Analysis</option>
                                        <option value="tax_report" {{ $reportType == 'tax_report' ? 'selected' : '' }}>Tax Report</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="period" class="form-label">Period</label>
                                    <select name="period" class="form-select" id="period" onchange="this.form.submit()">
                                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                                        <option value="this_week" {{ $period == 'this_week' ? 'selected' : '' }}>This Week</option>
                                        <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>This Month</option>
                                        <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                        <option value="this_quarter" {{ $period == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                                        <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>This Year</option>
                                        <option value="last_year" {{ $period == 'last_year' ? 'selected' : '' }}>Last Year</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control"
                                           value="{{ $startDate }}" id="start_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                           value="{{ $endDate }}" id="end_date">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Report Period -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Showing report for period:
                                <strong>{{ \Carbon\Carbon::parse($startDate)->format('M j, Y') }}</strong> to
                                <strong>{{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary Report with Both Currencies -->
                    @if($reportType === 'financial_summary')
                        <!-- KSH Summary -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <span class="badge bg-primary me-2">KSH</span> Kenyan Shilling Summary
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Revenue (KSH)</h6>
                                        <h3 class="mb-0">KSH {{ number_format($reportData['total_revenue_ksh'] ?? 0, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Pending Invoices (KSH)</h6>
                                        <h3 class="mb-0">{{ $reportData['pending_invoices_ksh'] ?? 0 }}</h3>
                                        <small>KSH {{ number_format($reportData['pending_amount_ksh'] ?? 0, 2) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Overdue Invoices (KSH)</h6>
                                        <h3 class="mb-0">{{ $reportData['overdue_invoices_ksh'] ?? 0 }}</h3>
                                        <small>KSH {{ number_format($reportData['overdue_amount_ksh'] ?? 0, 2) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Avg. Invoice Value (KSH)</h6>
                                        <h3 class="mb-0">
                                            @php
                                                $pendingInvoicesKsh = $reportData['pending_invoices_ksh'] ?? 0;
                                                $pendingAmountKsh = $reportData['pending_amount_ksh'] ?? 0;
                                                $avgValueKsh = $pendingInvoicesKsh > 0 ? $pendingAmountKsh / $pendingInvoicesKsh : 0;
                                            @endphp
                                            KSH {{ number_format($avgValueKsh, 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- USD Summary -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <span class="badge bg-secondary me-2">USD</span> US Dollar Summary
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Revenue (USD)</h6>
                                        <h3 class="mb-0">$ {{ number_format($reportData['total_revenue_usd'] ?? 0, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Pending Invoices (USD)</h6>
                                        <h3 class="mb-0">{{ $reportData['pending_invoices_usd'] ?? 0 }}</h3>
                                        <small>$ {{ number_format($reportData['pending_amount_usd'] ?? 0, 2) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Overdue Invoices (USD)</h6>
                                        <h3 class="mb-0">{{ $reportData['overdue_invoices_usd'] ?? 0 }}</h3>
                                        <small>$ {{ number_format($reportData['overdue_amount_usd'] ?? 0, 2) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Avg. Invoice Value (USD)</h6>
                                        <h3 class="mb-0">
                                            @php
                                                $pendingInvoicesUsd = $reportData['pending_invoices_usd'] ?? 0;
                                                $pendingAmountUsd = $reportData['pending_amount_usd'] ?? 0;
                                                $avgValueUsd = $pendingInvoicesUsd > 0 ? $pendingAmountUsd / $pendingInvoicesUsd : 0;
                                            @endphp
                                            $ {{ number_format($avgValueUsd, 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Currency Distribution -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Currency</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach(($reportData['revenue_by_currency'] ?? []) as $currency)
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="fw-bold">{{ strtoupper($currency->currency) }}</span>
                                                            <span class="fw-bold">{{ strtoupper($currency->currency) == 'KSH' ? 'KSH' : '$' }} {{ number_format($currency->total_revenue ?? 0, 2) }}</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px;">
                                                            @php
                                                                $totalAllRevenue = ($reportData['total_revenue_ksh'] ?? 0) + ($reportData['total_revenue_usd'] ?? 0);
                                                                $currencyRevenue = $currency->total_revenue ?? 0;
                                                                $width = $totalAllRevenue > 0 ? ($currencyRevenue / $totalAllRevenue) * 100 : 0;
                                                            @endphp
                                                            <div class="progress-bar bg-{{ $currency->currency == 'ksh' ? 'primary' : 'secondary' }}" style="width: {{ $width }}%"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $currency->invoice_count ?? 0 }} invoices | Avg: {{ strtoupper($currency->currency) == 'KSH' ? 'KSH' : '$' }} {{ number_format($currency->avg_invoice_amount ?? 0, 2) }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue by Type - Split by Currency -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Service Type (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        @forelse(($reportData['revenue_by_type_ksh'] ?? []) as $revenue)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize">{{ $revenue->billing_cycle ?? 'unknown' }}</span>
                                                    <span class="fw-bold">KSH {{ number_format($revenue->revenue ?? 0, 2) }}</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    @php
                                                        $totalRevenueKsh = $reportData['total_revenue_ksh'] ?? 1;
                                                        $revenueAmount = $revenue->revenue ?? 0;
                                                        $width = $totalRevenueKsh > 0 ? ($revenueAmount / $totalRevenueKsh) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar" style="width: {{ $width }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $revenue->count ?? 0 }} invoices</small>
                                            </div>
                                        @empty
                                            <p class="text-muted">No KSH revenue data available.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Service Type (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        @forelse(($reportData['revenue_by_type_usd'] ?? []) as $revenue)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize">{{ $revenue->billing_cycle ?? 'unknown' }}</span>
                                                    <span class="fw-bold">$ {{ number_format($revenue->revenue ?? 0, 2) }}</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    @php
                                                        $totalRevenueUsd = $reportData['total_revenue_usd'] ?? 1;
                                                        $revenueAmount = $revenue->revenue ?? 0;
                                                        $width = $totalRevenueUsd > 0 ? ($revenueAmount / $totalRevenueUsd) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar" style="width: {{ $width }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $revenue->count ?? 0 }} invoices</small>
                                            </div>
                                        @empty
                                            <p class="text-muted">No USD revenue data available.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Monthly Revenue Trend (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        @forelse(($reportData['monthly_trend_ksh'] ?? []) as $trend)
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ date('F Y', mktime(0, 0, 0, $trend->month ?? 1, 1, $trend->year ?? date('Y'))) }}</span>
                                                    <span class="fw-bold">KSH {{ number_format($trend->monthly_revenue ?? 0, 2) }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    @php
                                                        $monthlyTrendKsh = $reportData['monthly_trend_ksh'] ?? collect();
                                                        $maxRevenueKsh = $monthlyTrendKsh->max('monthly_revenue') ?? 1;
                                                        $trendRevenue = $trend->monthly_revenue ?? 0;
                                                        $width = $maxRevenueKsh > 0 ? ($trendRevenue / $maxRevenueKsh) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-success" style="width: {{ $width }}%"></div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted">No KSH trend data available.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Monthly Revenue Trend (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        @forelse(($reportData['monthly_trend_usd'] ?? []) as $trend)
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ date('F Y', mktime(0, 0, 0, $trend->month ?? 1, 1, $trend->year ?? date('Y'))) }}</span>
                                                    <span class="fw-bold">$ {{ number_format($trend->monthly_revenue ?? 0, 2) }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    @php
                                                        $monthlyTrendUsd = $reportData['monthly_trend_usd'] ?? collect();
                                                        $maxRevenueUsd = $monthlyTrendUsd->max('monthly_revenue') ?? 1;
                                                        $trendRevenue = $trend->monthly_revenue ?? 0;
                                                        $width = $maxRevenueUsd > 0 ? ($trendRevenue / $maxRevenueUsd) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-success" style="width: {{ $width }}%"></div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted">No USD trend data available.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Customers -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Top Customers (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Total Spent</th>
                                                        <th>Invoices</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse(($reportData['top_customers_ksh'] ?? []) as $customer)
                                                        <tr>
                                                            <td>{{ $customer->name }}</td>
                                                            <td>KSH {{ number_format($customer->total_spent, 2) }}</td>
                                                            <td>{{ $customer->invoices_count }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">No data available</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Top Customers (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Total Spent</th>
                                                        <th>Invoices</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse(($reportData['top_customers_usd'] ?? []) as $customer)
                                                        <tr>
                                                            <td>{{ $customer->name }}</td>
                                                            <td>$ {{ number_format($customer->total_spent, 2) }}</td>
                                                            <td>{{ $customer->invoices_count }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">No data available</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Most Delayed Invoices -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Most Delayed Invoices (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Days Late</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse(($reportData['most_delayed_invoices_ksh'] ?? []) as $invoice)
                                                        <tr>
                                                            <td>{{ $invoice->billing_number }}</td>
                                                            <td>{{ $invoice->customer_name }}</td>
                                                            <td>KSH {{ number_format($invoice->total_amount, 2) }}</td>
                                                            <td><span class="badge bg-danger">{{ $invoice->days_late }} days</span></td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">No delayed KSH invoices</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Most Delayed Invoices (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Days Late</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse(($reportData['most_delayed_invoices_usd'] ?? []) as $invoice)
                                                        <tr>
                                                            <td>{{ $invoice->billing_number }}</td>
                                                            <td>{{ $invoice->customer_name }}</td>
                                                            <td>$ {{ number_format($invoice->total_amount, 2) }}</td>
                                                            <td><span class="badge bg-danger">{{ $invoice->days_late }} days</span></td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">No delayed USD invoices</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Due Dates -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Upcoming Due Dates (KSH) - Next 7 Days</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Days Left</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse(($reportData['upcoming_due_dates_ksh'] ?? []) as $invoice)
                                                        <tr>
                                                            <td>{{ $invoice->billing_number }}</td>
                                                            <td>{{ $invoice->customer_name }}</td>
                                                            <td>KSH {{ number_format($invoice->total_amount, 2) }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M j, Y') }}</td>
                                                            <td><span class="badge bg-warning">{{ $invoice->days_until_due }} days</span></td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">No upcoming KSH invoices</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Upcoming Due Dates (USD) - Next 7 Days</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Days Left</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse(($reportData['upcoming_due_dates_usd'] ?? []) as $invoice)
                                                        <tr>
                                                            <td>{{ $invoice->billing_number }}</td>
                                                            <td>{{ $invoice->customer_name }}</td>
                                                            <td>$ {{ number_format($invoice->total_amount, 2) }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M j, Y') }}</td>
                                                            <td><span class="badge bg-warning">{{ $invoice->days_until_due }} days</span></td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">No upcoming USD invoices</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Revenue Analysis Report with Both Currencies -->
                    @elseif($reportType === 'revenue_analysis')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Top Customers by Revenue (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        @forelse(($reportData['revenue_by_customer_ksh'] ?? collect())->take(10) as $customer)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $customer->customer_name ?? 'Unknown Customer' }}</span>
                                                    <span class="fw-bold">KSH {{ number_format($customer->revenue ?? 0, 2) }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    @php
                                                        $revenueByCustomerKsh = $reportData['revenue_by_customer_ksh'] ?? collect();
                                                        $maxRevenueKsh = $revenueByCustomerKsh->max('revenue') ?? 1;
                                                        $customerRevenueKsh = $customer->revenue ?? 0;
                                                        $width = $maxRevenueKsh > 0 ? ($customerRevenueKsh / $maxRevenueKsh) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-primary" style="width: {{ $width }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $customer->invoice_count ?? 0 }} invoices</small>
                                            </div>
                                        @empty
                                            <p class="text-muted">No KSH revenue data available for this period.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Top Customers by Revenue (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        @forelse(($reportData['revenue_by_customer_usd'] ?? collect())->take(10) as $customer)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $customer->customer_name ?? 'Unknown Customer' }}</span>
                                                    <span class="fw-bold">$ {{ number_format($customer->revenue ?? 0, 2) }}</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    @php
                                                        $revenueByCustomerUsd = $reportData['revenue_by_customer_usd'] ?? collect();
                                                        $maxRevenueUsd = $revenueByCustomerUsd->max('revenue') ?? 1;
                                                        $customerRevenueUsd = $customer->revenue ?? 0;
                                                        $width = $maxRevenueUsd > 0 ? ($customerRevenueUsd / $maxRevenueUsd) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-primary" style="width: {{ $width }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $customer->invoice_count ?? 0 }} invoices</small>
                                            </div>
                                        @empty
                                            <p class="text-muted">No USD revenue data available for this period.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Service Type (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        @forelse(($reportData['revenue_by_service_ksh'] ?? collect()) as $service)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize">{{ $service->billing_cycle ?? 'Unknown' }}</span>
                                                    <span class="fw-bold">KSH {{ number_format($service->revenue ?? 0, 2) }}</span>
                                                </div>
                                                <small class="text-muted">{{ $service->count ?? 0 }} invoices</small>
                                            </div>
                                        @empty
                                            <p class="text-muted">No KSH service data available.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Service Type (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        @forelse(($reportData['revenue_by_service_usd'] ?? collect()) as $service)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize">{{ $service->billing_cycle ?? 'Unknown' }}</span>
                                                    <span class="fw-bold">$ {{ number_format($service->revenue ?? 0, 2) }}</span>
                                                </div>
                                                <small class="text-muted">{{ $service->count ?? 0 }} invoices</small>
                                            </div>
                                        @empty
                                            <p class="text-muted">No USD service data available.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Customer Billing Report with Both Currencies -->
                    @elseif($reportType === 'customer_billing')
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Customer Billing Summary - KSH</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Customer</th>
                                                <th>Total Billings</th>
                                                <th>Paid Amount (KSH)</th>
                                                <th>Pending Amount (KSH)</th>
                                                <th>Overdue Amount (KSH)</th>
                                                <th>Total Billed (KSH)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($reportData['customer_billing_ksh'] ?? []) as $billing)
                                                <tr>
                                                    <td>{{ $billing->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $billing->total_billings ?? 0 }}</td>
                                                    <td class="text-success">KSH {{ number_format($billing->paid_amount ?? 0, 2) }}</td>
                                                    <td class="text-warning">KSH {{ number_format($billing->pending_amount ?? 0, 2) }}</td>
                                                    <td class="text-danger">KSH {{ number_format($billing->overdue_amount ?? 0, 2) }}</td>
                                                    <td class="fw-bold">
                                                        KSH {{ number_format(($billing->paid_amount ?? 0) + ($billing->pending_amount ?? 0) + ($billing->overdue_amount ?? 0), 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No KSH customer billing data available.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Customer Billing Summary - USD</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Customer</th>
                                                <th>Total Billings</th>
                                                <th>Paid Amount (USD)</th>
                                                <th>Pending Amount (USD)</th>
                                                <th>Overdue Amount (USD)</th>
                                                <th>Total Billed (USD)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($reportData['customer_billing_usd'] ?? []) as $billing)
                                                <tr>
                                                    <td>{{ $billing->customer_name ?? 'Unknown' }}</td>
                                                    <td>{{ $billing->total_billings ?? 0 }}</td>
                                                    <td class="text-success">$ {{ number_format($billing->paid_amount ?? 0, 2) }}</td>
                                                    <td class="text-warning">$ {{ number_format($billing->pending_amount ?? 0, 2) }}</td>
                                                    <td class="text-danger">$ {{ number_format($billing->overdue_amount ?? 0, 2) }}</td>
                                                    <td class="fw-bold">
                                                        $ {{ number_format(($billing->paid_amount ?? 0) + ($billing->pending_amount ?? 0) + ($billing->overdue_amount ?? 0), 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No USD customer billing data available.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <!-- Aging Report with Both Currencies -->
                    @elseif($reportType === 'aging_report')
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Accounts Receivable Aging Report- KSH</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Customer</th>
                                                <th>Current (KSH)</th>
                                                <th>1-30 Days (KSH)</th>
                                                <th>31-60 Days (KSH)</th>
                                                <th>61-90+ Days (KSH)</th>
                                                <th>Total Outstanding (KSH)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($reportData['aging_report_ksh'] ?? []) as $aging)
                                                <tr>
                                                    <td>{{ $aging->customer_name ?? 'Unknown' }}</td>
                                                    <td class="text-success">KSH {{ number_format($aging->current ?? 0, 2) }}</td>
                                                    <td class="text-warning">KSH {{ number_format($aging->days_30 ?? 0, 2) }}</td>
                                                    <td class="text-warning">KSH {{ number_format($aging->days_60 ?? 0, 2) }}</td>
                                                    <td class="text-danger">KSH {{ number_format($aging->days_90_plus ?? 0, 2) }}</td>
                                                    <td class="fw-bold">
                                                        KSH {{ number_format(($aging->current ?? 0) + ($aging->days_30 ?? 0) + ($aging->days_60 ?? 0) + ($aging->days_90_plus ?? 0), 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No KSH aging data available.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Accounts Receivable Aging Report - USD</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Customer</th>
                                                <th>Current (USD)</th>
                                                <th>1-30 Days (USD)</th>
                                                <th>31-60 Days (USD)</th>
                                                <th>61-90+ Days (USD)</th>
                                                <th>Total Outstanding (USD)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($reportData['aging_report_usd'] ?? []) as $aging)
                                                <tr>
                                                    <td>{{ $aging->customer_name ?? 'Unknown' }}</td>
                                                    <td class="text-success">$ {{ number_format($aging->current ?? 0, 2) }}</td>
                                                    <td class="text-warning">$ {{ number_format($aging->days_30 ?? 0, 2) }}</td>
                                                    <td class="text-warning">$ {{ number_format($aging->days_60 ?? 0, 2) }}</td>
                                                    <td class="text-danger">$ {{ number_format($aging->days_90_plus ?? 0, 2) }}</td>
                                                    <td class="fw-bold">
                                                        $ {{ number_format(($aging->current ?? 0) + ($aging->days_30 ?? 0) + ($aging->days_60 ?? 0) + ($aging->days_90_plus ?? 0), 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No USD aging data available.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <!-- Debt Aging Analysis Report with Both Currencies -->
                    @elseif($reportType === 'debt_aging')
                        <!-- KSH Debt Aging Summary -->
                        @if(isset($reportData['debt_summary_ksh']))
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <span class="badge bg-primary me-2">KSH</span> Kenyan Shilling Debt Summary
                                    </h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Receivables (KSH)</h6>
                                            <h3 class="mb-0">KSH {{ number_format($reportData['debt_summary_ksh']['total_receivables'] ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Current (KSH)</h6>
                                            <h3 class="mb-0">KSH {{ number_format($reportData['debt_summary_ksh']['current'] ?? 0, 2) }}</h3>
                                            <small>{{ number_format($reportData['debt_summary_ksh']['current_percentage'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Overdue (KSH)</h6>
                                            <h3 class="mb-0">KSH {{ number_format($reportData['debt_summary_ksh']['overdue'] ?? 0, 2) }}</h3>
                                            <small>{{ number_format($reportData['debt_summary_ksh']['overdue_percentage'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Bad Debt Provision (KSH)</h6>
                                            <h3 class="mb-0">KSH {{ number_format($reportData['debt_summary_ksh']['bad_debt_provision'] ?? 0, 2) }}</h3>
                                            <small>{{ number_format($reportData['debt_summary_ksh']['bad_debt_percentage'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- USD Debt Aging Summary -->
                        @if(isset($reportData['debt_summary_usd']))
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <span class="badge bg-secondary me-2">USD</span> US Dollar Debt Summary
                                    </h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Receivables (USD)</h6>
                                            <h3 class="mb-0">$ {{ number_format($reportData['debt_summary_usd']['total_receivables'] ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Current (USD)</h6>
                                            <h3 class="mb-0">$ {{ number_format($reportData['debt_summary_usd']['current'] ?? 0, 2) }}</h3>
                                            <small>{{ number_format($reportData['debt_summary_usd']['current_percentage'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Overdue (USD)</h6>
                                            <h3 class="mb-0">$ {{ number_format($reportData['debt_summary_usd']['overdue'] ?? 0, 2) }}</h3>
                                            <small>{{ number_format($reportData['debt_summary_usd']['overdue_percentage'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Bad Debt Provision (USD)</h6>
                                            <h3 class="mb-0">$ {{ number_format($reportData['debt_summary_usd']['bad_debt_provision'] ?? 0, 2) }}</h3>
                                            <small>{{ number_format($reportData['debt_summary_usd']['bad_debt_percentage'] ?? 0, 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Debt Aging Distribution (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="debtAgingChartKsh" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Debt Aging Distribution (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="debtAgingChartUsd" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Collection Metrics (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6 mb-3">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-primary">{{ $reportData['collection_metrics_ksh']['average_collection_period'] ?? 0 }} days</h4>
                                                    <small class="text-muted">Avg Collection Period</small>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-success">{{ number_format($reportData['collection_metrics_ksh']['collection_efficiency'] ?? 0, 1) }}%</h4>
                                                    <small class="text-muted">Collection Efficiency</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-warning">{{ number_format($reportData['collection_metrics_ksh']['dsr'] ?? 0, 1) }}</h4>
                                                    <small class="text-muted">Days Sales Outstanding</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-info">{{ number_format($reportData['collection_metrics_ksh']['recovery_rate'] ?? 0, 1) }}%</h4>
                                                    <small class="text-muted">Recovery Rate</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Collection Metrics (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6 mb-3">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-primary">{{ $reportData['collection_metrics_usd']['average_collection_period'] ?? 0 }} days</h4>
                                                    <small class="text-muted">Avg Collection Period</small>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-success">{{ number_format($reportData['collection_metrics_usd']['collection_efficiency'] ?? 0, 1) }}%</h4>
                                                    <small class="text-muted">Collection Efficiency</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-warning">{{ number_format($reportData['collection_metrics_usd']['dsr'] ?? 0, 1) }}</h4>
                                                    <small class="text-muted">Days Sales Outstanding</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-info">{{ number_format($reportData['collection_metrics_usd']['recovery_rate'] ?? 0, 1) }}%</h4>
                                                    <small class="text-muted">Recovery Rate</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Debt Aging Table with Currency Column -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Detailed Debt Aging Analysis</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Customer</th>
                                                <th>Currency</th>
                                                <th>Total Due</th>
                                                <th>Current</th>
                                                <th>1-30 Days</th>
                                                <th>31-60 Days</th>
                                                <th>61-90 Days</th>
                                                <th>>90 Days</th>
                                                <th>Risk Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(($reportData['detailed_aging'] ?? []) as $debt)
                                                <tr>
                                                    <td>{{ $debt->customer_name ?? 'Unknown' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $debt->currency == 'ksh' ? 'primary' : 'secondary' }}">
                                                            {{ strtoupper($debt->currency ?? 'KSH') }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold">
                                                        {{ $debt->currency == 'ksh' ? 'KSH' : '$' }} {{ number_format($debt->total_due ?? 0, 2) }}
                                                    </td>
                                                    <td class="text-success">
                                                        {{ $debt->currency == 'ksh' ? 'KSH' : '$' }} {{ number_format($debt->current ?? 0, 2) }}
                                                    </td>
                                                    <td class="text-warning">
                                                        {{ $debt->currency == 'ksh' ? 'KSH' : '$' }} {{ number_format($debt->days_30 ?? 0, 2) }}
                                                    </td>
                                                    <td class="text-warning">
                                                        {{ $debt->currency == 'ksh' ? 'KSH' : '$' }} {{ number_format($debt->days_60 ?? 0, 2) }}
                                                    </td>
                                                    <td class="text-danger">
                                                        {{ $debt->currency == 'ksh' ? 'KSH' : '$' }} {{ number_format($debt->days_90 ?? 0, 2) }}
                                                    </td>
                                                    <td class="text-danger">
                                                        {{ $debt->currency == 'ksh' ? 'KSH' : '$' }} {{ number_format($debt->days_over_90 ?? 0, 2) }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $riskLevel = $debt->risk_level ?? 'low';
                                                            $badgeClass = [
                                                                'low' => 'bg-success',
                                                                'medium' => 'bg-warning',
                                                                'high' => 'bg-danger',
                                                                'critical' => 'bg-dark'
                                                            ][$riskLevel] ?? 'bg-secondary';
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} text-capitalize">{{ $riskLevel }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <!-- Tax Report with Both Currencies -->
                    @elseif($reportType === 'tax_report')
                        @php
                            $taxSummaryKsh = $reportData['tax_summary_ksh'] ?? null;
                            $taxSummaryUsd = $reportData['tax_summary_usd'] ?? null;
                        @endphp

                        @if($taxSummaryKsh)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <span class="badge bg-primary me-2">KSH</span> Kenyan Shilling Tax Summary
                                    </h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Tax Collected (KSH)</h6>
                                            <h3 class="mb-0">KSH {{ number_format($taxSummaryKsh->total_tax ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Revenue (KSH)</h6>
                                            <h3 class="mb-0">KSH {{ number_format($taxSummaryKsh->total_amount ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Average Tax Rate (KSH)</h6>
                                            <h3 class="mb-0">{{ number_format($taxSummaryKsh->avg_tax_rate ?? 0, 2) }}%</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Invoices (KSH)</h6>
                                            <h3 class="mb-0">{{ $taxSummaryKsh->invoice_count ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($taxSummaryUsd)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <span class="badge bg-secondary me-2">USD</span> US Dollar Tax Summary
                                    </h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Tax Collected (USD)</h6>
                                            <h3 class="mb-0">$ {{ number_format($taxSummaryUsd->total_tax ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Revenue (USD)</h6>
                                            <h3 class="mb-0">$ {{ number_format($taxSummaryUsd->total_amount ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Average Tax Rate (USD)</h6>
                                            <h3 class="mb-0">{{ number_format($taxSummaryUsd->avg_tax_rate ?? 0, 2) }}%</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Invoices (USD)</h6>
                                            <h3 class="mb-0">{{ $taxSummaryUsd->invoice_count ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Tax Collection by Billing Cycle</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Billing Cycle</th>
                                                <th>Currency</th>
                                                <th>Tax Collected</th>
                                                <th>Number of Invoices</th>
                                                <th>Percentage of Total Tax</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($reportData['tax_by_type'] ?? []) as $tax)
                                                <tr>
                                                    <td class="text-capitalize">{{ $tax->billing_cycle ?? 'unknown' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $tax->currency == 'ksh' ? 'primary' : 'secondary' }}">
                                                            {{ strtoupper($tax->currency ?? 'KSH') }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $tax->currency == 'ksh' ? 'KSH' : '$' }} {{ number_format($tax->tax_collected ?? 0, 2) }}</td>
                                                    <td>{{ $tax->count ?? 0 }}</td>
                                                    <td>
                                                        @php
                                                            $totalTax = $tax->currency == 'ksh' ? ($taxSummaryKsh->total_tax ?? 1) : ($taxSummaryUsd->total_tax ?? 1);
                                                            $taxCollected = $tax->tax_collected ?? 0;
                                                            $percentage = $totalTax > 0 ? ($taxCollected / $totalTax) * 100 : 0;
                                                        @endphp
                                                        {{ number_format($percentage, 1) }}%
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No tax data available for this period.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <!-- Cash Flow Statement -->
@elseif($reportType === 'cash_flow')
    <!-- Cash Flow Summary Cards - Combined -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="border-bottom pb-2 mb-3">
                <span class="badge bg-primary me-2">Combined Summary</span>
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Operating Cash Flow</h6>
                    <h3 class="mb-0">$ {{ number_format($reportData['cash_flow_summary']['operating'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Investing Cash Flow</h6>
                    <h3 class="mb-0">$ {{ number_format($reportData['cash_flow_summary']['investing'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Financing Cash Flow</h6>
                    <h3 class="mb-0">$ {{ number_format($reportData['cash_flow_summary']['financing'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Net Cash Flow</h6>
                    <h3 class="mb-0">$ {{ number_format($reportData['cash_flow_summary']['net_cash_flow'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Summary by Currency -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-primary me-2">KSH</span> Cash Flow Summary (KSH)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Operating</small>
                                <h5 class="text-success mb-0">KSH {{ number_format($reportData['cash_flow_summary_ksh']['operating'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Investing</small>
                                <h5 class="text-info mb-0">KSH {{ number_format($reportData['cash_flow_summary_ksh']['investing'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Financing</small>
                                <h5 class="text-warning mb-0">KSH {{ number_format($reportData['cash_flow_summary_ksh']['financing'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Net Cash Flow</small>
                                <h5 class="text-primary mb-0">KSH {{ number_format($reportData['cash_flow_summary_ksh']['net_cash_flow'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-secondary me-2">USD</span> Cash Flow Summary (USD)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Operating</small>
                                <h5 class="text-success mb-0">$ {{ number_format($reportData['cash_flow_summary_usd']['operating'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Investing</small>
                                <h5 class="text-info mb-0">$ {{ number_format($reportData['cash_flow_summary_usd']['investing'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Financing</small>
                                <h5 class="text-warning mb-0">$ {{ number_format($reportData['cash_flow_summary_usd']['financing'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Net Cash Flow</small>
                                <h5 class="text-primary mb-0">$ {{ number_format($reportData['cash_flow_summary_usd']['net_cash_flow'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Cash Flow Statements by Currency -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-primary me-2">KSH</span> Cash Flow Statement (KSH)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="table-light">
                                <td colspan="2"><strong>Operating Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Cash from Customers</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['cash_from_customers'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cash Paid to Suppliers</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['cash_to_suppliers'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Cash Paid for Expenses</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['cash_for_expenses'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Interest Paid</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['interest_paid'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Taxes Paid</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['taxes_paid'] ?? 0), 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Operating</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['cash_flow_summary_ksh']['operating'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="2"><strong>Investing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Purchase of Equipment</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['equipment_purchase'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Infrastructure Investments</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['infrastructure_investment'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Property Purchase</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['property_purchase'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Investment Income</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['investment_income'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Asset Sales</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['asset_sales'] ?? 0, 2) }}</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Investing</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['cash_flow_summary_ksh']['investing'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="2"><strong>Financing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Loan Proceeds</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['loan_proceeds'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Equity Issuance</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['equity_issuance'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Debt Repayment</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['debt_repayment'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Dividends Paid</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['dividends_paid'] ?? 0), 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Financing</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['cash_flow_summary_ksh']['financing'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="table-primary">
                                <td><strong>Net Increase in Cash</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['cash_flow_summary_ksh']['net_cash_flow'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-secondary me-2">USD</span> Cash Flow Statement (USD)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="table-light">
                                <td colspan="2"><strong>Operating Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Cash from Customers</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['cash_from_customers'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cash Paid to Suppliers</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['cash_to_suppliers'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Cash Paid for Expenses</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['cash_for_expenses'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Interest Paid</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['interest_paid'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Taxes Paid</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['taxes_paid'] ?? 0), 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Operating</strong></td>
                                <td class="fw-bold">$ {{ number_format($reportData['cash_flow_summary_usd']['operating'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="2"><strong>Investing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Purchase of Equipment</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['equipment_purchase'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Infrastructure Investments</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['infrastructure_investment'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Property Purchase</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['property_purchase'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Investment Income</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['investment_income'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Asset Sales</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['asset_sales'] ?? 0, 2) }}</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Investing</strong></td>
                                <td class="fw-bold">$ {{ number_format($reportData['cash_flow_summary_usd']['investing'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="2"><strong>Financing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Loan Proceeds</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['loan_proceeds'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Equity Issuance</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['equity_issuance'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Debt Repayment</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['debt_repayment'] ?? 0), 2) }})</td>
                            </tr>
                            <tr>
                                <td>Dividends Paid</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['dividends_paid'] ?? 0), 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Financing</strong></td>
                                <td class="fw-bold">$ {{ number_format($reportData['cash_flow_summary_usd']['financing'] ?? 0, 2) }}</td>
                            </tr>

                            <tr class="table-primary">
                                <td><strong>Net Increase in Cash</strong></td>
                                <td class="fw-bold">$ {{ number_format($reportData['cash_flow_summary_usd']['net_cash_flow'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Cash Flow Statement -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Combined Cash Flow Statement (All Currencies)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Activity</th>
                                <th>KSH</th>
                                <th>USD</th>
                                <th>Total (USD Equivalent)*</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-light">
                                <td colspan="4"><strong>Operating Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Cash from Customers</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['cash_from_customers'] ?? 0, 2) }}</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['cash_from_customers'] ?? 0, 2) }}</td>
                                <td class="text-success">
                                    @php
                                        $exchangeRate = $reportData['exchange_rate'] ?? 130;
                                        $total = ($reportData['cash_flow_details_ksh']['cash_from_customers'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['cash_from_customers'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Cash Paid to Suppliers</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['cash_to_suppliers'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['cash_to_suppliers'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['cash_to_suppliers'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['cash_to_suppliers'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Cash Paid for Expenses</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['cash_for_expenses'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['cash_for_expenses'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['cash_for_expenses'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['cash_for_expenses'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Interest Paid</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['interest_paid'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['interest_paid'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['interest_paid'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['interest_paid'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Taxes Paid</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['taxes_paid'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['taxes_paid'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['taxes_paid'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['taxes_paid'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Operating</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['cash_flow_summary_ksh']['operating'] ?? 0, 2) }}</td>
                                <td class="fw-bold">$ {{ number_format($reportData['cash_flow_summary_usd']['operating'] ?? 0, 2) }}</td>
                                <td class="fw-bold">
                                    @php
                                        $total = ($reportData['cash_flow_summary_ksh']['operating'] ?? 0) / $exchangeRate + ($reportData['cash_flow_summary_usd']['operating'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="4"><strong>Investing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Purchase of Equipment</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['equipment_purchase'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['equipment_purchase'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['equipment_purchase'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['equipment_purchase'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Infrastructure Investments</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['infrastructure_investment'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['infrastructure_investment'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['infrastructure_investment'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['infrastructure_investment'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Property Purchase</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['property_purchase'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['property_purchase'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['property_purchase'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['property_purchase'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Investment Income</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['investment_income'] ?? 0, 2) }}</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['investment_income'] ?? 0, 2) }}</td>
                                <td class="text-success">
                                    @php
                                        $total = ($reportData['cash_flow_details_ksh']['investment_income'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['investment_income'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Asset Sales</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['asset_sales'] ?? 0, 2) }}</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['asset_sales'] ?? 0, 2) }}</td>
                                <td class="text-success">
                                    @php
                                        $total = ($reportData['cash_flow_details_ksh']['asset_sales'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['asset_sales'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Investing</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['cash_flow_summary_ksh']['investing'] ?? 0, 2) }}</td>
                                <td class="fw-bold">$ {{ number_format($reportData['cash_flow_summary_usd']['investing'] ?? 0, 2) }}</td>
                                <td class="fw-bold">
                                    @php
                                        $total = ($reportData['cash_flow_summary_ksh']['investing'] ?? 0) / $exchangeRate + ($reportData['cash_flow_summary_usd']['investing'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="4"><strong>Financing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Loan Proceeds</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['loan_proceeds'] ?? 0, 2) }}</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['loan_proceeds'] ?? 0, 2) }}</td>
                                <td class="text-success">
                                    @php
                                        $total = ($reportData['cash_flow_details_ksh']['loan_proceeds'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['loan_proceeds'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Equity Issuance</td>
                                <td class="text-success">KSH {{ number_format($reportData['cash_flow_details_ksh']['equity_issuance'] ?? 0, 2) }}</td>
                                <td class="text-success">$ {{ number_format($reportData['cash_flow_details_usd']['equity_issuance'] ?? 0, 2) }}</td>
                                <td class="text-success">
                                    @php
                                        $total = ($reportData['cash_flow_details_ksh']['equity_issuance'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['equity_issuance'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Debt Repayment</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['debt_repayment'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['debt_repayment'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['debt_repayment'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['debt_repayment'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Dividends Paid</td>
                                <td class="text-danger">(KSH {{ number_format(abs($reportData['cash_flow_details_ksh']['dividends_paid'] ?? 0), 2) }})</td>
                                <td class="text-danger">($ {{ number_format(abs($reportData['cash_flow_details_usd']['dividends_paid'] ?? 0), 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $total = abs(($reportData['cash_flow_details_ksh']['dividends_paid'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['dividends_paid'] ?? 0));
                                    @endphp
                                    ($ {{ number_format($total, 2) }})
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Financing</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['cash_flow_summary_ksh']['financing'] ?? 0, 2) }}</td>
                                <td class="fw-bold">$ {{ number_format($reportData['cash_flow_summary_usd']['financing'] ?? 0, 2) }}</td>
                                <td class="fw-bold">
                                    @php
                                        $total = ($reportData['cash_flow_summary_ksh']['financing'] ?? 0) / $exchangeRate + ($reportData['cash_flow_summary_usd']['financing'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>

                            <tr class="table-primary">
                                <td><strong>Net Increase in Cash</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['cash_flow_summary_ksh']['net_cash_flow'] ?? 0, 2) }}</td>
                                <td class="fw-bold">$ {{ number_format($reportData['cash_flow_summary_usd']['net_cash_flow'] ?? 0, 2) }}</td>
                                <td class="fw-bold">
                                    @php
                                        $total = ($reportData['cash_flow_summary_ksh']['net_cash_flow'] ?? 0) / $exchangeRate + ($reportData['cash_flow_summary_usd']['net_cash_flow'] ?? 0);
                                    @endphp
                                    $ {{ number_format($total, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="text-muted">* USD equivalent using exchange rate: 1 USD = {{ number_format($reportData['exchange_rate'] ?? 130, 2) }} KSH</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Chart -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Cash Flow Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="cashFlowChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

                    <!-- Profitability Analysis -->
                @elseif($reportType === 'profitability')
                    <!-- Profitability Metrics - Percentages (same for both currencies) -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <span class="badge bg-primary me-2">Profitability Ratios</span>
                            </h5>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                    <h6 class="card-title">Gross Profit Margin</h6>
                    <h3 class="mb-0">{{ number_format($reportData['profitability_metrics']['gross_margin'] ?? 0, 1) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Operating Margin</h6>
                    <h3 class="mb-0">{{ number_format($reportData['profitability_metrics']['operating_margin'] ?? 0, 1) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Net Profit Margin</h6>
                    <h3 class="mb-0">{{ number_format($reportData['profitability_metrics']['net_margin'] ?? 0, 1) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">ROI</h6>
                    <h3 class="mb-0">{{ number_format($reportData['profitability_metrics']['roi'] ?? 0, 1) }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- P&L Statement by Currency -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-primary me-2">KSH</span> Profit & Loss Statement (KSH)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Total Revenue</strong></td>
                                <td class="text-success">KSH {{ number_format($reportData['p_l_statement_ksh']['revenue'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cost of Services</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['cost_of_services'] ?? 0, 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Gross Profit</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['p_l_statement_ksh']['gross_profit'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Operating Expenses</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['operating_expenses'] ?? 0, 2) }})</td>
                            </tr>
                            <tr>
                                <td>Depreciation</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['depreciation'] ?? 0, 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Operating Profit</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['p_l_statement_ksh']['operating_profit'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Interest Expense</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['interest_expense'] ?? 0, 2) }})</td>
                            </tr>
                            <tr>
                                <td>Taxes</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['taxes'] ?? 0, 2) }})</td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Net Profit</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['p_l_statement_ksh']['net_profit'] ?? 0, 2) }}</td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>EBITDA</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['p_l_statement_ksh']['ebitda'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-secondary me-2">USD</span> Profit & Loss Statement (USD)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Total Revenue</strong></td>
                                <td class="text-success">$ {{ number_format($reportData['p_l_statement_usd']['revenue'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cost of Services</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['cost_of_services'] ?? 0, 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Gross Profit</strong></td>
                                <td class="fw-bold">$ {{ number_format($reportData['p_l_statement_usd']['gross_profit'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Operating Expenses</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['operating_expenses'] ?? 0, 2) }})</td>
                            </tr>
                            <tr>
                                <td>Depreciation</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['depreciation'] ?? 0, 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Operating Profit</strong></td>
                                <td class="fw-bold">$ {{ number_format($reportData['p_l_statement_usd']['operating_profit'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Interest Expense</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['interest_expense'] ?? 0, 2) }})</td>
                            </tr>
                            <tr>
                                <td>Taxes</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['taxes'] ?? 0, 2) }})</td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Net Profit</strong></td>
                                <td class="fw-bold">$ {{ number_format($reportData['p_l_statement_usd']['net_profit'] ?? 0, 2) }}</td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>EBITDA</strong></td>
                                <td class="fw-bold">$ {{ number_format($reportData['p_l_statement_usd']['ebitda'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined P&L Statement (Optional) -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Combined Profit & Loss Statement (All Currencies)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>KSH</th>
                                <th>USD</th>
                                <th>Total (USD Equivalent)*</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Revenue</strong></td>
                                <td class="text-success">KSH {{ number_format($reportData['p_l_statement_ksh']['revenue'] ?? 0, 2) }}</td>
                                <td class="text-success">$ {{ number_format($reportData['p_l_statement_usd']['revenue'] ?? 0, 2) }}</td>
                                <td class="fw-bold">
                                    @php
                                        $exchangeRate = 130; // You should get this from your settings or API
                                        $totalRevenueUsd = ($reportData['p_l_statement_ksh']['revenue'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['revenue'] ?? 0);
                                    @endphp
                                    $ {{ number_format($totalRevenueUsd, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Cost of Services</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['cost_of_services'] ?? 0, 2) }})</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['cost_of_services'] ?? 0, 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $totalCostUsd = ($reportData['p_l_statement_ksh']['cost_of_services'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['cost_of_services'] ?? 0);
                                    @endphp
                                    ($ {{ number_format($totalCostUsd, 2) }})
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Gross Profit</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['p_l_statement_ksh']['gross_profit'] ?? 0, 2) }}</td>
                                <td class="fw-bold">$ {{ number_format($reportData['p_l_statement_usd']['gross_profit'] ?? 0, 2) }}</td>
                                <td class="fw-bold">
                                    @php
                                        $totalGrossProfitUsd = ($reportData['p_l_statement_ksh']['gross_profit'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['gross_profit'] ?? 0);
                                    @endphp
                                    $ {{ number_format($totalGrossProfitUsd, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Operating Expenses</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['operating_expenses'] ?? 0, 2) }})</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['operating_expenses'] ?? 0, 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $totalExpensesUsd = ($reportData['p_l_statement_ksh']['operating_expenses'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['operating_expenses'] ?? 0);
                                    @endphp
                                    ($ {{ number_format($totalExpensesUsd, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Depreciation</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['depreciation'] ?? 0, 2) }})</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['depreciation'] ?? 0, 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $totalDepreciationUsd = ($reportData['p_l_statement_ksh']['depreciation'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['depreciation'] ?? 0);
                                    @endphp
                                    ($ {{ number_format($totalDepreciationUsd, 2) }})
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Operating Profit</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['p_l_statement_ksh']['operating_profit'] ?? 0, 2) }}</td>
                                <td class="fw-bold">$ {{ number_format($reportData['p_l_statement_usd']['operating_profit'] ?? 0, 2) }}</td>
                                <td class="fw-bold">
                                    @php
                                        $totalOperatingProfitUsd = ($reportData['p_l_statement_ksh']['operating_profit'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['operating_profit'] ?? 0);
                                    @endphp
                                    $ {{ number_format($totalOperatingProfitUsd, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Interest Expense</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['interest_expense'] ?? 0, 2) }})</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['interest_expense'] ?? 0, 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $totalInterestUsd = ($reportData['p_l_statement_ksh']['interest_expense'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['interest_expense'] ?? 0);
                                    @endphp
                                    ($ {{ number_format($totalInterestUsd, 2) }})
                                </td>
                            </tr>
                            <tr>
                                <td>Taxes</td>
                                <td class="text-danger">(KSH {{ number_format($reportData['p_l_statement_ksh']['taxes'] ?? 0, 2) }})</td>
                                <td class="text-danger">($ {{ number_format($reportData['p_l_statement_usd']['taxes'] ?? 0, 2) }})</td>
                                <td class="text-danger">
                                    @php
                                        $totalTaxesUsd = ($reportData['p_l_statement_ksh']['taxes'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['taxes'] ?? 0);
                                    @endphp
                                    ($ {{ number_format($totalTaxesUsd, 2) }})
                                </td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Net Profit</strong></td>
                                <td class="fw-bold">KSH {{ number_format($reportData['p_l_statement_ksh']['net_profit'] ?? 0, 2) }}</td>
                                <td class="fw-bold">$ {{ number_format($reportData['p_l_statement_usd']['net_profit'] ?? 0, 2) }}</td>
                                <td class="fw-bold">
                                    @php
                                        $totalNetProfitUsd = ($reportData['p_l_statement_ksh']['net_profit'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['net_profit'] ?? 0);
                                    @endphp
                                    $ {{ number_format($totalNetProfitUsd, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="text-muted">* USD equivalent using exchange rate: 1 USD = {{ number_format($exchangeRate ?? 130, 2) }} KSH</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Profitability by Service -->
    <!-- Profitability by Service -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Profitability by Service Type</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Service Type</th>
                                <th>Currency</th>
                                <th>Revenue</th>
                                <th>Profit Margin</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($reportData['service_profitability'] ?? []) as $service)
                                @php
                                    // Handle both object and array formats
                                    $serviceType = is_object($service) ? ($service->service_type ?? 'Unknown') : ($service['service_type'] ?? 'Unknown');
                                    $currency = is_object($service) ? ($service->currency ?? 'ksh') : ($service['currency'] ?? 'ksh');
                                    $revenue = is_object($service) ? ($service->revenue ?? 0) : ($service['revenue'] ?? 0);
                                    $profitMargin = is_object($service) ? ($service->profit_margin ?? 0) : ($service['profit_margin'] ?? 0);
                                @endphp
                                <tr>
                                    <td class="text-capitalize">{{ $serviceType }}</td>
                                    <td>
                                        <span class="badge bg-{{ $currency == 'ksh' ? 'primary' : 'secondary' }}">
                                            {{ strtoupper($currency) }}
                                        </span>
                                    </td>
                                    <td>{{ $currency == 'ksh' ? 'KSH' : '$' }} {{ number_format($revenue, 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ number_format($profitMargin, 1) }}%</span>
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                @php
                                                    $width = min($profitMargin, 100);
                                                    $color = $profitMargin >= 40 ? 'success' : ($profitMargin >= 20 ? 'warning' : 'danger');
                                                @endphp
                                                <div class="progress-bar bg-{{ $color }}" style="width: {{ $width }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = $profitMargin >= 40 ? 'success' : ($profitMargin >= 20 ? 'warning' : 'danger');
                                            $status = $profitMargin >= 40 ? 'High' : ($profitMargin >= 20 ? 'Medium' : 'Low');
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No service profitability data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Profitability Chart -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Profitability by Service - Chart View</h6>
                </div>
                <div class="card-body">
                    <canvas id="profitabilityChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export functionality
        document.getElementById('exportBtn').addEventListener('click', function() {
            const tables = document.querySelectorAll('table');
            if (tables.length) {
                let csv = [];
                tables.forEach((table, index) => {
                    if (index > 0) csv.push(''); // Add separator between tables
                    const rows = table.querySelectorAll('tr');
                    rows.forEach(row => {
                        let rowData = [];
                        row.querySelectorAll('th, td').forEach(cell => {
                            rowData.push(cell.innerText);
                        });
                        csv.push(rowData.join(','));
                    });
                });

                const csvContent = csv.join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.setAttribute('hidden', '');
                a.setAttribute('href', url);
                a.setAttribute('download', 'financial_report_{{ $reportType }}_{{ $startDate }}_to_{{ $endDate }}.csv');
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                alert('No table data available for export.');
            }
        });

        // Debt Aging Chart KSH
        @if($reportType === 'debt_aging' && isset($reportData['debt_summary_ksh']))
        const debtAgingCtxKsh = document.getElementById('debtAgingChartKsh').getContext('2d');
        new Chart(debtAgingCtxKsh, {
            type: 'doughnut',
            data: {
                labels: ['Current', '1-30 Days', '31-60 Days', '61-90 Days', 'Over 90 Days'],
                datasets: [{
                    data: [
                        {{ $reportData['debt_summary_ksh']['current'] ?? 0 }},
                        {{ $reportData['debt_summary_ksh']['days_30'] ?? 0 }},
                        {{ $reportData['debt_summary_ksh']['days_60'] ?? 0 }},
                        {{ $reportData['debt_summary_ksh']['days_90'] ?? 0 }},
                        {{ $reportData['debt_summary_ksh']['days_over_90'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#fd7e14',
                        '#dc3545',
                        '#6f42c1'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        @endif

        // Debt Aging Chart USD
        @if($reportType === 'debt_aging' && isset($reportData['debt_summary_usd']))
        const debtAgingCtxUsd = document.getElementById('debtAgingChartUsd').getContext('2d');
        new Chart(debtAgingCtxUsd, {
            type: 'doughnut',
            data: {
                labels: ['Current', '1-30 Days', '31-60 Days', '61-90 Days', 'Over 90 Days'],
                datasets: [{
                    data: [
                        {{ $reportData['debt_summary_usd']['current'] ?? 0 }},
                        {{ $reportData['debt_summary_usd']['days_30'] ?? 0 }},
                        {{ $reportData['debt_summary_usd']['days_60'] ?? 0 }},
                        {{ $reportData['debt_summary_usd']['days_90'] ?? 0 }},
                        {{ $reportData['debt_summary_usd']['days_over_90'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#fd7e14',
                        '#dc3545',
                        '#6f42c1'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        @endif

        // Cash Flow Chart
        @if($reportType === 'cash_flow')
        const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
        new Chart(cashFlowCtx, {
            type: 'bar',
            data: {
                labels: ['Operating', 'Investing', 'Financing', 'Net Cash Flow'],
                datasets: [{
                    label: 'Cash Flow ($)',
                    data: [
                        {{ $reportData['cash_flow_summary']['operating'] ?? 0 }},
                        {{ $reportData['cash_flow_summary']['investing'] ?? 0 }},
                        {{ $reportData['cash_flow_summary']['financing'] ?? 0 }},
                        {{ $reportData['cash_flow_summary']['net_cash_flow'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#17a2b8',
                        '#ffc107',
                        '#007bff'
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        @endif

        // Profitability Chart
        @if($reportType === 'profitability' && isset($reportData['service_profitability']))
        const profitabilityCtx = document.getElementById('profitabilityChart').getContext('2d');
        new Chart(profitabilityCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(($reportData['service_profitability'] ?? collect())->pluck('service_type')) !!},
                datasets: [{
                    label: 'Profit Margin (%)',
                    data: {!! json_encode(($reportData['service_profitability'] ?? collect())->pluck('profit_margin')) !!},
                    backgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Profit Margin (%)'
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
