<table>
    <tr>
        <th colspan="10">Executive Dashboard</th>
    </tr>
    <tr>
        <td>Snapshot Date</td>
        <td>{{ $snapshotDate }}</td>
        <td>Period</td>
        <td>{{ $periodStart }} to {{ $periodEnd }}</td>
    </tr>
</table>

<br>

<table>
    <tr>
        <th colspan="4">Executive KPI Summary</th>
    </tr>
    <tr>
        <th>Metric</th>
        <th>KSH</th>
        <th>USD</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>Revenue</td>
        <td>{{ $kpis->revenue_ksh }}</td>
        <td>{{ $kpis->revenue_usd }}</td>
        <td></td>
    </tr>
    <tr>
        <td>Accounts Receivable</td>
        <td>{{ $kpis->accounts_receivable_ksh }}</td>
        <td>{{ $kpis->accounts_receivable_usd }}</td>
        <td></td>
    </tr>
    <tr>
        <td>Overdue Debt</td>
        <td>{{ $kpis->overdue_ksh }}</td>
        <td>{{ $kpis->overdue_usd }}</td>
        <td></td>
    </tr>
    <tr>
        <td>Quotation Pipeline</td>
        <td>{{ $kpis->quotation_pipeline_ksh }}</td>
        <td>{{ $kpis->quotation_pipeline_usd }}</td>
        <td></td>
    </tr>
    <tr>
        <td>Active Leases</td>
        <td></td>
        <td></td>
        <td>{{ $kpis->active_leases }}</td>
    </tr>
    <tr>
        <td>Active Contracts</td>
        <td></td>
        <td></td>
        <td>{{ $kpis->active_contracts }}</td>
    </tr>
    <tr>
        <td>Core Utilization %</td>
        <td></td>
        <td></td>
        <td>{{ $kpis->core_utilization_percent }}</td>
    </tr>
    <tr>
        <td>Network Availability %</td>
        <td></td>
        <td></td>
        <td>{{ $kpis->network_availability_percent }}</td>
    </tr>
</table>

<br>

<table>
    <tr>
        <th colspan="9">Debt Aging</th>
    </tr>
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
    @foreach($debtAging as $row)
        <tr>
            <td>{{ $row->customer->name ?? 'N/A' }}</td>
            <td>{{ $row->currency }}</td>
            <td>{{ $row->current_amount }}</td>
            <td>{{ $row->days_1_30 }}</td>
            <td>{{ $row->days_31_60 }}</td>
            <td>{{ $row->days_61_90 }}</td>
            <td>{{ $row->days_91_120 }}</td>
            <td>{{ $row->days_120_plus }}</td>
            <td>{{ $row->total_outstanding }}</td>
        </tr>
    @endforeach
</table>

<br>

<table>
    <tr>
        <th colspan="7">Revenue Details</th>
    </tr>
    <tr>
        <th>Billing ID</th>
        <th>Lease ID</th>
        <th>Service Type</th>
        <th>Currency</th>
        <th>Billed</th>
        <th>Paid</th>
        <th>Outstanding</th>
    </tr>
    @foreach($revenue as $row)
        <tr>
            <td>{{ $row->billing_id }}</td>
            <td>{{ $row->lease_id }}</td>
            <td>{{ $row->service_type }}</td>
            <td>{{ $row->currency }}</td>
            <td>{{ $row->billed_amount }}</td>
            <td>{{ $row->paid_amount }}</td>
            <td>{{ $row->outstanding_amount }}</td>
        </tr>
    @endforeach
</table>

<br>

<table>
    <tr>
        <th colspan="6">Top Customers</th>
    </tr>
    <tr>
        <th>Customer</th>
        <th>Currency</th>
        <th>Revenue</th>
        <th>Outstanding</th>
        <th>Contribution %</th>
        <th>Risk</th>
    </tr>
    @foreach($topCustomers as $row)
        <tr>
            <td>{{ $row->customer->name ?? 'N/A' }}</td>
            <td>{{ $row->currency }}</td>
            <td>{{ $row->revenue }}</td>
            <td>{{ $row->outstanding_amount }}</td>
            <td>{{ $row->revenue_contribution_percent }}</td>
            <td>{{ strtoupper($row->risk_level) }}</td>
        </tr>
    @endforeach
</table>

<br>

<table>
    <tr>
        <th colspan="8">Quotation Pipeline</th>
    </tr>
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
    @foreach($quotations as $row)
        <tr>
            <td>{{ $row->currency }}</td>
            <td>{{ $row->stage }}</td>
            <td>{{ $row->status }}</td>
            <td>{{ $row->quotation_count }}</td>
            <td>{{ $row->pipeline_value }}</td>
            <td>{{ $row->won_value }}</td>
            <td>{{ $row->lost_value }}</td>
            <td>{{ $row->conversion_rate_percent }}</td>
        </tr>
    @endforeach
</table>

<br>

<table>
    <tr>
        <th colspan="8">Contracts</th>
    </tr>
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
    @foreach($contracts as $row)
        <tr>
            <td>{{ $row->currency }}</td>
            <td>{{ $row->status }}</td>
            <td>{{ $row->contract_count }}</td>
            <td>{{ $row->contract_value }}</td>
            <td>{{ $row->expiring_30_days }}</td>
            <td>{{ $row->expiring_60_days }}</td>
            <td>{{ $row->expiring_90_days }}</td>
            <td>{{ $row->renewal_revenue_at_risk }}</td>
        </tr>
    @endforeach
</table>

<br>

<table>
    <tr>
        <th colspan="9">Leases</th>
    </tr>
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
    @foreach($leases as $row)
        <tr>
            <td>{{ $row->currency }}</td>
            <td>{{ $row->service_type }}</td>
            <td>{{ $row->status }}</td>
            <td>{{ $row->region }}</td>
            <td>{{ $row->lease_count }}</td>
            <td>{{ $row->monthly_revenue }}</td>
            <td>{{ $row->contract_value }}</td>
            <td>{{ $row->leased_distance_km }}</td>
            <td>{{ $row->leased_cores }}</td>
        </tr>
    @endforeach
</table>

<br>

<table>
    <tr>
        <th colspan="8">Fibre Utilization</th>
    </tr>
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
    @foreach($fiberUtilization as $row)
        <tr>
            <td>{{ $row->route_name }}</td>
            <td>{{ $row->region }}</td>
            <td>{{ $row->total_fibre_km }}</td>
            <td>{{ $row->total_cores }}</td>
            <td>{{ $row->used_cores }}</td>
            <td>{{ $row->available_cores }}</td>
            <td>{{ $row->utilization_percent }}</td>
            <td>{{ strtoupper($row->capacity_status) }}</td>
        </tr>
    @endforeach
</table>

<br>

<table>
    <tr>
        <th colspan="9">SLA & Network Availability</th>
    </tr>
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
    @foreach($slaNetwork as $row)
        <tr>
            <td>{{ $row->customer->name ?? 'N/A' }}</td>
            <td>{{ $row->lease->lease_number ?? $row->lease_id }}</td>
            <td>{{ $row->total_incidents }}</td>
            <td>{{ $row->open_incidents }}</td>
            <td>{{ $row->resolved_incidents }}</td>
            <td>{{ $row->downtime_minutes }}</td>
            <td>{{ $row->uptime_percent }}</td>
            <td>{{ $row->sla_target_percent }}</td>
            <td>{{ $row->sla_breaches }}</td>
        </tr>
    @endforeach
</table>
