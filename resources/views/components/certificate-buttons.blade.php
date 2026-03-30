{{-- @include('components.design-request-actions', ['request' => $designRequest]) --}}
<!-- Certificate Action Buttons -->
<div class="d-flex gap-2 align-items-center">
    <!-- View Details Button -->
    <a href="{{ route('designer.requests.show', $request->request_number) }}"
       class="btn btn-info" title="View Request Details">
        <i class="fas fa-eye"></i>
    </a>

    <!-- Conditional Certificate Button (Triggers Modal) -->
    <button type="button"
            class="btn btn-warning"
            data-bs-toggle="modal"
            data-bs-target="#conditionalCertificateModal"
            title="Generate Conditional Certificate">
        <i class="fas fa-file-contract"></i>
    </button>

    <!-- Acceptance Certificate Button (Triggers Modal) -->
    <button type="button"
            class="btn btn-success"
            data-bs-toggle="modal"
            data-bs-target="#acceptanceCertificateModal"
            title="Generate Acceptance Certificate">
        <i class="fas fa-award"></i>
    </button>
</div>

<!-- Conditional Certificate Modal -->
<div class="modal fade" id="conditionalCertificateModal" tabindex="-1" aria-labelledby="conditionalCertificateModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="conditionalCertificateForm" method="POST" action="{{ route('certificates.conditional.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_id" value="{{ $request->id }}">

                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="conditionalCertificateModalLabel">
                        <i class="fas fa-file-contract me-2"></i>Generate Conditional Certificate
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Certificate Header -->
                    <div class="logo">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="logo-img">
            </div>
        </div>
                    <div class="text-center mb-4 border-bottom pb-3">
                        <h4 class="fw-bold">Conditional Certificate (Sample1)</h4>
                        <h5 class="text-muted">Issued by Kenya Power</h5>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">REF: *</label>
                                <input type="text" class="form-control" name="ref_number"
                                       value="KPLC/CC/{{ date('Y') }}/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Lessor: *</label>
                                <input type="text" class="form-control" name="lessor"
                                       value="THE KENYA POWER & LIGHTING COMPANY PLC" readonly required>
                            </div>


                            <div class="mb-3">
    <label class="form-label fw-bold">Lessee: *</label>
    <input type="text" class="form-control" name="lessee"
           value="{{ $request->customer?->name ?? '' }}" required>
</div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Name of the link: *</label>
                                <input type="text" class="form-control" name="link_name"
                                       value="{{ $request->title ?? '' }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Serial No. of the OTDR: *</label>
                                <input type="text" class="form-control" name="otdr_serial" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Date of Calibration: *</label>
                                <!-- Changed: Set default to today minus 6 months -->
                                <input type="date" class="form-control" name="calibration_date"
                                       value="{{ date('Y-m-d', strtotime('-6 months')) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">KPLC Lead Engineer Name: *</label>
                                <input type="text" class="form-control" name="engineer_name" id="lead_engineer_input" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Certificate Date: *</label>
                                <input type="date" class="form-control" name="certificate_date"
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Commissioning Parameters -->
                    <div class="mt-4">
                        <h5 class="fw-bold border-bottom pb-2">Commissioning Parameters</h5>

                        <!-- 1. Physical Characteristics -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">1. Physical characteristics of the Dark fibre cable</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">NAME OF SITE (A) *</label>
                                            <input type="text" class="form-control" name="site_a" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">NAME OF SITE (B) *</label>
                                            <input type="text" class="form-control" name="site_b" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">a. Fibre Cable Technology (ADSS/Fig 8/OPGW) *</label>
                                            <select class="form-select" name="fibre_technology" required>
                                                <option value="">Select Technology</option>
                                                <option value="ADSS">ADSS</option>
                                                <option value="Fig 8">Fig 8</option>
                                                <option value="OPGW">OPGW</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">b. ODF - Connector Type *</label>
                                            <select class="form-select" name="odf_connector_type" required>
                                                <option value="">Select Connector Type</option>
                                                <option value="LC">LC</option>
                                                <option value="SC">SC</option>
                                                <option value="FC">FC</option>
                                                <option value="ST">ST</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. End-to-End Tests -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">2. Dark Fibre link End-to-End Tests</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Test Parameter</th>
                                            <th>Result *</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>a. Total Fibre Length (Km)</strong></td>
                                            <td>
                                                <input type="number" step="0.001" class="form-control"
                                                       name="total_length" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>b. Average Link Loss (dB)</strong></td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control"
                                                       name="average_loss" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>c. No of Splice Joints</strong></td>
                                            <td>
                                                <input type="number" class="form-control"
                                                       name="splice_joints" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>d. Test wavelength (nm)</strong></td>
                                            <td>
                                                <select class="form-select" name="test_wavelength" required>
                                                    <option value="1310">1310 nm</option>
                                                    <option value="1550" selected>1550 nm</option>
                                                    <option value="1625">1625 nm</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>e. IOR</strong></td>
                                            <td>
                                                <input type="number" step="0.0001" class="form-control"
                                                       name="ior" value="1.4682" required>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Declaration:</strong> The above table contains the test results of the Dark Fiber link
                                performed by Kenya Power and witnessed by the Lessee.
                            </p>
                            <p class="mb-0">
                                The Lessee is hereby granted thirty (30) days within which to conduct such tests and procedures
                                as shall be necessary to satisfy the quality of the Dark Fibers cores leased.
                            </p>
                        </div>
                    </div>

                    <!-- Signatory Section -->
                    <div class="row">
                        <!-- KPLC Engineer -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">KPLC LEAD ENGINEER Technical Services Support</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Name: *</label>
                                        <input type="text" class="form-control" name="engineer_name_signatory" id="engineer_name_signatory" readonly required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date: *</label>
                                        <!-- Changed: Removed duplicate field name, using display only -->
                                        <p class="form-control-plaintext" id="signatory_date_display">{{ date('Y-m-d') }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Signature:</label>
                                        <input type="file" class="form-control" name="engineer_signature" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LESSEE Section -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">LESSEE</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Name:</label>
                                        <input type="text" class="form-control" name="lessee_contact_name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date:</label>
                                        <input type="date" class="form-control" name="lessee_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Designation:</label>
                                        <input type="text" class="form-control" name="lessee_designation">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dates Section -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">This Certificate is dated the *</label>
                                <input type="date" class="form-control" name="certificate_issue_date"
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">The Commissioning period ends dated the *</label>
                                <input type="date" class="form-control" name="commissioning_end_date"
                                       value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Section -->
                    <div class="border-top pt-3 mt-3">
                        <h6 class="mb-3">
                            <i class="fas fa-paperclip me-2"></i>Attachments
                        </h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Inspection Report (OTDR Trace) *</label>
                            <input type="file" class="form-control" name="inspection_report"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                        </div>
                    </div>

                    <!-- NB Notes -->
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-exclamation-circle me-2"></i>NB</h6>
                        <ol class="mb-0">
                            <li>The fibre core(s) shall be considered accepted by the purchaser upon the expiration of the test period without any formal notification on the contrary.</li>
                            <li>Attach Test Report – Optical Time Domain Reflectometer (OTDR) Trace.</li>
                        </ol>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-pdf me-2"></i>Generate & Download Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Acceptance Certificate Modal -->
<!-- Acceptance Certificate Modal//route('certificates.acceptance.store') -->
<div class="modal fade" id="acceptanceCertificateModal" tabindex="-1" aria-labelledby="acceptanceCertificateModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="acceptanceCertificateForm" method="POST" action="{{route('certificates.acceptance.store')  }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_id" value="{{ $request->id }}">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="acceptanceCertificateModalLabel">
                        <i class="fas fa-award me-2"></i>Generate Acceptance Certificate
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Certificate Header -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-primary">Certificate of Acceptance</h4>
                        <h5 class="text-muted">ISSUED BY THE KENYA POWER & LIGHTING COMPANY PLC</h5>
                    </div>

                    <!-- To Section -->
                    <div class="mb-4">
                        {{-- <h6 class="fw-bold">To</h6> --}}
                        <input type="text" class="form-control" name="to_company"
                               value="{{ $request->customer->name ?? '' }}" required>
                    </div>

                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">LESSOR:</label>
                                <input type="text" class="form-control"
                                       value="THE KENYA POWER & LIGHTING COMPANY PLC" readonly>
                                <input type="hidden" name="lessor" value="THE KENYA POWER & LIGHTING COMPANY PLC">
                            </div>
                        </div>
                                            <div class="col-md-6">
                            <div class="mb-3">
    <label class="form-label fw-bold">Lessee: *</label>
    <input type="text" class="form-control" name="lessee"
           value="{{ $request->customer?->name ?? '' }}" required>
</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">NAME OF THE ROUTE: *</label>
                                <input type="text" class="form-control" name="route_name"
                                 value="{{ $request->description ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">NAME OF THE LINK: *</label>
                                <input type="text" class="form-control" name="link_name"
                                       value="{{ $request->title ?? '' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">CABLE TYPE: *</label>
                                <select class="form-select" name="cable_type" required>
                                    <option value="">Select Type</option>
                                    <option value="ADSS">ADSS</option>
                                    <option value="OPGW">OPGW</option>
                                    <option value="Fig 8">Fig 8</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">DISTANCE: *</label>
                                <input type="number" step="0.001" class="form-control" name="distance" required>
                                <small class="text-muted">KM</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">No. Of Cores: *</label>
                                <input type="number" class="form-control" name="cores_count" required>
                            </div>
                        </div>
                    </div>

                    <!-- Declaration -->
                    <div class="alert alert-info mb-4">
                        <p class="mb-2">
                            <strong>The above link has been tested and fully accepted by the officers listed in the table below.</strong>
                        </p>
                        <p class="mb-0">
                            We hereby certify that the above dark fiber link comprising the Purchased Capacity has
                            been completed, tested and commissioned to Acceptable Standards. This Certificate of
                            Acceptance is a confirmation of the same and the date indicated herein is the Effective Date.
                        </p>
                    </div>

                    <!-- Effective Date -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">This Certificate of Acceptance is issued dated the*</label>
                        <input type="date" class="form-control" name="effective_date"
                               value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Signatories -->
                    <div class="row">
                        <!-- KPLC Signatories -->
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2">LESSOR: THE KENYA POWER & LIGHTING COMPANY PLC</h6>

                            <!-- Witness 1 -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">1. INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">NAME: *</label>
                                        <input type="text" class="form-control" name="witness1_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date: *</label>
                                        <input type="date" class="form-control" name="witness1_date"
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Signature:</label>
                                        <input type="file" class="form-control" name="witness1_signature" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- Witness 2 -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">2. TELECOM LEAD ENGINEER, Kenya Power</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">NAME: *</label>
                                        <input type="text" class="form-control" name="witness2_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date: *</label>
                                        <input type="date" class="form-control" name="witness2_date"
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Signature:</label>
                                        <input type="file" class="form-control" name="witness2_signature" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stamp:</label>
                                        <input type="file" class="form-control" name="witness2_stamp" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- Witness 3 -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">3. TELECOM MANAGER, Kenya Power</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">NAME: *</label>
                                        <input type="text" class="form-control" name="witness3_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date: *</label>
                                        <input type="date" class="form-control" name="witness3_date"
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Signature:</label>
                                        <input type="file" class="form-control" name="witness3_signature" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stamp:</label>
                                        <input type="file" class="form-control" name="witness3_stamp" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LESSEE Signatories -->
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2">LESSEE: {{ $request->client->name ?? 'COMPANY NAME' }}</h6>

                            <!-- Lessee 1 -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">1. LEAD ENGINEER / TECHNICAL REP.</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">NAME: *</label>
                                        <input type="text" class="form-control" name="lessee1_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date: *</label>
                                        <input type="date" class="form-control" name="lessee1_date"
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Signature:</label>
                                        <input type="file" class="form-control" name="lessee1_signature" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stamp:</label>
                                        <input type="file" class="form-control" name="lessee1_stamp" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- Lessee 2 -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">2. MANAGER</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">NAME: *</label>
                                        <input type="text" class="form-control" name="lessee2_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date: *</label>
                                        <input type="date" class="form-control" name="lessee2_date"
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Signature:</label>
                                        <input type="file" class="form-control" name="lessee2_signature" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stamp:</label>
                                        <input type="file" class="form-control" name="lessee2_stamp" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Documents -->
                    <div class="border-top pt-3 mt-3">
                        <h6 class="mb-3">
                            <i class="fas fa-paperclip me-2"></i>Supporting Documents
                        </h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Test Report (PDF) *</label>
                            <input type="file" class="form-control" name="test_report"
                                   accept=".pdf" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Documents</label>
                            <input type="file" class="form-control" name="additional_documents[]"
                                   multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-certificate me-2"></i>Generate Acceptance Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Conditional Certificate Form Handling
        const conditionalForm = document.getElementById('conditionalCertificateForm');
        const acceptanceForm = document.getElementById('acceptanceCertificateForm');

        if (conditionalForm) {
            // Get all date elements
            const certDateInput = conditionalForm.querySelector('input[name="certificate_date"]');
            const calibrationDateInput = conditionalForm.querySelector('input[name="calibration_date"]');
            const commissioningEndInput = conditionalForm.querySelector('input[name="commissioning_end_date"]');
            const certificateIssueDate = conditionalForm.querySelector('input[name="certificate_issue_date"]');
            const lesseeDateInput = conditionalForm.querySelector('input[name="lessee_date"]');
            const signatoryDateDisplay = conditionalForm.querySelector('#signatory_date_display');

            // Auto-fill signatory engineer name from lead engineer input
            const leadEngineerInput = conditionalForm.querySelector('#lead_engineer_input');
            const signatoryEngineerInput = conditionalForm.querySelector('#engineer_name_signatory');

            if (leadEngineerInput && signatoryEngineerInput) {
                leadEngineerInput.addEventListener('input', function() {
                    signatoryEngineerInput.value = this.value;
                });

                // Set initial value
                if (leadEngineerInput.value) {
                    signatoryEngineerInput.value = leadEngineerInput.value;
                }
            }

            // Update signatory date display when certificate date changes
            if (certDateInput && signatoryDateDisplay) {
                certDateInput.addEventListener('change', function() {
                    signatoryDateDisplay.textContent = this.value;
                });

                // Set initial value
                if (certDateInput.value) {
                    signatoryDateDisplay.textContent = certDateInput.value;
                }
            }

            // Auto-set commissioning end date to 30 days from certificate date
            if (certDateInput && commissioningEndInput) {
                certDateInput.addEventListener('change', function() {
                    const certDate = new Date(this.value);
                    const endDate = new Date(certDate);
                    endDate.setDate(endDate.getDate() + 30); // 30 days period

                    commissioningEndInput.value = endDate.toISOString().split('T')[0];
                    commissioningEndInput.min = this.value;
                });

                // Trigger change on load
                if (certDateInput.value) {
                    certDateInput.dispatchEvent(new Event('change'));
                }
            }

            // Set default calibration date to 6 months ago if empty
            if (calibrationDateInput && !calibrationDateInput.value) {
                const sixMonthsAgo = new Date();
                sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);
                calibrationDateInput.value = sixMonthsAgo.toISOString().split('T')[0];
            }

            // Set all dates to today if they're empty
            const today = new Date().toISOString().split('T')[0];

            if (certDateInput && !certDateInput.value) {
                certDateInput.value = today;
            }

            if (certificateIssueDate && !certificateIssueDate.value) {
                certificateIssueDate.value = today;
            }

            if (lesseeDateInput && !lesseeDateInput.value) {
                lesseeDateInput.value = today;
            }

            // Form submission validation
            conditionalForm.addEventListener('submit', function(e) {
                // Validate REF number format
                const refNumber = conditionalForm.querySelector('input[name="ref_number"]').value;
                if (!refNumber.startsWith('KPLC/')) {
                    e.preventDefault();
                    alert('REF number must follow KPLC format (e.g., KPLC/CC/YYYY/XXXX)');
                    return false;
                }

                // Validate commissioning end date is after certificate date
                if (certDateInput && commissioningEndInput) {
                    const certDate = new Date(certDateInput.value);
                    const endDate = new Date(commissioningEndInput.value);

                    if (endDate <= certDate) {
                        e.preventDefault();
                        alert('Commissioning end date must be after the certificate date');
                        return false;
                    }
                }

                // Validate OTDR report is uploaded
                const otdrReport = conditionalForm.querySelector('input[name="inspection_report"]');
                if (!otdrReport.files.length) {
                    e.preventDefault();
                    alert('OTDR Trace report is required as per the certificate requirements');
                    return false;
                }

                // Validate file size (max 10MB)
                const maxSize = 10 * 1024 * 1024; // 10MB
                const fileInputs = conditionalForm.querySelectorAll('input[type="file"]');
                let validFileSize = true;

                fileInputs.forEach(input => {
                    if (input.files.length > 0) {
                        for (let file of input.files) {
                            if (file.size > maxSize) {
                                validFileSize = false;
                                alert(`File ${file.name} is too large. Maximum size is 10MB.`);
                                input.value = '';
                            }
                        }
                    }
                });

                if (!validFileSize) {
                    e.preventDefault();
                    return false;
                }

                // Ensure signatory engineer name is filled
                if (signatoryEngineerInput && !signatoryEngineerInput.value) {
                    e.preventDefault();
                    alert('Please fill in the engineer name in the basic information section');
                    return false;
                }

                // Show loading state
                const submitBtn = conditionalForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
                submitBtn.disabled = true;

                // Re-enable button after 5 seconds (in case submission fails)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        }

        // Acceptance Certificate Form Handling
        if (acceptanceForm) {
            // Set all date fields to today if empty
            const today = new Date().toISOString().split('T')[0];

            // Get all date inputs
            const dateInputs = acceptanceForm.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                if (!input.value && input.name !== 'calibration_date') {
                    input.value = today;
                }
            });

            // Form submission validation
            acceptanceForm.addEventListener('submit', function(e) {
                // Check required files
                const testReport = acceptanceForm.querySelector('input[name="test_report"]');
                if (!testReport.files.length) {
                    e.preventDefault();
                    alert('Test Report (PDF) is required');
                    return false;
                }

                // Validate file size (max 10MB)
                const maxSize = 10 * 1024 * 1024; // 10MB
                const fileInputs = acceptanceForm.querySelectorAll('input[type="file"]');
                let validFileSize = true;

                fileInputs.forEach(input => {
                    if (input.files.length > 0) {
                        for (let file of input.files) {
                            if (file.size > maxSize) {
                                validFileSize = false;
                                alert(`File ${file.name} is too large. Maximum size is 10MB.`);
                                input.value = '';
                            }
                        }
                    }
                });

                if (!validFileSize) {
                    e.preventDefault();
                    return false;
                }

                // Show loading state
                const submitBtn = acceptanceForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
                submitBtn.disabled = true;

                // Re-enable button after 5 seconds (in case submission fails)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        }

        // Auto-fill lessee from client if available
        const lesseeInput = document.querySelector('input[name="lessee"]');
        const clientName = "{{ $request->client->name ?? '' }}";
        if (lesseeInput && clientName && !lesseeInput.value) {
            lesseeInput.value = clientName;
        }

        // Auto-fill link name from request if available
        const linkNameInput = document.querySelector('input[name="link_name"]');
        const requestTitle = "{{ $request->title ?? '' }}";
        if (linkNameInput && requestTitle && !linkNameInput.value) {
            linkNameInput.value = requestTitle;
        }

        // File upload validation for both forms
        const fileInputs = document.querySelectorAll('input[type="file"]');
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

        // Fix for Bootstrap modal accessibility warning
        const modalElements = document.querySelectorAll('.modal');
        modalElements.forEach(modal => {
            // Remove aria-hidden attribute on show
            modal.addEventListener('show.bs.modal', function() {
                this.removeAttribute('aria-hidden');
            });

            // Add inert attribute when modal is hidden
            modal.addEventListener('hidden.bs.modal', function() {
                this.setAttribute('inert', '');
            });

            // Remove inert attribute when modal is shown
            modal.addEventListener('shown.bs.modal', function() {
                this.removeAttribute('inert');
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .modal-xl {
        max-width: 1200px;
    }

    .modal-header {
        border-bottom: 2px solid rgba(0,0,0,0.1);
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
    }

    .section-title {
        border-bottom: 2px solid #ffc107;
        padding-bottom: 10px;
        margin-bottom: 20px;
        color: #333;
        font-weight: 600;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 5px;
    }

    .form-control:read-only {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
        font-weight: 500;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        font-weight: 500;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }

    .border-bottom {
        border-color: #dee2e6 !important;
    }

    .text-muted {
        font-size: 0.85rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 10px;
        }

        .row > div {
            margin-bottom: 15px;
        }
    }
</style>
@endpush
