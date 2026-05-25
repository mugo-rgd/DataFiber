<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-chart-pie text-warning me-2"></i>Debt Aging Analysis
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Customer</th>
                        <th>Currency</th>
                        <th>Current</th>
                        <th>1-30</th>
                        <th>31-60</th>
                        <th>61-90</th>
                        <th>91-120</th>
                        <th>120+</th>
                        <th class="px-4 py-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debtAging as $row)
                        <tr>
                            <td class="px-4 py-3 fw-semibold">{{ $row->customer->name ?? 'N/A' }}</td>
                            <td><span class="badge bg-light text-dark rounded-pill">{{ $row->currency }}</span></td>
                            <td>{{ number_format($row->current_amount, 2) }}</td>
                            <td>{{ number_format($row->days_1_30, 2) }}</td>
                            <td>{{ number_format($row->days_31_60, 2) }}</td>
                            <td>{{ number_format($row->days_61_90, 2) }}</td>
                            <td>{{ number_format($row->days_91_120, 2) }}</td>
                            <td class="text-danger fw-bold">{{ number_format($row->days_120_plus, 2) }}</td>
                            <td class="px-4 py-3 fw-bold">{{ number_format($row->total_outstanding, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No debt aging records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
