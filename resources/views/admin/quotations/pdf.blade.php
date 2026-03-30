<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation - {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-info { margin-bottom: 20px; }
        .quotation-details { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; }
        .footer { margin-top: 50px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>QUOTATION</h1>
        <p>Quotation Number: {{ $quotation->quotation_number }}</p>
    </div>

    <div class="company-info">
        <h3>Dark Fibre CRM</h3>
        <p>Your Company Address</p>
        <p>Phone: +1234567890 | Email: info@darkfibre-crm.com</p>
    </div>

    <div class="quotation-details">
        <h3>Customer Information</h3>
        <p><strong>Customer:</strong> {{ $quotation->designRequest->customer->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $quotation->designRequest->customer->email ?? 'N/A' }}</p>
    </div>

    <div class="scope-of-work">
        <h3>Scope of Work</h3>
        <p>{{ $quotation->scope_of_work }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Technology</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->line_items as $item)
            <tr>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['technology'] }}</td>
                <td>{{ $item['quantity'] }} {{ $item['unit'] }}</td>
                <td>${{ number_format($item['unit_price'], 2) }}</td>
                <td>${{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Subtotal:</td>
                <td>${{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Tax ({{ ($quotation->tax_rate * 100) }}%):</td>
                <td>${{ number_format($quotation->tax_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Amount:</td>
                <td>${{ number_format($quotation->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="validity">
        <p><strong>Valid Until:</strong> {{ $quotation->valid_until->format('F d, Y') }}</p>
    </div>

    <div class="terms">
        <h3>Terms & Conditions</h3>
        <p>{{ $quotation->terms_and_conditions }}</p>
    </div>

    <div class="footer">
        <p>Generated on: {{ now()->format('F d, Y') }}</p>
    </div>
</body>
</html>
