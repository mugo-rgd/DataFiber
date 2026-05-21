@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-green text-white">
        <h4 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Account Manager Help Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-success">
            <i class="fas fa-chart-line me-2"></i>
            <strong>Your Portfolio:</strong> 7 active customers | 100% satisfaction rating
        </div>

        <h3>Portfolio Overview</h3>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h5>Active Customers</h5>
                    <h2>7</h2>
                    <small>In your portfolio</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h5>Satisfaction Rate</h5>
                    <h2 class="text-kp-green">100%</h2>
                    <small>Average rating</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h5>Payment Health</h5>
                    <h2 class="text-kp-yellow">0</h2>
                    <small>Pending collection</small>
                </div>
            </div>
        </div>

        <h3>Customer Management Tasks</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-users"></i> Customer Care</h5>
                        <ul>
                            <li>Schedule quarterly business reviews</li>
                            <li>Review customer feedback and satisfaction</li>
                            <li>Identify upsell opportunities</li>
                            <li>Monitor usage patterns</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-file-signature"></i> Contract Management</h5>
                        <ul>
                            <li>Track lease expiration dates</li>
                            <li>Process renewals and upgrades</li>
                            <li>Manage contract amendments</li>
                            <li>Document customer agreements</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <h3>Quick Actions</h3>
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-ticket-alt fa-2x text-kp-yellow"></i>
                <p><strong>New Ticket</strong><br>Register customer issues</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-money-bill-wave fa-2x text-kp-green"></i>
                <p><strong>Track Payment</strong><br>Follow up on debts</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-drafting-compass fa-2x text-kp-blue"></i>
                <p><strong>Requests</strong><br>Allocate design requests</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-file-signature fa-2x text-info"></i>
                <p><strong>Leases</strong><br>Manage network leases</p>
            </div>
        </div>

        <h3>Upcoming Activities</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li>📅 Q2 Business Review with top 3 customers - Schedule by May 30</li>
                    <li>📞 Follow-up calls for pending payments - 7 customers need contact</li>
                    <li>📧 Send customer satisfaction survey - Quarterly survey due</li>
                    <li>📝 Update customer contact information - Review monthly</li>
                </ul>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-chart-line me-2"></i>
            <strong>Tip:</strong> Use the Customer Insights module to identify at-risk customers before they churn.
        </div>

    </div>
</div>
@endsection
