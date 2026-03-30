{{-- resources/views/emails/quotations/sent-to-customer.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quotation #{{ $quotation->quotation_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .quotation-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Quotation Available</h1>
        <p>Quotation #{{ $quotation->quotation_number }}</p>
    </div>

    <div class="content">
        <h2>Dear {{ $quotation->customer->name }},</h2>

        <p>We are pleased to provide you with a quotation for your requested services.</p>

        <div class="quotation-details">
            <h3>Quotation Details:</h3>
            <p><strong>Quotation Number:</strong> {{ $quotation->quotation_number }}</p>
            <p><strong>Title:</strong> {{ $quotation->title }}</p>
            <p><strong>Valid Until:</strong> {{ \Carbon\Carbon::parse($quotation->valid_until)->format('F d, Y') }}</p>
            <p><strong>Total Amount:</strong> KES {{ number_format($quotation->total_amount, 2) }}</p>

            @if($quotation->designRequest)
            <p><strong>Related to Design Request:</strong> {{ $quotation->designRequest->request_number }}</p>
            @endif
        </div>

        <p>To view the complete quotation details and accept the quotation, please click the button below:</p>

        <div style="text-align: center;">
            <a href="{{ route('customer.quotations.show', $quotation->id) }}" class="button">
                View Quotation
            </a>
        </div>

        <p>If you have any questions or need clarification on any items, please don't hesitate to contact us.</p>

        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
