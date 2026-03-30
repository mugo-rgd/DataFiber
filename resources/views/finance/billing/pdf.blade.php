<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $billing->billing_number }}</title>
    <style>
        @page {
            margin: 15px;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif;
            font-size: 9.5px;
            color: #222;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .invoice-wrapper {
            border: 1.5px solid #2c3e50;
            padding: 15px;
            max-width: 780px;
            margin: 0 auto;
            background: white;
            border-radius: 4px;
        }
        .compact-table {
            width: 100%;
            border-collapse: collapse;
        }
        .compact-table td {
            vertical-align: top;
            padding: 4px 6px;
        }
        .header-row {
            border-bottom: 1.5px solid #2c3e50;
            margin-bottom: 12px;
            padding-bottom: 10px;
        }
        .company-cell {
            width: 65%;
            border-right: 1px solid #ddd;
        }
        .invoice-cell {
            width: 35%;
            text-align: right;
        }
        .section-heading {
            font-size: 10.5px;
            font-weight: 700;
            color: #2c3e50;
            background: #f1f5f9;
            padding: 5px 8px;
            margin: 10px 0 6px 0;
            border-left: 3px solid #3498db;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin: 8px 0;
        }
        .data-table th {
            background: #2c3e50;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #2c3e50;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #ddd;
        }
        .data-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .totals-section {
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }
        .footer-note {
            font-size: 8.5px;
            color: #666;
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
        }
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .currency-box {
            background: #f8f9fa;
            padding: 6px 10px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            font-size: 9.5px;
            margin: 8px 0;
        }
        .qr-container {
            text-align: center;
            padding: 8px;
            border: 1px solid #eaeaea;
            border-radius: 4px;
            background: white;
        }
        .totals-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
        }
        .grand-total-row {
            font-weight: 800;
            color: #2c3e50;
            background: #f1f8ff !important;
            border-top: 2px solid #2c3e50;
        }
        .text-sm { font-size: 8.5px; }
        .text-bold { font-weight: 700; }
        .text-right { text-align: right; }
        .color-primary { color: #2c3e50; }
        .color-secondary { color: #555; }
        .mb-1 { margin-bottom: 4px; }
        .mt-1 { margin-top: 4px; }
        .p-0 { padding: 0; }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <!-- ===== HEADER: Company & Invoice Info ===== -->
        <table class="compact-table header-row">
            <tr>
                <td class="company-cell" style="padding-right: 15px;">
                    <table class="compact-table">
                        <tr>
                            <td style="width: 75px; padding-right: 10px;">
                                <img src="{{ asset('images/logo.png') }}"
                                     alt="Logo"
                                     style="max-width: 65px; max-height: 65px; display: block;">
                            </td>

                            <td>
                                <p style="margin: 0 0 2px 0; font-size: 12px; font-weight: 800; color: #2c3e50;">
                                    Kenya Power & Lighting Co. Ltd
                                </p>
                                <p style="margin: 0 0 4px 0; font-size: 10px; color: #3498db; font-weight: 600;">
                                    Dark Fibre Services Division
                                </p>
                                <p style="margin: 1px 0; font-size: 8.5px; color: #555;">P.O. Box 30099 - 00100, Nairobi</p>
                                <p style="margin: 1px 0; font-size: 8.5px; color: #555;">Tel: +254 20 320 1000</p>
                                <p style="margin: 1px 0; font-size: 8.5px; color: #555;">Email: darkfibre@kplc.co.ke</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="invoice-cell">
                    <h1 style="margin: 0 0 5px 0; color: #2c3e50; font-size: 18px; font-weight: 800;">INVOICE</h1>
                    <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 700; color: #3498db;">
                        {{ $billing->billing_number }}
                    </p>
                    <p style="margin: 2px 0; font-size: 9px;"><strong>Consolidated Billing</strong></p>
                    <p style="margin: 2px 0; font-size: 8.5px;">Date: {{ $billing->billing_date->format('d-M-Y') }}</p>
                    <p style="margin: 2px 0; font-size: 8.5px;">Due: {{ $billing->due_date->format('d-M-Y') }}</p>
                    <p style="margin: 2px 0; font-size: 8.5px;">
                        Status:
                        @php
                            $isOverdue = $billing->status === 'pending' && $billing->due_date < now();
                            $statusClass = $billing->status === 'paid' ? 'status-paid' : ($isOverdue ? 'status-overdue' : 'status-pending');
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($billing->status) }}@if($isOverdue) (Overdue)@endif
                        </span>
                    </p>
                </td>
            </tr>
        </table>

        <!-- ===== BILLING & CLIENT INFO ===== -->
        <div class="section-heading">BILLING INFORMATION</div>
        <table class="compact-table">
            <tr>
                <td style="width: 55%; border-right: 1px solid #eee; padding-right: 15px;">
                    <p style="margin: 0 0 3px 0; font-size: 10px; font-weight: 700; color: #2c3e50;">BILL TO</p>
                    <p style="margin: 2px 0; font-size: 9.5px; font-weight: 600;">{{ $billing->user->name }}</p>
                    @if($billing->user->company_name)
                        <p style="margin: 2px 0; font-size: 9px; color: #555;">{{ $billing->user->company_name }}</p>
                    @endif
                    <p style="margin: 2px 0; font-size: 9px; color: #555;">{{ $billing->user->email }}</p>
                    @if($billing->user->phone)
                        <p style="margin: 2px 0; font-size: 9px; color: #555;">Tel: {{ $billing->user->phone }}</p>
                    @endif
                    <p style="margin: 2px 0; font-size: 8.5px; color: #777;">Customer ID: {{ $billing->user->id }}</p>
                </td>
                <td style="width: 45%; padding-left: 10px;">
                    <p style="margin: 0 0 3px 0; font-size: 10px; font-weight: 700; color: #2c3e50;">INVOICE DETAILS</p>
                    <p style="margin: 2px 0; font-size: 9px;"><strong>Currency:</strong> {{ $billing->currency }}</p>
                    <p style="margin: 2px 0; font-size: 9px;"><strong>Leases:</strong> {{ $billing->lineItems->count() }}</p>
                    @php
                        $periodStart = $billing->lineItems->min('period_start');
                        $periodEnd = $billing->lineItems->max('period_end');
                    @endphp
                    @if($periodStart && $periodEnd)
                        <p style="margin: 2px 0; font-size: 9px;"><strong>Service Period:</strong></p>
                        <p style="margin: 2px 0; font-size: 9px;">{{ $periodStart->format('d-M-Y') }} to {{ $periodEnd->format('d-M-Y') }}</p>
                    @endif
                </td>
            </tr>
        </table>

        <!-- ===== EXCHANGE RATE ===== -->
        {{-- @if(isset($exchangeRate) && $exchangeRate) --}}
        <div class="currency-box">
            <strong style="color: #2c3e50;">💱 Exchange Rate:</strong>
            1 USD = <strong style="color: #27ae60;">{{ number_format($billing->exchange_rate, 2) }} KES</strong>
            {{-- <span style="font-size: 8.5px; color: #7f8c8d; margin-left: 8px;">
                (Rate as of {{ $exchangeRateDate ?? now()->format('d-M-Y H:i') }})
            </span> --}}
        </div>
        {{-- @endif --}}

        <!-- ===== LEASE DETAILS TABLE ===== -->
        <div class="section-heading">LEASE DETAILS</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">#</th>
                    <th>Lease Number</th>
                    <th>Service Type</th>
                    <th>Billing Cycle</th>
                    <th>Period</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billing->lineItems as $index => $lineItem)
                    @php $lease = $lineItem->lease; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 600; color: #2c3e50; font-size: 9px;">{{ $lease->lease_number ?? 'N/A' }}</div>
                            @if($lease && $lease->title)
                                <div style="color: #666; font-size: 8px;">{{ $lease->title }}</div>
                            @endif
                        </td>
                        <td>
                            @if($lease)
                                <span style="background: #e3f2fd; color: #1976d2; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 500;">
                                    {{ str_replace('_', ' ', ucfirst($lease->service_type)) }}
                                </span>
                            @else
                                <span style="color: #999; font-size: 8.5px;">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span style="background: #f3e5f5; color: #7b1fa2; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 500;">
                                {{ ucfirst($lineItem->billing_cycle) }}
                            </span>
                        </td>
                        <td style="line-height: 1.1;">
                            <div style="color: #2c3e50; font-size: 8.5px;">{{ $lineItem->period_start->format('d-M-Y') }}</div>
                            <div style="color: #999; font-size: 7.5px; text-align: center;">to</div>
                            <div style="color: #2c3e50; font-size: 8.5px;">{{ $lineItem->period_end->format('d-M-Y') }}</div>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #2c3e50; font-size: 9.5px;">
                            {{ $lineItem->currency }} {{ number_format($lineItem->amount, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ===== TOTALS & QR CODE SECTION ===== -->
        <div class="totals-section">
            <table class="compact-table">
                <tr>
                    @if($billing->tevin_qr_code)
                    <td style="width: 45%; vertical-align: top; padding-right: 15px;">
                        <div class="qr-container">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($billing->tevin_qr_code) }}&margin=4"
                                 alt="KRA QR Code"
                                 style="max-width: 120px; display: block; margin: 0 auto;">
                            <div style="margin-top: 6px; font-size: 8.5px; color: #555;">
                                @if($billing->kra_invoice_number)
                                    <div style="margin: 3px 0;"><strong>Invoice No:</strong> {{ $billing->kra_invoice_number }}</div>
                                @endif
                                @if($billing->tevin_control_code)
                                    <div style="margin: 3px 0;"><strong>Control Code:</strong> {{ $billing->tevin_control_code }}</div>
                                @endif
                            </div>
                            <div style="margin-top: 8px; font-size: 8px;">
                                <a href="{{ $billing->tevin_qr_code }}"
                                   target="_blank"
                                   style="color: #1971c2; text-decoration: none;">
                                    🔗 Verify on KRA Portal
                                </a>
                            </div>
                        </div>
                    </td>
                    @endif

                    <td style="width: {{ $billing->tevin_qr_code ? '55%' : '100%' }}; vertical-align: top;">
                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                            <tr>
                                <td style="padding: 6px 10px; font-size: 9.5px;">Subtotal:</td>
                                <td style="padding: 6px 10px; text-align: right; font-weight: 700; font-size: 9.5px;">
                                    {{ $billing->currency }} {{ number_format($billing->total_amount, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 10px; font-size: 9.5px;">VAT (16%):</td>
                                <td style="padding: 6px 10px; text-align: right; font-weight: 700; font-size: 9.5px;">
                                    {{ $billing->currency }} {{ number_format($billing->total_amount * 0.16, 2) }}
                                </td>
                            </tr>
                            <tr class="grand-total-row">
                                <td style="padding: 8px 10px; font-size: 10.5px; font-weight: 800;">TOTAL DUE:</td>
                                <td style="padding: 8px 10px; text-align: right; font-size: 11px; font-weight: 800; color: #e74c3c;">
                                    {{ $billing->currency }} {{ number_format($billing->total_amount * 1.16, 2) }}
                                </td>
                            </tr>
                        </table>

                        @if($billing->tevin_status)
                        <div style="margin-top: 10px; padding: 8px; background: #f8f9fa; border-radius: 3px; border: 1px solid #eaeaea;">
                            <div style="display: flex; justify-content: space-between; font-size: 9px;">
                                <div>
                                    <strong>KRA Status:</strong>
                                    <span style="margin-left: 6px; padding: 2px 8px; background: #e3f2fd; color: #1976d2; border-radius: 10px; font-weight: 600;">
                                        {{ ucfirst($billing->tevin_status) }}
                                    </span>
                                </div>
                                @if($billing->tevin_committed_at)
                                    <div style="color: #666;">{{ \Carbon\Carbon::parse($billing->tevin_committed_at)->format('d-M-Y') }}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== PAYMENT INSTRUCTIONS ===== -->
        <div class="section-heading" style="margin-top: 15px;">PAYMENT INSTRUCTIONS</div>
        <table class="compact-table">
            <tr>
                <td style="width: 50%; border-right: 1px solid #eee; padding-right: 10px;">
                    <p style="margin: 0 0 4px 0; font-size: 9.5px; font-weight: 700; color: #2c3e50;">Bank Transfer</p>
                    <p style="margin: 2px 0; font-size: 8.5px;"><strong>Bank:</strong> Kenya Commercial Bank</p>
                    <p style="margin: 2px 0; font-size: 8.5px;"><strong>Account:</strong> Kenya Power & Lighting Co. Ltd</p>
                    <p style="margin: 2px 0; font-size: 8.5px;"><strong>Account No:</strong> 1100 1234 5678</p>
                    <p style="margin: 2px 0; font-size: 8.5px;"><strong>Swift:</strong> KCBLKENXXXX</p>
                </td>
                <td style="width: 50%; padding-left: 10px;">
                    <p style="margin: 0 0 4px 0; font-size: 9.5px; font-weight: 700; color: #2c3e50;">M-Pesa</p>
                    <div style="font-size: 8.5px; line-height: 1.3;">
                        <div>1. Go to <strong>Lipa Na M-Pesa</strong> &gt; <strong>Pay Bill</strong></div>
                        <div>2. Business No: <strong>123456</strong></div>
                        <div>3. Account No: <strong>{{ $billing->billing_number }}</strong></div>
                        <div>4. Amount: <strong>{{ $billing->currency }} {{ number_format($billing->total_amount * 1.16, 2) }}</strong></div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ===== FOOTER ===== -->
        <div class="footer-note">
            <p style="margin: 3px 0;">Thank you for choosing Kenya Power Dark Fibre Services</p>
            <p style="margin: 3px 0; font-size: 8px;">This is a computer-generated invoice. No signature required.</p>
            <p style="margin: 3px 0; font-size: 8px; color: #888;">Invoice generated on {{ now()->format('d-M-Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
