<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Conditional Certificate - {{ $conditionalCertificate->ref_number }}</title>
    <style>
        @page {
            margin: 5mm 8mm;
            size: A4;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9pt;
            line-height: 1.1;
            color: #000;
            margin: 0;
            padding: 0;
            width: 100%;
            background: #fff;
        }

        .certificate-container {
            width: 186mm;
            min-height: 273mm;
            margin: 0 auto;
            padding: 0;
            position: relative;
            box-sizing: border-box;
            border: 2px solid #000;
            display: flex;
            flex-direction: column;
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            padding: 2mm 0 1mm 0;
        }

        .logo-img {
            height: 55px;
            width: auto;
            max-width: 130px;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 0 0 2mm 0;
            border-bottom: 1px solid #000;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 0.5mm 0;
            color: #0056b3;
        }

        .header h3 {
            font-size: 10pt;
            margin: 0.5mm 0;
        }

        /* Main Content */
        .main-content {
            padding: 0 4mm;
        }

        /* Section */
        .section {
            margin-bottom: 1mm;
        }

        .section-title {
            border-bottom: 1.5px solid #333;
            padding-bottom: 0.5mm;
            margin-bottom: 1mm;
            font-size: 11pt;
            font-weight: bold;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.5mm 0 1mm 0;
            font-size: 8.5pt;
        }

        th, td {
            padding: 1mm;
            text-align: left;
            border: 1px solid #666;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        /* Signature Section */
        .signature-section {
            margin: 2mm 0 2mm 0;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .signature-cell {
            width: 50%;
            vertical-align: top;
            padding: 2mm;
            border: 1px solid #666;
        }

        .signature-title {
            font-size: 9pt;
            font-weight: bold;
            margin: 0 0 1.5mm 0;
            color: #000;
        }

        .signature-details {
            margin-bottom: 3mm;
        }

        .signature-details p {
            font-size: 8.5pt;
            margin: 0.5mm 0;
            font-weight: normal;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 3mm;
            padding-top: 0.5mm;
        }

        .signature-label {
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
        }

        /* Dates and Notes Section */
        .dates-section {
            margin: 2mm 0 5mm 0;
            font-size: 8.5pt;
        }

        .dates-section p {
            margin: 0.5mm 0;
        }

        .dates-section ol {
            margin: 1mm 0 1mm 15px;
            padding: 0;
        }

        .dates-section li {
            margin: 0.5mm 0;
            line-height: 1.2;
        }

        /* Footer - Centered everything */
        .footer {
            margin: 0 5mm;
            padding: 1mm 0;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 7pt;
            color: #666;
            flex-shrink: 0;
            position: relative;
            min-height: 25mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .footer-content {
            text-align: center;
            width: 100%;
        }

        .footer-text {
            margin-bottom: 2mm;
        }

        .footer-stamp {
            text-align: center;
            margin-top: 1mm;
        }

        .certificate-stamp {
            max-width: 35mm;
            max-height: 20mm;
            opacity: 0.85;
        }

        .footer p {
            margin: 0.3mm 0;
            text-align: center;
        }

        /* Utility Classes */
        .text-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        /* Page break prevention */
        .keep-together {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Logo Section -->
        <div class="logo-section">
            @php
                $logoPath = public_path('images/logo.png');
                $logoData = null;

                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                }
            @endphp

            @if($logoData)
                <img src="data:image/png;base64,{{ $logoData }}" alt="KPLC Logo" class="logo-img">
            @else
                <div style="font-weight: bold; font-size: 12pt; color: #0056b3; border: 1px solid #0056b3; padding: 4px 8px; display: inline-block;">
                    KENYA POWER
                </div>
            @endif
        </div>

        <!-- Header -->
        <div class="header">
            <h1>CONDITIONAL CERTIFICATE</h1>
            <h2>ISSUED BY</h2>
            <h3>KENYA POWER</h3>
        </div>

        <!-- Main Content -->
        <div class="main-content keep-together">
            <!-- Basic Information Section -->
            <div class="section">
                <table>
                    <tr>
                        <th style="width: 50%;">REF:</th>
                        <td>{{ $conditionalCertificate->ref_number }}</td>
                    </tr>
                    <tr>
                        <th>Lessor:</th>
                        <td>{{ $conditionalCertificate->lessor }}</td>
                    </tr>
                    <tr>
                        <th>Lessee:</th>
                        <td>{{ $conditionalCertificate->lessee }}</td>
                    </tr>
                    <tr>
                        <th>Name of the link:</th>
                        <td>{{ $conditionalCertificate->link_name }}</td>
                    </tr>
                </table>
            </div>

            <!-- Commissioning Parameters Section -->
            <div class="section">
                <div class="section-title">
                    <h3>Commissioning Parameters</h3>
                </div>

                <h4>1. Physical characteristics of the Dark fibre cable</h4>
                <table>
                    <tr>
                        <th style="width: 50%;">NAME OF SITE (A)</th>
                        <td>{{ $conditionalCertificate->site_a }}</td>
                    </tr>
                    <tr>
                        <th>NAME OF SITE (B)</th>
                        <td>{{ $conditionalCertificate->site_b }}</td>
                    </tr>
                    <tr>
                        <th>Fibre Cable Technology</th>
                        <td>{{ $conditionalCertificate->fibre_technology }}</td>
                    </tr>
                    <tr>
                        <th>ODF - Connector Type</th>
                        <td>{{ $conditionalCertificate->odf_connector_type }}</td>
                    </tr>
                </table>
            </div>

            <!-- Dark Fibre Tests Section -->
            <div class="section">
                <h4>2. Dark Fibre link End-to-End Tests</h4>
                <table>
                    <tr>
                        <th style="width: 50%;">Test Parameter</th>
                        <th>Result</th>
                    </tr>
                    <tr>
                        <td>Total Fibre Length (Km)</td>
                        <td>{{ $conditionalCertificate->total_length }}</td>
                    </tr>
                    <tr>
                        <td>Average Link Loss (dB)</td>
                        <td>{{ $conditionalCertificate->average_loss }}</td>
                    </tr>
                    <tr>
                        <td>No of Splice Joints</td>
                        <td>{{ $conditionalCertificate->splice_joints }}</td>
                    </tr>
                    <tr>
                        <td>Test wavelength (nm)</td>
                        <td>{{ $conditionalCertificate->test_wavelength }}</td>
                    </tr>
                    <tr>
                        <td>IOR</td>
                        <td>{{ $conditionalCertificate->ior }}</td>
                    </tr>
                </table>
            </div>

            <!-- Signature Section -->
            <div class="signature-section keep-together">
                <table class="signature-table">
                    <tr>
                        <!-- Left Column: KPLC Engineer -->
                        <td class="signature-cell">
                            <div class="signature-title">KPLC LEAD ENGINEER Technical Services Support</div>
                            <div class="signature-details">
                                <p><strong>Name:</strong> {{ $conditionalCertificate->engineer_name }}</p>
                                <p><strong>Date:</strong> {{ date('d/m/Y', strtotime($conditionalCertificate->certificate_date)) }}</p>
                            </div><p></p>
                            <div class="signature-line"></div>
                            <div class="signature-label">SIGNATURE</div>
                        </td>

                        <!-- Right Column: LESSEE -->
                        <td class="signature-cell">
                            <div class="signature-title">LESSEE</div>
                            <div class="signature-details">
                                <p><strong>Name:</strong> {{ $conditionalCertificate->lessee_contact_name }}</p>
                                <p><strong>Designation:</strong> {{ $conditionalCertificate->lessee_designation }}</p>
                                <p><strong>Date:</strong> {{ $conditionalCertificate->lessee_date ? date('d/m/Y', strtotime($conditionalCertificate->lessee_date)) : '' }}</p>
                            </div>
                            <div class="signature-line"></div>
                            <div class="signature-label">SIGNATURE</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Dates and Notes Section -->
            <div class="dates-section keep-together">
                <p class="text-bold">This Certificate is dated the: {{ date('d/m/Y', strtotime($conditionalCertificate->certificate_issue_date)) }}</p>
                <p class="text-bold">The Commissioning period ends dated the: {{ date('d/m/Y', strtotime($conditionalCertificate->commissioning_end_date)) }}</p>
                <ol>
                    <li>The fibre core(s) shall be considered accepted by the purchaser upon the expiration of the test period without any formal notification on the contrary.</li>
                    <li>Attach Test Report – Optical Time Domain Reflectometer (OTDR) Trace.</li>
                </ol>
            </div>

            <!-- Footer -->
            <div class="footer">
            <div class="footer-content">
                <div class="footer-stamp">
                @php
                    $stampPath = public_path('images/certificatestamp.png');
                    $stampData = null;
                    $qrPath = public_path('images/qrcode.png');
                    $qrData = null;

                    if (file_exists($stampPath)) {
                        $stampData = base64_encode(file_get_contents($stampPath));
                        $qrData = base64_encode(file_get_contents($qrPath));
                    }
                @endphp

                @if($stampData)
                    <img src="data:image/png;base64,{{ $stampData }}" alt="Certificate Stamp" class="certificate-stamp">
                     <img src="data:image/png;base64,{{ $qrData }}" alt="QR Code" class="certificate-stamp">
                @endif
            </div>
            </div>
        </div>
        </div>
    </div>
</body>
</html>
