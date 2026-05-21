@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-microchip me-2"></i>
            ICT Engineer Help Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-primary">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Your Role:</strong> ICT Engineer - Manage network infrastructure in your assigned region/county.
        </div>

        <h3>Region Performance</h3>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h5>Network Uptime</h5>
                    <h2 class="text-kp-green">99.5%</h2>
                    <small>Last 30 days</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h5>Pending Tickets</h5>
                    <h2 class="text-kp-yellow">0</h2>
                    <small>Awaiting resolution</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h5>Security Alerts</h5>
                    <h2 class="text-danger">3</h2>
                    <small>Require attention</small>
                </div>
            </div>
        </div>

        <h3>System Notifications</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item list-group-item-warning">
                        <i class="fas fa-chart-line me-2"></i>
                        <strong>Network Optimization Required</strong> - Bandwidth usage exceeding 85% on core switch
                        <small class="d-block text-muted">2 hours ago</small>
                    </li>
                    <li class="list-group-item list-group-item-info">
                        <i class="fas fa-drafting-compass me-2"></i>
                        <strong>New Design Request Assigned</strong> - Fibre route design for customer
                        <small class="d-block text-muted">1 hour ago</small>
                    </li>
                    <li class="list-group-item list-group-item-success">
                        <i class="fas fa-database me-2"></i>
                        <strong>Server Backup Completed</strong> - Nightly backup successful
                        <small class="d-block text-muted">1 day ago</small>
                    </li>
                </ul>
            </div>
        </div>

        <h3>Quick Actions</h3>
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-tasks fa-2x text-kp-blue"></i>
                <p><strong>My Requests</strong><br>View assigned design requests</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-ticket-alt fa-2x text-kp-yellow"></i>
                <p><strong>Manage Tickets</strong><br>Resolve support tickets</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-map-marked-alt fa-2x text-kp-green"></i>
                <p><strong>My County</strong><br>County-specific ICT management</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-chart-line fa-2x text-info"></i>
                <p><strong>Reports</strong><br>Generate ICT system reports</p>
            </div>
        </div>

        <h3>Using the Kenya Fibre Dashboard</h3>
        <div class="card mb-4">
            <div class="card-body">
                <p>Monitor network performance and infrastructure:</p>
                <ul>
                    <li><strong>Real-time Utilization</strong> - Track bandwidth usage across links</li>
                    <li><strong>Outage Alerts</strong> - View active and recent outages</li>
                    <li><strong>Capacity Planning</strong> - Identify links needing upgrade</li>
                    <li><strong>POP Locations</strong> - View all Points of Presence</li>
                </ul>
                <a href="{{ route('kenya.fibre.dashboard') }}" class="btn btn-sm btn-kp-primary">
                    <i class="fas fa-network-wired"></i> Open Fibre Dashboard
                </a>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Set up email alerts for critical network events to respond faster to outages.
        </div>

    </div>
</div>
@endsection
