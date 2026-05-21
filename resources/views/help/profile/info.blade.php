@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-user-edit text-kp-blue me-2"></i> Profile Information</h1>
<hr>

<div class="alert alert-kp-primary">
    <i class="fas fa-info-circle me-2"></i>
    Your profile contains your personal and professional information used across the DarkFibre CRM system.
</div>

<h3>Accessing Your Profile</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Click on your <strong>name/avatar</strong> in the top-right corner of any page</li>
            <li>Select <strong>"My Profile"</strong> from the dropdown menu</li>
            <li>You'll see your profile dashboard with all your information</li>
        </ol>
    </div>
</div>

<h3>Profile Fields Explained</h3>
<div class="table-responsive mb-4">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr><th>Field</th><th>Description</th><th>Required?</th></tr>
        </thead>
        <tbody>
            <tr><td>Full Name</td><td>Your complete legal name</td><td>Yes</td></tr>
            <tr><td>Email Address</td><td>Primary email for login and notifications</td><td>Yes</td></tr>
            <tr><td>Phone Number</td><td>Office/Work phone for contact</td><td>No</td></tr>
            <tr><td>Job Title</td><td>Your position in the organization</td><td>Yes</td></tr>
            <tr><td>Department</td><td>Your team or division</td><td>Yes</td></tr>
        </tbody>
    </table>
</div>

<h3>Updating Your Profile</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Go to <strong>My Profile</strong> from the top-right menu</li>
            <li>Click the <strong>"Edit Profile"</strong> button</li>
            <li>Update the fields you want to change</li>
            <li>Click <strong>"Save Changes"</strong> at the bottom</li>
        </ol>
    </div>
</div>

<h3>Uploading Profile Photo</h3>
<div class="card mb-4">
    <div class="card-body">
        <ol>
            <li>Go to <strong>My Profile</strong></li>
            <li>Hover over the default avatar or current photo</li>
            <li>Click the <strong>camera icon 📷</strong> that appears</li>
            <li>Select an image file from your computer</li>
            <li>Click <strong>"Save"</strong></li>
        </ol>
        <p><strong>Requirements:</strong> JPG, PNG (max 2MB)</p>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-question-circle me-2"></i>
    <strong>Note:</strong> Email changes require admin approval. Contact your system administrator to update your email address.
</div>
@endsection
