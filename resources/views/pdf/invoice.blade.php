<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $billing->billing_number }}</title>
    <style>
        @page {
            margin: 50px 30px;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .section {
            margin-bottom: 25px;
        }
        .billing-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .billing-table th {
            background-color: #3B82F6;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .billing-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .billing-table .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .bank-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .notes {
            margin-top: 30px;
            font-style: italic;
            color: #666;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft { background-color: #6B7280; color: white; }
        .status-sent { background-color: #F59E0B; color: white; }
        .status-paid { background-color: #10B981; color: white; }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="company-info">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Company Logo">
            @endif
            <h1 style="margin: 0; color: #3B82F6;">{{ $company['name'] }}</h1>
            <p style="margin: 5px 0;">
                {{ $company['address'] }}<br>
                {{ $company['city'] }}, {{ $company['zip'] }}<br>
                Phone: {{ $company['phone'] }}<br>
                Email: {{ $company['email'] }}<br>
                Tax ID: {{ $company['tax_id'] }}
            </p>
        </div>

        <div class="invoice-info">
            <h2 style="margin: 0; color: #3B82F6;">INVOICE</h2>
            <p style="margin: 5px 0;">
                <strong>Invoice Number:</strong> {{ $billing->billing_number }}<br>
                <strong>Invoice Date:</strong> {{ $billing->billing_date->format('F d, Y') }}<br>
                <strong>Due Date:</strong> {{ $billing->due_date->format('F d, Y') }}<br>
                <strong>Status:</strong>
                <span class="status-badge status-{{ $billing->status }}">
                    {{ strtoupper($billing->status) }}
                </span>
            </p>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="section clearfix">
        <div style="float: left; width: 48%;">
            <h3 style="color: #3B82F6; margin-bottom: 10px;">Bill To:</h3>
            <p style="margin: 0;">
                <strong>{{ $customer->name ?? 'Customer Name' }}</strong><br>
                {{ $customer->address ?? 'Customer Address' }}<br>
                {{ $customer->city ?? 'City' }}, {{ $customer->zip_code ?? 'ZIP' }}<br>
                {{ $customer->country ?? 'Country' }}<br>
                Email: {{ $customer->email ?? 'customer@example.com' }}<br>
                Phone: {{ $customer->phone ?? 'N/A' }}
            </p>
        </div>

        <div style="float: right; width: 48%;">
            <h3 style="color: #3B82F6; margin-bottom: 10px;">Service Details:</h3>
            <p style="margin: 0;">
                <strong>Lease:</strong> {{ $lease->lease_number }}<br>
                <strong>Service Type:</strong> {{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}<br>
                <strong>Billing Cycle:</strong> {{ ucfirst($billing->billing_cycle) }}<br>
                <strong>Period:</strong> {{ $billing->period_start->format('M d, Y') }} - {{ $billing->period_end->format('M d, Y') }}
            </p>
        </div>
    </div>

    <div class="clearfix"></div>

    <table class="billing-table">
        <thead>
            <tr>
                <th width="60%">Description</th>
                <th width="15%">Quantity</th>
                <th width="15%">Unit Price</th>
                <th width="10%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }} Service</strong><br>
                    <small>
                        Route: {{ $lease->start_location }} to {{ $lease->end_location }}<br>
                        @if($lease->bandwidth)
                        Bandwidth: {{ $lease->bandwidth }}<br>
                        @endif
                        @if($lease->distance_km)
                        Distance: {{ $lease->distance_km }} km<br>
                        @endif
                        Technology: {{ ucfirst(str_replace('_', ' ', $lease->technology ?? 'N/A')) }}
                    </small>
                </td>
                <td>1 {{ $billing->billing_cycle }}</td>
                <td>{{ $billing->currency }} {{ number_format($billing->amount, 2) }}</td>
                <td>{{ $billing->currency }} {{ number_format($billing->amount, 2) }}</td>
            </tr>

            @if($isFirstBilling && $lease->installation_fee > 0)
            <tr>
                <td>One-time Installation Fee</td>
                <td>1</td>
                <td>{{ $lease->currency }} {{ number_format($lease->installation_fee, 2) }}</td>
                <td>{{ $lease->currency }} {{ number_format($lease->installation_fee, 2) }}</td>
            </tr>
            @endif

            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>Total Amount:</strong></td>
                <td><strong>{{ $billing->currency }} {{ number_format($totalAmount, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="bank-details">
        <h4 style="margin: 0 0 10px 0; color: #3B82F6;">Payment Information</h4>
        <p style="margin: 5px 0;">
            <strong>Bank:</strong> {{ $bankDetails['bank_name'] }}<br>
            <strong>Account Name:</strong> {{ $bankDetails['account_name'] }}<br>
            <strong>Account Number:</strong> {{ $bankDetails['account_number'] }}<br>
            @if($bankDetails['routing_number'])
            <strong>Routing Number:</strong> {{ $bankDetails['routing_number'] }}<br>
            @endif
            @if($bankDetails['iban'])
            <strong>IBAN:</strong> {{ $bankDetails['iban'] }}<br>
            @endif
            @if($bankDetails['swift'])
            <strong>SWIFT:</strong> {{ $bankDetails['swift'] }}<br>
            @endif
        </p>
    </div>

    <div class="notes">
        <p><strong>Notes:</strong></p>
        <ul>
            <li>Please pay within 30 days of invoice date</li>
            <li>Late payments may be subject to fees</li>
            <li>For questions about this invoice, contact {{ $company['email'] }}</li>
            <li>Please include invoice number with your payment</li>
        </ul>
    </div>

    <div class="footer">
        <p style="text-align: center; margin: 0;">
            Thank you for your business!<br>
            {{ $company['name'] }} | {{ $company['website'] }} | {{ $company['email'] }}<br>
            This is a computer-generated document. No signature is required.
        </p>
    </div>
</body>
</html>
