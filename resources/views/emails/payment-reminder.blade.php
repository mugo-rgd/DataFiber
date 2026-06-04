<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Reminder - Kenya Power</title>
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
            background-color: #0066B3;
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
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
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
            margin-top: 20px;
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
            <h2>Kenya Power</h2>
            <p>Payment Reminder</p>
        </div>

        <div class="content">
            <h3>Dear {{ $customer->name }},</h3>

            <p>This is a reminder that you have an outstanding balance on your account.</p>

            <div class="amount">
                Total Outstanding: ${{ number_format($totalDebt, 2) }}
            </div>

            @if($overdueDebt > 0)
                <div class="warning">
                    <strong>⚠️ URGENT:</strong> You have overdue payments of <strong>${{ number_format($overdueDebt, 2) }}</strong>.
                    Please make immediate payment to avoid service disconnection.
                </div>
            @else
                <div class="warning">
                    <strong>📢 NOTICE:</strong> Please settle your outstanding balance by the due date to avoid late fees and possible service interruption.
                </div>
            @endif

            <p><strong>Why this matters:</strong></p>
            <ul>
                <li>Late payments may result in service disconnection</li>
                <li>Reconnection fees may apply after disconnection</li>
                <li>Unpaid balances affect your credit standing</li>
            </ul>

            <p><strong>How to pay:</strong></p>
            <ul>
                <li>Online banking via our customer portal</li>
                <li>Mobile money (M-Pesa Paybill: 888888)</li>
                <li>Visit any Kenya Power branch</li>
                <li>Bank deposit - Cooperative Bank Account: 01112345678900</li>
            </ul>

            <a href="{{ url('/customer/billings') }}" class="button">View Your Bill Online</a>

            <p style="margin-top: 20px;">If you have already made payment, please disregard this notice.</p>

            <p>Thank you for your prompt attention to this matter.</p>

            <p>Best regards,<br>
            <strong>Kenya Power Billing Department</strong></p>
        </div>

        <div class="footer">
            <p>Kenya Power & Lighting Company PLC | Stima Plaza, Kolobot Road, Parklands | Nairobi, Kenya</p>
            <p>Customer Care: +254 711 111 111 | Email: customerservice@kplc.co.ke</p>
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
