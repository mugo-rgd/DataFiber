
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Statement - <?php echo e($statement->statement_number); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 11px;
            color: #7f8c8d;
        }
        .statement-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            color: #3498db;
            text-align: center;
        }
        .info-section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-row {
            margin: 8px 0;
            font-size: 12px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
            color: #2c3e50;
        }
        .value {
            color: #34495e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        th {
            background-color: #3498db;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #ddd;
            padding: 8px;
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
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
        }
        .amount-positive {
            color: #27ae60;
        }
        .amount-negative {
            color: #e74c3c;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 250px;
            text-align: center;
            font-size: 11px;
            padding-top: 5px;
        }
        .watermark {
            position: fixed;
            bottom: 50px;
            right: 50px;
            opacity: 0.1;
            font-size: 60px;
            transform: rotate(-45deg);
            z-index: -1;
        }
        .currency-badge {
            display: inline-block;
            padding: 2px 6px;
            background-color: #e9ecef;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <?php
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

        // Format currency with symbol
        function formatCurrency($amount, $currency = 'USD') {
            $symbol = $currency === 'USD' ? '$' : ($currency === 'KSH' ? 'KSh ' : $currency . ' ');
            return $symbol . number_format($amount, 2);
        }
    ?>

    <div class="watermark"><?php echo e(config('app.name')); ?></div>

    <div class="header">
        <div class="company-name"><?php echo e(config('app.name', 'Kenya Power and lighting PLC')); ?></div>
        <div class="company-details">
            Kolobot Rd, Nairobi,<br>
            P.O Box 330099 - 00100,<br>
            Phone: (+254) 0711-311000 | Email: info@kplc.com
        </div>
        <div class="statement-title">STATEMENT OF ACCOUNT</div>
        <?php if(count($currencies) > 1): ?>
            <div class="currency-badge">Multi-Currency Statement</div>
        <?php endif; ?>
    </div>

    <div class="info-section">
        <div class="row">
            <div class="info-row">
                <span class="label">Statement Number:</span>
                <span class="value"><?php echo e($statement->statement_number); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Statement Date:</span>
                <span class="value"><?php echo e($statement->statement_date->format('F d, Y')); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Period:</span>
                <span class="value"><?php echo e($statement->period_start->format('F d, Y')); ?> - <?php echo e($statement->period_end->format('F d, Y')); ?></span>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h4 style="margin-top: 0; color: #2c3e50;">BILL TO:</h4>
        <div class="info-row"><span class="label">Customer:</span> <?php echo e($customer->name); ?></div>
        <?php if($customer->company_name): ?>
        <div class="info-row"><span class="label">Company:</span> <?php echo e($customer->company_name); ?></div>
        <?php endif; ?>
        <div class="info-row"><span class="label">Email:</span> <?php echo e($customer->email); ?></div>
        <?php if($customer->phone): ?>
        <div class="info-row"><span class="label">Phone:</span> <?php echo e($customer->phone); ?></div>
        <?php endif; ?>
        <?php if($customer->address): ?>
        <div class="info-row"><span class="label">Address:</span> <?php echo e($customer->address); ?></div>
        <?php endif; ?>
    </div>

    <div class="summary-box">
        <h4 style="margin-top: 0; color: #2c3e50;">ACCOUNT SUMMARY</h4>
        <div class="info-row">
            <span class="label">Opening Balance:</span>
            <span class="value"><?php echo e(formatCurrency($statement->opening_balance, $primaryCurrency)); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Total Debits (Payments Out):</span>
            <span class="value amount-negative"><?php echo e(formatCurrency($statement->total_debits, $primaryCurrency)); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Total Credits (Payments In):</span>
            <span class="value amount-positive"><?php echo e(formatCurrency($statement->total_credits, $primaryCurrency)); ?></span>
        </div>
        <div class="info-row balance-row">
            <span class="label">Closing Balance:</span>
            <span class="value <?php echo e($statement->closing_balance >= 0 ? 'amount-positive' : 'amount-negative'); ?>">
                <?php echo e(formatCurrency($statement->closing_balance, $primaryCurrency)); ?>

            </span>
        </div>
    </div>

    <h4 style="color: #2c3e50;">TRANSACTION DETAILS</h4>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction #</th>
                <th>Description</th>
                <th>Reference</th>
                <th class="text-right">Currency</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" class="text-right"><strong>Opening Balance:</strong></td>
                <td class="text-right"><strong><?php echo e(formatCurrency($statement->opening_balance, $primaryCurrency)); ?></strong></td>
            </tr>

            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($transaction->transaction_date->format('d/m/Y')); ?></td>
                <td><?php echo e($transaction->transaction_number); ?></td>
                <td><?php echo e($transaction->description); ?></td>
                <td><?php echo e($transaction->reference ?? '-'); ?></td>
                <td class="text-right"><?php echo e($transaction->currency ?? 'USD'); ?></td>
                <td class="text-right"><?php echo e($transaction->direction == 'out' ? formatCurrency($transaction->amount, $transaction->currency ?? 'USD') : '-'); ?></td>
                <td class="text-right"><?php echo e($transaction->direction == 'in' ? formatCurrency($transaction->amount, $transaction->currency ?? 'USD') : '-'); ?></td>
                <td class="text-right"><?php echo e(formatCurrency($transaction->balance, $transaction->currency ?? 'USD')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="8" class="text-center">No transactions found for this period</td>
            </tr>
            <?php endif; ?>

            <tr class="balance-row">
                <td colspan="7" class="text-right"><strong>Closing Balance:</strong></td>
                <td class="text-right"><strong><?php echo e(formatCurrency($statement->closing_balance, $primaryCurrency)); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <?php if($statement->closing_balance > 0): ?>
    <div style="margin-top: 20px; padding: 10px; background-color: #fef9e7; border-left: 4px solid #f39c12;">
        <strong>Payment Due:</strong> Please pay the outstanding balance of <strong><?php echo e(formatCurrency($statement->closing_balance, $primaryCurrency)); ?></strong>.
    </div>
    <?php elseif($statement->closing_balance < 0): ?>
    <div style="margin-top: 20px; padding: 10px; background-color: #e8f5e9; border-left: 4px solid #27ae60;">
        <strong>Credit Balance:</strong> Your account has a credit balance of <strong><?php echo e(formatCurrency(abs($statement->closing_balance), $primaryCurrency)); ?></strong>.
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>This is a computer-generated statement. No signature is required.</p>
        <p>Generated on: <?php echo e(now()->format('F d, Y H:i:s')); ?></p>
        <p>For questions regarding this statement, please contact our billing department at billing@kplc.com</p>
        <div class="signature-line">Authorized Signature</div>
    </div>
</body>
</html>
<?php /**PATH G:\project\darkfibre-crm\resources\views/statements/pdf.blade.php ENDPATH**/ ?>