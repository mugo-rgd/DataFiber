{{-- resources/views/customer/documents/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('customer.customer-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Documents</li>
                    </ol>
                </div>
                <h4 class="page-title">Project Documents</h4>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">All Project Documents</h5>
                    <p class="text-muted mb-0">Access and download your project documents</p>
                </div>
                <div>
                    <a href="{{ route('customer.documents.index') }}" class="btn btn-success">
                        <i class="fas fa-file-import me-2"></i>Request Missing Documents
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($leases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Project Name</th>
                                        <th>Service Type</th>
                                        <th>Route</th>
                                        <th>Status</th>
                                        <th>Documents Available</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leases as $lease)
                                    @php
                                        // Count documents for this lease
                                        $docCount = \App\Models\Document::where('lease_id', $lease->id)->count();

                                        // Check specific document types
                                        $hasQuotation = \App\Models\Quotation::whereHas('lease', function($q) use ($lease) {
                                            $q->where('id', $lease->id);
                                        })->exists();

                                        $hasContract = \App\Models\Contract::whereHas('quotation', function($q) use ($lease) {
                                            $q->whereHas('lease', function($query) use ($lease) {
                                                $query->where('id', $lease->id);
                                            });
                                        })->exists();

                                        $hasAcceptance = \App\Models\AcceptanceCertificate::whereHas('request', function($q) use ($lease) {
                                            $q->whereHas('lease', function($query) use ($lease) {
                                                $query->where('id', $lease->id);
                                            });
                                        })->exists();

                                        $hasConditional = \App\Models\ConditionalCertificate::whereHas('request', function($q) use ($lease) {
                                            $q->whereHas('lease', function($query) use ($lease) {
                                                $query->where('id', $lease->id);
                                            });
                                        })->exists();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-project-diagram text-primary"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">{{ $lease->title }}</h6>
                                                    <p class="text-muted mb-0 small">#{{ $lease->lease_number }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-map-marker-alt text-danger"></i> {{ $lease->start_location }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-map-marker-alt text-success"></i> {{ $lease->end_location }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $lease->status == 'active' ? 'success' : ($lease->status == 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($lease->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @if($hasQuotation)
                                                <span class="badge bg-info" data-bs-toggle="tooltip" title="Quotation">
                                                    <i class="fas fa-file-invoice"></i>
                                                </span>
                                                @endif

                                                @if($hasContract)
                                                <span class="badge bg-warning" data-bs-toggle="tooltip" title="Contract">
                                                    <i class="fas fa-file-contract"></i>
                                                </span>
                                                @endif

                                                @if($hasAcceptance)
                                                <span class="badge bg-success" data-bs-toggle="tooltip" title="Acceptance Certificate">
                                                    <i class="fas fa-certificate"></i>
                                                </span>
                                                @endif

                                                @if($hasConditional)
                                                <span class="badge bg-purple" data-bs-toggle="tooltip" title="Conditional Certificate">
                                                    <i class="fas fa-file-certificate"></i>
                                                </span>
                                                @endif

                                                @if($lease->test_report_path)
                                                <span class="badge bg-dark" data-bs-toggle="tooltip" title="Test Reports">
                                                    <i class="fas fa-chart-line"></i>
                                                </span>
                                                @endif

                                                @if($docCount > 0)
                                                <span class="badge bg-secondary">
                                                    +{{ $docCount }} more
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('customer.documents.lease.show', $lease->id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> View All
                                                </a>
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('customer.quotations.show', $quotation->id) }}?type=quotation">
                                                            <i class="fas fa-file-invoice me-2"></i> Quotations
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('customer.contracts.show', $contracts->id) }}?type=contract">
                                                            <i class="fas fa-file-contract me-2"></i> Contracts
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <!-- Conditional Certificate Option -->
<a class="dropdown-item" href="{{ route('customer.certificate.show', $certificate->id) }}?type=conditional">
    <i class="fas fa-certificate me-2"></i> Conditional Certificate
</a>

<!-- Acceptance Certificate Option -->
<a class="dropdown-item" href="{{ route('customer.certificate.show', $certificate->id) }}?type=acceptance">
    <i class="fas fa-certificate me-2"></i> Acceptance Certificate
</a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                           data-bs-target="#requestModal{{ $lease->id }}">
                                                            <i class="fas fa-file-import me-2"></i> Request Documents
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $leases->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                            <h5 class="text-muted">No Projects Found</h5>
                            <p class="text-muted mb-4">You don't have any active projects yet.</p>
                            <a href="{{ route('customer.leases.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Create Your First Project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Document Modal Template -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer.documents.request.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Request Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="projectSelect" class="form-label">Select Project</label>
                        <select class="form-select" id="projectSelect" name="lease_id" required>
                            <option value="">Choose project...</option>
                            @foreach($leases as $lease)
                            <option value="{{ $lease->id }}">{{ $lease->title }} (#{{ $lease->lease_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Document Type</label>
                        <div class="row">
                            @foreach(['quotation', 'contract', 'acceptance_certificate', 'conditional_certificate', 'lease', 'report'] as $type)
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input"
                                           id="doc_{{ $type }}" name="document_types[]" value="{{ $type }}">
                                    <label class="form-check-label" for="doc_{{ $type }}">
                                        {{ ucwords(str_replace('_', ' ', $type)) }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="requestNotes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="requestNotes" name="notes"
                                  rows="3" placeholder="Specify any requirements..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
