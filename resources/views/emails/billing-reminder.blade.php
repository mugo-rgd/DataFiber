<!DOCTYPE html>
<html>
<head>
    <title>Payment Reminder - DarkFibre CRM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
            background: #ffffff;
        }
        .invoice-details {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        .invoice-details h3 {
            margin-top: 0;
            color: #495057;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            color: #495057;
        }
        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
        }
        .overdue {
            color: #dc3545;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
        }
        .payment-methods {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .payment-methods h4 {
            margin-top: 0;
            color: #495057;
        }
        .payment-methods ul {
            margin: 0;
            padding-left: 20px;
        }
        .footer {
            margin-top: 20px;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 0 0 10px 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #6c757d;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payment Reminder</h2>
            <p>DarkFibre CRM - Enterprise Solutions</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $billing->user->name ?? 'Customer' }}</strong>,</p>

            <p>This is a friendly reminder that invoice <strong>{{ $billing->billing_number }}</strong> is due for payment.</p>

            <div class="invoice-details">
                <h3>Invoice Details:</h3>
                <div class="detail-row">
                    <span class="detail-label">Invoice Number:</span>
                    <span class="detail-value">{{ $billing->billing_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Invoice Date:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($billing->billing_date)->format('F j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Due Date:</span>
                    <span class="detail-value">
                        @if(\Carbon\Carbon::parse($billing->due_date)->isPast())
                            <span class="overdue">{{ \Carbon\Carbon::parse($billing->due_date)->format('F j, Y') }} (Overdue)</span>
                        @else
                            {{ \Carbon\Carbon::parse($billing->due_date)->format('F j, Y') }}
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount Due:</span>
                    <span class="amount">
                        @if($billing->currency == 'USD')
                            ${{ number_format($billing->total_amount - ($billing->paid_amount ?? 0), 2) }}
                        @else
                            KSH {{ number_format($billing->total_amount - ($billing->paid_amount ?? 0), 2) }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="payment-methods">
                <h4>Payment Methods:</h4>
                <ul>
                    <li><strong>Bank Transfer:</strong> Cooperative Bank - Account: 0112000000000</li>
                    <li><strong>M-Pesa:</strong> Paybill 123456 - Account: {{ $billing->billing_number }}</li>
                    <li><strong>Credit Card:</strong> Available on our payment portal</li>
                </ul>
            </div>

            @if(\Carbon\Carbon::parse($billing->due_date)->isPast())
                <div class="warning">
                    <strong>⚠️ Important:</strong> Your payment is now overdue. Please make the payment immediately to avoid service interruption.
                </div>
            @endif

            <div class="text-center">
                <a href="{{ $invoiceUrl }}" class="button">View & Pay Invoice</a>
            </div>

            <p>If you have already made the payment, please disregard this message.</p>

            <p>Thank you for your business!</p>

            <p>Best regards,<br>
            <strong>DarkFibre CRM Team</strong></p>
        </div>

        <div class="footer">
            <p>DarkFibre CRM | Enterprise Solutions</p>
            <p>Email: support@darkfibre-crm.com | Phone: +254 XXX XXX XXX</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
