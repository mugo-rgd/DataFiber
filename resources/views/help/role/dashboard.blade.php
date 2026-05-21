@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>
            {{ $roleDisplayName }} Dashboard Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Welcome to your {{ $roleDisplayName }} Dashboard!</strong>
            <p class="mb-0 mt-2">This guide will help you understand and navigate your role-specific dashboard.</p>
        </div>

        <h3>Dashboard Overview</h3>
        <div class="card mb-4">
            <div class="card-body">
                <p>Your dashboard provides real-time metrics and quick access to your most important tasks.</p>

                @if($role == 'finance')
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-info">$3.2M</h4>
                        <p>Pending Payments (USD)</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-info">KSh 43.4M</h4>
                        <p>Pending Payments (KES)</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-kp-yellow">104</h4>
                        <p>Overdue Invoices</p>
                    </div>
                </div>
                @elseif($role == 'technical_admin')
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-kp-blue">92</h4>
                        <p>Total Users</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-kp-green">286</h4>
                        <p>Active Leases</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-kp-yellow">2</h4>
                        <p>Pending Designs</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-danger">0</h4>
                        <p>Pending Tickets</p>
                    </div>
                </div>
                @elseif($role == 'designer')
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-kp-yellow">2</h4>
                        <p>Pending Design Requests</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-secondary">0</h4>
                        <p>In Progress</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-kp-green">0</h4>
                        <p>Completed Designs</p>
                    </div>
                </div>
                @elseif($role == 'debt_manager')
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-danger">$3.2M</h4>
                        <p>Total Overdue (USD)</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-danger">KSh 43.4M</h4>
                        <p>Total Overdue (KES)</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-kp-yellow">104</h4>
                        <p>Overdue Invoices</p>
                    </div>
                </div>
                @elseif($role == 'customer')
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-kp-yellow">33%</h4>
                        <p>Profile Completion</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-danger">6</h4>
                        <p>Documents Required</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-kp-green">0</h4>
                        <p>Active Tickets</p>
                    </div>
                </div>
                @elseif($role == 'ict_engineer')
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-kp-green">99.5%</h4>
                        <p>Network Uptime</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-kp-yellow">0</h4>
                        <p>Pending Tickets</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-danger">3</h4>
                        <p>Security Alerts</p>
                    </div>
                </div>
                @elseif($role == 'account_manager')
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-kp-blue">7</h4>
                        <p>Active Customers</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-kp-green">100%</h4>
                        <p>Satisfaction Rate</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-kp-yellow">0</h4>
                        <p>Pending Collection</p>
                    </div>
                </div>
                @else
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4>{{ $metrics['total_users'] ?? 'N/A' }}</h4>
                        <p>Total Users</p>
                    </div>
                    <div class="col-md-4">
                        <h4>{{ $metrics['active_leases'] ?? 'N/A' }}</h4>
                        <p>Active Leases</p>
                    </div>
                    <div class="col-md-4">
                        <h4>{{ $metrics['pending_designs'] ?? 'N/A' }}</h4>
                        <p>Pending Designs</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <h3>Quick Tips for Your Role</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    @foreach($quickTips as $tip)
                        <li><i class="fas fa-lightbulb text-kp-yellow me-2"></i> {{ $tip }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <h3>Need More Help?</h3>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('help.faq') }}" class="btn btn-outline-kp-primary w-100 mb-2">
                            <i class="fas fa-question-circle"></i> View FAQ
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('help.contact') }}" class="btn btn-outline-kp-success w-100 mb-2">
                            <i class="fas fa-headset"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
