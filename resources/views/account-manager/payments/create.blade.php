@extends('layouts.app')

@section('title', 'Create Payment Followup')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create Payment Followup</h1>
                <a href="{{ route('account-manager.payments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('account-manager.payments.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id">Customer *</label>
                                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->email }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Amount *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" name="amount" id="amount"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               value="{{ old('amount') }}" step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date *</label>
                                    <input type="date" name="due_date" id="due_date"
                                           class="form-control @error('due_date') is-invalid @enderror"
                                           value="{{ old('due_date') }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="reminded" {{ old('status') == 'reminded' ? 'selected' : '' }}>Reminded</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Payment status will be set to pending by default</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="4" placeholder="Add any notes about this payment followup...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Payment Followup
                            </button>
                            <a href="{{ route('account-manager.payments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('due_date').min = today;

        // Format amount field on blur
        $('#amount').on('blur', function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                $(this).val(value.toFixed(2));
            }
        });
    });
</script>
@endpush
