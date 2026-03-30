<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quotation->designRequest->customer->name }} - Quotation {{ $quotation->quotation_number }}</title>
    <style>
        :root {
            --primary-color: #1a3a6c;
            --secondary-color: #2c5282;
            --light-gray: #f7fafc;
            --medium-gray: #e2e8f0;
            --dark-gray: #4a5568;
            --text-color: #2d3748;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            background-color: #fff;
            font-size: 12px;
            text-align: justify;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 8px; /* Reduced from 10px */
        }

        .header {
            text-align: center;
            margin-bottom: 8px; /* Reduced from 10px */
            padding-bottom: 6px; /* Reduced from 8px */
            border-bottom: 2px solid var(--primary-color);
        }

        .logo-container {
            width: 140px;
            height: 45px;
            margin: 0 auto 4px auto; /* Reduced from 5px */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px; /* Reduced from 20px */
        }

        .logo-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .company-info h2 {
            color: var(--primary-color);
            margin: 2px 0; /* Reduced from 4px */
            font-size: 16px;
        }

     .address-block {
    margin: 2px 0; /* Reduced from 3px */
    line-height: 1.3;
    font-size: 11px;
    text-align: center; /* Keep address centered */
}

     .address-block p {
    margin: 0; /* Removed 1px margin */
    padding: 0;
    text-align: center !important; /* Force center alignment */
}

        .reference {
            background-color: var(--light-gray);
            padding: 6px 8px; /* Reduced from 8px 10px */
            border-left: 4px solid var(--primary-color);
            margin: 6px 0; /* Reduced from 10px */
            line-height: 1.3;
            font-size: 11px;
            text-align: left;
        }

        .recipient {
            margin: 6px 0; /* Reduced from 8px */
            padding: 6px 8px; /* Reduced from 8px 10px */
            background-color: var(--light-gray);
            border-radius: 3px;
            line-height: 1.3;
        }

        .recipient p {
            margin: 0; /* Removed 1px margin */
            padding: 0;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .subject {
            margin: 6px 0; /* Reduced from 10px */
            padding: 8px 10px; /* Reduced from 10px 12px */
            background-color: var(--primary-color);
            color: white;
            border-radius: 3px;
            line-height: 1.3;
            text-align: justify;
        }

        .subject p {
            margin: 1px 0; /* Reduced from 2px */
        }

        .section {
            margin: 4px 0; /* Reduced from 8px */
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 2px; /* Reduced from 4px */
            color: var(--primary-color);
            font-size: 13px;
            border-bottom: 1px solid var(--medium-gray);
            padding-bottom: 2px; /* Reduced from 3px */
            text-align: left;
        }

        .subsection {
            margin: 3px 0; /* Reduced from 6px */
        }

        .subsection p {
            margin: 1px 0; /* Reduced from 3px */
            line-height: 1.3;
            text-align: justify;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0 3px 0; /* Reduced from 4px 0 6px 0 */
            font-size: 11px;
            line-height: 1.2;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 4px 4px; /* Reduced from 6px 5px */
            text-align: left;
            vertical-align: middle;
            font-size: 11px;
        }

        td {
            border: 1px solid var(--medium-gray);
            padding: 3px 3px; /* Reduced from 5px 4px */
            text-align: left;
            vertical-align: middle;
            font-size: 10.5px;
        }

        tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .text-end {
            text-align: right !important;
        }

        .text-justify {
            text-align: justify !important;
        }

        .content p {
            margin: 2px 0; /* Reduced from 5px */
            line-height: 1.3;
            text-align: justify;
        }

        .signature-block {
            margin-top: 6px; /* Reduced from 8px */
            padding-top: 4px; /* Reduced from 6px */
            border-top: 1px solid var(--medium-gray);
            line-height: 1.3;
            text-align: left;
        }

        .signature-block p {
            margin: 1px 0; /* Reduced from 3px */
        }

        .schedule {
            margin-top: 10px; /* Reduced from 15px */
            padding-top: 6px; /* Reduced from 8px */
            border-top: 2px solid var(--primary-color);
        }

        .schedule-header {
            text-align: center;
            margin-bottom: 8px; /* Reduced from 10px */
            padding-bottom: 6px; /* Reduced from 8px */
        }

        .schedule h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 4px; /* Reduced from 6px */
            font-size: 15px;
        }

        .schedule h3 {
            color: var(--secondary-color);
            text-align: center;
            margin: 2px 0 4px 0; /* Reduced from 3px 0 8px 0 */
            font-weight: 600;
            font-size: 13px;
        }

        .text-muted {
            color: var(--dark-gray);
        }

        .summary-table th, .summary-table td {
            padding: 3px 3px; /* Reduced from 5px 4px */
            font-size: 10.5px;
        }

        .total-row {
            font-weight: bold;
            background-color: rgba(26, 58, 108, 0.05);
        }

        .subtotal-row {
            border-top: 2px solid var(--medium-gray);
        }

        .total-row td {
            padding-top: 4px; /* Reduced from 6px */
            padding-bottom: 4px; /* Reduced from 6px */
        }

        .page-break {
            page-break-before: always;
            margin-top: 4px; /* Reduced from 15px */
        }

        /* Reduced gap class */
        .small-gap {
            margin-top: 1px !important;
            margin-bottom: 1px !important;
        }

        /* Remove top margin from first section after page break */
        .page-break + .section {
            margin-top: 0 !important;
        }

        @media print {
            body {
                padding: 0;
                font-size: 11px;
                text-align: justify;
            }

            .container {
                padding: 6px; /* Reduced from 8px */
                max-width: none;
            }

            .header {
                margin-bottom: 6px; /* Reduced from 8px */
                padding-bottom: 4px; /* Reduced from 6px */
            }

            .section {
                margin: 2px 0 !important; /* Reduced from 3px */
            }

            .signature-block {
                margin-top: 4px !important; /* Reduced from 6px */
                padding-top: 3px !important; /* Reduced from 4px */
            }

            .schedule {
                margin-top: 8px !important; /* Reduced from 12px */
                padding-top: 4px !important; /* Reduced from 6px */
            }

            table {
                font-size: 10px;
                margin: 1px 0 2px 0 !important; /* Reduced from 2px 0 3px 0 */
            }

            th, td {
                padding: 2px 2px !important; /* Reduced from 4px 3px */
            }

            .summary-table th, .summary-table td {
                padding: 2px 2px !important; /* Reduced from 4px 3px */
            }

            .page-break {
                page-break-before: always;
                margin-top: 2px !important; /* Reduced from 12px */
            }

            .small-gap {
                margin-top: 0.5px !important;
                margin-bottom: 0.5px !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page 1 -->
       <div class="header">
    <div class="logo">
        <div class="logo-container">
            @php
            $kplcLogoPath = public_path('images/logo.png');
            @endphp
            @if(file_exists($kplcLogoPath))
                <img src="file://{{ $kplcLogoPath }}" alt="KPLC Logo" style="height:60px;">
            @endif
        </div>
        <div class="company-info">
            <h2>The Kenya Power & Lighting Co. Ltd. </h2>
            <div class="address-block">
                <p><strong>Central Office</strong> -- P.O. Box 30099, Nairobi, Kenya</p>
                <p>Telephone -- 254-02-3201000 - Telegrams 'ELECTRIC'</p>
                <p>Fax No. 254-02-310336</p>
                <p>Stima Plaza, Kolobot Road, Parklands</p>
            </div>
        </div>
    </div>
</div>

        <div class="reference">
            <p><strong>Our Ref:</strong> {{ $quotation->quotation_number }} -
            @if($quotation->sent_at)
                {{ $quotation->sent_at->format('M d, Y') }}
            @else
                Draft
            @endif
            </p>
            <p><strong>Customer Ref:</strong> {{ $quotation->designRequest->request_number ?? 'N/A' }}</p>
        </div>

        <div class="recipient">
            <p><strong>{{ $quotation->designRequest->customer->name }}</strong></p>
            <p class="text-muted">{{ $quotation->designRequest->customer->email }}</p>
            <p class="mb-0 text-muted">{{ $quotation->designRequest->customer->phone ?? 'N/A' }}</p>
            @if($quotation->designRequest->customer->company)
                <p class="mb-0 text-muted">{{ $quotation->designRequest->customer->company }}</p>
            @endif
        </div>

        <div class="subject">
            <p><strong>RE: COMMERCIAL QUOTATION FOR TEMPORARY COLOCATION SERVICES AND FIBRE LEASE</strong></p>
            <p style="font-size: 11px; margin-top: 2px;"> <!-- Reduced from 3px -->
                Quotation Valid Until:
                @if($quotation->valid_until)
                    {{ $quotation->valid_until->format('F d, Y') }}
                @else
                    {{ now()->addDays(30)->format('F d, Y') }}
                @endif
            </p>
        </div>

        <div class="content">
            <p>Reference is made to your request dated {{ $quotation->created_at->format('jS F, Y') }} for colocation services and fibre lease services.</p>

            <!-- Service Details Summary Section -->
            <div class="section">
                <div class="section-title">i. Service Details Summary</div>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Service Category</th>
                            <th>Service Type</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Duration</th>
                            <th class="text-end">Unit Price (USD)</th>
                            <th class="text-end">Total (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $commercialRoutesTotal = 0;
                            $colocationServicesTotal = 0;
                            $customItemsTotal = 0;
                        @endphp

                        <!-- Commercial Routes -->
                        @if(isset($groupedItems['commercial_routes']) && count($groupedItems['commercial_routes']) > 0)
                            @foreach($groupedItems['commercial_routes'] as $route)
                                @php $commercialRoutesTotal += $route['total']; @endphp
                                <tr>
                                    <td>Fibre Lease</td>
                                    <td>
                                        <strong>{{ $route['metadata']['route_name'] ?? $route['description'] }}</strong>
                                        @if(isset($route['metadata']['technology_type']))
                                            <br><small class="text-muted">Tech: {{ $route['metadata']['technology_type'] }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $route['metadata']['cores'] ?? 1 }} cores</td>
                                    <td class="text-end">{{ $route['metadata']['duration_months'] ?? 12 }}m</td>
                                    <td class="text-end">${{ number_format($route['metadata']['monthly_cost'] ?? $route['unit_price'], 2) }}/m</td>
                                    <td class="text-end">${{ number_format($route['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- Colocation Services -->
                        @if(isset($groupedItems['colocation_services']) && count($groupedItems['colocation_services']) > 0)
                            @foreach($groupedItems['colocation_services'] as $service)
                                @php $colocationServicesTotal += $service['total']; @endphp
                                <tr>
                                    <td>Colocation</td>
                                    <td>
                                        <strong>{{ $service['metadata']['service_type'] ?? $service['description'] }}</strong>
                                        @if(isset($service['metadata']['space_sqm']))
                                            <br><small class="text-muted">{{ number_format($service['metadata']['space_sqm'], 2) }} m²</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $service['metadata']['quantity'] ?? 1 }}</td>
                                    <td class="text-end">{{ $service['metadata']['duration_months'] ?? 12 }}m</td>
                                    <td class="text-end">
                                        @if(isset($service['metadata']['monthly_rate']))
                                            ${{ number_format($service['metadata']['monthly_rate'], 2) }}/m
                                            @if(isset($service['metadata']['setup_fee']) && $service['metadata']['setup_fee'] > 0)
                                                <br><small class="text-muted">+ ${{ number_format($service['metadata']['setup_fee'], 2) }} setup</small>
                                            @endif
                                        @else
                                            ${{ number_format($service['unit_price'], 2) }}
                                        @endif
                                    </td>
                                    <td class="text-end">${{ number_format($service['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- Custom Items -->
                        @if(isset($groupedItems['custom_items']) && count($groupedItems['custom_items']) > 0)
                            @foreach($groupedItems['custom_items'] as $item)
                                @php $customItemsTotal += $item['total']; @endphp
                                <tr>
                                    <td>Other Services</td>
                                    <td>{{ $item['description'] }}</td>
                                    <td class="text-center">{{ $item['quantity'] }}</td>
                                    <td class="text-end">N/A</td>
                                    <td class="text-end">${{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="text-end">${{ number_format($item['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- Summary Rows -->
                        @if($commercialRoutesTotal > 0)
                            <tr class="subtotal-row">
                                <td colspan="5" class="text-end"><strong>Fibre Lease Subtotal:</strong></td>
                                <td class="text-end"><strong>${{ number_format($commercialRoutesTotal, 2) }}</strong></td>
                            </tr>
                        @endif

                        @if($colocationServicesTotal > 0)
                            <tr class="subtotal-row">
                                <td colspan="5" class="text-end"><strong>Colocation Subtotal:</strong></td>
                                <td class="text-end"><strong>${{ number_format($colocationServicesTotal, 2) }}</strong></td>
                            </tr>
                        @endif

                        @if($customItemsTotal > 0)
                            <tr class="subtotal-row">
                                <td colspan="5" class="text-end"><strong>Other Services Subtotal:</strong></td>
                                <td class="text-end"><strong>${{ number_format($customItemsTotal, 2) }}</strong></td>
                            </tr>
                        @endif

                        <tr class="total-row">
                            <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end"><strong>${{ number_format($quotation->subtotal, 2) }}</strong></td>
                        </tr>

                        <tr class="total-row">
                            <td colspan="5" class="text-end"><strong>Tax ({{ $quotation->tax_rate * 100 }}%):</strong></td>
                            <td class="text-end"><strong>${{ number_format($quotation->tax_amount, 2) }}</strong></td>
                        </tr>

                        <tr class="total-row" style="background-color: rgba(26, 58, 108, 0.1);">
                            <td colspan="5" class="text-end"><strong>TOTAL AMOUNT (USD):</strong></td>
                            <td class="text-end"><strong>${{ number_format($quotation->total_amount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Detailed Service Specifications -->
            <div class="section">
                <div class="section-title">ii. Detailed Service Specifications</div>

                <div class="subsection">
                    <p><strong>a. Fibre Lease Services:</strong></p>
                    @if(isset($groupedItems['commercial_routes']) && count($groupedItems['commercial_routes']) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Route Name</th>
                                    <th class="text-center">Cores</th>
                                    <th class="text-center">Technology</th>
                                    <th class="text-center">Distance</th>
                                    <th class="text-center">Pickup Points</th>
                                    <th class="text-center">Link Class</th>
                                    <th class="text-end">Monthly Rate/Core</th>
                                    <th class="text-end">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedItems['commercial_routes'] as $route)
                                    <tr>
                                        <td>{{ $route['metadata']['route_name'] ?? $route['description'] }}</td>
                                        <td class="text-center">{{ $route['metadata']['cores'] ?? 1 }}</td>
                                        <td class="text-center">{{ $route['metadata']['technology_type'] ?? 'OPGW' }}</td>
                                        <td class="text-center">{{ $route['metadata']['distance_km'] ?? 'N/A' }} km</td>
                                        <td class="text-center">{{ $route['metadata']['pickup_points'] ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $route['metadata']['link_class'] ?? 'Premium' }}</td>
                                        <td class="text-end">
                                            @php
                                                $unitPrice = $route['metadata']['monthly_cost'] ?? $route['unit_price'];
                                                $cores = $route['metadata']['cores'] ?? 1;
                                                $perCorePrice = $cores > 0 ? $unitPrice / $cores : $unitPrice;
                                            @endphp
                                            ${{ number_format($perCorePrice, 2) }}
                                        </td>
                                        <td class="text-end">${{ number_format($route['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No fibre lease services selected.</p>
                    @endif
                </div>

                <div class="subsection">
                    <p><strong>b. Colocation Services:</strong></p>
                    @if(isset($groupedItems['colocation_services']) && count($groupedItems['colocation_services']) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Service Description</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Space (m²)</th>
                                    <th class="text-center">Power</th>
                                    <th class="text-center">Contract Period</th>
                                    <th class="text-end">Monthly Rate</th>
                                    <th class="text-end">Setup Fee</th>
                                    <th class="text-end">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedItems['colocation_services'] as $service)
                                    <tr>
                                        <td>{{ $service['metadata']['service_type'] ?? $service['description'] }}</td>
                                        <td class="text-center">{{ $service['metadata']['quantity'] ?? 1 }}</td>
                                        <td class="text-center">
                                            @if(isset($service['metadata']['space_sqm']))
                                                {{ number_format($service['metadata']['space_sqm'], 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(isset($service['metadata']['power_kw']))
                                                {{ number_format($service['metadata']['power_kw'], 2) }} kW
                                            @elseif(isset($service['metadata']['power_amps']))
                                                {{ number_format($service['metadata']['power_amps'], 2) }} A
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $service['metadata']['duration_months'] ?? 12 }}m</td>
                                        <td class="text-end">
                                            @if(isset($service['metadata']['monthly_rate']))
                                                ${{ number_format($service['metadata']['monthly_rate'], 2) }}/m
                                            @else
                                                ${{ number_format($service['unit_price'], 2) }}/m
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if(isset($service['metadata']['setup_fee']) && $service['metadata']['setup_fee'] > 0)
                                                ${{ number_format($service['metadata']['setup_fee'], 2) }}
                                            @else
                                                $0.00
                                            @endif
                                        </td>
                                        <td class="text-end">${{ number_format($service['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No colocation services selected.</p>
                    @endif
                </div>

                <div class="subsection">
                    <p><strong>c. Electricity Connectivity:</strong></p>
                    <p>Kenya Power will provide Alternating Current (AC) electricity. <strong>{{ $quotation->designRequest->customer->name }}</strong> will be required to fill a supply form for electricity connection and pay a refundable account deposit at Kenya Power office. Billing for monthly electricity consumption will be done at the prevailing market rates.</p>
                </div>
            </div>

            <div class="section">
                <div class="section-title">iii. Delivery Periods</div>
                <p>The above-mentioned services are ready for provisioning as confirmed by site surveys and technical evaluations.</p>
            </div>

            <!-- Page break for Page 2 -->
            <div class="page-break"></div>

            <!-- Page 2 -->
            <div class="section">
    <div class="section-title">iv. Terms and Conditions</div>
    <div class="text-left" style="white-space: pre-line; font-size: 11px; line-height: 1.3; margin-top: 1px;">
        @php
            $terms = $quotation->terms_and_conditions;
            // Remove heading variations
            $terms = preg_replace('/^[ivx0-9\.\s\-]*TERMS\s+AND\s+CONDITIONS[:\s]*/i', '', $terms);
            // Trim any remaining whitespace
            $terms = trim($terms);
        @endphp
        {{ $terms }}
    </div>
</div>

<div class="section">
    <div class="section-title">v. Scope of Work</div>
    <div class="text-left" style="white-space: pre-line; font-size: 11px; line-height: 1.3; margin-top: 1px;">
        @php
            $scope = $quotation->scope_of_work;
            // Remove heading variations for Scope of Work
            $scope = preg_replace('/^[vx0-9\.\s\-]*SCOPE\s+OF\s+WORK[:\s]*/i', '', $scope);
            $scope = trim($scope);
        @endphp
        {{ $scope }}
    </div>
</div>

@if($quotation->customer_notes)
<div class="section">
    <div class="section-title">vi. Additional Notes</div>
    <div class="text-left" style="white-space: pre-line; font-size: 11px; line-height: 1.3; margin-top: 1px;">
        @php
            $notes = $quotation->customer_notes;
            // Remove heading variations for Additional Notes
            $notes = preg_replace('/^[vix0-9\.\s\-]*ADDITIONAL\s+NOTES[:\s]*/i', '', $notes);
            $notes = trim($notes);
        @endphp
        {{ $notes }}
    </div>
</div>
@endif

            <p>If you accept this offer, please complete the attached IRU/Lease Order Form and return it to the undersigned office.</p>

            <div class="signature-block">
                <p>Yours faithfully</p>
                <p><strong>For: THE KENYA POWER & LIGHTING CO. LTD</strong></p>
                <p style="margin-top: 10px;">_________________________________</p> <!-- Reduced from 15px -->
                <p><strong>GENERAL MANAGER, ICT</strong></p>
            </div>

            <!-- Page break for Schedule (Page 3) -->
            <div class="page-break"></div>

            <!-- Page 3 - Schedule (with logo) -->
            <div class="schedule">
                <!-- Logo on Schedule page -->
                <!-- Logo on Schedule page -->
<div class="schedule-header">
    <div class="logo">
        <div class="logo-container">
            @php
            $kplcLogoPath = public_path('images/logo.png');
            @endphp
            @if(file_exists($kplcLogoPath))
                <img src="file://{{ $kplcLogoPath }}" alt="KPLC Logo" style="height:60px;">
            @endif
        </div>
    </div>

    <div class="company-info">
        <h2>The Kenya Power & Lighting Co. Ltd.</h2>
        <div class="address-block">
            <p><strong>Central Office</strong> -- P.O. Box 30099, Nairobi, Kenya</p>
        </div>
    </div>
</div>

                <h2>THE SECOND SCHEDULE</h2>
                <h3>IRU/LEASE ORDER FORM - GRANT/LEASE OF DARK FIBRE</h3>

                <div class="section">
                    <p><strong>1. Purchaser/Lessee and The Grantor/Lessor</strong></p>
                    <p class="text-justify" style="font-size: 11px; line-height: 1.3; margin-top: 1px;">
                        We <strong>{{ $quotation->designRequest->customer->name }}</strong>,
                        @if($quotation->designRequest->customer->company)
                            a company incorporated under the laws of Kenya and having its principal office at
                            @if(isset($customerProfile) && $customerProfile->physical_address)
                                {{ $customerProfile->physical_address }}
                            @else
                                ..............................................................................
                            @endif
                            Street .................... City/Town in Kenya and of P.O Box
                            @if(isset($customerProfile) && $customerProfile->address)
                                {{ $customerProfile->address }}
                            @else
                                ......................
                            @endif
                            @if(isset($customerProfile) && $customerProfile->postal_code)
                                {{ $customerProfile->postal_code }}
                            @endif
                        @else
                            ..............................................................................
                        @endif
                        hereby request for a Grant IRU/Lease of Dark fibre from THE KENYA POWER & LIGHTING COMPANY LIMITED a limited liability company duly incorporated under the Companies Act, Chapter 486 of the Laws of Kenya, with its registered office situated at Stima Plaza, Kolobot Road, Parklands, Nairobi in the Republic of Kenya and of Post Office Box Number 30099-00100, Nairobi in the Republic aforesaid.
                    </p>
                </div>

                <div class="section">
                    <div class="section-title">2. Details of Service</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Service Type</th>
                                <th>Technology Type</th>
                                <th>Optic Distance</th>
                                <th>Pick up Points</th>
                                <th>Lease Period</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($groupedItems['commercial_routes']) && count($groupedItems['commercial_routes']) > 0)
                                @foreach($groupedItems['commercial_routes'] as $route)
                                    <tr>
                                        <td>Optical Grounding Wire (OPGW)</td>
                                        <td>{{ $route['metadata']['technology_type'] ?? 'OPGW' }}</td>
                                        <td>{{ $route['metadata']['distance_km'] ?? 'N/A' }} km</td>
                                        <td>{{ $route['metadata']['pickup_points'] ?? 'N/A' }}</td>
                                        <td>{{ $route['metadata']['duration_months'] ?? 12 }} months</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No fibre lease services</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">3. Charges for Temporary Colocation Space</div>
                    @if(isset($groupedItems['colocation_services']) && count($groupedItems['colocation_services']) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Service Description</th>
                                    <th class="text-end">One-off Amount (USD)</th>
                                    <th class="text-end">Recurrent Amount (USD) per annum</th>
                                    <th class="text-end">Grand Total USD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedItems['colocation_services'] as $service)
                                    <tr>
                                        <td>{{ $service['metadata']['service_type'] ?? $service['description'] }}</td>
                                        <td class="text-end">
                                            @if(isset($service['metadata']['setup_fee']) && $service['metadata']['setup_fee'] > 0)
                                                ${{ number_format($service['metadata']['setup_fee'], 2) }}
                                            @else
                                                $0.00
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $monthlyRate = $service['metadata']['monthly_rate'] ?? $service['unit_price'];
                                                $annualRecurrent = $monthlyRate * 12;
                                            @endphp
                                            ${{ number_format($annualRecurrent, 2) }}
                                        </td>
                                        <td class="text-end">${{ number_format($service['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="3" class="text-end"><strong>Total Colocation Charges:</strong></td>
                                    <td class="text-end"><strong>${{ number_format($colocationServicesTotal, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No colocation services selected.</p>
                    @endif
                </div>

                <div class="section">
                    <div class="section-title">4. Assignment of Cores</div>
                    @if(isset($groupedItems['commercial_routes']) && count($groupedItems['commercial_routes']) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Item No.</th>
                                    <th>Cable Type (OPGW/ADSS)</th>
                                    <th>Service Type</th>
                                    <th>No. of Cores</th>
                                    <th>Route Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedItems['commercial_routes'] as $index => $route)
                                    <tr>
                                        <td>{{ $index + 1 }}.</td>
                                        <td>{{ $route['metadata']['technology_type'] ?? 'OPGW' }}</td>
                                        <td>Premium</td>
                                        <td>{{ $route['metadata']['cores'] ?? 1 }}</td>
                                        <td>{{ $route['metadata']['route_name'] ?? $route['description'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <p class="text-justify" style="margin-top: 2px;">Indicate tentative take up date: {{ $quotation->valid_until->format('F d, Y') }}</p>
                    <p class="text-justify" style="margin-top: 1px;"><em>Note: Acceptance certificate to be signed by both parties after the necessary tests of the cable</em></p>
                </div>

                <div class="section">
                    <div class="section-title">5. Summary of Charges</div>
                    <table>
                        <tbody>
                            <tr>
                                <td><strong>5.1. Total Fibre Lease Contract Value:</strong></td>
                                <td class="text-end"><strong>${{ number_format($commercialRoutesTotal, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>5.2. Total Colocation Contract Value:</strong></td>
                                <td class="text-end"><strong>${{ number_format($colocationServicesTotal, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>5.3. Subtotal:</strong></td>
                                <td class="text-end"><strong>${{ number_format($quotation->subtotal, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>5.4. Tax ({{ $quotation->tax_rate * 100 }}%):</strong></td>
                                <td class="text-end"><strong>${{ number_format($quotation->tax_amount, 2) }}</strong></td>
                            </tr>
                            <tr class="total-row">
                                <td><strong>5.5. Grand Total Contract Value:</strong></td>
                                <td class="text-end"><strong>${{ number_format($quotation->total_amount, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-justify" style="margin-top: 2px;"><em>Note: All amounts are in United States Dollars (USD)</em></p>
                </div>

                <div class="signature-block">
                    <p>Signed ................................................................</p>
                    <p>Date ...................................................................</p>
                    <p>Name of Signatory ................................................................</p>
                    <p>Designation ...................................................................</p>
                    <p>Stamp ...................................................................</p>
                    <p><strong>{{ $quotation->designRequest->customer->name }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
