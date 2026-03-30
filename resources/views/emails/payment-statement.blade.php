{{-- resources/views/emails/payment-statement.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            background: #f9f9f9;
            padding: 30px 20px;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 10px 10px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .statement-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .statement-details h3 {
            margin-top: 0;
            color: #764ba2;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .info-item {
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .info-item .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            padding: 20px;
            background: #e8f5e9;
            border-radius: 8px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn.secondary {
            background: #6c757d;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
            color: #666;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .summary-table th,
        .summary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .summary-table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .text-success {
            color: #28a745;
        }
        .text-danger {
            color: #dc3545;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Statement</h1>
            <p>Statement #{{ $statement->statement_number ?? $statement->number }}</p>
        </div>

        <div class="content">
            <div class="greeting">
                Dear {{ $customer->name ?? $statement->customer->name ?? 'Valued Customer' }},
            </div>

            <p>Please find attached your payment statement for the period:</p>

            <div class="statement-details">
                <h3>Statement Summary</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Statement Number</div>
                        <div class="value">{{ $statement->statement_number ?? $statement->number }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Date</div>
                        <div class="value">{{ $statement->date ?? now()->format('Y-m-d') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Period</div>
                        <div class="value">
                            {{ $statement->period_start ?? $statement->period }} to
                            {{ $statement->period_end ?? $statement->period }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="label">Currency</div>
                        <div class="value">
                            @php
                                $currency = $statement->currency ?? 'USD';
                            @endphp
                            @if($currency == 'USD')
                                💵 USD (US Dollar)
                            @else
                                💰 KSH (Kenyan Shilling)
                            @endif
                        </div>
                    </div>
                </div>

                <table class="summary-table">
                    <tr>
                        <th>Opening Balance</th>
                        <td class="text-end">
                            @php
                                $currency = $statement->currency ?? 'USD';
                                $opening = $statement->opening_balance ?? 0;
                            @endphp
                            @if($currency == 'USD')
                                ${{ number_format($opening, 2) }}
                            @else
                                KSh {{ number_format($opening, 2) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Closing Balance</th>
                        <td class="text-end {{ ($statement->closing_balance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            @php
                                $closing = $statement->closing_balance ?? 0;
                            @endphp
                            @if($currency == 'USD')
                                ${{ number_format($closing, 2) }}
                            @else
                                KSh {{ number_format($closing, 2) }}
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="amount">
                    @if(($statement->closing_balance ?? 0) >= 0)
                        Amount Due:
                    @else
                        Credit Balance:
                    @endif
                    @php
                        $amount = abs($statement->closing_balance ?? 0);
                    @endphp
                    @if($currency == 'USD')
                        ${{ number_format($amount, 2) }}
                    @else
                        KSh {{ number_format($amount, 2) }}
                    @endif
                </div>

                @if(isset($statement->status))
                <div style="text-align: center; margin: 20px 0;">
                    <span class="badge badge-{{ $statement->status == 'paid' ? 'success' : ($statement->status == 'overdue' ? 'danger' : 'info') }}">
                        Status: {{ ucfirst($statement->status) }}
                    </span>
                </div>
                @endif
            </div>

            @if(isset($transactions) && count($transactions) > 0)
            <div class="statement-details">
                <h3>Recent Transactions</h3>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date ?? $transaction->created_at->format('Y-m-d') }}</td>
                            <td>{{ $transaction->description ?? $transaction->type }}</td>
                            <td class="text-end">
                                @if($currency == 'USD')
                                    ${{ number_format($transaction->amount ?? 0, 2) }}
                                @else
                                    KSh {{ number_format($transaction->amount ?? 0, 2) }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('statements.download', $statement->id) }}" class="btn">
                    📥 Download Statement (PDF)
                </a>
                <a href="{{ route('customer.statements', $customer->id ?? $statement->customer_id) }}" class="btn secondary">
                    📊 View Online
                </a>
            </div>

            <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 0; color: #856404;">
                    <strong>📌 Note:</strong> This is an auto-generated statement. For any queries regarding this statement,
                    please contact our support team.
                </p>
            </div>

            <div class="footer">
                <p>
                    <strong>Company Name</strong><br>
                    Address Line 1<br>
                    Address Line 2<br>
                    Phone: +254 XXX XXX XXX<br>
                    Email: support@company.com
                </p>
                <p style="font-size: 12px; color: #999; margin-top: 20px;">
                    This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed.
                </p>
                <p style="font-size: 12px; color: #999;">
                    &copy; {{ date('Y') }} Company Name. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
