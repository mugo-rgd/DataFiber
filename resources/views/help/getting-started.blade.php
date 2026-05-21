@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-rocket text-kp-blue me-2"></i> Getting Started</h1>
<hr>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    This guide will help you navigate the DarkFibre CRM system and submit your first compliance return.
</div>

<h3>1. Logging In</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Navigate to <code>https://darkfiber.kplc.co.ke/login</code></li>
            <li>Enter your registered email address and password</li>
            <li>Click the <strong>"Login"</strong> button</li>
        </ol>
        <div class="alert alert-kp-warning">
            <i class="fas fa-key me-2"></i>
            <strong>First time logging in?</strong> You will be prompted to change your password.
        </div>
    </div>
</div>

<h3>2. Understanding the Dashboard</h3>
<div class="card mb-4">
    <div class="card-body">
        <p>The dashboard gives you a quick overview of all compliance activities:</p>
        <ul>
            <li><strong>Summary Cards</strong> - Show total ASP, CSP, and NFP returns</li>
            <li><strong>Status Cards</strong> - Display counts by status (Draft, Generated, Submitted, Approved)</li>
            <li><strong>Recent Returns</strong> - Shows the latest 10 submissions</li>
            <li><strong>New Return Button</strong> - Quick access to create a new compliance return</li>
        </ul>
    </div>
</div>

<h3>3. Status Meanings</h3>
<div class="table-responsive mb-4">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr><th>Status</th><th>Color</th><th>Meaning</th><th>Action Required</th></tr>
        </thead>
        <tbody>
            <tr><td>Draft</td><td><span class="badge bg-secondary">Gray</span></td><td>Return saved but not submitted</td><td>Complete and submit</td></tr>
            <tr><td>Generated</td><td><span class="badge bg-dark">Dark</span></td><td>PDF generated</td><td>Review and submit to CAK</td></tr>
            <tr><td>Submitted</td><td><span class="badge bg-kp-yellow">Yellow</span></td><td>Sent to internal review</td><td>Awaiting approval</td></tr>
            <tr><td>Sent to CAK</td><td><span class="badge bg-info">Blue</span></td><td>Forwarded to regulator</td><td>Waiting response</td></tr>
            <tr><td>Approved</td><td><span class="badge bg-kp-green">Green</span></td><td>CAK approved</td><td>No action needed</td></tr>
            <tr><td>Rejected</td><td><span class="badge bg-danger">Red</span></td><td>Return rejected</td><td>Revise and resubmit</td></tr>
        </tbody>
    </table>
</div>

<h3>4. Creating Your First Return</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Click the <strong>"New Return"</strong> button on the dashboard</li>
            <li>Select the appropriate return type (ASP, CSP, or NFP)</li>
            <li>Fill in all required fields (marked with <span class="text-danger">*</span>)</li>
            <li>Upload your signature and company stamp</li>
            <li>Click <strong>"Save Draft"</strong> to save progress, or <strong>"Submit"</strong> to submit</li>
        </ol>
    </div>
</div>

<h3>5. Next Steps</h3>
<div class="row">
    <div class="col-md-4">
        <a href="{{ route('help.asp') }}" class="btn btn-kp-primary w-100 mb-2">
            <i class="fas fa-server"></i> ASP Guide
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('help.csp') }}" class="btn btn-kp-success w-100 mb-2">
            <i class="fas fa-envelope"></i> CSP Guide
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('help.nfp') }}" class="btn btn-kp-warning w-100 mb-2">
            <i class="fas fa-network-wired"></i> NFP Guide
        </a>
    </div>
</div>
@endsection
