{{-- resources/views/customer/quotations/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Quotations</h2>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Home
                </a>
            </div>

            @if($quotations->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    You don't have any quotations yet.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Quotation #</th>
                                        <th>Design Request</th>
                                        <th>Amount</th>
                                        <th>Timeline</th>
                                        <th>Status</th>
                                        <th>Customer Approval</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotations as $quotation)
                                    <tr>
                                        <td>
                                            <strong>{{ $quotation->quotation_number ?? 'Q-' . $quotation->id }}</strong>
                                        </td>
                                        <td>
                                            @if($quotation->designRequest)
                                                {{ $quotation->designRequest->title }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ $quotation->formatted_total_amount ?? '$0.00' }}</strong>
                                        </td>
                                        <td>{{ $quotation->timeline_days ?? 0 }} days</td>
                                        <td>
                                            <span class="badge bg-{{ $quotation->status === 'sent' ? 'warning' : ($quotation->status === 'approved' ? 'success' : ($quotation->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                                {{ ucfirst($quotation->status ?? 'draft') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($quotation->status === 'approved')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Approved
                                                </span>
                                            @elseif($quotation->status === 'rejected')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Rejected
                                                </span>
                                            @elseif($quotation->status === 'sent')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Pending Review
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-file me-1"></i>Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $quotation->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('customer.quotations.show', $quotation) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Quotation Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- Approve Button - Only show for sent quotations --}}
                                                @if($quotation->status === 'sent')
                                                    <button type="button"
                                                            class="btn btn-outline-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $quotation->id }}"
                                                            title="Approve Quotation">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif

                                                {{-- Reject Button - Only show for sent quotations --}}
                                                @if($quotation->status === 'sent')
                                                    <button type="button"
                                                            class="btn btn-outline-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $quotation->id }}"
                                                            title="Reject Quotation">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Approve Modal - Only for sent quotations -->
                                    @if($quotation->status === 'sent')
                                    <div class="modal fade" id="approveModal{{ $quotation->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $quotation->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title" id="approveModalLabel{{ $quotation->id }}">
                                                        <i class="fas fa-check-circle me-2"></i>Approve Quotation
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to approve this quotation?</p>
                                                    <div class="alert alert-info">
                                                        <strong>Quotation #:</strong> {{ $quotation->quotation_number ?? 'Q-' . $quotation->id }}<br>
                                                        <strong>Amount:</strong> {{ $quotation->formatted_total_amount ?? '$0.00' }}<br>
                                                        @if($quotation->valid_until)
                                                            <strong>Valid Until:</strong> {{ $quotation->valid_until->format('M j, Y') }}
                                                        @endif
                                                    </div>
                                                    <p class="text-muted">By approving, you agree to the terms and conditions outlined in the quotation.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('customer.quotations.approve', $quotation) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-check me-1"></i>Confirm Approval
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Reject Modal - Only for sent quotations -->
                                    @if($quotation->status === 'sent')
                                    <div class="modal fade" id="rejectModal{{ $quotation->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $quotation->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="rejectModalLabel{{ $quotation->id }}">
                                                        <i class="fas fa-times-circle me-2"></i>Reject Quotation
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Please provide a reason for rejecting this quotation:</p>
                                                    <form action="{{ route('customer.quotations.reject', $quotation) }}" method="POST" id="rejectForm{{ $quotation->id }}">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="rejection_reason{{ $quotation->id }}" class="form-label">Rejection Reason</label>
                                                            <textarea class="form-control"
                                                                      id="rejection_reason{{ $quotation->id }}"
                                                                      name="rejection_reason"
                                                                      rows="3"
                                                                      placeholder="Please specify why you are rejecting this quotation..."
                                                                      required></textarea>
                                                            <div class="form-text">This feedback will help us improve our service.</div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" form="rejectForm{{ $quotation->id }}" class="btn btn-danger">
                                                        <i class="fas fa-times me-1"></i>Confirm Rejection
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    {{ $quotations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Action Notification -->
@if($quotations->where('status', 'sent')->count() > 0)
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-primary text-white">
            <strong class="me-auto">
                <i class="fas fa-bell me-2"></i>Action Required
            </strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            You have {{ $quotations->where('status', 'sent')->count() }} quotation(s) pending your review.
            <div class="mt-2 pt-2 border-top">
                <small class="text-muted">Click the check or cross icons to approve or reject quotations.</small>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

.modal-header {
    border-bottom: 2px solid rgba(255,255,255,0.1);
}

.toast {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    max-width: 350px;
}
</style>

<script>
// Auto-focus on rejection reason textarea when modal opens
document.addEventListener('DOMContentLoaded', function() {
    const rejectModals = document.querySelectorAll('[id^="rejectModal"]');
    rejectModals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            const textarea = this.querySelector('textarea');
            if (textarea) {
                textarea.focus();
            }
        });
    });
});

// Clear any cached form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Clear form data on page load to prevent stale submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.reset();
    });
});
</script>
@endsection
