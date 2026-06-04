<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Reminder - {{ $invoiceNumber }} - Kenya Power</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .invoice-box {
            background: white;
            border: 2px solid #0066B3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #dc3545;
            text-align: center;
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0066B3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>URGENT: Payment Required</h2>
            <p>Invoice {{ $invoiceNumber }}</p>
        </div>

        <div class="content">
            <h3>Dear {{ $customer->name }},</h3>

            <p>This is an urgent reminder regarding your unpaid invoice.</p>

            <div class="invoice-box">
                <h4 style="margin-bottom: 15px;">Invoice Details:</h4>
                <table style="width: 100%;">
                    <tr>
                        <td><strong>Invoice Number:</strong></td>
                        <td>{{ $invoiceNumber }}</td>
                    </tr>
                    <tr>
                        <td><strong>Amount Due:</strong></td>
                        <td style="color: #dc3545; font-weight: bold;">${{ number_format($amountDue, 2) }}</td>
                    </tr>
                    @if(isset($dueDate))
                    <tr>
                        <td><strong>Due Date:</strong></td>
                        <td>{{ $dueDate }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="amount">
                Due: ${{ number_format($amountDue, 2) }}
            </div>

            <div class="warning">
                <strong>⚠️ FINAL NOTICE:</strong> Your service may be <strong style="color: #dc3545;">DISCONNECTED</strong> if payment is not received within 7 days.
                <br><br>
                A reconnection fee of $50 will apply after disconnection.
            </div>

            <p><strong>Immediate Payment Options:</strong></p>
            <ul>
                <li><strong>M-Pesa Paybill:</strong> 888888 | Account: {{ $invoiceNumber }}</li>
                <li><strong>Online Banking:</strong> Kenya Power Customer Portal</li>
                <li><strong>Bank Deposit:</strong> Cooperative Bank - Account: 01112345678900</li>
                <li><strong>Customer Service Centers:</strong> Nationwide branches</li>
            </ul>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/customer/billings') }}" class="button">Pay Online Now</a>
                <a href="{{ url('/customer/contact') }}" class="button" style="background-color: #6c757d;">Contact Support</a>
            </div>

            <p style="margin-top: 20px;"><strong>After payment:</strong> Please allow 24 hours for payment processing. If you have already paid, disregard this notice.</p>

            <p>For immediate assistance, call our 24/7 Customer Care: <strong>+254 711 111 111</strong></p>

            <p>Thank you for your immediate attention to this matter.</p>

            <p>Sincerely,<br>
            <strong>Kenya Power Credit Control Department</strong></p>
        </div>

        <div class="footer">
            <p>Kenya Power & Lighting Company PLC | Stima Plaza, Kolobot Road, Parklands | Nairobi, Kenya</p>
            <p>This is an automated message from our billing system.</p>
        </div>
    </div>
</body>
</html>
