<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lease Agreement - {{ $lease->lease_number }}</title>

    <style>
     * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

@page {
    size: A4;
    margin: 15mm;
}

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 8.6px;
    line-height: 1.22;
    color: #1f2933;
    background: #ffffff;
}

.watermark {
    position: fixed;
    top: 56%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    font-size: 46px;
    color: rgba(0, 102, 204, 0.025);
    z-index: -1;
    white-space: nowrap;
}

table {
    width: 100%;
    border-collapse: collapse;
}

.logo-table {
    margin-bottom: 8px;
    border-bottom: 2px solid #0066cc;
}

.logo-table td {
    width: 50%;
    height: 62px;
    padding: 5px;
    border: none;
    text-align: center;
    vertical-align: middle;
}

img {
    max-height: 58px;
    max-width: 150px;
    object-fit: contain;
}

.company-header {
    text-align: center;
    margin-bottom: 6px;
}

.company-header .company-name {
    font-size: 13px;
    font-weight: bold;
    color: #0066cc;
}

.company-header .tagline {
    font-size: 8px;
    color: #6b7280;
}

.doc-info {
    background: #0066cc;
    color: #ffffff;
    padding: 5px 8px;
    border-radius: 2px;
    font-size: 8px;
    margin-bottom: 7px;
}

.doc-info td {
    border: none;
    color: #ffffff;
    padding: 1px;
}

.title-box {
    text-align: center;
    background: #003f7f;
    color: #ffffff;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 3px;
    border-bottom: 3px solid #fdb913;
}

.title-box .main-title {
    font-size: 14px;
    font-weight: bold;
    letter-spacing: 0.3px;
}

.title-box .sub-title {
    font-size: 8px;
    color: #fff3cd;
}

.section {
    margin-bottom: 8px;
    page-break-inside: avoid;
}

.section-title {
    background: #0066cc;
    color: #ffffff;
    padding: 5px 8px;
    font-weight: bold;
    border-left: 4px solid #fdb913;
    font-size: 8.5px;
    margin-bottom: 3px;
}

.two-column-table td {
    width: 50%;
    vertical-align: top;
    border: none;
}

.two-column-table td:first-child {
    padding-right: 4px;
}

.two-column-table td:last-child {
    padding-left: 4px;
}

.info-card {
    border: 1px solid #c7d7ee;
    padding: 7px;
    min-height: 78px;
    background: #ffffff;
}

.card-title {
    font-size: 8.4px;
    font-weight: bold;
    color: #003f7f;
    border-bottom: 1px solid #fdb913;
    padding-bottom: 3px;
    margin-bottom: 4px;
}

.compact-text {
    font-size: 8px;
    line-height: 1.35;
}

.data-table {
    font-size: 8px;
}

.data-table th {
    background: #0066cc;
    color: #ffffff;
    border: 1px solid #0052a3;
    padding: 4px;
    font-weight: bold;
    text-align: left;
}

.data-table td {
    border: 1px solid #d9e6f7;
    padding: 4px;
}

.data-table tr:nth-child(even) td {
    background: #f5f9ff;
}

.financial-box {
    border: 1px solid #16a34a;
    background: #f0fff4;
    padding: 6px;
}

.financial-table td {
    border: none;
    padding: 3px;
    font-size: 8px;
}

.financial-total td {
    border-top: 1px solid #16a34a;
    padding-top: 4px;
    font-weight: bold;
    color: #003f7f;
}

.signature-section {
    margin-top: 10px;
    padding: 8px;
    border: 1px dashed #0066cc;
    background: #f8fbff;
    page-break-inside: avoid;
}

.signature-table td {
    width: 50%;
    border: none;
    padding: 3px;
    vertical-align: top;
}

.signature-box {
    border: 1px solid #c7d7ee;
    padding: 8px;
    background: #ffffff;
}

.signature-area {
    height: 38px;
    line-height: 38px;
    border: 1px dashed #fdb913;
    margin: 4px 0;
    font-size: 7px;
    background: #fffdf3;
    text-align: center;
    color: #6b7280;
}

.signature-line {
    border-top: 1px solid #003f7f;
    margin-top: 4.5px;
    padding-top: 3px;
    font-size: 7.5px;
    line-height: 2.15;
}

.contact-info {
    background: #003f7f;
    color: #ffffff;
    padding: 6px;
    margin-top: 10px;
    text-align: center;
    border-top: 2px solid #fdb913;
    font-size: 7.5px;
}

.footer {
    margin-top: 10px;
    padding-top: 3px;
    border-top: 1px solid #0066cc;
    text-align: center;
    font-size: 6.8px;
    color: #6b7280;
}

.status-badge {
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 6.8px;
    font-weight: bold;
}

.active {
    background: #16a34a;
    color: #ffffff;
}

.pending {
    background: #fdb913;
    color: #1f2933;
}

.draft {
    background: #6b7280;
    color: #ffffff;
}

.other {
    background: #dc2626;
    color: #ffffff;
}

.value-highlight {
    background: #fff3cd;
    padding: 2px 4px;
    border-radius: 2px;
    font-weight: bold;
}

strong {
    color: #003f7f;
}

.doc-info strong,
.section-title strong,
.contact-info strong {
    color: #ffffff;
}
    </style>
</head>

<body>

<div class="watermark">LEASE</div>

<table class="logo-table">
    <tr>
        <td>
            <img src="{{ public_path('images/logo.png') }}" alt="KPLC Logo">
        </td>

        <td>
            @if($lease->customer->companyProfile->profile_photo ?? false)
                <img src="{{ storage_path('app/public/' . $lease->customer->companyProfile->profile_photo) }}" alt="Customer Logo">
            @else
                <strong>{{ $lease->customer->name ?? 'Customer' }}</strong>
            @endif
        </td>
    </tr>
</table>

<div class="company-header">
    <div class="company-name">
        Kenya Power and Lighting Company PLC
    </div>

    <div class="tagline">
        Professional Network Infrastructure Services
    </div>
</div>

<table class="doc-info">
    <tr>
        <td>
            <strong>Document:</strong> {{ $lease->lease_number }}
        </td>

        <td style="text-align:center;">
            <strong>Date:</strong> {{ now()->format('M d, Y') }}
        </td>

        <td style="text-align:right;">
            <strong>Status:</strong>

            <span class="status-badge {{ $lease->status === 'active' ? 'active' : ($lease->status === 'pending' ? 'pending' : ($lease->status === 'draft' ? 'draft' : 'other')) }}">
                {{ ucfirst($lease->status) }}
            </span>
        </td>
    </tr>
</table>

<div class="title-box">
    <div class="main-title">
        FIBRE OPTIC LEASE AGREEMENT
    </div>

    <div class="sub-title">
        Professional Dark Fibre Services Contract
    </div>
</div>

<div class="section">
    <div class="section-title">
        CONTRACTING PARTIES
    </div>

    <table class="two-column-table">
        <tr>
            <td>
                <div class="info-card">
                    <div class="card-title">
                        LESSOR / SERVICE PROVIDER
                    </div>

                    <div class="compact-text">
                        <strong>Company:</strong> Kenya Power and Lighting Company PLC<br>
                        <strong>Address:</strong> P.O. Box 30099-00100, Nairobi<br>
                        <strong>Contact:</strong> +254 720 000000<br>
                        <strong>Email:</strong> fibre@kplc.co.ke
                    </div>
                </div>
            </td>

            <td>
                <div class="info-card">
                    <div class="card-title">
                        LESSEE / CUSTOMER
                    </div>

                    <div class="compact-text">
                        <strong>Name:</strong> {{ $lease->customer->name ?? 'N/A' }}<br>
                        <strong>Email:</strong> {{ $lease->customer->email ?? 'N/A' }}<br>
                        <strong>Phone:</strong> {{ $lease->customer->phone ?? 'N/A' }}<br>
                        <strong>Company:</strong> {{ $lease->customer->company ?? $lease->customer->name ?? 'N/A' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <table class="two-column-table">
        <tr>
            <td>
                <div class="section-title">
                    SERVICE OVERVIEW
                </div>

                <table class="data-table">
                    <tr>
                        <td><strong>Service Type</strong></td>
                        <td>{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</td>
                    </tr>

                    <tr>
                        <td><strong>Bandwidth</strong></td>
                        <td>{{ $lease->bandwidth ?? 'Custom' }}</td>
                    </tr>

                    <tr>
                        <td><strong>Technology</strong></td>
                        <td>{{ $lease->technology ?? 'Standard Fibre' }}</td>
                    </tr>

                    <tr>
                        <td><strong>Route</strong></td>
                        <td>{{ $lease->start_location ?? 'TBD' }} → {{ $lease->end_location ?? 'TBD' }}</td>
                    </tr>

                    <tr>
                        <td><strong>Distance</strong></td>
                        <td>{{ $lease->distance_km ? number_format($lease->distance_km, 2) . ' km' : 'TBD' }}</td>
                    </tr>
                </table>
            </td>

            <td>
                <div class="section-title">
                    CONTRACT TIMELINE
                </div>

                <table class="data-table">
                    <tr>
                        <td><strong>Start Date</strong></td>
                        <td>
                            <span class="value-highlight">
                                {{ $lease->start_date ? $lease->start_date->format('M d, Y') : 'N/A' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>End Date</strong></td>
                        <td>
                            <span class="value-highlight">
                                {{ $lease->end_date ? $lease->end_date->format('M d, Y') : 'N/A' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Contract Term</strong></td>
                        <td>{{ $lease->contract_term_months }} months</td>
                    </tr>

                    <tr>
                        <td><strong>Billing Cycle</strong></td>
                        <td>{{ ucfirst($lease->billing_cycle) }}</td>
                    </tr>

                    <tr>
                        <td><strong>Next Billing</strong></td>
                        <td>{{ $lease->next_billing_date ? $lease->next_billing_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">
        FINANCIAL SUMMARY
    </div>

    <div class="financial-box">
        <table class="financial-table">
            <tr>
                <td>
                    Monthly Service Fee:
                </td>

                <td style="text-align:right;">
                    <strong>
                        {{ strtoupper($lease->currency) }} {{ number_format($lease->monthly_cost, 2) }}
                    </strong>
                </td>
            </tr>

            <tr>
                <td>
                    Installation Fee:
                </td>

                <td style="text-align:right;">
                    {{ strtoupper($lease->currency) }} {{ number_format($lease->installation_fee, 2) }}
                </td>
            </tr>

            <tr>
                <td>
                    Billing Cycle:
                </td>

                <td style="text-align:right;">
                    {{ ucfirst($lease->billing_cycle) }}
                </td>
            </tr>

            <tr class="financial-total">
                <td>
                    Total Contract Value:
                </td>

                <td style="text-align:right;">
                    {{ strtoupper($lease->currency) }} {{ number_format($lease->total_contract_value, 2) }}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="section">
    <div class="section-title">
        PAYMENT SCHEDULE
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Period</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Due Date</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>Initial</td>
                <td>Installation Fee</td>
                <td>{{ strtoupper($lease->currency) }} {{ number_format($lease->installation_fee, 2) }}</td>
                <td>Upon signing</td>
            </tr>

            <tr>
                <td>{{ ucfirst($lease->billing_cycle) }}</td>
                <td>Recurring Service Fee</td>
                <td>{{ strtoupper($lease->currency) }} {{ number_format($lease->monthly_cost, 2) }}</td>
                <td>{{ $lease->next_billing_date ? $lease->next_billing_date->format('M d, Y') : 'Per billing cycle' }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="signature-section">
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-box">
                    <div class="card-title">
                        FOR KENYA POWER AND LIGHTING
                    </div>

                    <div class="signature-area">
                        Signature Area
                    </div>

                    <div class="signature-line">
                        Name: ___________________________________________<br>
                        Title: __________________________________________<br>
                        Date: ___________________________________________
                    </div>
                </div>
            </td>

            <td>
                <div class="signature-box">
                    <div class="card-title">
                        FOR CUSTOMER
                    </div>

                    <div class="signature-area">
                        Signature Area
                    </div>

                    <div class="signature-line">
                        Name: ___________________________________________<br>
                        Title: __________________________________________<br>
                        Date: ___________________________________________
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="contact-info">
    <strong>Need Assistance?</strong>
    Support: +254 720 000000 |
    Email: fibre@kplc.co.ke
</div>

<div class="footer">
    <strong>Kenya Power and Lighting Company PLC</strong><br>
    Generated on {{ now()->format('M d, Y \a\t h:i A') }} |
    Document ID: {{ $lease->id }} |
    Confidential document - Unauthorized distribution prohibited
</div>

</body>
</html>
