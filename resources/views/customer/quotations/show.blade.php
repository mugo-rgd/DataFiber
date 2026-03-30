{{-- resources/views/customer/quotations/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quotation Details</h2>
                <div>
                    <a href="{{ route('customer.quotations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to My Quotations
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Quotation #{{ $quotation->quotation_number ?? $quotation->id }}</h4>
                    <div>
                        @if($quotation->status === 'sent')
                            <span class="badge bg-warning">Awaiting Approval</span>
                        @elseif($quotation->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($quotation->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($quotation->status) }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Design Request Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Design Request Information</h5>
                            @if($quotation->designRequest)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Request Number:</label>
                                    <p class="fw-bold">{{ $quotation->designRequest->request_number ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Title:</label>
                                    <p class="fw-bold">{{ $quotation->designRequest->title }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Description:</label>
                                    <p class="text-muted">{{ $quotation->designRequest->description }}</p>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>No design request associated
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Quotation Details</h5>
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Status:</label>
                                <p>
                                    <span class="badge bg-{{ $quotation->status === 'sent' ? 'warning' : ($quotation->status === 'approved' ? 'success' : ($quotation->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                        {{ ucfirst($quotation->status) }}
                                    </span>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small mb-1">Your Approval:</label>
                                @if($quotation->customer_approval_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($quotation->customer_approval_status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small mb-1">Valid Until:</label>
                                <p class="fw-bold {{ $quotation->valid_until < now() ? 'text-danger' : 'text-success' }}">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $quotation->valid_until->format('F j, Y') }}
                                </p>
                            </div>

                            @if($quotation->customer_approved_at)
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Approved Date:</label>
                                <p class="text-success">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    {{ $quotation->customer_approved_at->format('F j, Y \a\t g:i A') }}
                                </p>
                            </div>
                            @endif

                            @if($quotation->customer_rejected_at)
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Rejected Date:</label>
                                <p class="text-danger">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    {{ $quotation->customer_rejected_at->format('F j, Y \a\t g:i A') }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Financial Summary -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3">Financial Summary</h5>
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted">Subtotal</h6>
                                            <h4 class="text-primary">${{ number_format($quotation->subtotal, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted">Tax ({{ $quotation->tax_rate * 100 }}%)</h6>
                                            <h4 class="text-info">${{ number_format($quotation->tax_amount, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted">Total Amount</h6>
                                            <h4 class="text-success">${{ number_format($quotation->total_amount, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted">Valid Until</h6>
                                            <h6 class="text-warning">{{ $quotation->valid_until->format('M d, Y') }}</h6>
                                            <small class="text-muted">{{ $quotation->valid_until->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($quotation->scope_of_work)
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Scope of Work</h5>
                        <div class="border rounded p-4 bg-light">
                            <div style="white-space: pre-line;">{{ $quotation->scope_of_work }}</div>
                        </div>
                    </div>
                    @endif

                    @if($quotation->customer_notes)
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Additional Notes</h5>
                        <div class="border rounded p-4 bg-light">
                            <div style="white-space: pre-line;">{{ $quotation->customer_notes }}</div>
                        </div>
                    </div>
                    @endif

                    @if($quotation->rejection_reason)
                    <div class="mb-4">
                        <div class="alert alert-danger">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Rejection Reason</h5>
                            <div style="white-space: pre-line;">{{ $quotation->rejection_reason }}</div>
                        </div>
                    </div>
                    @endif

                    <!-- Terms and Conditions -->
                    @if($quotation->terms_and_conditions)
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Terms and Conditions</h5>
                        <div class="border rounded p-4 bg-light">
                            <div style="white-space: pre-line; font-size: 0.9rem;">{{ $quotation->terms_and_conditions }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Add PDF buttons in the card footer -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($quotation->status === 'sent' && $quotation->customer_approval_status === 'pending')
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Please review and approve or reject this quotation
                                </small>
                            @endif
                        </div>
                        <div class="d-flex">
                            <a href="{{ route('quotations.pdf.preview', $quotation) }}"
                               class="btn btn-info me-2" target="_blank">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </a>
                            <a href="{{ route('quotations.pdf.download', $quotation) }}"
                               class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Actions -->
            @if($quotation->canBeApprovedByCustomer())
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Approve Quotation</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        By approving this quotation, you confirm that you accept all terms, pricing, and timeline outlined above.
                    </div>

                    <div class="d-flex justify-content-start">
                        {{-- Approval Form - POST method only --}}
                        <form action="{{ route('customer.quotations.approve', $quotation) }}"
                              method="POST" class="d-inline me-3">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg"
                                    onclick="return confirm('Are you sure you want to approve this quotation? This action cannot be undone.')">
                                <i class="fas fa-check-circle me-2"></i> Approve Quotation
                            </button>
                        </form>

                        <button type="button" class="btn btn-outline-danger btn-lg"
                                data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-2"></i> Reject Quotation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Rejection Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Reject Quotation</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        {{-- Rejection Form - POST method only --}}
                        <form action="{{ route('customer.quotations.reject', $quotation) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Please provide a reason for rejecting this quotation. This will help us improve our service.
                                </div>
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                    <textarea name="rejection_reason" class="form-control" rows="4"
                                              placeholder="Enter your reason for rejection..." required></textarea>
                                    <div class="form-text">This information will be shared with our team.</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Submit Rejection</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @elseif($quotation->customer_approval_status === 'approved')
            <div class="alert alert-success mt-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Quotation Approved!</h5>
                        <p class="mb-0">
                            You approved this quotation on {{ $quotation->customer_approved_at ? $quotation->customer_approved_at->format('F j, Y \a\t g:i A') : 'an unspecified date' }}.
                            Our team has been notified and will begin work shortly.
                        </p>
                        <p class="mb-0 mt-2">
                            <strong>Next Steps:</strong> You will receive a confirmation email with contract details.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($quotation->customer_approval_status === 'rejected')
            <div class="alert alert-danger mt-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-times-circle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Quotation Rejected</h5>
                        <p class="mb-0">
                            You rejected this quotation on {{ $quotation->customer_rejected_at->format('F j, Y \a\t g:i A') }}.
                        </p>
                        @if($quotation->rejection_reason)
                            <p class="mb-0 mt-2"><strong>Reason Provided:</strong> {{ $quotation->rejection_reason }}</p>
                        @endif
                        <p class="mb-0 mt-2">
                            If you would like to discuss alternative options, please contact our sales team.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Additional Information -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Account Manager</h6>
                            @if($quotation->accountManager)
                                <p class="mb-1"><strong>{{ $quotation->accountManager->name }}</strong></p>
                                <p class="text-muted small mb-0">{{ $quotation->accountManager->email }}</p>
                                @if($quotation->accountManager->phone)
                                    <p class="text-muted small mb-0">{{ $quotation->accountManager->phone }}</p>
                                @endif
                            @else
                                <p class="text-muted">Not assigned</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Created Information</h6>
                            <p class="mb-1"><strong>Created:</strong> {{ $quotation->created_at->format('F j, Y \a\t g:i A') }}</p>
                            <p class="mb-0"><strong>Last Updated:</strong> {{ $quotation->updated_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .alert {
        border-left: 4px solid;
    }
    .alert-info {
        border-left-color: #17a2b8;
    }
    .alert-success {
        border-left-color: #28a745;
    }
    .alert-danger {
        border-left-color: #dc3545;
    }
    .alert-warning {
        border-left-color: #ffc107;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-focus on rejection reason textarea when modal opens
    document.getElementById('rejectModal').addEventListener('shown.bs.modal', function () {
        document.querySelector('#rejectModal textarea[name="rejection_reason"]').focus();
    });

    // Confirm rejection
    document.querySelector('#rejectModal form').addEventListener('submit', function(e) {
        const reason = document.querySelector('#rejectModal textarea[name="rejection_reason"]').value.trim();
        if (!reason) {
            e.preventDefault();
            alert('Please provide a reason for rejection.');
            return false;
        }

        if (!confirm('Are you sure you want to reject this quotation? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush
