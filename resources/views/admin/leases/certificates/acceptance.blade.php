<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kenya Power Certificates</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.1;
            color: #333;
            margin: 0;
            padding: 10px;
            font-size: 11px;
        }

        .certificate {
            margin-bottom: 20px;
            page-break-after: always;
            border: 1px solid #ddd;
            padding: 15px;
            position: relative;
        }

        /* Remove page break after the last certificate */
        .certificate:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }

        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .logo {
            max-width: 70px;
            max-height: 35px;
            display: block;
        }

        .logo-left {
            flex: 1;
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .logo-right {
            flex: 1;
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .title-section {
            flex: 1.5;
            text-align: center;
            padding: 0 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .title-section h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .subtitle {
            color: #7f8c8d;
            font-size: 9px;
            margin: 1px 0;
        }

        .stamp {
            border: 2px solid #e74c3c;
            background-color: #fbeaea;
            padding: 2px 6px;
            display: inline-block;
            margin-top: 2px;
            font-weight: bold;
            color: #e74c3c;
            font-size: 8px;
            line-height: 1.1;
        }

        .certificate-info,
        .section {
            margin-bottom: 12px;
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 6px 8px;
            font-weight: bold;
            border-left: 4px solid #3498db;
            margin-bottom: 8px;
            font-size: 11px;
            line-height: 1.1;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .details-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #ddd;
            line-height: 1.1;
        }

        .details-table td:first-child {
            font-weight: bold;
            width: 35%;
        }

        .signature-section {
            margin-top: 20px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            line-height: 1.1;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0,0,0,0.05);
            z-index: -1;
            font-weight: bold;
        }

        .terms-conditions {
            margin-top: 15px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 10px;
            line-height: 1.2;
        }

        p {
            margin: 6px 0;
            line-height: 1.2;
        }

        ul {
            margin: 4px 0;
            padding-left: 12px;
            line-height: 1.2;
        }

        li {
            margin: 1px 0;
            line-height: 1.2;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .signature-section table td {
            padding: 3px;
            line-height: 1.1;
            font-size: 10px;
        }

        .colocation-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }

        .colocation-table th, .colocation-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        .colocation-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .signature-blocks {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .signature-blocks td {
            vertical-align: top;
            padding: 8px;
        }

        .test-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }

        .test-table th, .test-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        .test-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .commissioning-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }

        .commissioning-table th, .commissioning-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        .commissioning-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* LESSEE LOGO POSITIONING */
        .lessee-logo-container {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        .lessee-logo {
            max-width: 70px;
            max-height: 35px;
            display: block;
        }

        /* PRINT STYLES */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .certificate {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 10px;
                page-break-after: always;
                page-break-inside: avoid;
            }

            /* Remove page break after the last certificate in print */
            .certificate:last-child {
                page-break-after: auto;
            }

            .watermark {
                display: block;
            }

            .footer {
                position: relative;
                bottom: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Conditional Certificate -->
    <div class="certificate">
        <!-- Watermark -->
        <div class="watermark">CONDITIONAL CERTIFICATE</div>

        <!-- Lessee Logo - Top Right Corner -->
        <div class="lessee-logo-container">
            <img src="images/safaricomlogo.png" class="logo" alt="Lessee Logo">
        </div>

        <!-- Header -->
        <div class="header">
            <!-- Licensor - Left Aligned -->
            <div class="logo-left">
                <img src="images/logo.png" class="logo" alt="Kenya Power Logo">
            </div>

            <!-- Title - Center -->
            <div class="title-section">
                <h1>Conditional Certificate</h1>
                <div class="subtitle">Dark Fiber Link</div>
                <div class="stamp">ISSUED BY KENYA POWER</div>
            </div>

            <!-- Empty right section to maintain layout -->
            <div class="logo-right"></div>
        </div>

        <!-- Reference -->
        <div class="section">
            <div class="section-title">Reference</div>
            <p><strong>REF:</strong> ....................................................................................</p>
        </div>

        <!-- Parties Info -->
        <div class="section">
            <div class="section-title">Parties Information</div>
            <table class="details-table">
                <tr><td>Lessor:</td><td>..........................................................................................................................</td></tr>
                <tr><td>Lessee:</td><td>........................................................................................................................</td></tr>
                <tr><td>Name of the link:</td><td>........................................................................................................................</td></tr>
                <tr><td>Serial No. of the OTDR:</td><td>......................................................</td></tr>
                <tr><td>Date of Calibration:</td><td>.........................................................</td></tr>
            </table>
        </div>

        <!-- Commissioning Parameters -->
        <div class="section">
            <div class="section-title">Commissioning Parameters</div>

            <table class="commissioning-table">
                <tr>
                    <th colspan="3">1. Physical characteristics of the Dark fibre cable</th>
                </tr>
                <tr>
                    <td width="40%"></td>
                    <td width="30%"><strong>NAME OF SITE (A)</strong><br>........................</td>
                    <td width="30%"><strong>NAME OF SITE (B)</strong><br>...........................</td>
                </tr>
                <tr>
                    <td>a. Fibre Cable Technology (ADSS/Fig 8/OPGW)</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>b. ODF - Connector Type</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="3">2. Dark Fibre link End-to-End Tests</th>
                </tr>
                <tr>
                    <td>a. Total Fibre Length (Km)</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>b. Average Link Loss (dB)</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>c. No of Splice Joints</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>d. Test wavelength (nm)</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>e. IOR</td>
                    <td colspan="2"></td>
                </tr>
            </table>
        </div>

        <!-- Declaration -->
        <div class="section">
            <div class="section-title">Declaration</div>
            <p>The above table contains the test results of the Dark Fiber link performed by Kenya Power and witnessed by the Lessee. The Lessee is hereby granted thirty (30) days within which to conduct such tests and procedures as shall be necessary to satisfy the quality of the Dark Fibers cores leased.</p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <table class="signature-blocks">
                <tr>
                    <td width="50%">
                        <strong>LESSOR: THE KENYA POWER & LIGHTING CO. PLC</strong>
                        <table width="100%" style="margin-top: 8px;">
                            <tr>
                                <td width="70%"><strong>Name:</strong> ..................................................</td>
                                <td><strong>Date:</strong> ..................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">KPLC LEAD ENGINEER Technical Services Support</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ..............................................</td>
                                <td><strong>Stamp:</strong> ..............................................</td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%">
                        <strong>LESSEE:</strong>
                        <table width="100%" style="margin-top: 8px;">
                            <tr>
                                <td width="70%"><strong>Name:</strong> ..................................................</td>
                                <td><strong>Date:</strong> ..................................</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Designation:</strong> ..................................................</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ..............................................</td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Certificate Date -->
        <div class="section">
            <div class="section-title">Certificate Date</div>
            <p>This Certificate is dated the <strong>...................</strong> day of <strong>........................... 20................</strong>. The Commissioning period ends dated the <strong>................</strong> day of <strong>.................. 20.....................</strong></p>
        </div>

        <!-- Notes -->
        <div class="terms-conditions">
            <strong>NB:</strong>
            <ul>
                <li>The fibre core(s) shall be considered accepted by the purchaser upon the expiration of the test period without any formal notification on the contrary.</li>
                <li>Attach Test Report -- Optical Time Domain Reflectometer (OTDR) Trace.</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated acceptance certificate. No physical signature is required.</p>
            <p>Generated on: October 15, 2025 at 16:57</p>
            <p>Certificate ID: ACC-COND-001</p>
        </div>
    </div>

    <!-- Certificate of Acceptance -->
    <div class="certificate">
        <!-- Watermark -->
        <div class="watermark">CERTIFICATE OF ACCEPTANCE</div>

        <!-- Lessee Logo - Top Right Corner -->
        <div class="lessee-logo-container">
            <img src="images/safaricomlogo.png" class="logo" alt="Lessee Logo">
        </div>

        <!-- Header -->
        <div class="header">
            <!-- Licensor - Left Aligned -->
            <div class="logo-left">
                <img src="images/logo.png" class="logo" alt="Kenya Power Logo">
            </div>

            <!-- Title - Center -->
            <div class="title-section">
                <h1>Certificate of Acceptance</h1>
                <div class="subtitle">Dark Fiber Link</div>
                <div class="stamp">ISSUED BY KENYA POWER</div>
            </div>

            <!-- Empty right section to maintain layout -->
            <div class="logo-right"></div>
        </div>

        <!-- Certificate Info -->
        <div class="certificate-info">
            <p>This document certifies that the dark fiber link has been accepted and is now in effect between the parties mentioned below.</p>
        </div>

        <!-- Parties Info -->
        <div class="section">
            <div class="section-title">Parties Information</div>
            <table class="details-table">
                <tr><td>Lessor:</td><td>THE KENYA POWER & LIGHTING CO. PLC</td></tr>
                <tr><td>Lessee:</td><td>...........................................................</td></tr>
                <tr><td>Name of the Route:</td><td>...............................................</td></tr>
                <tr><td>Name of the Link:</td><td>................................................</td></tr>
                <tr><td>Cable Type:</td><td>............</td></tr>
                <tr><td>Distance:</td><td>............... KM</td></tr>
                <tr><td>No. Of Cores:</td><td>..............</td></tr>
            </table>
        </div>

        <!-- Declaration -->
        <div class="section">
            <div class="section-title">Acceptance Declaration</div>
            <p>The above link has been tested and fully accepted by the officers listed in the table below.</p>
            <p>We hereby certify that the above dark fiber link comprising the Purchased Capacity has been completed, tested and commissioned to Acceptable Standards. This Certificate of Acceptance is a confirmation of the same and the date indicated herein is the Effective Date.</p>
        </div>

        <!-- Certificate Date -->
        <div class="section">
            <div class="section-title">Certificate Date</div>
            <p>This Certificate of Acceptance is issued dated the <strong>.........</strong> day of <strong>.........</strong>, <strong>............</strong></p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <table class="signature-blocks">
                <tr>
                    <td width="50%">
                        <strong>LESSEE:</strong>
                        <table width="100%" style="margin-top: 8px;">
                            <tr>
                                <td width="70%"><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ..................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">LEAD ENGINEER / TECHNICAL REP.</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td><strong>Stamp:</strong> .................................</td>
                            </tr>
                            <tr>
                                <td><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">MANAGER</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td><strong>Stamp:</strong> ................................</td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%">
                        <strong>LESSOR: THE KENYA POWER & LIGHTING CO. PLC</strong>
                        <table width="100%" style="margin-top: 8px;">
                            <tr>
                                <td width="70%"><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ..................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">TELECOM LEAD ENGINEER, Kenya Power</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td><strong>Stamp:</strong> ................................</td>
                            </tr>
                            <tr>
                                <td><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">TELECOM MANAGER, Kenya Power</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td><strong>Stamp:</strong> ................................</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated acceptance certificate. No physical signature is required.</p>
            <p>Generated on: October 15, 2025 at 16:57</p>
            <p>Certificate ID: ACC-DFL-001</p>
        </div>
    </div>

    <!-- Change Request Certificate of Acceptance -->
    <div class="certificate">
        <!-- Watermark -->
        <div class="watermark">CHANGE REQUEST CERTIFICATE</div>

        <!-- Lessee Logo - Top Right Corner -->
        <div class="lessee-logo-container">
            <img src="images/safaricomlogo.png" class="logo" alt="Lessee Logo">
        </div>

        <!-- Header -->
        <div class="header">
            <!-- Licensor - Left Aligned -->
            <div class="logo-left">
                <img src="images/logo.png" class="logo" alt="Kenya Power Logo">
            </div>

            <!-- Title - Center -->
            <div class="title-section">
                <h1>Change Request Certificate</h1>
                <div class="subtitle">Dark Fiber Link</div>
                <div class="stamp">ISSUED BY KENYA POWER</div>
            </div>

            <!-- Empty right section to maintain layout -->
            <div class="logo-right"></div>
        </div>

        <!-- Certificate Info -->
        <div class="certificate-info">
            <p>This document certifies that the requested changes to the dark fiber link have been accepted and are now in effect between the parties mentioned below.</p>
        </div>

        <!-- Parties Info -->
        <div class="section">
            <div class="section-title">Parties Information</div>
            <table class="details-table">
                <tr><td>Lessor:</td><td>KENYA POWER & LIGHTING CO. PLC</td></tr>
                <tr><td>Lessee:</td><td>..........................................................................................................</td></tr>
                <tr><td>Name of the Route:</td><td>..................................................</td></tr>
                <tr><td>Name of the Link:</td><td>..................................................</td></tr>
                <tr><td>Nature of Change:</td><td>..................................................</td></tr>
            </table>
        </div>

        <!-- Declaration -->
        <div class="section">
            <div class="section-title">Acceptance Declaration</div>
            <p>The above link has been and fully accepted by the officers listed in the table below.</p>
            <p>We hereby certify that the above dark fiber link comprising the Purchased Capacity has been as requested.</p>
            <p>This Certificate is a confirmation of the same and the date indicated herein is the Effective Date.</p>
        </div>

        <!-- Certificate Date -->
        <div class="section">
            <div class="section-title">Certificate Date</div>
            <p>This Change Request Certificate of Acceptance is issued dated the <strong>.................</strong> day of <strong>...........................</strong></p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <table class="signature-blocks">
                <tr>
                    <td width="50%">
                        <strong>LESSEE:</strong>
                        <table width="100%" style="margin-top: 8px;">
                            <tr>
                                <td width="70%"><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ..................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">LEAD ENGINEER / TECHNICAL REP.</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td><strong>Stamp:</strong> .................................</td>
                            </tr>
                            <tr>
                                <td><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">MANAGER</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td><strong>Stamp:</strong> ................................</td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%">
                        <strong>LESSOR: THE KENYA POWER & LIGHTING CO. PLC</strong>
                        <table width="100%" style="margin-top: 8px;">
                            <tr>
                                <td width="70%"><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ..................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">TELECOM LEAD ENGINEER, Kenya Power</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td><strong>Stamp:</strong> ................................</td>
                            </tr>
                            <tr>
                                <td><strong>NAME:</strong> ................................................................</td>
                                <td><strong>Date:</strong> ................................</td>
                            </tr>
                            <tr>
                                <td colspan="2">TELECOM MANAGER, Kenya Power</td>
                            </tr>
                            <tr>
                                <td><strong>Signature:</strong> ................................................................</td>
                                <td><strong>Stamp:</strong> ................................</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated acceptance certificate. No physical signature is required.</p>
            <p>Generated on: October 15, 2025 at 16:57</p>
            <p>Certificate ID: ACC-CHG-001</p>
        </div>
    </div>

</body>
</html>
