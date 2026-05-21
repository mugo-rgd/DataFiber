@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            <i class="fas fa-file-invoice-dollar me-2"></i>
            Invoices & Payments Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Current Outstanding: $10,540.44</strong> - 2 invoices overdue
        </div>

        <h3>Current Invoices</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CON-INV-000037-20260409002753-835</td>
                        <td>Apr 09, 2026</td>
                        <td>Apr 16, 2026</td>
                        <td>$5,270.22</td>
                        <td><span class="badge bg-danger">Overdue</span></td>
                        <td><button class="btn btn-sm btn-kp-success">Pay Now</button></td>
                    </tr>
                    <tr>
                        <td>CON-INV-000037-20260213215059-862</td>
                        <td>Feb 13, 2026</td>
                        <td>Feb 20, 2026</td>
                        <td>$5,270.22</td>
                        <td><span class="badge bg-danger">Overdue</span></td>
                        <td><button class="btn btn-sm btn-kp-success">Pay Now</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Invoice Details</h3>
        <div class="card mb-4">
            <div class="card-body">
                <h5>CON-INV-000037-20260409002753-835</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Lease</th><th>Description</th><th>Period</th><th>Amount</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Lease #3368</td>
                                <td>Dark fibre: EADC-RUAI KPLC OFFICE</td>
                                <td>Feb 13 - May 12, 2026</td>
                                <td>$3,058.86</td>
                            </tr>
                            <tr>
                                <td>Lease #3370</td>
                                <td>Dark fibre: EADC-RUIRU SS</td>
                                <td>Feb 13 - May 12, 2026</td>
                                <td>$2,211.36</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h3>Making a Payment</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Click <strong>"Pay Now"</strong> on the invoice you want to pay</li>
                    <li>Select payment method:
                        <ul>
                            <li>💳 Credit/Debit Card</li>
                            <li>📱 MPESA (Paybill: 123456, Account: Invoice #)</li>
                            <li>🏦 Bank Transfer</li>
                        </ul>
                    </li>
                    <li>Follow the payment instructions</li>
                    <li>Upload payment receipt (optional but recommended)</li>
                    <li>Click <strong>"Confirm Payment"</strong></li>
                </ol>
            </div>
        </div>

        <h3>Payment Methods</h3>
        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <i class="fas fa-credit-card fa-2x text-kp-blue"></i>
                <p><strong>Credit Card</strong><br>Visa, Mastercard, Amex</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-mobile-alt fa-2x text-kp-green"></i>
                <p><strong>MPESA</strong><br>Paybill: 123456</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-university fa-2x text-info"></i>
                <p><strong>Bank Transfer</strong><br>Equity Bank, KCB, Stanbic</p>
            </div>
        </div>

        <div class="alert alert-kp-warning">
            <i class="fas fa-clock me-2"></i>
            <strong>Late Payment:</strong> Payments received after due date may incur late fees. Contact support for payment arrangements.
        </div>

    </div>
</div>
@endsection
