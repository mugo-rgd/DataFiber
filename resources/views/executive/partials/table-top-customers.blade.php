<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-trophy text-kp-yellow me-2"></i>Top Customers
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Customer</th>
                        <th>Currency</th>
                        <th>Revenue</th>
                        <th>Outstanding</th>
                        <th>Contribution %</th>
                        <th class="px-4 py-3">Risk Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCustomers as $row)
                        <tr>
                            <td class="px-4 py-3 fw-semibold">{{ $row->customer->name ?? 'N/A' }}</td>
                            <td><span class="badge bg-light text-dark rounded-pill">{{ $row->currency }}</span></td>
                            <td>{{ number_format($row->revenue, 2) }}</td>
                            <td class="text-danger">{{ number_format($row->outstanding_amount, 2) }}</td>
                            <td>{{ number_format($row->revenue_contribution_percent, 2) }}%</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $row->risk_level == 'high' ? 'danger' : ($row->risk_level == 'medium' ? 'warning' : 'success') }} rounded-pill px-3 py-1">
                                    {{ strtoupper($row->risk_level) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No top customer records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
