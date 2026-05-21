@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-green text-white">
        <h4 class="mb-0">
            <i class="fas fa-rocket me-2"></i>
            Getting Started as {{ $roleDisplayName }}
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-success">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Welcome to DarkFibre CRM!</strong>
            <p class="mb-0 mt-2">This guide will help you get started with your role and understand the key features available to you.</p>
        </div>

        <h3>First Steps</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li><strong>Complete Your Profile</strong> - Update your contact information and preferences</li>
                    <li><strong>Review Your Dashboard</strong> - Familiarize yourself with key metrics</li>
                    <li><strong>Check Notifications</strong> - Review any pending tasks or alerts</li>
                    <li><strong>Explore the Menu</strong> - Navigate through available modules</li>
                    <li><strong>Set Up Notifications</strong> - Configure email/SMS alerts for important events</li>
                </ol>
            </div>
        </div>

        <h3>Key Modules for Your Role</h3>
        <div class="card mb-4">
            <div class="card-body">
                @if($role == 'finance')
                <ul>
                    <li><strong>Generate Statements</strong> - Create customer invoices and statements</li>
                    <li><strong>Financial Reports</strong> - View revenue and collection reports</li>
                    <li><strong>Payment Tracking</strong> - Monitor and reconcile payments</li>
                </ul>
                @elseif($role == 'technical_admin')
                <ul>
                    <li><strong>Kenya Fibre Dashboard</strong> - Monitor network performance</li>
                    <li><strong>Leases</strong> - Manage fibre leases and contracts</li>
                    <li><strong>Design Requests</strong> - Review and approve designs</li>
                    <li><strong>Tickets</strong> - Handle support tickets</li>
                </ul>
                @elseif($role == 'designer')
                <ul>
                    <li><strong>Design Requests</strong> - View and work on pending designs</li>
                    <li><strong>Quotations</strong> - Create and send customer quotes</li>
                    <li><strong>Kenya Fibre Dashboard</strong> - Plan routes and check capacity</li>
                </ul>
                @elseif($role == 'debt_manager')
                <ul>
                    <li><strong>Aging Report</strong> - Track overdue invoices by age</li>
                    <li><strong>Collection Report</strong> - Monitor collection performance</li>
                    <li><strong>Customer Debts</strong> - View customer outstanding balances</li>
                    <li><strong>AI Analytics</strong> - Predict payment behavior</li>
                </ul>
                @elseif($role == 'customer')
                <ul>
                    <li><strong>Complete Profile</strong> - Add missing company information</li>
                    <li><strong>Upload Documents</strong> - Submit required compliance documents</li>
                    <li><strong>My Leases</strong> - View your active fibre leases</li>
                    <li><strong>Support Tickets</strong> - Submit and track issues</li>
                </ul>
                @elseif($role == 'ict_engineer')
                <ul>
                    <li><strong>Network Monitoring</strong> - Track performance metrics</li>
                    <li><strong>Tickets</strong> - Resolve support tickets</li>
                    <li><strong>Reports</strong> - Generate system reports</li>
                </ul>
                @elseif($role == 'account_manager')
                <ul>
                    <li><strong>My Customers</strong> - View and manage your portfolio</li>
                    <li><strong>Support Tickets</strong> - Handle customer issues</li>
                    <li><strong>Payment Follow-ups</strong> - Track collections</li>
                </ul>
                @elseif($role == 'compliance_officer')
                <ul>
                    <li><strong>CAK Compliance</strong> - Submit ASP, CSP, NFP returns</li>
                    <li><strong>Export Data</strong> - Download compliance reports</li>
                    <li><strong>Compliance History</strong> - View past submissions</li>
                </ul>
                @else
                <ul>
                    <li><strong>Dashboard</strong> - View key metrics and alerts</li>
                    <li><strong>Reports</strong> - Access system reports</li>
                    <li><strong>Settings</strong> - Configure your preferences</li>
                </ul>
                @endif
            </div>
        </div>

        <h3>Need Assistance?</h3>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center">
                            <i class="fas fa-envelope fa-2x text-kp-blue mb-2"></i>
                            <p><strong>Email Support</strong><br>support@darkfibre.co.ke</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <i class="fas fa-phone fa-2x text-kp-green mb-2"></i>
                            <p><strong>Phone Support</strong><br>020 3201 000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
