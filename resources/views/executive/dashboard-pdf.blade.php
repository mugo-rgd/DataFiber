<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Dashboard PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        h2 {
            margin-bottom: 3px;
            color: #0066B3;
        }

        h4 {
            margin: 18px 0 6px;
            color: #009639;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th, td {
            border: 1px solid #999;
            padding: 4px;
            text-align: left;
        }

        th {
            background: #eeeeee;
            font-weight: bold;
        }

        .summary-table td {
            width: 25%;
            vertical-align: top;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-success {
            color: #009639;
        }

        .text-warning {
            color: #b8860b;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

<h2>Executive Dashboard</h2>
<p>
    <strong>Snapshot:</strong> {{ $snapshotDate }}
    |
    <strong>Period:</strong> {{ $periodStart }} to {{ $periodEnd }}
</p>

<h4>Executive KPI Summary</h4>
<table class="summary-table">
    <tr>
        <td>
            <strong>Revenue</strong><br>
            KSH {{ number_format($kpis->revenue_ksh, 2) }}<br>
            USD {{ number_format($kpis->revenue_usd, 2) }}
        </td>
        <td>
            <strong>Accounts Receivable</strong><br>
            KSH {{ number_format($kpis->accounts_receivable_ksh, 2) }}<br>
            USD {{ number_format($kpis->accounts_receivable_usd, 2) }}
        </td>
        <td>
            <strong>Active Leases</strong><br>
            {{ number_format($kpis->active_leases) }}
        </td>
        <td>
            <strong>Active Contracts</strong><br>
            {{ number_format($kpis->active_contracts) }}
        </td>
    </tr>
    <tr>
        <td>
            <strong>Quotation Pipeline</strong><br>
            KSH {{ number_format($kpis->quotation_pipeline_ksh, 2) }}<br>
            USD {{ number_format($kpis->quotation_pipeline_usd, 2) }}
        </td>
        <td>
            <strong>Overdue Debt</strong><br>
            KSH {{ number_format($kpis->overdue_ksh, 2) }}<br>
            USD {{ number_format($kpis->overdue_usd, 2) }}
        </td>
        <td>
            <strong>Core Utilization</strong><br>
            {{ number_format($kpis->core_utilization_percent, 2) }}%
        </td>
        <td>
            <strong>Network Availability</strong><br>
            {{ number_format($kpis->network_availability_percent, 2) }}%
        </td>
    </tr>
</table>

<h4>Debt Aging Summary</h4>
<table>
    <tr>
        <th>Total Debt KSH</th>
        <th>Total Debt USD</th>
        <th>Customers With Debt</th>
        <th>Overdue Accounts</th>
    </tr>
    <tr>
        <td>{{ number_format($debtAging->where('currency', 'KSH')->sum('total_outstanding'), 2) }}</td>
        <td>{{ number_format($debtAging->where('currency', 'USD')->sum('total_outstanding'), 2) }}</td>
        <td>{{ number_format($debtAging->count()) }}</td>
        <td>{{ number_format($debtAging->where('overdue_count', '>', 0)->count()) }}</td>
    </tr>
</table>

<h4>Debt Aging</h4>
<table>
    <thead>
        <tr>
            <th>Customer</th>
            <th>Currency</th>
            <th>Current</th>
            <th>1-30</th>
            <th>31-60</th>
            <th>61-90</th>
            <th>91-120</th>
            <th>120+</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($debtAging as $row)
            <tr>
                <td>{{ $row->customer->name ?? 'N/A' }}</td>
                <td>{{ $row->currency }}</td>
                <td>{{ number_format($row->current_amount, 2) }}</td>
                <td>{{ number_format($row->days_1_30, 2) }}</td>
                <td>{{ number_format($row->days_31_60, 2) }}</td>
                <td>{{ number_format($row->days_61_90, 2) }}</td>
                <td>{{ number_format($row->days_91_120, 2) }}</td>
                <td>{{ number_format($row->days_120_plus, 2) }}</td>
                <td>{{ number_format($row->total_outstanding, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9">No debt aging records.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h4>Revenue Summary</h4>
<table>
    <tr>
        <th>Revenue KSH</th>
        <th>Revenue USD</th>
        <th>Paid KSH</th>
        <th>Paid USD</th>
        <th>Outstanding KSH</th>
        <th>Outstanding USD</th>
    </tr>
    <tr>
        <td>{{ number_format($summary['revenue_ksh'] ?? 0, 2) }}</td>
        <td>{{ number_format($summary['revenue_usd'] ?? 0, 2) }}</td>
        <td>{{ number_format($summary['paid_ksh'] ?? 0, 2) }}</td>
        <td>{{ number_format($summary['paid_usd'] ?? 0, 2) }}</td>
        <td>{{ number_format($summary['outstanding_ksh'] ?? 0, 2) }}</td>
        <td>{{ number_format($summary['outstanding_usd'] ?? 0, 2) }}</td>
    </tr>
</table>

<h4>Revenue Details</h4>
<table>
    <thead>
        <tr>
            <th>Billing ID</th>
            <th>Lease ID</th>
            <th>Service Type</th>
            <th>Currency</th>
            <th>Billed</th>
            <th>Paid</th>
            <th>Outstanding</th>
        </tr>
    </thead>
    <tbody>
        @forelse($revenue as $row)
            <tr>
                <td>{{ $row->billing_id }}</td>
                <td>{{ $row->lease_id ?? 'N/A' }}</td>
                <td>{{ $row->service_type ?? 'N/A' }}</td>
                <td>{{ $row->currency }}</td>
                <td>{{ number_format($row->billed_amount, 2) }}</td>
                <td>{{ number_format($row->paid_amount, 2) }}</td>
                <td>{{ number_format($row->outstanding_amount, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No revenue records.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="page-break"></div>

<h4>Top Customers</h4>
<table>
    <thead>
        <tr>
            <th>Customer</th>
            <th>Currency</th>
            <th>Revenue</th>
            <th>Outstanding</th>
            <th>Contribution %</th>
            <th>Risk</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topCustomers as $row)
            <tr>
                <td>{{ $row->customer->name ?? 'N/A' }}</td>
                <td>{{ $row->currency }}</td>
                <td>{{ number_format($row->revenue, 2) }}</td>
                <td>{{ number_format($row->outstanding_amount, 2) }}</td>
                <td>{{ number_format($row->revenue_contribution_percent, 2) }}%</td>
                <td>{{ strtoupper($row->risk_level) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No top customer records.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h4>Quotation Pipeline</h4>
<table>
    <thead>
        <tr>
            <th>Currency</th>
            <th>Stage</th>
            <th>Status</th>
            <th>Count</th>
            <th>Pipeline Value</th>
            <th>Won Value</th>
            <th>Lost Value</th>
            <th>Conversion %</th>
        </tr>
    </thead>
    <tbody>
        @forelse($quotations as $row)
            <tr>
                <td>{{ $row->currency }}</td>
                <td>{{ $row->stage ?? 'N/A' }}</td>
                <td>{{ $row->status ?? 'N/A' }}</td>
                <td>{{ $row->quotation_count }}</td>
                <td>{{ number_format($row->pipeline_value, 2) }}</td>
                <td>{{ number_format($row->won_value, 2) }}</td>
                <td>{{ number_format($row->lost_value, 2) }}</td>
                <td>{{ number_format($row->conversion_rate_percent, 2) }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No quotation records.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h4>Contracts</h4>
<table>
    <thead>
        <tr>
            <th>Currency</th>
            <th>Status</th>
            <th>Contracts</th>
            <th>Value</th>
            <th>Expiring 30</th>
            <th>Expiring 60</th>
            <th>Expiring 90</th>
            <th>Revenue At Risk</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contracts as $row)
            <tr>
                <td>{{ $row->currency }}</td>
                <td>{{ $row->status ?? 'N/A' }}</td>
                <td>{{ $row->contract_count }}</td>
                <td>{{ number_format($row->contract_value, 2) }}</td>
                <td>{{ $row->expiring_30_days }}</td>
                <td>{{ $row->expiring_60_days }}</td>
                <td>{{ $row->expiring_90_days }}</td>
                <td>{{ number_format($row->renewal_revenue_at_risk, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No contract records.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h4>Leases</h4>
<table>
    <thead>
        <tr>
            <th>Currency</th>
            <th>Service Type</th>
            <th>Status</th>
            <th>Region</th>
            <th>Lease Count</th>
            <th>Monthly Revenue</th>
            <th>Contract Value</th>
            <th>Distance KM</th>
            <th>Cores</th>
        </tr>
    </thead>
    <tbody>
        @forelse($leases as $row)
            <tr>
                <td>{{ $row->currency }}</td>
                <td>{{ $row->service_type ?? 'N/A' }}</td>
                <td>{{ $row->status ?? 'N/A' }}</td>
                <td>{{ $row->region ?? 'N/A' }}</td>
                <td>{{ $row->lease_count }}</td>
                <td>{{ number_format($row->monthly_revenue, 2) }}</td>
                <td>{{ number_format($row->contract_value, 2) }}</td>
                <td>{{ number_format($row->leased_distance_km, 2) }}</td>
                <td>{{ $row->leased_cores }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9">No lease records.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="page-break"></div>

<h4>Fibre Utilization</h4>
<table>
    <thead>
        <tr>
            <th>Route</th>
            <th>Region</th>
            <th>Total KM</th>
            <th>Total Cores</th>
            <th>Used Cores</th>
            <th>Available Cores</th>
            <th>Utilization %</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($fiberUtilization as $row)
            <tr>
                <td>{{ $row->route_name ?? 'N/A' }}</td>
                <td>{{ $row->region ?? 'N/A' }}</td>
                <td>{{ number_format($row->total_fibre_km, 2) }}</td>
                <td>{{ $row->total_cores }}</td>
                <td>{{ $row->used_cores }}</td>
                <td>{{ $row->available_cores }}</td>
                <td>{{ number_format($row->utilization_percent, 2) }}%</td>
                <td>{{ strtoupper($row->capacity_status) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No fibre utilization records.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h4>SLA & Network Availability</h4>
<table>
    <thead>
        <tr>
            <th>Customer</th>
            <th>Lease</th>
            <th>Incidents</th>
            <th>Open</th>
            <th>Resolved</th>
            <th>Downtime</th>
            <th>Uptime %</th>
            <th>SLA Target %</th>
            <th>Breaches</th>
        </tr>
    </thead>
    <tbody>
        @forelse($slaNetwork as $row)
            <tr>
                <td>{{ $row->customer->name ?? 'N/A' }}</td>
                <td>{{ $row->lease->lease_number ?? $row->lease_id }}</td>
                <td>{{ $row->total_incidents }}</td>
                <td>{{ $row->open_incidents }}</td>
                <td>{{ $row->resolved_incidents }}</td>
                <td>{{ $row->downtime_minutes }}</td>
                <td>{{ number_format($row->uptime_percent, 3) }}%</td>
                <td>{{ number_format($row->sla_target_percent, 3) }}%</td>
                <td>{{ $row->sla_breaches }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9">No SLA/network records.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
