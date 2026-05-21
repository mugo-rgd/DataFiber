@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-network-wired me-2"></i>
            Technical Administrator Help Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-primary">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Your Role:</strong> Technical Administrator - Manage network operations, infrastructure, leases, and technical support.
        </div>

        <h3>Dashboard Overview</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h2 class="text-kp-blue">92</h2>
                        <p>Total Users</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-kp-green">286</h2>
                        <p>Active Leases</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-kp-yellow">2</h2>
                        <p>Pending Designs</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-danger">$3.2M</h2>
                        <p>Pending Payments</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Quick Actions</h3>
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-file-signature fa-2x text-kp-blue"></i>
                <p><strong>Manage Leases</strong><br>View all 286 active leases</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-pencil-ruler fa-2x text-kp-yellow"></i>
                <p><strong>Design Requests</strong><br>2 pending - needs attention</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-ticket-alt fa-2x text-info"></i>
                <p><strong>Tickets</strong><br>0 pending tickets</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-users fa-2x text-kp-green"></i>
                <p><strong>Users</strong><br>Manage user access</p>
            </div>
        </div>

        <h3>Key Responsibilities</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li><strong>Network Monitoring</strong> - Ensure 99.5% uptime across all fibre links</li>
                    <li><strong>Lease Management</strong> - Track 286 active leases and process renewals</li>
                    <li><strong>Design Approval</strong> - Review and approve 2 pending design requests</li>
                    <li><strong>Ticket Resolution</strong> - Respond to support tickets within SLA</li>
                    <li><strong>Infrastructure Planning</strong> - Plan capacity upgrades based on utilization</li>
                    <li><strong>CAK forms submission</strong> - Timely fill in the CAK forms for every quarter</li>
                </ul>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Use the Kenya Fibre Dashboard to monitor real-time network performance and identify potential issues before they become outages.
        </div>

    </div>
</div>
@endsection
