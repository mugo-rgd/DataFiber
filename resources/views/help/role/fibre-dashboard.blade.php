@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h4 class="mb-0">
            <i class="fas fa-network-wired me-2"></i>
            Kenya Fibre Dashboard Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Dashboard Features</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-map-marked-alt text-kp-blue"></i> Interactive Map</h5>
                        <p>View all fibre routes, POP locations, and network infrastructure</p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-tachometer-alt text-kp-green"></i> Real-time Utilization</h5>
                        <p>Monitor bandwidth usage across all links</p>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h5><i class="fas fa-chart-line text-kp-yellow"></i> Capacity Planning</h5>
                        <p>Identify links approaching capacity limits</p>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h5><i class="fas fa-download text-info"></i> Export Data</h5>
                        <p>Download network data for analysis</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Map Color Coding</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Color</th><th>Status</th><th>Meaning</th></tr>
                </thead>
                <tbody>
                    <tr><td class="bg-kp-green text-white">Green</td><td>Normal</td><td>Operating normally</td></tr>
                    <tr><td class="bg-kp-yellow">Yellow</td><td>Warning</td><td>High utilization >80%</td></tr>
                    <tr><td class="bg-danger text-white">Red</td><td>Critical</td><td>Outage or failure</td></tr>
                    <tr><td class="bg-info text-white">Blue</td><td>Maintenance</td><td>Planned work</td></tr>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Refresh the dashboard every 5 minutes for real-time updates.
        </div>

    </div>
</div>
@endsection
