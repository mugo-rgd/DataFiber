@extends('layouts.app')

@section('title', 'Create Quotation')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-invoice-dollar text-success"></i> Create Quotation
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('designer.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('designer.requests') }}">Design Requests</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('designer.requests.show', $designRequest) }}">#{{ $designRequest->request_number }}</a></li>
                    <li class="breadcrumb-item active">Create Quotation</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-receipt text-success"></i> Quotation Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('designer.quotations.store', $designRequest) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Design Request</label>
                                    <input type="text" class="form-control" value="#{{ $designRequest->request_number }} - {{ $designRequest->title }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Customer</label>
                                    <input type="text" class="form-control" value="{{ $designRequest->customer->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Base Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="amount" name="amount"
                                               step="0.01" min="0" value="{{ old('amount', $designRequest->estimated_cost) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tax_amount" class="form-label">Tax Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="tax_amount" name="tax_amount"
                                               step="0.01" min="0" value="{{ old('tax_amount', 0) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Total Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" id="total_amount" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="scope_of_work" class="form-label">Scope of Work *</label>
                            <textarea class="form-control" id="scope_of_work" name="scope_of_work" rows="6" required>{{ old('scope_of_work') }}</textarea>
                            <div class="form-text">Detailed description of the work to be performed</div>
                        </div>

                        <div class="mb-3">
                            <label for="terms_and_conditions" class="form-label">Terms & Conditions *</label>
                            <textarea class="form-control" id="terms_and_conditions" name="terms_and_conditions" rows="4" required>{{ old('terms_and_conditions') }}</textarea>
                            <div class="form-text">Payment terms, delivery schedule, and other conditions</div>
                        </div>

                        <div class="mb-3">
                            <label for="valid_until" class="form-label">Quotation Valid Until *</label>
                            <input type="date" class="form-control" id="valid_until" name="valid_until"
                                   value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            <div class="form-text">Date until which this quotation is valid</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Send Quotation
                            </button>
                            <a href="{{ route('designer.requests.show', $designRequest) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Design Summary -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-drafting-compass text-primary"></i> Design Summary</h5>
                </div>
                <div class="card-body">
                    <h6>Design Specifications:</h6>
                    <p class="text-muted small">{{ Str::limit($designRequest->design_specifications, 150) }}</p>

                    <h6>Estimated Cost:</h6>
                    <p class="text-success">${{ number_format($designRequest->estimated_cost, 2) }}</p>

                    <h6>Design Notes:</h6>
                    <p class="text-muted small">{{ $designRequest->design_notes ?: 'No additional notes' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const taxInput = document.getElementById('tax_amount');
    const totalInput = document.getElementById('total_amount');

    function calculateTotal() {
        const amount = parseFloat(amountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;
        const total = amount + tax;
        totalInput.value = total.toFixed(2);
    }

    amountInput.addEventListener('input', calculateTotal);
    taxInput.addEventListener('input', calculateTotal);

    // Initial calculation
    calculateTotal();
});
</script>
@endsection
