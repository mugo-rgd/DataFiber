<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .field {
            margin-bottom: 8px;
        }
        .field-label {
            font-weight: bold;
            display: inline-block;
            width: 250px;
        }
        .signature-area {
            margin-top: 60px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 300px;
            margin-top: 40px;
        }
        .footer {
            margin-top: 100px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Conditional Certificate</div>
        <div class="subtitle">Issued by Kenya Power</div>
        <div class="field">
            <span class="field-label">REF:</span>
            <span>{{ $certificate->ref_number }}</span>
        </div>
    </div>

    <div class="section">
        <div class="field">
            <span class="field-label">Lessor:</span>
            <span>{{ $certificate->lessor }}</span>
        </div>
        <div class="field">
            <span class="field-label">Lessee:</span>
            <span>{{ $certificate->lessee }}</span>
        </div>
        <div class="field">
            <span class="field-label">Name of the link:</span>
            <span>{{ $certificate->link_name }}</span>
        </div>
        <div class="field">
            <span class="field-label">Serial No. of the OTDR:</span>
            <span>{{ $certificate->otdr_serial }}</span>
        </div>
        <div class="field">
            <span class="field-label">Date of Calibration:</span>
            <span>{{ \Carbon\Carbon::parse($certificate->calibration_date)->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Commissioning Parameters</div>

        <div class="subsection">
            <strong>1. Physical characteristics of the Dark fibre cable</strong>
            <div class="field">
                <span class="field-label">NAME OF SITE (A):</span>
                <span>{{ $certificate->site_a }}</span>
            </div>
            <div class="field">
                <span class="field-label">NAME OF SITE (B):</span>
                <span>{{ $certificate->site_b }}</span>
            </div>
            <div class="field">
                <span class="field-label">a. Fibre Cable Technology (ADSS/Fig 8/OPGW):</span>
                <span>{{ $certificate->fibre_technology }}</span>
            </div>
            <div class="field">
                <span class="field-label">b. ODF - Connector Type:</span>
                <span>{{ $certificate->odf_connector_type }}</span>
            </div>
        </div>

        <div class="subsection">
            <strong>2. Dark Fibre link End-to-End Tests</strong>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Result</th>
                </tr>
                <tr>
                    <td>a. Total Fibre Length (Km)</td>
                    <td>{{ $certificate->total_fibre_length }}</td>
                </tr>
                <tr>
                    <td>b. Average Link Loss (dB)</td>
                    <td>{{ $certificate->average_link_loss }}</td>
                </tr>
                <tr>
                    <td>c. No of Splice Joints</td>
                    <td>{{ $certificate->splice_joints }}</td>
                </tr>
                <tr>
                    <td>d. Test wavelength (nm)</td>
                    <td>{{ $certificate->test_wavelength }}</td>
                </tr>
                <tr>
                    <td>e. IOR</td>
                    <td>{{ $certificate->ior }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <p>The above table contains the test results of the Dark Fiber link performed by Kenya Power and witnessed by the Lessee.</p>
        <p>The Lessee is hereby granted thirty (30) days within which to conduct such tests and procedures as shall be necessary to satisfy the quality of the Dark Fibers cores leased.</p>
    </div>

    <div class="signature-area">
        <div class="field">
            <span class="field-label">Name:</span>
            <span>{{ $certificate->engineer_name }}</span>
        </div>
        <div class="field">
            <span class="field-label">Date:</span>
            <span>{{ \Carbon\Carbon::parse($certificate->certificate_date)->format('d/m/Y') }}</span>
        </div>

        <div class="section-title">KPLC LEAD ENGINEER Technical Services Support</div>
        <div class="signature-line"></div>
        <div>Signature</div>
        <div class="signature-line"></div>
        <div>Stamp</div>
    </div>

    <div class="signature-area">
        <div class="section-title">LESSEE</div>
        <div class="field">
            <span class="field-label">Name:</span>
            <span>_______________________</span>
        </div>
        <div class="field">
            <span class="field-label">Date:</span>
            <span>_______________________</span>
        </div>
        <div class="field">
            <span class="field-label">Designation:</span>
            <span>_______________________</span>
        </div>
        <div class="signature-line"></div>
        <div>Signature</div>
        <div class="signature-line"></div>
        <div>Stamp</div>
    </div>

    <div class="section">
        <div class="section-title">LESSOR: THE KENYA POWER & LIGHTING COMPANY PLC</div>
        <p>This Certificate is dated the {{ \Carbon\Carbon::parse($certificate->certificate_date)->format('jS') }} day of {{ \Carbon\Carbon::parse($certificate->certificate_date)->format('F Y') }}.</p>
        <p>The Commissioning period ends dated the {{ \Carbon\Carbon::parse($certificate->commissioning_end_date)->format('jS') }} day of {{ \Carbon\Carbon::parse($certificate->commissioning_end_date)->format('F Y') }}.</p>
    </div>

    <div class="footer">
        <p><strong>NB</strong></p>
        <p>1. The fibre core(s) shall be considered accepted by the purchaser upon the expiration of the test period without any formal notification on the contrary.</p>
        <p>2. Attach Test Report – Optical Time Domain Reflectometer (OTDR) Trace.</p>
    </div>
</body>
</html>
