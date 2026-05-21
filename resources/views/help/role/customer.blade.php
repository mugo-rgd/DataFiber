@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-yellow text-dark">
        <h4 class="mb-0">
            <i class="fas fa-user-circle me-2"></i>
            Customer Dashboard Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-warning">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Welcome back, {{ Auth::user()->name ?? 'Customer' }}!</strong>
            <p class="mb-0 mt-2">This guide will help you navigate your customer dashboard and manage your fibre services.</p>
        </div>

        <h3>Dashboard Overview</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h2 class="text-kp-yellow">2</h2>
                            <p>Total Invoices</p>
                            <small class="text-muted">All time</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h2 class="text-danger">2</h2>
                            <p>Pending</p>
                            <small class="text-muted">Awaiting payment</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h2 class="text-danger">2</h2>
                            <p>Overdue</p>
                            <small class="text-muted">Past due date</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h2 class="text-danger">$10,540.44</h2>
                            <p>Total Outstanding</p>
                            <small class="text-muted">Amount pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3>Quick Actions</h3>
        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <i class="fas fa-file-signature fa-2x text-kp-blue"></i>
                <p><strong>My Leases</strong><br>Manage your fibre leases</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-road fa-2x text-kp-green"></i>
                <p><strong>Request Fibre Routes</strong><br>Submit new connection requests</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-file-invoice-dollar fa-2x text-info"></i>
                <p><strong>My Billings</strong><br>View and manage billing</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <i class="fas fa-ticket-alt fa-2x text-kp-yellow"></i>
                <p><strong>Support Tickets</strong><br>Get help and support</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-user-edit fa-2x text-secondary"></i>
                <p><strong>Profile</strong><br>Manage account settings</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-file-upload fa-2x text-danger"></i>
                <p><strong>Documents</strong><br>Upload and manage documents</p>
            </div>
        </div>

        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Payment Due:</strong> You have 2 overdue invoices totaling <strong>$10,540.44</strong>. Please make payment to avoid service interruption.
        </div>

    </div>
</div>
@endsection
