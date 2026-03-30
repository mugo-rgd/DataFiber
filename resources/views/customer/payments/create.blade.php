@extends('layouts.app')

@section('title', 'Make Payment - Billing #' . $billing->billing_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-credit-card me-2"></i>Make Payment
                </h1>
                <a href="{{ route('customer.invoices.show', $billing->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Billing
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <!-- Billing Summary -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Billing Summary</h6>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Billing #:</strong> {{ $billing->billing_number }}<br>
                                <strong>Due Date:</strong> {{ $billing->due_date->format('M d, Y') }}<br>
                                <strong>Status:</strong>
                                <span class="badge badge-{{ $billing->status === 'paid' ? 'success' : ($billing->isOverdue() ? 'danger' : 'warning') }}">
                                    {{ ucfirst($billing->status) }}
                                    @if($billing->isOverdue()) (Overdue) @endif
                                </span>
                            </div>
                            <div class="col-md-6 text-right">
                                <strong>Amount Due:</strong><br>
                                <h4 class="text-primary">${{ number_format((float)$billing->total_amount, 2) }}</h4>
                                @if($billing->isOverdue())
                                    <small class="text-danger">
                                        + 10% late fee: ${{ number_format((float)$billing->total_amount * 0.1, 2) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('customer.payments.store', $billing->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="payment_method"><strong>Payment Method *</strong></label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="">Select Payment Method</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount"><strong>Amount to Pay *</strong></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number"
                                       name="amount"
                                       id="amount"
                                       class="form-control"
                                       value="{{ $billing->total_amount }}"
                                       min="0.01"
                                       max="{{ $billing->total_amount * 1.1 }}"
                                       step="0.01"
                                       required>
                            </div>
                            <small class="form-text text-muted">
                                Maximum amount: ${{ number_format((float)$billing->total_amount * ($billing->isOverdue() ? 1.1 : 1), 2) }}
                                @if($billing->isOverdue()) (including 10% late fee) @endif
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="transaction_id">Transaction ID (Optional)</label>
                            <input type="text"
                                   name="transaction_id"
                                   id="transaction_id"
                                   class="form-control"
                                   placeholder="Enter transaction reference if available">
                            <small class="form-text text-muted">
                                For M-Pesa: Enter M-Pesa transaction code<br>
                                For Bank Transfer: Enter bank reference number
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="notes">Payment Notes (Optional)</label>
                            <textarea name="notes"
                                      id="notes"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Any additional payment details..."></textarea>
                        </div>

                        <!-- Payment Method Instructions -->
                        <div id="payment-instructions" class="alert alert-warning mt-3" style="display: none;">
                            <h6 class="alert-heading" id="method-title">Payment Instructions</h6>
                            <div id="method-instructions"></div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-paper-plane me-2"></i>Submit Payment
                            </button>
                        </div>

                        <div class="alert alert-light border mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Note:</strong> Payments may take 1-2 business days to process.
                                You will receive a confirmation email once your payment is verified.
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const instructionsDiv = document.getElementById('payment-instructions');
    const methodTitle = document.getElementById('method-title');
    const methodInstructions = document.getElementById('method-instructions');

    const paymentMethods = {
        mpesa: {
            title: 'M-Pesa Payment Instructions',
            instructions: `
                <ol>
                    <li>Go to M-Pesa on your phone</li>
                    <li>Select "Lipa Na M-Pesa"</li>
                    <li>Select "Pay Bill"</li>
                    <li>Enter Business Number: <strong>123456</strong></li>
                    <li>Enter Account Number: <strong>{{ $billing->billing_number }}</strong></li>
                    <li>Enter Amount: <strong>${{ number_format((float)$billing->total_amount, 2) }}</strong></li>
                    <li>Enter your M-Pesa PIN and confirm</li>
                    <li>Enter the transaction code in the form above</li>
                </ol>
            `
        },
        bank_transfer: {
            title: 'Bank Transfer Instructions',
            instructions: `
                <p><strong>Bank Details:</strong></p>
                <ul>
                    <li>Bank: Kenya Commercial Bank</li>
                    <li>Account Name: Dark Fibre Solutions Ltd.</li>
                    <li>Account Number: 1234567890</li>
                    <li>Branch: Nairobi Central</li>
                    <li>Reference: {{ $billing->billing_number }}</li>
                </ul>
            `
        },
        credit_card: {
            title: 'Credit Card Payment',
            instructions: `
                <p>You will be redirected to our secure payment gateway to complete your credit card payment.</p>
                <p>We accept Visa, MasterCard, and American Express.</p>
            `
        },
        paypal: {
            title: 'PayPal Payment',
            instructions: `
                <p>You will be redirected to PayPal to complete your payment.</p>
                <p>You can pay with your PayPal balance or any linked credit/debit card.</p>
            `
        }
    };

    paymentMethod.addEventListener('change', function() {
        const method = this.value;

        if (method && paymentMethods[method]) {
            methodTitle.textContent = paymentMethods[method].title;
            methodInstructions.innerHTML = paymentMethods[method].instructions;
            instructionsDiv.style.display = 'block';
        } else {
            instructionsDiv.style.display = 'none';
        }
    });
});
</script>
@endsection
