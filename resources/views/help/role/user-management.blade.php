@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-users me-2"></i>
            User Management Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Current Statistics</h3>
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="text-kp-blue">92</h2>
                <p>Total Registered Users</p>
            </div>
        </div>

        <h3>Creating a New User</h3>
        <div class="card mb-4">
            <div class="card-body">
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
            </div>
        </div>

        <h3>User Roles & Permissions</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Role</th><th>Access Level</th><th>Typical Users</th></tr>
                </thead>
                <tbody>
                    <td>Admin</td><td>Full access</td><td>System administrators</td></tr>
                    <tr><td>Technical Admin</td><td>Network operations</td><td>Engineers</td></tr>
                    <tr><td>Finance</td><td>Billing & invoices</td><td>Accountants</td></tr>
                    <tr><td>Designer</td><td>Design & quotations</td><td>Pre-sales engineers</td></tr>
                </tbody>
            </table>
        </div>

        <div class="alert alert-kp-warning">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Note:</strong> Only assign roles that are necessary for the user's job function.
        </div>

    </div>
</div>
@endsection
