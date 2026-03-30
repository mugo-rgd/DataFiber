@extends('layouts.app')

@section('title', 'Generate Acceptance Certificate - ICT Engineer')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-award text-success me-2"></i>Generate Acceptance Certificate
                    </h1>
                    <p class="text-muted mb-0">ICT Engineer - Create Acceptance Certificate for Design Request</p>
                </div>
                <a href="{{ route('ictengineer.requests') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Requests
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-award me-2"></i>
                        Acceptance Certificate Form
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Request Information -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-1">Design Request Details</h6>
                                <p class="mb-0">
                                    <strong>Request #:</strong> {{ $request->request_number ?? $request->id }} |
                                    <strong>Customer:</strong> {{ $request->customer->name ?? 'N/A' }} |
                                    <strong>Title:</strong> {{ $request->title ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form id="acceptanceCertificateForm" method="POST" action="{{ route('ictengineer.certificates.acceptance.store', $request) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $request->id }}">

                        <!-- Certificate Header -->
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-primary">Certificate of Acceptance</h4>
                            <p class="text-muted">THE KENYA POWER & LIGHTING COMPANY PLC</p>
                        </div>

                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">To (Company/Organization)</label>
                                        <input type="text" class="form-control" name="to_company"
                                               value="{{ $request->customer->name ?? '' }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Certificate Reference Number</label>
                                        <input type="text" class="form-control" name="certificate_ref"
                                               value="KPLC/AC/{{ date('Y') }}/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}" readonly required>
                                        <small class="text-muted">Auto-generated reference number</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Name of the Route</label>
                                        <input type="text" class="form-control" name="route_name" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Name of the Link</label>
                                        <input type="text" class="form-control" name="link_name"
                                               value="{{ $request->title ?? '' }}" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Cable Type</label>
                                        <select class="form-select" name="cable_type" required>
                                            <option value="">Select Cable Type</option>
                                            <option value="ADSS">ADSS</option>
                                            <option value="OPGW">OPGW</option>
                                            <option value="Figure-8">Figure 8</option>
                                            <option value="Underground">Underground</option>
                                            <option value="Duct">Duct</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Distance (KM)</label>
                                        <input type="number" step="0.001" class="form-control" name="distance" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Number of Cores</label>
                                        <input type="number" class="form-control" name="cores_count" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Effective Date</label>
                                        <input type="date" class="form-control" name="effective_date"
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Parties Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-building me-2"></i>Parties Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="bg-primary text-white p-2 rounded mb-3">LESSOR</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Company Name</label>
                                                <input type="text" class="form-control"
                                                       value="THE KENYA POWER & LIGHTING COMPANY PLC" readonly>
                                            </div>
                                            <input type="hidden" name="lessor" value="THE KENYA POWER & LIGHTING COMPANY PLC">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="bg-success text-white p-2 rounded mb-3">LESSEE</h6>
                                            <div class="mb-3">
                                                <label class="form-label required">Company Name</label>
                                                <input type="text" class="form-control" name="lessee"
                                                       value="{{ $request->customer->name ?? '' }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <input type="text" class="form-control" name="lessee_address">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Contact Person</label>
                                                <input type="text" class="form-control" name="lessee_contact">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kenya Power Signatories -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-signature me-2"></i>Kenya Power Signatories</h6>
                            </div>
                            <div class="card-body">
                                <!-- Witness 1 -->
                                <div class="signatory-card border rounded p-3 mb-3">
                                    <div class="signatory-header bg-primary text-white p-2 rounded mb-3">
                                        1. INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Name</label>
                                            <input type="text" class="form-control" name="witness1_name" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Date</label>
                                            <input type="date" class="form-control" name="witness1_date"
                                                   value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Signature Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="witness1_signature"
                                                       accept="image/*" id="witness1Signature">
                                                <small class="text-muted">Upload signature image (PNG, JPG)</small>
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="witness1Preview" style="min-height: 80px;">
                                                Signature Preview
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Stamp Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="witness1_stamp"
                                                       accept="image/*" id="witness1Stamp">
                                                <small class="text-muted">Upload stamp image (PNG, JPG)</small>
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="witness1StampPreview" style="min-height: 80px;">
                                                Stamp Preview
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Witness 2 -->
                                <div class="signatory-card border rounded p-3 mb-3">
                                    <div class="signatory-header bg-primary text-white p-2 rounded mb-3">
                                        2. TELECOM LEAD ENGINEER
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Name</label>
                                            <input type="text" class="form-control" name="witness2_name" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Date</label>
                                            <input type="date" class="form-control" name="witness2_date"
                                                   value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Signature Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="witness2_signature"
                                                       accept="image/*" id="witness2Signature">
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="witness2Preview" style="min-height: 80px;">
                                                Signature Preview
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Stamp Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="witness2_stamp"
                                                       accept="image/*" id="witness2Stamp">
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="witness2StampPreview" style="min-height: 80px;">
                                                Stamp Preview
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Witness 3 -->
                                <div class="signatory-card border rounded p-3 mb-3">
                                    <div class="signatory-header bg-primary text-white p-2 rounded mb-3">
                                        3. TELECOM MANAGER
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Name</label>
                                            <input type="text" class="form-control" name="witness3_name" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Date</label>
                                            <input type="date" class="form-control" name="witness3_date"
                                                   value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Signature Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="witness3_signature"
                                                       accept="image/*" id="witness3Signature">
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="witness3Preview" style="min-height: 80px;">
                                                Signature Preview
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Stamp Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="witness3_stamp"
                                                       accept="image/*" id="witness3Stamp">
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="witness3StampPreview" style="min-height: 80px;">
                                                Stamp Preview
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LESSEE Signatories -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Lessee Signatories</h6>
                            </div>
                            <div class="card-body">
                                <!-- Lessee 1 -->
                                <div class="signatory-card border rounded p-3 mb-3">
                                    <div class="signatory-header bg-success text-white p-2 rounded mb-3">
                                        1. LESSEE - LEAD ENGINEER / TECHNICAL REPRESENTATIVE
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Name</label>
                                            <input type="text" class="form-control" name="lessee1_name" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Date</label>
                                            <input type="date" class="form-control" name="lessee1_date"
                                                   value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Signature Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="lessee1_signature"
                                                       accept="image/*" id="lessee1Signature">
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="lessee1Preview" style="min-height: 80px;">
                                                Signature Preview
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Stamp Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="lessee1_stamp"
                                                       accept="image/*" id="lessee1Stamp">
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="lessee1StampPreview" style="min-height: 80px;">
                                                Stamp Preview
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lessee 2 -->
                                <div class="signatory-card border rounded p-3 mb-3">
                                    <div class="signatory-header bg-success text-white p-2 rounded mb-3">
                                        2. LESSEE - MANAGER
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Name</label>
                                            <input type="text" class="form-control" name="lessee2_name" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Date</label>
                                            <input type="date" class="form-control" name="lessee2_date"
                                                   value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Signature Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="lessee2_signature"
                                                       accept="image/*" id="lessee2Signature">
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="lessee2Preview" style="min-height: 80px;">
                                                Signature Preview
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Stamp Upload</label>
                                            <div class="file-upload border-dashed rounded p-3">
                                                <input type="file" class="form-control" name="lessee2_stamp"
                                                       accept="image/*" id="lessee2Stamp">
                                            </div>
                                            <div class="signature-preview border rounded p-2 mt-2" id="lessee2StampPreview" style="min-height: 80px;">
                                                Stamp Preview
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Documents -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Supporting Documents</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Test Report (PDF)</label>
                                        <div class="file-upload border-dashed rounded p-3">
                                            <input type="file" class="form-control" name="test_report"
                                                   accept=".pdf" required>
                                            <small class="text-muted">Upload final test report (PDF format, max 5MB)</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Additional Documents</label>
                                        <div class="file-upload border-dashed rounded p-3">
                                            <input type="file" class="form-control" name="additional_documents[]"
                                                   multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                            <small class="text-muted">You can upload multiple files if needed</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Declaration -->
                        <div class="alert alert-info mb-4">
                            <h6><i class="fas fa-check-circle me-2"></i>Declaration</h6>
                            <p class="mb-0">
                                We hereby certify that the above dark fiber link comprising the Purchased Capacity has been completed,
                                tested and commissioned to Acceptable Standards. This Certificate of Acceptance is a confirmation of
                                the same and the date indicated herein is the Effective Date.
                            </p>
                        </div>

                        <!-- Preview Section -->
                        <div class="preview-section card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Certificate Preview</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Reference:</strong> <span id="previewRef">KPLC/AC/{{ date('Y') }}/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                                        <p><strong>To:</strong> <span id="previewTo">{{ $request->customer->name ?? 'N/A' }}</span></p>
                                        <p><strong>Link Name:</strong> <span id="previewLink">{{ $request->title ?? 'N/A' }}</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Effective Date:</strong> <span id="previewDate">{{ date('F d, Y') }}</span></p>
                                        <p><strong>Signatories:</strong> <span id="previewSignatories">0 of 5 completed</span></p>
                                        <p><strong>Status:</strong> <span class="badge bg-success">Ready to Generate</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center border-top pt-4">
                            <a href="{{ route('ictengineer.requests') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success" id="generateCertificateBtn">
                                <i class="fas fa-file-certificate me-2"></i>Generate Acceptance Certificate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Copy the JavaScript from your modal code for form handling
    document.addEventListener('DOMContentLoaded', function() {
        // Acceptance Certificate Form Handling
        const acceptanceForm = document.getElementById('acceptanceCertificateForm');

        if (acceptanceForm) {
            // Preview updates
            const previewTo = document.getElementById('previewTo');
            const previewLink = document.getElementById('previewLink');
            const previewDate = document.getElementById('previewDate');
            const previewSignatories = document.getElementById('previewSignatories');

            // Update To company preview
            const toCompanyInput = acceptanceForm.querySelector('input[name="to_company"]');
            if (toCompanyInput && previewTo) {
                toCompanyInput.addEventListener('input', function() {
                    previewTo.textContent = this.value || 'N/A';
                });
            }

            // Update Link name preview
            const linkNameInput = acceptanceForm.querySelector('input[name="link_name"]');
            if (linkNameInput && previewLink) {
                linkNameInput.addEventListener('input', function() {
                    previewLink.textContent = this.value || 'N/A';
                });
            }

            // Update Effective Date preview
            const effectiveDateInput = acceptanceForm.querySelector('input[name="effective_date"]');
            if (effectiveDateInput && previewDate) {
                effectiveDateInput.addEventListener('change', function() {
                    const date = new Date(this.value);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    previewDate.textContent = formattedDate;
                });

                // Trigger initial update
                if (effectiveDateInput.value) {
                    effectiveDateInput.dispatchEvent(new Event('change'));
                }
            }

            // Signature preview functionality
            const signatureInputs = [
                'witness1Signature', 'witness2Signature', 'witness3Signature',
                'lessee1Signature', 'lessee2Signature'
            ];

            signatureInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                const previewId = inputId + 'Preview';
                const preview = document.getElementById(previewId);

                if (input && preview) {
                    input.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file && file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid" alt="Signature Preview">';
                                preview.style.minHeight = '80px';
                                preview.classList.add('text-center');
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });

            // Stamp preview functionality
            const stampInputs = [
                'witness1Stamp', 'witness2Stamp', 'witness3Stamp',
                'lessee1Stamp', 'lessee2Stamp'
            ];

            stampInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                const previewId = inputId + 'Preview';
                const preview = document.getElementById(previewId);

                if (input && preview) {
                    input.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file && file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid" alt="Stamp Preview">';
                                preview.style.minHeight = '80px';
                                preview.classList.add('text-center');
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });

            // Update signatory count
            function updateSignatoryCount() {
                const nameInputs = [
                    'witness1_name', 'witness2_name', 'witness3_name',
                    'lessee1_name', 'lessee2_name'
                ];

                let completed = 0;
                nameInputs.forEach(name => {
                    const input = acceptanceForm.querySelector(`input[name="${name}"]`);
                    if (input && input.value.trim()) {
                        completed++;
                    }
                });

                if (previewSignatories) {
                    previewSignatories.textContent = `${completed} of 5 signatories completed`;
                }
            }

            // Listen to all name inputs
            const nameInputs = acceptanceForm.querySelectorAll('input[name*="_name"]');
            nameInputs.forEach(input => {
                input.addEventListener('input', updateSignatoryCount);
            });

            // Initialize signatory count
            updateSignatoryCount();

            // File upload validation
            const fileInputs = acceptanceForm.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const files = this.files;
                    const maxSize = 10 * 1024 * 1024; // 10MB

                    if (files.length > 0) {
                        for (let file of files) {
                            if (file.size > maxSize) {
                                alert(`File ${file.name} is too large. Maximum size is 10MB.`);
                                this.value = '';
                                break;
                            }
                        }
                    }
                });
            });

            // Form submission validation
            acceptanceForm.addEventListener('submit', function(e) {
                // Check if test report is uploaded
                const testReport = acceptanceForm.querySelector('input[name="test_report"]');
                if (!testReport || !testReport.files.length) {
                    e.preventDefault();
                    alert('Please upload the test report.');
                    return false;
                }

                // Validate at least one signature is filled
                const signatureInputs = acceptanceForm.querySelectorAll('input[name*="_name"]');
                let hasSignatures = false;
                signatureInputs.forEach(input => {
                    if (input.value.trim()) {
                        hasSignatures = true;
                    }
                });

                if (!hasSignatures) {
                    e.preventDefault();
                    alert('Please fill in at least one signatory name.');
                    return false;
                }

                // Show loading state
                const submitBtn = document.getElementById('generateCertificateBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
                submitBtn.disabled = true;

                // Re-enable button after 5 seconds (in case submission fails)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });

            // Auto-populate dates
            const today = new Date().toISOString().split('T')[0];
            const dateInputs = acceptanceForm.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                if (!input.value) {
                    input.value = today;
                }
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    .required::after {
        content: " *";
        color: #dc3545;
    }

    .signatory-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .signatory-header {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding: 0.5rem;
        border-radius: 5px;
    }

    .file-upload {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        background: #fff;
        transition: all 0.3s;
    }

    .file-upload:hover {
        border-color: #28a745;
        background: #f8fff8;
    }

    .border-dashed {
        border-style: dashed !important;
    }

    .signature-preview {
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 5px;
        overflow: hidden;
    }

    .signature-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .preview-section {
        background: #e7f3fe;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .preview-section .card-body p {
        margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
        .signature-preview {
            min-height: 60px;
        }
    }
</style>
@endpush
