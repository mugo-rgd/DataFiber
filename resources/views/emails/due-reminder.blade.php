<!DOCTYPE html>
<html>
<head>
    <title>Payment Due Soon - DarkFibre CRM</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ffc107; color: #333; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; background: #f9f9f9; }
        .invoice-item { margin: 15px 0; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 5px; }
        .footer { margin-top: 20px; padding: 20px; text-align: center; font-size: 12px; color: #777; }
        .button { display: inline-block; padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payment Due Soon</h2>
        </div>

        <div class="content">
            <p>Dear {{ $customer->name ?? 'Customer' }},</p>

            <p>This is a reminder that you have invoice(s) due for payment within the next 3 days.</p>

            @foreach($billings as $billing)
            <div class="invoice-item">
                <p><strong>Invoice:</strong> {{ $billing->billing_number }}</p>
                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($billing->due_date)->format('F j, Y') }}</p>
                <p><strong>Amount:</strong>
                    @if($billing->currency == 'USD')
                        ${{ number_format($billing->total_amount - ($billing->paid_amount ?? 0), 2) }}
                    @else
                        KSH {{ number_format($billing->total_amount - ($billing->paid_amount ?? 0), 2) }}
                    @endif
                </p>
                <p><a href="{{ route('finance.billing.show', $billing->id) }}">View Invoice</a></p>
            </div>
            @endforeach

            <p style="text-align: center;">
                <a href="{{ route('finance.billing.index') }}" class="button">View All Invoices</a>
            </p>
        </div>

        <div class="footer">
            <p>DarkFibre CRM | Enterprise Solutions</p>
            <p>Please ensure timely payment to avoid service interruption.</p>
        </div>
    </div>
</body>
</html>
