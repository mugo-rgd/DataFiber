@extends('layouts.help')

@section('help-content')
<div class="text-center mb-5">
    <i class="fas fa-user-circle fa-4x text-kp-blue mb-3"></i>
    <h2>Profile Management Help Center</h2>
    <p class="lead">Manage your account settings, security, and preferences</p>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-user-edit fa-3x text-kp-blue mb-3"></i>
                <h5>Profile Information</h5>
                <p class="small">Update your personal details, contact information, and job title.</p>
                <a href="{{ route('help.profile.info') }}" class="btn btn-sm btn-outline-kp-primary">
                    <i class="fas fa-arrow-right"></i> Learn More
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-lock fa-3x text-kp-yellow mb-3"></i>
                <h5>Password & Security</h5>
                <p class="small">Change your password, enable 2FA, and review security settings.</p>
                <a href="{{ route('help.profile.security') }}" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-arrow-right"></i> Learn More
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-bell fa-3x text-kp-green mb-3"></i>
                <h5>Notifications</h5>
                <p class="small">Configure email alerts, system notifications, and reminder settings.</p>
                <a href="{{ route('help.profile.notifications') }}" class="btn btn-sm btn-outline-kp-success">
                    <i class="fas fa-arrow-right"></i> Learn More
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-history fa-3x text-info mb-3"></i>
                <h5>Activity Log</h5>
                <p class="small">View your login history, actions taken, and security events.</p>
                <a href="{{ route('help.profile.activity') }}" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-arrow-right"></i> Learn More
                </a>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mt-3">
    <i class="fas fa-shield-alt me-2"></i>
    <strong>Security Tip:</strong> Regularly review your profile information and change your password every 90 days for optimal security.
</div>
@endsection
