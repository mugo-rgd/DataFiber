<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-info { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-section { margin-top: 20px; float: right; width: 300px; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <h2>{{ $invoice->invoice_number }}</h2>
    </div>

    <div class="invoice-info">
        <table width="100%">
            <tr>
                <td width="50%">
                    <strong>Billed To:</strong><br>
                    {{ $invoice->user->name }}<br>
                    {{ $invoice->user->email }}
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}<br>
                    <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                    <strong>Status:</strong> {{ strtoupper($invoice->status) }}
                </td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th width="100">Quantity</th>
                <th width="100">Unit Price</th>
                <th width="100">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->line_items as $item)
            <tr>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td class="text-right">{{ number_format($item['unit_price'], 2) }} {{ $invoice->currency }}</td>
                <td class="text-right">{{ number_format($item['total'], 2) }} {{ $invoice->currency }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table width="100%">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td class="text-right">{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
            <tr style="font-weight: bold; border-top: 2px solid #000;">
                <td>Total:</td>
                <td class="text-right">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($invoice->notes)
    <div class="notes" style="margin-top: 30px;">
        <strong>Notes:</strong><br>
        {{ $invoice->notes }}
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>{{ $invoice->payment_terms }}</p>
    </div>
</body>
</html>
