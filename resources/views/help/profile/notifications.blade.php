@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-bell text-kp-green me-2"></i> Notification Settings</h1>
<hr>

<div class="alert alert-kp-success">
    <i class="fas fa-info-circle me-2"></i>
    Configure how and when you receive alerts from DarkFibre CRM.
</div>

<h3>Notification Types</h3>
<div class="table-responsive mb-4">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr><th>Notification Type</th><th>Description</th><th>Channels</th></tr>
        </thead>
        <tbody>
            <tr><td>Tickets & Support</td><td>New tickets, status changes</td><td>Email, In-app</td></tr>
            <tr><td>Lease Management</td><td>New leases, renewals, expirations</td><td>Email, In-app</td></tr>
            <tr><td>Design Requests</td><td>New designs, approvals needed</td><td>Email, In-app</td></tr>
            <tr><td>CAK Compliance</td><td>Submission deadlines, approvals</td><td>Email, In-app</td></tr>
        </tbody>
    </table>
</div>

<h3>Configuring Notifications</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Go to <strong>My Profile</strong> → <strong>Notifications</strong> tab</li>
            <li>Toggle each notification type on/off</li>
            <li>Select preferred channels (Email, In-app, SMS)</li>
            <li>Click <strong>"Save Preferences"</strong></li>
        </ol>
    </div>
</div>

<h3>Setting Up Quiet Hours</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Go to <strong>Notifications → Quiet Hours</strong></li>
            <li>Enable <strong>"Quiet Hours"</strong></li>
            <li>Set start and end times (e.g., 22:00 - 07:00)</li>
            <li>Select which days apply</li>
            <li>Save settings</li>
        </ol>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-moon me-2"></i>
    <strong>Note:</strong> Critical alerts (outages, security) will still be sent during quiet hours.
</div>
@endsection
