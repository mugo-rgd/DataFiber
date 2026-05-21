@extends('layouts.app')

@section('title', 'Contract Details')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Contract Details</h1>
            <p class="text-muted mb-0">{{ $contract->contract_number }}</p>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ $contract->contract_number }}</strong>

            <span class="badge bg-{{ $contract->getStatusBadgeColor() }}">
                {{ $contract->getStatusDisplayText() }}
            </span>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <small class="text-muted">Customer</small>
                    <div class="fw-bold">
                        {{ $contract->customer->name ?? $contract->quotation->customer->name ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Account Manager</small>
                    <div class="fw-bold">
                        {{ $contract->accountManager->name ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Quotation</small>
                    <div class="fw-bold">
                        {{ $contract->quotation->quotation_number ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <hr>

            <h5>Contract Content</h5>

            <div class="contract-preview">
    {!! html_entity_decode($contract->contract_content) !!}
</div>
        </div>

        <div class="card-footer d-flex gap-2 flex-wrap">

            @if($contract->pdf_path)
    <a href="{{ asset('storage/' . $contract->pdf_path) }}"
       target="_blank"
       class="btn btn-outline-dark">
        <i class="fas fa-print me-1"></i>Print / Download PDF
    </a>
@endif

            @if($contract->canBeSent())
                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit Draft
                </a>

                <form action="{{ route('contracts.send', $contract) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-paper-plane me-1"></i>Send to Customer
                    </button>
                </form>
            @endif

           @if($contract->canBeApprovedByAdmin())
    <button type="button"
            class="btn btn-success"
            data-bs-toggle="modal"
            data-bs-target="#adminApproveContractModal">
        <i class="fas fa-check me-1"></i>Final Admin Approval
    </button>
@endif

            @if($contract->canBeActivated())
                <form action="{{ route('contracts.activate', $contract) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-bolt me-1"></i>Activate Lease
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>

@if($contract->canBeApprovedByAdmin())
<div class="modal fade" id="adminApproveContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Final Admin Approval
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('contracts.approve', $contract) }}"
                  method="POST"
                  class="contract-action-form">
                @csrf
                @method('PATCH')

                <div class="modal-body">
                    <div class="alert alert-info">
                        Customer has accepted this contract. Approving it will move it to the final approved stage.
                    </div>

                    <label class="form-label">Approval Notes Optional</label>

                    <textarea name="approval_notes"
                              rows="4"
                              class="form-control"
                              placeholder="Add any approval notes..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-success action-submit-btn">
                        <i class="fas fa-check me-1"></i>Approve Contract
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
    document.querySelectorAll('.contract-action-form').forEach(function (form) {
        form.addEventListener('submit', function () {
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
    background: #ffffff;
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
