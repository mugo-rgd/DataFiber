@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Manage Documents for {{ $customer->name }}
                    </h5>
                    <div>
                        <a href="{{ route('account-manager.customers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Customers
                        </a>
                        <a href="{{ route('account-manager.customers.documents.upload', $customer) }}" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Upload New Document
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Customer Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Customer Information</h6>
                                    <p class="mb-1"><strong>Name:</strong> {{ $customer->name }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $customer->email }}</p>
                                    <p class="mb-1"><strong>Phone:</strong> {{ $customer->phone ?? 'Not provided' }}</p>
                                    <p class="mb-1"><strong>Company:</strong> {{ $customer->company_name ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Active Leases</h6>
                                    @if($leases->count() > 0)
                                        <ul class="mb-0">
                                            @foreach($leases as $lease)
                                                <li>{{ $lease->lease_number }} - {{ $lease->property_name }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">No active leases</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Types Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                <select class="form-select" id="documentTypeFilter">
                                    <option value="">All Document Types</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="documentSearch" placeholder="Search documents...">
                            </div>
                        </div>
                    </div>

                    <!-- Documents Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="documentsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Document Name</th>
                                    <th>Lease</th>
                                    <th>Uploaded</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr data-type="{{ $document->document_type }}">
                                        <td>
        <span class="badge bg-info">
            {{ $document->document_type }}
        </span>
    </td>
                                        <td>
                                            <i class="fas fa-file-pdf text-danger me-1"></i>
                                            {{ $document->original_filename ?? $document->file_name }}
                                        </td>
                                        <td>
                                            @if($document->lease)
                                                <span class="badge bg-secondary">{{ $document->lease->lease_number }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $document->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($document->is_approved)
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($document->is_rejected)
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('account-manager.customers.documents.download', [$customer, $document]) }}"
                                                   class="btn btn-outline-primary" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if(!$document->is_approved && !$document->is_rejected)
                                                    <form action="{{ route('account-manager.documents.approve-single', $document) }}"
                                                          method="POST" class="d-inline" id="approveForm{{ $document->id }}">
                                                        @csrf
                                                        <button type="button"
                                                                class="btn btn-outline-success"
                                                                title="Approve"
                                                                onclick="confirmApprove('{{ $document->id }}')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('account-manager.documents.reject', $document) }}"
                                                          method="POST" class="d-inline" id="rejectForm{{ $document->id }}">
                                                        @csrf
                                                        <button type="button"
                                                                class="btn btn-outline-danger"
                                                                title="Reject"
                                                                onclick="confirmReject('{{ $document->id }}')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('account-manager.customers.documents.destroy', [$customer, $document]) }}"
                                                      method="POST" class="d-inline" id="deleteForm{{ $document->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-outline-danger"
                                                            title="Delete"
                                                            onclick="confirmDelete('{{ $document->id }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-folder-open fa-2x mb-3"></i>
                                                <h5>No documents found</h5>
                                                <p>Upload documents using the button above</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($documents->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $documents->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Document type filter
    document.getElementById('documentTypeFilter').addEventListener('change', function() {
        const typeId = this.value;
        const rows = document.querySelectorAll('#documentsTable tbody tr');

        rows.forEach(row => {
            if (!typeId || row.getAttribute('data-type') == typeId) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Document search
    document.getElementById('documentSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#documentsTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Confirmation functions
    function confirmApprove(documentId) {
        if (confirm('Are you sure you want to approve this document?')) {
            document.getElementById('approveForm' + documentId).submit();
        }
    }

    function confirmReject(documentId) {
        if (confirm('Are you sure you want to reject this document?')) {
            document.getElementById('rejectForm' + documentId).submit();
        }
    }

    function confirmDelete(documentId) {
        if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
            document.getElementById('deleteForm' + documentId).submit();
        }
    }
</script>
@endpush

@push('styles')
<style>
    .card {
        border: 1px solid #e0e0e0;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
    }
    .table th {
        background-color: #f8f9fa;
        border-top: none;
    }
    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush
