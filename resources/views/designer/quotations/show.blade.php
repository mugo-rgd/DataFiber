<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quotation->designRequest->customer->name }} - Naivasha 132kV SS Outdoor Shelter</title>
    <style>
        :root {
            --primary-color: #1a3a6c;
            --secondary-color: #2c5282;
            --accent-color: #e53e3e;
            --light-gray: #f7fafc;
            --medium-gray: #e2e8f0;
            --dark-gray: #4a5568;
            --text-color: #2d3748;
            --border-color: #cbd5e0;
        }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: var(--text-color);
            background-color: #fff;
            font-size: 14px;
            text-align: justify;
            text-justify: inter-word;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            background: white;
            border-radius: 4px;
        }

        /* Logo styling - Centered */
        .logo {
            text-align: center;
            margin: 0 auto 25px;
            padding: 10px 0;
        }

        .logo-container {
            width: 180px;
            height: 70px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .company-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-info h2 {
            color: var(--primary-color);
            margin: 10px 0 5px;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .address-block {
            margin: 8px 0;
            line-height: 1.2;
            font-size: 13px;
            color: var(--dark-gray);
        }

        .address-block p {
            margin: 3px 0;
            padding: 0;
        }

        .reference {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 14px 18px;
            border-left: 4px solid var(--primary-color);
            margin: 25px 0;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .reference p {
            margin: 0;
            font-weight: 500;
        }

        .recipient {
            margin: 20px 0;
            padding: 16px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 6px;
            border: 1px solid var(--medium-gray);
        }

        .recipient p {
            margin: 4px 0;
            padding: 0;
        }

        .mb-1 {
            margin-bottom: 4px !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .subject {
            margin: 25px 0;
            padding: 16px 20px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 6px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }

        .subject p {
            margin: 0;
            font-weight: 600;
            font-size: 15px;
        }

        .section {
            margin: 35px 0;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 18px;
            color: var(--primary-color);
            font-size: 17px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
            position: relative;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: var(--accent-color);
        }

        .subsection {
            margin: 25px 0 30px;
        }

        .subsection p {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 25px;
            font-size: 13px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            overflow: hidden;
        }

        th {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            font-weight: 600;
            padding: 14px 10px;
            text-align: left;
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            border: 1px solid var(--medium-gray);
            padding: 12px 10px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        tr:hover {
            background-color: rgba(26, 58, 108, 0.05);
        }

        tfoot tr {
            background-color: #f8f9fa !important;
        }

        tfoot tr.table-active {
            background-color: rgba(26, 58, 108, 0.15) !important;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-muted {
            color: var(--dark-gray);
            opacity: 0.8;
        }

        .signature-block {
            margin-top: 60px;
            padding-top: 25px;
            border-top: 2px solid var(--medium-gray);
        }

        .schedule {
            margin-top: 60px;
            padding-top: 35px;
            border-top: 3px solid var(--primary-color);
        }

        .schedule h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: 700;
        }

        .schedule h3 {
            color: var(--secondary-color);
            text-align: center;
            margin: 10px 0 25px;
            font-weight: 600;
            font-size: 18px;
        }

        ol {
            padding-left: 22px;
            margin: 15px 0;
        }

        li {
            margin-bottom: 10px;
            padding-left: 5px;
        }

        .bg-light {
            background-color: var(--light-gray);
        }

        .page-break {
            page-break-before: always;
        }

        /* Page break logo styling */
        .page-break-logo {
            text-align: center;
            margin: 40px auto 30px;
        }

        /* Content styling for justification */
        .content {
            text-align: justify;
            text-justify: inter-word;
        }

        .content p {
            margin-bottom: 15px;
            line-height: 1.7;
        }

        /* Print specific styles */
        @media print {
            body {
                padding: 0;
                text-align: justify;
            }

            .container {
                box-shadow: none;
                border: none;
                padding: 15px;
                margin: 0;
                border-radius: 0;
            }

            .page-break {
                page-break-before: always;
            }

            .subject {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            th {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .recipient, .reference {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }

        /* Utility classes */
        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-30 {
            margin-top: 30px;
        }

        .pt-20 {
            padding-top: 20px;
        }

        .font-bold {
            font-weight: 700;
        }

        .border-bottom {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        /* Highlight important numbers */
        .highlight-amount {
            background-color: #fff3cd;
            padding: 3px 8px;
            border-radius: 3px;
            border-left: 3px solid #ffc107;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Centered Logo -->
        <div class="logo">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="logo-img">
            </div>
        </div>

        <div class="company-info">
            <h2>The Kenya Power & Lighting Co. Ltd.</h2>
            <div class="address-block">
                <p><strong>Central Office</strong> -- P.O. Box 30099, Nairobi, Kenya</p>
                <p>Telephone -- 254-02-3201000-Telegrams 'ELECTRIC'</p>
                <p>Fax No. 254-02-310336</p>
                <p>Stima Plaza, Kolobot Road</p>
            </div>
        </div>

        <div class="reference">
            <p><strong>Our Ref:</strong> {{ $quotation->designRequest->request_number }} -
            @if($quotation->sent_at)
                {{ $quotation->sent_at->format('M d, Y') }}
            @else
                Draft
            @endif
            </p>
        </div>

        <div class="recipient">
            <p class="mb-1 font-bold">{{ $quotation->designRequest->customer->name }}</p>
            <p class="mb-1 text-muted">{{ $quotation->designRequest->customer->email }}</p>
            <p class="mb-0 text-muted">{{ $quotation->designRequest->customer->phone ?? 'N/A' }}</p>
        </div>

        <div class="subject">
            <p>RE: COMMERCIAL QUOTATION FOR DARK FIBRE/COLOCATION SERVICES.</p>
        </div>

        <div class="content">
            <p>Reference is made to your application submitted via our online portal on <strong>{{ $quotation->designRequest->requested_at->format('jS F Y') }}</strong> requesting for Dark Fibre/colocation services.</p>

            <div class="section">
                <div class="section-title">i. Service details</div>
                <table>
                    <thead>
                        <tr>
                            <th>Service type</th>
                            <th>Technology Type</th>
                            <th>Optic Distance</th>
                            <th>Pick up points</th>
                            <th>Lease Period</th>
                        </tr>
                    </thead>
                   <tbody>
                        @php
                            // Parse line_items if it's a JSON string
                            $items = is_string($quotation->line_items) ? json_decode($quotation->line_items, true) : $quotation->line_items;
                        @endphp

                        @if(is_array($items) && count($items) > 0)
                            @foreach($items as $index => $item)
                            @php
                                // Get technology type and link class from design_request or commercial_routes
                                $technologyType = $quotation->design_request->technology_type ?? 'N/A';
                                $linkClass = $quotation->design_request->link_class ?? 'Unknown';

                                // For commercial routes, get specific data from commercial_routes relationship
                                $commercialRouteData = null;
                                if ($item['type'] === 'commercial_route' && isset($quotation->commercial_routes) && count($quotation->commercial_routes) > 0) {
                                    $commercialRouteData = $quotation->commercial_routes->first();
                                    $technologyType = $commercialRouteData->tech_type ?? $technologyType;
                                    $linkClass = 'Premium'; // You can map this based on option field
                                }

                                // Calculate period in years
                            // Calculate period in years
                    $periodYears = 'N/A';
                    if (isset($item['metadata']['duration_months'])) {
                        $months = (float)$item['metadata']['duration_months'];
                        $years = floor($months / 12);
                        $remainingMonths = $months % 12;

                        if ($years > 0 && $remainingMonths > 0) {
                            // Format like "Ten Years (6)"
                            $yearWord = $years == 1 ? 'One Year' : (new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($years) . ' Years';
                            $periodYears = $yearWord . ' (' . $remainingMonths . ')';
                        } elseif ($years > 0) {
                            $yearWord = $years == 1 ? 'One Year' : (new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($years) . ' Years';
                            $periodYears = $yearWord;
                        } else {
                            $periodYears = $months . ' Months';
                        }
                    }
                            @endphp

                            <tr>
                            <td>
                        @php
                            switch($item['type']) {
                                case 'commercial_route':
                                    echo 'Fibre Service';
                                    break;
                                case 'colocation_service':
                                    echo 'Colocation Service';
                                    break;
                                case 'custom_item':
                                    echo 'Custom Service';
                                    break;
                                default:
                                    echo 'Other Service';
                            }
                        @endphp
                    </td>
                                <td>
                        @php
                            if ($item['type'] === 'commercial_route') {
                                echo $item['metadata']['technology_type'] ?? $technologyType ?? 'Fibre Service';
                            } elseif ($item['type'] === 'colocation_service') {
                                echo $item['metadata']['service_type'] ?? 'Colocation Service';
                            } elseif ($item['type'] === 'custom_item') {
                                echo $item['description'] ?? 'Custom Service';
                            } else {
                                echo 'Other Service';
                            }
                        @endphp
                    </td>
                                <td>As below</td>
                                <td>As below</td>
                                <td>{{ $periodYears }}</td> <!-- Fixed the syntax error here -->
                            </tr>
                            @endforeach
                        @else
                            <!-- Fallback if no items -->
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i>No line items available</i>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="section">
                <div class="section-title">ii. Charges</div>

                <div class="subsection">
    <p class="font-bold">a. Charges for Lease of Dark Fibre;</p>
    <table>
        <thead>
            <tr>
                <th width="25%">Route</th>
                <th width="5%" class="text-center">Cores</th>
                <th width="10%" class="text-end">Monthly unit cost per core</th>
                <th width="10%" class="text-end">Distance</th>
                <th width="10%" class="text-end">Quarterly Charges(USD)</th>
                <th width="8%" class="text-end">Period (yrs)</th>
                <th width="10%" class="text-end">Contract Value(USD)</th>
                <th width="11%" class="text-end">Technology Type</th>
                <th width="11%" class="text-end">Link Class</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Parse line_items if it's a JSON string
                $items = is_string($quotation->line_items) ? json_decode($quotation->line_items, true) : $quotation->line_items;
            @endphp

            @if(is_array($items) && count($items) > 0)
                @foreach($items as $index => $item)
                @php
                    // Get technology type and link class from design_request or commercial_routes
                    $technologyType = $quotation->design_request->technology_type ?? 'N/A';
                    $linkClass = $quotation->design_request->link_class ?? 'Unknown';

                    // For commercial routes, get specific data from commercial_routes relationship
                    $commercialRouteData = null;
                    if ($item['type'] === 'commercial_route' && isset($quotation->commercial_routes) && count($quotation->commercial_routes) > 0) {
                        $commercialRouteData = $quotation->commercial_routes->first();
                        $technologyType = $commercialRouteData->tech_type ?? $technologyType;
                        $linkClass = 'Premium'; // You can map this based on option field
                    }

                    // Calculate derived values
                    $monthlyCost = $item['unit_price'] ?? 0;
                    $cores = $item['quantity'] ?? 1;

                    // For commercial routes, get cores from metadata
                    if (isset($item['metadata']['cores'])) {
                        $cores = $item['metadata']['cores'];
                    }

                    // Calculate quarterly charges (monthly * 3)
                    $quarterlyCharges = $monthlyCost * 3;

                    // Calculate period in years
                    $periodYears = 'N/A';
                    if (isset($item['metadata']['duration_months'])) {
                        $months = (float)$item['metadata']['duration_months'];
                        $periodYears = round($months / 12, 2);
                    }

                    // Calculate contract value (total from the item)
                    $contractValue = $item['total'] ?? 0;

                    // Get distance from design_request or commercial_route
                    $distance = 0;
                    if ($item['type'] === 'commercial_route' && $commercialRouteData) {
                        $distance = $commercialRouteData->approx_distance_km ?? $quotation->design_request->distance ?? 0;
                    } else {
                        $distance = $quotation->design_request->distance ?? 0;
                    }
                @endphp

                <tr>
                    <td>
                        <strong>{{ $item['description'] ?? 'Item ' . ($index + 1) }}</strong>
                        @if(isset($item['metadata']['route_name']) && $item['type'] === 'commercial_route')
                            <br>
                            <small class="text-muted">
                                Route: {{ $item['metadata']['route_name'] }}
                                @if(isset($item['metadata']['distance_km']))
                                    | Distance: {{ $item['metadata']['distance_km'] }} km
                                @endif
                            </small>
                        @endif
                        @if(isset($item['metadata']['service_type']) && $item['type'] === 'colocation_service')
                            <br>
                            <small class="text-muted">
                                {{ $item['metadata']['service_type'] }}
                                @if(isset($item['metadata']['duration_months']))
                                    | Duration: {{ $item['metadata']['duration_months'] }} months
                                @endif
                            </small>
                        @endif
                    </td>

                    <td class="text-center">{{ $cores }}</td>

                    <td class="text-end">${{ number_format($monthlyCost, 2) }}</td>

                    <td class="text-end">
                        @if($item['type'] === 'commercial_route')
                            {{ number_format($distance, 2) }} km
                        @else
                            $0.00
                        @endif
                    </td>

                    <td class="text-end">${{ number_format($quarterlyCharges, 2) }}</td>

                    <td class="text-end">
                        @if($periodYears !== 'N/A')
                            {{ $periodYears }}
                        @else
                            N/A
                        @endif
                    </td>

                    <td class="text-end">${{ number_format($contractValue, 2) }}</td>

                    <td class="text-end">
                        @if($item['type'] === 'commercial_route')
                            {{ $item['metadata']['technology_type'] ?? $technologyType }}
                        @else
                            N/A
                        @endif
                    </td>

                    <td class="text-end">
                        @if($item['type'] === 'commercial_route')
                            {{ $linkClass }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                @endforeach
            @else
                <!-- Fallback if no items -->
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i>No line items available</i>
                    </td>
                </tr>
            @endif
        </tbody>
        <tfoot class="bg-light">
            <tr>
                <td colspan="8" class="text-end font-bold">Subtotal:</td>
                <td class="text-end font-bold">${{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="8" class="text-end font-bold">Tax ({{ $quotation->tax_rate * 100 }}%):</td>
                <td class="text-end font-bold">${{ number_format($quotation->tax_amount, 2) }}</td>
            </tr>
            <tr class="table-active">
                <td colspan="8" class="text-end font-bold">Total Amount:</td>
                <td class="text-end font-bold">${{ number_format($quotation->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

                <div class="subsection">
    <p class="font-bold">b. Charges for colocation;</p>
    <table>
        <thead>
            <tr>
                <th>Collocating in sub-station area</th>
                <th class="text-center">One-off Amount (USD)</th>
                <th class="text-center">Recurrent Amount (USD) per annum</th>
                <th class="text-center">Grand Totals USD</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Extract colocation items
                $colocationItems = [];
                $items = is_string($quotation->line_items) ? json_decode($quotation->line_items, true) : $quotation->line_items;

                foreach ($items as $item) {
                    if ($item['type'] === 'colocation_service') {
                        $colocationItems[] = $item;
                    }
                }
            @endphp

            @if(count($colocationItems) > 0)
                @foreach($colocationItems as $item)
                @php
                    $setupFee = $item['metadata']['setup_fee'] ?? $item['metadata']['setup_fee_usd'] ?? 0;
                    $monthlyRate = $item['unit_price'] ?? $item['metadata']['monthly_rate'] ?? 0;
                    $recurrentAnnual = $monthlyRate * 12;
                    $grandTotal = $setupFee + $recurrentAnnual;
                @endphp
                <tr>
                    <td>
                        {{ $item['description'] ?? 'Colocation Service' }}
                        @if(isset($item['metadata']['service_type']))
                            <br>
                            <small class="text-muted">{{ $item['metadata']['service_type'] }}</small>
                        @endif
                    </td>
                    <td class="text-center">${{ number_format($setupFee, 2) }}</td>
                    <td class="text-center">${{ number_format($recurrentAnnual, 2) }}</td>
                    <td class="text-center">${{ number_format($grandTotal, 2) }}</td>
                </tr>
                @endforeach
            @else
                <!-- Default example data -->
                <tr>
                    <td>1 No. Colocation Shelter Space measuring 5x3 M²</td>
                    <td class="text-center">6,500</td>
                    <td class="text-center">1,000</td>
                    <td class="text-center">7,500</td>
                </tr>
            @endif
            <tr class="table-active">
                <td class="text-end font-bold">Totals</td>
                <td class="text-center font-bold">
                    @if(count($colocationItems) > 0)
                        ${{ number_format(array_sum(array_map(function($item) {
                            return $item['metadata']['setup_fee'] ?? $item['metadata']['setup_fee_usd'] ?? 0;
                        }, $colocationItems)), 2) }}
                    @else
                        6,500
                    @endif
                </td>
                <td class="text-center font-bold">
                    @if(count($colocationItems) > 0)
                        ${{ number_format(array_sum(array_map(function($item) {
                            $monthlyRate = $item['unit_price'] ?? $item['metadata']['monthly_rate'] ?? 0;
                            return $monthlyRate * 12;
                        }, $colocationItems)), 2) }}
                    @else
                        1,000
                    @endif
                </td>
                <td class="text-center font-bold">
                    @if(count($colocationItems) > 0)
                        ${{ number_format(array_sum(array_map(function($item) {
                            $setupFee = $item['metadata']['setup_fee'] ?? $item['metadata']['setup_fee_usd'] ?? 0;
                            $monthlyRate = $item['unit_price'] ?? $item['metadata']['monthly_rate'] ?? 0;
                            return $setupFee + ($monthlyRate * 12);
                        }, $colocationItems)), 2) }}
                    @else
                        7,500
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

                <div class="subsection">
                    <p class="font-bold">c. Electricity connectivity;</p>
                    <p>Kenya Power will provide Alternating Current (AC) electricity. <strong class="font-bold">{{ $quotation->designRequest->customer->name }}</strong> will be required to fill a supply form for electricity connection and pay a refundable account deposit at Kenya Power office. Billing for monthly electricity consumption will be done at the prevailing market rates.</p>
                </div>
            </div>

            <div class="section">
                <div class="section-title">iii. Delivery Periods;</div>
                <p>The above-mentioned is ready for lease as confirmed by a site visit conducted by the joint KPLC and {{ $quotation->designRequest->customer->name }} technical teams.</p>
            </div>

            <div class="section">
                <div class="section-title">iv. Other conditions;</div>
                <ol>
                    <li>All quoted prices are valid for a 30-day period from the date of the quotation unless agreed to in writing stating otherwise.</li>
                    <li>KPLC and {{ $quotation->designRequest->customer->name }} Limited will enter into a commercial contract for lease.</li>
                    <li>A joint site survey will be required by the two technical teams to establish the full details of the colocation.</li>
                    <li>The submitted diagram/schematics will be used for seeking approval of appropriate site locate.</li>
                    <li>All taxes are applicable on lease.</li>
                </ol>
            </div>

            <p>If you accept this offer, please complete the attached IRU/Lease Order Form and return it the undersigned office.</p>
        </div>

        <div class="signature-block">
            <p class="mb-20">Yours faithfully</p>
            <p class="font-bold">For: KENYA POWER & LIGHTING CO. LTD</p>
            <p>&nbsp;</p>
            <p class="font-bold">GENERAL MANAGER, ICT</p>
        </div>

        <!-- Page break for second schedule -->
        <div class="page-break"></div>

        <!-- Centered Logo at top of second page -->
        <div class="page-break-logo">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="logo-img">
            </div>
        </div>

        <div class="schedule">
            <h2>THE SECOND SCHEDULE</h2>
            <h3>IRU/LEASE ORDER FORM</h3>
            <h3>GRANT/LEASE OF DARK FIBRE</h3>

            <div class="content">
               <div class="section">
    <p class="font-bold">1. Purchaser/Lessee and The Grantor/Lessor</p>
    @php
        $customer = $quotation->designRequest->customer;
        $customerName = $customer->name;

        if ($customer->customerProfile) {
            $profile = $customer->customerProfile;

            // Build street address
            $streetParts = [];
            if (!empty($profile->physical_location)) {
                $streetParts[] = $profile->physical_location;
            }
            if (!empty($profile->road)) {
                $streetParts[] = $profile->road;
            }
            $street = !empty($streetParts) ? implode(', ', $streetParts) : '..............................................';

            // Town/City
            $town = !empty($profile->town) ? ucfirst($profile->town) : '....................';

            // P.O Box
            $poBoxParts = [];
            if (!empty($profile->address)) {
                $poBoxParts[] = $profile->address;
            }
            if (!empty($profile->code)) {
                $poBoxParts[] = $profile->code;
            }
            $poBox = !empty($poBoxParts) ? implode('-', $poBoxParts) : '...............................................';
        } else {
            $street = '..............................................';
            $town = '....................';
            $poBox = '...............................................';
        }
    @endphp

    <p>We....<strong class="font-bold">{{ $customerName }}</strong>..., a company incorporated under the laws of Kenya and having its principal office at <strong class="font-bold">{{ $street }} Street {{ $town }}</strong> City/Town in Kenya and of P.O Box <strong class="font-bold">{{ $poBox }}</strong> hereby request for a Grant IRU/Lease of Dark fibre from <strong class="font-bold">THE KENYA POWER & LIGHTING COMPANY LIMITED</strong> a limited liability company duly incorporated under the Companies Act, Chapter 486 of the Laws of Kenya, with its registered office situated at Stima Plaza, Kolobot Road, Parklands, Nairobi in the Republic of Kenya and of Post Office Box Number<strong class="font-bold">30099-00100</strong>, Nairobi in the Republic aforesaid.</p>
</div>

                <div class="section">
                <div class="section-title">2. Details of Service </div>
                <table>
                    <thead>
                        <tr>
                            <th>Service type</th>
                            <th>Technology Type</th>
                            <th>Optic Distance</th>
                            <th>Pick up points</th>
                            <th>Lease Period</th>
                        </tr>
                    </thead>
                   <tbody>
                        @php
                            // Parse line_items if it's a JSON string
                            $items = is_string($quotation->line_items) ? json_decode($quotation->line_items, true) : $quotation->line_items;
                        @endphp

                        @if(is_array($items) && count($items) > 0)
                            @foreach($items as $index => $item)
                            @php
                                // Get technology type and link class from design_request or commercial_routes
                                $technologyType = $quotation->design_request->technology_type ?? 'N/A';
                                $linkClass = $quotation->design_request->link_class ?? 'Unknown';

                                // For commercial routes, get specific data from commercial_routes relationship
                                $commercialRouteData = null;
                                if ($item['type'] === 'commercial_route' && isset($quotation->commercial_routes) && count($quotation->commercial_routes) > 0) {
                                    $commercialRouteData = $quotation->commercial_routes->first();
                                    $technologyType = $commercialRouteData->tech_type ?? $technologyType;
                                    $linkClass = 'Premium'; // You can map this based on option field
                                }

                                // Calculate period in years
                            // Calculate period in years
                    $periodYears = 'N/A';
                    if (isset($item['metadata']['duration_months'])) {
                        $months = (float)$item['metadata']['duration_months'];
                        $years = floor($months / 12);
                        $remainingMonths = $months % 12;

                        if ($years > 0 && $remainingMonths > 0) {
                            // Format like "Ten Years (6)"
                            $yearWord = $years == 1 ? 'One Year' : (new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($years) . ' Years';
                            $periodYears = $yearWord . ' (' . $remainingMonths . ')';
                        } elseif ($years > 0) {
                            $yearWord = $years == 1 ? 'One Year' : (new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($years) . ' Years';
                            $periodYears = $yearWord;
                        } else {
                            $periodYears = $months . ' Months';
                        }
                    }
                            @endphp

                            <tr>
                            <td>
                        @php
                            switch($item['type']) {
                                case 'commercial_route':
                                    echo 'Fibre Service';
                                    break;
                                case 'colocation_service':
                                    echo 'Colocation Service';
                                    break;
                                case 'custom_item':
                                    echo 'Custom Service';
                                    break;
                                default:
                                    echo 'Other Service';
                            }
                        @endphp
                    </td>
                                <td>
                        @php
                            if ($item['type'] === 'commercial_route') {
                                echo $item['metadata']['technology_type'] ?? $technologyType ?? 'Fibre Service';
                            } elseif ($item['type'] === 'colocation_service') {
                                echo $item['metadata']['service_type'] ?? 'Colocation Service';
                            } elseif ($item['type'] === 'custom_item') {
                                echo $item['description'] ?? 'Custom Service';
                            } else {
                                echo 'Other Service';
                            }
                        @endphp
                    </td>
                                <td>As below</td>
                                <td>As below</td>
                                <td>{{ $periodYears }}</td> <!-- Fixed the syntax error here -->
                            </tr>
                            @endforeach
                        @else
                            <!-- Fallback if no items -->
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i>No line items available</i>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

                <div class="section">
                    <div class="section-title">3. Charges for colocation space;</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Collocating in sub-station area (for 15M² shelter)</th>
                                <th class="text-center">One-off Amount (USD)</th>
                                <th class="text-center">Recurrent Amount (USD) per annum</th>
                                <th class="text-center">Grand Totals USD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 No. Colocation Shelter Space measuring 5x3 M²</td>
                                <td class="text-center">6,500</td>
                                <td class="text-center">1,000</td>
                                <td class="text-center">7,500</td>
                            </tr>
                            <tr class="table-active">
                                <td class="text-end font-bold">Totals</td>
                                <td class="text-center font-bold">6,500</td>
                                <td class="text-center font-bold">1,000</td>
                                <td class="text-center font-bold">7,500</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">4. Assignment of Cores</div>
                    <table>
    <thead>
        <tr>
            <th>Item No.</th>
            <th>Cable type (OPGW/ADSS)</th>
            <th>Service type (Metro, Premium, etc)</th>
            <th>No. of cores</th>
        </tr>
    </thead>
    <tbody>
    @php
        // Check if we have commercial routes in the relationship
        if ($quotation->commercial_routes && $quotation->commercial_routes->count() > 0) {
            $route = $quotation->commercial_routes->first();
            $cableType = $route->tech_type ?? 'ADSS';
            $serviceType = 'Premium'; // Based on your mapping
            $cores = $route->pivot->quantity ??
                    $route->no_of_cores_required ??
                    ($quotation->design_request->cores_required ?? 1);
    @endphp
        <tr>
            <td>1.</td>
            <td>{{ $cableType }}</td>
            <td>{{ $serviceType }}</td>
            <td>{{ $cores }}</td>
        </tr>
    @php
        } else {
            // Fallback to line_items
            $items = is_string($quotation->line_items) ? json_decode($quotation->line_items, true) : $quotation->line_items;

            if (is_array($items)) {
                $commercialRouteItems = array_filter($items, function($item) {
                    return $item['type'] === 'commercial_route';
                });

                if (count($commercialRouteItems) > 0) {
                    foreach ($commercialRouteItems as $index => $item) {
    @endphp
                        <tr>
                            <td>{{ $index + 1 }}.</td>
                            <td>{{ $item['metadata']['technology_type'] ?? 'OPGW' }}</td>
                            <td>Premium</td>
                            <td>{{ $item['metadata']['cores'] ?? 1 }}</td>
                        </tr>
    @php
                    }
                } else {
                    // Final fallback
    @endphp
                    <tr>
                        <td>1.</td>
                        <td>{{ $quotation->design_request->technology_type ?? 'OPGW' }}</td>
                        <td>Premium</td>
                        <td>{{ $quotation->design_request->cores_required ?? 1 }}</td>
                    </tr>
    @php
                }
            } else {
                // Default row
    @endphp
                <tr>
                    <td>1.</td>
                    <td>OPGW</td>
                    <td>Premium</td>
                    <td>1</td>
                </tr>
    @php
            }
        }
    @endphp
</tbody>
</table>
                    <p class="mt-30">Kindly indicate the tentative take-up date, which is proposed to be 30 days following the signing of this agreement.</p>
                    <p class="text-muted"><em>Note: Acceptance certificate to be signed by both parties after the necessary tests of the cable</em></p>
                </div>

                <div class="section">
    <div class="section-title">5. Lump Sums and O&M Charges</div>
    @php
        // Don't use number_format() here - pass the raw number
        $totalAmountInWords = \App\Helpers\NumberToWordsHelper::convert($quotation->total_amount);
    @endphp
    <p>5.1. Lease total sum is USD ......... <span class="highlight-amount">{{ number_format($quotation->total_amount, 2) }}</span> ({{ $totalAmountInWords }})..................</p>
    <p>5.2 Lease Lump sum paid is USD.........NIL.........................</p>
    <p>5.3 Annual O&M Charges payable is USD......NIL......................</p>
    <p>Lease, IRU and O&M charges to be approved by KPLC Management after negotiations/tender proceeding with Purchaser/Lessee</p>
</div>

                <div class="section">
                    <div class="section-title">6. Charges for temporary colocation space;</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Collocating in sub-station area (for 15M² shelter)</th>
                                <th class="text-center">One-off Amount (USD)</th>
                                <th class="text-center">Recurrent Amount (USD) per annum</th>
                                <th class="text-center">Grand Totals USD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 No. Colocation Shelter Space measuring 5x3 M²</td>
                                <td class="text-center">6,500</td>
                                <td class="text-center">1,000</td>
                                <td class="text-center">7,500</td>
                            </tr>
                            <tr class="table-active">
                                <td class="text-end font-bold">Totals</td>
                                <td class="text-center font-bold">6,500</td>
                                <td class="text-center font-bold">1,000</td>
                                <td class="text-center font-bold">7,500</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-muted mt-20">*The annual amount chargeable to be determined by KPLC Management based on prevailing market rates</p>
                </div>

                <div class="signature-block">
                    <p>Signed .......................................</p>
                    <p>Date........................................</p>
                    <p>Name of Signatory .......................................</p>
                    <p>Stamp........................................</p>
                    <p class="font-bold mt-20">{{ $quotation->designRequest->customer->name }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
