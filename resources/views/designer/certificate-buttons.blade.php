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
<div class="modal fade" id="conditionalCertificateModal" tabindex="-1" aria-labelledby="conditionalCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="conditionalCertificateForm" method="POST" action="{{ route('designer.requests.certificate.conditional', $request->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="conditionalCertificateModalLabel">
                        <i class="fas fa-file-contract me-2"></i>Generate Conditional Certificate
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="design_request_id" value="{{ $request->id }}">
                    <input type="hidden" name="certificate_type" value="conditional">

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Basic Information -->
                            <div class="mb-3">
                                <label for="certificate_number" class="form-label">Certificate Number *</label>
                                <input type="text" class="form-control" id="certificate_number" name="certificate_number"
                                       value="CC-{{ strtoupper(Str::random(8)) }}" required>
                                <small class="text-muted">Auto-generated certificate number</small>
                            </div>

                            <div class="mb-3">
                                <label for="issued_date" class="form-label">Issued Date *</label>
                                <input type="date" class="form-control" id="issued_date" name="issued_date"
                                       value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="valid_until" class="form-label">Valid Until *</label>
                                <input type="date" class="form-control" id="valid_until" name="valid_until"
                                       min="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="inspector_name" class="form-label">Inspector Name *</label>
                                <input type="text" class="form-control" id="inspector_name" name="inspector_name"
                                       placeholder="Enter inspector name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Conditions & Requirements -->
                            <div class="mb-3">
                                <label for="conditions" class="form-label">Conditions *</label>
                                <textarea class="form-control" id="conditions" name="conditions" rows="4"
                                          placeholder="List the conditions that must be met..." required></textarea>
                                <small class="text-muted">Specify all conditions for full certification</small>
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2"
                                          placeholder="Additional remarks or notes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Section -->
                    <div class="border-top pt-3 mt-3">
                        <h6 class="mb-3">
                            <i class="fas fa-paperclip me-2"></i>Attachments
                        </h6>

                        <div class="mb-3">
                            <label for="supporting_document" class="form-label">Supporting Document</label>
                            <input type="file" class="form-control" id="supporting_document" name="supporting_document"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload supporting document (PDF, Word, or Images)</small>
                        </div>

                        <div class="mb-3">
                            <label for="inspection_report" class="form-label">Inspection Report</label>
                            <input type="file" class="form-control" id="inspection_report" name="inspection_report"
                                   accept=".pdf,.xls,.xlsx">
                            <small class="text-muted">Upload inspection report if available</small>
                        </div>

                        <div class="mb-3">
                            <label for="photos" class="form-label">Site Photos</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple
                                   accept="image/*">
                            <small class="text-muted">Upload multiple site photos (Max 5 files)</small>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Certificate Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="pending_review">Pending Review</option>
                            <option value="issued">Issued</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                    </div>

                    <!-- Preview Section -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Certificate Preview</h6>
                        <div class="small">
                            <strong>Request:</strong> {{ $request->request_number }}<br>
                            <strong>Client:</strong> {{ $request->client->name ?? 'N/A' }}<br>
                            <strong>Date:</strong> <span id="previewDate">{{ date('F d, Y') }}</span><br>
                            <strong>Conditions:</strong> <span id="previewConditions" class="text-muted">Not specified</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Generate Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Acceptance Certificate Modal -->
<!-- Acceptance Certificate Modal -->
<div class="modal fade" id="acceptanceCertificateModal" tabindex="-1" aria-labelledby="acceptanceCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            
            <form id="acceptanceCertificateForm" method="POST" action="{{ route('certificates.acceptance.store') }}" enctype="multipart/form-data">
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
                        <p class="text-muted">Generate Kenya Power & Lighting Company PLC Acceptance Certificate</p>
                    </div>

                    <div class="kplc-header bg-primary text-white p-3 rounded mb-4">
                        <h5 class="text-center mb-0">
                            <i class="fas fa-bolt me-2"></i>THE KENYA POWER & LIGHTING COMPANY PLC
                        </h5>
                    </div>

                    <!-- Basic Information -->
                    <div class="form-section card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">To (Company/Organization)</label>
                                    <input type="text" class="form-control" name="to_company"
                                           value="{{ $request->client->name ?? '' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Certificate Reference Number</label>
                                    <input type="text" class="form-control" name="certificate_ref"
                                           value="KPLC/AC/{{ date('Y') }}/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}" readonly>
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
                    <div class="form-section card mb-3">
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
                                                   value="{{ $request->client->name ?? '' }}" required>
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
                    <div class="form-section card mb-3">
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
                    <div class="form-section card mb-3">
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
                    <div class="form-section card mb-3">
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
                                        <small class="text-muted">Upload final test report (PDF format)</small>
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
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-check-circle me-2"></i>Declaration</h6>
                        <p class="mb-0">
                            We hereby certify that the above dark fiber link comprising the Purchased Capacity has been completed,
                            tested and commissioned to Acceptable Standards. This Certificate of Acceptance is a confirmation of
                            the same and the date indicated herein is the Effective Date.
                        </p>
                    </div>

                    <!-- Preview Section -->
                    <div class="preview-section card mt-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Certificate Preview</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Reference:</strong> <span id="previewRef">KPLC/AC/{{ date('Y') }}/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                                    <p><strong>To:</strong> <span id="previewTo">{{ $request->client->name ?? 'N/A' }}</span></p>
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

            // Auto-populate dates
            const today = new Date().toISOString().split('T')[0];
            const dateInputs = acceptanceForm.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                if (!input.value) {
                    input.value = today;
                }
            });
        }

        // Conditional Certificate Form Handling
        const conditionalForm = document.getElementById('conditionalCertificateForm');

        if (conditionalForm) {
            // Auto-set valid until date (30 days from issued date)
            const issuedDateInput = conditionalForm.querySelector('input[name="issued_date"]');
            const validUntilInput = conditionalForm.querySelector('input[name="valid_until"]');

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

            // Preview updates for conditional certificate
            const previewDate = conditionalForm.querySelector('#previewDate');
            const previewConditions = conditionalForm.querySelector('#previewConditions');
            const conditionsTextarea = conditionalForm.querySelector('textarea[name="conditions"]');

            if (issuedDateInput && previewDate) {
                issuedDateInput.addEventListener('change', function() {
                    const date = new Date(this.value);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    previewDate.textContent = formattedDate;
                });

                // Trigger initial update
                if (issuedDateInput.value) {
                    issuedDateInput.dispatchEvent(new Event('change'));
                }
            }

            if (conditionsTextarea && previewConditions) {
                conditionsTextarea.addEventListener('input', function() {
                    const text = this.value;
                    if (text.length > 100) {
                        previewConditions.textContent = text.substring(0, 100) + '...';
                    } else {
                        previewConditions.textContent = text || 'Not specified';
                    }
                });
            }

            // Form submission validation
            conditionalForm.addEventListener('submit', function(e) {
                const certificateNumber = conditionalForm.querySelector('input[name="certificate_number"]').value;
                if (!certificateNumber.startsWith('CC-')) {
                    e.preventDefault();
                    alert('Conditional certificate number must start with "CC-"');
                    return false;
                }

                // Show loading state
                const submitBtn = conditionalForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
                submitBtn.disabled = true;

                // Re-enable button after 5 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        }
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

    .form-section {
        margin-bottom: 1.5rem;
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

    .form-label.required::after {
        content: " *";
        color: #dc3545;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
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

        .signature-preview {
            min-height: 60px;
        }
    }
</style>
@endpush
