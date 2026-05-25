<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-file-contract text-purple me-2"></i>Contracts
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Currency</th>
                        <th>Status</th>
                        <th>Contracts</th>
                        <th>Value</th>
                        <th>Expiring 30</th>
                        <th>Expiring 60</th>
                        <th>Expiring 90</th>
                        <th class="px-4 py-3">Revenue At Risk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $row)
                        <tr>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark rounded-pill">{{ $row->currency }}</span></td>
                            <td>{{ $row->status ?? 'N/A' }}</td>
                            <td>{{ $row->contract_count }}</td>
                            <td>{{ number_format($row->contract_value, 2) }}</td>
                            <td class="text-warning">{{ $row->expiring_30_days }}</td>
                            <td>{{ $row->expiring_60_days }}</td>
                            <td>{{ $row->expiring_90_days }}</td>
                            <td class="px-4 py-3 text-danger fw-bold">{{ number_format($row->renewal_revenue_at_risk, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No contract records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
