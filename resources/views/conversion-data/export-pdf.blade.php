<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fibre Links Export - {{ now()->format('Y-m-d') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #0056b3; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #0056b3; color: white; font-weight: bold; padding: 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 6px; }
        .footer { margin-top: 30px; font-size: 9px; color: #666; text-align: center; }
        .text-right { text-align: right; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fibre Links Export</h1>
        <p>Generated: {{ now()->format('F j, Y H:i:s') }}</p>
        <p>Total Records: {{ $data->count() }}</p>

        @if(!empty($filters))
        <div style="margin-top: 10px; font-size: 9px;">
            <strong>Applied Filters:</strong>
            @foreach($filters as $key => $value)
                @if($value && !in_array($key, ['_token', 'page', 'per_page']))
                    <span>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}, </span>
                @endif
            @endforeach
        </div>
        @endif
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
                <th>Duration</th>
                <th>Total USD</th>
                <th>Created Date</th>
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
                <td class="text-right">{{ $item->monthly_link_value_usd ? '$' . number_format($item->monthly_link_value_usd, 2) : '-' }}</td>
                <td>{{ $item->contract_duration_yrs ? $item->contract_duration_yrs . ' yrs' : '-' }}</td>
                <td class="text-right">{{ $item->total_contract_value_usd ? '$' . number_format($item->total_contract_value_usd, 2) : '-' }}</td>
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
