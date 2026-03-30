@extends('layouts.app')

@section('title', 'Create Payment Followup')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create Payment Followup</h1>
                <a href="{{ route('finance.payments.followups') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Followups
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('finance.payments.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                    <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="billing_id" class="form-label">Billing/Invoice (Optional)</label>
                                    <select name="billing_id" id="billing_id" class="form-select @error('billing_id') is-invalid @enderror">
                                        <option value="">No Specific Invoice</option>
                                        <!-- This will be populated via AJAX when customer is selected -->
                                    </select>
                                    @error('billing_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">KES</span>
                                        <input type="number" step="0.01" name="amount" id="amount"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               value="{{ old('amount') }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" id="due_date"
                                           class="form-control @error('due_date') is-invalid @enderror"
                                           value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes / Description</label>
                                    <textarea name="notes" id="notes" rows="4"
                                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Followup
                                </button>
                                <a href="{{ route('finance.payments.followups') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('customer_id').addEventListener('change', function() {
    const customerId = this.value;
    const billingSelect = document.getElementById('billing_id');

    if (!customerId) {
        billingSelect.innerHTML = '<option value="">No Specific Invoice</option>';
        return;
    }

    // Clear current options
    billingSelect.innerHTML = '<option value="">Loading...</option>';

    // Fetch customer's unpaid billings
    fetch(`/api/customers/${customerId}/unpaid-billings`)
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">No Specific Invoice</option>';

            if (data.success && data.data.length > 0) {
                data.data.forEach(billing => {
                    options += `<option value="${billing.id}">${billing.billing_number} - KES ${parseFloat(billing.amount).toFixed(2)} - Due: ${billing.due_date}</option>`;
                });
            }

            billingSelect.innerHTML = options;
        })
        .catch(error => {
            console.error('Error:', error);
            billingSelect.innerHTML = '<option value="">Error loading invoices</option>';
        });
});
</script>
@endpush
