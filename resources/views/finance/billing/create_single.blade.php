{{-- resources/views/finance/billing/create_single.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Single Billing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('finance.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('finance.billing.index') }}">Billings</a></li>
                        <li class="breadcrumb-item active">Create Single Billing</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Single Billing</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('finance.billing.storeSingle') }}" method="POST" id="billingForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror"
                                            id="customer_id" name="customer_id" required>
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
                                    <label for="lease_id" class="form-label">Lease <span class="text-danger">*</span></label>
                                    <select class="form-select @error('lease_id') is-invalid @enderror"
                                            id="lease_id" name="lease_id" required>
                                        <option value="">Select Lease</option>
                                        @foreach($leases as $lease)
                                            <option value="{{ $lease->id }}"
                                                data-amount="{{ $lease->monthly_cost ?? 0 }}"
                                                data-currency="{{ $lease->currency ?? 'KES' }}"
                                                data-cycle="{{ $lease->billing_cycle ?? 'monthly' }}"
                                                {{ old('lease_id') == $lease->id ? 'selected' : '' }}>
                                                {{ $lease->lease_number }} - {{ $lease->customer_name ?? 'N/A' }} ({{ $lease->monthly_cost ?? 0 }} {{ $lease->currency ?? 'KES' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lease_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="billing_number" class="form-label">Billing Number (Optional)</label>
                                    <input type="text" class="form-control @error('billing_number') is-invalid @enderror"
                                           id="billing_number" name="billing_number"
                                           value="{{ old('billing_number') }}"
                                           placeholder="Leave empty for auto-generation">
                                    @error('billing_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="billing_date" class="form-label">Billing Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('billing_date') is-invalid @enderror"
                                           id="billing_date" name="billing_date"
                                           value="{{ old('billing_date', now()->format('Y-m-d')) }}" required>
                                    @error('billing_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                           id="due_date" name="due_date"
                                           value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="billing_cycle" class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                                    <select class="form-select @error('billing_cycle') is-invalid @enderror"
                                            id="billing_cycle" name="billing_cycle" required>
                                        <option value="">Select Cycle</option>
                                        @foreach($billingCycles as $value => $label)
                                            <option value="{{ $value }}" {{ old('billing_cycle') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('billing_cycle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                    <select class="form-select @error('currency') is-invalid @enderror"
                                            id="currency" name="currency" required>
                                        <option value="">Select Currency</option>
                                        <option value="KES" {{ old('currency') == 'KES' ? 'selected' : '' }}>KES</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status" name="status" required>
                                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_start" class="form-label">Period Start <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('period_start') is-invalid @enderror"
                                           id="period_start" name="period_start"
                                           value="{{ old('period_start', now()->startOfMonth()->format('Y-m-d')) }}" required>
                                    @error('period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_end" class="form-label">Period End <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('period_end') is-invalid @enderror"
                                           id="period_end" name="period_end"
                                           value="{{ old('period_end', now()->endOfMonth()->format('Y-m-d')) }}" required>
                                    @error('period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (excl. VAT) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror"
                                           id="amount" name="amount" value="{{ old('amount') }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Tax Rate (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('tax_rate') is-invalid @enderror"
                                           id="tax_rate" name="tax_rate" value="{{ old('tax_rate', 16) }}" required>
                                    @error('tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="vat_amount" class="form-label">VAT Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('vat_amount') is-invalid @enderror"
                                           id="vat_amount" name="vat_amount" value="{{ old('vat_amount') }}" readonly
                                           style="background-color: #f8f9fa;">
                                    @error('vat_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label">Total Amount (incl. VAT) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('total_amount') is-invalid @enderror"
                                           id="total_amount" name="total_amount" value="{{ old('total_amount') }}" readonly
                                           style="background-color: #f8f9fa; font-weight: bold;">
                                    @error('total_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror"
                                            id="payment_method" name="payment_method">
                                        <option value="">Select Method</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="mpesa" {{ old('payment_method') == 'mpesa' ? 'selected' : '' }}>M-PESA</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="2">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{ route('finance.billing.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary float-end">
                                    <i class="fas fa-save me-2"></i>Create Billing
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-label {
        font-weight: 500;
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const leaseSelect = document.getElementById('lease_id');
        const amountInput = document.getElementById('amount');
        const taxRateInput = document.getElementById('tax_rate');
        const vatAmountInput = document.getElementById('vat_amount');
        const totalAmountInput = document.getElementById('total_amount');
        const currencySelect = document.getElementById('currency');
        const billingCycleSelect = document.getElementById('billing_cycle');
        const periodStartInput = document.getElementById('period_start');
        const periodEndInput = document.getElementById('period_end');
        const billingDateInput = document.getElementById('billing_date');
        const dueDateInput = document.getElementById('due_date');

        // Load lease details when lease is selected
        leaseSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const amount = selectedOption.dataset.amount;
                const currency = selectedOption.dataset.currency;
                const cycle = selectedOption.dataset.cycle;

                if (amount) amountInput.value = parseFloat(amount).toFixed(2);
                if (currency) currencySelect.value = currency;
                if (cycle) billingCycleSelect.value = cycle;

                calculateTotals();
            }
        });

        // Calculate VAT and total
        function calculateTotals() {
            const amount = parseFloat(amountInput.value) || 0;
            const taxRate = parseFloat(taxRateInput.value) || 0;

            const vatAmount = (amount * taxRate / 100).toFixed(2);
            const totalAmount = (amount + parseFloat(vatAmount)).toFixed(2);

            vatAmountInput.value = vatAmount;
            totalAmountInput.value = totalAmount;
        }

        amountInput.addEventListener('input', calculateTotals);
        taxRateInput.addEventListener('input', calculateTotals);

        // Set period based on billing date and cycle
        function updatePeriod() {
            if (billingDateInput.value && billingCycleSelect.value) {
                const billingDate = new Date(billingDateInput.value);

                if (billingCycleSelect.value === 'monthly') {
                    periodStartInput.value = new Date(billingDate.getFullYear(), billingDate.getMonth(), 1)
                        .toISOString().split('T')[0];
                    periodEndInput.value = new Date(billingDate.getFullYear(), billingDate.getMonth() + 1, 0)
                        .toISOString().split('T')[0];
                } else if (billingCycleSelect.value === 'quarterly') {
                    const quarter = Math.floor(billingDate.getMonth() / 3);
                    periodStartInput.value = new Date(billingDate.getFullYear(), quarter * 3, 1)
                        .toISOString().split('T')[0];
                    periodEndInput.value = new Date(billingDate.getFullYear(), quarter * 3 + 3, 0)
                        .toISOString().split('T')[0];
                }
            }
        }

        billingCycleSelect.addEventListener('change', updatePeriod);
        billingDateInput.addEventListener('change', updatePeriod);

        // Validate dates
        function validateDates() {
            if (billingDateInput.value && dueDateInput.value) {
                if (dueDateInput.value < billingDateInput.value) {
                    dueDateInput.setCustomValidity('Due date must be after billing date');
                } else {
                    dueDateInput.setCustomValidity('');
                }
            }

            if (periodStartInput.value && periodEndInput.value) {
                if (periodEndInput.value < periodStartInput.value) {
                    periodEndInput.setCustomValidity('Period end must be after period start');
                } else {
                    periodEndInput.setCustomValidity('');
                }
            }
        }

        billingDateInput.addEventListener('change', validateDates);
        dueDateInput.addEventListener('change', validateDates);
        periodStartInput.addEventListener('change', validateDates);
        periodEndInput.addEventListener('change', validateDates);

        // Form validation
        document.getElementById('billingForm').addEventListener('submit', function(e) {
            const customerId = document.getElementById('customer_id').value;
            const leaseId = document.getElementById('lease_id').value;
            const amount = document.getElementById('amount').value;

            if (!customerId || !leaseId || !amount) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });

        // Auto-populate due date 30 days after billing date
        billingDateInput.addEventListener('change', function() {
            if (this.value) {
                const billingDate = new Date(this.value);
                const dueDate = new Date(billingDate);
                dueDate.setDate(dueDate.getDate() + 30);
                dueDateInput.value = dueDate.toISOString().split('T')[0];
            }
        });
    });
</script>
@endpush
