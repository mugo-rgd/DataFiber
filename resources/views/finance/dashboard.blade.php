@php
use App\Models\ConsolidatedBilling;
use Carbon\Carbon;
@endphp

@extends('layouts.app')

@section('title', 'Finance Dashboard - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">

    {{-- Hero Section --}}
    <div class="dashboard-hero text-white py-4 py-md-5">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-4">

                {{-- Left Column - Welcome --}}
                <div class="col-12 col-lg-7">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="hero-icon">
                            <i class="fas fa-chart-line fa-3x fa-fw"></i>
                        </div>
                        <div>
                            <h1 class="display-5 fw-bold mb-2">Finance Dashboard</h1>
                            <p class="lead mb-0 opacity-90">
                                Welcome back, <strong>{{ Auth::user()->name ?? 'User' }}</strong>!
                            </p>
                        </div>
                    </div>

                    {{-- Meta Info --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ now()->format('l, F j, Y') }}
                        </span>
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="far fa-clock me-1"></i>
                            {{ now()->format('h:i A') }}
                        </span>
                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            <i class="fas fa-circle me-1 small"></i> Live Updates
                        </span>
                    </div>
                </div>

                {{-- Right Column - Actions --}}
                <div class="col-12 col-lg-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @include('partials.role-help-widget')

                        <button class="btn btn-light btn-dashboard-action" id="refreshBtn">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light btn-dashboard-action">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-4">

        {{-- USD Summary Section --}}
        <div class="row mb-4">
            <div class="col-12 mb-3">
                <h4 class="fw-bold">
                    <span class="badge bg-kp-blue px-3 py-2 rounded-pill">USD</span>
                    <span class="ms-2 text-dark">US Dollar Summary</span>
                </h4>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card bg-gradient-kp-blue text-white rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="opacity-75 mb-2">TOTAL REVENUE (USD)</h6>
                            <div class="metric-value-large fw-bold">{{ $financialMetrics['usd']['total_revenue']['formatted'] ?? '$0.00' }}</div>
                            @if(isset($financialMetrics['usd']['total_revenue']['change']) && $financialMetrics['usd']['total_revenue']['change'] != 0)
                                <div class="mt-2">
                                    <span class="badge bg-white text-kp-blue rounded-pill">
                                        <i class="fas fa-arrow-{{ $financialMetrics['usd']['total_revenue']['change'] > 0 ? 'up' : 'down' }} me-1"></i>
                                        {{ abs($financialMetrics['usd']['total_revenue']['change']) }}%
                                    </span>
                                    <small class="opacity-75 ms-1">vs last month</small>
                                </div>
                            @endif
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card bg-gradient-kp-yellow text-dark rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="opacity-75 mb-2">PENDING BILLINGS (USD)</h6>
                            <div class="metric-value-large fw-bold">{{ $financialMetrics['usd']['pending_invoices']['value'] ?? 0 }}</div>
                            @if(isset($financialMetrics['usd']['pending_invoices']['amount']))
                                <div class="mt-2">
                                    <small class="opacity-75">Amount: {{ $financialMetrics['usd']['pending_invoices']['formatted_amount'] ?? '$0.00' }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="metric-icon-large bg-dark-20 rounded-3">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card bg-gradient-danger text-white rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="opacity-75 mb-2">OVERDUE PAYMENTS (USD)</h6>
                            <div class="metric-value-large fw-bold">{{ $financialMetrics['usd']['overdue_payments']['value'] ?? 0 }}</div>
                            @if(isset($financialMetrics['usd']['overdue_payments']['amount']))
                                <div class="mt-2">
                                    <small class="opacity-75">Amount: {{ $financialMetrics['usd']['overdue_payments']['formatted_amount'] ?? '$0.00' }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card bg-gradient-info text-white rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="opacity-75 mb-2">INVOICED AMOUNT (USD)</h6>
                            <div class="metric-value-large fw-bold">{{ $financialMetrics['usd']['invoiced_amount']['formatted'] ?? '$0.00' }}</div>
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KSH Summary Section --}}
        <div class="row mb-4">
            <div class="col-12 mb-3">
                <h4 class="fw-bold">
                    <span class="badge bg-kp-green px-3 py-2 rounded-pill">KSH</span>
                    <span class="ms-2 text-dark">Kenyan Shilling Summary</span>
                </h4>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card bg-gradient-kp-green text-white rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="opacity-75 mb-2">TOTAL REVENUE (KSH)</h6>
                            <div class="metric-value-large fw-bold">{{ $financialMetrics['ksh']['total_revenue']['formatted'] ?? 'KSH 0.00' }}</div>
                            @if(isset($financialMetrics['ksh']['total_revenue']['change']) && $financialMetrics['ksh']['total_revenue']['change'] != 0)
                                <div class="mt-2">
                                    <span class="badge bg-white text-kp-green rounded-pill">
                                        <i class="fas fa-arrow-{{ $financialMetrics['ksh']['total_revenue']['change'] > 0 ? 'up' : 'down' }} me-1"></i>
                                        {{ abs($financialMetrics['ksh']['total_revenue']['change']) }}%
                                    </span>
                                    <small class="opacity-75 ms-1">vs last month</small>
                                </div>
                            @endif
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-shilling-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card bg-gradient-kp-yellow text-dark rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="opacity-75 mb-2">PENDING BILLINGS (KSH)</h6>
                            <div class="metric-value-large fw-bold">{{ $financialMetrics['ksh']['pending_invoices']['value'] ?? 0 }}</div>
                            @if(isset($financialMetrics['ksh']['pending_invoices']['amount']))
                                <div class="mt-2">
                                    <small class="opacity-75">Amount: {{ $financialMetrics['ksh']['pending_invoices']['formatted_amount'] ?? 'KSH 0.00' }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="metric-icon-large bg-dark-20 rounded-3">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card bg-gradient-danger text-white rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="opacity-75 mb-2">OVERDUE PAYMENTS (KSH)</h6>
                            <div class="metric-value-large fw-bold">{{ $financialMetrics['ksh']['overdue_payments']['value'] ?? 0 }}</div>
                            @if(isset($financialMetrics['ksh']['overdue_payments']['amount']))
                                <div class="mt-2">
                                    <small class="opacity-75">Amount: {{ $financialMetrics['ksh']['overdue_payments']['formatted_amount'] ?? 'KSH 0.00' }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card bg-gradient-orange text-white rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="opacity-75 mb-2">INVOICED AMOUNT (KSH)</h6>
                            <div class="metric-value-large fw-bold">{{ $financialMetrics['ksh']['invoiced_amount']['formatted'] ?? 'KSH 0.00' }}</div>
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Combined Summary Card --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="combined-card rounded-4 p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded-3 bg-white-10">
                                <h6 class="text-white-70 mb-2">
                                    <i class="fas fa-chart-line me-1"></i>Total Revenue
                                </h6>
                                <h3 class="mb-0 text-white fw-bold">{{ $financialMetrics['combined']['total_revenue']['formatted'] ?? '$0.00' }}</h3>
                                <small class="text-white-50">
                                    <i class="fas fa-exchange-alt me-1"></i>
                                    KSH {{ number_format(($financialMetrics['combined']['total_revenue']['value'] ?? 0) * ($financialMetrics['combined']['exchange_rate'] ?? 130), 2) }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded-3 bg-white-10">
                                <h6 class="text-white-70 mb-2">
                                    <i class="fas fa-clock me-1"></i>Pending Amount
                                </h6>
                                <h3 class="mb-0 text-kp-yellow fw-bold">{{ $financialMetrics['combined']['pending_amount']['formatted'] ?? '$0.00' }}</h3>
                                <small class="text-white-50">
                                    <i class="fas fa-exchange-alt me-1"></i>
                                    KSH {{ number_format(($financialMetrics['combined']['pending_amount']['value'] ?? 0) * ($financialMetrics['combined']['exchange_rate'] ?? 130), 2) }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded-3 bg-white-10">
                                <h6 class="text-white-70 mb-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Overdue Amount
                                </h6>
                                <h3 class="mb-0 text-danger fw-bold">{{ $financialMetrics['combined']['overdue_amount']['formatted'] ?? '$0.00' }}</h3>
                                <small class="text-white-50">
                                    <i class="fas fa-exchange-alt me-1"></i>
                                    KSH {{ number_format(($financialMetrics['combined']['overdue_amount']['value'] ?? 0) * ($financialMetrics['combined']['exchange_rate'] ?? 130), 2) }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded-3 bg-white-10">
                                <h6 class="text-white-70 mb-2">
                                    <i class="fas fa-file-invoice me-1"></i>Invoiced Amount
                                </h6>
                                <h3 class="mb-0 text-info fw-bold">{{ $financialMetrics['combined']['invoiced_amount']['formatted'] ?? '$0.00' }}</h3>
                                <small class="text-white-50">
                                    <i class="fas fa-exchange-alt me-1"></i>
                                    KSH {{ number_format(($financialMetrics['combined']['invoiced_amount']['value'] ?? 0) * ($financialMetrics['combined']['exchange_rate'] ?? 130), 2) }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4 pt-2 border-top border-white-20">
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

        {{-- Additional Metrics Row --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="small-metric-card text-center p-3 rounded-4 h-100">
                    <div class="metric-icon-sm bg-kp-blue-light rounded-3 mx-auto mb-2">
                        <i class="fas fa-check-circle fa-fw text-kp-blue"></i>
                    </div>
                    <div class="h3 mb-0 fw-bold">{{ ($financialMetrics['usd']['paid_invoices']['value'] ?? 0) + ($financialMetrics['ksh']['paid_invoices']['value'] ?? 0) }}</div>
                    <small class="text-muted">Paid Invoices</small>
                    <div class="small text-muted mt-1">
                        USD: {{ $financialMetrics['usd']['paid_invoices']['value'] ?? 0 }} | KSH: {{ $financialMetrics['ksh']['paid_invoices']['value'] ?? 0 }}
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="small-metric-card text-center p-3 rounded-4 h-100">
                    <div class="metric-icon-sm bg-kp-green-light rounded-3 mx-auto mb-2">
                        <i class="fas fa-cash-register fa-fw text-kp-green"></i>
                    </div>
                    <div class="h5 mb-0 fw-bold">{{ $financialMetrics['usd']['monthly_revenue']['formatted'] ?? '$0.00' }}</div>
                    <small class="text-muted">Monthly Revenue</small>
                    <div class="small text-muted mt-1">{{ $financialMetrics['ksh']['monthly_revenue']['formatted'] ?? 'KSH 0.00' }}</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="small-metric-card text-center p-3 rounded-4 h-100">
                    <div class="metric-icon-sm bg-info-light rounded-3 mx-auto mb-2">
                        <i class="fas fa-user-plus fa-fw text-info"></i>
                    </div>
                    <div class="h3 mb-0 fw-bold">{{ $financialMetrics['new_customers']['value'] ?? 0 }}</div>
                    <small class="text-muted">New Customers</small>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="small-metric-card text-center p-3 rounded-4 h-100">
                    <div class="metric-icon-sm bg-kp-yellow-light rounded-3 mx-auto mb-2">
                        <i class="fas fa-percentage fa-fw text-warning"></i>
                    </div>
                    <div class="h3 mb-0 fw-bold">{{ $financialMetrics['combined']['collection_rate']['value'] ?? 0 }}%</div>
                    <small class="text-muted">Collection Rate</small>
                    <div class="small text-muted mt-1">USD: {{ $financialMetrics['usd']['collection_rate']['value'] ?? 0 }}% | KSH: {{ $financialMetrics['ksh']['collection_rate']['value'] ?? 0 }}%</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="small-metric-card text-center p-3 rounded-4 h-100">
                    <div class="metric-icon-sm bg-danger-light rounded-3 mx-auto mb-2">
                        <i class="fas fa-clock fa-fw text-danger"></i>
                    </div>
                    <div class="h3 mb-0 fw-bold">{{ $financialMetrics['avg_payment_days']['value'] ?? 0 }}</div>
                    <small class="text-muted">Avg Payment Days</small>
                    @if(isset($financialMetrics['avg_payment_days']['trend']) && $financialMetrics['avg_payment_days']['trend'] != 'neutral')
                        <span class="badge bg-{{ $financialMetrics['avg_payment_days']['trend_color'] ?? 'secondary' }} rounded-pill mt-1">
                            <i class="fas fa-{{ $financialMetrics['avg_payment_days']['trend_icon'] ?? 'minus' }}"></i>
                            {{ $financialMetrics['avg_payment_days']['trend'] == 'positive' ? 'Improving' : 'Slowing' }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="small-metric-card text-center p-3 rounded-4 h-100">
                    <div class="metric-icon-sm bg-purple-light rounded-3 mx-auto mb-2">
                        <i class="fas fa-users fa-fw text-purple"></i>
                    </div>
                    <div class="h3 mb-0 fw-bold">{{ $financialMetrics['active_customers']['value'] ?? 0 }}</div>
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

        {{-- Quick Actions & Recent Transactions --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                            </h5>
                            <span class="badge bg-kp-blue rounded-pill px-3 py-2">8 Actions</span>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-3">
                            @php
   $quickActions = [
    // Billing & Payments
    ['title' => 'Manage Billings', 'icon' => 'file-invoice', 'color' => 'primary', 'route' => 'finance.billing.index'],
    ['title' => 'View Payments', 'icon' => 'credit-card', 'color' => 'success', 'route' => 'finance.payments.index'],
    ['title' => 'Manual Payment Update', 'icon' => 'hand-holding-usd', 'color' => 'kp-yellow', 'route' => 'finance.payments.create'],
    ['title' => 'Payment Follow-ups', 'icon' => 'bell', 'color' => 'warning', 'route' => 'finance.payments.followups'],

    // Reports & Analytics
    ['title' => 'Financial Reports', 'icon' => 'chart-line', 'color' => 'info', 'route' => 'finance.financial-reports'],
    ['title' => 'Aging Report', 'icon' => 'calendar-alt', 'color' => 'teal', 'route' => 'finance.debt.aging.report'],
    ['title' => 'Collection Report', 'icon' => 'chart-pie', 'color' => 'purple', 'route' => 'finance.debt.collection.report'],

    // Debt Management
    ['title' => 'Debt Dashboard', 'icon' => 'exclamation-triangle', 'color' => 'danger', 'route' => 'finance.debt.dashboard'],
    ['title' => 'Overdue Invoices', 'icon' => 'clock', 'color' => 'danger', 'route' => 'finance.debt.overdue-invoices'],
    ['title' => 'Debt Customers', 'icon' => 'users', 'color' => 'warning', 'route' => 'finance.debt.customers'],

    // Automation & Configuration
    ['title' => 'Auto Billing', 'icon' => 'clock', 'color' => 'warning', 'route' => 'finance.auto-billing'],
    ['title' => 'Financial Parameters', 'icon' => 'sliders-h', 'color' => 'dark', 'route' => 'finance.financial-parameters.index'],
    ['title' => 'SAP Assignment', 'icon' => 'building', 'color' => 'info', 'route' => 'finance.sap-assignment.index'],
    ['title' => 'Transactions', 'icon' => 'exchange-alt', 'color' => 'secondary', 'route' => 'finance.transactions.index'],

    // Export
    ['title' => 'Export Debt Data', 'icon' => 'download', 'color' => 'teal', 'route' => 'finance.debt.export'],
];
                            @endphp
<div class="row g-3">
    @foreach($quickActions as $action)
        <div class="col-6 col-md-3">
            @if(Route::has($action['route']))
                <a href="{{ route($action['route']) }}" class="action-card text-center p-3 rounded-3 border h-100 text-decoration-none d-block">
                    <div class="action-icon bg-{{ $action['color'] }} rounded-3 mx-auto mb-2">
                        <i class="fas fa-{{ $action['icon'] }} fa-fw"></i>
                    </div>
                    <h6 class="fw-semibold mb-0">{{ $action['title'] }}</h6>
                </a>
            @else
                <div class="action-card text-center p-3 rounded-3 border h-100 text-decoration-none d-block opacity-50" style="cursor: not-allowed; background-color: #f8f9fa;">
                    <div class="action-icon bg-secondary rounded-3 mx-auto mb-2">
                        <i class="fas fa-{{ $action['icon'] }} fa-fw"></i>
                    </div>
                    <h6 class="fw-semibold mb-0">{{ $action['title'] }}</h6>
                    <small class="text-muted">Route not found</small>
                </div>
            @endif
        </div>
    @endforeach
</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-history text-info me-2"></i>Recent Transactions
                        </h5>
                        <span class="badge bg-info rounded-pill">{{ $recentTransactions->count() ?? 0 }}</span>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                            @foreach($recentTransactions as $transaction)
                                <div class="transaction-item p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $transaction->description ?? 'Transaction' }}</h6>
                                            <small class="text-muted">
                                                <i class="far fa-calendar me-1"></i>
                                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold @if(($transaction->type ?? '') === 'debit') text-danger @else text-kp-green @endif">
                                                ${{ number_format($transaction->amount ?? 0, 2) }}
                                            </div>
                                            <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'secondary') }} rounded-pill">
                                                {{ ucfirst($transaction->status ?? 'pending') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exchange-alt fa-3x text-muted opacity-25 mb-3"></i>
                                <p class="text-muted">No recent transactions</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center pb-4">
                        <a href="{{ route('finance.transactions.index') }}" class="btn btn-sm btn-outline-kp-blue rounded-pill px-4">
                            View All Transactions <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-line text-kp-blue me-2"></i>Revenue Trend (USD)
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if(isset($revenueTrends) && count($revenueTrends['months'] ?? []) > 0)
                            <canvas id="revenueChart" height="250"></canvas>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-chart-line fa-4x text-muted opacity-25 mb-3"></i>
                                <p class="text-muted">Connect to data source to display charts</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Payment Status Distribution -->
<div class="card shadow mt-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Payment Distribution</h6>
    </div>
    <div class="card-body">
        @php
            $paymentDistribution = $paymentDistributionData ?? [];
            $paymentMethods = $paymentDistribution['payment_methods'] ?? collect();
            $statusDist = $paymentDistribution['status_distribution'] ?? collect();
        @endphp

        @if($paymentMethods->count() > 0)
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">By Payment Method</h6>
                    @foreach($paymentMethods as $method)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ ucfirst(str_replace('_', ' ', $method->payment_method)) }}</span>
                                <span>${{ number_format($method->total_amount, 2) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                @php
                                    $maxAmount = $paymentMethods->max('total_amount');
                                    $percentage = $maxAmount > 0 ? ($method->total_amount / $maxAmount) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <small class="text-muted">{{ $method->count }} transactions</small>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">By Status</h6>
                    @foreach($statusDist as $status)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ ucfirst($status->status) }}</span>
                                <span>{{ $status->count }} payments</span>
                            </div>
                            @php
                                $totalCount = $statusDist->sum('count');
                                $percentage = $totalCount > 0 ? ($status->count / $totalCount) * 100 : 0;
                            @endphp
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $status->status == 'validated' ? 'success' : ($status->status == 'pending' ? 'warning' : 'danger') }}"
                                     role="progressbar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-chart-pie fa-2x mb-2"></i>
                <p>No payment data available yet</p>
            </div>
        @endif
    </div>
</div>
        </div>

        {{-- Overdue Invoices Table --}}
        @if(isset($overdueInvoices) && $overdueInvoices->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 border-danger">
                    <div class="card-header bg-danger text-white border-0 rounded-top-4 pt-3 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i>Overdue Invoices
                            <span class="badge bg-white text-danger ms-2 rounded-pill">{{ $overdueInvoices->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Invoice #</th>
                                        <th class="py-3">Customer</th>
                                        <th class="py-3">Due Date</th>
                                        <th class="py-3">Amount</th>
                                        <th class="py-3">Currency</th>
                                        <th class="py-3">Overdue Days</th>
                                        <th class="px-4 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueInvoices as $invoice)
                                    <tr>
                                        <td class="px-4 py-3 fw-bold">{{ $invoice->invoice_number }}</td>
                                        <td class="py-3">{{ $invoice->customer_name }}</td>
                                        <td class="py-3 text-danger fw-bold">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                                        <td class="py-3 fw-bold">
                                            @if($invoice->currency == 'USD')
                                                ${{ number_format($invoice->amount, 2) }}
                                            @else
                                                KSH {{ number_format($invoice->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-{{ $invoice->currency == 'USD' ? 'primary' : 'success' }} rounded-pill">
                                                {{ $invoice->currency }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-danger rounded-pill">
                                                {{ \Carbon\Carbon::parse($invoice->due_date)->diffInDays(now()) }} days
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button class="btn btn-sm btn-outline-danger rounded-pill">
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
</div>

<style>
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
    --kp-dark: #003f20;
    --kp-orange: #fd7e14;
}

/* Hero Section */
.dashboard-hero {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

/* Metric Cards */
.metric-card {
    transition: all 0.3s ease;
    border: none;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.metric-value-large {
    font-size: 2rem;
    line-height: 1.2;
}

.metric-icon-large {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Small Metric Cards */
.small-metric-card {
    background: white;
    border: 1px solid rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.small-metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.metric-icon-sm {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Combined Card */
.combined-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Action Cards */
.action-card {
    transition: all 0.3s ease;
    background: white;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: transparent !important;
}

.action-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

/* Transaction Items */
.transaction-item {
    transition: background 0.2s ease;
}

.transaction-item:hover {
    background: #f8f9fa;
}

/* Button Styles */
.btn-dashboard-action {
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-dashboard-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-outline-kp-blue {
    border: 1px solid var(--kp-blue);
    color: var(--kp-blue);
}
.btn-outline-kp-blue:hover {
    background: var(--kp-blue);
    color: white;
}

/* Background Gradients */
.bg-gradient-kp-blue {
    background: linear-gradient(45deg, var(--kp-blue) 0%, #005499 100%);
}

.bg-gradient-kp-green {
    background: linear-gradient(45deg, var(--kp-green) 0%, #00802c 100%);
}

.bg-gradient-kp-yellow {
    background: linear-gradient(45deg, var(--kp-yellow) 0%, #e6c300 100%);
}

.bg-gradient-danger {
    background: linear-gradient(45deg, #e74a3b 0%, #be2617 100%);
}

.bg-gradient-info {
    background: linear-gradient(45deg, #36b9cc 0%, #258391 100%);
}

.bg-gradient-orange {
    background: linear-gradient(45deg, var(--kp-orange) 0%, #dc710a 100%);
}

/* Color Classes */
.bg-kp-blue { background-color: var(--kp-blue) !important; }
.bg-kp-green { background-color: var(--kp-green) !important; }
.bg-kp-yellow { background-color: var(--kp-yellow) !important; color: var(--kp-dark) !important; }
.bg-purple { background-color: #6f42c1 !important; }

.bg-kp-blue-light { background: rgba(0, 102, 179, 0.1); }
.bg-kp-green-light { background: rgba(0, 150, 57, 0.1); }
.bg-kp-yellow-light { background: rgba(255, 215, 0, 0.15); }
.bg-info-light { background: rgba(23, 162, 184, 0.1); }
.bg-danger-light { background: rgba(220, 53, 69, 0.1); }
.bg-purple-light { background: rgba(111, 66, 193, 0.1); }

.text-kp-blue { color: var(--kp-blue) !important; }
.text-kp-green { color: var(--kp-green) !important; }
.text-kp-yellow { color: var(--kp-yellow) !important; }
.text-purple { color: #6f42c1 !important; }
.text-white-70 { color: rgba(255, 255, 255, 0.7); }
.text-white-50 { color: rgba(255, 255, 255, 0.5); }

.bg-white-10 { background: rgba(255, 255, 255, 0.1); }
.bg-white-20 { background: rgba(255, 255, 255, 0.2); }
.bg-dark-20 { background: rgba(0, 0, 0, 0.2); }

.border-white-20 {
    border-color: rgba(255, 255, 255, 0.2) !important;
}

/* Rounded Utilities */
.rounded-4 { border-radius: 1rem !important; }
.rounded-3 { border-radius: 0.75rem !important; }

/* Responsive Adjustments */
@media (max-width: 768px) {
    .metric-value-large { font-size: 1.5rem; }
    .btn-dashboard-action { padding: 6px 16px; font-size: 0.875rem; }
}

@media (max-width: 576px) {
    .dashboard-hero { text-align: center; }
    .hero-icon { display: none; }
}
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));

    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            setTimeout(() => {
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
                    label: 'Revenue (USD)',
                    data: @json($revenueTrends['revenues'] ?? []),
                    borderColor: '#0066B3',
                    backgroundColor: 'rgba(0, 102, 179, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#009639',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'USD $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e9ecef' },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
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
