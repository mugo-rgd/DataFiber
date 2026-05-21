@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h4 class="mb-0">
            <i class="fas fa-id-card me-2"></i>
            Profile Setup Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Complete your profile to access all features and manage your account.
        </div>

        <h3>Profile Sections</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-building text-kp-blue"></i> Company Information</h5>
                        <ul>
                            <li>Company Name</li>
                            <li>Business Registration Number</li>
                            <li>Tax PIN</li>
                            <li>Industry Type</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-address-card text-kp-green"></i> Contact Information</h5>
                        <ul>
                            <li>Physical Address</li>
                            <li>Postal Address</li>
                            <li>Phone Numbers</li>
                            <li>Email Address</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <h3>Required Documents</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr><th>Document</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Business Registration Certificate</td>
                                <td><span class="badge bg-danger">Missing</span></td>
                                <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                            </tr>
                            <tr>
                                <td>KRA PIN Certificate</td>
                                <td><span class="badge bg-danger">Missing</span></td>
                                <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                            </tr>
                            <tr>
                                <td>Trade License</td>
                                <td><span class="badge bg-danger">Missing</span></td>
                                <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                            </tr>
                            <tr>
                                <td>CAK License</td>
                                <td><span class="badge bg-danger">Missing</span></td>
                                <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                            </tr>
                            <tr>
                                <td>CR12 Certificate</td>
                                <td><span class="badge bg-danger">Missing</span></td>
                                <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                            </tr>
                            <tr>
                                <td>Tax Compliance Certificate</td>
                                <td><span class="badge bg-danger">Missing</span></td>
                                <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h3>How to Update Your Profile</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Click on <strong>"Profile"</strong> from the main menu</li>
                    <li>Click the <strong>"Edit Profile"</strong> button</li>
                    <li>Update your information in each section</li>
                    <li>Click <strong>"Save Changes"</strong></li>
                    <li>Upload required documents in the <strong>"Documents"</strong> tab</li>
                </ol>
            </div>
        </div>

        <div class="alert alert-kp-success">
            <i class="fas fa-gift me-2"></i>
            <strong>Complete your profile</strong> to enable all features including support tickets and invoice management.
        </div>

    </div>
</div>
@endsection
