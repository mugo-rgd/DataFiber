@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-edit me-2"></i>Edit Transaction
                </h1>
                <div class="btn-group">
                    <a href="{{ route('finance.transactions.show', $transaction->id) }}" class="btn btn-secondary">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                    <a href="{{ route('finance.transactions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('finance.transactions.update', $transaction->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Transaction Type *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        @foreach($transactionTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('type', $transaction->type) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" id="amount"
                                               name="amount" value="{{ old('amount', $transaction->amount) }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_date" class="form-label">Transaction Date *</label>
                                    <input type="date" class="form-control" id="transaction_date"
                                           name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date) }}" required>
                                    @error('transaction_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ old('status', $transaction->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed" {{ old('status', $transaction->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method *</label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="">Select Method</option>
                                        @foreach($paymentMethods as $value => $label)
                                            <option value="{{ $value }}" {{ old('payment_method', $transaction->payment_method) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $value => $label)
                                            <option value="{{ $value }}" {{ old('category', $transaction->category) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer (Optional)</label>
                                    <select class="form-select" id="customer_id" name="customer_id">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id', $transaction->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="billing_id" class="form-label">Related Billing (Optional)</label>
                                    <select class="form-select" id="billing_id" name="billing_id">
                                        <option value="">Select Billing</option>
                                        @foreach($billings as $billing)
                                            <option value="{{ $billing->id }}" {{ old('billing_id', $transaction->billing_id) == $billing->id ? 'selected' : '' }}>
                                                {{ $billing->billing_number }} - ${{ number_format($billing->total_amount, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('billing_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Enter transaction description..." required>{{ old('description', $transaction->description) }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" id="reference_number"
                                           name="reference_number" value="{{ old('reference_number', $transaction->reference_number) }}"
                                           placeholder="Transaction reference number">
                                    @error('reference_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="1"
                                              placeholder="Additional notes...">{{ old('notes', $transaction->notes) }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Summary -->
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <strong>Transaction Summary:</strong>
                                    This is a <span class="text-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">{{ $transaction->type }}</span>
                                    transaction of <strong>${{ number_format($transaction->amount, 2) }}</strong>
                                    with status: <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'secondary') }}">{{ $transaction->status }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('finance.transactions.show', $transaction->id) }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time amount formatting
document.getElementById('amount').addEventListener('input', function(e) {
    const value = parseFloat(e.target.value);
    if (!isNaN(value) && value >= 0) {
        const summaryAmount = document.querySelector('.alert strong:nth-child(3)');
        if (summaryAmount) {
            summaryAmount.textContent = '$' + value.toFixed(2);
        }
    }
});

// Real-time type updating
document.getElementById('type').addEventListener('change', function(e) {
    const type = e.target.value;
    const typeSpan = document.querySelector('.alert .text-success, .alert .text-danger');
    const typeText = document.querySelector('.alert strong:nth-child(2)');

    if (typeSpan && typeText) {
        typeSpan.className = type === 'income' ? 'text-success' : 'text-danger';
        typeSpan.textContent = type;
        typeText.textContent = type;
    }
});
</script>

<style>
.alert {
    border-left: 4px solid #0dcaf0;
}
</style>
@endsection
