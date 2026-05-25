<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-shield-alt text-info me-2"></i>SLA & Network Availability
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Customer</th>
                        <th>Lease</th>
                        <th>Incidents</th>
                        <th>Open</th>
                        <th>Resolved</th>
                        <th>Downtime</th>
                        <th>Uptime %</th>
                        <th>SLA Target %</th>
                        <th class="px-4 py-3">Breaches</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slaNetwork as $row)
                        <tr>
                            <td class="px-4 py-3">{{ $row->customer->name ?? 'N/A' }}</td>
                            <td>{{ $row->lease->lease_number ?? $row->lease_id }}</td>
                            <td>{{ $row->total_incidents }}</td>
                            <td class="text-warning">{{ $row->open_incidents }}</td>
                            <td class="text-kp-green">{{ $row->resolved_incidents }}</td>
                            <td>{{ $row->downtime_minutes }}</td>
                            <td class="fw-bold">{{ number_format($row->uptime_percent, 3) }}%</td>
                            <td>{{ number_format($row->sla_target_percent, 3) }}%</td>
                            <td class="px-4 py-3 text-danger fw-bold">{{ $row->sla_breaches }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No SLA/network records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
