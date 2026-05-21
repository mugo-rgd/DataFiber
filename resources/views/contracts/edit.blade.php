@extends('layouts.app')

@section('title', 'Edit Contract Draft')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-file-contract text-primary me-2"></i>
                Edit Contract Draft
            </h1>
            <p class="text-muted mb-0">{{ $contract->contract_number }}</p>
        </div>

        <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Contract
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the errors below.</strong>
        </div>
    @endif

    <form action="{{ route('contracts.update', $contract) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="row">

            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>
                            <i class="fas fa-eye me-2"></i>Contract Preview
                        </strong>

                        <span class="badge bg-secondary">Draft</span>
                    </div>

                    <div class="card-body">
                        <div class="contract-preview">
                            {!! $contract->contract_content !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <strong>
                            <i class="fas fa-info-circle me-2"></i>Contract Details
                        </strong>
                    </div>

                    <div class="card-body small">
                        <p><strong>Contract #:</strong><br>{{ $contract->contract_number }}</p>
                        <p><strong>Customer:</strong><br>{{ $contract->customer->name ?? $contract->quotation->customer->name ?? 'N/A' }}</p>
                        <p><strong>Quotation:</strong><br>{{ $contract->quotation->quotation_number ?? 'N/A' }}</p>

                        <p>
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $contract->getStatusBadgeColor() }}">
                                {{ $contract->getStatusDisplayText() }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <strong>
                            <i class="fas fa-edit me-2"></i>Editable Notes
                        </strong>
                    </div>

                    <div class="card-body">
                        <label class="form-label">Special Contract Notes</label>

                        <textarea name="contract_notes"
                                  rows="10"
                                  class="form-control"
                                  placeholder="Add special notes, variations, or internal comments here...">{{ old('contract_notes', $contract->contract_notes ?? '') }}</textarea>

                        <input type="hidden"
                               name="contract_content"
                               value="{{ e($contract->contract_content) }}">

                        <small class="text-muted d-block mt-2">
                            The main SLA template is protected. Use this area for customer-specific notes.
                        </small>
                    </div>

                    <div class="card-footer d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Save Draft
                        </button>

                        <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection

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

.contract-preview html,
.contract-preview body {
    all: unset;
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
