@extends('layouts.app')

@section('title', 'ICT Engineer - Design Requests')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-network-wired text-kp-blue"></i> ICT Engineer - Design Requests
                    </h1>
                    <p class="text-muted">Manage fibre route design requests assigned to you for technical review</p>
                </div>
                <a href="{{ route('ictengineer.dashboard') }}" class="btn btn-outline-kp-primary">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list-check me-2 text-kp-blue"></i>Technical Review Requests
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('ictengineer.dashboard') }}" class="btn btn-sm btn-outline-kp-primary">
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
                                    <span class="badge bg-{{ App\Helpers\StatusHelper::getStatusBadgeColor($request->technical_status ?? $request->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->technical_status ?? $request->status)) }}
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
                                        @if(in_array($request->technical_status ?? $request->status, ['assigned', 'under_technical_review', 'conditional_certificate_issued', 'acceptance_certificate_issued']))
                                            @if($request->conditionalCertificate || $request->conditional_certificate_id)
                                                <a href="{{ route('ictengineer.certificates.conditional.show', $request->conditionalCertificate ?? $request->conditional_certificate_id) }}"
                                                   target="_blank"
                                                   class="btn btn-info"
                                                   title="View Conditional Certificate">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('ictengineer.certificates.conditional.download', $request->conditionalCertificate ?? $request->conditional_certificate_id) }}"
                                                   class="btn btn-outline-info"
                                                   title="Download Conditional Certificate">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('ictengineer.certificates.conditional.create', $request) }}"
                                                   class="btn btn-success"
                                                   title="Generate Conditional Certificate">
                                                    <i class="fas fa-file-contract"></i>
                                                </a>
                                            @endif
                                        @endif

                                        <!-- Acceptance Certificate Actions -->
                                        @if(in_array($request->technical_status ?? $request->status, ['approved', 'ready_for_acceptance', 'acceptance_certificate_issued']))
                                            @if($request->acceptance_certificate || $request->acceptance_certificate_id)
                                                <a href="{{ route('ictengineer.certificates.acceptance.show', $request->acceptance_certificate ?? $request->acceptance_certificate_id) }}"
                                                   target="_blank"
                                                   class="btn btn-secondary"
                                                   title="View Acceptance Certificate">
                                                    <i class="fas fa-file-contract"></i>
                                                </a>
                                                <a href="{{ route('ictengineer.certificates.acceptance.download', $request->acceptance_certificate ?? $request->acceptance_certificate_id) }}"
                                                   class="btn btn-outline-secondary"
                                                   title="Download Acceptance Certificate">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('ictengineer.certificates.acceptance.create', $request) }}"
                                                   class="btn btn-warning"
                                                   title="Generate Acceptance Certificate">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @endif
                                        @endif

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

                                        <!-- Update Technical Status Button - FIXED -->
                                        <button type="button"
                                                class="btn btn-outline-primary update-technical-status-btn"
                                                data-request-id="{{ $request->id }}"
                                                data-current-status="{{ $request->technical_status ?? $request->status }}"
                                                data-request-number="{{ $request->request_number }}"
                                                title="Update Technical Status">
                                            <i class="fas fa-cogs"></i>
                                        </button>
                                    </div>
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
<div class="modal fade" id="technicalStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cogs me-2"></i>Update Technical Status
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="technicalStatusForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="request_id" id="technicalRequestId">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Request Number</label>
                        <p class="form-control-plaintext" id="requestNumberDisplay"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Technical Status</label>
                        <select class="form-select" id="technical_status" name="technical_status" required>
                            <option value="under_technical_review">🔍 Under Technical Review</option>
                            <option value="technically_approved">✅ Technically Approved</option>
                            <option value="technical_revisions_required">✏️ Technical Revisions Required</option>
                            <option value="ready_for_acceptance">📋 Ready for Acceptance</option>
                            <option value="accepted">✔️ Accepted</option>
                            <option value="rejected">❌ Rejected</option>
                        </select>
                        <small class="text-muted">Select the appropriate technical review status</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Technical Notes</label>
                        <textarea class="form-control" id="technical_notes" name="technical_notes" rows="4"
                                  placeholder="Add detailed notes about the technical review..."></textarea>
                        <small class="text-muted">These notes will be visible to the design team</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveTechnicalStatus">
                    <i class="fas fa-save me-1"></i>Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>
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

document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal
    let technicalStatusModal = null;
    try {
        technicalStatusModal = new bootstrap.Modal(document.getElementById('technicalStatusModal'));
    } catch(e) {
        console.error('Modal initialization error:', e);
    }

    // Get all update status buttons
    const updateTechnicalStatusBtns = document.querySelectorAll('.update-technical-status-btn');

    // Get form elements
    const technicalRequestId = document.getElementById('technicalRequestId');
    const requestNumberDisplay = document.getElementById('requestNumberDisplay');
    const technicalStatusSelect = document.getElementById('technical_status');
    const technicalNotes = document.getElementById('technical_notes');
    const saveTechnicalStatusBtn = document.getElementById('saveTechnicalStatus');

    // Add click event to each update button
    updateTechnicalStatusBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const requestId = this.getAttribute('data-request-id');
            const currentStatus = this.getAttribute('data-current-status');
            const requestNumber = this.getAttribute('data-request-number') || 'N/A';

            console.log('Opening modal for request:', requestId, 'Current status:', currentStatus);

            // Set values in modal
            if (technicalRequestId) technicalRequestId.value = requestId;
            if (requestNumberDisplay) requestNumberDisplay.textContent = requestNumber;

            // Set current status in select dropdown
            if (technicalStatusSelect && currentStatus) {
                technicalStatusSelect.value = currentStatus;
            }

            // Clear notes field
            if (technicalNotes) technicalNotes.value = '';

            // Show modal
            if (technicalStatusModal) technicalStatusModal.show();
        });
    });

    // Save technical status
    if (saveTechnicalStatusBtn) {
        saveTechnicalStatusBtn.addEventListener('click', function() {
            const requestId = technicalRequestId?.value;

            if (!requestId) {
                showToast('Request ID not found', 'error');
                return;
            }

            const form = document.getElementById('technicalStatusForm');
            const formData = new FormData(form);

            // Add CSRF token
            formData.append('_token', '{{ csrf_token() }}');

            // Build the URL
            const url = `/ictengineer/requests/${requestId}/update-status`;

            console.log('Sending request to:', url);
            console.log('Form data:', Object.fromEntries(formData));

            // Disable button and show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    if (technicalStatusModal) technicalStatusModal.hide();
                    showToast(data.message || 'Technical status updated successfully!', 'success');

                    // Reload after a short delay
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Error updating technical status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Error updating technical status. Please try again.', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save me-1"></i>Update Status';
            });
        });
    }

    // Toast notification function
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 mb-2`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 5000 });
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    // Keyboard shortcut: ESC to go back
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (technicalStatusModal && document.getElementById('technicalStatusModal')?.classList.contains('show')) {
                technicalStatusModal.hide();
            } else {
                goBack();
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
:root {
    --kp-blue: #0d6efd;
    --kp-green: #198754;
    --kp-yellow: #ffc107;
}

.btn-group-sm > .btn {
    padding: 0.3rem 0.6rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.03);
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

/* Button styles */
.btn-primary { background-color: var(--kp-blue); border-color: var(--kp-blue); }
.btn-success { background-color: var(--kp-green); border-color: var(--kp-green); }
.btn-warning { background-color: var(--kp-yellow); border-color: var(--kp-yellow); color: #000; }
.btn-info { background-color: #0dcaf0; border-color: #0dcaf0; color: #000; }
.btn-dark { background-color: #212529; border-color: #212529; }
.btn-outline-secondary:hover { background-color: #6c757d; color: white; }

.btn-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
.btn-success:hover { background-color: #157347; border-color: #146c43; }
.btn-warning:hover { background-color: #e0a800; border-color: #d39e00; }
.btn-info:hover { background-color: #0ba5d1; border-color: #0a9ac2; }

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
    margin-bottom: 5px;
}

/* Modal animation */
.modal-content {
    border-radius: 12px;
    overflow: hidden;
}

.modal-header {
    border-bottom: none;
    padding: 1rem 1.5rem;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

/* Form styles */
.form-control:focus, .form-select:focus {
    border-color: var(--kp-blue);
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Toast styles */
.toast {
    min-width: 300px;
    border-radius: 8px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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

    .table td, .table th {
        padding: 0.5rem;
    }
}

/* External link indicator */
.btn[target="_blank"] i {
    position: relative;
}
</style>
@endpush
