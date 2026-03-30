{{-- resources/views/customer/quotations/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Quotations</h2>
                <div>
                    <a href="{{ route('customer.contracts.index') }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-file-contract me-1"></i> View Contracts
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Home
                    </a>
                </div>
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
                                        <th>Contract Status</th>
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
                                            <span class="badge bg-{{ $quotation->getStatusBadgeColor() }}">
                                                {{ $quotation->getStatusDisplayText() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($quotation->contract)
                                                <span class="badge bg-{{ $quotation->contract->getStatusBadgeColor() }}">
                                                    {{ $quotation->contract->getStatusDisplayText() }}
                                                </span>
                                                @if($quotation->contract->status == 'approved')
                                                    <br>
                                                    <small class="text-success">
                                                        <i class="fas fa-check me-1"></i>Work Ready
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">No Contract</span>
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

                                                @if($quotation->canBeApprovedByCustomer())
                                                    <button type="button"
                                                            class="btn btn-outline-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $quotation->id }}"
                                                            title="Approve Quotation">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif

                                                @if($quotation->canBeRejectedByCustomer())
                                                    <button type="button"
                                                            class="btn btn-outline-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $quotation->id }}"
                                                            title="Reject Quotation">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Contract Actions -->
                                            @if($quotation->contract)
                                                <div class="mt-1">
                                                    <a href="{{ route('customer.contracts.show', $quotation->contract) }}"
                                                       class="btn btn-sm btn-outline-info"
                                                       title="View Contract">
                                                        <i class="fas fa-file-contract me-1"></i>Contract
                                                    </a>
                                                </div>
                                            @endif

                                            <!-- Download Button for Approved/Sent Quotations -->
                                            @if(in_array($quotation->status, ['sent', 'approved']))
                                                <a href="{{ route('customer.quotations.download', $quotation) }}"
                                                   class="btn btn-sm btn-outline-dark mt-1"
                                                   title="Download PDF">
                                                    <i class="fas fa-download me-1"></i>PDF
                                                </a>
                                            @endif
                                        </td>
                                     </tr>

                                     <!-- Approve Modal - Updated for Contract Generation -->
                                     @if($quotation->canBeApprovedByCustomer())
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
                                                        <strong>Quotation #:</strong> {{ $quotation->quotation_number }}<br>
                                                        <strong>Amount:</strong> {{ $quotation->formatted_total_amount }}<br>
                                                        <strong>Valid Until:</strong> {{ $quotation->valid_until->format('M j, Y') }}
                                                    </div>

                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>Important:</strong> By approving this quotation, You will need to review and approve the contract separately.
                                                    </div>

                                                    <p class="text-muted">By approving, you agree to the terms and conditions outlined in the quotation.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                  <!-- Replace your current approve button section with this: -->
                                    @if($quotation->customer_approval_status === 'pending' && $quotation->status === 'sent')
                                        <form action="{{ route('customer.quotations.approve', $quotation) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">Approve Quotation</button>
                                        </form>

                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $quotation->id }}">
                                            Reject
                                        </button>
                                    @elseif($quotation->customer_approval_status === 'approved')
                                        <span class="badge bg-success me-2">Approved</span>
                                        <small class="text-muted">
                                            {{ $quotation->customer_approved_at->format('M d, Y') }}
                                        </small>
                                    @elseif($quotation->customer_approval_status === 'rejected')
                                        <span class="badge bg-danger me-2">Rejected</span>
                                        <small class="text-muted">
                                            {{ $quotation->customer_rejected_at->format('M d, Y') }}
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Reject Modal -->
                                    @if($quotation->canBeRejectedByCustomer())
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
                                                        @method('PATCH')
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

<!-- Quick Action Buttons for Multiple Items -->
@if($quotations->where('status', 'sent')->count() > 0 || $quotations->where('contract.status', 'pending_approval')->count() > 0)
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-primary text-white">
            <strong class="me-auto">
                <i class="fas fa-bell me-2"></i>Action Required
            </strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            @php
                $pendingQuotations = $quotations->where('status', 'sent')->count();
                $pendingContracts = $quotations->where('contract.status', 'pending_approval')->count();
            @endphp

            @if($pendingQuotations > 0)
                <p class="mb-1">You have {{ $pendingQuotations }} quotation(s) pending review.</p>
            @endif

            @if($pendingContracts > 0)
                <p class="mb-1">You have {{ $pendingContracts }} contract(s) pending approval.</p>
            @endif

            <div class="mt-2 pt-2 border-top">
                @if($pendingQuotations > 0)
                    <a href="#pending-quotations" class="btn btn-sm btn-outline-primary me-1">
                        <i class="fas fa-file-invoice me-1"></i>Review Quotations
                    </a>
                @endif
                @if($pendingContracts > 0)
                    <a href="{{ route('customer.contracts.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-file-contract me-1"></i>Review Contracts
                    </a>
                @endif
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
}

.contract-status-badge {
    font-size: 0.7rem;
    padding: 0.25em 0.4em;
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

    // Add smooth scrolling for the review now button
    document.querySelector('a[href="#pending-quotations"]')?.addEventListener('click', function(e) {
        e.preventDefault();
        const firstPending = document.querySelector('tr:has(.bg-warning)');
        if (firstPending) {
            firstPending.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstPending.classList.add('highlight-row');
            setTimeout(() => firstPending.classList.remove('highlight-row'), 2000);
        }
    });
});

// Add highlight animation
const style = document.createElement('style');
style.textContent = `
    .highlight-row {
        background-color: rgba(255, 193, 7, 0.2) !important;
        transition: background-color 2s ease;
    }

    .contract-ready {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
`;
document.head.appendChild(style);
</script>
@endsection
