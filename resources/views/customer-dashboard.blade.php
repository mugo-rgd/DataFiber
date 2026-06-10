@extends('layouts.app')

@section('title', 'Customer Dashboard - Dark Fibre CRM')

@section('content')
@php
// Add this helper function if not exists
    if (!function_exists('formatCurrency')) {
        function formatCurrency($amount, $currency) {
            if ($currency === 'USD') {
                return '$' . number_format($amount, 2);
            } elseif ($currency === 'KSH' || $currency === 'KES') {
                return 'KSh ' . number_format($amount, 2);
            }
            return number_format($amount, 2) . ' ' . $currency;
        }
    }
    
    $user = Auth::user();
    $accountManager = $user->accountManager ?? null;
    $requiredProfileDocs = [
        'kra_pin_certificate',
        'business_registration_certificate',
        'trade_license',
        'ca_license',
        'cr12_certificate'
    ];

    $uploadedDocs = $user->documents()
        ->whereIn('document_type', $requiredProfileDocs)
        ->pluck('document_type')
        ->toArray();

    $missingDocs = array_diff($requiredProfileDocs, $uploadedDocs);

    $approvedDocs = $user->documents()
        ->whereIn('document_type', $requiredProfileDocs)
        ->where('status', 'approved')
        ->count();

    $profileComplete = count($missingDocs) === 0 && $approvedDocs === count($requiredProfileDocs);

    // Currency handling for both USD and KSH
    $defaultCurrency = 'USD';
    $currencies = ['USD', 'KSH'];
    $currencyData = [];

    // Initialize currency data
    foreach ($currencies as $currency) {
        $currencyData[$currency] = [
            'total_invoices' => 0,
            'paid_invoices' => 0,
            'pending_invoices' => 0,
            'overdue_invoices' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'outstanding_amount' => 0,
        ];
    }

    // Process billing data by currency if consolidatedBillings exists
    if(isset($consolidatedBillings) && $consolidatedBillings->count() > 0) {
        // Set default currency from first billing
        $firstBilling = $consolidatedBillings->first();
        $defaultCurrency = $firstBilling->currency ?? 'USD';

        // Calculate statistics by currency
        foreach ($consolidatedBillings as $billing) {
            $currency = $billing->currency ?? 'USD';

            if (!isset($currencyData[$currency])) {
                $currencyData[$currency] = [
                    'total_invoices' => 0,
                    'paid_invoices' => 0,
                    'pending_invoices' => 0,
                    'overdue_invoices' => 0,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'outstanding_amount' => 0,
                ];
            }

            $currencyData[$currency]['total_invoices']++;

            // Check if overdue (due date passed and not paid)
            $isOverdue = $billing->due_date &&
                        $billing->due_date->lt(now()) &&
                        $billing->status !== 'paid';

            // Status counts
            if ($billing->status === 'paid') {
                $currencyData[$currency]['paid_invoices']++;
            } elseif ($isOverdue) {
                $currencyData[$currency]['overdue_invoices']++;
            } elseif ($billing->status === 'pending') {
                $currencyData[$currency]['pending_invoices']++;
            }

            // Amount calculations
            $currencyData[$currency]['total_amount'] += $billing->total_amount ?? 0;
            $currencyData[$currency]['paid_amount'] += $billing->paid_amount ?? 0;
            $currencyData[$currency]['outstanding_amount'] += ($billing->total_amount - $billing->paid_amount);
        }
    }

    // Helper function to format currency
    function formatCurrencyByCurrency($amount, $currency) {
        if ($currency === 'USD') {
            return '$' . number_format($amount, 2);
        } elseif ($currency === 'KSH' || $currency === 'KES') {
            return 'KSh ' . number_format($amount, 2);
        }
        return number_format($amount, 2) . ' ' . $currency;
    }

    // Get available currencies from data
    $availableCurrencies = array_keys(array_filter($currencyData, function($data) {
        return $data['total_invoices'] > 0;
    }));

    if (empty($availableCurrencies)) {
        $availableCurrencies = ['USD'];
    }
@endphp

<div class="container-fluid px-0">

    {{-- Hero Section --}}
    <div class="dashboard-hero text-white py-4 py-md-5">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-4">

                {{-- Left Column - Welcome --}}
                <div class="col-12 col-lg-7">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="hero-icon">
                            <i class="fas fa-user-circle fa-3x fa-fw"></i>
                        </div>
                        <div>
                            <small class="text-white-50 text-uppercase tracking-wide">Customer Portal</small>
                            <h1 class="display-5 fw-bold mb-2">
                                Welcome, {{ $user->company_name ?? $user->name }}
                            </h1>
                            <p class="lead mb-0 opacity-90">
                                Manage your fibre requests, leases, invoices, and support tickets from one place.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Actions --}}
                <div class="col-12 col-lg-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @include('partials.role-help-widget')

                        <a href="{{ route('customer.design-requests.create') }}" class="btn btn-success btn-dashboard-action">
                            <i class="fas fa-plus me-2"></i>Request Fibre Route
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-dashboard-action">
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

        {{-- Get Started & Profile Status Row --}}
        <div class="row g-4 mb-5">

            {{-- Get Started Card --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="fas fa-route text-kp-green me-2"></i>Get Started
                        </h4>
                        <p class="text-muted mb-4">
                            Need a new connection or route? Submit a request and track the process from quotation to lease activation.
                        </p>

                        <a href="{{ route('customer.design-requests.create') }}" class="btn btn-kp-success btn-lg mb-4 px-4">
                            <i class="fas fa-plus-circle me-2"></i>Submit Fibre Route Request
                        </a>

                        {{-- Fibre Advert --}}
                        <div class="fibre-advert rounded-4 p-4">
                            <div class="d-flex gap-3 flex-wrap flex-md-nowrap">
                                <div class="fibre-advert-icon flex-shrink-0">
                                    <i class="fas fa-bolt fa-2x"></i>
                                </div>
                                <div>
                                    <span class="advert-label">Premium Dark Fibre Connectivity</span>
                                    <h4 class="fw-bold mb-2">Power your business with reliable high-capacity fibre</h4>
                                    <p class="mb-3">Enjoy secure, scalable, and dedicated fibre connectivity designed for ISPs, enterprises, data centres, and mission-critical operations.</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-white-20 rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Dedicated bandwidth
                                        </span>
                                        <span class="badge bg-white-20 rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Nationwide reach
                                        </span>
                                        <span class="badge bg-white-20 rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Enterprise-grade reliability
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Profile Status & Account Manager --}}
            <div class="col-lg-4">
                {{-- Profile Status Card --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-user-check text-kp-blue me-2"></i>Profile Status
                        </h5>

                        @if(count($missingDocs) > 0)
                            <div class="alert alert-warning rounded-3 mb-3">
                                <strong>⚠️ Profile Incomplete</strong><br>
                                {{ count($missingDocs) }} required document(s) missing.
                            </div>
                            <a href="{{ route('customer.profile.show') }}" class="btn btn-outline-warning rounded-pill w-100">
                                <i class="fas fa-upload me-2"></i>Complete Profile
                            </a>
                        @elseif(!$profileComplete)
                            <div class="alert alert-info rounded-3 mb-3">
                                <strong>📄 Documents Submitted</strong><br>
                                Your documents are awaiting approval.
                            </div>
                            <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-info rounded-pill w-100">
                                <i class="fas fa-folder-open me-2"></i>View Documents
                            </a>
                        @else
                            <div class="alert alert-success rounded-3 mb-3">
                                <strong>✅ Profile Complete</strong><br>
                                All required documents have been approved.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Account Manager Card --}}
                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="manager-avatar flex-shrink-0">
                                <div class="avatar-circle">
                                    <i class="fas fa-user-tie fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-uppercase fw-bold text-kp-blue tracking-wide">
                                    <i class="fas fa-user-tie me-1 text-kp-green"></i>Your Account Manager
                                </small>

                                @if($accountManager)
                                    <h5 class="fw-bold mb-1">{{ $accountManager->name }}</h5>
                                    <div class="text-muted small mb-3">
                                        <i class="fas fa-envelope me-1"></i>{{ $accountManager->email }}
                                        @if($accountManager->phone)
                                            <span class="mx-1">|</span>
                                            <i class="fas fa-phone me-1"></i>{{ $accountManager->phone }}
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="mailto:{{ $accountManager->email }}?subject=Dark Fibre Customer Support Request"
                                           class="btn btn-outline-primary rounded-pill btn-sm flex-grow-1">
                                            <i class="fas fa-envelope me-1"></i>Email
                                        </a>
                                        <a href="{{ route('chat.index', ['user' => $accountManager->id]) }}"
                                           class="btn btn-kp-success rounded-pill btn-sm flex-grow-1">
                                            <i class="fas fa-comments me-1"></i>Chat
                                        </a>
                                    </div>
                                @else
                                    <p class="text-muted mb-2">Not Assigned Yet</p>
                                    <a href="{{ route('customer.support.create') }}" class="btn btn-outline-warning rounded-pill w-100">
                                        <i class="fas fa-ticket-alt me-2"></i>Request Assistance
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Invoice Statistics Cards --}}
        <div class="row g-4 mb-5">
    @foreach($availableCurrencies as $currency)
        <div class="col-12">
            <h5 class="mb-3">
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    {{ $currency }} Portfolio
                </span>
            </h5>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card rounded-4 p-4 h-100">
                <div>
                    <small class="text-uppercase fw-bold text-kp-blue">Total Invoices ({{ $currency }})</small>
                    <div class="stat-value fw-bold">{{ $currencyData[$currency]['total_invoices'] }}</div>
                </div>
                <i class="fas fa-file-invoice fa-2x text-kp-blue opacity-25"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card rounded-4 p-4 h-100">
                <div>
                    <small class="text-uppercase fw-bold text-kp-green">Paid ({{ $currency }})</small>
                    <div class="stat-value fw-bold text-kp-green">{{ $currencyData[$currency]['paid_invoices'] }}</div>
                </div>
                <i class="fas fa-check-circle fa-2x text-kp-green opacity-25"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card rounded-4 p-4 h-100">
                <div>
                    <small class="text-uppercase fw-bold text-warning">Pending ({{ $currency }})</small>
                    <div class="stat-value fw-bold text-warning">{{ $currencyData[$currency]['pending_invoices'] }}</div>
                </div>
                <i class="fas fa-clock fa-2x text-warning opacity-25"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card rounded-4 p-4 h-100">
                <div>
                    <small class="text-uppercase fw-bold text-danger">Overdue ({{ $currency }})</small>
                    <div class="stat-value fw-bold text-danger">{{ $currencyData[$currency]['overdue_invoices'] }}</div>
                </div>
                <i class="fas fa-exclamation-triangle fa-2x text-danger opacity-25"></i>
            </div>
        </div>
    @endforeach
</div>

        {{-- Amount Summary Cards --}}
        <div class="row g-4 mb-5">
    @foreach($availableCurrencies as $currency)
        <div class="col-lg-6">
            <div class="amount-card amount-card-danger rounded-4 p-4">
                <div>
                    <small class="text-uppercase tracking-wide">Total Outstanding ({{ $currency }})</small>
                    <div class="amount-value fw-bold display-6 mb-2">
                        {{ formatCurrencyByCurrency($currencyData[$currency]['outstanding_amount'], $currency) }}
                    </div>
                    <p class="mb-0 opacity-75">Amount pending for payment</p>
                </div>
                <i class="fas fa-money-bill-wave fa-3x opacity-25"></i>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="amount-card amount-card-success rounded-4 p-4">
                <div>
                    <small class="text-uppercase tracking-wide">Total Paid ({{ $currency }})</small>
                    <div class="amount-value fw-bold display-6 mb-2">
                        {{ formatCurrencyByCurrency($currencyData[$currency]['paid_amount'], $currency) }}
                    </div>
                    <p class="mb-0 opacity-75">Amount successfully paid</p>
                </div>
                <i class="fas fa-check-circle fa-3x opacity-25"></i>
            </div>
        </div>
    @endforeach
</div>

        {{-- Quick Actions Section --}}
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h4 class="mb-0 fw-bold">
                            <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                        </h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-3">
                            @php
                                $quickActions = [
                                    ['title' => 'My Leases', 'icon' => 'network-wired', 'color' => 'kp-blue', 'route' => 'customer.leases.index', 'desc' => 'View active leases'],
                                    ['title' => 'My Requests', 'icon' => 'list', 'color' => 'kp-green', 'route' => 'customer.design-requests.index', 'desc' => 'Track requests'],
                                    ['title' => 'My Quotations', 'icon' => 'file-invoice-dollar', 'color' => 'info', 'route' => 'customer.quotations.index', 'desc' => 'View quotations'],
                                    ['title' => 'Invoices', 'icon' => 'file-invoice', 'color' => 'warning', 'route' => 'customer.billings.index', 'desc' => 'View & pay'],
                                    ['title' => 'Support', 'icon' => 'ticket-alt', 'color' => 'danger', 'route' => 'customer.support.create', 'desc' => 'Raise ticket'],
                                    ['title' => 'Profile', 'icon' => 'user-cog', 'color' => 'secondary', 'route' => 'customer.profile.show', 'desc' => 'Update info'],
                                    ['title' => 'Documents', 'icon' => 'folder', 'color' => 'dark', 'route' => 'customer.documents.index', 'desc' => 'Manage docs'],
                                    ['title' => 'Contracts', 'icon' => 'file-contract', 'color' => 'purple', 'route' => 'customer.contracts.index', 'desc' => 'View contracts'],
                                ];
                            @endphp

                            @foreach($quickActions as $action)
                                <div class="col-6 col-md-3">
                                    <a href="{{ route($action['route']) }}" class="action-card text-center p-3 rounded-3 border h-100 text-decoration-none d-block">
                                        <div class="action-icon bg-{{ $action['color'] }} rounded-3 mx-auto mb-2">
                                            <i class="fas fa-{{ $action['icon'] }} fa-fw"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-1">{{ $action['title'] }}</h6>
                                        <small class="text-muted d-none d-md-block">{{ $action['desc'] }}</small>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Invoices Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-file-invoice text-kp-blue me-2"></i>Recent Invoices
                        </h5>
                        <a href="{{ route('customer.billings.index') }}" class="btn btn-sm btn-outline-kp-blue rounded-pill px-3">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Invoice #</th>
                                        <th class="py-3">Date</th>
                                        <th class="py-3">Due Date</th>
                                        <th class="py-3">Currency</th>
                                        <th class="py-3">Amount</th>
                                        <th class="py-3">Status</th>
                                        <th class="px-4 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($consolidatedBillings as $billing)
                                        @php
                                            $billingCurrency = $billing->currency ?? 'KES';
                                            $statusClasses = [
                                                'paid' => 'success',
                                                'pending' => 'warning',
                                                'overdue' => 'danger',
                                                'cancelled' => 'secondary',
                                            ];
                                            $statusClass = $statusClasses[$billing->status] ?? 'secondary';
                                            $isOverdue = $billing->due_date && $billing->due_date->lt(now()) && $billing->status !== 'paid';
                                            $leaseId = optional($billing->lineItems->first())->lease_id;
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-kp-blue">{{ $billing->billing_number }}</td>
                                            <td class="py-3">{{ optional($billing->billing_date)->format('M d, Y') ?? 'N/A' }}</td>
                                            <td class="py-3">
                                                @if($billing->due_date)
                                                    <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                                        {{ $billing->due_date->format('M d, Y') }}
                                                        @if($isOverdue)
                                                            <br><small class="text-danger">Overdue</small>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1">{{ $billingCurrency }}</span>
                                            </td>
                                            <td class="py-3 fw-bold">{{ formatCurrency($billing->total_amount, $billingCurrency) }}</td>
                                            <td class="py-3">
                                                <span class="badge bg-{{ $statusClass }} rounded-pill px-3 py-1">{{ ucfirst($billing->status) }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="btn-group gap-1">
                                                    <a href="{{ route('customer.billing.show', $billing->id) }}"
                                                       class="btn btn-sm btn-outline-primary rounded-pill px-2"
                                                       data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('customer.billing.download', $billing->id) }}"
                                                       class="btn btn-sm btn-outline-success rounded-pill px-2"
                                                       data-bs-toggle="tooltip" title="Download PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($billing->status === 'pending')
                                                        <a href="{{ $leaseId ? route('customer.payments.create', ['lease' => $leaseId]) : route('customer.billing.show', $billing->id) }}"
                                                           class="btn btn-sm btn-outline-danger rounded-pill px-2"
                                                           data-bs-toggle="tooltip" title="Pay Now">
                                                            <i class="fas fa-credit-card"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-file-invoice fa-4x text-muted opacity-25 mb-3"></i>
                                                <h6 class="text-muted">No invoices yet</h6>
                                                <p class="small text-muted">Your invoices will appear here once your fibre lease or service request is processed.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($consolidatedBillings->hasPages())
                        <div class="card-footer bg-transparent border-0 text-center pt-3 pb-4">
                            {{ $consolidatedBillings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<style>
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
    --kp-dark: #003f20;
}

/* Hero Section */
.dashboard-hero {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

/* Tracking Wide */
.tracking-wide {
    letter-spacing: 0.05em;
}

/* Stat Cards */
.stat-card {
    background: white;
    border: 1px solid rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.stat-value {
    font-size: 2rem;
    line-height: 1.2;
}

/* Amount Cards */
.amount-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.amount-card-danger {
    background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
}

.amount-card-success {
    background: linear-gradient(135deg, var(--kp-green) 0%, var(--kp-blue) 100%);
}

.amount-value {
    font-size: 1.75rem;
}

/* Action Cards */
.action-card {
    transition: all 0.3s ease;
    background: white;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
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

/* Manager Avatar */
.manager-avatar .avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--kp-blue), var(--kp-green));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

/* Fibre Advert */
.fibre-advert {
    background: linear-gradient(135deg, var(--kp-dark) 0%, var(--kp-green) 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.fibre-advert::after {
    content: "";
    position: absolute;
    right: -30px;
    top: -30px;
    width: 120px;
    height: 120px;
    background: rgba(255, 215, 0, 0.1);
    border-radius: 50%;
}

.fibre-advert-icon {
    width: 60px;
    height: 60px;
    background: var(--kp-yellow);
    color: var(--kp-dark);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.advert-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--kp-yellow);
    font-weight: 600;
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

.btn-kp-success {
    background: var(--kp-green);
    border-color: var(--kp-green);
    color: white;
}
.btn-kp-success:hover {
    background: #00802c;
    border-color: #00802c;
}

.btn-outline-kp-blue {
    border: 1px solid var(--kp-blue);
    color: var(--kp-blue);
}
.btn-outline-kp-blue:hover {
    background: var(--kp-blue);
    color: white;
}

/* Color Classes */
.bg-kp-blue { background: var(--kp-blue) !important; }
.bg-kp-green { background: var(--kp-green) !important; }
.bg-kp-yellow { background: var(--kp-yellow) !important; color: var(--kp-dark) !important; }
.bg-purple { background: #6f42c1 !important; }
.bg-white-20 { background: rgba(255, 255, 255, 0.2); }

.text-kp-blue { color: var(--kp-blue) !important; }
.text-kp-green { color: var(--kp-green) !important; }
.text-white-50 { color: rgba(255, 255, 255, 0.5); }

/* Table Styles */
.table th {
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #5a5c69;
}

.table td {
    vertical-align: middle;
}

/* Rounded Utilities */
.rounded-4 { border-radius: 1rem !important; }
.rounded-3 { border-radius: 0.75rem !important; }

/* Responsive */
@media (max-width: 768px) {
    .stat-value { font-size: 1.5rem; }
    .amount-value { font-size: 1.25rem; }
    .btn-dashboard-action { padding: 6px 16px; font-size: 0.875rem; }
    .action-card { text-align: left; }
    .action-icon { margin: 0 0 0.75rem 0; }
}

@media (max-width: 576px) {
    .dashboard-hero { text-align: center; }
    .hero-icon { display: none; }
}

@media print {
    .dashboard-hero, .action-card, .btn, .badge { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));

    // Add animation to cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.stat-card, .action-card, .amount-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
</script>

@endsection
