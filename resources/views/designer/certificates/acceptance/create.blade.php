@extends('layouts.app')

@section('title', 'Generate Acceptance Certificate')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-signature text-success me-2"></i>Generate Acceptance Certificate
                    </h1>
                    <p class="text-muted mb-0">Designer - Create Acceptance Certificate for Design Request</p>
                </div>
                <a href="{{ route('designer.certificates.acceptance.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Acceptance Certificate Details</h5>
                </div>
                <div class="card-body">
                    <!-- Conditional Certificate Info Alert -->
                    <div class="alert alert-success mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-1">Conditional Certificate Ready</h6>
                                <p class="mb-0 small">
                                    Conditional Certificate #{{ $conditionalCert->ref_number }} issued on
                                    {{ Carbon\Carbon::parse($conditionalCert->certificate_date)->format('M d, Y') }}
                                    ({{ Carbon\Carbon::parse($conditionalCert->certificate_date)->diffInDays(now()) }} days ago)
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('designer.certificates.acceptance.store', $request) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $request->id }}">

                        <!-- Certificate Header -->
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-kp-blue">Certificate of Acceptance</h4>
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
                                               value="{{ $certificateRef }}" readonly required>
                                        <small class="text-muted">Auto-generated reference number</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Name of the Route</label>
                                        <input type="text" class="form-control" name="route_name"
                                               value="{{ $request->title }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Name of the Link</label>
                                        <input type="text" class="form-control" name="link_name"
                                               value="{{ $conditionalCert->link_name ?? $request->title }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Cable Type</label>
                                        <select class="form-select" name="cable_type" required>
                                            <option value="ADSS" {{ ($conditionalCert->fibre_technology ?? '') == 'ADSS' ? 'selected' : '' }}>ADSS</option>
                                            <option value="OPGW" {{ ($conditionalCert->fibre_technology ?? '') == 'OPGW' ? 'selected' : '' }}>OPGW</option>
                                            <option value="Figure-8">Figure 8</option>
                                            <option value="Underground">Underground</option>
                                            <option value="Duct">Duct</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Distance (KM)</label>
                                        <input type="number" step="0.001" class="form-control" name="distance"
                                               value="{{ $conditionalCert->total_length }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Number of Cores</label>
                                        <input type="number" class="form-control" name="cores_count"
                                               value="{{ $request->cores_required ?? 2 }}" required>
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
                                        <div class="border rounded p-3 h-100 bg-light">
                                            <h6 class="fw-bold text-kp-blue mb-3">LESSOR</h6>
                                            <p class="mb-0">THE KENYA POWER & LIGHTING COMPANY PLC</p>
                                            <input type="hidden" name="lessor" value="THE KENYA POWER & LIGHTING COMPANY PLC">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3">
                                            <h6 class="fw-bold text-kp-green mb-3">LESSEE</h6>
                                            <div class="mb-2">
                                                <label class="form-label small required">Company Name</label>
                                                <input type="text" class="form-control form-control-sm" name="lessee"
                                                       value="{{ $request->customer->name ?? '' }}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Address</label>
                                                <input type="text" class="form-control form-control-sm" name="lessee_address"
                                                       value="{{ $request->customer->address ?? '' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Contact Person</label>
                                                <input type="text" class="form-control form-control-sm" name="lessee_contact"
                                                       value="{{ $request->customer->contact_person ?? '' }}">
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
                                @foreach([1 => 'INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)',
                                         2 => 'TELECOM LEAD ENGINEER',
                                         3 => 'TELECOM MANAGER'] as $num => $title)
                                    <div class="signatory-card border rounded p-3 mb-3">
                                        <div class="signatory-header bg-kp-blue text-white p-2 rounded mb-3">
                                            {{ $num }}. {{ $title }}
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">Name</label>
                                                <input type="text" class="form-control" name="witness{{ $num }}_name" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">Date</label>
                                                <input type="date" class="form-control" name="witness{{ $num }}_date"
                                                       value="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Signature</label>
                                                <input type="file" class="form-control" name="witness{{ $num }}_signature" accept="image/*">
                                                <div class="signature-preview border rounded p-2 mt-2" id="witness{{ $num }}Preview"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Stamp</label>
                                                <input type="file" class="form-control" name="witness{{ $num }}_stamp" accept="image/*">
                                                <div class="signature-preview border rounded p-2 mt-2" id="witness{{ $num }}StampPreview"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- LESSEE Signatories -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Lessee Signatories</h6>
                            </div>
                            <div class="card-body">
                                @foreach([1 => 'LEAD ENGINEER / TECHNICAL REPRESENTATIVE',
                                         2 => 'MANAGER'] as $num => $title)
                                    <div class="signatory-card border rounded p-3 mb-3">
                                        <div class="signatory-header bg-kp-green text-white p-2 rounded mb-3">
                                            {{ $num }}. LESSEE - {{ $title }}
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">Name</label>
                                                <input type="text" class="form-control" name="lessee{{ $num }}_name" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">Date</label>
                                                <input type="date" class="form-control" name="lessee{{ $num }}_date"
                                                       value="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Signature</label>
                                                <input type="file" class="form-control" name="lessee{{ $num }}_signature" accept="image/*">
                                                <div class="signature-preview border rounded p-2 mt-2" id="lessee{{ $num }}Preview"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Stamp</label>
                                                <input type="file" class="form-control" name="lessee{{ $num }}_stamp" accept="image/*">
                                                <div class="signature-preview border rounded p-2 mt-2" id="lessee{{ $num }}StampPreview"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
                                        <input type="file" class="form-control" name="test_report" accept=".pdf" required>
                                        <small class="text-muted">Upload final test report (PDF format, max 10MB)</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Additional Documents</label>
                                        <input type="file" class="form-control" name="additional_documents[]"
                                               multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <small class="text-muted">You can upload multiple files</small>
                                        <div id="additionalDocsPreview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Declaration -->
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Declaration:</strong> We hereby certify that the above dark fiber link has been completed, tested and commissioned to Acceptable Standards.
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between border-top pt-4">
                            <a href="{{ route('designer.certificates.acceptance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success" id="generateCertificateBtn">
                                <i class="fas fa-save me-2"></i>Generate Acceptance Certificate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Conditional Certificate Info -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i>Conditional Certificate</h6>
                </div>
                <div class="card-body">
                    <p><strong>Reference:</strong> {{ $conditionalCert->ref_number }}</p>
                    <p><strong>Issue Date:</strong> {{ Carbon\Carbon::parse($conditionalCert->certificate_date)->format('M d, Y') }}</p>
                    <p><strong>Commissioning End:</strong> {{ Carbon\Carbon::parse($conditionalCert->commissioning_end_date)->format('M d, Y') }}</p>
                    <p><strong>ICT Engineer:</strong> {{ $conditionalCert->ictEngineer->name ?? 'N/A' }}</p>
                    <hr>
                    <p><strong>Link Name:</strong> {{ $conditionalCert->link_name }}</p>
                    <p><strong>Total Length:</strong> {{ number_format($conditionalCert->total_length, 3) }} km</p>
                    <p><strong>Average Loss:</strong> {{ number_format($conditionalCert->average_loss, 2) }} dB</p>
                </div>
            </div>

            <!-- Progress -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Progress</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="display-4 fw-bold text-success">✓</div>
                        <p class="mb-0">Conditional Certificate Issued</p>
                        <hr>
                        <div class="display-4 fw-bold text-primary">📄</div>
                        <p class="mb-0">Generating Acceptance Certificate</p>
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
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('generateCertificateBtn');

    // Signature preview function
    function setupFilePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input && preview) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid" style="max-height: 80px;" alt="Preview">';
                        preview.style.minHeight = '60px';
                        preview.classList.add('text-center');
                    };
                    reader.readAsDataURL(file);
                } else if (file) {
                    preview.innerHTML = '<span class="text-muted small">File selected: ' + file.name + '</span>';
                }
            });
        }
    }

    // Setup all previews
    for (let i = 1; i <= 3; i++) {
        setupFilePreview(`witness${i}Signature`, `witness${i}Preview`);
        setupFilePreview(`witness${i}Stamp`, `witness${i}StampPreview`);
        setupFilePreview(`lessee${i}Signature`, `lessee${i}Preview`);
        setupFilePreview(`lessee${i}Stamp`, `lessee${i}StampPreview`);
    }

    // Additional documents preview
    const additionalDocs = document.querySelector('input[name="additional_documents[]"]');
    const previewDiv = document.getElementById('additionalDocsPreview');

    if (additionalDocs && previewDiv) {
        additionalDocs.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            previewDiv.innerHTML = '';
            files.forEach(file => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-info me-1 mb-1';
                badge.textContent = file.name;
                previewDiv.appendChild(badge);
            });
        });
    }

    // Form submission
    if (form) {
        form.addEventListener('submit', function() {
            const testReport = document.querySelector('input[name="test_report"]');
            if (!testReport || !testReport.files.length) {
                e.preventDefault();
                alert('Please upload the test report.');
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
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
        transition: all 0.3s ease;
    }

    .signatory-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .signatory-header {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding: 0.5rem;
        border-radius: 5px;
    }

    .bg-kp-blue {
        background-color: #0066B3 !important;
    }

    .bg-kp-green {
        background-color: #009639 !important;
    }

    .text-kp-blue {
        color: #0066B3 !important;
    }

    .text-kp-green {
        color: #009639 !important;
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

    .signature-preview {
        min-height: 60px;
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

    @media (max-width: 768px) {
        .signature-preview {
            min-height: 50px;
        }
    }
</style>
@endpush
