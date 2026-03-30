<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billings Export</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; }
        h1 { color: #4e73df; border-bottom: 2px solid #4e73df; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background-color: #4e73df; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .text-end { text-align: right; }
        .badge { display: inline-block; padding: 3px 6px; border-radius: 3px; }
        .bg-success { background-color: #1cc88a; color: white; }
        .bg-warning { background-color: #f6c23e; color: white; }
        .bg-danger { background-color: #e74a3b; color: white; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; text-align: center; color: #6c757d; }
    </style>
</head>
<body>
    <h1>Billings Export</h1>
    <p>Generated on: {{ now()->format('d M Y H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>Billing #</th>
                <th>Date</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Status</th>
                <th>KRA Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($billings as $billing)
            <tr>
                <td>{{ $billing->billing_number }}</td>
                <td>{{ \Carbon\Carbon::parse($billing->billing_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($billing->due_date)->format('d/m/Y') }}</td>
                <td class="text-end">{{ number_format($billing->total_amount, 2) }}</td>
                <td>{{ $billing->currency }}</td>
                <td>{{ ucfirst($billing->status) }}</td>
                <td>{{ $billing->tevin_status ?? 'pending' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Kenya Power & Lighting Co. Ltd - Dark Fibre Services Division</p>
    </div>
</body>
</html>
