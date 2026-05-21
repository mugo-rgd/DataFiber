@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            <i class="fas fa-file-upload me-2"></i>
            Documents Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Required Documents Missing!</strong> Please upload the following documents to complete your profile.
        </div>

        <h3>Required Documents</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Document Type</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Business Registration Certificate</td>
                        <td><span class="badge bg-danger">Not Uploaded</span></td>
                        <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                    </tr>
                    <tr>
                        <td>KRA PIN Certificate</td>
                        <td><span class="badge bg-danger">Not Uploaded</span></td>
                        <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                    </tr>
                    <tr>
                        <td>Trade License</td>
                        <td><span class="badge bg-danger">Not Uploaded</span></td>
                        <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                    </tr>
                    <tr>
                        <td>CAK License</td>
                        <td><span class="badge bg-danger">Not Uploaded</span></td>
                        <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                    </tr>
                    <tr>
                        <td>CR12 Certificate</td>
                        <td><span class="badge bg-danger">Not Uploaded</span></td>
                        <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                    </tr>
                    <tr>
                        <td>Tax Compliance Certificate</td>
                        <td><span class="badge bg-danger">Not Uploaded</span></td>
                        <td><button class="btn btn-sm btn-kp-primary">Upload</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>How to Upload Documents</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>My Documents</strong> from the main menu</li>
                    <li>Click <strong>"Upload Document"</strong></li>
                    <li>Select document type from dropdown</li>
                    <li>Choose file from your computer (PDF, JPG, PNG)</li>
                    <li>Click <strong>"Upload"</strong></li>
                    <li>Wait for admin approval (24-48 hours)</li>
                </ol>
            </div>
        </div>

        <h3>Document Requirements</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li><strong>Format:</strong> PDF, JPG, JPEG, PNG</li>
                    <li><strong>Size:</strong> Maximum 5MB per file</li>
                    <li><strong>Quality:</strong> Clear and readable</li>
                    <li><strong>Validity:</strong> Must be current (not expired)</li>
                </ul>
            </div>
        </div>

        <div class="alert alert-kp-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>After Upload:</strong> Documents will be reviewed within 24-48 hours. You'll receive email notification once approved.
        </div>

    </div>
</div>
@endsection
