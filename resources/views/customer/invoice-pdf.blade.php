@extends('layouts.app')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing #{{ $billing->billing_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-info {
            text-align: left;
            margin-bottom: 20px;
        }
        .billing-details {
            margin-bottom: 30px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 1.1em;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-overdue { background-color: #f8d7da; color: #721c24; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>BILLING STATEMENT</h1>
        <h2>#{{ $billing->billing_number }}</h2>
        <p>Dark Fibre CRM</p>
    </div>

    <!-- Company Information -->
    <div class="company-info">
        <h3>Dark Fibre Solutions Ltd.</h3>
        <p>123 Business Street, Nairobi, Kenya</p>
        <p>Email: billing@darkfibre.co.ke | Phone: +254 700 000 000</p>
    </div>

    <!-- Billing Details -->
    <div class="billing-details">
        <table width="100%">
            <tr>
                <td width="50%">
                    <strong>Billed To:</strong><br>
                    {{ $billing->user->company_name ?? $billing->user->name }}<br>
                    {{ $billing->user->email }}<br>
                    @if($billing->user->companyProfile && $billing->user->companyProfile->address)
                        {{ $billing->user->companyProfile->address }}<br>
                    @endif
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Billing Date:</strong> {{ $billing->billing_date->format('M d, Y') }}<br>
                    <strong>Due Date:</strong> {{ $billing->due_date->format('M d, Y') }}<br>
                    <strong>Status:</strong>
                    <span class="status-badge status-{{ $billing->status }}">
                        {{ ucfirst($billing->status) }}
                        @if($billing->isOverdue()) (Overdue) @endif
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Service Description -->
    <div class="mb-4">
        <h4>Service Description</h4>
        <p>{{ $billing->description ?? 'Monthly service charge' }}</p>
    </div>

    <!-- Billing Items -->
    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th width="15%">Billing Cycle</th>
                <th width="15%">Period</th>
                <th width="15%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $billing->description ?? 'Service Charge' }}
                    @if($billing->lease)
                        <br><small>
                            {{ $billing->lease->service_type }} -
                            {{ $billing->lease->start_location }} to {{ $billing->lease->end_location }}
                            @if($billing->lease->distance_km)
                                ({{ number_format((float)$billing->lease->distance_km, 2) }} km)
                            @endif
                        </small>
                    @endif
                </td>
                <td>{{ ucfirst($billing->billing_cycle) }}</td>
                <td>
                    {{ $billing->period_start->format('M d, Y') }}<br>
                    to<br>
                    {{ $billing->period_end->format('M d, Y') }}
                </td>
                <td class="text-right">${{ number_format((float)$billing->total_amount, 2) }}</td>
            </tr>

            <!-- Late Fee (if overdue) -->
            @if($billing->isOverdue())
            <tr>
                <td colspan="3" class="text-right"><strong>Late Fee (10%):</strong></td>
                <td class="text-right">${{ number_format((float)$billing->total_amount * 0.1, 2) }}</td>
            </tr>
            @endif

            <!-- Total -->
            <tr>
                <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                <td class="text-right">
                    <strong>${{ number_format((float)$billing->total_amount * ($billing->isOverdue() ? 1.1 : 1), 2) }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Instructions -->
    <div class="mb-4">
        <h4>Payment Instructions</h4>
        <p>
            <strong>Bank Transfer:</strong><br>
            Bank: Kenya Commercial Bank<br>
            Account Name: Dark Fibre Solutions Ltd.<br>
            Account Number: 1234567890<br>
            Branch: Nairobi Central<br>
        </p>
        <p>
            <strong>Mobile Money:</strong><br>
            Paybill: 123456<br>
            Account: {{ $billing->billing_number }}
        </p>
    </div>

    <!-- Terms and Conditions -->
    <div class="mb-4">
        <h4>Terms & Conditions</h4>
        <p>
            • Payment is due within 15 days of billing date<br>
            • Late payments may incur a 10% late fee<br>
            • Service may be suspended for accounts 30+ days overdue<br>
            • All amounts are in {{ $billing->currency }}<br>
            • For questions, contact billing@darkfibre.co.ke
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>
            Dark Fibre Solutions Ltd. |
            P.O. Box 12345-00100 Nairobi |
            Tel: +254 700 000 000 |
            Email: info@darkfibre.co.ke
        </p>
        <p>
            <small>
                This is an computer-generated document. No signature is required.<br>
                Generated on: {{ now()->format('M d, Y \a\t h:i A') }}
            </small>
        </p>
    </div>
</body>
</html>
