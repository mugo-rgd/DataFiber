{{-- resources/views/customer/quotations/show.blade.php --}}
@if($quotation->isPendingCustomerApproval())
<div class="border-top pt-4">
    <h5>Approve Quotation</h5>
    <p class="text-muted">
        By approving this quotation, you confirm that you accept the terms, pricing, and timeline outlined above.
    </p>

    <form action="{{ route('customer.quotations.approve', $quotation) }}"
          method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success btn-lg"
                onclick="return confirm('Are you sure you want to approve this quotation? This action cannot be undone.')">
            <i class="fas fa-check-circle me-2"></i> Approve Quotation
        </button>
    </form>

    <button type="button" class="btn btn-danger ms-2"
            data-bs-toggle="modal" data-bs-target="#rejectModal">
        <i class="fas fa-times me-2"></i> Reject Quotation
    </button>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.quotations.reject', $quotation) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this quotation:</p>
                    <textarea name="rejection_reason" class="form-control" rows="4"
                              placeholder="Enter your reason for rejection..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Submit Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@elseif($quotation->isApprovedByCustomer())
<div class="alert alert-success mt-3">
    <div class="d-flex align-items-center">
        <i class="fas fa-check-circle fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading mb-1">Quotation Approved!</h5>
            <p class="mb-0">You approved this quotation on {{ $quotation->customer_approved_at->format('F j, Y \a\t g:i A') }}. Our team has been notified and will begin work shortly.</p>
        </div>
    </div>
</div>
@elseif($quotation->isRejectedByCustomer())
<div class="alert alert-danger mt-3">
    <div class="d-flex align-items-center">
        <i class="fas fa-times-circle fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading mb-1">Quotation Rejected</h5>
            <p class="mb-0">You rejected this quotation on {{ $quotation->customer_rejected_at->format('F j, Y \a\t g:i A') }}.</p>
            @if($quotation->rejection_reason)
                <p class="mb-0 mt-2"><strong>Reason:</strong> {{ $quotation->rejection_reason }}</p>
            @endif
        </div>
    </div>
</div>
@endif
