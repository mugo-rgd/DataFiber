<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $billing->billing_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .container { max-width: 100%; margin: 0 auto; padding: 20px; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #4e73df; padding-bottom: 15px; }
        .company-details { float: left; width: 50%; }
        .invoice-details { float: right; width: 40%; text-align: right; }
        .clearfix::after { content: ""; clear: both; display: table; }
        h1 { color: #4e73df; margin: 0 0 10px 0; font-size: 24px; }
        h2 { color: #4e73df; margin: 0 0 15px 0; font-size: 20px; }
        h3 { color: #333; margin: 0 0 10px 0; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background-color: #4e73df; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .total-row { background-color: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 10px; color: #6c757d; text-align: center; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; }
        .bg-success { background-color: #1cc88a; color: white; }
        .bg-warning { background-color: #f6c23e; color: white; }
        .bg-danger { background-color: #e74a3b; color: white; }
        .bg-secondary { background-color: #858796; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header clearfix">
            <div class="company-details">
                <h1>Kenya Power & Lighting Co. Ltd</h1>
                <div>Dark Fibre Services Division</div>
                <div>P.O. Box 30099 - 00100</div>
                <div>Nairobi, Kenya</div>
                <div>Tel: +254 20 320 1000</div>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <div><strong>Invoice #:</strong> {{ $billing->billing_number }}</div>
                <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($billing->billing_date)->format('d M Y') }}</div>
                <div><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($billing->due_date)->format('d M Y') }}</div>
                <div><strong>Status:</strong>
                    <span class="badge bg-{{ $billing->status === 'paid' ? 'success' : ($billing->due_date < now() ? 'danger' : 'warning') }}">
                        {{ ucfirst($billing->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div style="margin-bottom: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
            <h3>Bill To:</h3>
            <div style="float: left; width: 60%;">
                <div><strong>{{ $billing->user->name }}</strong></div>
                <div>{{ $billing->user->email }}</div>
                @if($billing->user->phone)<div>Tel: {{ $billing->user->phone }}</div>@endif
            </div>
            <div style="float: right; width: 35%;">
                <div><strong>Customer ID:</strong> {{ $billing->user_id }}</div>
                @if($billing->kra_pin)<div><strong>KRA PIN:</strong> {{ $billing->kra_pin }}</div>@endif
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Line Items -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lease</th>
                    <th>Description</th>
                    <th>Period</th>
                    <th class="text-end">Amount ({{ $billing->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billing->lineItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->lease->lease_number ?? 'N/A' }}</td>
                    <td>{{ $item->description ?? 'Dark Fibre Service' }}</td>
                    <td>
                        @if($item->period_start && $item->period_end)
                            {{ \Carbon\Carbon::parse($item->period_start)->format('d/m/Y') }} -
                            {{ \Carbon\Carbon::parse($item->period_end)->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Subtotal:</th>
                    <th class="text-end">{{ number_format($billing->total_amount, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-end">VAT (16%):</th>
                    <th class="text-end">{{ number_format($billing->total_amount * 0.16, 2) }}</th>
                </tr>
                <tr class="total-row">
                    <th colspan="4" class="text-end">Total Due:</th>
                    <th class="text-end">{{ number_format($billing->total_amount * 1.16, 2) }} {{ $billing->currency }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- KRA Information -->
        @if($billing->tevin_control_code)
        <div style="margin-top: 30px; padding: 15px; background-color: #d4edda; border-radius: 5px;">
            <p><strong>KRA Control Code:</strong> {{ $billing->tevin_control_code }}</p>
            @if($billing->tevin_qr_code)
            <p><strong>QR Code:</strong> {{ $billing->tevin_qr_code }}</p>
            @endif
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated invoice. No signature is required.</p>
            <p>For inquiries, contact billing@kplc.co.ke or call +254 20 320 2000</p>
            <p>Invoice #{{ $billing->billing_number }} | Generated on {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
