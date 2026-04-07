<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt - DarkFibre CRM</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; background: #f9f9f9; }
        .receipt-details { margin: 20px 0; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 5px; }
        .amount { font-size: 24px; font-weight: bold; color: #28a745; }
        .footer { margin-top: 20px; padding: 20px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payment Receipt</h2>
        </div>

        <div class="content">
            <p>Dear {{ $transaction->user->name ?? 'Customer' }},</p>

            <p>Thank you for your payment. Your transaction has been successfully processed.</p>

            <div class="receipt-details">
                <h3>Payment Details:</h3>
                <p><strong>Reference Number:</strong> {{ $transaction->reference_number ?? 'N/A' }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('F j, Y') }}</p>
                <p><strong>Amount:</strong> <span class="amount">
                    @if($transaction->currency == 'USD')
                        ${{ number_format($transaction->amount, 2) }}
                    @else
                        KSH {{ number_format($transaction->amount, 2) }}
                    @endif
                </span></p>
                <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $transaction->payment_method ?? 'N/A')) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($transaction->status ?? 'Completed') }}</p>
            </div>

            <p>Thank you for choosing DarkFibre CRM!</p>
        </div>

        <div class="footer">
            <p>DarkFibre CRM | Enterprise Solutions</p>
            <p>Keep this receipt for your records.</p>
        </div>
    </div>
</body>
</html>
