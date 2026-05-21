@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-yellow text-dark">
        <h4 class="mb-0">
            <i class="fas fa-database me-2"></i>
            Backup & Recovery Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-warning">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Backup Overview:</strong> Regular backups protect your data from loss or corruption.
        </div>

        <h3>Backup Best Practices</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li>Schedule daily automated backups</li>
                    <li>Store backups in multiple locations (local + cloud)</li>
                    <li>Test restore process monthly</li>
                    <li>Keep backups for 30 days minimum</li>
                    <li>Document recovery procedures</li>
                    <li>Encrypt sensitive backup data</li>
                </ul>
            </div>
        </div>

        <h3>Types of Backups</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Backup Type</th><th>Description</th><th>Frequency</th></tr>
                </thead>
                <tbody>
                    <tr><td>Full Backup</td><td>Complete system backup</td><td>Weekly</td></tr>
                    <tr><td>Incremental</td><td>Only changed data since last backup</td><td>Daily</td></tr>
                    <tr><td>Database Only</td><td>Only database without files</td><td>Daily</td></tr>
                    <tr><td>Configuration Backup</td><td>System settings and configurations</td><td>Weekly</td></tr>
                </tbody>
            </table>
        </div>

        <h3>Manual Backup</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>System → Backup</strong></li>
                    <li>Select data to backup (Database, Files, or Both)</li>
                    <li>Choose backup location</li>
                    <li>Click <strong>"Create Backup"</strong></li>
                    <li>Download backup file when complete</li>
                </ol>
            </div>
        </div>

        <h3>Restore Process</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>System → Restore</strong></li>
                    <li>Select backup file to restore</li>
                    <li>Choose restore options</li>
                    <li>Confirm restore action</li>
                    <li>Wait for restore to complete</li>
                    <li>Verify system functionality</li>
                </ol>
                <div class="alert alert-danger mt-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> Restoring will overwrite current data. Ensure you have current backups before proceeding.
                </div>
            </div>
        </div>

        <h3>Automated Backup Schedule</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-clock text-kp-blue"></i> Recommended Schedule</h5>
                        <ul>
                            <li>Daily Incremental: 2:00 AM</li>
                            <li>Weekly Full: Sunday 1:00 AM</li>
                            <li>Monthly Archive: 1st of month 3:00 AM</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-folder text-kp-green"></i> Retention Policy</h5>
                        <ul>
                            <li>Daily backups: 7 days</li>
                            <li>Weekly backups: 4 weeks</li>
                            <li>Monthly backups: 12 months</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-kp-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success Tip:</strong> Test your restore process regularly to ensure backups are working correctly.
        </div>

        <h3>Quick Links</h3>
        <div class="row">
            <div class="col-md-6">
                <a href="{{ url('/backup/create') }}" class="btn btn-kp-primary w-100 mb-2">
                    <i class="fas fa-database"></i> Create Backup Now
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ url('/backup/schedule') }}" class="btn btn-info w-100 mb-2">
                    <i class="fas fa-calendar-alt"></i> Configure Schedule
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
