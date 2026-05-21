@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-yellow text-dark">
        <h4 class="mb-0">
            <i class="fas fa-ticket-alt me-2"></i>
            Support Tickets Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-warning">
            <i class="fas fa-info-circle me-2"></i>
            Submit and track support tickets for technical issues, billing inquiries, or general questions.
        </div>

        <h3>Creating a Support Ticket</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>Support</strong> from the main menu</li>
                    <li>Click <strong>"New Ticket"</strong></li>
                    <li>Select the appropriate category:
                        <ul>
                            <li>🔴 <strong>Outage</strong> - Service interruption (Emergency)</li>
                            <li>🟡 <strong>Performance</strong> - Slow speeds, latency issues</li>
                            <li>🔵 <strong>Billing</strong> - Invoice questions, payment issues</li>
                            <li>⚪ <strong>General</strong> - Other inquiries</li>
                        </ul>
                    </li>
                    <li>Describe your issue in detail</li>
                    <li>Attach screenshots if applicable (max 5MB)</li>
                    <li>Click <strong>"Submit"</strong></li>
                </ol>
            </div>
        </div>

        <h3>Ticket Response Times (SLA)</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Priority</th><th>Response Time</th><th>Resolution Time</th></tr>
                </thead>
                <tbody>
                    <tr><td class="bg-danger text-white">Critical (Outage)</td><td>15 minutes</td><td>4 hours</td></tr>
                    <tr><td class="bg-kp-yellow">High</td><td>1 hour</td><td>8 hours</td></tr>
                    <tr><td class="bg-info text-white">Medium</td><td>4 hours</td><td>24 hours</td></tr>
                    <tr><td class="bg-secondary text-white">Low</td><td>24 hours</td><td>72 hours</td></tr>
                </tbody>
            </table>
        </div>

        <h3>Checking Ticket Status</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li><strong>Open</strong> - Ticket received, awaiting response</li>
                    <li><strong>In Progress</strong> - Being worked on by support team</li>
                    <li><strong>Resolved</strong> - Issue fixed, awaiting your confirmation</li>
                    <li><strong>Closed</strong> - Completed and confirmed</li>
                </ul>
            </div>
        </div>

        <h3>Tips for Faster Resolution</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li>Provide detailed description of the issue</li>
                    <li>Include error messages or screenshots</li>
                    <li>Specify affected services or locations</li>
                    <li>Mention when the issue started</li>
                    <li>Provide contact information for follow-up</li>
                </ul>
            </div>
        </div>

        <div class="alert alert-kp-success">
            <i class="fas fa-headset me-2"></i>
            <strong>Need Immediate Help?</strong> Call our support hotline at <strong>020 3201 000</strong> (24/7 for outages)
        </div>

    </div>
</div>
@endsection
