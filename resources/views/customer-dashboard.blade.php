@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
@php
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

     $defaultCurrency = 'USD';

    if(isset($consolidatedBillings) && $consolidatedBillings->count() > 0){
        $defaultCurrency = $consolidatedBillings->first()->currency ?? 'USD';
    }
@endphp

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-hero shadow-sm">
                <div>
                    <span class="text-muted small">Customer Portal</span>
                    <h1 class="h3 mb-2">
                        Welcome, {{ $user->company_name ?? $user->name }}
                    </h1>
                    <p class="mb-0 text-muted">
                        Manage your fibre requests, leases, invoices, documents, contracts, and support tickets from one place.
                    </p>
                </div>

                <div class="hero-actions">
                    @include('partials.role-help-widget')

                    <a href="{{ route('customer.design-requests.create') }}" class="btn" style="background: #009639; border-color: #009639; color: white;">
                        <i class="fas fa-plus me-2"></i>Request New Fibre Route
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Status --}}
    <div class="row mb-4">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">
                        <i class="fas fa-route" style="color: #009639;"></i> Get Started
                    </h5>
                    <p class="text-muted mb-3">
                        Need a new connection or route? Submit a request and track the process from quotation to lease activation.
                    </p>
                    <a href="{{ route('customer.design-requests.create') }}" class="btn btn-lg mb-3" style="background: #009639; border-color: #009639; color: white;">
                        <i class="fas fa-plus-circle me-2"></i>Submit Fibre Route Request
                    </a>

                    <div class="fibre-advert mt-3">
                        <div class="fibre-advert-content">
                            <div class="fibre-advert-icon">
                                <i class="fas fa-bolt"></i>
                            </div>

                            <div>
                                <span class="advert-label">Premium Dark Fibre Connectivity</span>
                                <h4>Power your business with reliable high-capacity fibre</h4>
                                <p>
                                    Enjoy secure, scalable, and dedicated fibre connectivity designed for ISPs,
                                    enterprises, data centres, and mission-critical operations.
                                </p>

                                <div class="advert-features">
                                    <span><i class="fas fa-check-circle"></i> Dedicated bandwidth</span>
                                    <span><i class="fas fa-check-circle"></i> Nationwide reach</span>
                                    <span><i class="fas fa-check-circle"></i> Enterprise-grade reliability</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">
                        <i class="fas fa-user-check me-2" style="color: #0066B3;"></i>Profile Status
                    </h5>

                    @if(count($missingDocs) > 0)
                        <div class="alert alert-kp-warning mb-3">
                            <strong>Profile Incomplete</strong><br>
                            {{ count($missingDocs) }} required document(s) missing.
                        </div>

                        <a href="{{ route('customer.profile.show') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-upload me-2"></i>Complete Profile
                        </a>
                    @elseif(!$profileComplete)
                        <div class="alert alert-info mb-3">
                            <strong>Documents Submitted</strong><br>
                            Your documents are awaiting approval.
                        </div>

                        <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-folder-open me-2"></i>View Documents
                        </a>
                    @else
                        <div class="alert alert-kp-success mb-0" style="background: #d4edda; border-color: #c3e6cb; color: #155724;">
                            <strong>Profile Complete</strong><br>
                            All required documents have been approved.
                        </div>
                    @endif
                </div>

                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm account-manager-card">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                                    <div class="d-flex align-items-center gap-3">
                                        <div class="manager-avatar">
                                            <div class="avatar-circle">
                                                <i class="fas fa-user-tie"></i>
                                                <div class="avatar-ring"></div>
                                            </div>
                                        </div>

                                        <div>
                                            <small class="fw-bold" style="color: #0066B3; letter-spacing: 1px; border-left: 3px solid #FFD700; padding-left: 8px;">
                                                <i class="fas fa-user-tie me-1" style="color: #009639;"></i>Your Account Manager
                                            </small>

                                            @if($accountManager)
                                                <h5 class="mb-1">{{ $accountManager->name }}</h5>

                                                <div class="text-muted small">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    {{ $accountManager->email }}

                                                    @if($accountManager->phone)
                                                        <span class="mx-2">|</span>
                                                        <i class="fas fa-phone me-1"></i>
                                                        {{ $accountManager->phone }}
                                                    @endif
                                                </div>
                                            @else
                                                <h5 class="mb-1 text-kp-yellow">Not Assigned Yet</h5>
                                                <div class="text-muted small">
                                                    An account manager has not yet been assigned to your account.
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column flex-sm-row gap-2">
                                        @if($accountManager)
                                            <a href="mailto:{{ $accountManager->email }}?subject=Dark Fibre Customer Support Request"
                                               class="btn btn-outline-kp-primary">
                                                <i class="fas fa-envelope me-2"></i>Email Manager
                                            </a>

                                            <a href="{{ route('chat.index', ['user' => $accountManager->id]) }}"
                                               class="btn" style="background: #009639; border-color: #009639; color: white;">
                                                <i class="fas fa-comments me-2"></i>Chat Manager
                                            </a>
                                        @else
                                            <a href="{{ route('customer.support.create') }}" class="btn btn-kp-warning">
                                                <i class="fas fa-ticket-alt me-2"></i>Request Assistance
                                            </a>
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

    {{-- Invoice Stats --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card border-kp-blue">
                <div>
                    <small class="fw-bold text-uppercase" style="color: #0066B3;">Total Invoices</small>
                    <h4 class="mb-0">{{ $billingStats['total'] ?? 0 }}</h4>
                </div>
                <i class="fas fa-file-invoice stat-icon" style="color: #0066B3; opacity: 0.35;"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card border-kp-green">
                <div>
                    <small class="fw-bold text-uppercase" style="color: #009639;">Paid</small>
                    <h4 class="mb-0" style="color: #009639;">{{ $billingStats['paid'] ?? 0 }}</h4>
                </div>
                <i class="fas fa-check-circle stat-icon" style="color: #009639; opacity: 0.35;"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card border-kp-yellow">
                <div>
                    <small class="fw-bold text-uppercase text-kp-yellow">Pending</small>
                    <h4 class="mb-0 text-kp-yellow">{{ $billingStats['pending'] ?? 0 }}</h4>
                </div>
                <i class="fas fa-clock stat-icon text-kp-yellow opacity-35"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card border-danger">
                <div>
                    <small class="fw-bold text-uppercase text-danger">Overdue</small>
                    <h4 class="mb-0 text-danger">{{ $billingStats['overdue'] ?? 0 }}</h4>
                </div>
                <i class="fas fa-exclamation-triangle stat-icon text-danger opacity-35"></i>
            </div>
        </div>
    </div>

    {{-- Amount Summary --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="amount-card danger" style="background: linear-gradient(135deg, #dc3545, #b02a37);">
                <div>
                    <small>Total Outstanding</small>
                    <h2>
                        {{ formatCurrency($billingStats['total_amount'] ?? 0, $defaultCurrency) }}
                    </h2>
                    <p>Amount pending for payment</p>
                </div>
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="amount-card success" style="background: linear-gradient(135deg, #009639, #0066B3);">
                <div>
                    <small>Total Paid</small>
                    <h2>
                        {{ formatCurrency($billingStats['paid_amount'] ?? 0, $defaultCurrency) }}
                    </h2>
                    <p>Amount successfully paid</p>
                </div>
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="action-card">
                <div class="icon-circle bg-kp-blue" style="background: linear-gradient(135deg, #0066B3, #003f8c);">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h5>My Leases</h5>
                <p>View active and pending fibre leases.</p>
                <a href="{{ route('customer.leases.index') }}" class="btn w-100" style="border-color: #0066B3; color: #0066B3;">
                    View Leases
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="action-card">
                <div class="icon-circle bg-kp-green" style="background: linear-gradient(135deg, #009639, #006633);">
                    <i class="fas fa-list"></i>
                </div>
                <h5>My Requests/Quotations</h5>
                <p>Track your submitted fibre route requests and Quotations.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('customer.design-requests.index') }}" class="btn w-100" style="border-color: #009639; color: #009639;">
                        View Requests
                    </a>
                    <a href="{{ route('customer.quotations.index') }}" class="btn w-100" style="border-color: #0066B3; color: #0066B3;">
                        My Quotations
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="action-card">
                <div class="icon-circle bg-info" style="background: linear-gradient(135deg, #17a2b8, #0f6674);">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h5>Invoices & Payments</h5>
                <p>View invoices and make payments.</p>
                <a href="{{ route('customer.billings.index') }}" class="btn w-100" style="border-color: #17a2b8; color: #17a2b8;">
                    View Billings
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="action-card">
                <div class="icon-circle bg-kp-yellow" style="background: linear-gradient(135deg, #ffc107, #d39e00);">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h5>Support</h5>
                <p>Raise a support ticket or service issue.</p>
                <a href="{{ route('customer.support.create') }}" class="btn btn-outline-warning w-100">
                    New Ticket
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="action-card">
                <div class="icon-circle bg-secondary" style="background: linear-gradient(135deg, #6c757d, #545b62);">
                    <i class="fas fa-user-cog"></i>
                </div>
                <h5>Profile Settings</h5>
                <p>Update account and company details.</p>
                <a href="{{ route('customer.profile.show') }}" class="btn w-100" style="border-color: #6c757d; color: #6c757d;">
                    Manage Profile
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="action-card">
                <div class="icon-circle bg-dark" style="background: linear-gradient(135deg, #343a40, #1d2124);">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h5>Documents</h5>
                <p>Upload and manage required documents.</p>

                @if($user->leases->count() > 0)
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.documents.index') }}" class="btn w-100" style="border-color: #343a40; color: #343a40;">
                            My Documents
                        </a>
                        <a href="{{ route('customer.documents.requests.index') }}" class="btn w-100" style="background: #009639; border-color: #009639; color: white;">
                            Requested Documents
                        </a>
                    </div>
                @else
                    <button class="btn btn-outline-dark w-100" disabled data-bs-toggle="tooltip"
                            title="You need to have a lease before uploading lease documents">
                        Upload Documents
                    </button>
                @endif
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="action-card">
                <div class="icon-circle bg-purple" style="background: linear-gradient(135deg, #6f42c1, #5a32a3);">
                    <i class="fas fa-handshake"></i>
                </div>
                <h5>Contracts</h5>
                <p>View signed and pending contracts.</p>
                <a href="{{ route('customer.contracts.index') }}" class="btn btn-purple w-100" style="background: #6f42c1; border-color: #6f42c1; color: white;">
                    View Contracts
                </a>
            </div>
        </div>
    </div>

    {{-- Consolidated Invoices --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold" style="color: #0066B3;">
                <i class="fas fa-file-invoice me-2"></i>Recent Invoices
            </h5>

            <a href="{{ route('customer.billings.index') }}" class="btn btn-sm" style="border-color: #0066B3; color: #0066B3;">
                <i class="fas fa-list me-1"></i>View All
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th class="text-center">Actions</th>
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
                                <td class="fw-bold">{{ $billing->billing_number }}</td>

                                <td>
                                    {{ optional($billing->billing_date)->format('M d, Y') ?? 'N/A' }}
                                </td>

                                <td>
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

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $billingCurrency }}
                                    </span>
                                </td>

                                <td class="fw-bold">
                                    {{ formatCurrency($billing->total_amount, $billingCurrency) }}
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($billing->status) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-info">
                                        {{ $billing->lineItems->count() }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('customer.billing.show', $billing->id) }}"
                                           class="btn btn-outline-kp-primary"
                                           data-bs-toggle="tooltip"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('customer.billing.download', $billing->id) }}"
                                           class="btn btn-outline-kp-success"
                                           data-bs-toggle="tooltip"
                                           title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        @if($billing->status === 'pending')
                                            @if($leaseId)
                                                <a href="{{ route('customer.payments.create', ['lease' => $leaseId]) }}"
                                                   class="btn btn-outline-danger"
                                                   data-bs-toggle="tooltip"
                                                   title="Pay Now">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('customer.billing.show', $billing->id) }}"
                                                   class="btn btn-outline-danger"
                                                   data-bs-toggle="tooltip"
                                                   title="View & Pay">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if($billing->lineItems->count() > 0)
                                <tr class="table-light">
                                    <td colspan="8" class="p-3">
                                        <div class="small fw-bold text-muted mb-2">
                                            <i class="fas fa-list me-1"></i>
                                            Line Items {{ $billing->lineItems->count() }}
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Lease</th>
                                                        <th>Description</th>
                                                        <th>Period</th>
                                                        <th class="text-end">Amount</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach($billing->lineItems as $item)
                                                        <tr>
                                                            <td>
                                                                @if($item->lease)
                                                                    {{ $item->lease->name ?? 'Lease #' . $item->lease_id }}
                                                                @else
                                                                    <em class="text-muted">N/A</em>
                                                                @endif
                                                            </td>

                                                            <td>{{ $item->description }}</td>

                                                            <td>
                                                                @if($item->period_start && $item->period_end)
                                                                    {{ $item->period_start->format('M d') }}
                                                                    -
                                                                    {{ $item->period_end->format('M d, Y') }}
                                                                @else
                                                                    <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>

                                                            <td class="text-end fw-bold">
                                                                {{ formatCurrency($item->amount, $billingCurrency) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No invoices yet</h5>
                                    <p class="text-muted mb-0">
                                        Your invoices will appear here once your fibre lease or service request is processed.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($consolidatedBillings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $consolidatedBillings->links() }}
                </div>
            @endif
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

    .dashboard-hero {
        background: #ffffff;
        border-radius: 14px;
        padding: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        border-left: 5px solid var(--kp-green);
    }

    .hero-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e9ecef;
        border-left-width: 5px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
    }

    .stat-card.border-kp-blue {
        border-left-color: var(--kp-blue) !important;
    }

    .stat-card.border-kp-green {
        border-left-color: var(--kp-green) !important;
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.35;
    }

    .amount-card {
        border-radius: 14px;
        padding: 24px;
        color: #ffffff;
        min-height: 170px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .amount-card h2 {
        font-weight: 700;
        margin: 8px 0;
    }

    .amount-card p {
        margin: 0;
        opacity: 0.85;
    }

    .amount-card i {
        font-size: 3rem;
        opacity: 0.25;
    }

    .amount-card.danger {
        background: linear-gradient(135deg, #dc3545, #b02a37);
    }

    .amount-card.success {
        background: linear-gradient(135deg, var(--kp-green), var(--kp-blue));
    }

    .action-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 24px;
        height: 100%;
        text-align: center;
        border: 1px solid #e9ecef;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.2s ease-in-out;
    }

    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .action-card p {
        color: #6c757d;
        min-height: 45px;
    }

    .icon-circle {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        color: #ffffff;
        font-size: 1.5rem;
    }

    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }

    .btn-purple:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
        color: white;
    }

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .manager-avatar {
        position: relative;
    }

    .avatar-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--kp-blue), var(--kp-green));
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
    }

    .avatar-circle i {
        font-size: 35px;
        color: white;
    }

    .avatar-circle:hover {
        transform: scale(1.05);
    }

    .avatar-ring {
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        border-radius: 50%;
        border: 2px solid var(--kp-yellow);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .avatar-circle:hover .avatar-ring {
        opacity: 1;
    }

    .fibre-advert {
        background: linear-gradient(135deg, var(--kp-dark), var(--kp-green));
        border-radius: 16px;
        padding: 22px;
        color: #ffffff;
        overflow: hidden;
        position: relative;
    }

    .fibre-advert::after {
        content: "";
        position: absolute;
        right: -40px;
        top: -40px;
        width: 140px;
        height: 140px;
        background: rgba(255, 215, 0, 0.18);
        border-radius: 50%;
    }

    .fibre-advert-content {
        display: flex;
        gap: 18px;
        align-items: flex-start;
        position: relative;
        z-index: 1;
    }

    .fibre-advert-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: var(--kp-yellow);
        color: var(--kp-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .advert-label {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--kp-yellow);
        margin-bottom: 6px;
    }

    .fibre-advert h4 {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .fibre-advert p {
        margin-bottom: 12px;
        opacity: 0.9;
    }

    .advert-features {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .advert-features span {
        background: rgba(255, 255, 255, 0.14);
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 0.82rem;
    }

    .advert-features i {
        color: var(--kp-yellow);
        margin-right: 5px;
    }

    .account-manager-card {
        border-left: 5px solid var(--kp-blue) !important;
        border-radius: 14px;
    }

    @media (max-width: 768px) {
        .dashboard-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .hero-actions,
        .hero-actions .btn,
        .hero-actions form,
        .hero-actions form button {
            width: 100%;
        }

        .amount-card {
            min-height: 140px;
        }

        .fibre-advert-content {
            flex-direction: column;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
