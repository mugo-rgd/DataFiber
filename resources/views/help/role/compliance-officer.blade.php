@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>
            Compliance Officer Help Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-gavel me-2"></i>
            <strong>Your Role:</strong> Compliance Officer - Manage CAK quarterly compliance returns for ASP, CSP, and NFP licenses.
        </div>

        <h3>CAK Filing Deadlines</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="alert alert-kp-warning">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <strong>Current Quarter:</strong> Q1 2025/2026 (July - September) due by <strong>October 15, 2026</strong>
                </div>

                <h5>2026 Filing Calendar</h5>
                <ul>
                    <li><strong>Q1 (Jul-Sep):</strong> Submit by Oct 15, 2026</li>
                    <li><strong>Q2 (Oct-Dec):</strong> Submit by Jan 15, 2027</li>
                    <li><strong>Q3 (Jan-Mar):</strong> Submit by Apr 15, 2027</li>
                    <li><strong>Q4 (Apr-Jun):</strong> Submit by Jul 15, 2027</li>
                </ul>
            </div>
        </div>

        <h3>License Types & Status</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>License Type</th><th>License Number</th><th>Current Quarter</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ASP (Application Service Provider)</td>
                        <td>AFP:TL/NFP/00051</td>
                        <td>Q1 2025/2026</td>
                        <td><span class="badge bg-kp-green">Generated</span></td>
                        <td><a href="#" class="btn btn-sm btn-kp-primary">Submit</a></td>
                    </tr>
                    <tr>
                        <td>CSP (Content Service Provider)</td>
                        <td>CSP:TL/CSP/00451</td>
                        <td>Q4 2025/2026</td>
                        <td><span class="badge bg-kp-green">Generated</span></td>
                        <td><a href="#" class="btn btn-sm btn-kp-primary">Submit</a></td>
                    </tr>
                    <tr>
                        <td>NFP (Network Facility Provider)</td>
                        <td>NFP:TL/NFP/00051</td>
                        <td>Q4 2025/2026</td>
                        <td><span class="badge bg-kp-green">Generated</span></td>
                        <td><a href="#" class="btn btn-sm btn-kp-primary">Submit</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Compliance Workflow</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <i class="fas fa-pencil-alt fa-2x text-secondary"></i>
                        <p><strong>1. Draft</strong><br>Create return</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-file-pdf fa-2x text-kp-yellow"></i>
                        <p><strong>2. Generated</strong><br>PDF created</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-paper-plane fa-2x text-info"></i>
                        <p><strong>3. Submitted</strong><br>To CAK</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-check-circle fa-2x text-kp-green"></i>
                        <p><strong>4. Approved</strong><br>By CAK</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Creating a Compliance Return</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li>Go to <strong>CAK Compliance</strong> from the main menu</li>
                    <li>Select return type (ASP, CSP, or NFP)</li>
                    <li>Click <strong>"New Return"</strong></li>
                    <li>Fill in all required fields (marked with *)</li>
                    <li>Upload digital signature and company stamp</li>
                    <li>Click <strong>"Save Draft"</strong> to save progress</li>
                    <li>Click <strong>"Generate PDF"</strong> to create return</li>
                    <li>Review and click <strong>"Submit to CAK"</strong></li>
                </ol>
            </div>
        </div>

        <h3>Required Documents</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li>📄 Digital Signature (PNG/JPG - max 2MB)</li>
                    <li>📄 Company Stamp (PNG/JPG - max 2MB)</li>
                    <li>📄 Supporting Documents (PDF - max 5MB)</li>
                </ul>
            </div>
        </div>

        <div class="alert alert-kp-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Tip:</strong> Export compliance data quarterly for internal record keeping using the Export module.
        </div>

    </div>
</div>
@endsection
