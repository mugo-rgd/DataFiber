<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Payment Reminder</title>
    <style>
        /* Reset styles */
        body, table, td, div, p, a {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333333;
        }

        .invoice-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .invoice-box h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .invoice-details {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-details tr {
            border-bottom: 1px solid #dee2e6;
        }

        .invoice-details td {
            padding: 10px 0;
        }

        .invoice-details td:first-child {
            font-weight: bold;
            color: #495057;
        }

        .invoice-details td:last-child {
            text-align: right;
            color: #2c3e50;
        }

        .highlight-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }

        .highlight-box p {
            color: #856404;
            margin: 0;
        }

        .attachment-note {
            background-color: #e7f3fe;
            border: 1px solid #b3d7ff;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 25px 0;
        }

        .action-button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            flex: 1;
        }

        .print-button {
            display: inline-block;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            flex: 1;
        }

        .button-icon {
            margin-right: 8px;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 12px;
        }

        .contact-info {
            margin-top: 15px;
            font-size: 12px;
        }

        .contact-info a {
            color: #3498db;
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }

            .header {
                padding: 20px 15px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>DarkFibre CRM</h1>
            <p>Invoice Payment Reminder</p>
        </div>

        <div class="content">
            <p class="greeting">Dear {{ $customer->name ?? 'Valued Customer' }},</p>

            <p>This is a friendly reminder regarding your invoice payment.</p>

            <div class="invoice-box">
                <h3>Invoice Details</h3>
                <table class="invoice-details">
                    <tr>
                        <td>Invoice Number:</td>
                        <td><strong>{{ $billing->billing_number ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Invoice Date:</td>
                        <td>{{ \Carbon\Carbon::parse($billing->billing_date ?? now())->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td>Due Date:</td>
                        <td><strong>{{ $due_date_formatted }}</strong></td>
                    </tr>
                    <tr>
                        <td>Amount Due:</td>
                        <td><strong style="color: #e74c3c;">{{ $billing->currency ?? 'USD' }} {{ $total_amount_formatted }}</strong></td>
                    </tr>
                    <tr>
                        <td>Current Status:</td>
                        <td>
                            @if($billing->status == 'overdue')
                                <span style="color: #e74c3c; font-weight: bold;">⚠️ OVERDUE</span>
                            @else
                                <strong>{{ ucfirst($billing->status ?? 'pending') }}</strong>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Add attachment note -->
            <div class="attachment-note">
                <p><strong>📎 Invoice Attached</strong><br>
                A copy of invoice #{{ $billing->billing_number ?? 'N/A' }} is attached to this email as a PDF file.</p>
            </div>

            @if(in_array($billing->status ?? '', ['overdue', 'past_due']))
            <div class="highlight-box">
                <p><strong>Important:</strong> This invoice is now overdue. Please arrange payment as soon as possible to avoid any service interruptions.</p>
            </div>
            @endif

            <p>You can view, print, or download your invoice by clicking the buttons below:</p>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ $invoiceUrl }}" class="action-button">
                    <span class="button-icon">👁️</span> View Invoice Online
                </a>
                <a href="{{ $invoiceUrl }}/print" class="print-button">
                    <span class="button-icon">🖨️</span> Print Invoice
                </a>
            </div>

            <!-- Direct link for easy access -->
            <p style="text-align: center; font-size: 13px; margin: 15px 0;">
                <a href="{{ $invoiceUrl }}" style="color: #3498db;">{{ $invoiceUrl }}</a>
            </p>

            <p>If you have already made the payment, please disregard this reminder. It may take 24-48 hours for our system to update.</p>

            <p>Should you have any questions or require assistance with your payment, please don't hesitate to contact our billing department.</p>

            <p>Best regards,<br>
            <strong>DarkFibre CRM Billing Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated message from DarkFibre CRM. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} DarkFibre CRM. All rights reserved.</p>

            <div class="contact-info">
                <p>DarkFibre CRM | support@darkfibre-crm.figtreealliance.com</p>
                <p><a href="{{ url('/unsubscribe') }}">Unsubscribe from billing reminders</a></p>
            </div>
        </div>
    </div>
</body>
</html>
