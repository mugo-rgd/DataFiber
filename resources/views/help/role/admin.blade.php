@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            <i class="fas fa-shield-alt me-2"></i>
            System Administrator Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-danger">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Your Role:</strong> System Administrator - Manage users, system settings, and overall platform operations.
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
                        <p>Pending Invoices</p>
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
                        <h5>User Management</h5>
                        <p class="small">Create, edit, and manage user accounts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-cog fa-2x text-secondary mb-2"></i>
                        <h5>System Settings</h5>
                        <p class="small">Configure system parameters and preferences</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-database fa-2x text-info mb-2"></i>
                        <h5>Backup Management</h5>
                        <p class="small">Ensure regular system backups</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Quick Actions</h3>
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-user-plus fa-2x text-kp-blue"></i>
                <p><strong>Add User</strong><br>Create new system users</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-cog fa-2x text-secondary"></i>
                <p><strong>System Settings</strong><br>Configure parameters</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-chart-bar fa-2x text-kp-green"></i>
                <p><strong>Reports</strong><br>View analytics</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-database fa-2x text-info"></i>
                <p><strong>Backup</strong><br>Manage backups</p>
            </div>
        </div>

        <h3>User Management</h3>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Creating a New User</h5>
                <ol>
                    <li>Go to <strong>Users → Add User</strong></li>
                    <li>Fill in user details:
                        <ul>
                            <li>Full Name</li>
                            <li>Email Address</li>
                            <li>Phone Number</li>
                            <li>Role (Admin, Finance, Designer, etc.)</li>
                        </ul>
                    </li>
                    <li>Set temporary password</li>
                    <li>Click <strong>"Create User"</strong></li>
                </ol>

                <h5 class="mt-3">User Roles & Permissions</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr><th>Role</th><th>Access Level</th><th>Typical Users</th></tr>
                        </thead>
                        <tbody>
                            <tr><td class="bg-danger text-white">Admin</td><td>Full access</td><td>System administrators</td></tr>
                            <tr><td class="bg-kp-blue text-white">Technical Admin</td><td>Network operations</td><td>Engineers</td></tr>
                            <tr><td class="bg-info text-white">Finance</td><td>Billing & invoices</td><td>Accountants</td></tr>
                            <tr><td class="bg-kp-yellow">Designer</td><td>Design & quotations</td><td>Pre-sales engineers</td></tr>
                            <tr><td class="bg-kp-green text-white">Account Manager</td><td>Customer management</td><td>Sales team</td></tr>
                            <tr><td class="bg-secondary text-white">Viewer</td><td>Read-only</td><td>Auditors</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h3>System Administration Tasks</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li><strong>User Management</strong> - Create, edit, and disable user accounts</li>
                    <li><strong>Role Assignment</strong> - Assign and manage user permissions</li>
                    <li><strong>System Monitoring</strong> - Check system logs and performance</li>
                    <li><strong>Backup Management</strong> - Ensure regular database backups</li>
                    <li><strong>Security Updates</strong> - Apply system patches and updates</li>
                    <li><strong>Audit Logs</strong> - Review user activity and security logs</li>
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
                    <li class="list-group-item">
                        <i class="fas fa-user-plus text-kp-green me-2"></i>
                        New user registered: Eric Wekesa Wanjala
                        <small class="text-muted d-block">3 months ago</small>
                    </li>
                </ul>
            </div>
        </div>

        <h3>Backup Management</h3>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Best Practices</h5>
                <ul>
                    <li>Schedule daily automated backups</li>
                    <li>Store backups in multiple locations (local + cloud)</li>
                    <li>Test restore process monthly</li>
                    <li>Keep backups for 30 days minimum</li>
                    <li>Document recovery procedures</li>
                </ul>
                <div class="alert alert-kp-warning mt-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> Never skip scheduled backups. Data loss can be catastrophic.
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Review system logs weekly for any unusual activity or errors.
        </div>

        <h3>Quick Links</h3>
        <div class="row">
            <div class="col-md-4">
                <a href="{{ url('/users') }}" class="btn btn-outline-kp-primary w-100 mb-2">
                    <i class="fas fa-users"></i> Manage Users
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ url('/settings') }}" class="btn btn-outline-secondary w-100 mb-2">
                    <i class="fas fa-cog"></i> System Settings
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ url('/backup') }}" class="btn btn-outline-info w-100 mb-2">
                    <i class="fas fa-database"></i> Backup Manager
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
