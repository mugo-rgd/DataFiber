@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-file-signature me-2"></i>
            Lease Management Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-primary">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Lease Management Overview:</strong> Manage all fibre and infrastructure leases for your customers.
        </div>

        <h3>Current Lease Statistics</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h2 class="text-kp-blue">286</h2>
                        <p>Active Leases</p>
                        <small class="text-muted">Currently operational</small>
                    </div>
                    <div class="col-md-4">
                        <h2 class="text-kp-yellow">2</h2>
                        <p>Expiring in 30 Days</p>
                        <small class="text-muted">Require renewal attention</small>
                    </div>
                    <div class="col-md-4">
                        <h2 class="text-kp-green">288</h2>
                        <p>Total Leases (All Time)</p>
                        <small class="text-muted">Historical records</small>
                    </div>
                </div>
            </div>
        </div>

        <h3>Lease Types</h3>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-cloud fa-2x text-kp-blue mb-2"></i>
                        <h5>Dark Fibre</h5>
                        <p class="small">Unlit fibre pair for dedicated use</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-wifi fa-2x text-kp-green mb-2"></i>
                        <h5>Lit Service</h5>
                        <p class="small">Active bandwidth service</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-building fa-2x text-kp-yellow mb-2"></i>
                        <h5>Colocation</h5>
                        <p class="small">Rack space in data centre</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Lease Management Tasks</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li><strong>View Active Leases</strong> - Monitor all 286 active leases</li>
                            <li><strong>Process Renewals</strong> - Handle lease expirations and renewals</li>
                            <li><strong>Upgrade Bandwidth</strong> - Process customer upgrade requests</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li><strong>Terminate Leases</strong> - Handle early terminations</li>
                            <li><strong>Generate Reports</strong> - Export lease data for analysis</li>
                            <li><strong>Track Payments</strong> - Monitor lease payment status</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <h3>How to Manage a Lease</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>Leases</strong> from the main navigation menu</li>
                    <li>Use filters to find specific leases (by customer, status, or date)</li>
                    <li>Click on a lease ID to view detailed information</li>
                    <li>Available actions:
                        <ul>
                            <li><i class="fas fa-edit text-kp-yellow"></i> <strong>Edit</strong> - Modify lease details</li>
                            <li><i class="fas fa-sync-alt text-info"></i> <strong>Renew</strong> - Extend lease term</li>
                            <li><i class="fas fa-chart-line text-kp-green"></i> <strong>Upgrade</strong> - Increase bandwidth</li>
                            <li><i class="fas fa-trash text-danger"></i> <strong>Terminate</strong> - End lease</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>

        <h3>Lease Expiration Monitoring</h3>
        <div class="card mb-4">
            <div class="card-body">
                <p>Set up alerts for upcoming lease expirations:</p>
                <ol>
                    <li>Go to <strong>Leases → Settings</strong></li>
                    <li>Configure notification days (30, 60, 90 days before expiration)</li>
                    <li>Select recipients for alerts</li>
                    <li>Save settings</li>
                </ol>
                <div class="alert alert-kp-warning mt-2">
                    <i class="fas fa-bell me-2"></i>
                    <strong>Tip:</strong> Review expiring leases 30 days in advance to ensure timely renewals and avoid service interruptions.
                </div>
            </div>
        </div>

        <h3>Quick Links</h3>
        <div class="row">
            <div class="col-md-4">
                <a href="{{ url('/leases') }}" class="btn btn-outline-kp-primary w-100 mb-2">
                    <i class="fas fa-list"></i> View All Leases
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ url('/leases/expiring') }}" class="btn btn-outline-warning w-100 mb-2">
                    <i class="fas fa-clock"></i> View Expiring Leases
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ url('/reports/leases') }}" class="btn btn-outline-kp-success w-100 mb-2">
                    <i class="fas fa-chart-bar"></i> Lease Reports
                </a>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Best Practice:</strong> Review lease utilization quarterly to identify underutilized capacity that can be repurposed.
        </div>

    </div>
</div>
@endsection
