@php
use App\Models\ConsolidatedBilling;
use Carbon\Carbon;
@endphp
@extends('layouts.app')

@section('title', 'Finance Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">
                <i class="fas fa-chart-line text-primary me-2"></i>Finance Dashboard
            </h1>
            <p class="text-muted mb-0">Financial overview and real-time metrics</p>
            <div class="small text-muted">
                <i class="fas fa-calendar-alt me-1"></i>
                {{ now()->format('F d, Y') }} |
                <i class="fas fa-clock me-1 ms-2"></i>
                {{ now()->format('h:i A') }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" id="refreshBtn">
                <i class="fas fa-sync-alt"></i>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- USD Summary Cards -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-3">
                <span class="badge bg-primary me-2">USD</span> US Dollar Summary
            </h5>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">TOTAL REVENUE (USD)</h6>
                            <h2 class="card-title mb-0">{{ $financialMetrics['usd']['total_revenue']['formatted'] ?? '$0.00' }}</h2>
                            @if(isset($financialMetrics['usd']['total_revenue']['change']) && $financialMetrics['usd']['total_revenue']['change'] != 0)
                                <div class="mt-2">
                                    <span class="badge bg-white text-primary">
                                        <i class="fas fa-arrow-{{ $financialMetrics['usd']['total_revenue']['change'] > 0 ? 'up' : 'down' }} me-1"></i>
                                        {{ abs($financialMetrics['usd']['total_revenue']['change']) }}%
                                    </span>
                                    <small class="opacity-75">vs last month</small>
                                </div>
                            @endif
                        </div>
                        <div class="icon-circle bg-white-20">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card bg-gradient-warning">
                <div class="card-body text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">PENDING BILLINGS (USD)</h6>
                            <h2 class="card-title mb-0">{{ $financialMetrics['usd']['pending_invoices']['value'] ?? 0 }}</h2>
                            @if(isset($financialMetrics['usd']['pending_invoices']['amount']))
                                <div class="mt-2">
                                    <small class="opacity-75">Amount: {{ $financialMetrics['usd']['pending_invoices']['formatted_amount'] ?? '$0.00' }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="icon-circle bg-warning-20">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">OVERDUE PAYMENTS (USD)</h6>
                            <h2 class="card-title mb-0">{{ $financialMetrics['usd']['overdue_payments']['value'] ?? 0 }}</h2>
                            @if(isset($financialMetrics['usd']['overdue_payments']['amount']))
                                <div class="mt-2">
                                    <small class="opacity-75">Amount: {{ $financialMetrics['usd']['overdue_payments']['formatted_amount'] ?? '$0.00' }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="icon-circle bg-white-20">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card" style="background: linear-gradient(45deg, #36b9cc 0%, #258391 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">INVOICED AMOUNT (USD)</h6>
                            <h2 class="card-title mb-0">{{ $financialMetrics['usd']['invoiced_amount']['formatted'] ?? '$0.00' }}</h2>
                        </div>
                        <div class="icon-circle bg-white-20">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KSH Summary Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <span class="badge bg-success me-2">KSH</span> Kenyan Shilling Summary
            </h5>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card" style="background: linear-gradient(45deg, #20c997 0%, #0f8a6c 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">TOTAL REVENUE (KSH)</h6>
                            <h2 class="card-title mb-0">{{ $financialMetrics['ksh']['total_revenue']['formatted'] ?? 'KSH 0.00' }}</h2>
                            @if(isset($financialMetrics['ksh']['total_revenue']['change']) && $financialMetrics['ksh']['total_revenue']['change'] != 0)
                                <div class="mt-2">
                                    <span class="badge bg-white text-success">
                                        <i class="fas fa-arrow-{{ $financialMetrics['ksh']['total_revenue']['change'] > 0 ? 'up' : 'down' }} me-1"></i>
                                        {{ abs($financialMetrics['ksh']['total_revenue']['change']) }}%
                                    </span>
                                    <small class="opacity-75">vs last month</small>
                                </div>
                            @endif
                        </div>
                        <div class="icon-circle bg-white-20">
                            <i class="fas fa-shilling-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card bg-gradient-warning">
                <div class="card-body text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">PENDING BILLINGS (KSH)</h6>
                            <h2 class="card-title mb-0">{{ $financialMetrics['ksh']['pending_invoices']['value'] ?? 0 }}</h2>
                            @if(isset($financialMetrics['ksh']['pending_invoices']['amount']))
                                <div class="mt-2">
                                    <small class="opacity-75">Amount: {{ $financialMetrics['ksh']['pending_invoices']['formatted_amount'] ?? 'KSH 0.00' }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="icon-circle bg-warning-20">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">OVERDUE PAYMENTS (KSH)</h6>
                            <h2 class="card-title mb-0">{{ $financialMetrics['ksh']['overdue_payments']['value'] ?? 0 }}</h2>
                            @if(isset($financialMetrics['ksh']['overdue_payments']['amount']))
                                <div class="mt-2">
                                    <small class="opacity-75">Amount: {{ $financialMetrics['ksh']['overdue_payments']['formatted_amount'] ?? 'KSH 0.00' }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="icon-circle bg-white-20">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card" style="background: linear-gradient(45deg, #fd7e14 0%, #dc710a 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">INVOICED AMOUNT (KSH)</h6>
                            <h2 class="card-title mb-0">{{ $financialMetrics['ksh']['invoiced_amount']['formatted'] ?? 'KSH 0.00' }}</h2>
                        </div>
                        <div class="icon-circle bg-white-20">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Combined Summary (USD Equivalent) -->
    <div class="row mb-4">
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <div class="row text-center">
                    <!-- Total Revenue -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.15);">
                            <h6 class="text-white-50 mb-2">
                                <i class="fas fa-chart-line me-1"></i>Total Revenue
                            </h6>
                            <h3 class="mb-0 text-white">{{ $financialMetrics['combined']['total_revenue']['formatted'] ?? '$0.00' }}</h3>
                            <small class="text-white-50">
                                <i class="fas fa-exchange-alt me-1"></i>
                                KSH {{ number_format(($financialMetrics['combined']['total_revenue']['value'] ?? 0) * ($financialMetrics['combined']['exchange_rate'] ?? 130), 2) }}
                            </small>
                        </div>
                    </div>

                    <!-- Pending Amount -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.15);">
                            <h6 class="text-white-50 mb-2">
                                <i class="fas fa-clock me-1"></i>Pending Amount
                            </h6>
                            <h3 class="mb-0 text-warning">{{ $financialMetrics['combined']['pending_amount']['formatted'] ?? '$0.00' }}</h3>
                            <small class="text-white-50">
                                <i class="fas fa-exchange-alt me-1"></i>
                                KSH {{ number_format(($financialMetrics['combined']['pending_amount']['value'] ?? 0) * ($financialMetrics['combined']['exchange_rate'] ?? 130), 2) }}
                            </small>
                        </div>
                    </div>

                    <!-- Overdue Amount -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.15);">
                            <h6 class="text-white-50 mb-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>Overdue Amount
                            </h6>
                            <h3 class="mb-0 text-danger">{{ $financialMetrics['combined']['overdue_amount']['formatted'] ?? '$0.00' }}</h3>
                            <small class="text-white-50">
                                <i class="fas fa-exchange-alt me-1"></i>
                                KSH {{ number_format(($financialMetrics['combined']['overdue_amount']['value'] ?? 0) * ($financialMetrics['combined']['exchange_rate'] ?? 130), 2) }}
                            </small>
                        </div>
                    </div>

                    <!-- Invoiced Amount -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.15);">
                            <h6 class="text-white-50 mb-2">
                                <i class="fas fa-file-invoice me-1"></i>Invoiced Amount
                            </h6>
                            <h3 class="mb-0 text-info">{{ $financialMetrics['combined']['invoiced_amount']['formatted'] ?? '$0.00' }}</h3>
                            <small class="text-white-50">
                                <i class="fas fa-exchange-alt me-1"></i>
                                KSH {{ number_format(($financialMetrics['combined']['invoiced_amount']['value'] ?? 0) * ($financialMetrics['combined']['exchange_rate'] ?? 130), 2) }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Exchange Rate Info -->
                <div class="text-center mt-3 pt-2 border-top border-white-20">
                    <small class="text-white-50">
                        <i class="fas fa-info-circle me-1"></i>
                        Exchange Rate: 1 USD = {{ number_format($financialMetrics['combined']['exchange_rate'] ?? 130, 2) }} KSH
                        <span class="mx-2">•</span>
                        <i class="fas fa-sync-alt me-1"></i>
                        Last updated: {{ now()->format('M d, Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-white-20 {
    border-color: rgba(255, 255, 255, 0.2) !important;
}
.text-white-50 {
    color: rgba(255, 255, 255, 0.7) !important;
}
</style>

    <!-- Additional Metrics -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border h-100">
                <div class="card-body text-center p-3">
                    <div class="text-primary mb-2">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div class="h4 mb-1">{{ ($financialMetrics['usd']['paid_invoices']['value'] ?? 0) + ($financialMetrics['ksh']['paid_invoices']['value'] ?? 0) }}</div>
                    <small class="text-muted">Paid Invoices</small>
                    <div class="small text-muted">USD: {{ $financialMetrics['usd']['paid_invoices']['value'] ?? 0 }} | KSH: {{ $financialMetrics['ksh']['paid_invoices']['value'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border h-100">
                <div class="card-body text-center p-3">
                    <div class="text-success mb-2">
                        <i class="fas fa-cash-register fa-2x"></i>
                    </div>
                    <div class="h5 mb-0">{{ $financialMetrics['usd']['monthly_revenue']['formatted'] ?? '$0.00' }}</div>
                    <small class="text-muted">Monthly Revenue</small>
                    <div class="small text-muted">{{ $financialMetrics['ksh']['monthly_revenue']['formatted'] ?? 'KSH 0.00' }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border h-100">
                <div class="card-body text-center p-3">
                    <div class="text-info mb-2">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                    <div class="h4 mb-1">{{ $financialMetrics['new_customers']['value'] ?? 0 }}</div>
                    <small class="text-muted">New Customers</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border h-100">
                <div class="card-body text-center p-3">
                    <div class="text-warning mb-2">
                        <i class="fas fa-percentage fa-2x"></i>
                    </div>
                    <div class="h4 mb-1">{{ $financialMetrics['combined']['collection_rate']['value'] ?? 0 }}%</div>
                    <small class="text-muted">Collection Rate</small>
                    <div class="small text-muted">USD: {{ $financialMetrics['usd']['collection_rate']['value'] ?? 0 }}% | KSH: {{ $financialMetrics['ksh']['collection_rate']['value'] ?? 0 }}%</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border h-100">
                <div class="card-body text-center p-3">
                    <div class="text-danger mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div class="h4 mb-1">{{ $financialMetrics['avg_payment_days']['value'] ?? 0 }}</div>
                    <small class="text-muted">Avg Payment Days</small>
                    @if(isset($financialMetrics['avg_payment_days']['trend']) && $financialMetrics['avg_payment_days']['trend'] != 'neutral')
                        <span class="badge bg-{{ $financialMetrics['avg_payment_days']['trend_color'] ?? 'secondary' }} ms-2">
                            <i class="fas fa-{{ $financialMetrics['avg_payment_days']['trend_icon'] ?? 'minus' }}"></i>
                            {{ $financialMetrics['avg_payment_days']['trend'] == 'positive' ? 'Improving' : 'Slowing' }}
                        </span>
                    @endif
                </div>
                @if(isset($financialMetrics['avg_payment_days']['subtitle']))
                <div class="mt-2 small text-muted text-center">
                    <i class="fas fa-info-circle"></i> {{ $financialMetrics['avg_payment_days']['subtitle'] }}
                </div>
                @endif
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border h-100">
                <div class="card-body text-center p-3">
                    <div class="text-purple mb-2">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="h4 mb-1">{{ $financialMetrics['active_customers']['value'] ?? 0 }}</div>
                    <small class="text-muted">Active Customers</small>
                    @if(isset($financialMetrics['active_customers']['change']) && $financialMetrics['active_customers']['change'] != 0)
                        <div class="small text-{{ $financialMetrics['active_customers']['change'] > 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-arrow-{{ $financialMetrics['active_customers']['change'] > 0 ? 'up' : 'down' }}"></i>
                            {{ abs($financialMetrics['active_customers']['change']) }} this month
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

     <!-- Quick Actions & Recent Transactions -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h5>
                    <span class="badge bg-primary">8 Actions</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('finance.billing.index') }}" class="card action-card text-center h-100">
                                <div class="card-body">
                                    <div class="action-icon bg-primary mb-3">
                                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                    </div>
                                    <h6 class="card-title mb-2">Manage Billings</h6>
                                    <p class="card-text small text-muted">Create, view, and manage invoices</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('finance.payments.followups') }}" class="card action-card text-center h-100">
                                <div class="card-body">
                                    <div class="action-icon bg-success mb-3">
                                        <i class="fas fa-credit-card fa-2x"></i>
                                    </div>
                                    <h6 class="card-title mb-2">View Payments</h6>
                                    <p class="card-text small text-muted">Track customer payments</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('finance.reports') }}" class="card action-card text-center h-100">
                                <div class="card-body">
                                    <div class="action-icon bg-info mb-3">
                                        <i class="fas fa-chart-bar fa-2x"></i>
                                    </div>
                                    <h6 class="card-title mb-2">Financial Reports</h6>
                                    <p class="card-text small text-muted">Generate reports & analytics</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('finance.auto-billing') }}" class="card action-card text-center h-100">
                                <div class="card-body">
                                    <div class="action-icon bg-secondary mb-3">
                                        <i class="fas fa-robot fa-2x"></i>
                                    </div>
                                    <h6 class="card-title mb-2">Auto Billing</h6>
                                    <p class="card-text small text-muted">Configure automated billing</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('finance.transactions.index') }}" class="card action-card text-center h-100">
                                <div class="card-body">
                                    <div class="action-icon bg-warning mb-3">
                                        <i class="fas fa-exchange-alt fa-2x"></i>
                                    </div>
                                    <h6 class="card-title mb-2">Transactions</h6>
                                    <p class="card-text small text-muted">View all transactions</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('finance.financial-parameters.index') }}" class="card action-card text-center h-100">
                                <div class="card-body">
                                    <div class="action-icon bg-dark mb-3">
                                        <i class="fas fa-percentage fa-2x"></i>
                                    </div>
                                    <h6 class="card-title mb-2">Financial Parameters</h6>
                                    <p class="card-text small text-muted">Configure rates & taxes</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('finance.sap-assignment.index') }}" class="card action-card text-center h-100">
                                <div class="card-body">
                                    <div class="action-icon bg-purple mb-3">
                                        <i class="fas fa-database fa-2x"></i>
                                    </div>
                                    <h6 class="card-title mb-2">Master Data</h6>
                                    <p class="card-text small text-muted">Assign SAP accounts</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('finance.financial-reports') }}" class="card action-card text-center h-100">
                                <div class="card-body">
                                    <div class="action-icon bg-danger mb-3">
                                        <i class="fas fa-file-excel fa-2x"></i>
                                    </div>
                                    <h6 class="card-title mb-2">Export Reports</h6>
                                    <p class="card-text small text-muted">Export financial data</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-info me-2"></i>Recent Transactions
                        <span class="badge bg-info float-end">{{ $recentTransactions->count() ?? 0 }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentTransactions as $transaction)
                            <div class="list-group-item border-0 px-4 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $transaction->description ?? 'Transaction' }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="h6 mb-1 @if(($transaction->type ?? '') === 'debit') text-danger @else text-success @endif">
                                            ${{ number_format($transaction->amount ?? 0, 2) }}
                                        </div>
                                        <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($transaction->status ?? 'pending') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent transactions</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('finance.transactions.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>View All Transactions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Revenue Trend
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($revenueTrends) && count($revenueTrends['months'] ?? []) > 0)
                        <canvas id="revenueChart" height="250"></canvas>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                            <p class="text-muted">Revenue chart would appear here</p>
                            <small class="text-muted">Connect to your data source to display charts</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-success me-2"></i>Payment Status Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-pie-chart fa-4x text-muted mb-3"></i>
                        <p class="text-muted">Payment distribution chart would appear here</p>
                        <small class="text-muted">Connect to your data source to display charts</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Invoices (if available) -->
    @if(isset($overdueInvoices) && $overdueInvoices->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Overdue Invoices
                        <span class="badge bg-white text-danger float-end">{{ $overdueInvoices->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Overdue Days</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueInvoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->customer_name }}</td>
                                    <td class="text-danger fw-bold">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                                    <td class="fw-bold">
                                        @if($invoice->currency == 'USD')
                                            ${{ number_format($invoice->amount, 2) }}
                                        @else
                                            KSH {{ number_format($invoice->amount, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->currency == 'USD' ? 'primary' : 'success' }}">
                                            {{ $invoice->currency }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ \Carbon\Carbon::parse($invoice->due_date)->diffInDays(now()) }} days
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-envelope me-1"></i>Remind
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    /* Keep your existing styles */
    .summary-card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-2px);
    }
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(45deg, #f6c23e 0%, #dda20a 100%);
    }
    .bg-gradient-danger {
        background: linear-gradient(45deg, #e74a3b 0%, #be2617 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a 0%, #13855c 100%);
    }
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-white-20 {
        background: rgba(255, 255, 255, 0.2);
    }
    .bg-warning-20 {
        background: rgba(255, 255, 255, 0.2);
    }
    .action-card {
        border: 1px solid #e3e6f0;
        transition: all 0.3s;
        text-decoration: none;
        color: inherit;
    }
    .action-card:hover {
        border-color: #4e73df;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.1);
    }
    .action-icon {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
    }
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    .text-purple {
        color: #6f42c1 !important;
    }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            setTimeout(() => {
                icon.classList.remove('fa-spin');
                location.reload();
            }, 500);
        });
    }

    // Revenue Chart
    @if(isset($revenueTrends) && count($revenueTrends['months'] ?? []) > 0)
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueTrends['months'] ?? []),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($revenueTrends['revenues'] ?? []),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endsection
@endsection
