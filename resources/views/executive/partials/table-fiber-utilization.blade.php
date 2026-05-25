<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-chart-bar text-kp-green me-2"></i>Fibre Utilization
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Route</th>
                        <th>Region</th>
                        <th>Total KM</th>
                        <th>Total Cores</th>
                        <th>Used Cores</th>
                        <th>Available Cores</th>
                        <th>Utilization %</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fiberUtilization as $row)
                        <tr>
                            <td class="px-4 py-3 fw-semibold">{{ $row->route_name ?? 'N/A' }}</td>
                            <td>{{ $row->region ?? 'N/A' }}</td>
                            <td>{{ number_format($row->total_fibre_km, 2) }}</td>
                            <td>{{ $row->total_cores }}</td>
                            <td>{{ $row->used_cores }}</td>
                            <td>{{ $row->available_cores }}</td>
                            <td class="fw-bold">{{ number_format($row->utilization_percent, 2) }}%</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $row->capacity_status == 'critical' || $row->capacity_status == 'saturated' ? 'danger' : ($row->capacity_status == 'warning' ? 'warning' : 'success') }} rounded-pill px-3 py-1">
                                    {{ strtoupper($row->capacity_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No fibre utilization records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
