@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h4 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>
            Network Monitoring Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Network Performance Metrics</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h2 class="text-kp-green">99.5%</h2>
                        <p>Network Uptime (Last 30 days)</p>
                    </div>
                    <div class="col-md-4">
                        <h2 class="text-kp-yellow">150ms</h2>
                        <p>Average Response Time</p>
                    </div>
                    <div class="col-md-4">
                        <h2 class="text-info">0</h2>
                        <p>Active Outages</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Using the Kenya Fibre Dashboard</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li><strong>Real-time Utilization</strong> - Monitor bandwidth usage</li>
                    <li><strong>Outage Alerts</strong> - View active incidents</li>
                    <li><strong>Capacity Planning</strong> - Identify links needing upgrade</li>
                    <li><strong>Historical Reports</strong> - Analyze trends</li>
                </ul>
                <a href="{{ url('/fibre-dashboard') }}" class="btn btn-kp-primary">
                    <i class="fas fa-network-wired"></i> Open Fibre Dashboard
                </a>
            </div>
        </div>

        <div class="alert alert-kp-warning">
            <i class="fas fa-bell me-2"></i>
            <strong>Alert:</strong> Set up notifications for links exceeding 80% utilization to prevent congestion.
        </div>

    </div>
</div>
@endsection
