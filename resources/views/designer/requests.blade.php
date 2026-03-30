@extends('layouts.app')

@section('title', 'Design Requests')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-drafting-compass text-primary"></i> Design Requests
                    </h1>
                    <p class="text-muted">Manage your assigned fibre route design requests</p>
                </div>
                <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                    <i class="fas fa-arrow-left me-2"></i>Back to Previous
                </button>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list me-2 text-primary"></i>My Design Requests
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="goBack()">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </button>
                    <a href="{{ route('designer.dashboard') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fs-5"></i>
                        <div>
                            <strong>You have {{ $requests->count() }} design request(s)</strong>
                            <p class="mb-0 small">Click "View" to work on a design request or use the back button to return to your previous page.</p>
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
                            <th>Title</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Assigned</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td><strong>#{{ $request->request_number }}</strong></td>
                                <td>{{ $request->customer->name }}</td>
                                <td>{{ Str::limit($request->title, 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ App\Helpers\StatusHelper::getStatusBadgeColor($request->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td>{{ $request->assigned_at?->format('M d, Y') ?? 'Not assigned' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
@php
    $existingQuotation = \App\Models\Quotation::where('design_request_id', $request->id)->first();
@endphp
    <!-- View Request Button - Normal window -->
       {{-- @include('components.certificate-buttons', ['request' => $request]) --}}

 {{-- @include('components.certificate-buttons', ['request' => $designRequest]) --}}
    <!-- View Quotation Button - Opens in New Window -->
    @if($existingQuotation)
        <a href="{{ route('designer.quotations.show', $existingQuotation->id) }}"
           target="_blank"
           rel="noopener noreferrer"
           class="btn btn-warning" title="View Quote">
            <i class="fas fa-list"></i>
        </a>
    @else
        <!-- Show disabled button if no quotation exists -->
        <button class="btn btn-warning" disabled title="No Quote Available">
            <i class="fas fa-list"></i>
        </button>
    @endif

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

    @endif

    <!-- Quotation Actions -->
    @php
        // Pass openInNewWindow parameter to quotation actions if they include quotation links
        $openInNewWindow = true;
    @endphp
    @include('partials.quotation-actions', [
        'request' => $request,
        'openInNewWindow' => $openInNewWindow
    ])

    <!-- Update Status Button - Modal, no link needed -->
    <button type="button" class="btn btn-secondary update-status-btn"
            data-request-id="{{ $request->id }}"
            title="Update Status">
        <i class="fas fa-sync-alt"></i>
    </button>
</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-drafting-compass fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No design requests assigned to you.</p>
                                    <p class="text-muted small">Design requests will appear here when assigned by administrators.</p>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="goBack()">
                                            <i class="fas fa-arrow-left me-1"></i>Back to Previous
                                        </button>
                                        <a href="{{ route('designer.dashboard') }}" class="btn btn-primary">
                                            <i class="fas fa-tachometer-alt me-1"></i>Go to Dashboard
                                        </a>
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

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Request Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    @csrf
                    <input type="hidden" name="request_id" id="requestId">
                    <div class="mb-3">
                        <label for="status" class="form-label">Select Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Choose status...</option>
                            <option value="pending">Pending</option>
                            <option value="assigned">In Design(Assigned)</option>
                            <option value="designed">Designed</option>
                            <option value="quoted">Quoted</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveStatus" data-update-url="{{ route('account-manager.design-requests.update-status', ['designRequest' => 'REPLACE_ID']) }}">
                    Update Status
                </button>
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
        window.location.href = "{{ route('designer.dashboard') }}";
    }
}

// Status update functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    const updateStatusBtns = document.querySelectorAll('.update-status-btn');
    const saveStatusBtn = document.getElementById('saveStatus');
    const baseUpdateUrl = saveStatusBtn.getAttribute('data-update-url');

    updateStatusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            document.getElementById('requestId').value = requestId;

            // Update the save button URL with the actual request ID
            const actualUrl = baseUpdateUrl.replace('REPLACE_ID', requestId);
            saveStatusBtn.setAttribute('data-actual-url', actualUrl);

            statusModal.show();
        });
    });

    // Save status
    saveStatusBtn.addEventListener('click', function() {
        const form = document.getElementById('statusForm');
        const formData = new FormData(form);
        const url = this.getAttribute('data-actual-url');

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusModal.hide();
                // Show success message
                showToast('Status updated successfully!', 'success');
                // Reload after a short delay
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Error updating status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating status', 'error');
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
        // You can implement a toast notification here
        // For now using alert, but consider using a proper toast library
        const bgColor = type === 'success' ? '#d4edda' : '#f8d7da';
        const textColor = type === 'success' ? '#155724' : '#721c24';

        // Simple alert for demonstration
        alert(message);
    }
});
</script>
@endpush

@push('styles')
<style>
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.02);
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start !important;
    }

    .btn-group {
        flex-wrap: wrap;
    }

    .btn-group .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
}

/* Optional: Add a visual indicator for links that open in new windows */
.btn-warning[target="_blank"] {
    position: relative;
}

/* Add a small external link icon to quotation buttons */
.btn-warning[target="_blank"] i::after {
    content: " ↗";
    font-size: 0.7em;
    position: relative;
    top: -1px;
}
</style>
@endpush
