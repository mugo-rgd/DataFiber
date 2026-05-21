@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-yellow text-dark">
        <h4 class="mb-0">
            <i class="fas fa-wrench me-2"></i>
            Maintenance Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-warning">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Maintenance Overview:</strong> Schedule and track network maintenance activities.
        </div>

        <h3>Maintenance Types</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-calendar-alt text-kp-blue"></i> Scheduled Maintenance</h5>
                        <p>Planned upgrades and routine maintenance</p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-exclamation-triangle text-danger"></i> Emergency Maintenance</h5>
                        <p>Urgent repairs and outage responses</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Maintenance Process</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li><strong>Plan</strong> - Schedule maintenance window</li>
                    <li><strong>Notify</strong> - Inform affected customers (minimum 48 hours notice)</li>
                    <li><strong>Execute</strong> - Perform maintenance activities</li>
                    <li><strong>Verify</strong> - Confirm service restoration</li>
                    <li><strong>Report</strong> - Document maintenance completion</li>
                </ol>
            </div>
        </div>

        <div class="alert alert-kp-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Best Practice:</strong> Always schedule maintenance during off-peak hours (12 AM - 6 AM).
        </div>

    </div>
</div>
@endsection
