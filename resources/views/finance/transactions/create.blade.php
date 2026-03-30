@extends('layouts.app')

@section('title', 'Create Transaction')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-plus-circle me-2"></i>Create New Transaction
                </h1>
                <a href="{{ route('finance.transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Transactions
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('finance.transactions.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Transaction Type *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        @foreach($transactionTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" id="amount"
                                               name="amount" value="{{ old('amount') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_date" class="form-label">Transaction Date *</label>
                                    <input type="date" class="form-control" id="transaction_date"
                                           name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
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
                                            <option value="{{ $value }}" {{ old('payment_method') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $value => $label)
                                            <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="billing_id" class="form-label">Related Billing (Optional)</label>
                                    <select class="form-select" id="billing_id" name="billing_id">
                                        <option value="">Select Billing</option>
                                        @foreach($billings as $billing)
                                            <option value="{{ $billing->id }}" {{ old('billing_id') == $billing->id ? 'selected' : '' }}>
                                                {{ $billing->billing_number }} - ${{ number_format($billing->total_amount, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Enter transaction description..." required>{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Reference Number (Optional)</label>
                                    <input type="text" class="form-control" id="reference_number"
                                           name="reference_number" value="{{ old('reference_number') }}"
                                           placeholder="Leave blank to auto-generate">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="1"
                                              placeholder="Additional notes...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('finance.transactions.index') }}" class="btn btn-secondary me-md-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
