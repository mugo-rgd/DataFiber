<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-chart-line text-kp-green me-2"></i>Revenue Details
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Billing ID</th>
                        <th>Lease ID</th>
                        <th>Service Type</th>
                        <th>Currency</th>
                        <th>Billed</th>
                        <th>Paid</th>
                        <th class="px-4 py-3">Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenue as $row)
                        <tr>
                            <td class="px-4 py-3 fw-semibold">{{ $row->billing_id }}</td>
                            <td>{{ $row->lease_id ?? 'N/A' }}</td>
                            <td>{{ $row->service_type ?? 'N/A' }}</td>
                            <td><span class="badge bg-light text-dark rounded-pill">{{ $row->currency }}</span></td>
                            <td>{{ number_format($row->billed_amount, 2) }}</td>
                            <td class="text-kp-green fw-bold">{{ number_format($row->paid_amount, 2) }}</td>
                            <td class="px-4 py-3 text-danger fw-bold">{{ number_format($row->outstanding_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No revenue records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
