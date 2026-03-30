{{-- @extends('layouts.app')

@section('title', 'My Documents')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800 mb-0">
                    <i class="fas fa-folder me-2"></i>My Documents
                </h1>
                <div>
                    <a href="{{ route('customer.customer-dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('customer.documents.create') }}" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload New Document
                    </a>
                </div>
            </div>

            <!-- Document Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-1">{{ $documents->where('status', 'approved')->count() }}</h4>
                            <small>Approved</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-1">{{ $documents->where('status', 'pending_review')->count() }}</h4>
                            <small>Pending Review</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-1">{{ $documents->where('status', 'rejected')->count() }}</h4>
                            <small>Rejected</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-1">{{ $documents->count() }}</h4>
                            <small>Total Documents</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Table -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>All Uploaded Documents
                    </h5>
                </div>
                <div class="card-body">
                    @if($documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Document Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Upload Date</th>
                                    <th>File Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas
                                                @if(str_contains($document->document_type, 'contract')) fa-file-contract
                                                @elseif(str_contains($document->document_type, 'certificate')) fa-file-certificate
                                                @elseif(str_contains($document->document_type, 'report')) fa-file-chart-line
                                                @else fa-file
                                                @endif
                                                me-3 text-primary fa-lg">
                                            </i>
                                            <div>
                                                <strong>{{ $document->name }}</strong>
                                                @if($document->is_required)
                                                    <span class="badge bg-danger ms-2">Required</span>
                                                @endif
                                                @if($document->description)
                                                    <br><small class="text-muted">{{ Str::limit($document->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary text-capitalize">
                                            {{ str_replace('_', ' ', $document->document_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge bg-{{ $document->status === 'approved' ? 'success' : ($document->status === 'pending_review' ? 'warning' : 'danger') }}">
                                            <i class="fas
                                                @if($document->status === 'approved') fa-check-circle
                                                @elseif($document->status === 'pending_review') fa-clock
                                                @else fa-times-circle
                                                @endif
                                                me-1">
                                            </i>
                                            {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                        </span>
                                        @if($document->rejection_reason)
                                            <br><small class="text-danger">{{ Str::limit($document->rejection_reason, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $document->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $document->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        {{ number_format($document->file_size / 1024, 1) }} KB
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('customer.documents.download', $document) }}"
                                               class="btn btn-outline-success"
                                               title="Download Document"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('customer.documents.show', $document) }}"
                                               class="btn btn-outline-primary"
                                               title="View Details"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($document->status, ['pending_review', 'rejected']))
                                            <form action="{{ route('customer.documents.destroy', $document) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-outline-danger"
                                                        title="Delete Document"
                                                        data-bs-toggle="tooltip"
                                                        onclick="return confirm('Are you sure you want to delete \"{{ $document->name }}\"? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Documents Found</h4>
                        <p class="text-muted mb-4">You haven't uploaded any documents yet.</p>
                        <a href="{{ route('customer.documents.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-upload me-2"></i>Upload Your First Document
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if($documents->count() > 0)
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-upload fa-2x text-success mb-3"></i>
                            <h5>Upload Another Document</h5>
                            <p class="text-muted">Need to submit more documents?</p>
                            <a href="{{ route('customer.documents.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Upload New Document
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-question-circle fa-2x text-info mb-3"></i>
                            <h5>Need Help?</h5>
                            <p class="text-muted">Having issues with document upload?</p>
                            <a href="{{ route('customer.support.create') }}" class="btn btn-info">
                                <i class="fas fa-headset me-2"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endpush
@endsection --}}

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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">All Projects</h5>
                            <p class="text-muted mb-0">Access documents for your projects</p>
                        </div>
                        <div>
                            <a href="{{ route('customer.documents.requests.index') }}" class="btn btn-success">
                                <i class="fas fa-file-import me-2"></i>Request Missing Documents
                            </a>
                            {{-- <a href="{{ route('customer.documents.profile.create') }}" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Profile Documents
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($leases->count() > 0)
        <div class="row">
            @foreach($leases as $lease)
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-0">{{ $lease->title }}</h5>
                                <p class="text-muted mb-0 small">#{{ $lease->lease_number }}</p>
                            </div>
                            <span class="badge bg-{{ $lease->status == 'active' ? 'success' : ($lease->status == 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($lease->status) }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1">
                                <i class="fas fa-route text-primary me-2"></i>
                                {{ $lease->start_location }} → {{ $lease->end_location }}
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-network-wired text-info me-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-file-alt text-secondary me-2"></i>
                                {{ $lease->documents_count }} documents available
                            </p>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.documents.lease.show', $lease->id) }}"
                               class="btn btn-primary">
                                <i class="fas fa-folder-open me-2"></i>View Documents
                            </a>

                            @if($lease->status == 'active')
                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#requestModal{{ $lease->id }}">
                                <i class="fas fa-file-import me-2"></i>Request More
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
{{-- In your customer/documents/index.blade.php --}}
{{-- <div class="alert alert-info mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-1"><i class="fas fa-info-circle me-2"></i>Profile Documents</h6>
            <p class="mb-0">Upload required company documents for verification.</p>
        </div>
        <a href="{{ route('customer.documents.profile.create') }}" class="btn btn-info">
            <i class="fas fa-upload me-2"></i>Upload Profile Documents
        </a>
    </div>
</div> --}}
                <!-- Request Modal -->
                <div class="modal fade" id="requestModal{{ $lease->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('customer.documents.requests.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="lease_id" value="{{ $lease->id }}">

                                <div class="modal-header">
                                    <h5 class="modal-title">Request Documents</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Select Document Types</label>
                                        <div class="row">
                                            @foreach(['quotation', 'contract', 'acceptance_certificate', 'conditional_certificate', 'lease', 'report'] as $type)
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input"
                                                           id="doc_{{ $lease->id }}_{{ $type }}"
                                                           name="document_types[]" value="{{ $type }}">
                                                    <label class="form-check-label" for="doc_{{ $lease->id }}_{{ $type }}">
                                                        {{ ucwords(str_replace('_', ' ', $type)) }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="notes{{ $lease->id }}" class="form-label">Notes</label>
                                        <textarea class="form-control" id="notes{{ $lease->id }}"
                                                  name="notes" rows="3"></textarea>
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
            </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted">No Projects Found</h5>
                        <p class="text-muted mb-4">You don't have any active projects yet.</p>
                        <a href="{{ route('customer.design-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Project
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
