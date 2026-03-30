@extends('layouts.app')

@section('title', 'Create Lease Billing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mt-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('finance.billing.index') }}">Finance Billing</a></li>
                        <li class="breadcrumb-item active">Create Billing</li>
                    </ol>
                </nav>

                <x-back-button
                    :url="route('finance.billing.index')"
                    text="Back to Billings"
                />
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Create New Lease Billing
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('finance.billing.storeSingle') }}" id="billingForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lease_id" class="form-label">Lease *</label>
                                    <select class="form-select" id="lease_id" name="lease_id" required>
                                        <option value="">Select Lease</option>
                                        @foreach($leases as $lease)
                                            <option value="{{ $lease->id }}" {{ old('lease_id') == $lease->id ? 'selected' : '' }}>
                                                {{ $lease->title }} - {{ $lease->property->name ?? 'No Property' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lease_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer *</label>
                                    <select class="form-select" id="customer_id" name="customer_id" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="billing_number" class="form-label">Billing Number</label>
                                    <input type="text" class="form-control" id="billing_number"
                                           name="billing_number" value="{{ old('billing_number') }}"
                                           placeholder="Leave blank to auto-generate">
                                    <div class="form-text text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Will be auto-generated as INV-YYYYMMDD-XXXX if left blank
                                    </div>
                                    @error('billing_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="billing_date" class="form-label">Billing Date *</label>
                                    <input type="date" class="form-control" id="billing_date"
                                           name="billing_date" value="{{ old('billing_date', date('Y-m-d')) }}" required>
                                    @error('billing_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date *</label>
                                    <input type="date" class="form-control" id="due_date"
                                           name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                    @error('due_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (Excluding VAT) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" id="amount"
                                               name="amount" value="{{ old('amount') }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Amount before adding VAT</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="vat_amount" class="form-label">VAT (16%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" id="vat_amount"
                                               name="vat_amount" value="{{ old('vat_amount', '0.00') }}" readonly>
                                    </div>
                                    <small class="text-muted">Calculated automatically</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label">Total Amount (Including VAT) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" id="total_amount"
                                               name="total_amount" value="{{ old('total_amount') }}" required readonly>
                                    </div>
                                    @error('total_amount')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Amount + 16% VAT</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info p-2">
                                    <div id="calculation-breakdown" class="small">
                                        <strong>Calculation Breakdown:</strong><br>
                                        <span id="breakdown-amount">Amount: $0.00</span><br>
                                        <span id="breakdown-vat">VAT (16%): $0.00</span><br>
                                        <strong><span id="breakdown-total">Total: $0.00</span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency *</label>
                                    <select class="form-select" id="currency" name="currency" required>
                                        <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="KES" {{ old('currency') == 'KES' ? 'selected' : '' }}>KES</option>
                                    </select>
                                    @error('currency')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                                    <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                        <option value="">Select Cycle</option>
                                        @foreach($billingCycles as $value => $label)
                                            <option value="{{ $value }}" {{ old('billing_cycle') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('billing_cycle')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Tax Rate (%) *</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" id="tax_rate"
                                               name="tax_rate" value="{{ old('tax_rate', '16.00') }}" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('tax_rate')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="period_start" class="form-label">Period Start *</label>
                                    <input type="date" class="form-control" id="period_start"
                                           name="period_start" value="{{ old('period_start', date('Y-m-01')) }}" required>
                                    @error('period_start')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="period_end" class="form-label">Period End *</label>
                                    <input type="date" class="form-control" id="period_end"
                                           name="period_end" value="{{ old('period_end', date('Y-m-t')) }}" required>
                                    @error('period_end')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select" id="payment_method" name="payment_method">
                                        <option value="">Select Method</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                        <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Enter billing description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Internal Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="1"
                                              placeholder="Internal notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('finance.billing.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Billing
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate VAT and Total Amount
function calculateAmounts() {
    const amountInput = document.getElementById('amount');
    const vatInput = document.getElementById('vat_amount');
    const totalInput = document.getElementById('total_amount');
    const taxRateInput = document.getElementById('tax_rate');
    const breakdownAmount = document.getElementById('breakdown-amount');
    const breakdownVAT = document.getElementById('breakdown-vat');
    const breakdownTotal = document.getElementById('breakdown-total');

    if (amountInput && vatInput && totalInput && taxRateInput) {
        const amount = parseFloat(amountInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 16.00; // Default 16%
        const vatAmount = amount * (taxRate / 100);
        const totalAmount = amount + vatAmount;

        // Update all fields
        vatInput.value = vatAmount.toFixed(2);
        totalInput.value = totalAmount.toFixed(2);

        // Update breakdown display
        const formattedAmount = formatCurrency(amount);
        const formattedVAT = formatCurrency(vatAmount);
        const formattedTotal = formatCurrency(totalAmount);

        if (breakdownAmount && breakdownVAT && breakdownTotal) {
            breakdownAmount.textContent = `Amount: ${formattedAmount}`;
            breakdownVAT.textContent = `VAT (${taxRate}%): ${formattedVAT}`;
            breakdownTotal.textContent = `Total: ${formattedTotal}`;
        }
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Calculate initial amounts
    calculateAmounts();

    // Add event listeners for amount and tax rate changes
    const amountInput = document.getElementById('amount');
    const taxRateInput = document.getElementById('tax_rate');

    if (amountInput) {
        amountInput.addEventListener('input', calculateAmounts);
        amountInput.addEventListener('change', calculateAmounts);
        amountInput.addEventListener('blur', calculateAmounts);
    }

    if (taxRateInput) {
        taxRateInput.addEventListener('input', calculateAmounts);
        taxRateInput.addEventListener('change', calculateAmounts);
    }

    // Set default period dates based on billing cycle
    const billingCycleSelect = document.getElementById('billing_cycle');
    if (billingCycleSelect) {
        billingCycleSelect.addEventListener('change', function(e) {
            const today = new Date();
            const periodStart = document.getElementById('period_start');
            const periodEnd = document.getElementById('period_end');

            if (periodStart && periodEnd) {
                switch(e.target.value) {
                    case 'monthly':
                        periodStart.value = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                        periodEnd.value = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
                        break;
                    case 'quarterly':
                        const quarter = Math.floor(today.getMonth() / 3);
                        periodStart.value = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
                        periodEnd.value = new Date(today.getFullYear(), (quarter + 1) * 3, 0).toISOString().split('T')[0];
                        break;
                    case 'semi_annual':
                        const month = today.getMonth();
                        const half = month < 6 ? 0 : 6;
                        periodStart.value = new Date(today.getFullYear(), half, 1).toISOString().split('T')[0];
                        periodEnd.value = new Date(today.getFullYear(), half + 5, 0).toISOString().split('T')[0];
                        break;
                    case 'annual':
                        periodStart.value = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                        periodEnd.value = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
                        break;
                    case 'one_time':
                        periodStart.value = today.toISOString().split('T')[0];
                        periodEnd.value = today.toISOString().split('T')[0];
                        break;
                }
            }
        });
    }

    // Generate billing number preview when form is submitted
    const billingForm = document.getElementById('billingForm');
    if (billingForm) {
        billingForm.addEventListener('submit', function(e) {
            const billingNumberField = document.getElementById('billing_number');

            // If billing number is empty, generate one
            if (!billingNumberField.value.trim()) {
                const now = new Date();
                const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
                const randomNum = Math.floor(1000 + Math.random() * 9000); // 4-digit random number

                billingNumberField.value = `INV-${dateStr}-${randomNum}`;
            }
        });
    }

    // Optional: Show preview of auto-generated number when field loses focus
    const billingNumberField = document.getElementById('billing_number');
    if (billingNumberField) {
        billingNumberField.addEventListener('blur', function(e) {
            if (!this.value.trim()) {
                const now = new Date();
                const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
                const randomNum = Math.floor(1000 + Math.random() * 9000);
                const generatedNumber = `INV-${dateStr}-${randomNum}`;

                // Show preview in placeholder
                this.placeholder = `Auto: ${generatedNumber}`;
            }
        });
    }
});
</script>
@endsection
