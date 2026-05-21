@extends('layouts.app')

@section('title', 'Contract Details')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Contract Details</h1>
            <p class="text-muted mb-0">{{ $contract->contract_number }}</p>
        </div>

        <a href="{{ route('customer.contracts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Contracts
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-kp-blue text-white d-flex justify-content-between align-items-center">
            <strong>{{ $contract->contract_number }}</strong>

            <span class="badge bg-{{ $contract->getStatusBadgeColor() }}">
                {{ $contract->getStatusDisplayText() }}
            </span>
        </div>

        <div class="card-body">

            <div class="row mb-4">
                <div class="col-md-4">
                    <small class="text-muted">Quotation</small>
                    <div class="fw-bold">
                        {{ $contract->quotation->quotation_number ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Account Manager</small>
                    <div class="fw-bold">
                        {{ $contract->accountManager->name ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Customer Approval</small>
                    <div>
                        @if($contract->customer_approval_status === 'approved')
                            <span class="badge bg-success">Accepted</span>
                        @elseif($contract->customer_approval_status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Contract Content</h5>

           <div class="contract-preview">
    {!! html_entity_decode($contract->contract_content) !!}
</div>

            @if($contract->rejection_reason)
                <div class="alert alert-danger mt-4">
                    <strong>Rejection Reason:</strong><br>
                    {{ $contract->rejection_reason }}
                </div>
            @endif
        </div>

        @if($contract->pdf_path)
    <a href="{{ asset('storage/' . $contract->pdf_path) }}"
       target="_blank"
       class="btn btn-outline-dark">
        <i class="fas fa-print me-1"></i>Print / Download PDF
    </a>
@endif

        @if($contract->canBeApprovedByCustomer())
            <div class="card-footer">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Please review this contract carefully before accepting or rejecting it.
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <form action="{{ route('customer.contracts.approve', $contract) }}"
                          method="POST"
                          class="contract-action-form">
                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="btn btn-success action-submit-btn"
                                onclick="return confirm('Are you sure you want to accept this contract?')">
                            <i class="fas fa-check me-1"></i>Accept Contract
                        </button>
                    </form>

                    <button type="button"
                            class="btn btn-outline-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectContractModal">
                        <i class="fas fa-times me-1"></i>Reject Contract
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

@if($contract->canBeRejectedByCustomer())
    <div class="modal fade" id="rejectContractModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Contract</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('customer.contracts.reject', $contract) }}"
                      method="POST"
                      class="contract-action-form">
                    @csrf
                    @method('PATCH')

                    <div class="modal-body">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="rejection_reason"
                                  rows="4"
                                  class="form-control"
                                  required
                                  placeholder="Please explain why you are rejecting this contract..."></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                                class="btn btn-danger action-submit-btn">
                            Submit Rejection
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rejectModal = document.getElementById('rejectContractModal');

    if (rejectModal) {
        rejectModal.addEventListener('shown.bs.modal', function () {
            const textarea = rejectModal.querySelector('textarea[name="rejection_reason"]');

            if (textarea) {
                textarea.focus();
            }
        });
    }

    document.querySelectorAll('.contract-action-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const textarea = form.querySelector('textarea[name="rejection_reason"]');

            if (textarea && !textarea.value.trim()) {
                event.preventDefault();
                alert('Please provide a rejection reason.');
                return false;
            }

            const button = form.querySelector('.action-submit-btn');

            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.contract-preview {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
    max-height: 850px;
    overflow-y: auto;
    font-size: 0.85rem;
}

.contract-preview table {
    width: 100%;
    border-collapse: collapse;
}

.contract-preview table,
.contract-preview th,
.contract-preview td {
    border: 1px solid #333;
}

.contract-preview th,
.contract-preview td {
    padding: 4px;
}

.contract-preview img {
    max-height: 60px;
}
</style>
@endpush
