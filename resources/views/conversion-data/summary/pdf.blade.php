<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fibre Summary Report</title>
    <style>
        /* PDF-specific styles */
        @page {
            margin: 20px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #007bff;
            font-size: 24px;
            margin: 0;
        }

        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            background-color: #fff;
        }

        .stat-card .label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }

        .stat-card .sub-value {
            font-size: 12px;
            color: #28a745;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            color: #495057;
        }

        table td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #6c757d;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        .currency {
            font-family: 'Courier New', monospace;
        }

        .highlight {
            background-color: #fff3cd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Fibre Network Summary Report</h1>
        <div class="subtitle">
            Generated on: {{ date('F j, Y') }} | Period: Last 12 Months
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="section">
        <div class="section-title">Executive Summary</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Contracts</div>
                <div class="value">{{ number_format($summary['total_contracts']) }}</div>
            </div>

            <div class="stat-card">
                <div class="label">Total Customers</div>
                <div class="value">{{ number_format($summary['total_customers']) }}</div>
            </div>

            <div class="stat-card">
                <div class="label">Total Monthly Revenue</div>
                <div class="value currency">${{ number_format($summary['total_monthly_value_usd'], 2) }}</div>
                <div class="sub-value currency">KES {{ number_format($summary['total_monthly_value_kes'], 2) }}</div>
            </div>

            <div class="stat-card">
                <div class="label">Total Contract Value</div>
                <div class="value currency">${{ number_format($summary['total_contract_value_usd'], 2) }}</div>
                <div class="sub-value currency">KES {{ number_format($summary['total_contract_value_kes'], 2) }}</div>
            </div>

            <div class="stat-card">
                <div class="label">Avg. Monthly/Contract</div>
                <div class="value currency">${{ number_format($summary['avg_monthly_usd'], 2) }}</div>
                <div class="sub-value currency">KES {{ number_format($summary['avg_monthly_kes'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Contract Duration Distribution -->
    <div class="section">
        <div class="section-title">Contract Duration Distribution</div>
        @if(count($contractDurationDistribution) > 0)
            @php
                $totalDurationCount = 0;
                foreach($contractDurationDistribution as $duration) {
                    $totalDurationCount += $duration['count'];
                }
            @endphp

            <table>
                <thead>
                    <tr>
                        <th>Duration</th>
                        <th class="text-end">Count</th>
                        <th class="text-end">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contractDurationDistribution as $duration)
                    <tr>
                        <td>{{ $duration['label'] }}</td>
                        <td class="text-end">{{ $duration['count'] }}</td>
                        <td class="text-end">{{ $totalDurationCount > 0 ? number_format(($duration['count'] / $totalDurationCount) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach

                    @if($noDurationCount > 0)
                    <tr class="highlight">
                        <td>No duration specified</td>
                        <td class="text-end">{{ $noDurationCount }}</td>
                        <td class="text-end">{{ $totalDurationCount > 0 ? number_format(($noDurationCount / $totalDurationCount) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        @else
            <div class="no-data">No contract duration data available</div>
        @endif
    </div>

    <!-- Link Class Distribution -->
    <div class="section">
        <div class="section-title">Link Class Distribution</div>
        @if(count($linkClassDistribution) > 0)
            @php
                $totalLinkClassCount = 0;
                foreach($linkClassDistribution as $linkClass) {
                    $totalLinkClassCount += $linkClass['count'];
                }
            @endphp

            <table>
                <thead>
                    <tr>
                        <th>Link Class</th>
                        <th class="text-end">Count</th>
                        <th class="text-end">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($linkClassDistribution as $linkClass)
                    <tr>
                        <td>{{ $linkClass['label'] }}</td>
                        <td class="text-end">{{ $linkClass['count'] }}</td>
                        <td class="text-end">{{ $totalLinkClassCount > 0 ? number_format(($linkClass['count'] / $totalLinkClassCount) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No link class data available</div>
        @endif
    </div>

    <!-- Top Customers -->
    <div class="section">
        <div class="section-title">Top 10 Customers by Contract Value</div>
        @if(count($topCustomers) > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th class="text-end">Total Contract Value (USD)</th>
                        <th class="text-end">Total Contract Value (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCustomers as $index => $customer)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $customer->customer_name ?? 'Unknown' }}</td>
                        <td class="text-end currency">${{ number_format($customer->total_contract_value_usd, 2) }}</td>
                        <td class="text-end currency">KES {{ number_format($customer->total_contract_value_kes, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No customer data available</div>
        @endif
    </div>

    <!-- Monthly Trends -->
    <div class="section">
        <div class="section-title">Monthly Trends (Last 12 Months)</div>
        @if(count($monthlyTrends) > 0)
            @php
                $totalContracts = 0;
                $totalRevenueUSD = 0;
                $totalRevenueKES = 0;
                foreach($monthlyTrends as $trend) {
                    $totalContracts += $trend->contracts;
                    $totalRevenueUSD += $trend->monthly_usd;
                    $totalRevenueKES += $trend->monthly_kes;
                }
                $avgMonthlyContracts = count($monthlyTrends) > 0 ? $totalContracts / count($monthlyTrends) : 0;
                $avgMonthlyRevenueUSD = count($monthlyTrends) > 0 ? $totalRevenueUSD / count($monthlyTrends) : 0;
                $avgMonthlyRevenueKES = count($monthlyTrends) > 0 ? $totalRevenueKES / count($monthlyTrends) : 0;
            @endphp

            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-end">Contracts</th>
                        <th class="text-end">Monthly Revenue (USD)</th>
                        <th class="text-end">Monthly Revenue (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyTrends as $trend)
                    <tr>
                        <td>{{ date('M Y', strtotime($trend->month . '-01')) }}</td>
                        <td class="text-end">{{ $trend->contracts }}</td>
                        <td class="text-end currency">${{ number_format($trend->monthly_usd, 2) }}</td>
                        <td class="text-end currency">KES {{ number_format($trend->monthly_kes, 2) }}</td>
                    </tr>
                    @endforeach

                    <!-- Summary row -->
                    <tr class="highlight">
                        <td><strong>Average</strong></td>
                        <td class="text-end"><strong>{{ number_format($avgMonthlyContracts, 1) }}</strong></td>
                        <td class="text-end currency"><strong>${{ number_format($avgMonthlyRevenueUSD, 2) }}</strong></td>
                        <td class="text-end currency"><strong>KES {{ number_format($avgMonthlyRevenueKES, 2) }}</strong></td>
                    </tr>

                    <!-- Total row -->
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="text-end"><strong>{{ $totalContracts }}</strong></td>
                        <td class="text-end currency"><strong>${{ number_format($totalRevenueUSD, 2) }}</strong></td>
                        <td class="text-end currency"><strong>KES {{ number_format($totalRevenueKES, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">No monthly trend data available</div>
        @endif
    </div>

    <!-- Detailed Statistics -->
    <div class="section">
        <div class="section-title">Detailed Network Statistics</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Cores Leased</div>
                <div class="value">{{ number_format($detailedStats['total_cores_leased'], 0) }}</div>
                <div class="sub-value">Avg: {{ number_format($detailedStats['avg_cores_leased'], 1) }}</div>
            </div>

            <div class="stat-card">
                <div class="label">Total Distance (KM)</div>
                <div class="value">{{ number_format($detailedStats['total_distance'], 1) }}</div>
                <div class="sub-value">Avg: {{ number_format($detailedStats['avg_distance'], 1) }} km</div>
            </div>

            <div class="stat-card">
                <div class="label">Avg Contract Duration</div>
                <div class="value">{{ number_format($detailedStats['avg_contract_duration'], 1) }} yrs</div>
                <div class="sub-value">
                    Range: {{ $detailedStats['min_contract_duration'] ?? 0 }} -
                    {{ $detailedStats['max_contract_duration'] }} yrs
                </div>
            </div>

            <div class="stat-card">
                <div class="label">Contracts with Pricing</div>
                <div class="value">{{ number_format($detailedStats['contracts_with_pricing']) }}</div>
                <div class="sub-value">
                    {{ $summary['total_contracts'] > 0 ?
                        number_format(($detailedStats['contracts_with_pricing'] / $summary['total_contracts']) * 100, 1) : 0 }}% coverage
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Report generated on {{ date('F j, Y H:i:s') }}</div>
        <div>DarkFibre CRM System | Confidential Report</div>
        <div>Page 1 of 1</div>
    </div>
</body>
</html>
