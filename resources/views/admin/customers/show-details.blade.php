@extends('layouts.app')

@section('title', 'Customer Details - ' . ($user->company_name ?? $user->name))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="header-actions mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-2">
                <i class="fas fa-user-circle text-kp-blue me-2"></i>
                Customer Details: {{ $user->company_name ?? $user->name }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Customers
            </a>
            <a href="{{ route('admin.customers.export', $user->id) }}" class="btn btn-kp-primary">
                <i class="fas fa-download me-2"></i>Export Data
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Customer Info -->
        <div class="col-xl-4 col-lg-5">
            <!-- Customer Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-kp-blue text-white">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Customer Profile</h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-xl mx-auto mb-3">
                        <div class="avatar-title bg-kp-blue text-white rounded-circle" style="width: 100px; height: 100px; font-size: 48px;">
                            {{ substr($user->company_name ?? $user->name, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $user->company_name ?? $user->name }}</h4>
                    <p class="text-muted mb-2">Customer ID: #{{ $user->id }}</p>
                    <div class="mb-3">
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }} px-3 py-2">
                            {{ ucfirst($user->status) }}
                        </span>
                        <span class="badge bg-info px-3 py-2 ms-1">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>

                    <hr>

                    <div class="text-start">
                        <h6 class="mb-3">Contact Information</h6>
                        <p><i class="fas fa-envelope me-2 text-kp-blue"></i> {{ $user->email }}</p>
                        @if($user->phone)
                        <p><i class="fas fa-phone me-2 text-kp-blue"></i> {{ $user->phone }}</p>
                        @endif
                        @if($user->address)
                        <p><i class="fas fa-map-marker-alt me-2 text-kp-blue"></i> {{ $user->address }}, {{ $user->city ?? '' }}</p>
                        @endif
                        <p><i class="fas fa-calendar me-2 text-kp-blue"></i> Member since: {{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Manager Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-kp-green text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Account Manager</h5>
                </div>
                <div class="card-body">
                    @if($user->accountManager)
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-kp-green text-white rounded-circle" style="width: 50px; height: 50px; font-size: 24px;">
                                    {{ substr($user->accountManager->name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $user->accountManager->name }}</h6>
                                <small class="text-muted">{{ $user->accountManager->email }}</small>
                                <br>
                                <small class="text-muted">Assigned: {{ $user->assigned_at ? \Carbon\Carbon::parse($user->assigned_at)->format('M d, Y') : 'N/A' }}</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash fa-3x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No account manager assigned</p>
                            <button class="btn btn-sm btn-outline-kp-primary mt-2" data-bs-toggle="modal" data-bs-target="#assignManagerModal">
                                <i class="fas fa-plus me-1"></i>Assign Manager
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Company Profile Card (if exists) -->
            @if($user->companyProfile)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Company Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">KRA PIN:</td>
                            <td class="fw-bold">{{ $user->companyProfile->kra_pin }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Registration No:</td>
                            <td>{{ $user->companyProfile->registration_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">SAP Account:</td>
                            <td>{{ $user->companyProfile->sap_account ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Company Type:</td>
                            <td>{{ ucfirst($user->companyProfile->company_type) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Physical Location:</td>
                            <td>{{ $user->companyProfile->physical_location }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Statistics and Details -->
        <div class="col-xl-8 col-lg-7">
            <!-- Financial Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Billed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $totalBilledKsh = $financialSummary['currency_breakdown']->where('currency', 'KSH')->first()->total ?? 0;
                                    $totalBilledUsd = $financialSummary['currency_breakdown']->where('currency', 'USD')->first()->total ?? 0;
                                @endphp
                                @if($totalBilledKsh > 0)<div>KSh {{ number_format($totalBilledKsh, 2) }}</div>@endif
                                @if($totalBilledUsd > 0)<div>$ {{ number_format($totalBilledUsd, 2) }}</div>@endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success shadow h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $totalPaidKsh = $financialSummary['currency_breakdown']->where('currency', 'KSH')->first()->paid ?? 0;
                                    $totalPaidUsd = $financialSummary['currency_breakdown']->where('currency', 'USD')->first()->paid ?? 0;
                                @endphp
                                @if($totalPaidKsh > 0)<div>KSh {{ number_format($totalPaidKsh, 2) }}</div>@endif
                                @if($totalPaidUsd > 0)<div>$ {{ number_format($totalPaidUsd, 2) }}</div>@endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Outstanding</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KSh {{ number_format($financialSummary['total_outstanding'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-danger shadow h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KSh {{ number_format($financialSummary['overdue_amount'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lease Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-network-wired me-2 text-kp-blue"></i>Lease Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="h2 text-kp-blue">{{ $leaseStats['total_leases'] }}</div>
                            <div class="text-muted">Total Leases</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="h2 text-success">{{ $leaseStats['active_leases'] }}</div>
                            <div class="text-muted">Active Leases</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="h2 text-warning">{{ $leaseStats['pending_leases'] }}</div>
                            <div class="text-muted">Pending</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="h2 text-danger">{{ $leaseStats['expired_leases'] }}</div>
                            <div class="text-muted">Expired</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Monthly Revenue:</small>
                            <div class="fw-bold">KSh {{ number_format($leaseStats['total_monthly_revenue'], 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Total Contract Value:</small>
                            <div class="fw-bold">KSh {{ number_format($leaseStats['total_contract_value'], 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Leased Distance:</small>
                            <div class="fw-bold">{{ number_format($leaseStats['leased_distance_km'], 2) }} km</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Tickets -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt me-2 text-kp-blue"></i>Support Tickets</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="h4">{{ $ticketStats['total_tickets'] }}</div>
                            <div class="text-muted">Total Tickets</div>
                        </div>
                        <div class="col-md-4">
                            <div class="h4 text-warning">{{ $ticketStats['open_tickets'] }}</div>
                            <div class="text-muted">Open</div>
                        </div>
                        <div class="col-md-4">
                            <div class="h4 text-success">{{ $ticketStats['resolved_tickets'] }}</div>
                            <div class="text-muted">Resolved</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotation Pipeline -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2 text-kp-blue"></i>Quotation Pipeline</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="h5">{{ $quotationStats['total_quotations'] }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="h5 text-warning">{{ $quotationStats['pending_quotations'] }}</div>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="h5 text-success">{{ $quotationStats['won_quotations'] }}</div>
                            <small class="text-muted">Won</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="h5 text-danger">{{ $quotationStats['lost_quotations'] }}</div>
                            <small class="text-muted">Lost</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Pipeline Value:</small>
                            <div class="fw-bold">KSh {{ number_format($quotationStats['total_value_pipeline'], 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Won Value:</small>
                            <div class="fw-bold text-success">KSh {{ number_format($quotationStats['total_value_won'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Billings -->
            @if($billings->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2 text-kp-blue"></i>Recent Billings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice #</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($billings->take(10) as $billing)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($billing->billing_date)->format('M d, Y') }}</td>
                                    <td>#{{ $billing->id }}</td>
                                    <td>{{ $billing->currency }} {{ number_format($billing->total_amount, 2) }}</td>
                                    <td>{{ $billing->currency }} {{ number_format($billing->paid_amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($billing->due_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if($billing->paid_amount >= $billing->total_amount)
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($billing->due_date < now())
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-xl {
    width: 100px;
    height: 100px;
    margin: 0 auto;
}
.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
.border-left-primary { border-left: 4px solid #0066B3; }
.border-left-success { border-left: 4px solid #009639; }
.border-left-warning { border-left: 4px solid #FFD700; }
.border-left-danger { border-left: 4px solid #dc3545; }
.btn-kp-primary { background-color: #0066B3; color: white; }
.btn-kp-primary:hover { background-color: #005198; color: white; }
</style>
@endsection
