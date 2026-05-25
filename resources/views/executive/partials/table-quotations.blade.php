<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-file-invoice-dollar text-info me-2"></i>Quotation Pipeline
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Currency</th>
                        <th>Stage</th>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Pipeline Value</th>
                        <th>Won Value</th>
                        <th>Lost Value</th>
                        <th class="px-4 py-3">Conversion %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $row)
                        <tr>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark rounded-pill">{{ $row->currency }}</span></td>
                            <td>{{ $row->stage ?? 'N/A' }}</td>
                            <td>{{ $row->status ?? 'N/A' }}</td>
                            <td>{{ $row->quotation_count }}</td>
                            <td>{{ number_format($row->pipeline_value, 2) }}</td>
                            <td class="text-kp-green">{{ number_format($row->won_value, 2) }}</td>
                            <td class="text-danger">{{ number_format($row->lost_value, 2) }}</td>
                            <td class="px-4 py-3 fw-bold">{{ number_format($row->conversion_rate_percent, 2) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No quotation records</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
