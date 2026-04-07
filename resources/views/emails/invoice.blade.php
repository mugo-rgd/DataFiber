<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $billing->billing_number }} - DarkFibre CRM</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; background: #f9f9f9; }
        .invoice-details { margin: 20px 0; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 5px; }
        .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .footer { margin-top: 20px; padding: 20px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Invoice {{ $billing->billing_number }}</h2>
        </div>

        <div class="content">
            <p>Dear {{ $billing->user->name ?? 'Customer' }},</p>

            <p>Please find attached your invoice <strong>{{ $billing->billing_number }}</strong>.</p>

            <div class="invoice-details">
                <h3>Invoice Summary:</h3>
                <p><strong>Billing Period:</strong> {{ \Carbon\Carbon::parse($billing->billing_date)->format('F j, Y') }}</p>
                <p><strong>Total Amount:</strong>
                    @if($billing->currency == 'USD')
                        ${{ number_format($billing->total_amount, 2) }}
                    @else
                        KSH {{ number_format($billing->total_amount, 2) }}
                    @endif
                </p>
                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($billing->due_date)->format('F j, Y') }}</p>
            </div>

            <p style="text-align: center;">
                <a href="{{ route('finance.billing.show', $billing->id) }}" class="button">View Online</a>
            </p>

            <p>For any questions, please contact our billing department.</p>
        </div>

        <div class="footer">
            <p>DarkFibre CRM | Enterprise Solutions</p>
            <p>This invoice is attached as a PDF file.</p>
        </div>
    </div>
</body>
</html>
