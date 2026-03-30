{{-- resources/views/documents/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                @php
                    // Determine header display based on user role
                    $user = auth()->user();
                    $headerTitle = 'System Documents';

                    if ($user->role === 'account_manager') {
                        $companyDisplay = $companyName ?? 'Assigned Customers';
                        $customerCount = $customerCount ?? 0;
                        $companyCount = count($assignedCompanies ?? []);
                    } elseif ($user->role === 'customer' && isset($customer)) {
                        $companyDisplay = $customer->company_name ?? $customer->name;
                    } else {
                        $companyDisplay = ucfirst($user->role);
                    }
                @endphp

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>{{ $headerTitle }}
                    </h3>

                    <div class="card-tools">
                        @if($user->role === 'account_manager')
                            {{-- Account Manager Header --}}
                            <div class="d-flex align-items-center">
                                @if(isset($assignedCompanies) && count($assignedCompanies) > 0)
                                    <div class="dropdown mr-2">
                                        <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fas fa-building mr-1"></i>
                                            @if(count($assignedCompanies) === 1)
                                                {{ $assignedCompanies[0] }}
                                            @else
                                                Multiple Companies ({{ count($assignedCompanies) }})
                                            @endif
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <h6 class="dropdown-header">Managed Companies</h6>
                                            @foreach($assignedCompanies as $company)
                                                <span class="dropdown-item-text">
                                                    <i class="fas fa-check-circle text-success mr-1"></i>
                                                    {{ $company }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-info">
                                        <i class="fas fa-users mr-1"></i> Assigned Customers
                                    </span>
                                @endif

                                @if(isset($customerCount) && $customerCount > 0)
                                    <span class="badge badge-primary ml-2" title="Assigned Customers">
                                        <i class="fas fa-user-friends mr-1"></i> {{ $customerCount }}
                                    </span>
                                @endif

                                @if(isset($totalRequests))
                                    <span class="badge badge-success ml-2" title="Total Requests">
                                        <i class="fas fa-file-contract mr-1"></i> {{ $totalRequests }}
                                    </span>
                                @endif
                            </div>

                        @elseif($user->role === 'customer' && isset($customer))
                            {{-- Customer Header --}}
                            <span class="badge badge-info">
                                {{ $companyDisplay }}
                            </span>
                            @if(isset($totalRequests))
                                <span class="badge badge-primary ml-2">
                                    <i class="fas fa-file-alt mr-1"></i> {{ $totalRequests }} Requests
                                </span>
                            @endif

                        @else
                            {{-- Admin/Other Roles Header --}}
                            <span class="badge badge-success">
                                <i class="fas fa-user-tie mr-1"></i> {{ $companyDisplay }}
                            </span>
                            @if(isset($totalRequests))
                                <span class="badge badge-primary ml-2">
                                    <i class="fas fa-folder mr-1"></i> {{ $totalRequests }} Documents
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    {{-- Account Manager Stats Card --}}
                    @if(auth()->user()->role === 'account_manager' && isset($customerCount))
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-bar mr-2"></i>Account Manager Dashboard
                                        </h5>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row text-center">
                                            <div class="col-md-3 mb-3">
                                                <div class="border-right">
                                                    <div class="h3 mb-0">{{ $customerCount }}</div>
                                                    <div class="text-muted small">Assigned Customers</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="border-right">
                                                    <div class="h3 mb-0">{{ count($assignedCompanies ?? []) }}</div>
                                                    <div class="text-muted small">Companies</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="border-right">
                                                    <div class="h3 mb-0">{{ $totalRequests ?? 0 }}</div>
                                                    <div class="text-muted small">Design Requests</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div>
                                                    <div class="h3 mb-0">{{ $documents->count() ?? 0 }}</div>
                                                    <div class="text-muted small">Total Documents</div>
                                                </div>
                                            </div>
                                        </div>
                                        @if(isset($assignedCompanies) && count($assignedCompanies) > 0)
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Managing customers from:
                                                    @foreach($assignedCompanies as $index => $company)
                                                        <span class="badge badge-light mr-1">{{ $company }}</span>
                                                        @if($index == 2 && count($assignedCompanies) > 3)
                                                            +{{ count($assignedCompanies) - 3 }} more
                                                            @break
                                                        @endif
                                                    @endforeach
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Display messages --}}
                    @if(isset($message))
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle mr-2"></i>{{ $message }}
                        </div>
                    @endif

                    {{-- No documents message --}}
                    @if($documents->isEmpty() && !isset($message))
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> No Documents Found</h5>
                            @if(auth()->user()->role === 'account_manager')
                                <p>No documents found for your assigned customers. Documents will appear here once design requests are created and processed.</p>
                                @if(isset($customerCount) && $customerCount > 0)
                                    <p class="mb-0">
                                        <small>
                                            You have <strong>{{ $customerCount }}</strong> assigned customer(s) across
                                            <strong>{{ count($assignedCompanies ?? []) }}</strong> company/companies.
                                        </small>
                                    </p>
                                @endif
                            @elseif(auth()->user()->role === 'customer')
                                <p>You don't have any documents yet. Documents will appear here once your design requests are processed.</p>
                            @else
                                <p>No documents found in the system.</p>
                            @endif
                        </div>
                    @elseif(!$documents->isEmpty())
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search documents..." id="documentSearch">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                                        <i class="fas fa-filter"></i> Filter by Type
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item filter-doc" href="#" data-type="all">All Documents</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item filter-doc" href="#" data-type="quotation">Quotations</a>
                                        <a class="dropdown-item filter-doc" href="#" data-type="conditional_certificate">Conditional Certificates</a>
                                        <a class="dropdown-item filter-doc" href="#" data-type="acceptance_certificate">Acceptance Certificates</a>
                                        <a class="dropdown-item filter-doc" href="#" data-type="contract">Contracts</a>
                                        <a class="dropdown-item filter-doc" href="#" data-type="lease">Lease Agreements</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="documentsTable">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Document Reference</th>
                                        <th>Design Request</th>
                                        <th>Customer</th>
                                        <th>Route Name</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $doc)
                                        @php
                                            $document = $doc['document'];
                                            $designRequest = $doc['design_request'];
                                            $customer = $designRequest->customer ?? $document->customer ?? null;
                                        @endphp
                                        <tr class="document-row" data-type="{{ $doc['type'] }}">
                                            <td>
                                                <span class="badge rounded-pill" style="{{ documentTypeBadgeStyle($doc['type']) }}">
                                                    {{ $doc['type_name'] }}
                                                </span>
                                            </td>
                                            <td>
                                                @switch($doc['type'])
                                                    @case('quotation')
                                                        {{ $document->quotation_number }}
                                                        @break
                                                    @case('conditional_certificate')
                                                        {{ $document->ref_number }}
                                                        @break
                                                    @case('acceptance_certificate')
                                                        {{ $document->certificate_ref }}
                                                        @break
                                                    @case('contract')
                                                        {{ $document->contract_number }}
                                                        @break
                                                    @case('lease')
                                                        {{ $document->lease_number }}
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($designRequest)
                                                    @if(Route::has('design-requests.show'))
                                                        <a href="{{ route('design-requests.show', $designRequest->id) }}"
                                                           class="text-primary">
                                                            {{ $designRequest->request_number }}
                                                        </a>
                                                    @else
                                                        <span class="text-primary">
                                                            {{ $designRequest->request_number }}
                                                        </span>
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($customer)
                                                    <div>
                                                        <strong>{{ $customer->name }}</strong>
                                                        @if($customer->company_name)
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-building mr-1"></i>
                                                                {{ $customer->company_name }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                {{ $designRequest->route_name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill" style="{{ statusBadgeStyle($doc['status']) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $doc['status'])) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $doc['created_at']->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @switch($doc['type'])
                                                        @case('quotation')
                                                            @if(Route::has('designer.quotations.show'))
                                                                <a href="{{ route('designer.quotations.show', $document->id) }}"
                                                                   class="btn btn-info" title="View Quotation">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif
                                                            @if(Route::has('account-manager.quotations.download'))
                                                                <a href="{{ route('account-manager.quotations.download', $document->id) }}"
                                                                   class="btn btn-primary" title="Download Quotation">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            @endif
                                                            @break
                                                        @case('conditional_certificate')
                                                        <button class="btn btn-info view-document"
                                                                    data-type="{{ $doc['type'] }}"
                                                                    data-id="{{ $document->id }}"
                                                                    title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        @if (Route::has('ictengineer.certificates.conditional.download'))
                                                        <a href="{{ route('ictengineer.certificates.conditional.download', $document->id) }}"
                                                        class="btn btn-primary"
                                                        title="Download Conditional Certificate">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif
                                                            @break
                                                        @case('acceptance_certificate')
                                                            <button class="btn btn-info view-document"
                                                                    data-type="{{ $doc['type'] }}"
                                                                    data-id="{{ $document->id }}"
                                                                    title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if(Route::has('certificates.acceptance.download'))
                                                                <a href="{{ route('certificates.acceptance.download', ['type' => $doc['type'], 'id' => $document->id]) }}"
                                                                   class="btn btn-primary" title="Download Acceptance Certificate">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            @endif
                                                            @break
                                                        @case('contract')
                                                            @if(Route::has('admin.contracts.show'))
                                                                <a href="{{ route('admin.contracts.show', $document->id) }}"
                                                                   class="btn btn-info" title="View Contract">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif
                                                             @if(Route::has('admin.contracts.download'))
                                                                <a href="{{ route('admin.contracts.download', $document->id) }}"
                                                                   class="btn btn-primary" title="Download Contract">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            @endif
                                                            @break
                                                        @case('lease')
                                                            @if(Route::has('admin.leases.show'))
                                                                <a href="{{ route('admin.leases.show', $document->id) }}"
                                                                   class="btn btn-info" title="View Lease">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif
                                                            @if(Route::has('admin.leases.download'))
                                                                <a href="{{ route('admin.leases.download', $document->id) }}"
                                                                   class="btn btn-info" title="Download Lease">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif
                                                            @break
                                                    @endswitch

                                                    @if($designRequest && Route::has('design-requests.documents'))
                                                        <a href="{{ route('design-requests.documents', $designRequest->id) }}"
                                                           class="btn btn-secondary" title="View All Request Documents">
                                                            <i class="fas fa-folder"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Document Summary Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-pie mr-2"></i>Document Summary
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $summary = [
                                                'quotation' => $documents->where('type', 'quotation')->count(),
                                                'conditional_certificate' => $documents->where('type', 'conditional_certificate')->count(),
                                                'acceptance_certificate' => $documents->where('type', 'acceptance_certificate')->count(),
                                                'contract' => $documents->where('type', 'contract')->count(),
                                                'lease' => $documents->where('type', 'lease')->count(),
                                            ];
                                            $total = array_sum($summary);
                                        @endphp

                                        <div class="row">
                                            @foreach($summary as $type => $count)
                                                @if($count > 0)
                                                <div class="col-md-4 mb-3">
                                                    <div class="info-box">
                                                        <span class="info-box-icon" style="background-color: {{ getDocumentTypeColorHex($type) }}; color: {{ getDocumentTypeTextColor($type) }};">
                                                            <i class="fas fa-file-alt"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text text-capitalize">
                                                                {{ str_replace('_', ' ', $type) }}
                                                            </span>
                                                            <span class="info-box-number">{{ $count }}</span>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: {{ $total > 0 ? ($count/$total)*100 : 0 }}%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                {{ $total > 0 ? round(($count/$total)*100, 1) : 0 }}% of total
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-bolt mr-2"></i>Quick Actions
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if(auth()->user()->role === 'account_manager')
                                            @if(Route::has('design-requests.index'))
                                                <a href="{{ route('design-requests.index') }}" class="btn btn-primary btn-block mb-2">
                                                    <i class="fas fa-list mr-2"></i> View Design Requests
                                                </a>
                                            @endif
                                            @if(Route::has('customers.index'))
                                                <a href="{{ route('customers.index') }}" class="btn btn-info btn-block mb-2">
                                                    <i class="fas fa-users mr-2"></i> Manage Customers
                                                </a>
                                            @endif
                                        @endif

                                        @if(auth()->user()->role === 'customer' && Route::has('design-requests.create'))
                                            <a href="{{ route('design-requests.create') }}" class="btn btn-success btn-block mb-2">
                                                <i class="fas fa-plus mr-2"></i> New Design Request
                                            </a>
                                        @endif

                                        @if(Route::has('dashboard'))
                                            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-block">
                                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                            </a>
                                        @endif
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

<!-- Modal for Document Details -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="documentDetails">
                <!-- Details will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.document-row:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: .25rem;
    background: #fff;
    display: flex;
    margin-bottom: 1rem;
    min-height: 80px;
    padding: .5rem;
    position: relative;
}
.info-box-icon {
    align-items: center;
    display: flex;
    font-size: 1.875rem;
    justify-content: center;
    text-align: center;
    width: 70px;
}
.h3 { font-size: 1.75rem; font-weight: 700; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Search functionality
    $('#documentSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#documentsTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Filter by document type
    $('.filter-doc').click(function(e) {
        e.preventDefault();
        var type = $(this).data('type');

        if (type === 'all') {
            $('#documentsTable tbody tr').show();
        } else {
            $('#documentsTable tbody tr').hide();
            $('.document-row[data-type="' + type + '"]').show();
        }

        // Update active filter
        $('.filter-doc').removeClass('active');
        $(this).addClass('active');
    });

    // View document details
    $('.view-document').click(function() {
        const type = $(this).data('type');
        const id = $(this).data('id');

        // Build the URL correctly
        const url = '/documents/' + type + '/' + id + '/details';

        $.ajax({
            url: url,
            method: 'GET',
            beforeSend: function() {
                $('#documentDetails').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3">Loading document details...</p>
                    </div>
                `);
            },
            success: function(response) {
                $('#documentDetails').html(response);
                $('#documentModal').modal('show');
            },
            error: function(xhr) {
                let errorMessage = 'Error loading document details.';
                if (xhr.status === 403) {
                    errorMessage = 'You are not authorized to view this document.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Document not found.';
                }

                $('#documentDetails').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>${errorMessage}
                    </div>
                `);
                $('#documentModal').modal('show');
            }
        });
    });
});
</script>
@endpush
