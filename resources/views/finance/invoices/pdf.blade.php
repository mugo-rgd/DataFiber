<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-info {
            margin-bottom: 30px;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-overdue { background-color: #f8d7da; color: #721c24; }
        .status-draft { background-color: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>INVOICE</h1>
        <h2>#{{ $invoice->invoice_number }}</h2>
    </div>

    <!-- Company Information -->
    <div class="company-info">
        <table width="100%">
            <tr>
                <td width="50%">
                    <strong>From:</strong><br>
                    Your Company Name<br>
                    123 Business Street<br>
                    City, State 12345<br>
                    Phone: (123) 456-7890<br>
                    Email: billing@company.com
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>To:</strong><br>
                    @if($invoice->user)
                    {{ $invoice->user->name }}<br>
                    @if($invoice->user->company)
                    {{ $invoice->user->company }}<br>
                    @endif
                    {{ $invoice->user->email }}<br>
                    {{ $invoice->user->phone ?? 'N/A' }}
                    @else
                    Customer Information Not Available
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Invoice Details -->
    <div class="invoice-details">
        <table width="100%">
            <tr>
                <td width="33%">
                    <strong>Invoice Date:</strong><br>
                    {{ $invoice->invoice_date->format('F j, Y') }}
                </td>
                <td width="33%">
                    <strong>Due Date:</strong><br>
                    {{ $invoice->due_date->format('F j, Y') }}
                </td>
                <td width="33%">
                    <strong>Status:</strong><br>
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Line Items -->
    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th width="15%">Quantity</th>
                <th width="15%">Unit Price</th>
                <th width="15%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @if($invoice->lineItems && count($invoice->lineItems) > 0)
                @foreach($invoice->lineItems as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $invoice->currency }} {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            @else
            <tr>
                <td colspan="4" style="text-align: center;">
                    {{ $invoice->description }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Totals -->
    <div class="total-section">
        <table style="width: 300px; margin-left: auto;">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td>{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Tax:</strong></td>
                <td>{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            <tr style="border-top: 2px solid #333;">
                <td><strong>Total:</strong></td>
                <td><strong>{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</strong></td>
            </tr>
            @if($invoice->status === 'paid')
            <tr>
                <td><strong>Amount Paid:</strong></td>
                <td style="color: green;">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Balance Due:</strong></td>
                <td>{{ $invoice->currency }} 0.00</td>
            </tr>
            @else
            <tr>
                <td><strong>Balance Due:</strong></td>
                <td style="color: red;"><strong>{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</strong></td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Payment Terms -->
    @if($invoice->payment_terms)
    <div style="margin-top: 30px;">
        <strong>Payment Terms:</strong><br>
        {{ $invoice->payment_terms }}
    </div>
    @endif

    <!-- Notes -->
    @if($invoice->notes)
    <div style="margin-top: 20px;">
        <strong>Notes:</strong><br>
        {{ $invoice->notes }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>If you have any questions about this invoice, please contact our billing department.</p>
        <p>Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
