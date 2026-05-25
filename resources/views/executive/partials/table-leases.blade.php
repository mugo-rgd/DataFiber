<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-network-wired text-kp-blue me-2"></i>Leases
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Currency</th>
                        <th>Service Type</th>
                        <th>Status</th>
                        <th>Region</th>
                        <th>Lease Count</th>
                        <th>Monthly Revenue</th>
                        <th>Contract Value</th>
                        <th>Distance KM</th>
                        <th class="px-4 py-3">Cores</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leases as $row)
                        <tr>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark rounded-pill">{{ $row->currency }}</span></td>
                            <td>{{ $row->service_type ?? 'N/A' }}</td>
                            <td>{{ $row->status ?? 'N/A' }}</td>
                            <td>{{ $row->region ?? 'N/A' }}</td>
                            <td>{{ $row->lease_count }}</td>
                            <td>{{ number_format($row->monthly_revenue, 2) }}</td>
                            <td>{{ number_format($row->contract_value, 2) }}</td>
                            <td>{{ number_format($row->leased_distance_km, 2) }}</td>
                            <td class="px-4 py-3 fw-bold">{{ $row->leased_cores }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No lease records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
