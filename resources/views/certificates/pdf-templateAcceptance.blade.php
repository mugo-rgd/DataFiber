<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate of Acceptance - Kenya Power</title>
    <style>
        @page {
            margin: 8mm 12mm;
            size: A4;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1;
            color: #000;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
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

        /* Main content area that grows */
        .main-content {
            flex: 1;
            overflow: hidden;
        }

        /* Logo */
        .logo-section {
            text-align: center;
            margin: 3mm 0 2mm 0;
        }

        .logo-img {
            height: 40px;
            max-width: 110px;
        }

        /* Header */
        .header {
            text-align: center;
            margin: 0 0 3mm 0;
            padding: 1mm 0 2mm 0;
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
            margin: 1mm 0;
            color: #0056b3;
        }

        .header h3 {
            font-size: 10pt;
            margin: 1mm 0;
        }

        /* Link Information */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 5mm 2mm 5mm;
            font-size: 9pt;
        }

        .info-table td {
            padding: 1.5mm 2mm;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            width: 35%;
        }

        /* Declaration */
        .declaration-box {
            margin: 0 5mm 2mm 5mm;
            padding: 3mm;
            border: 1px solid #000;
            background: #f8f9fa;
            font-size: 9pt;
            line-height: 1.1;
        }

        .declaration-box p {
            margin: 1mm 0;
        }

        .date-line {
            display: inline-block;
            min-width: 20mm;
            border-bottom: 1px solid #000;
            text-align: center;
            margin: 0 1mm;
            font-size: 9pt;
        }

        /* Two Column Layout for LESSOR and LESSEE */
        .signatures-container {
            display: flex;
            gap: 4mm;
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
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            margin: 0 0 2mm 0;
            padding-bottom: 1mm;
            border-bottom: 1px solid #000;
        }

        /* Clean signature entries without boxes */
        .signature-entry {
            margin-bottom: 1mm;
            min-height: 18mm;
            position: relative;
        }

        .signature-number {
            position: absolute;
            top: 0;
            left: 0;
            font-weight: bold;
            font-size: 9pt;
        }

        /* Name and Date in one line */
        .signature-details {
            margin-left: 4mm;
            padding-right: 2mm;
            display: flex;
            flex-wrap: wrap;
            gap: 3mm;
            align-items: flex-start;
        }

        .detail-group {
            display: flex;
            align-items: center;
            margin-bottom: 0.3mm;
        }

        .detail-label {
            font-size: 8pt;
            font-weight: bold;
            margin-right: 1mm;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .detail-value {
            font-size: 8pt;
            margin: 0;
            white-space: nowrap;
            font-weight: normal;
        }

        .signature-title {
            font-size: 7pt;
            font-weight: bold;
            margin: 0.3mm 0 0 0;
            width: 100%;
        }

        .signature-line-container {
            margin-top: 1.5mm;
            position: relative;
            height: 6mm;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 0.5mm;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .signature-label {
            font-size: 6pt;
            text-align: center;
            margin-top: 0.3mm;
        }

        .signature-image {
            max-width: 40mm;
            max-height: 8mm;
            position: absolute;
            bottom: 1.5mm;
            left: 0;
        }

        .stamp-image {
            max-width: 14mm;
            max-height: 14mm;
            position: absolute;
            bottom: 0;
            right: 0;
            opacity: 0.9;
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
            padding-right: 2mm;
        }

        .signature-column:last-child {
            padding-left: 2mm;
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
                @endif
            </div>

            <!-- Header -->
            <div class="header">
                <h1>CERTIFICATE OF ACCEPTANCE</h1>
                <h2>ISSUED BY</h2>
                <h3>THE KENYA POWER & LIGHTING COMPANY PLC</h3>
            </div>

            <!-- Link Information -->
            <table class="info-table">
                <tr>
                    <td class="label">LESSOR:</td>
                    <td>THE KENYA POWER & LIGHTING COMPANY PLC</td>
                </tr>
                <tr>
                    <td class="label">LESSEE:</td>
                    <td>{{ $certificate->lessee }}</td>
                </tr>
                <tr>
                    <td class="label">ROUTE NAME:</td>
                    <td>{{ $certificate->route_name }}</td>
                </tr>
                <tr>
                    <td class="label">LINK NAME:</td>
                    <td>{{ $certificate->link_name }}</td>
                </tr>
                <tr>
                    <td class="label">CABLE TYPE:</td>
                    <td>{{ $certificate->cable_type }}</td>
                </tr>
                <tr>
                    <td class="label">DISTANCE:</td>
                    <td>{{ number_format($certificate->distance, 3) }} KM</td>
                </tr>
                <tr>
                    <td class="label">NO. OF CORES:</td>
                    <td>{{ $certificate->cores_count }}</td>
            </tr>
        </table>

        <!-- Declaration -->
        <div class="declaration-box">
            <p class="text-center text-bold">
                The above link has been tested and fully accepted by the officers listed below.
            </p>
            <p>
                We hereby certify that the above dark fiber link comprising the Purchased Capacity has
                been completed, tested and commissioned to Acceptable Standards. This Certificate of
                Acceptance is a confirmation of the same and the date indicated herein is the Effective Date.
            </p>
            <p class="text-center">
                This Certificate of Acceptance is issued dated the
                <span class="date-line">{{ date('jS', strtotime($certificate->effective_date)) }}</span> day of
                <span class="date-line">{{ date('F', strtotime($certificate->effective_date)) }}</span>,
                <span class="date-line">{{ date('Y', strtotime($certificate->effective_date)) }}</span>
            </p>
        </div>

        <!-- Two Columns Side by Side -->
        <div class="signatures-container no-break">
            <!-- Column 1: LESSOR -->
            <div class="signature-column">
                <div class="signature-column-wrapper">
                    <div class="column-header">LESSOR: THE KENYA POWER & LIGHTING COMPANY PLC</div>

                    <!-- Witness 1 -->
                    <div class="signature-entry">
                        <span class="signature-number">1.</span>
                        <div class="signature-details">
                            <div class="detail-group">
                                <span class="detail-label">NAME:</span>
                                <span class="detail-value">{{ $certificate->witness1_name }}</span>
                            </div>
                            <div class="detail-group">
                                <span class="detail-label">DATE:</span>
                                <span class="detail-value">{{ date('d/m/Y', strtotime($certificate->witness1_date)) }}</span>
                            </div>
                            <div class="signature-title">INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)</div>
                        </div>

                        <div class="signature-line-container">
                            <div class="signature-line">
                                @if($certificate->witness1_signature_path)
                                    @php
                                        $sigPath1 = storage_path('app/public/' . $certificate->witness1_signature_path);
                                        if (file_exists($sigPath1)) {
                                            $sigData1 = base64_encode(file_get_contents($sigPath1));
                                        }
                                    @endphp
                                    @if(isset($sigData1))
                                        <img src="data:image/png;base64,{{ $sigData1 }}" class="signature-image">
                                    @endif
                                @endif
                                <div class="signature-label">SIGNATURE</div>
                            </div>

                            @if($certificate->witness1_stamp_path)
                                @php
                                    $stampPath1 = storage_path('app/public/' . $certificate->witness1_stamp_path);
                                    if (file_exists($stampPath1)) {
                                        $stampData1 = base64_encode(file_get_contents($stampPath1));
                                    }
                                @endphp
                                @if(isset($stampData1))
                                    <img src="data:image/png;base64,{{ $stampData1 }}" class="stamp-image">
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Witness 2 -->
                    <div class="signature-entry">
                        <span class="signature-number">2.</span>
                        <div class="signature-details">
                            <div class="detail-group">
                                <span class="detail-label">NAME:</span>
                                <span class="detail-value">{{ $certificate->witness2_name }}</span>
                            </div>
                            <div class="detail-group">
                                <span class="detail-label">DATE:</span>
                                <span class="detail-value">{{ date('d/m/Y', strtotime($certificate->witness2_date)) }}</span>
                            </div>
                            <div class="signature-title">TELECOM LEAD ENGINEER, Kenya Power</div>
                        </div>

                        <div class="signature-line-container">
                            <div class="signature-line">
                                @if($certificate->witness2_signature_path)
                                    @php
                                        $sigPath2 = storage_path('app/public/' . $certificate->witness2_signature_path);
                                        if (file_exists($sigPath2)) {
                                            $sigData2 = base64_encode(file_get_contents($sigPath2));
                                        }
                                    @endphp
                                    @if(isset($sigData2))
                                        <img src="data:image/png;base64,{{ $sigData2 }}" class="signature-image">
                                    @endif
                                @endif
                                <div class="signature-label">SIGNATURE</div>
                            </div>

                            @if($certificate->witness2_stamp_path)
                                @php
                                    $stampPath2 = storage_path('app/public/' . $certificate->witness2_stamp_path);
                                    if (file_exists($stampPath2)) {
                                        $stampData2 = base64_encode(file_get_contents($stampPath2));
                                    }
                                @endphp
                                @if(isset($stampData2))
                                    <img src="data:image/png;base64,{{ $stampData2 }}" class="stamp-image">
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Witness 3 -->
                    <div class="signature-entry">
                        <span class="signature-number">3.</span>
                        <div class="signature-details">
                            <div class="detail-group">
                                <span class="detail-label">NAME:</span>
                                <span class="detail-value">{{ $certificate->witness3_name }}</span>
                            </div>
                            <div class="detail-group">
                                <span class="detail-label">DATE:</span>
                                <span class="detail-value">{{ date('d/m/Y', strtotime($certificate->witness3_date)) }}</span>
                            </div>
                            <div class="signature-title">TELECOM MANAGER, Kenya Power</div>
                        </div>

                        <div class="signature-line-container">
                            <div class="signature-line">
                                @if($certificate->witness3_signature_path)
                                    @php
                                        $sigPath3 = storage_path('app/public/' . $certificate->witness3_signature_path);
                                        if (file_exists($sigPath3)) {
                                            $sigData3 = base64_encode(file_get_contents($sigPath3));
                                        }
                                    @endphp
                                    @if(isset($sigData3))
                                        <img src="data:image/png;base64,{{ $sigData3 }}" class="signature-image">
                                    @endif
                                @endif
                                <div class="signature-label">SIGNATURE</div>
                            </div>

                            @if($certificate->witness3_stamp_path)
                                @php
                                    $stampPath3 = storage_path('app/public/' . $certificate->witness3_stamp_path);
                                    if (file_exists($stampPath3)) {
                                        $stampData3 = base64_encode(file_get_contents($stampPath3));
                                    }
                                @endphp
                                @if(isset($stampData3))
                                    <img src="data:image/png;base64,{{ $stampData3 }}" class="stamp-image">
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column 2: LESSEE -->
            <div class="signature-column">
                <div class="signature-column-wrapper">
                    <div class="column-header">LESSEE: {{ $certificate->lessee }}</div>

                    <!-- Lessee 1 -->
                    <div class="signature-entry">
                        <span class="signature-number">4.</span>
                        <div class="signature-details">
                            <div class="detail-group">
                                <span class="detail-label">NAME:</span>
                                <span class="detail-value">{{ $certificate->lessee1_name }}</span>
                            </div>
                            <div class="detail-group">
                                <span class="detail-label">DATE:</span>
                                <span class="detail-value">{{ date('d/m/Y', strtotime($certificate->lessee1_date)) }}</span>
                            </div>
                            <div class="signature-title">LEAD ENGINEER / TECHNICAL REP.</div>
                        </div>

                        <div class="signature-line-container">
                            <div class="signature-line">
                                @if($certificate->lessee1_signature_path)
                                    @php
                                        $sigPath4 = storage_path('app/public/' . $certificate->lessee1_signature_path);
                                        if (file_exists($sigPath4)) {
                                            $sigData4 = base64_encode(file_get_contents($sigPath4));
                                        }
                                    @endphp
                                    @if(isset($sigData4))
                                        <img src="data:image/png;base64,{{ $sigData4 }}" class="signature-image">
                                    @endif
                                @endif
                                <div class="signature-label">SIGNATURE</div>
                            </div>

                            @if($certificate->lessee1_stamp_path)
                                @php
                                    $stampPath4 = storage_path('app/public/' . $certificate->lessee1_stamp_path);
                                    if (file_exists($stampPath4)) {
                                        $stampData4 = base64_encode(file_get_contents($stampPath4));
                                    }
                                @endphp
                                @if(isset($stampData4))
                                    <img src="data:image/png;base64,{{ $stampData4 }}" class="stamp-image">
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Lessee 2 -->
                    <div class="signature-entry">
                        <span class="signature-number">5.</span>
                        <div class="signature-details">
                            <div class="detail-group">
                                <span class="detail-label">NAME:</span>
                                <span class="detail-value">{{ $certificate->lessee2_name }}</span>
                            </div>
                            <div class="detail-group">
                                <span class="detail-label">DATE:</span>
                                <span class="detail-value">{{ date('d/m/Y', strtotime($certificate->lessee2_date)) }}</span>
                            </div>
                            <div class="signature-title">MANAGER</div>
                        </div>

                        <div class="signature-line-container">
                            <div class="signature-line">
                                @if($certificate->lessee2_signature_path)
                                    @php
                                        $sigPath5 = storage_path('app/public/' . $certificate->lessee2_signature_path);
                                        if (file_exists($sigPath5)) {
                                            $sigData5 = base64_encode(file_get_contents($sigPath5));
                                        }
                                    @endphp
                                    @if(isset($sigData5))
                                        <img src="data:image/png;base64,{{ $sigData5 }}" class="signature-image">
                                    @endif
                                @endif
                                <div class="signature-label">SIGNATURE</div>
                            </div>

                            @if($certificate->lessee2_stamp_path)
                                @php
                                    $stampPath5 = storage_path('app/public/' . $certificate->lessee2_stamp_path);
                                    if (file_exists($stampPath5)) {
                                        $stampData5 = base64_encode(file_get_contents($stampPath5));
                                    }
                                @endphp
                                @if(isset($stampData5))
                                    <img src="data:image/png;base64,{{ $stampData5 }}" class="stamp-image">
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer - Centered everything -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-text">
                <p class="text-bold">REFERENCE: {{ $certificate->certificate_ref }}</p>
                <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
                <p><em>This is an official Certificate of Acceptance. All information must be verified before use.</em></p>
            <br></br>
            </div>
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
