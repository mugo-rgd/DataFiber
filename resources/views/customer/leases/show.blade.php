I'll add Approve and Reject actions to the Documents tab and make all actions functional with proper routes. Here's the updated code:

```blade
@extends('layouts.app')

@section('title', 'My Lease Details - ' . $lease->lease_number)

@section('content')
<div class="container-fluid py-4">
   <!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 text-gray-800 mb-2">
            <i class="fas fa-file-contract text-primary me-2"></i> My Lease Details
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('customer.customer-dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('customer.leases.index') }}">
                        <i class="fas fa-list me-1"></i> My Leases
                    </a>
                </li>
                <li class="breadcrumb-item active">Lease #{{ $lease->lease_number }}</li>
            </ol>
        </nav>

        <!-- Right-justified Back Buttons -->
        <div class="mt-3 text-end">
            <a href="{{ route('customer.leases.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Back to My Leases
            </a>
            <a href="{{ route('customer.customer-dashboard') }}" class="btn btn-outline-secondary btn-sm ms-2">
                <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
            </a>
        </div>
    </div>
</div>

    <!-- Loading State (hidden by default) -->
    <div class="row skeleton-loader d-none">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="placeholder-glow">
                        <span class="placeholder col-7"></span>
                        <span class="placeholder col-4"></span>
                        <span class="placeholder col-4"></span>
                        <span class="placeholder col-6"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="lease-content">
        <!-- Tabs Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-tabs" id="leaseTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">Documents</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="billing-tab" data-bs-toggle="tab" data-bs-target="#billing" type="button" role="tab">Billing History</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="support-tab" data-bs-toggle="tab" data-bs-target="#support" type="button" role="tab">Support</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="leaseTabsContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Lease Information Card -->
                        <div class="card shadow mb-4">
                            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Lease Information
                                </h5>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light action-btn" id="copyLeaseNumber">
                                        <i class="fas fa-copy me-1"></i>Copy ID
                                    </button>
                                    <button class="btn btn-sm btn-light action-btn" data-bs-toggle="modal" data-bs-target="#shareModal">
                                        <i class="fas fa-share me-1"></i>Share
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3"><i class="fas fa-cogs me-2"></i>Lease Details</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td><strong>Lease Number:</strong></td>
                                                    <td>{{ $lease->lease_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Status:</strong></td>
                                                    <td>
                                                        <span class="badge status-badge bg-{{ $lease->status === 'active' ? 'success' : ($lease->status === 'pending' ? 'warning' : 'secondary') }}">
                                                            <i class="fas fa-{{ $lease->status === 'active' ? 'check-circle' : 'clock' }} me-1"></i>
                                                            {{ ucfirst($lease->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Service Type:</strong></td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Bandwidth:</strong></td>
                                                    <td>{{ $lease->bandwidth }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Duration:</strong></td>
                                                    <td>{{ $lease->contract_term_months }} months</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3"><i class="fas fa-route me-2"></i>Service Route</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td><strong>From:</strong></td>
                                                    <td>{{ $lease->start_location }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>To:</strong></td>
                                                    <td>{{ $lease->end_location }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Distance:</strong></td>
                                                    <td>{{ $lease->distance_km }} km</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Technology:</strong></td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $lease->technology)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Installation Date:</strong></td>
                                                    <td>{{ $lease->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Report Card -->
                        @if($lease->test_report_path)
                        <div class="card shadow mb-4">
                            <div class="card-header bg-success text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Test Report
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td><strong>Report Type:</strong></td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $lease->test_report_type)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Test Date:</strong></td>
                                                    <td>{{ $lease->test_date ? $lease->test_date->format('M d, Y') : 'Not set' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>File:</strong></td>
                                                    <td>
                                                        <a href="{{ Storage::url($lease->test_report_path) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-outline-primary action-btn">
                                                            <i class="fas fa-eye me-1"></i>View Report
                                                        </a>
                                                        <a href="{{ Storage::url($lease->test_report_path) }}"
                                                           download
                                                           class="btn btn-sm btn-outline-success action-btn">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                @if($lease->test_report_description)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-primary"><i class="fas fa-align-left me-2"></i>Test Description</h6>
                                        <div class="border rounded p-3 bg-light">
                                            {{ $lease->test_report_description }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @else
                        <!-- No Test Report Available -->
                        <div class="card shadow mb-4">
                            <div class="card-header bg-secondary text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Test Report
                                </h5>
                            </div>
                            <div class="card-body text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">
                                    No test report available yet. Please check back later.
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Acceptance Certificate Card -->
                        @if($lease->acceptance_certificate_path)
                        <div class="card shadow mb-4">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-certificate me-2"></i>
                                    Acceptance Certificate
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td><strong>Generated On:</strong></td>
                                                    <td>{{ $lease->acceptance_certificate_generated_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Certificate:</strong></td>
                                                    <td>
                                                        <a href="{{ Storage::url($lease->acceptance_certificate_path) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-outline-primary action-btn">
                                                            <i class="fas fa-eye me-1"></i>View Certificate
                                                        </a>
                                                        <a href="{{ Storage::url($lease->acceptance_certificate_path) }}"
                                                           download
                                                           class="btn btn-sm btn-outline-success action-btn">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Status:</strong></td>
                                                    <td>
                                                        <span class="badge bg-success status-badge">
                                                            <i class="fas fa-check-circle me-1"></i>Generated
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- No Acceptance Certificate Available -->
                        <div class="card shadow mb-4">
                            <div class="card-header bg-secondary text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-certificate me-2"></i>
                                    Acceptance Certificate
                                </h5>
                            </div>
                            <div class="card-body text-center py-4">
                                <i class="fas fa-file-certificate fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">
                                    Acceptance certificate not yet generated. Please check back later.
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <!-- Quick Information Card -->
                        <div class="card shadow mb-4">
                            <div class="card-header bg-info text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar me-2"></i>
                                    Service Dates
                                </h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $remaining = now()->diffInDays($lease->end_date, false);
                                    $totalDays = $lease->start_date->diffInDays($lease->end_date);
                                    $daysPassed = $lease->start_date->diffInDays(now());
                                    $progress = min(100, max(0, ($daysPassed / $totalDays) * 100));
                                @endphp
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Start Date:</strong></td>
                                            <td>
                                                {{ $lease->start_date->format('M d, Y') }}
                                                <small class="text-muted d-block">({{ $lease->start_date->diffForHumans() }})</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>End Date:</strong></td>
                                            <td>
                                                {{ $lease->end_date->format('M d, Y') }}
                                                <small class="text-muted d-block">({{ $lease->end_date->diffForHumans() }})</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Remaining:</strong></td>
                                            <td>
                                                @if($remaining > 0)
                                                    <span class="badge bg-success status-badge">{{ $remaining }} days</span>
                                                @else
                                                    <span class="badge bg-danger status-badge">Expired</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Lease Progress</small>
                                        <small>{{ number_format($progress, 1) }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $remaining > 0 ? 'success' : 'danger' }}" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Information Card -->
                        <div class="card shadow mb-4">
                            <div class="card-header bg-warning text-dark py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-money-bill me-2"></i>
                                    Billing Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Monthly Cost:</strong></td>
                                            <td>{{ $lease->currency }} {{ number_format($lease->monthly_cost, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Billing Cycle:</strong></td>
                                            <td>{{ ucfirst($lease->billing_cycle) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Value:</strong></td>
                                            <td>{{ $lease->currency }} {{ number_format($lease->total_contract_value, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Next Billing:</strong></td>
                                            <td>{{ now()->addMonth()->startOfMonth()->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <a href="{{ route('customer.invoices.index', ['lease' => $lease->id]) }}" class="btn btn-outline-primary btn-sm action-btn">
                                        <i class="fas fa-file-invoice-dollar me-1"></i>View Invoices
                                    </a>
                                    <a href="{{ route('customer.payments.create', ['lease' => $lease->id]) }}" class="btn btn-outline-success btn-sm action-btn">
                                        <i class="fas fa-credit-card me-1"></i>Make Payment
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Support Information -->
                        <div class="card shadow">
                            <div class="card-header bg-secondary text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-headset me-2"></i>
                                    Need Help?
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">For technical support or questions about your lease:</p>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-phone text-primary me-2"></i> Support: +254-700-123-456</li>
                                    <li class="mb-2"><i class="fas fa-envelope text-primary me-2"></i> Email: support@darkfibre.com</li>
                                    <li class="mb-2"><i class="fas fa-clock text-primary me-2"></i> Hours: 24/7</li>
                                    <li><i class="fas fa-ticket-alt text-primary me-2"></i> <a href="{{ route('customer.support.create') }}" class="text-decoration-none">Open Support Ticket</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-folder me-2"></i>
                                    Lease Documents
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Document Status Summary -->
                                <!-- Document Status Summary -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-light border-0">
            <div class="card-body text-center py-3">
                <h4 class="text-primary mb-1">{{ $documents->where('status', 'approved')->count() }}</h4>
                <small class="text-muted">Approved</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light border-0">
            <div class="card-body text-center py-3">
                <h4 class="text-warning mb-1">{{ $documents->where('status', 'pending_review')->count() }}</h4>
                <small class="text-muted">Pending Review</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light border-0">
            <div class="card-body text-center py-3">
                <h4 class="text-danger mb-1">{{ $documents->where('status', 'rejected')->count() }}</h4>
                <small class="text-muted">Rejected</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light border-0">
            <div class="card-body text-center py-3">
                <h4 class="text-info mb-1">{{ $documents->count() }}</h4>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>
</div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Size</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @forelse($documents as $document)
<tr>
    <td>
        <i class="fas {{ $document->document_type === 'contract' ? 'fa-file-contract text-primary' : ($document->document_type === 'certificate' ? 'fa-file-certificate text-info' : 'fa-file-alt text-success') }} me-2"></i>
        {{ $document->name }}
    </td>
    <td>
        <span class="badge bg-secondary">{{ ucfirst($document->document_type) }}</span>
    </td>
    <td>
        <span class="badge status-badge bg-{{ $document->status === 'approved' ? 'success' : ($document->status === 'pending_review' ? 'warning' : 'danger') }}">
            <i class="fas fa-{{ $document->status === 'approved' ? 'check-circle' : ($document->status === 'pending_review' ? 'clock' : 'times-circle') }} me-1"></i>
            {{ ucfirst(str_replace('_', ' ', $document->status)) }}
        </span>
    </td>
    <td>{{ $document->created_at->format('M d, Y') }}</td>
    <td>{{ number_format($document->file_size / 1024, 1) }} KB</td>
    <td>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('customer.documents.lease.show', $document->id) }}"
               target="_blank"
               class="btn btn-outline-primary action-btn"
               title="View Document">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('customer.documents.download', $document->id) }}"
               class="btn btn-outline-success action-btn"
               title="Download Document">
                <i class="fas fa-download"></i>
            </a>
            @if($document->status === 'pending_review')
            <button type="button"
                    class="btn btn-outline-success action-btn approve-document"
                    data-document-id="{{ $document->id }}"
                    data-document-name="{{ $document->name }}"
                    title="Approve Document">
                <i class="fas fa-check"></i>
            </button>
            <button type="button"
                    class="btn btn-outline-danger action-btn reject-document"
                    data-document-id="{{ $document->id }}"
                    data-document-name="{{ $document->name }}"
                    title="Reject Document">
                <i class="fas fa-times"></i>
            </button>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-4">
        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
        <p class="text-muted">No documents found for this lease.</p>
    </td>
</tr>
@endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Document Upload Section -->
                                <!-- Document Upload Section -->
<div class="mt-4">
    <h6 class="text-primary mb-3"><i class="fas fa-upload me-2"></i>Upload New Document</h6>
    <form action="{{ route('customer.documents.create', $lease->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
        @csrf
        <div class="col-md-4">
            <input type="text" name="name" class="form-control form-control-sm" placeholder="Document Name" required>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select form-select-sm" required>
                <option value="">Select Type</option>
                <option value="contract">Contract</option>
                <option value="certificate">Certificate</option>
                <option value="report">Report</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="file" name="document" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="fas fa-upload me-1"></i>Upload
            </button>
        </div>
    </form>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Tab -->
            <div class="tab-pane fade" id="billing" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-warning text-dark py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    Billing History
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($lease->payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Period</th>
                                                <th>Amount</th>
                                                <th>Due Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lease->payments as $payment)
                                            <tr>
                                                <td>INV-{{ $payment->id }}</td>
                                                <td>{{ $payment->payment_date->format('F Y') }}</td>
                                                <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ $payment->due_date->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }} status-badge">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('customer.invoices.show', $payment->id) }}" class="btn btn-sm btn-outline-primary action-btn">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="{{ route('customer.invoices.download', $payment->id) }}" class="btn btn-sm btn-outline-success action-btn">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No payment history found for this lease.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Tab -->
            <div class="tab-pane fade" id="support" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-info text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-ticket-alt me-2"></i>
                                    Open Support Ticket
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('customer.support.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="lease_id" value="{{ $lease->id }}">
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Brief description of your issue" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select a category</option>
                                            <option value="technical">Technical Issue</option>
                                            <option value="billing">Billing Question</option>
                                            <option value="service">Service Upgrade</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Please provide details about your issue" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="attachment" class="form-label">Attachment (Optional)</label>
                                        <input class="form-control" type="file" id="attachment" name="attachment">
                                    </div>
                                    <button type="submit" class="btn btn-primary action-btn">
                                        <i class="fas fa-paper-plane me-1"></i> Submit Ticket
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card shadow">
                            <div class="card-header bg-secondary text-white py-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Recent Support Tickets
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @forelse($supportTickets as $ticket)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $ticket->subject }}</h6>
                                            <p class="mb-1 text-muted small">Submitted on {{ $ticket->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <span class="badge bg-{{ $ticket->status === 'open' ? 'warning' : ($ticket->status === 'resolved' ? 'success' : 'secondary') }} status-badge">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </div>
                                    @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-ticket-alt fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">No support tickets found.</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast for notifications -->
<div class="toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3" id="successToast" role="alert">
    <div class="d-flex">
        <div class="toast-body" id="toastMessage">
            Lease ID copied to clipboard!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
</div>

<!-- Document Approval Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve <strong id="approveDocumentName"></strong>?</p>
                <form id="approveForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="approvalNotes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approvalNotes" name="notes" rows="3" placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApprove">Approve Document</button>
            </div>
        </div>
    </div>
</div>

<!-- Document Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject <strong id="rejectDocumentName"></strong>?</p>
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejectionReason" name="reason" rows="3" placeholder="Please provide the reason for rejection..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Reject Document</button>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Lease Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Share lease information with others:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="{{ url()->current() }}" readonly>
                    <button class="btn btn-outline-secondary" type="button" id="copyShareLink">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="includeSensitive">
                    <label class="form-check-label" for="includeSensitive">
                        Include sensitive information (billing details, test reports)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Generate Share Link</button>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #4361ee;
        --success: #4cc9a7;
        --warning: #f9a826;
        --danger: #f72585;
        --info: #4895ef;
        --secondary: #6c757d;
    }

    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
        font-weight: 600;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: var(--primary);
        text-decoration: none;
    }

    .status-badge {
        font-size: 0.8rem;
        padding: 0.35rem 0.75rem;
    }

    .progress {
        border-radius: 10px;
    }

    .progress-bar {
        border-radius: 10px;
    }

    .skeleton-loader {
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    .empty-state {
        padding: 3rem 1rem;
    }

    .table-borderless td {
        padding: 0.5rem 0.25rem;
    }

    .action-btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .nav-tabs .nav-link {
        border: none;
        color: var(--secondary);
        font-weight: 500;
        padding: 0.75rem 1.5rem;
    }

    .nav-tabs .nav-link.active {
        color: var(--primary);
        border-bottom: 3px solid var(--primary);
        background: transparent;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Copy lease number functionality
    document.getElementById('copyLeaseNumber').addEventListener('click', function() {
        navigator.clipboard.writeText('{{ $lease->lease_number }}');
        const toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
    });

    // Copy share link functionality
    document.getElementById('copyShareLink').addEventListener('click', function() {
        const shareLink = document.querySelector('#shareModal input[type="text"]');
        shareLink.select();
        navigator.clipboard.writeText(shareLink.value);

        // Change button text temporarily
        const originalHTML = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => {
            this.innerHTML = originalHTML;
        }, 2000);
    });

    // Simulate loading state (for demonstration)
    function simulateLoading() {
        document.querySelector('.skeleton-loader').classList.remove('d-none');
        document.getElementById('lease-content').classList.add('d-none');

        setTimeout(() => {
            document.querySelector('.skeleton-loader').classList.add('d-none');
            document.getElementById('lease-content').classList.remove('d-none');
        }, 1500);
    }

    // Uncomment the line below to see loading simulation
    // window.addEventListener('load', simulateLoading);
</script>
@endsection
