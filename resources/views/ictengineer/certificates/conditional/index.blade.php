@extends('layouts.app')

@section('title', 'Conditional Certificates')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-contract text-info me-2"></i>Conditional Certificates
                    </h1>
                    <p class="text-muted mb-0">Manage conditional certificates issued for design requests</p>
                </div>
                <a href="{{ route('ictengineer.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-list me-2 text-kp-blue"></i>All Conditional Certificates
            </h5>
            <div class="d-flex gap-2">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchTable" placeholder="Search certificates...">
                </div>
                <select class="form-select form-select-sm" id="filterStatus" style="width: 150px;">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="sent_to_designer">Sent to Designer</option>
                    <option value="acknowledged">Acknowledged</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            @if($certificates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="certificatesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Certificate Ref</th>
                                <th>Request #</th>
                                <th>Customer</th>
                                <th>Link Name</th>
                                <th>Issue Date</th>
                                <th>Commissioning End</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $certificate)
                                @php
                                    $designRequest = $certificate->designRequest;
                                    $daysSince = Carbon\Carbon::parse($certificate->certificate_date)->diffInDays(now());
                                    $daysRemaining = max(0, 30 - $daysSince);
                                    $isExpiring = $daysRemaining <= 7 && $daysRemaining > 0;
                                @endphp
                                <tr data-status="{{ $certificate->certificate_status }}">
                                    <td>
                                        <strong>{{ $certificate->ref_number }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $certificate->id }}</small>
                                    </td>
                                    <td>
                                        <strong>#{{ $designRequest->request_number ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $designRequest->id ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $designRequest->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $certificate->link_name }}</td>
                                    <td>
                                        {{ Carbon\Carbon::parse($certificate->certificate_date)->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $daysSince }} days ago</small>
                                    </td>
                                    <td>
                                        {{ Carbon\Carbon::parse($certificate->commissioning_end_date)->format('M d, Y') }}
                                        @if($daysRemaining > 0)
                                            <br>
                                            <small class="{{ $isExpiring ? 'text-warning' : 'text-muted' }}">
                                                {{ $daysRemaining }} days remaining
                                            </small>
                                        @else
                                            <br>
                                            <small class="text-success">Completed</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'pending_designer' => 'warning',
                                                'sent_to_designer' => 'info',
                                                'acknowledged' => 'primary',
                                                'completed' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $color = $statusColors[$certificate->certificate_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }} rounded-pill px-3 py-1">
                                            {{ ucfirst(str_replace('_', ' ', $certificate->certificate_status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group gap-1">
                                            <a href="{{ route('ictengineer.certificates.conditional.show', $certificate) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                               title="View Certificate">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('ictengineer.certificates.conditional.download', $certificate) }}"
                                               class="btn btn-sm btn-outline-success rounded-pill px-3"
                                               title="Download Report">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('ictengineer.certificates.conditional.preview', $certificate) }}"
                                               class="btn btn-sm btn-outline-info rounded-pill px-3"
                                               target="_blank"
                                               title="Preview">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $certificates->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-contract fa-4x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">No Conditional Certificates</h5>
                    <p class="text-muted mb-0">You haven't issued any conditional certificates yet.</p>
                    <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Generate New Certificate
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}

.table td {
    vertical-align: middle;
}

.table-primary {
    background-color: #e8f4fd !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTable');
    const filterSelect = document.getElementById('filterStatus');
    const tableRows = document.querySelectorAll('#certificatesTable tbody tr');

    function filterTable() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const filterValue = filterSelect?.value || '';

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matchesSearch = !searchTerm || text.includes(searchTerm);
            const matchesFilter = !filterValue || row.dataset.status === filterValue;

            row.style.display = matchesSearch && matchesFilter ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', filterTable);
    }
});
</script>
@endsection
