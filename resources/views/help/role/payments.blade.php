@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-green text-white">
        <h4 class="mb-0">
            <i class="fas fa-money-bill-wave me-2"></i>
            Payments Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Recording Payments</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>Customer Accounts</strong> or <strong>Invoices</strong></li>
                    <li>Find the invoice to pay</li>
                    <li>Click <strong>"Record Payment"</strong></li>
                    <li>Enter amount, date, and reference number</li>
                    <li>Upload payment receipt (optional)</li>
                    <li>Click <strong>"Save"</strong></li>
                </ol>
            </div>
        </div>

        <h3>Payment Methods</h3>
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-university fa-2x text-kp-blue"></i>
                <p>Bank Transfer</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-mobile-alt fa-2x text-kp-green"></i>
                <p>MPESA</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-credit-card fa-2x text-info"></i>
                <p>Credit Card</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-money-check-alt fa-2x text-kp-yellow"></i>
                <p>Cheque</p>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-robot me-2"></i>
            <strong>Auto-Billing:</strong> Customers with auto-billing enabled are charged automatically on the due date.
        </div>

    </div>
</div>
@endsection
