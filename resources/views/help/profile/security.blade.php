@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-lock text-kp-yellow me-2"></i> Password & Security</h1>
<hr>

<div class="alert alert-kp-warning">
    <i class="fas fa-shield-alt me-2"></i>
    <strong>Security First:</strong> Your password is your first line of defense. Keep it strong and unique.
</div>

<h3>Changing Your Password</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Go to <strong>My Profile</strong> from the top-right menu</li>
            <li>Click on the <strong>"Security"</strong> or <strong>"Change Password"</strong> tab</li>
            <li>Enter your <strong>Current Password</strong></li>
            <li>Enter your <strong>New Password</strong></li>
            <li>Confirm your <strong>New Password</strong></li>
            <li>Click <strong>"Update Password"</strong></li>
        </ol>
    </div>
</div>

<h3>Password Requirements</h3>
<div class="card mb-4">
    <div class="card-body">
        <ul>
            <li>Minimum <strong>8 characters</strong> long</li>
            <li>At least <strong>1 uppercase letter</strong> (A-Z)</li>
            <li>At least <strong>1 lowercase letter</strong> (a-z)</li>
            <li>At least <strong>1 number</strong> (0-9)</li>
            <li>At least <strong>1 special character</strong> (!@#$%^&*)</li>
        </ul>
    </div>
</div>

<h3>Two-Factor Authentication (2FA)</h3>
<div class="card mb-4">
    <div class="card-body">
        <p>2FA adds an extra layer of security to your account.</p>
        <ol>
            <li>Go to <strong>Security Settings</strong> in your profile</li>
            <li>Click <strong>"Enable Two-Factor Authentication"</strong></li>
            <li>Scan the QR code with Google Authenticator</li>
            <li>Enter the 6-digit code from your app</li>
            <li>Click <strong>"Verify & Enable"</strong></li>
        </ol>
    </div>
</div>

<h3>Security Best Practices</h3>
<div class="card mb-4">
    <div class="card-body">
        <ul>
            <li>Change password every 90 days</li>
            <li>Use unique passwords for different systems</li>
            <li>Enable 2FA for critical access</li>
            <li>Log out when using shared computers</li>
            <li>Report suspicious activity immediately</li>
        </ul>
    </div>
</div>
@endsection
