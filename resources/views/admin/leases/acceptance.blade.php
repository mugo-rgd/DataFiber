<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceptance Certificate - Lease #{{ $lease->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .certificate-container {
            max-width: 800px;
            margin: 0 auto;
            border: 3px solid #2c5aa0;
            padding: 30px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c5aa0;
            margin: 0;
            font-size: 28px;
        }
        .header h2 {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: normal;
        }
        .content-section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 8px 15px;
            font-weight: bold;
            border-left: 4px solid #2c5aa0;
            margin-bottom: 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .details-table td:first-child {
            font-weight: bold;
            width: 35%;
            color: #555;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 300px;
            margin-top: 60px;
        }
        .signature-label {
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Logo Section -->
        <div class="logo">
            <h1 style="color: #2c5aa0; margin: 0;">DarkFibre CRM</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Telecommunications Services</p>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>LEASE ACCEPTANCE CERTIFICATE</h1>
            <h2>Certificate No: ACC-{{ str_pad($lease->id, 6, '0', STR_PAD_LEFT) }}</h2>
        </div>

        <!-- Introduction -->
        <div class="content-section">
            <p>This document certifies that the telecommunications lease agreement has been successfully implemented, tested, and accepted by both parties.</p>
        </div>

        <!-- Lease Details -->
        <div class="section-title">LEASE INFORMATION</div>
        <table class="details-table">
            <tr>
                <td>Lease Number:</td>
                <td>{{ $lease->lease_number }}</td>
            </tr>
            <tr>
                <td>Lease Title:</td>
                <td>{{ $lease->title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Service Type:</td>
                <td>{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</td>
            </tr>
            <tr>
                <td>Bandwidth/Service Level:</td>
                <td>{{ $lease->bandwidth ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Technology:</td>
                <td>{{ $lease->technology ? ucfirst(str_replace('_', ' ', $lease->technology)) : 'N/A' }}</td>
            </tr>
        </table>

        <!-- Route Information -->
        <div class="section-title">ROUTE INFORMATION</div>
        <table class="details-table">
            <tr>
                <td>Start Location:</td>
                <td>{{ $lease->start_location }}</td>
            </tr>
            <tr>
                <td>End Location:</td>
                <td>{{ $lease->end_location }}</td>
            </tr>
            <tr>
                <td>Distance:</td>
                <td>{{ $lease->distance_km ? $lease->distance_km . ' km' : 'N/A' }}</td>
            </tr>
        </table>

        <!-- Customer Information -->
        <div class="section-title">CUSTOMER INFORMATION</div>
        <table class="details-table">
            <tr>
                <td>Customer Name:</td>
                <td>{{ $customerName }}</td>
            </tr>
            <tr>
                <td>Company:</td>
                <td>{{ $customerCompany ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- Service Details -->
        <div class="section-title">SERVICE DETAILS</div>
        <table class="details-table">
            <tr>
                <td>Contract Term:</td>
                <td>{{ $lease->contract_term_months }} months</td>
            </tr>
            <tr>
                <td>Start Date:</td>
                <td>{{ $lease->start_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>End Date:</td>
                <td>{{ $lease->end_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>Monthly Cost:</td>
                <td>{{ $lease->currency }} {{ number_format($lease->monthly_cost, 2) }}</td>
            </tr>
            <tr>
                <td>Billing Cycle:</td>
                <td>{{ ucfirst($lease->billing_cycle) }}</td>
            </tr>
        </table>

        <!-- Testing & Acceptance -->
        <div class="section-title">TESTING & ACCEPTANCE</div>
        <div class="content-section">
            <p>The service has been tested and verified to meet the following specifications:</p>

            @if($lease->test_report_path)
            <p><strong>Test Report:</strong> Available (Reference: {{ basename($lease->test_report_path) }})</p>
            @endif

            @if($lease->test_report_type)
            <p><strong>Test Type:</strong> {{ ucfirst(str_replace('_', ' ', $lease->test_report_type)) }}</p>
            @endif

            @if($lease->test_date)
            <p><strong>Test Date:</strong> {{ $lease->test_date->format('F d, Y') }}</p>
            @endif

            <p><strong>Service Status:</strong> <span style="color: #28a745;">✓ ACCEPTED AND OPERATIONAL</span></p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <div class="signature-line"></div>
                        <div class="signature-label">Customer Representative</div>
                        <div class="signature-label">Name: ___________________</div>
                        <div class="signature-label">Date: ___________________</div>
                    </td>
                    <td style="width: 50%;">
                        <div class="signature-line"></div>
                        <div class="signature-label">Service Provider Representative</div>
                        <div class="signature-label">Name: ___________________</div>
                        <div class="signature-label">Date: ___________________</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This acceptance certificate is generated electronically and is valid without signature.</p>
            <p>Generated on: {{ now()->format('F d, Y \a\t H:i') }}</p>
            <p>Certificate ID: {{ md5($lease->id . $lease->lease_number . $lease->created_at) }}</p>
        </div>
    </div>
</body>
</html>
