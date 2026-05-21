@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h4 class="mb-0">
            <i class="fas fa-file-invoice-dollar me-2"></i>
            Billing Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Billing Overview</h3>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-danger">$3,202,608.66</h3>
                        <p>Pending Payments (USD)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-danger">KSh 43,440,500.10</h3>
                        <p>Pending Payments (KES)</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Generating Invoices</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>Generate Statements</strong></li>
                    <li>Select billing month</li>
                    <li>Click <strong>"Generate Monthly Invoices"</strong></li>
                    <li>Review generated invoices</li>
                    <li>Send to customers or download PDF</li>
                </ol>
            </div>
        </div>

        <div class="alert alert-kp-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Attention:</strong> 104 invoices are currently pending payment. Send reminders to delinquent customers.
        </div>

    </div>
</div>
@endsection
