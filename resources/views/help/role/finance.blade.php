@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h4 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>
            Finance Officer Help Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Your Role:</strong> Finance Officer - Manage billing, invoices, payments, and revenue tracking.
        </div>

        <h3>Dashboard Overview</h3>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Current Financial Metrics</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="border rounded p-3 mb-2">
                            <strong>USD Summary</strong>
                            <ul class="mb-0 mt-2">
                                <li>Pending Billings: <strong class="text-danger">$3,202,608.66</strong> (98 invoices)</li>
                                <li>Overdue Payments: <strong class="text-danger">$3,202,608.66</strong> (98 invoices)</li>
                                <li>Collection Rate: <strong>0%</strong></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 mb-2">
                            <strong>KES Summary</strong>
                            <ul class="mb-0 mt-2">
                                <li>Pending Billings: <strong class="text-danger">KSh 43,440,500.10</strong> (6 invoices)</li>
                                <li>Overdue Payments: <strong class="text-danger">KSh 43,440,500.10</strong> (6 invoices)</li>
                                <li>Exchange Rate: <strong>1 USD = 130 KES</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3>Quick Actions</h3>
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-file-invoice-dollar fa-2x text-kp-blue"></i>
                <p><strong>Manage Billings</strong><br>Create and view invoices</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-money-bill-wave fa-2x text-kp-green"></i>
                <p><strong>View Payments</strong><br>Track customer payments</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-chart-bar fa-2x text-kp-yellow"></i>
                <p><strong>Financial Reports</strong><br>Generate analytics</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-robot fa-2x text-info"></i>
                <p><strong>Auto Billing</strong><br>Configure automated billing</p>
            </div>
        </div>

        <h3>Recent Transactions</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr><th>Date</th><th>Invoice #</th><th>Customer</th><th>Amount</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <tr><td>Apr 09, 2026</td><td>CON-INV-000059</td><td>RAIN COMMUNICATIONS LTD</td><td>$32,291.04</td><td><span class="badge bg-kp-yellow">Pending</span></td></tr>
                    <tr><td>Apr 09, 2026</td><td>CON-INV-000041</td><td>SAFHOME FIBRE LIMITED</td><td>$226.80</td><td><span class="badge bg-kp-yellow">Pending</span></td></tr>
                    <tr><td>Apr 08, 2026</td><td>CON-INV-000112</td><td>PHPLAVATEC SOLUTIONS LIMITED</td><td>$2,094.60</td><td><span class="badge bg-kp-yellow">Pending</span></td></tr>
                </tbody>
            </table>
        </div>

        <h3>Payment Collection Best Practices</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li><strong>Review Aging Report Daily</strong> - Focus on 30/60/90 day buckets</li>
                    <li><strong>Send Payment Reminders</strong> - 7 days before due date</li>
                    <li><strong>Escalate Overdue Accounts</strong> - 90+ days to debt manager</li>
                    <li><strong>Reconcile Payments</strong> - Match received payments to invoices</li>
                    <li><strong>Generate Monthly Statements</strong> - By the 5th of each month</li>
                </ol>
            </div>
        </div>

        <div class="alert alert-kp-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Urgent Action Required:</strong> 104 invoices are overdue. Focus on top debtors: MINISTRY OF ICT (KSh 23M), KENGEN (KSh 18.5M)
        </div>

    </div>
</div>
@endsection
