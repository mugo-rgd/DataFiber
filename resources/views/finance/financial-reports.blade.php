@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Financial Reports
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Total Revenue</h6>
                                    <h4 class="mb-0">
                                        @php
                                            $totalRevenue = \App\Models\Billing::where('status', 'paid')->sum('total_amount');
                                        @endphp
                                        ${{ number_format($totalRevenue, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">This Month</h6>
                                    <h4 class="mb-0">
                                        @php
                                            $monthRevenue = \App\Models\Billing::where('status', 'paid')
                                                ->whereBetween('paid_date', [now()->startOfMonth(), now()->endOfMonth()])
                                                ->sum('total_amount');
                                        @endphp
                                        ${{ number_format($monthRevenue, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Pending Invoices</h6>
                                    <h4 class="mb-0">
                                        @php
                                            $pendingInvoices = \App\Models\Billing::whereIn('status', ['sent', 'viewed', 'partial'])->count();
                                        @endphp
                                        {{ $pendingInvoices }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Overdue</h6>
                                    <h4 class="mb-0">
                                        @php
                                            $overdueInvoices = \App\Models\Billing::where('due_date', '<', now())
                                                ->whereIn('status', ['sent', 'viewed', 'partial'])
                                                ->count();
                                        @endphp
                                        {{ $overdueInvoices }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Cards -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Billing Reports
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p>Comprehensive billing and invoice reports with detailed analytics.</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Revenue by service type</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Customer billing history</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Tax collection reports</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Payment method analysis</li>
                                    </ul>
                                    <a href="{{ route('finance.reports', ['report_type' => 'financial_summary']) }}"
                                       class="btn btn-primary w-100">
                                        View Billing Reports
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-exchange-alt me-2"></i>Transaction Reports
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p>Detailed transaction analysis and financial movement tracking.</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Income vs expense analysis</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Cash flow reports</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Transaction categorization</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Payment method breakdown</li>
                                    </ul>
                                    <a href="{{ route('finance.transactions') }}" class="btn btn-success w-100">
                                        View Transaction Reports
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Analytics & Insights
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p>Advanced financial analytics and business intelligence.</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Revenue trend analysis</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Customer profitability</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Service performance</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Financial forecasting</li>
                                    </ul>
                                    <a href="{{ route('finance.reports', ['report_type' => 'revenue_analysis']) }}"
                                       class="btn btn-info w-100">
                                        View Analytics
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Quick Report Links</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('finance.reports', ['report_type' => 'financial_summary', 'period' => 'this_month']) }}"
                                               class="btn btn-outline-primary w-100">
                                                <i class="fas fa-chart-bar me-2"></i>Monthly Summary
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('finance.reports', ['report_type' => 'revenue_analysis', 'period' => 'this_quarter']) }}"
                                               class="btn btn-outline-success w-100">
                                                <i class="fas fa-chart-line me-2"></i>Revenue Analysis
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('finance.reports', ['report_type' => 'customer_billing', 'period' => 'this_year']) }}"
                                               class="btn btn-outline-info w-100">
                                                <i class="fas fa-users me-2"></i>Customer Reports
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('finance.reports', ['report_type' => 'aging_report']) }}"
                                               class="btn btn-outline-warning w-100">
                                                <i class="fas fa-clock me-2"></i>Aging Report
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('finance.reports', ['report_type' => 'tax_report', 'period' => 'this_year']) }}"
                                               class="btn btn-outline-danger w-100">
                                                <i class="fas fa-receipt me-2"></i>Tax Report
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('finance.billing') }}"
                                               class="btn btn-outline-secondary w-100">
                                                <i class="fas fa-file-invoice me-2"></i>All Invoices
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('finance.transactions') }}"
                                               class="btn btn-outline-dark w-100">
                                                <i class="fas fa-list me-2"></i>All Transactions
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <button class="btn btn-outline-primary w-100" onclick="window.print()">
                                                <i class="fas fa-print me-2"></i>Print Reports
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Recent Invoices</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $recentInvoices = \App\Models\Billing::with('customer')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
                                    @endphp
                                    @if($recentInvoices->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentInvoices as $invoice)
                                                <div class="list-group-item px-0">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">{{ $invoice->invoice_number }}</h6>
                                                        <small class="text-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($invoice->status) }}
                                                        </small>
                                                    </div>
                                                    <p class="mb-1">{{ $invoice->customer->name ?? 'Unknown' }}</p>
                                                    <small class="text-muted">${{ number_format($invoice->total_amount, 2) }} • {{ $invoice->created_at->diffForHumans() }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No recent invoices</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Recent Transactions</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $recentTransactions = \App\Models\Transaction::with('billing')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
                                    @endphp
                                    @if($recentTransactions->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentTransactions as $transaction)
                                                <div class="list-group-item px-0">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">{{ $transaction->description }}</h6>
                                                        <span class="badge bg-{{ $transaction->type == 'income' ? 'success' : 'danger' }}">
                                                            {{ ucfirst($transaction->type) }}
                                                        </span>
                                                    </div>
                                                    <p class="mb-1">${{ number_format($transaction->amount, 2) }}</p>
                                                    <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No recent transactions</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
