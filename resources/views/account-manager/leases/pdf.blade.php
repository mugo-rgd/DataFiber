<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lease Agreement - {{ $lease->lease_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        @page { margin: 8px 6px; size: A4; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px; /* Increased from 9px */
            line-height: 1.35; /* Adjusted for better readability */
            color: #2c3e50;
            background: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Compact watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px; /* Increased from 45px */
            color: rgba(44, 62, 80, 0.02);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }

        /* Compact layout */
        .section {
            margin-bottom: 7px; /* Slightly increased */
            page-break-inside: avoid;
        }

        .section-title {
            background: #007bff;
            color: white;
            padding: 5px 8px; /* Increased padding */
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 6px;
            font-size: 11px; /* Increased from 10px */
        }

        /* Two column layout */
        .two-column {
            display: flex;
            gap: 5px; /* Increased from 4px */
            margin-bottom: 5px; /* Increased from 4px */
        }

        .column {
            flex: 1;
            min-width: 0;
        }

        /* Compact tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0; /* Increased from 4px */
            font-size: 9px; /* Increased from 8px */
        }

        .data-table th {
            background: #007bff;
            color: white;
            text-align: left;
            padding: 4px 6px; /* Increased from 3px 4px */
            border: 1px solid #0056b3;
            font-weight: bold;
            font-size: 9px; /* Added for consistency */
        }

        .data-table td {
            padding: 3px 6px; /* Increased from 2px 4px */
            border: 1px solid #dee2e6;
        }

        .data-table tr:nth-child(even) td {
            background: #f8f9fa;
        }

        /* Compact info cards */
        .info-card {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 6px; /* Increased from 5px */
            margin-bottom: 5px; /* Increased from 4px */
            background: #ffffff;
        }

        .card-title {
            font-weight: bold;
            margin-bottom: 4px; /* Increased from 3px */
            font-size: 10px; /* Increased from 9px */
            color: #2c3e50;
        }

        /* Compact financial box */
        .financial-box {
            background: #f8fff8;
            border: 1px solid #28a745;
            border-radius: 4px;
            padding: 7px; /* Increased from 6px */
            margin: 6px 0; /* Increased from 5px */
        }

        .financial-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px; /* Increased from 2px */
            font-size: 9px; /* Increased from 8px */
        }

        .financial-total {
            font-weight: bold;
            border-top: 1px solid #28a745;
            margin-top: 5px; /* Increased from 4px */
            padding-top: 5px; /* Increased from 4px */
            font-size: 10px; /* Increased from 9px */
        }

        /* Compact signatures */
        .signature-section {
            margin-top: 12px; /* Increased from 10px */
            padding: 9px; /* Increased from 8px */
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px dashed #6c757d;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin: 12px -8px 4px -8px; /* Adjusted */
            width: calc(100% + 16px);
            position: relative;
            left: -8px;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 2px 5px; /* Increased padding */
            border-radius: 6px;
            font-size: 8px; /* Increased from 7px */
            font-weight: bold;
        }

        /* Contact info */
        .contact-info {
            background: #2c3e50;
            color: white;
            padding: 6px; /* Increased from 5px */
            border-radius: 4px;
            margin: 7px 0; /* Increased from 6px */
            text-align: center;
            font-size: 9px; /* Increased from 8px */
        }

        /* Footer */
        .footer {
            margin-top: 10px; /* Increased from 8px */
            padding-top: 6px; /* Increased from 5px */
            border-top: 1px solid #2c3e50;
            text-align: center;
            font-size: 8px; /* Increased from 7px */
            color: #6c757d;
        }

        /* Value highlights */
        .value-highlight {
            background: #fff3cd;
            padding: 3px 5px; /* Increased from 2px 4px */
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px; /* Increased from 8px */
            display: inline-block;
        }

        /* Badge styles */
        .badge {
            display: inline-block;
            padding: 2px 4px; /* Increased from 1px 3px */
            border-radius: 5px;
            font-size: 7px; /* Kept same */
            font-weight: bold;
            margin-left: 2px;
        }

        .badge-primary { background: #007bff; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #212529; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }

        /* Progress bar */
        .progress-container {
            background: #e9ecef;
            border-radius: 3px;
            height: 4px; /* Increased from 3px */
            margin: 3px 0; /* Increased from 2px */
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: #28a745;
            border-radius: 3px;
        }

        /* Utility */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .compact-text {
            font-size: 9px; /* Increased from 8px */
            line-height: 1.3; /* Adjusted */
        }
    </style>
</head>
<body>
    <div class="watermark">LEASE</div>

    <!-- Logo Container -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px; border-bottom: 2px solid #2c3e50;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: middle; padding: 4px;">
                <img src="{{ public_path('images/logo.png') }}" alt="KPLC Logo" style="max-height: 60px; max-width: 120px; height: auto;"> <!-- Slightly reduced -->
            </td>
            <td style="width: 50%; text-align: center; vertical-align: middle; padding: 4px;">
                @if($lease->customer->companyProfile->profile_photo ?? false)
                <img src="{{ storage_path('app/public/' . $lease->customer->companyProfile->profile_photo) }}" alt="Customer Logo" style="max-height: 60px; max-width: 120px; height: auto;"> <!-- Slightly reduced -->
                @endif
            </td>
        </tr>
    </table>

    <!-- Company Header -->
    <div style="text-align: center; margin-bottom: 5px;">
        <div style="font-size: 12px; font-weight: bold; color: #2c3e50;">Kenya Power and Lighting Pty</div> <!-- Increased from 11px -->
        <div style="font-size: 9px; color: #6c757d; margin: 2px 0;">Professional Network Infrastructure Services</div> <!-- Increased from 8px -->
    </div>

    <!-- Document Info -->
    <div style="background: #007bff; color: white; padding: 5px; border-radius: 3px; font-size: 9px; margin-bottom: 7px; display: flex; justify-content: space-between;"> <!-- Increased sizes -->
        <div><strong>Document:</strong> {{ $lease->lease_number }}</div>
        <div><strong>Date:</strong> {{ now()->format('M d, Y') }}</div>
        <div><strong>Status:</strong>
            <span class="status-badge" style="background:
                @if($lease->status == 'active') #28a745
                @elseif($lease->status == 'pending') #ffc107
                @elseif($lease->status == 'draft') #6c757d
                @else #dc3545 @endif;
                color: white;">
                {{ ucfirst($lease->status) }}
            </span>
        </div>
    </div>

    <!-- Main Title -->
    <div style="text-align: center; background: #2c3e50; color: white; padding: 7px; margin: 7px 0; border-radius: 4px;"> <!-- Increased padding -->
        <div style="font-size: 12px; font-weight: bold;">FIBRE OPTIC LEASE AGREEMENT</div> <!-- Increased from 10px -->
        <div style="font-size: 9px; opacity: 0.9;">Professional Dark Fibre Services Contract</div> <!-- Increased from 8px -->
    </div>

    <!-- Contracting Parties -->
    <div class="section">
        <div class="section-title">CONTRACTING PARTIES</div>
        <div class="two-column">
            <div class="column">
                <div class="info-card">
                    <div class="card-title">LESSEE (Service Provider)</div>
                    <div class="compact-text">
                        <strong>Company:</strong> Kenya Power and Lighting Pty<br>
                        <strong>Address:</strong> 30099 Nairobi Kolobot road<br>
                        <strong>Contact:</strong> +254 720 000<br>
                        <strong>Email:</strong> contracts@kpl.co.ke
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="info-card">
                    <div class="card-title">LESSOR (Customer)</div>
                    <div class="compact-text">
                        <strong>Name:</strong> {{ $lease->customer->name }}<br>
                        <strong>Email:</strong> {{ $lease->customer->email }}<br>
                        <strong>Phone:</strong> {{ $lease->customer->phone ?? 'N/A' }}<br>
                        <strong>Company:</strong> {{ $lease->customer->company ?? 'Individual' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Details -->
    <div class="section">
        <div class="two-column">
            <div class="column">
                <div class="section-title">SERVICE OVERVIEW</div>
                <table class="data-table">
                    <tr><td style="width: 40%; font-weight: bold;">Service Type:</td><td>{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</td></tr>
                    <tr><td style="font-weight: bold;">Bandwidth:</td><td>{{ $lease->bandwidth ?? 'Custom' }}</td></tr>
                    <tr><td style="font-weight: bold;">Technology:</td><td>{{ $lease->technology ?? 'Standard Fibre' }}</td></tr>
                    <tr><td style="font-weight: bold;">Route:</td><td>{{ $lease->start_location }} → {{ $lease->end_location }}</td></tr>
                    <tr><td style="font-weight: bold;">Distance:</td><td>{{ $lease->distance_km ? number_format($lease->distance_km) . ' km' : 'TBD' }}</td></tr>
                </table>
            </div>
            <div class="column">
                <div class="section-title">CONTRACT TIMELINE</div>
                <table class="data-table">
                    <tr><td style="width: 40%; font-weight: bold;">Start Date:</td><td><span class="value-highlight">{{ $lease->start_date->format('M d, Y') }}</span></td></tr>
                    <tr><td style="font-weight: bold;">End Date:</td><td><span class="value-highlight">{{ $lease->end_date->format('M d, Y') }}</span></td></tr>
                    <tr><td style="font-weight: bold;">Contract Term:</td><td>{{ $lease->contract_term_months }} months</td></tr>
                    <tr><td style="font-weight: bold;">Remaining:</td><td>
                        @if($lease->isExpired()) Expired @else {{ $lease->daysUntilExpiry() }} days @endif
                    </td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Financial Information -->
    <div class="section">
        <div class="section-title">FINANCIAL SUMMARY</div>
        <div class="financial-box">
            <div class="financial-row">
                <span>Monthly Service Fee:</span>
                <span><strong>${{ number_format($lease->monthly_cost, 2) }} {{ strtoupper($lease->currency) }}</strong></span>
            </div>
            <div class="financial-row">
                <span>Installation Fee:</span>
                <span>${{ number_format($lease->installation_fee, 2) }} {{ strtoupper($lease->currency) }}</span>
            </div>
            <div class="financial-row">
                <span>Billing Cycle:</span>
                <span>{{ ucfirst($lease->billing_cycle) }}</span>
            </div>
            <div class="financial-row financial-total">
                <span>Total Contract Value:</span>
                <span>${{ number_format($lease->total_contract_value, 2) }} {{ strtoupper($lease->currency) }}</span>
            </div>
        </div>
    </div>

    <!-- Payment Schedule -->
    <div class="section">
        <div class="section-title">PAYMENT SCHEDULE</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Period</th>
                    <th style="width: 40%;">Description</th>
                    <th style="width: 20%;">Amount</th>
                    <th style="width: 25%;">Due Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Initial</td>
                    <td>Installation Fee</td>
                    <td>${{ number_format($lease->installation_fee, 2) }}</td>
                    <td>Upon signing</td>
                </tr>
                <tr>
                    <td>Monthly</td>
                    <td>Service Fee</td>
                    <td>${{ number_format($lease->monthly_cost, 2) }}</td>
                    <td>1st of each month</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Signatures -->
    <!-- Signatures - Compact Version -->
<!-- Signatures -->
<div class="signature-section">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                <div class="info-card" style="margin-bottom: 0;">
                    <div class="card-title">FOR KENYA POWER AND LIGHTING</div>

                    <!-- Signature area with space for appended signature -->
                    <div style="margin: 15px 0; text-align: center;">
                        <div style="height: 50px; border: 1px dashed #007bff; border-radius: 4px; margin-bottom: 8px;
                                 display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                            <div style="font-size: 8px; color: #6c757d; font-style: italic;">
                                [Signature Area]
                            </div>
                        </div>
                        <div style="font-size: 7px; color: #6c757d; margin-bottom: 5px;">
                            <strong>Digital/Physical Signature</strong>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #333; margin: 10px 0; padding-top: 8px; font-size: 9px; line-height: 1.8;">
                        <div><strong>Authorized Representative</strong></div><br>
                        <div>Name:  ______________________________</div>
                        <div>Title: ______________________________</div>
                        <div>Date:  ______________________________</div>
                    </div>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                <div class="info-card" style="margin-bottom: 0;">
                    <div class="card-title">FOR CUSTOMER</div>

                    <!-- Signature area with space for appended signature -->
                    <div style="margin: 15px 0; text-align: center;">
                        <div style="height: 50px; border: 1px dashed #28a745; border-radius: 4px; margin-bottom: 8px;
                                 display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                            <div style="font-size: 8px; color: #6c757d; font-style: italic;">
                                [Signature Area]
                            </div>
                        </div>
                        <div style="font-size: 7px; color: #6c757d; margin-bottom: 5px;">
                            <strong>Digital/Physical Signature</strong>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #333; margin: 10px 0; padding-top: 8px; font-size: 9px; line-height: 1.8;">
                        <div><strong>Authorized Representative</strong></div><br>
                        <div>Name:  ______________________________</div>
                        <div>Title: ______________________________</div>
                        <div>Date:  ______________________________</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

    <!-- Contact Information -->
    <div class="contact-info">
        <div style="font-size: 10px; font-weight: bold; margin-bottom: 3px;">Need Assistance?</div> <!-- Increased -->
        <div style="font-size: 9px;"> <!-- Increased -->
            Support: +254 720 000000 | Email: support@kpl.co.ke
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="font-weight: bold; margin-bottom: 3px; font-size: 9px;">Kenya Power and Lighting Pty</div> <!-- Increased -->
        <div style="color: #007bff; margin-bottom: 3px; font-size: 8px;">
            Generated on {{ now()->format('M d, Y \a\t h:i A') }} | Document ID: {{ $lease->id }}
        </div>
        <div style="font-size: 7px; color: #868e96;"> <!-- Increased from 6px -->
            Confidential document - Unauthorized distribution prohibited
        </div>
    </div>
</body>
</html>
