@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-green text-white">
        <h4 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>
            Account Manager Admin Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-success">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Your Role:</strong> Account Manager Admin - Oversee account managers, marketing analytics, and customer insights.
        </div>

        <h3>Dashboard Overview</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h2 class="text-kp-blue">92</h2>
                        <p>Total Users</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-kp-green">286</h2>
                        <p>Active Leases</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-kp-yellow">2</h2>
                        <p>Pending Designs</p>
                    </div>
                    <div class="col-md-3">
                        <h2 class="text-info">104</h2>
                        <p>Pending Billings</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Key Responsibilities</h3>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-kp-blue mb-2"></i>
                        <h5>Team Management</h5>
                        <p class="small">Oversee account managers and team performance</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-2x text-kp-green mb-2"></i>
                        <h5>Marketing Analytics</h5>
                        <p class="small">Track marketing campaigns and customer acquisition</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-lightbulb fa-2x text-kp-yellow mb-2"></i>
                        <h5>Customer Insights</h5>
                        <p class="small">Analyze customer behavior and preferences</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Quick Actions</h3>
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-file-signature fa-2x text-kp-blue"></i>
                <p><strong>Manage Leases</strong><br>Approve and send leases</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-users fa-2x text-kp-green"></i>
                <p><strong>Manage Users</strong><br>User management</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-pencil-ruler fa-2x text-kp-yellow"></i>
                <p><strong>Design Requests</strong><br>Assign engineers</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-file-signature fa-2x text-info"></i>
                <p><strong>Quotations</strong><br>Approve and send</p>
            </div>
        </div>

        <h3>Team Management</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li><strong>Account Manager Performance</strong> - Track team KPIs and targets</li>
                    <li><strong>Customer Assignment</strong> - Assign customers to account managers</li>
                    <li><strong>Sales Pipeline</strong> - Monitor opportunities and conversion rates</li>
                    <li><strong>Customer Satisfaction</strong> - Review feedback and ratings</li>
                    <li><strong>Commission Tracking</strong> - Manage account manager commissions</li>
                </ul>
            </div>
        </div>

        <h3>Recent Activity</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="fas fa-user-plus text-kp-green me-2"></i>
                        New user registered: PHPLAVATEC SOLUTIONS LIMITED
                        <small class="text-muted d-block">2 months ago</small>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-user-plus text-kp-green me-2"></i>
                        New user registered: Nelson Orumi
                        <small class="text-muted d-block">3 months ago</small>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-user-plus text-kp-green me-2"></i>
                        New user registered: Stephen Onditi Nyamichaba
                        <small class="text-muted d-block">3 months ago</small>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-user-plus text-kp-green me-2"></i>
                        New user registered: Michael Chege
                        <small class="text-muted d-block">3 months ago</small>
                    </li>
                </ul>
            </div>
        </div>

        <h3>Reports & Analytics</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-chart-bar text-kp-blue"></i> Available Reports</h5>
                        <ul>
                            <li>Team Performance Dashboard</li>
                            <li>Customer Acquisition Report</li>
                            <li>Churn Analysis</li>
                            <li>Revenue by Account Manager</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-download text-kp-green"></i> Export Options</h5>
                        <ul>
                            <li>Excel (.xlsx)</li>
                            <li>CSV (.csv)</li>
                            <li>PDF (.pdf)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-chart-line me-2"></i>
            <strong>Tip:</strong> Use the Customer Insights module to identify cross-selling and upselling opportunities.
        </div>

        <h3>Quick Links</h3>
        <div class="row">
            <div class="col-md-4">
                <a href="{{ url('/users') }}" class="btn btn-outline-kp-primary w-100 mb-2">
                    <i class="fas fa-users"></i> Manage Users
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ url('/reports/team-performance') }}" class="btn btn-outline-kp-success w-100 mb-2">
                    <i class="fas fa-chart-line"></i> Team Performance
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ url('/customers/insights') }}" class="btn btn-outline-info w-100 mb-2">
                    <i class="fas fa-lightbulb"></i> Customer Insights
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
