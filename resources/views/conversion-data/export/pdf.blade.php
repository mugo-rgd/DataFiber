<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fibre Links Export - {{ now()->format('Y-m-d') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #0056b3; color: white; font-weight: bold; padding: 8px; }
        td { border: 1px solid #ddd; padding: 6px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #0056b3; }
        .footer { margin-top: 30px; font-size: 10px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fibre Links Export</h1>
        <p>Generated: {{ now()->format('F j, Y H:i') }}</p>
        <p>Total Records: {{ $data->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Route</th>
                <th>Link Class</th>
                <th>Cores</th>
                <th>Distance (km)</th>
                <th>Monthly USD</th>
                <th>Contract Years</th>
                <th>Total USD</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->customer_name }}</td>
                <td>{{ $item->route_name ?? '-' }}</td>
                <td>{{ $item->link_class }}</td>
                <td>{{ $item->cores_leased ?? '-' }}</td>
                <td>{{ $item->distance_km ? number_format($item->distance_km, 1) : '-' }}</td>
                <td>{{ $item->monthly_link_value_usd ? '$' . number_format($item->monthly_link_value_usd, 2) : '-' }}</td>
                <td>{{ $item->contract_duration_yrs ?? '-' }}</td>
                <td>{{ $item->total_contract_value_usd ? '$' . number_format($item->total_contract_value_usd, 2) : '-' }}</td>
                <td>{{ $item->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Kenya Power Dark Fibre CRM | Export ID: {{ Str::random(8) }}</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
