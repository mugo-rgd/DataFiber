{{-- resources/views/customer/documents/lease-show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('customer.customer-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.documents.index') }}">Documents</a></li>
                        <li class="breadcrumb-item active">{{ $lease->title }}</li>
                    </ol>
                </div>
                <h4 class="page-title">Documents for {{ $lease->title }}</h4>
            </div>
        </div>
    </div>

    <!-- Project Summary Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">{{ $lease->title }}</h5>
                            <p class="card-text">
                                <i class="fas fa-hashtag text-muted me-2"></i>
                                <strong>Lease Number:</strong> {{ $lease->lease_number }}
                            </p>
                            <p class="card-text">
                                <i class="fas fa-route text-muted me-2"></i>
                                <strong>Route:</strong> {{ $lease->start_location }} → {{ $lease->end_location }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="card-text">
                                <i class="fas fa-network-wired text-muted me-2"></i>
                                <strong>Service Type:</strong>
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</span>
                            </p>
                            <p class="card-text">
                                <i class="fas fa-calendar-alt text-muted me-2"></i>
                                <strong>Period:</strong> {{ $lease->start_date->format('M d, Y') }} - {{ $lease->end_date->format('M d, Y') }}
                            </p>
                            <p class="card-text">
                                <i class="fas fa-info-circle text-muted me-2"></i>
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $lease->status == 'active' ? 'success' : ($lease->status == 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($lease->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Projects
                        </a>
                        <a href="{{ route('customer.documents.requests.index') }}" class="btn btn-primary">
                            <i class="fas fa-file-import me-2"></i>Request Missing Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotations Section -->
    @if($documents['quotations']->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Quotations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Quotation #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Valid Until</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents['quotations'] as $quotation)
                                <tr>
                                    <td>
                                        <strong>{{ $quotation->quotation_number }}</strong>
                                    </td>
                                    <td>{{ $quotation->created_at->format('M d, Y') }}</td>
                                    <td>${{ number_format($quotation->total_amount, 2) }}</td>
                                    <td>{{ $quotation->valid_until->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $quotation->status == 'approved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($quotation->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($quotation->document && $quotation->document->file_path)
                                            <a href="{{ Storage::url($quotation->document->file_path) }}"
                                               target="_blank" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ Storage::url($quotation->document->file_path) }}"
                                               download class="btn btn-outline-success">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            @else
                                            <a href="{{ route('customer.quotations.show', $quotation->id) }}"
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                            @endif
                                        </div>
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

    <!-- Contracts Section -->
    @if($documents['contracts']->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-contract me-2"></i>Contracts
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Contract #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents['contracts'] as $contract)
                                <tr>
                                    <td>
                                        <strong>{{ $contract->contract_number }}</strong>
                                    </td>
                                    <td>{{ $contract->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $contract->status == 'approved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($contract->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($contract->document && $contract->document->file_path)
                                            <a href="{{ Storage::url($contract->document->file_path) }}"
                                               target="_blank" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ Storage::url($contract->document->file_path) }}"
                                               download class="btn btn-outline-success">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            @else
                                            <a href="{{ route('customer.contracts.show', $contract->id) }}"
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                            @endif
                                        </div>
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

    <!-- Certificates Section -->
    @if($documents['acceptance_certificates']->count() > 0 || $documents['conditional_certificates']->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-certificate me-2"></i>Certificates
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Acceptance Certificates -->
                    @if($documents['acceptance_certificates']->count() > 0)
                    <h6 class="mb-3">
                        <i class="fas fa-file-certificate me-2"></i>Acceptance Certificates
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Certificate #</th>
                                    <th>Effective Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents['acceptance_certificates'] as $cert)
                                <tr>
                                    <td>
                                        <strong>{{ $cert->certificate_ref }}</strong>
                                    </td>
                                    <td>{{ $cert->effective_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($cert->test_report_path)
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ Storage::url($cert->test_report_path) }}"
                                               target="_blank" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ Storage::url($cert->test_report_path) }}"
                                               download class="btn btn-outline-success">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- Conditional Certificates -->
                    @if($documents['conditional_certificates']->count() > 0)
                    <h6 class="mb-3">
                        <i class="fas fa-file-certificate me-2"></i>Conditional Certificates
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Certificate #</th>
                                    <th>Issue Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents['conditional_certificates'] as $cert)
                                <tr>
                                    <td>
                                        <strong>{{ $cert->ref_number }}</strong>
                                    </td>
                                    <td>{{ $cert->certificate_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($cert->inspection_report_path)
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ Storage::url($cert->inspection_report_path) }}"
                                               target="_blank" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ Storage::url($cert->inspection_report_path) }}"
                                               download class="btn btn-outline-success">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Lease Documents & Reports -->
    @if($documents['leases']->count() > 0 || $documents['reports']->count() > 0)
    <div class="row mb-4">
        <div class="col-md-6">
            @if($documents['leases']->count() > 0)
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-signature me-2"></i>Lease Agreements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($documents['leases'] as $doc)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $doc->name }}</h6>
                                <small>{{ $doc->created_at->format('M d, Y') }}</small>
                            </div>
                            <p class="mb-1 small text-muted">
                                {{ $doc->description ?? 'Lease agreement document' }}
                            </p>
                            <div class="btn-group btn-group-sm mt-2">
                                <a href="{{ Storage::url($doc->file_path) }}"
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ Storage::url($doc->file_path) }}"
                                   download class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-6">
            @if($documents['reports']->count() > 0)
            <div class="card h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Test Reports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($documents['reports'] as $doc)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $doc->name }}</h6>
                                <small>{{ $doc->created_at->format('M d, Y') }}</small>
                            </div>
                            <p class="mb-1 small text-muted">
                                {{ $doc->description ?? 'Test report document' }}
                            </p>
                            <div class="btn-group btn-group-sm mt-2">
                                <a href="{{ Storage::url($doc->file_path) }}"
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ Storage::url($doc->file_path) }}"
                                   download class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Other Documents -->
    @if($documents['other']->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Other Documents
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Document Name</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents['other'] as $doc)
                                <tr>
                                    <td>{{ $doc->name }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $doc->document_type ?? 'Other' }}
                                        </span>
                                    </td>
                                    <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($doc->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ Storage::url($doc->file_path) }}"
                                               target="_blank" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ Storage::url($doc->file_path) }}"
                                               download class="btn btn-outline-success">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
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

    <!-- Empty State -->
    @php
        $totalDocuments = array_sum(array_map(fn($docs) => $docs->count(), $documents));
    @endphp

    @if($totalDocuments === 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                    <h5 class="text-muted">No Documents Available</h5>
                    <p class="text-muted mb-4">No documents have been uploaded for this project yet.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('customer.documents.requests.index') }}" class="btn btn-primary">
                            <i class="fas fa-file-import me-2"></i>Request Documents
                        </a>
                        <a href="{{ route('customer.design-requests.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Create Design Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .card {
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>

@endsection
