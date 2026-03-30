@extends('layouts.app')

@section('title', 'Edit Payment - ' . $payment->billing_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Payment - {{ $payment->billing_number }}</h4>
                    <a href="{{ route('finance.debt.payments') }}" class="btn btn-secondary float-end">
                        <i class="fas fa-arrow-left"></i> Back to Payments
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column: Payment Details -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Billing Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Billing Number:</th>
                                            <td>{{ $payment->billing_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Customer:</th>
                                            <td>{{ $payment->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Billing Date:</th>
                                            <td>{{ $payment->billing_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Due Date:</th>
                                            <td>{{ $payment->due_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Amount:</th>
                                            <td class="fw-bold">USD {{ number_format($payment->total_amount ?? $payment->total_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Current Paid Amount:</th>
                                            <td>USD {{ number_format($payment->paid_amount ?? $payment->paid_amount ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Balance:</th>
                                            <td class="fw-bold">
                                                @php
                                                    $total = $payment->total_amount ?? $payment->total_amount;
                                                    $paid = $payment->paid_amount ?? $payment->paid_amount ?? 0;
                                                    $balance = $total - $paid;
                                                @endphp
                                                USD {{ number_format($balance, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Payment Date:</th>
                                            <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y H:i') : 'Not paid yet' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Line Items -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Billing Line Items</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Lease</th>
                                                    <th>Period</th>
                                                    <th class="text-end">Amount (USD)</th>
                                                    <th class="text-end">Paid (USD)</th>
                                                    <th class="text-end">Balance (USD)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($payment->billingLineItems as $item)
                                                @php
                                                    $itemPaidUSD = $item->paid_amount ?? 0;
                                                    // if ($item->currency === 'USD' && $payment->exchange_rate) {
                                                    //     $itemPaidKES = $item->paid_amount * $payment->exchange_rate;
                                                    // }
                                                    $itemBalance = $item->amount - $itemPaidUSD;
                                                @endphp
                                                <tr>
                                                    <td>{{ $item->lease->lease_number ?? 'N/A' }}</td>
                                                    <td>{{ $item->period_start->format('M Y') }} - {{ $item->period_end->format('M Y') }}</td>
                                                    <td class="text-end">USD {{ number_format($item->amount, 2) }}</td>
                                                    <td class="text-end">USD {{ number_format($itemPaidUSD, 2) }}</td>
                                                    <td class="text-end">USD {{ number_format($itemBalance, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Update Form -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Update Payment Details</h5>
                                </div>
                                <div class="card-body">
                                   <form action="{{ route('finance.debt.payments.update', ['payment' => $payment->id]) }}" method="POST">
                                    @csrf
                                      @method('PUT')

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="status" class="form-label">Status *</label>
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="sent" {{ $payment->status == 'sent' ? 'selected' : '' }}>Sent</option>
                                                    <option value="paid" {{ $payment->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="overdue" {{ $payment->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                                    <option value="partial" {{ $payment->status == 'partial' ? 'selected' : '' }}>partial</option>
                                                    <option value="cancelled" {{ $payment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="payment_date" class="form-label">Payment Date *</label>
                                                <input type="datetime-local" name="payment_date" id="payment_date"
                                                       class="form-control"
                                                       value="{{ old('payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                           <label for="paid_amount">Paid Amount (USD)</label>
    <input type="number" step="0.01" class="form-control"
           id="paid_amount" name="paid_amount"
           value="{{ old('paid_amount', $payment->paid_amount) }}"
           min="0" max="{{ $payment->total_amount }}" required
           oninput="validatePaymentAmount(this)">
                                            </div>
                                            <div class="col-md-6">
                                               <label for="paid_amount_kes">Paid Amount (KES) - Optional</label>
    <input type="number" step="0.01" class="form-control"
           id="paid_amount_kes" name="paid_amount_kes"
           value="{{ old('paid_amount_kes', $payment->paid_amount_kes ?? ($payment->paid_amount * ($payment->metadata['exchange_rate'] ?? 1))) }}"
           min="0"
           oninput="calculateFromKES(this)">
    <small class="form-text text-muted">
        Leave empty to calculate automatically from USD
    </small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="payment_method" class="form-label">Payment Method</label>
                                                <select name="payment_method" id="payment_method" class="form-control">
                                                    <option value="">Select Method</option>
                                                    <option value="bank_transfer" {{ (isset($payment->metadata['payment_method']) && $payment->metadata['payment_method'] == 'bank_transfer') ? 'selected' : '' }}>Bank Transfer</option>
                                                    <option value="mpesa" {{ (isset($payment->metadata['payment_method']) && $payment->metadata['payment_method'] == 'mpesa') ? 'selected' : '' }}>M-Pesa</option>
                                                    <option value="credit_card" {{ (isset($payment->metadata['payment_method']) && $payment->metadata['payment_method'] == 'credit_card') ? 'selected' : '' }}>Credit Card</option>
                                                    <option value="cash" {{ (isset($payment->metadata['payment_method']) && $payment->metadata['payment_method'] == 'cash') ? 'selected' : '' }}>Cash</option>
                                                    <option value="cheque" {{ (isset($payment->metadata['payment_method']) && $payment->metadata['payment_method'] == 'cheque') ? 'selected' : '' }}>Cheque</option>
                                                    <option value="other" {{ (isset($payment->metadata['payment_method']) && $payment->metadata['payment_method'] == 'other') ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Line item update option -->
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="update_line_items" id="update_line_items"
                                                       class="form-check-input" value="1" checked>
                                                <label for="update_line_items" class="form-check-label">
                                                    Update billing line items proportionally
                                                </label>
                                                <small class="text-muted d-block">
                                                    Line item paid amounts will be updated based on the payment ratio.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_reference" class="form-label">Payment Reference</label>
                                            <input type="text" name="payment_reference" id="payment_reference"
                                                   class="form-control"
                                                   value="{{ old('payment_reference', $payment->metadata['payment_reference'] ?? '') }}"
                                                   placeholder="e.g., Transaction ID, Cheque No.">
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $payment->metadata['notes'] ?? '') }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="send_notification" id="send_notification" class="form-check-input" value="1">
                                                <label for="send_notification" class="form-check-label">
                                                    Send notification to customer
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('finance.debt.payments') }}" class="btn btn-secondary">
                                                Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Payment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- KRA/TEV Status -->
                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">KRA/TEV Status</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">KRA Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $payment->kra_status == 'verified' ? 'success' : ($payment->kra_status == 'failed' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($payment->kra_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>KRA Invoice:</th>
                                            <td>{{ $payment->kra_invoice_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>TEV Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $payment->tevin_status == 'committed' ? 'success' : ($payment->tevin_status == 'failed' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($payment->tevin_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>TEV Control Code:</th>
                                            <td>{{ $payment->tevin_control_code ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>TEV Submitted At:</th>
                                            <td>{{ $payment->tevin_submitted_at ? $payment->tevin_submitted_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>TEV Committed At:</th>
                                            <td>{{ $payment->tevin_committed_at ? $payment->tevin_committed_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const paidAmountInput = document.getElementById('paid_amount');
        const paymentDateInput = document.getElementById('payment_date');
        const updateLineItemsCheckbox = document.getElementById('update_line_items');

        // When status changes to paid, set paid amount to total and enable payment date
        statusSelect.addEventListener('change', function() {
            if (this.value === 'paid') {
                // Set paid amount to total
                const maxAmount = parseFloat('{{ $payment->total_amount ?? $payment->total_amount }}');
                paidAmountInput.value = maxAmount.toFixed(2);

                // Set payment date to now if not already set
                if (!paymentDateInput.value) {
                    const now = new Date();
                    const timezoneOffset = now.getTimezoneOffset() * 60000;
                    const localISOTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);
                    paymentDateInput.value = localISOTime;
                }

                // Enable line item update checkbox
                updateLineItemsCheckbox.checked = true;
                updateLineItemsCheckbox.disabled = false;

            } else if (this.value === 'pending' || this.value === 'sent') {
                // Reset paid amount for non-paid statuses
                paidAmountInput.value = '0.00';

                // Disable line item update for non-paid statuses
                updateLineItemsCheckbox.checked = false;
                updateLineItemsCheckbox.disabled = true;
            }
        });

        // Validate paid amount doesn't exceed total
        paidAmountInput.addEventListener('input', function() {
            const max = parseFloat('{{ $payment->total_amount ?? $payment->total_amount }}');
            const current = parseFloat(this.value) || 0;

            if (current > max) {
                this.value = max.toFixed(2);
                alert('Paid amount cannot exceed total amount.');
            }
        });

        // Show/hide line items update option based on paid amount
        paidAmountInput.addEventListener('change', function() {
            const paidAmount = parseFloat(this.value) || 0;
            const totalAmount = parseFloat('{{ $payment->total_amount ?? $payment->total_amount }}');

            if (paidAmount > 0 && paidAmount <= totalAmount) {
                updateLineItemsCheckbox.disabled = false;
                updateLineItemsCheckbox.checked = true;
            } else {
                updateLineItemsCheckbox.disabled = true;
                updateLineItemsCheckbox.checked = false;
            }
        });

        // Initialize form state
        if (statusSelect.value === 'paid') {
            paidAmountInput.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
