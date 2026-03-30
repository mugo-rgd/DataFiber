@extends('layouts.app')

@section('title', 'Generate Conditional Certificate - ICT Engineer')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-contract text-warning me-2"></i>Generate Conditional Certificate
                    </h1>
                    <p class="text-muted mb-0">ICT Engineer - Create Conditional Certificate for Design Request</p>
                </div>
                <a href="{{ route('ictengineer.requests') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Requests
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-file-contract me-2"></i>
                        Conditional Certificate Form
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

                    <form id="conditionalCertificateForm" method="POST" action="{{ route('ictengineer.certificates.conditional.store', $request) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $request->id }}">

                        <div class="row">
    <div class="col-md-6">
        <!-- Basic Information -->
        <div class="mb-3">
            <label for="certificate_number" class="form-label required">Certificate Number</label>
            <input type="text" class="form-control" id="certificate_number" name="certificate_number"
                   value="CC-{{ strtoupper(Str::random(8)) }}" required readonly>
            <small class="text-muted">Auto-generated certificate number</small>
        </div>

        <div class="mb-3">
            <label for="ref_number" class="form-label required">Reference Number</label>
            <input type="text" class="form-control" id="ref_number" name="ref_number"
                   value="CC-{{ date('Ymd') }}-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}" required>
            <small class="text-muted">Unique reference number for tracking</small>
        </div>

        <div class="mb-3">
            <label for="certificate_date" class="form-label required">Certificate Date</label>
            <input type="date" class="form-control" id="certificate_date" name="certificate_date"
                   value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
            <label for="commissioning_end_date" class="form-label required">Commissioning End Date</label>
            <input type="date" class="form-control" id="commissioning_end_date" name="commissioning_end_date"
                   value="{{ date('Y-m-d', strtotime('+30 days')) }}" min="{{ date('Y-m-d') }}" required>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Parties Information -->
        <div class="mb-3">
            <label for="lessor" class="form-label required">Lessor (Kenya Power)</label>
            <input type="text" class="form-control" id="lessor" name="lessor"
                   value="THE KENYA POWER & LIGHTING COMPANY PLC" required readonly>
        </div>

        <div class="mb-3">
            <label for="lessee" class="form-label required">Lessee (Customer)</label>
            <input type="text" class="form-control" id="lessee" name="lessee"
                   value="{{ $request->customer->name ?? '' }}" required>
        </div>

        <div class="mb-3">
            <label for="link_name" class="form-label required">Link Name</label>
            <input type="text" class="form-control" id="link_name" name="link_name"
                   value="{{ $request->title ?? '' }}" required>
        </div>

        <div class="mb-3">
            <label for="engineer_name" class="form-label required">ICT Engineer Name</label>
            <input type="text" class="form-control" id="engineer_name" name="engineer_name"
                   value="{{ Auth::user()->name }}" required readonly>
        </div>
    </div>
</div>

                       <!-- Technical Details Section - Add these fields -->
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="site_a" class="form-label required">Site A</label>
            <input type="text" class="form-control" id="site_a" name="site_a" required>
        </div>

        <div class="mb-3">
            <label for="site_b" class="form-label required">Site B</label>
            <input type="text" class="form-control" id="site_b" name="site_b" required>
        </div>

        <div class="mb-3">
            <label for="fibre_technology" class="form-label required">Fibre Technology</label>
            <select class="form-select" id="fibre_technology" name="fibre_technology" required>
                <option value="">Select Technology</option>
                <option value="ADSS">ADSS</option>
                <option value="OPGW">OPGW</option>
                <option value="Figure-8">Figure 8</option>
                <option value="Underground">Underground</option>
                <option value="Duct">Duct</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="odf_connector_type" class="form-label required">ODF Connector Type</label>
            <input type="text" class="form-control" id="odf_connector_type" name="odf_connector_type" required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="total_length" class="form-label required">Total Fibre Length (KM)</label>
            <input type="number" step="0.001" class="form-control" id="total_length" name="total_length" required>
        </div>

        <div class="mb-3">
            <label for="average_loss" class="form-label required">Average Link Loss (dB)</label>
            <input type="number" step="0.01" class="form-control" id="average_loss" name="average_loss" required>
        </div>

        <div class="mb-3">
            <label for="splice_joints" class="form-label required">Number of Splice Joints</label>
            <input type="number" class="form-control" id="splice_joints" name="splice_joints" required>
        </div>

        <div class="mb-3">
            <label for="test_wavelength" class="form-label required">Test Wavelength (nm)</label>
            <input type="text" class="form-control" id="test_wavelength" name="test_wavelength" value="1310/1550" required>
        </div>

        <div class="mb-3">
            <label for="ior" class="form-label required">Index of Refraction (IOR)</label>
            <input type="number" step="0.0001" class="form-control" id="ior" name="ior" value="1.4680" required>
        </div>
    </div>
</div>

<!-- Test Equipment Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Test Equipment Details</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="otdr_serial" class="form-label required">OTDR Serial Number</label>
                    <input type="text" class="form-control" id="otdr_serial" name="otdr_serial" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="calibration_date" class="form-label required">Calibration Date</label>
                    <input type="date" class="form-control" id="calibration_date" name="calibration_date"
                           value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conditions Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Conditions & Requirements</h6>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="conditions" class="form-label required">Conditions for Full Certification</label>
            <textarea class="form-control" id="conditions" name="conditions" rows="6" required
                      placeholder="List all conditions that must be met for full certification..."></textarea>
        </div>

        <div class="mb-3">
            <label for="remarks" class="form-label">Additional Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="3"
                      placeholder="Any additional remarks or notes..."></textarea>
        </div>

        <!-- Status Field -->
        <div class="mb-3">
    <label for="status" class="form-label required">Certificate Status</label>
    <select class="form-select" id="status" name="status" required>
        <option value="draft">Draft</option>
        <option value="pending_designer" selected>Pending Designer</option>
        <option value="sent_to_designer">Sent to Designer</option>
        <option value="acknowledged">Acknowledged</option>
        <option value="completed">Completed</option>
        <option value="rejected">Rejected</option>
    </select>
</div>
    </div>
</div>

                        <!-- File Upload Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachments</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="inspection_report" class="form-label required">Inspection Report</label>
                                    <input type="file" class="form-control" id="inspection_report" name="inspection_report"
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                                    <small class="text-muted">Upload OTDR test report or inspection document (PDF, Images, Word)</small>
                                </div>

                                <div class="mb-3">
                                    <label for="engineer_signature" class="form-label">Engineer Signature</label>
                                    <input type="file" class="form-control" id="engineer_signature" name="engineer_signature"
                                           accept="image/*">
                                    <small class="text-muted">Upload signature image (PNG, JPG, JPEG)</small>
                                </div>

                                <div class="mb-3">
                                    <label for="additional_documents" class="form-label">Additional Documents</label>
                                    <input type="file" class="form-control" id="additional_documents" name="additional_documents[]"
                                           multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted">Upload multiple supporting documents if needed</small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="alert alert-info mb-4">
                            <h6><i class="fas fa-eye me-2"></i>Certificate Preview</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Request #:</strong> {{ $request->request_number ?? $request->id }}</p>
                                    <p class="mb-1"><strong>Customer:</strong> {{ $request->customer->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Link Name:</strong> <span id="previewLink">{{ $request->title ?? 'N/A' }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Issued Date:</strong> <span id="previewDate">{{ date('F d, Y') }}</span></p>
                                    <p class="mb-1"><strong>Valid Until:</strong> <span id="previewValidUntil">{{ date('F d, Y', strtotime('+30 days')) }}</span></p>
                                    <p class="mb-0"><strong>Engineer:</strong> {{ Auth::user()->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center border-top pt-4">
                            <a href="{{ route('ictengineer.requests') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning" id="generateCertificateBtn">
                                <i class="fas fa-file-certificate me-2"></i>Generate Conditional Certificate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Certificate Information
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary mb-3">About Conditional Certificates</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Issued after preliminary inspection</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Valid for 30 days from issue date</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Lists conditions for full certification</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Requires OTDR test report</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Signed by ICT Engineer</small>
                        </li>
                    </ul>

                    <div class="alert alert-warning mt-3">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                        <small class="mb-0">
                            <ul class="mb-0 ps-3">
                                <li>Ensure all technical details are accurate</li>
                                <li>Upload clear test reports</li>
                                <li>Certificate will be downloadable as ZIP file</li>
                                <li>Request status will be updated automatically</li>
                            </ul>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('conditionalCertificateForm');
        const generateBtn = document.getElementById('generateCertificateBtn');
        const previewDate = document.getElementById('previewDate');
        const previewValidUntil = document.getElementById('previewValidUntil');
        const previewLink = document.getElementById('previewLink');

        // Update preview dates
        const issuedDateInput = document.getElementById('issued_date');
        const validUntilInput = document.getElementById('valid_until');
        const linkNameInput = document.getElementById('link_name');

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Update issued date preview
        if (issuedDateInput && previewDate) {
            issuedDateInput.addEventListener('change', function() {
                previewDate.textContent = formatDate(this.value);
            });
        }

        // Update valid until preview
        if (validUntilInput && previewValidUntil) {
            validUntilInput.addEventListener('change', function() {
                previewValidUntil.textContent = formatDate(this.value);
            });
        }

        // Update link name preview
        if (linkNameInput && previewLink) {
            linkNameInput.addEventListener('input', function() {
                previewLink.textContent = this.value || '{{ $request->title ?? 'N/A' }}';
            });
        }

        // Form submission
        if (form) {
            form.addEventListener('submit', function(e) {
                // Validate file sizes
                const fileInputs = form.querySelectorAll('input[type="file"]');
                const maxSize = 10 * 1024 * 1024; // 10MB

                for (let input of fileInputs) {
                    if (input.files.length > 0) {
                        for (let file of input.files) {
                            if (file.size > maxSize) {
                                e.preventDefault();
                                alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
                                input.value = '';
                                return false;
                            }
                        }
                    }
                }

                // Show loading state
                const originalText = generateBtn.innerHTML;
                generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
                generateBtn.disabled = true;

                // Re-enable button after 10 seconds if submission fails
                setTimeout(() => {
                    generateBtn.innerHTML = originalText;
                    generateBtn.disabled = false;
                }, 10000);
            });
        }

        // Auto-set valid until date (30 days from issued date)
        if (issuedDateInput && validUntilInput) {
            issuedDateInput.addEventListener('change', function() {
                if (!validUntilInput.value) {
                    const issuedDate = new Date(this.value);
                    issuedDate.setDate(issuedDate.getDate() + 30);
                    const nextMonth = issuedDate.toISOString().split('T')[0];
                    validUntilInput.value = nextMonth;
                    validUntilInput.min = this.value;
                }
            });

            // Trigger initial calculation
            if (issuedDateInput.value) {
                issuedDateInput.dispatchEvent(new Event('change'));
            }
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

    .card {
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.5rem;
    }

    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
        font-weight: 600;
    }

    .form-control:read-only {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    .alert ul {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .d-flex.justify-content-between.align-items-center .btn {
            margin-top: 1rem;
        }
    }
</style>
@endpush
