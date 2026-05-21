@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-history text-info me-2"></i> Activity Log</h1>
<hr>

<div class="alert alert-info">
    <i class="fas fa-chart-line me-2"></i>
    Track your actions and login history for security and auditing purposes.
</div>

<h3>Accessing Your Activity Log</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Go to <strong>My Profile</strong> from the top-right menu</li>
            <li>Click on the <strong>"Activity Log"</strong> tab</li>
            <li>View chronological list of your actions</li>
        </ol>
    </div>
</div>

<h3>What's Tracked</h3>
<div class="table-responsive mb-4">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr><th>Activity Type</th><th>Tracked Data</th><th>Retention</th></tr>
        </thead>
        <tbody>
            <tr><td>Login Events</td><td>Timestamp, IP address, device, browser</td><td>90 days</td></tr>
            <tr><td>Profile Changes</td><td>Field changed, old value, new value</td><td>Permanent</td></tr>
            <tr><td>Password Changes</td><td>Timestamp, IP address</td><td>1 year</td></tr>
            <tr><td>Data Exports</td><td>Export type, date range, format</td><td>Permanent</td></tr>
        </tbody>
    </table>
</div>

<h3>Filtering Your Activity Log</h3>
<div class="card mb-4">
    <div class="card-body">
        <ul>
            <li><strong>Date Range:</strong> Today, Last 7 days, Last 30 days, Custom</li>
            <li><strong>Activity Type:</strong> Login, Profile, Security, Exports</li>
            <li><strong>Status:</strong> Success, Failure</li>
            <li><strong>IP Address:</strong> Filter by specific IP</li>
        </ul>
    </div>
</div>

<h3>Security Monitoring</h3>
<div class="card mb-4">
    <div class="card-body">
        <h5>Red Flags to Watch For:</h5>
        <ul>
            <li>Login attempts at unusual hours</li>
            <li>Logins from unexpected locations</li>
            <li>Multiple failed login attempts</li>
            <li>Password changes you didn't make</li>
        </ul>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            If you see suspicious activity, change your password immediately and contact IT Security.
        </div>
    </div>
</div>

<h3>Exporting Your Activity Log</h3>
<div class="card mb-4">
    <div class="card-body">
        <p>Download your activity log for personal records:</p>
        <ul>
            <li><strong>CSV</strong> - Open in Excel</li>
            <li><strong>PDF</strong> - Official format</li>
            <li><strong>JSON</strong> - Technical analysis</li>
        </ul>
        <p>Click the <strong>"Export"</strong> button above the log table to download.</p>
    </div>
</div>
@endsection
