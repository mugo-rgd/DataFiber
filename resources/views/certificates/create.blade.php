<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditional Certificate Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .section-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="form-container">
            <h2 class="text-center mb-4">Conditional Certificate Generator</h2>

            <form id="certificateForm" action="{{ route('certificates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="section-title">Basic Information</h5>

                        <div class="mb-3">
                            <label class="form-label">REF Number *</label>
                            <input type="text" class="form-control" name="ref_number" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lessor *</label>
                            <input type="text" class="form-control" name="lessor" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lessee *</label>
                            <input type="text" class="form-control" name="lessee" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Name of the Link *</label>
                            <input type="text" class="form-control" name="link_name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Serial No. of OTDR *</label>
                            <input type="text" class="form-control" name="otdr_serial" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date of Calibration *</label>
                            <input type="date" class="form-control" name="calibration_date" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="section-title">Site Information</h5>

                        <div class="mb-3">
                            <label class="form-label">Site A Name *</label>
                            <input type="text" class="form-control" name="site_a" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Site B Name *</label>
                            <input type="text" class="form-control" name="site_b" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fibre Cable Technology *</label>
                            <select class="form-control" name="fibre_technology" required>
                                <option value="">Select Technology</option>
                                <option value="ADSS">ADSS</option>
                                <option value="Fig 8">Fig 8</option>
                                <option value="OPGW">OPGW</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ODF - Connector Type *</label>
                            <input type="text" class="form-control" name="odf_connector_type" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="section-title">Test Results</h5>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Total Fibre Length (Km) *</label>
                            <input type="number" step="0.01" class="form-control" name="total_fibre_length" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Average Link Loss (dB) *</label>
                            <input type="number" step="0.01" class="form-control" name="average_link_loss" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">No of Splice Joints *</label>
                            <input type="number" class="form-control" name="splice_joints" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Test Wavelength (nm) *</label>
                            <input type="number" class="form-control" name="test_wavelength" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">IOR *</label>
                            <input type="number" step="0.0001" class="form-control" name="ior" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5 class="section-title">Signatory Details</h5>

                        <div class="mb-3">
                            <label class="form-label">KPLC Lead Engineer Name *</label>
                            <input type="text" class="form-control" name="engineer_name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Certificate Date *</label>
                            <input type="date" class="form-control" name="certificate_date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Commissioning End Date *</label>
                            <input type="date" class="form-control" name="commissioning_end_date" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="section-title">Attachments</h5>

                        <div class="mb-3">
                            <label class="form-label">Inspection Report (OTDR Trace) *</label>
                            <input type="file" class="form-control" name="inspection_report" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Signature (Optional)</label>
                            <input type="file" class="form-control" name="signature" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stamp (Optional)</label>
                            <input type="file" class="form-control" name="stamp" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                        <span id="btnText">Generate Certificate</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

 {{-- <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script> --}}

    <script>
        $(document).ready(function() {
            $('#certificateForm').on('submit', function(e) {
                const btn = $('#generateBtn');
                const btnText = $('#btnText');
                const spinner = $('#btnSpinner');

                // Show loading state
                btn.prop('disabled', true);
                btnText.text('Generating...');
                spinner.removeClass('d-none');

                // Form will submit normally, file download will start
            });

            // Optional: AJAX submission alternative
            function submitViaAjax() {
                const formData = new FormData(document.getElementById('certificateForm'));

                $.ajax({
                    url: '{{ route("certificates.generate") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhrFields: {
                        responseType: 'blob'
                    },
                    beforeSend: function() {
                        // Show loading
                    },
                    success: function(blob) {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'certificate_package.zip';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    },
                    error: function(xhr) {
                        alert('Error generating certificate: ' + xhr.responseText);
                    }
                });
            }
        });
    </script>
</body>
</html>
