<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        .box { padding: 15px; border: 1px solid #ddd; margin-bottom: 10px; }
        .title { color: #0066B3; }
    </style>
</head>
<body>

<h2 class="title">Daily Executive Dashboard Report</h2>

<p>
    Dear Team,<br>
    Please find attached the executive dashboard report for
    <strong>{{ $snapshotDate }}</strong>.
</p>

<div class="box">
    <h3>Key Highlights</h3>

    <p><strong>Revenue:</strong></p>
    <p>KSH {{ number_format($kpis->revenue_ksh, 2) }}</p>
    <p>USD {{ number_format($kpis->revenue_usd, 2) }}</p>

    <p><strong>Accounts Receivable:</strong></p>
    <p>KSH {{ number_format($kpis->accounts_receivable_ksh, 2) }}</p>
    <p>USD {{ number_format($kpis->accounts_receivable_usd, 2) }}</p>

    <p><strong>Active Leases:</strong> {{ number_format($kpis->active_leases) }}</p>
    <p><strong>Active Contracts:</strong> {{ number_format($kpis->active_contracts) }}</p>

    <p><strong>Overdue Debt:</strong></p>
    <p>KSH {{ number_format($kpis->overdue_ksh, 2) }}</p>
    <p>USD {{ number_format($kpis->overdue_usd, 2) }}</p>
</div>

<p>
    Regards,<br>
    Dark Fibre CRM
</p>

</body>
</html>
