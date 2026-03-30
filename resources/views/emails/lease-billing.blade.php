<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $billing->billing_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { border-bottom: 2px solid #3B82F6; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice {{ $billing->billing_number }}</h1>
        <p><strong>Date:</strong> {{ $billingDate ? $billingDate->format('F d, Y') : 'N/A' }}</p>
    </div>

    <div>
        <h3>Bill To:</h3>
        <p>
            <strong>{{ $customer->name }}</strong><br>
            {{ $customer->email }}
        </p>
    </div>

    <div class="invoice-details">
        <h3>Service Details:</h3>
        <p>
            <strong>Lease:</strong> {{ $lease->lease_number }} - {{ $lease->title }}<br>
            <strong>Service Type:</strong> {{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}<br>
            <strong>Route:</strong> {{ $lease->start_location }} to {{ $lease->end_location }}<br>
            <strong>Billing Period:</strong>
            {{ $periodStart ? $periodStart->format('M d, Y') : 'N/A' }} to {{ $periodEnd ? $periodEnd->format('M d, Y') : 'N/A' }}<br>
            <strong>Due Date:</strong> {{ $dueDate ? $dueDate->format('F d, Y') : 'N/A' }}
        </p>
    </div>

    <div>
        <h3>Amount Due:</h3>
        <p style="font-size: 18px; font-weight: bold; color: #3B82F6;">
            {{ $billing->currency }} {{ number_format($billing->amount, 2) }}
        </p>
        @if($isFirstBilling && $lease->installation_fee > 0)
        <p><small>Includes one-time installation fee of {{ $lease->currency }} {{ number_format($lease->installation_fee, 2) }}</small></p>
        @endif
    </div>

    <div class="footer">
        <p>
            Please find the detailed invoice attached as a PDF.<br>
            Payment is due within 30 days. Late payments may be subject to fees.<br><br>
            Thank you for your business!<br>
            If you have any questions, please contact our billing department.
        </p>
    </div>
</body>
</html>
