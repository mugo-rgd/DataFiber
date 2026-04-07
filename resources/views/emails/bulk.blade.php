<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }} - DarkFibre CRM</title>
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
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            padding: 30px 20px;
            background: #ffffff;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $subject }}</h2>
            <p>DarkFibre CRM - Enterprise Solutions</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $customer->name ?? 'Customer' }}</strong>,</p>

            <div>
                {!! nl2br(e($messageContent)) !!}
            </div>

            <p>Best regards,<br>
            <strong>DarkFibre CRM Team</strong></p>
        </div>

        <div class="footer">
            <p>DarkFibre CRM | Enterprise Solutions</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
