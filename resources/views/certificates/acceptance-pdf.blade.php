<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate of Acceptance - {{ $acceptanceCertificate->to_company ?? 'Kenya Power' }}</title>
    <style>
        @page {
            margin: 6mm 10mm;
            size: A4;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt; /* Balanced base size */
            line-height: 1.1;
            color: #000;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        .certificate-container {
            width: 188mm;
            min-height: 277mm;
            margin: 0 auto;
            padding: 0;
            position: relative;
            box-sizing: border-box;
            border: 2px solid #000;
            display: flex;
            flex-direction: column;
        }

        /* Main content area that grows */
        .main-content {
            flex: 1;
            overflow: hidden;
        }

        /* Logo - Balanced */
        .logo-section {
            text-align: center;
            margin: 2mm 0 1mm 0;
        }

        .logo-img {
            height: 32px; /* Balanced size */
            max-width: 95px;
        }

        /* Header - Proportional hierarchy */
        .header {
    text-align: center;
    margin: 0 0 2mm 0;
    padding: 0.5mm 0 1mm 0;
    border-bottom: 1px solid #000;
    position: relative;
}

.header h1 {
    font-size: 13pt;
    font-weight: bold;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5pt;
}

.header h2 {
    font-size: 10pt;
    font-weight: bold;
    margin: 0.5mm 0;
    color: #0056b3;
    text-transform: uppercase;
    letter-spacing: 0.3pt;
}

.header h3 {
    font-size: 9pt;
    margin: 0.5mm 0 1mm 0;
    font-weight: normal;
    text-transform: uppercase;
}

.certificate-number {
    font-size: 9pt;
    font-weight: bold;
    color: #000;
    margin-top: 1mm;
    padding: 0.5mm 2mm;
    display: inline-block;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 2px;
    letter-spacing: 0.2pt;
}

        /* Link Information - Clear hierarchy */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 5mm 1mm 5mm;
            font-size: 9pt; /* Slightly smaller than body */
        }

        .info-table td {
            padding: 1mm 1.5mm;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            width: 35%;
            font-size: 9.5pt; /* Slightly larger than values */
        }

        .info-table .value {
            font-size: 9pt;
        }

        /* Declaration - Clear and readable */
        .declaration-box {
            margin: 0 5mm 1mm 5mm;
            padding: 2mm;
            border: 1px solid #000;
            background: #f8f9fa;
            font-size: 9.5pt; /* Slightly larger for emphasis */
            line-height: 1.15; /* Better readability */
        }

        .declaration-box p {
            margin: 1mm 0;
        }

        .date-line {
            display: inline-block;
            min-width: 16mm; /* Balanced width */
            border-bottom: 1px solid #000;
            text-align: center;
            margin: 0 0.5mm;
            font-size: 9.5pt;
        }

        /* Two Column Layout */
        .signatures-container {
            display: flex;
            gap: 2mm;
            margin: 0 5mm 2mm 5mm;
            flex: 1;
            min-height: 0;
        }

        .signature-column {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .signature-column-wrapper {
            flex: 1;
            overflow: hidden;
        }

        .column-header {
            font-size: 10pt; /* Clear section headers */
            font-weight: bold;
            text-align: center;
            margin: 0 0 1mm 0;
            padding-bottom: 0.5mm;
            border-bottom: 1px solid #000;
        }

        /* Signature entries - Proportional */
        .signature-entry {
            margin-bottom: 1.5mm; /* Balanced spacing */
            min-height: 15mm; /* Optimal height */
            position: relative;
        }

        .signature-number {
            position: absolute;
            top: 0;
            left: 0;
            font-weight: bold;
            font-size: 9pt; /* Clear numbering */
        }

        /* Name and Date - Good proportions */
        .signature-details {
            margin-left: 4mm;
            padding-right: 1mm;
            display: flex;
            flex-wrap: wrap;
            gap: 1mm;
            align-items: flex-start;
        }

        .detail-group {
            display: flex;
            align-items: center;
            margin-bottom: 0.3mm; /* Balanced */
        }

        .detail-label {
            font-size: 8pt; /* Clear labels */
            font-weight: bold;
            margin-right: 0.5mm;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .detail-value {
            font-size: 8.5pt; /* Slightly larger for names */
            margin: 0;
            white-space: nowrap;
            font-weight: normal;
        }

        .signature-title {
            font-size: 7pt; /* Clear but not dominant */
            font-weight: bold;
            margin: 0.3mm 0 0 0;
            width: 100%;
        }

        /* Signature line container */
        .signature-line-container {
            margin-top: 1.5mm;
            position: relative;
            height: 9mm; /* Optimal for signature above line */
        }

        /* Signature line */
        .signature-line {
            border-top: 1px solid #000;
            position: absolute;
            bottom: 1.5mm;
            left: 0;
            right: 0;
            padding-top: 0.3mm;
            text-align: center;
        }

        /* Signature label - Clear */
        .signature-label {
            font-size: 6pt; /* Clear but not too small */
            text-align: center;
            margin-top: 0.2mm;
            font-weight: bold;
        }

        /* Signature image - Proportional */
        .signature-image {
            max-width: 32mm; /* Good proportion */
            max-height: 7mm; /* Clear signature */
            position: absolute;
            bottom: 5mm; /* Well positioned above line */
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }

        .stamp-image {
            max-width: 12mm; /* Balanced size */
            max-height: 12mm;
            position: absolute;
            bottom: 0;
            right: 0;
            opacity: 0.85;
        }

        /* Footer - Proportional */
      .footer {
    margin: 0 5mm;
    padding: 0.5mm 0;
    border-top: 1px solid #ccc;
    text-align: center;
    font-size: 7pt; /* Clear footer text */
    color: #666;
    flex-shrink: 0;
    position: relative;
    min-height: 16mm; /* Balanced height */
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
    margin-bottom: 1mm;
}

.footer-text p {
    margin: 0.3mm 0;
}


   .certificate-stamp {
    max-width: 35mm;
    max-height: 20mm;
    opacity: 0.85;
    margin-top: 3mm; /* Added to move it down */
}

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Ensure signature columns are truly side by side */
        .signature-column:first-child {
            border-right: 1px dashed #ccc;
            padding-right: 1.5mm;
        }

        .signature-column:last-child {
            padding-left: 1.5mm;
        }

        /* QR Code - Proportional */
     .qr-code {
    max-width: 18mm; /* Balanced size */
    max-height: 18mm;
    margin: 0 3mm;
    margin-top: 3mm; /* Also moved down to align with stamp */
}

.stamp-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 0.5mm;
}

        /* Adjust declaration text */
        .declaration-text {
            text-align: justify;
            margin-bottom: 0.5mm;
        }

        /* Special emphasis for important text */
        .emphasis {
            font-weight: bold;
            color: #000;
        }

        /* Compact text class for long titles */
        .compact-title {
            font-size: 6.5pt;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Main Content Area -->
        <div class="main-content">
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
                    <div style="text-align: center; font-weight: bold; padding: 5px;">KPLC LOGO</div>
                @endif
            </div>

            <!-- Header -->
        <div class="header">
    <h1>CERTIFICATE OF ACCEPTANCE</h1>
    <h2>ISSUED BY</h2>
    <h3>THE KENYA POWER & LIGHTING COMPANY PLC</h3>
    <div class="certificate-number">CERTIFICATE NO.: {{ $acceptanceCertificate->certificate_ref ?? 'N/A' }}</div>
</div>
            <!-- Link Information -->
            <table class="info-table">
                <tr>
                    <td class="label">LESSOR:</td>
                    <td class="value">{{ $acceptanceCertificate->lessor ?? 'THE KENYA POWER & LIGHTING COMPANY PLC' }}</td>
                </tr>
                <tr>
                    <td class="label">LESSEE:</td>
                    <td class="value">{{ $acceptanceCertificate->lessee ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">ROUTE NAME:</td>
                    <td class="value">{{ $acceptanceCertificate->route_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">LINK NAME:</td>
                    <td class="value">{{ $acceptanceCertificate->link_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">CABLE TYPE:</td>
                    <td class="value">{{ $acceptanceCertificate->cable_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">DISTANCE:</td>
                    <td class="value">{{ isset($acceptanceCertificate->distance) ? number_format($acceptanceCertificate->distance, 3) . ' KM' : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">NO. OF CORES:</td>
                    <td class="value">{{ $acceptanceCertificate->cores_count ?? 'N/A' }}</td>
                </tr>
            </table>

            <!-- Declaration -->
            <div class="declaration-box">
                <p class="text-center text-bold">
                    The above link has been tested and fully accepted by the officers listed below.
                </p>
                <p class="declaration-text">
                    We hereby certify that the above dark fiber link comprising the Purchased Capacity has
                    been completed, tested and commissioned to Acceptable Standards. This Certificate of
                    Acceptance is a confirmation of the same and the date indicated herein is the Effective Date.
                </p>
                <p class="text-center">
                    This Certificate of Acceptance is issued dated the
                    <span class="date-line">{{ isset($acceptanceCertificate->effective_date) ? date('jS', strtotime($acceptanceCertificate->effective_date)) : '___' }}</span> day of
                    <span class="date-line">{{ isset($acceptanceCertificate->effective_date) ? date('F', strtotime($acceptanceCertificate->effective_date)) : '_________' }}</span>,
                    <span class="date-line">{{ isset($acceptanceCertificate->effective_date) ? date('Y', strtotime($acceptanceCertificate->effective_date)) : '____' }}</span>
                </p>
            </div>

            <!-- Two Columns Side by Side -->
            <div class="signatures-container no-break">
                <!-- Column 1: LESSOR -->
                <div class="signature-column">
                    <div class="signature-column-wrapper">
                        <div class="column-header">LESSOR: {{ $acceptanceCertificate->lessor ?? 'THE KENYA POWER & LIGHTING COMPANY PLC' }}</div>

                        <!-- Witness 1 -->
                        <div class="signature-entry">
                            <span class="signature-number">1.</span>
                            <div class="signature-details">
                                <div class="detail-group">
                                    <span class="detail-label">NAME:</span>
                                    <span class="detail-value">{{ $acceptanceCertificate->witness1_name ?? '________________' }}</span>
                                </div>
                                <div class="detail-group">
                                    <span class="detail-label">DATE:</span>
                                    <span class="detail-value">{{ isset($acceptanceCertificate->witness1_date) ? date('d/m/Y', strtotime($acceptanceCertificate->witness1_date)) : '__/__/____' }}</span>
                                </div>
                                <div class="signature-title compact-title">INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)</div>
                            </div>

                            <div class="signature-line-container">
                                @if(isset($acceptanceCertificate->witness1_signature_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->witness1_signature_path)))
                                    @php
                                        $sigPath1 = storage_path('app/public/' . $acceptanceCertificate->witness1_signature_path);
                                        $sigData1 = base64_encode(file_get_contents($sigPath1));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $sigData1 }}" class="signature-image">
                                @endif

                                <div class="signature-line">
                                    <div class="signature-label">SIGNATURE</div>
                                </div>

                                @if(isset($acceptanceCertificate->witness1_stamp_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->witness1_stamp_path)))
                                    @php
                                        $stampPath1 = storage_path('app/public/' . $acceptanceCertificate->witness1_stamp_path);
                                        $stampData1 = base64_encode(file_get_contents($stampPath1));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $stampData1 }}" class="stamp-image">
                                @endif
                            </div>
                        </div>

                        <!-- Witness 2 -->
                        <div class="signature-entry">
                            <span class="signature-number">2.</span>
                            <div class="signature-details">
                                <div class="detail-group">
                                    <span class="detail-label">NAME:</span>
                                    <span class="detail-value">{{ $acceptanceCertificate->witness2_name ?? '________________' }}</span>
                                </div>
                                <div class="detail-group">
                                    <span class="detail-label">DATE:</span>
                                    <span class="detail-value">{{ isset($acceptanceCertificate->witness2_date) ? date('d/m/Y', strtotime($acceptanceCertificate->witness2_date)) : '__/__/____' }}</span>
                                </div>
                                <div class="signature-title compact-title">TELECOM LEAD ENGINEER, Kenya Power</div>
                            </div>

                            <div class="signature-line-container">
                                @if(isset($acceptanceCertificate->witness2_signature_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->witness2_signature_path)))
                                    @php
                                        $sigPath2 = storage_path('app/public/' . $acceptanceCertificate->witness2_signature_path);
                                        $sigData2 = base64_encode(file_get_contents($sigPath2));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $sigData2 }}" class="signature-image">
                                @endif

                                <div class="signature-line">
                                    <div class="signature-label">SIGNATURE</div>
                                </div>

                                @if(isset($acceptanceCertificate->witness2_stamp_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->witness2_stamp_path)))
                                    @php
                                        $stampPath2 = storage_path('app/public/' . $acceptanceCertificate->witness2_stamp_path);
                                        $stampData2 = base64_encode(file_get_contents($stampPath2));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $stampData2 }}" class="stamp-image">
                                @endif
                            </div>
                        </div>

                        <!-- Witness 3 -->
                        <div class="signature-entry">
                            <span class="signature-number">3.</span>
                            <div class="signature-details">
                                <div class="detail-group">
                                    <span class="detail-label">NAME:</span>
                                    <span class="detail-value">{{ $acceptanceCertificate->witness3_name ?? '________________' }}</span>
                                </div>
                                <div class="detail-group">
                                    <span class="detail-label">DATE:</span>
                                    <span class="detail-value">{{ isset($acceptanceCertificate->witness3_date) ? date('d/m/Y', strtotime($acceptanceCertificate->witness3_date)) : '__/__/____' }}</span>
                                </div>
                                <div class="signature-title compact-title">TELECOM MANAGER, Kenya Power</div>
                            </div>

                            <div class="signature-line-container">
                                @if(isset($acceptanceCertificate->witness3_signature_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->witness3_signature_path)))
                                    @php
                                        $sigPath3 = storage_path('app/public/' . $acceptanceCertificate->witness3_signature_path);
                                        $sigData3 = base64_encode(file_get_contents($sigPath3));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $sigData3 }}" class="signature-image">
                                @endif

                                <div class="signature-line">
                                    <div class="signature-label">SIGNATURE</div>
                                </div>

                                @if(isset($acceptanceCertificate->witness3_stamp_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->witness3_stamp_path)))
                                    @php
                                        $stampPath3 = storage_path('app/public/' . $acceptanceCertificate->witness3_stamp_path);
                                        $stampData3 = base64_encode(file_get_contents($stampPath3));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $stampData3 }}" class="stamp-image">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: LESSEE -->
                <div class="signature-column">
                    <div class="signature-column-wrapper">
                        <div class="column-header">LESSEE: {{ $acceptanceCertificate->lessee ?? 'N/A' }}</div>

                        <!-- Lessee 1 -->
                        <div class="signature-entry">
                            <span class="signature-number">4.</span>
                            <div class="signature-details">
                                <div class="detail-group">
                                    <span class="detail-label">NAME:</span>
                                    <span class="detail-value">{{ $acceptanceCertificate->lessee1_name ?? '________________' }}</span>
                                </div>
                                <div class="detail-group">
                                    <span class="detail-label">DATE:</span>
                                    <span class="detail-value">{{ isset($acceptanceCertificate->lessee1_date) ? date('d/m/Y', strtotime($acceptanceCertificate->lessee1_date)) : '__/__/____' }}</span>
                                </div>
                                <div class="signature-title compact-title">LEAD ENGINEER / TECHNICAL REP.</div>
                            </div>

                            <div class="signature-line-container">
                                @if(isset($acceptanceCertificate->lessee1_signature_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->lessee1_signature_path)))
                                    @php
                                        $sigPath4 = storage_path('app/public/' . $acceptanceCertificate->lessee1_signature_path);
                                        $sigData4 = base64_encode(file_get_contents($sigPath4));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $sigData4 }}" class="signature-image">
                                @endif

                                <div class="signature-line">
                                    <div class="signature-label">SIGNATURE</div>
                                </div>

                                @if(isset($acceptanceCertificate->lessee1_stamp_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->lessee1_stamp_path)))
                                    @php
                                        $stampPath4 = storage_path('app/public/' . $acceptanceCertificate->lessee1_stamp_path);
                                        $stampData4 = base64_encode(file_get_contents($stampPath4));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $stampData4 }}" class="stamp-image">
                                @endif
                            </div>
                        </div>

                        <!-- Lessee 2 -->
                        <div class="signature-entry">
                            <span class="signature-number">5.</span>
                            <div class="signature-details">
                                <div class="detail-group">
                                    <span class="detail-label">NAME:</span>
                                    <span class="detail-value">{{ $acceptanceCertificate->lessee2_name ?? '________________' }}</span>
                                </div>
                                <div class="detail-group">
                                    <span class="detail-label">DATE:</span>
                                    <span class="detail-value">{{ isset($acceptanceCertificate->lessee2_date) ? date('d/m/Y', strtotime($acceptanceCertificate->lessee2_date)) : '__/__/____' }}</span>
                                </div>
                                <div class="signature-title compact-title">MANAGER</div>
                            </div>

                            <div class="signature-line-container">
                                @if(isset($acceptanceCertificate->lessee2_signature_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->lessee2_signature_path)))
                                    @php
                                        $sigPath5 = storage_path('app/public/' . $acceptanceCertificate->lessee2_signature_path);
                                        $sigData5 = base64_encode(file_get_contents($sigPath5));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $sigData5 }}" class="signature-image">
                                @endif

                                <div class="signature-line">
                                    <div class="signature-label">SIGNATURE</div>
                                </div>

                                @if(isset($acceptanceCertificate->lessee2_stamp_path) && file_exists(storage_path('app/public/' . $acceptanceCertificate->lessee2_stamp_path)))
                                    @php
                                        $stampPath5 = storage_path('app/public/' . $acceptanceCertificate->lessee2_stamp_path);
                                        $stampData5 = base64_encode(file_get_contents($stampPath5));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $stampData5 }}" class="stamp-image">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer - Balanced -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-text">
                    <p class="text-bold">REFERENCE: {{ $acceptanceCertificate->certificate_ref ?? 'N/A' }}</p>
                    <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
                    <p><em>This is an official Certificate of Acceptance. All information must be verified before use.</em></p>
                </div>
                <div class="stamp-container">
                    @php
                        $stampPath = public_path('images/certificatestamp.png');
                        $qrPath = public_path('images/qrcode.png');
                    @endphp

                    @if(file_exists($stampPath))
                        @php
                            $stampData = base64_encode(file_get_contents($stampPath));
                        @endphp
                        <img src="data:image/png;base64,{{ $stampData }}" alt="Certificate Stamp" class="certificate-stamp">
                    @endif

                    @if(file_exists($qrPath))
                        @php
                            $qrData = base64_encode(file_get_contents($qrPath));
                        @endphp
                        <img src="data:image/png;base64,{{ $qrData }}" alt="QR Code" class="qr-code">
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
