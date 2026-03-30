@extends('layouts.app')

@section('title', 'ICT Engineer - Design Requests')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-network-wired text-primary"></i> ICT Engineer - Design Requests
                    </h1>
                    <p class="text-muted">Manage fibre route design requests assigned to you for technical review</p>
                </div>
                <a href="{{ route('ictengineer.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list-check me-2 text-primary"></i>Technical Review Requests
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('ictengineer.dashboard') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="goBack()">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fs-5"></i>
                        <div>
                            <strong>You have {{ $requests->count() }} design request(s) for technical review</strong>
                            <p class="mb-0 small">Review designs, generate certificates, and manage technical approvals.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Request #</th>
                            <th>Customer</th>
                            <th>Presale Engineer</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Assigned To You</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td><strong>#{{ $request->request_number ?? $request->id }}</strong></td>
                                <td>{{ $request->customer->name ?? 'N/A' }}</td>
                                <td>{{ $request->designer->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($request->title ?? 'No Title', 40) }}</td>
                                <td>
                                    <span class="badge bg-{{ App\Helpers\StatusHelper::getStatusBadgeColor($request->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td>{{ $request->ict_assigned_at?->format('M d, Y') ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- View Design Request Details -->
                                        <a href="{{ route('ictengineer.requests.show', $request) }}"
                                           class="btn btn-primary"
                                           title="View Design Request">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                  <!-- Conditional Certificate Actions -->
<div class="d-flex gap-2 flex-wrap">
   <!-- Conditional Certificate Actions -->
@if(in_array($request->status, ['assigned', 'under_technical_review', 'conditional_certificate_issued','acceptance_certificate_issued']))
       @if($request->conditionalCertificate || $request->conditional_certificate_id)
        <!-- View Conditional Certificate -->
        <a href="{{ route('ictengineer.certificates.conditional.show', $request->conditionalCertificate ?? $request->conditional_certificate_id) }}"
           target="_blank"
           class="btn btn-info me-2"
           title="View Conditional Certificate">
            <i class="fas fa-file-pdf"></i>
        </a>

        <!-- Download Conditional Certificate -->
        <a href="{{ route('ictengineer.certificates.conditional.download', $request->conditionalCertificate ?? $request->conditional_certificate_id) }}"
           class="btn btn-outline-info me-2"
           title="Download Conditional Certificate">
            <i class="fas fa-download"></i>
        </a>
    @else
        <!-- Generate Conditional Certificate -->
        <a href="{{ route('ictengineer.certificates.conditional.create', $request) }}"
           class="btn btn-success me-2"
           title="Generate Conditional Certificate">
            <i class="fas fa-file-contract"></i>
        </a>
    @endif
@endif

    <!-- Acceptance Certificate Actions -->
    @if(in_array($request->status, ['approved', 'ready_for_acceptance','acceptance_certificate_issued']))

        <!-- View Acceptance Certificate (if exists) -->
        @if($request->acceptance_certificate || $request->acceptance_certificate_id)
            <a href="{{ route('ictengineer.certificates.acceptance.show', $request->acceptance_certificate ?? $request->acceptance_certificate_id) }}"
               target="_blank"
               class="btn btn-secondary"
               title="View Acceptance Certificate">
                <i class="fas fa-file-contract"></i>
            </a>

            <!-- Download Acceptance Certificate -->
            <a href="{{ route('ictengineer.certificates.acceptance.download', $request->acceptance_certificate ?? $request->acceptance_certificate_id) }}"
               class="btn btn-outline-secondary"
               title="Download Acceptance Certificate">
                <i class="fas fa-download"></i>
            </a>

        @else
        <!-- Generate Acceptance Certificate -->
        <a href="{{ route('ictengineer.certificates.acceptance.create', $request) }}"
           class="btn btn-warning"
           title="Generate Acceptance Certificate">
            <i class="fas fa-file-signature"></i>
        </a>

        @endif
    @endif
</div>

                                        <!-- View Quotation (if exists) -->
                                        @php
                                            $existingQuotation = \App\Models\Quotation::where('design_request_id', $request->id)->first();
                                        @endphp
                                        @if($existingQuotation)
                                            <a href="{{ route('ictengineer.quotations.show', $existingQuotation->id) }}"
                                               target="_blank"
                                               class="btn btn-dark"
                                               title="View Quotation">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </a>
                                        @endif

                                        <!-- Update Technical Status -->
                                        <button type="button"
                                                class="btn btn-outline-primary update-technical-status-btn"
                                                data-request-id="{{ $request->id }}"
                                                data-current-status="{{ $request->status }}"
                                                title="Update Technical Status">
                                            <i class="fas fa-cogs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-network-wired fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No design requests assigned for technical review.</p>
                                    <p class="text-muted small">Design requests will appear here when assigned by administrators or designers.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('ictengineer.dashboard') }}" class="btn btn-primary me-2">
                                            <i class="fas fa-tachometer-alt me-1"></i>Go to Dashboard
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                                            <i class="fas fa-arrow-left me-1"></i>Back to Previous
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->count() > 0)
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div class="text-muted">
                        Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} requests
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                            <i class="fas fa-arrow-left me-2"></i>Back to Previous
                        </button>
                        {{ $requests->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Technical Status Update Modal -->
<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Technical Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Technical Status</label>
                        <select class="form-select" id="technical_status" name="technical_status" required>
                            <option value="under_technical_review">Under Technical Review</option>
                            <option value="technically_approved">Technically Approved</option>
                            <option value="technical_revisions_required">Technical Revisions Required</option>
                            <option value="ready_for_acceptance">Ready for Acceptance</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Technical Notes</label>
                        <textarea class="form-control" id="technical_notes" name="technical_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTechnicalStatus"
                        data-update-url-template="{{ route('ictengineer.requests.update-status', ['id' => 'PLACEHOLDER']) }}">
                    Update Status
                </button>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = "{{ route('ictengineer.dashboard') }}";
    }
}


// Technical status update functionality
document.addEventListener('DOMContentLoaded', function() {
    const technicalStatusModal = new bootstrap.Modal(document.getElementById('technicalStatusModal'));
    const updateTechnicalStatusBtns = document.querySelectorAll('.update-technical-status-btn');
    const saveTechnicalStatusBtn = document.getElementById('saveTechnicalStatus');
    const baseUpdateUrl = saveTechnicalStatusBtn.getAttribute('data-update-url');

    updateTechnicalStatusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const currentStatus = this.getAttribute('data-current-status');

            document.getElementById('technicalRequestId').value = requestId;

            // Set current status as selected option
            const statusSelect = document.getElementById('technical_status');
            statusSelect.value = currentStatus;

            // Update the save button URL with the actual request ID
            const actualUrl = baseUpdateUrl.replace('REPLACE_ID', requestId);
            saveTechnicalStatusBtn.setAttribute('data-actual-url', actualUrl);

            technicalStatusModal.show();
        });
    });

    // Save technical status
   // Change this part in your script section
saveTechnicalStatusBtn.addEventListener('click', function() {
    const form = document.getElementById('technicalStatusForm');
    const formData = new FormData(form);
    const requestId = document.getElementById('technicalRequestId').value;

    // Build the URL correctly
    const url = `/ictengineer/requests/${requestId}/update-status`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            technicalStatusModal.hide();
            showToast('Technical status updated successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error updating technical status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating technical status. Please try again.', 'error');
    });
});

    // Keyboard shortcuts
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            goBack();
        }
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        // Implement toast notification or use alert
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>
@endpush

@push('styles')
<style>
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.25rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.03);
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

/* Button colors for different actions */
.btn-primary { background-color: #0d6efd; }
.btn-success { background-color: #198754; }
.btn-info { background-color: #0dcaf0; }
.btn-warning { background-color: #ffc107; }
.btn-secondary { background-color: #6c757d; }
.btn-dark { background-color: #212529; }
.btn-outline-primary { color: #0d6efd; border-color: #0d6efd; }

.btn-primary:hover { background-color: #0b5ed7; }
.btn-success:hover { background-color: #157347; }
.btn-info:hover { background-color: #0ba5d1; }
.btn-warning:hover { background-color: #e0a800; }

/* Action button tooltips */
.btn-group .btn {
    position: relative;
}

.btn-group .btn:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start !important;
    }

    .btn-group {
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .btn-group .btn {
        margin-bottom: 0.25rem;
        flex: 1;
        min-width: 40px;
    }

    .table-responsive {
        font-size: 0.875rem;
    }
}

/* External link indicator for new window links */
.btn[target="_blank"] i {
    position: relative;
}

.btn[target="_blank"] i::after {
    content: " ↗";
    font-size: 0.7em;
    position: relative;
    top: -1px;
}
</style>
@endpush
