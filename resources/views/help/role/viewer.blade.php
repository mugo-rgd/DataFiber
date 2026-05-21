@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        <h4 class="mb-0">
            <i class="fas fa-eye me-2"></i>
            Viewer Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-secondary">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Your Role:</strong> Viewer - Read-only access to reports, dashboards, and data exports.
        </div>

        <h3>Available Features</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-tachometer-alt text-kp-blue"></i> View Dashboards</h5>
                        <p>Monitor key metrics and KPIs across the system</p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-chart-bar text-kp-green"></i> Run Reports</h5>
                        <p>Generate and view system reports</p>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h5><i class="fas fa-download text-info"></i> Export Data</h5>
                        <p>Download data in CSV, Excel, or PDF format</p>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h5><i class="fas fa-search text-kp-yellow"></i> Search</h5>
                        <p>Search across system records</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>What You Cannot Do (Read-Only)</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul class="text-danger">
                    <li><i class="fas fa-times-circle me-2"></i> Cannot create, edit, or delete records</li>
                    <li><i class="fas fa-times-circle me-2"></i> Cannot process transactions</li>
                    <li><i class="fas fa-times-circle me-2"></i> Cannot manage users</li>
                    <li><i class="fas fa-times-circle me-2"></i> Cannot submit compliance returns</li>
                    <li><i class="fas fa-times-circle me-2"></i> Cannot approve or reject requests</li>
                </ul>
            </div>
        </div>

        <h3>Exporting Data</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to the <strong>Export Data</strong> module</li>
                    <li>Select data type (ASP, CSP, NFP, or Combined)</li>
                    <li>Apply filters (status, financial year, quarter, date range)</li>
                    <li>Choose export format (Excel, CSV, or PDF)</li>
                    <li>Click <strong>"Export"</strong></li>
                </ol>
            </div>
        </div>

        <h3>Tips for Viewers</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li>Use filters to narrow down report data</li>
                    <li>Export reports to Excel for offline analysis</li>
                    <li>Bookmark important dashboards for quick access</li>
                    <li>Request data export permission for analysis</li>
                    <li>Contact your manager for additional access needs</li>
                </ul>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-question-circle me-2"></i>
            <strong>Need Additional Access?</strong> Contact your system administrator to request additional permissions if needed.
        </div>

        <h3>Quick Links</h3>
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('export.index') }}" class="btn btn-outline-kp-primary w-100 mb-2">
                    <i class="fas fa-download"></i> Export Data
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-kp-success w-100 mb-2">
                    <i class="fas fa-chart-bar"></i> View Reports
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
