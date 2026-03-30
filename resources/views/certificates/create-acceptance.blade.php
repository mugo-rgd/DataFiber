<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Acceptance Certificate</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .certificate-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin: 40px auto;
            max-width: 1200px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }

        .kplc-header {
            background: #0056b3;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #28a745;
        }

        .signatory-section {
            background: #fff3cd;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #ffc107;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .signatory-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .signatory-header {
            background: #0056b3;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin: -20px -20px 15px -20px;
            font-weight: 600;
        }

        .file-upload {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
        }

        .file-upload:hover {
            border-color: #28a745;
            background: #e8f5e8;
        }

        .btn-generate {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .preview-section {
            background: #e7f3fe;
            border-radius: 10px;
            padding: 25px;
            margin-top: 30px;
            border-left: 5px solid #17a2b8;
        }

        .preview-box {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            height: calc(3.5rem + 2px);
            padding: 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
        }

        .signature-preview {
            width: 200px;
            height: 100px;
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-style: italic;
            background: white;
            margin-top: 10px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="certificate-container">
         <div class="logo">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="logo-img">
            </div>
        </div>
        <div class="header-section">
            <h1 class="display-5 fw-bold text-primary">Certificate of Acceptance</h1>
            <p class="lead text-muted">Generate Kenya Power & Lighting Company PLC Acceptance Certificate</p>
        </div>

        <div class="kplc-header">
            <h3 class="text-center mb-0">
                <i class="fas fa-bolt me-2"></i>THE KENYA POWER & LIGHTING COMPANY PLC
            </h3>
        </div>

        <form id="acceptanceCertificateForm" method="POST" action="{{ route('certificates.acceptance.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="request_id" value="{{ $request->id }}">

            <!-- Basic Information -->
            <div class="form-section">
                <h4 class="mb-4">
                    <i class="fas fa-info-circle me-2"></i>Basic Information
                </h4>

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
                        <select class="form-control" name="cable_type" required>
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

            <!-- LESSOR & LESSEE Information -->
            <div class="form-section">
                <h4 class="mb-4">
                    <i class="fas fa-building me-2"></i>Parties Information
                </h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">LESSOR</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control"
                                           value="THE KENYA POWER & LIGHTING COMPANY PLC" readonly>
                                </div>
                                <input type="hidden" name="lessor" value="THE KENYA POWER & LIGHTING COMPANY PLC">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">LESSEE</h5>
                            </div>
                            <div class="card-body">
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

            <!-- KENYA POWER Signatories -->
            <div class="signatory-section">
                <h4 class="mb-4">
                    <i class="fas fa-signature me-2"></i>Kenya Power Signatories
                </h4>

                <!-- Witness 1: Infrastructure Support Engineer -->
                <div class="signatory-card">
                    <div class="signatory-header">
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
                            <div class="file-upload">
                                <input type="file" class="form-control" name="witness1_signature"
                                       accept="image/*" id="witness1Signature">
                                <small class="text-muted">Upload signature image (PNG, JPG)</small>
                            </div>
                            <div class="signature-preview" id="witness1Preview">
                                Signature Preview
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stamp Upload</label>
                            <div class="file-upload">
                                <input type="file" class="form-control" name="witness1_stamp"
                                       accept="image/*" id="witness1Stamp">
                                <small class="text-muted">Upload stamp image (PNG, JPG)</small>
                            </div>
                            <div class="signature-preview" id="witness1StampPreview">
                                Stamp Preview
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Witness 2: Telecom Lead Engineer -->
                <div class="signatory-card">
                    <div class="signatory-header">
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
                            <div class="file-upload">
                                <input type="file" class="form-control" name="witness2_signature"
                                       accept="image/*" id="witness2Signature">
                            </div>
                            <div class="signature-preview" id="witness2Preview">
                                Signature Preview
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stamp Upload</label>
                            <div class="file-upload">
                                <input type="file" class="form-control" name="witness2_stamp"
                                       accept="image/*" id="witness2Stamp">
                            </div>
                            <div class="signature-preview" id="witness2StampPreview">
                                Stamp Preview
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Witness 3: Telecom Manager -->
                <div class="signatory-card">
                    <div class="signatory-header">
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
                            <div class="file-upload">
                                <input type="file" class="form-control" name="witness3_signature"
                                       accept="image/*" id="witness3Signature">
                            </div>
                            <div class="signature-preview" id="witness3Preview">
                                Signature Preview
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stamp Upload</label>
                            <div class="file-upload">
                                <input type="file" class="form-control" name="witness3_stamp"
                                       accept="image/*" id="witness3Stamp">
                            </div>
                            <div class="signature-preview" id="witness3StampPreview">
                                Stamp Preview
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LESSEE Signatories -->
            <div class="signatory-section">
                <h4 class="mb-4">
                    <i class="fas fa-user-tie me-2"></i>Lessee Signatories
                </h4>

                <!-- Lessee Witness 1 -->
                <div class="signatory-card">
                    <div class="signatory-header" style="background: #28a745;">
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
                            <div class="file-upload">
                                <input type="file" class="form-control" name="lessee1_signature"
                                       accept="image/*" id="lessee1Signature">
                            </div>
                            <div class="signature-preview" id="lessee1Preview">
                                Signature Preview
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stamp Upload</label>
                            <div class="file-upload">
                                <input type="file" class="form-control" name="lessee1_stamp"
                                       accept="image/*" id="lessee1Stamp">
                            </div>
                            <div class="signature-preview" id="lessee1StampPreview">
                                Stamp Preview
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lessee Witness 2 -->
                <div class="signatory-card">
                    <div class="signatory-header" style="background: #28a745;">
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
                            <div class="file-upload">
                                <input type="file" class="form-control" name="lessee2_signature"
                                       accept="image/*" id="lessee2Signature">
                            </div>
                            <div class="signature-preview" id="lessee2Preview">
                                Signature Preview
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stamp Upload</label>
                            <div class="file-upload">
                                <input type="file" class="form-control" name="lessee2_stamp"
                                       accept="image/*" id="lessee2Stamp">
                            </div>
                            <div class="signature-preview" id="lessee2StampPreview">
                                Stamp Preview
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supporting Documents -->
            <div class="form-section">
                <h4 class="mb-4">
                    <i class="fas fa-paperclip me-2"></i>Supporting Documents
                </h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Test Report (PDF)</label>
                        <div class="file-upload">
                            <input type="file" class="form-control" name="test_report"
                                   accept=".pdf" required>
                            <small class="text-muted">Upload final test report (PDF format)</small>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Additional Documents</label>
                        <div class="file-upload">
                            <input type="file" class="form-control" name="additional_documents[]"
                                   multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">You can upload multiple files if needed</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Declaration -->
            <div class="alert alert-info mt-4">
                <h5><i class="fas fa-check-circle me-2"></i>Declaration</h5>
                <p class="mb-0">
                    We hereby certify that the above dark fiber link comprising the Purchased Capacity has been completed,
                    tested and commissioned to Acceptable Standards. This Certificate of Acceptance is a confirmation of
                    the same and the date indicated herein is the Effective Date.
                </p>
            </div>

            <!-- Preview Section -->
            <div class="preview-section">
                <h4 class="mb-3">
                    <i class="fas fa-eye me-2"></i>Certificate Preview
                </h4>
                <div class="preview-box">
                    <p><strong>Reference:</strong> <span id="previewRef">KPLC/AC/{{ date('Y') }}/{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                    <p><strong>To:</strong> <span id="previewTo">{{ $request->client->name ?? 'N/A' }}</span></p>
                    <p><strong>Link Name:</strong> <span id="previewLink">{{ $request->title ?? 'N/A' }}</span></p>
                    <p><strong>Effective Date:</strong> <span id="previewDate">{{ date('F d, Y') }}</span></p>
                    <p><strong>Signatories:</strong> <span id="previewSignatories">0 of 4 completed</span></p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-generate">
                    <i class="fas fa-file-certificate me-2"></i>Generate Acceptance Certificate
                </button>
            </div>
        </form>
    </div>

    <!-- Scripts -->
  {{-- <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('select').select2({
                placeholder: 'Select an option',
                allowClear: true
            });

            // Preview updates
            $('input[name="to_company"]').on('input', function() {
                $('#previewTo').text($(this).val() || 'N/A');
            });

            $('input[name="link_name"]').on('input', function() {
                $('#previewLink').text($(this).val() || 'N/A');
            });

            $('input[name="effective_date"]').on('change', function() {
                const date = new Date($(this).val());
                const formattedDate = date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                $('#previewDate').text(formattedDate);
            });

            // Signature preview
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
                                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width:100%; max-height:100%; object-fit:contain;">';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });

            // Stamp preview
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
                                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width:100%; max-height:100%; object-fit:contain;">';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });

            // Update signatory count
            function updateSignatoryCount() {
                const inputs = [
                    'witness1_name', 'witness2_name', 'witness3_name',
                    'lessee1_name', 'lessee2_name'
                ];

                let completed = 0;
                inputs.forEach(name => {
                    if ($(`input[name="${name}"]`).val().trim()) {
                        completed++;
                    }
                });

                $('#previewSignatories').text(`${completed} of 5 signatories completed`);
            }

            $('input[name*="_name"]').on('input', updateSignatoryCount);

            // Form validation
            $('#acceptanceCertificateForm').on('submit', function(e) {
                // Check if test report is uploaded
                const testReport = $('input[name="test_report"]').val();
                if (!testReport) {
                    e.preventDefault();
                    alert('Please upload the test report.');
                    return false;
                }

                // Check if at least one signature is uploaded
                const signatureUploads = $('input[name*="signature"]').filter(function() {
                    return $(this).val() !== '';
                }).length;

                if (signatureUploads === 0) {
                    if (!confirm('No signatures uploaded. Continue without signatures?')) {
                        e.preventDefault();
                        return false;
                    }
                }

                // Show loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalHtml = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Generating...');
                submitBtn.prop('disabled', true);

                // Re-enable after 10 seconds if submission fails
                setTimeout(() => {
                    submitBtn.html(originalHtml);
                    submitBtn.prop('disabled', false);
                }, 10000);
            });

            // Auto-populate dates
            const today = new Date().toISOString().split('T')[0];
            $('input[type="date"]').each(function() {
                if (!$(this).val()) {
                    $(this).val(today);
                }
            });

            // Initialize preview
            updateSignatoryCount();
        });
    </script>
</body>
</html>
