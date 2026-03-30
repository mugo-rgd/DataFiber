{{-- resources/views/customer-portal/statements/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statement - {{ $statementNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 10px;
            color: #7f8c8d;
        }
        .statement-title {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            color: #3498db;
            text-align: center;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-row {
            margin: 6px 0;
            font-size: 11px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 130px;
            color: #2c3e50;
        }
        .value {
            color: #34495e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        th {
            background-color: #3498db;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .balance-row {
            font-weight: bold;
            background-color: #e8f4f8 !important;
        }
        .summary-box {
            margin: 20px 0;
            padding: 12px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
        }
        .amount-positive {
            color: #27ae60;
            font-weight: bold;
        }
        .amount-negative {
            color: #e74c3c;
            font-weight: bold;
        }
        .watermark {
            position: fixed;
            bottom: 30px;
            right: 30px;
            opacity: 0.1;
            font-size: 50px;
            transform: rotate(-45deg);
            z-index: -1;
        }
        .currency-badge {
            display: inline-block;
            padding: 2px 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            margin-left: 5px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            font-size: 10px;
            padding-top: 5px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    @php
        // Determine the primary currency from transactions
        $primaryCurrency = 'USD'; // Default
        $currencies = [];

        if (isset($transactions) && $transactions->count() > 0) {
            foreach ($transactions as $trans) {
                if (!empty($trans->currency)) {
                    $currencies[$trans->currency] = true;
                }
            }

            // If there's only one currency type, use that
            if (count($currencies) === 1) {
                $primaryCurrency = array_key_first($currencies);
            }
        }

        // Calculate totals
        $totalDebits = $transactions->where('direction', 'out')->sum('amount');
        $totalCredits = $transactions->where('direction', 'in')->sum('amount');
        $closingBalance = $openingBalance - $totalDebits + $totalCredits;

        // Format currency with symbol
        function formatCurrency($amount, $currency = 'USD') {
            if ($currency === 'USD' || $currency === 'USD') {
                return '$' . number_format($amount, 2);
            } elseif ($currency === 'KSH' || $currency === 'KES') {
                return 'KSh ' . number_format($amount, 2);
            } else {
                return $currency . ' ' . number_format($amount, 2);
            }
        }
    @endphp

    <div class="watermark">{{ config('app.name') }}</div>

    <div class="header">
        <div class="company-name">{{ config('app.name', 'DarkFibre CRM') }}</div>
        <div class="company-details">
            Kenya Power and Lighting PLC<br>
            P.O Box 30099 - 00100 Nairobi, Kenya<br>
            Phone: (+254) XXX-XXX-XXX | Email: billing@darkfibre.com
        </div>
        <div class="statement-title">CUSTOMER STATEMENT</div>
        @if(count($currencies) > 1)
            <div class="currency-badge">Multi-Currency Statement</div>
        @endif
    </div>

    <div class="info-section">
        <div class="row">
            <div class="info-row">
                <span class="label">Statement Number:</span>
                <span class="value">{{ $statementNumber }}</span>
            </div>
            <div class="info-row">
                <span class="label">Date Range:</span>
                <span class="value">{{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Generated On:</span>
                <span class="value">{{ now()->format('F d, Y H:i:s') }}</span>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h4 style="margin-top: 0; color: #2c3e50;">CUSTOMER INFORMATION</h4>
        <div class="info-row"><span class="label">Customer Name:</span> {{ $customer->name }}</div>
        @if($customer->company_name)
        <div class="info-row"><span class="label">Company:</span> {{ $customer->company_name }}</div>
        @endif
        <div class="info-row"><span class="label">Email:</span> {{ $customer->email }}</div>
        @if($customer->phone)
        <div class="info-row"><span class="label">Phone:</span> {{ $customer->phone }}</div>
        @endif
    </div>

    <div class="summary-box">
        <h4 style="margin-top: 0; color: #2c3e50;">ACCOUNT SUMMARY</h4>
        <table style="width: 100%; margin: 0;">
            <tr>
                <td style="border: none; width: 50%;"><strong>Opening Balance:</strong></td>
                <td style="border: none; text-align: right;">{{ formatCurrency($openingBalance, $primaryCurrency) }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Total Debits (Payments Out):</strong></td>
                <td style="border: none; text-align: right; color: #e74c3c;">{{ formatCurrency($totalDebits, $primaryCurrency) }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Total Credits (Payments In):</strong></td>
                <td style="border: none; text-align: right; color: #27ae60;">{{ formatCurrency($totalCredits, $primaryCurrency) }}</td>
            </tr>
            <tr style="font-weight: bold; background-color: #e8f4f8;">
                <td style="border: none;"><strong>Closing Balance:</strong></td>
                <td style="border: none; text-align: right; {{ $closingBalance >= 0 ? 'color: #27ae60;' : 'color: #e74c3c;' }}">
                    <strong>{{ formatCurrency($closingBalance, $primaryCurrency) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <h4 style="color: #2c3e50;">TRANSACTION DETAILS</h4>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Reference</th>
                <th>Currency</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6" class="text-right"><strong>Opening Balance:</strong></td>
                <td class="text-right"><strong>{{ formatCurrency($openingBalance, $primaryCurrency) }}</strong></td>
            </tr>

            @forelse($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->reference ?? '-' }}</td>
                <td>{{ $transaction->currency ?? 'USD' }}</td>
                <td class="text-right">{{ $transaction->direction == 'out' ? formatCurrency($transaction->amount, $transaction->currency ?? 'USD') : '-' }}</td>
                <td class="text-right">{{ $transaction->direction == 'in' ? formatCurrency($transaction->amount, $transaction->currency ?? 'USD') : '-' }}</td>
                <td class="text-right">{{ formatCurrency($transaction->balance, $transaction->currency ?? 'USD') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No transactions found for this period</td>
            </tr>
            @endforelse

            <tr class="balance-row">
                <td colspan="6" class="text-right"><strong>Closing Balance:</strong></td>
                <td class="text-right"><strong>{{ formatCurrency($closingBalance, $primaryCurrency) }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if($closingBalance > 0)
    <div style="margin-top: 20px; padding: 10px; background-color: #fef9e7; border-left: 4px solid #f39c12;">
        <strong>Payment Due:</strong> Please pay the outstanding balance of <strong>{{ formatCurrency($closingBalance, $primaryCurrency) }}</strong>.
    </div>
    @elseif($closingBalance < 0)
    <div style="margin-top: 20px; padding: 10px; background-color: #e8f5e9; border-left: 4px solid #27ae60;">
        <strong>Credit Balance:</strong> Your account has a credit balance of <strong>{{ formatCurrency(abs($closingBalance), $primaryCurrency) }}</strong>.
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated statement. No signature is required.</p>
        <p>For questions regarding this statement, please contact our billing department.</p>
        <div class="signature-line">Authorized Signature</div>
    </div>
</body>
</html>
