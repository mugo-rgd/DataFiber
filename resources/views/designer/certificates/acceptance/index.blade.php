@extends('layouts.app')

@section('title', 'Acceptance Certificates')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-signature text-success me-2"></i>Acceptance Certificates
                    </h1>
                    <p class="text-muted mb-0">Manage acceptance certificates for all design requests</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                    <a href="{{ route('designer.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-kp-blue text-uppercase mb-1">
                                Total Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $designRequests->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-kp-green text-uppercase mb-1">
                                Certificates Issued
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $designRequests->filter(function($r) { return $r->acceptanceCertificate; })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-kp-yellow text-uppercase mb-1">
                                Ready for Generation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $designRequests->filter(function($r) {
                                    $cert = $r->conditionalCertificate;
                                    $date = $cert ? ($cert->certificate_date ?? $cert->created_at) : null;
                                    $days = $date ? \Carbon\Carbon::parse($date)->diffInDays(now()) : 0;
                                    return $cert && $days >= 30 && !$r->acceptanceCertificate && $r->designer_id == Auth::id();
                                })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                My Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $designRequests->where('designer_id', Auth::id())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-list me-2 text-kp-blue"></i>All Design Requests - Acceptance Certificate Status
            </h5>
            <div class="d-flex gap-2">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchTable" placeholder="Search requests...">
                </div>
                <select class="form-select form-select-sm" id="filterStatus" style="width: 150px;">
                    <option value="">All Status</option>
                    <option value="ready">Ready for Generation</option>
                    <option value="issued">Certificate Issued</option>
                    <option value="pending">Pending Conditional</option>
                    <option value="waiting">Waiting Period</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            @if($designRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="certificatesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Request #</th>
                                <th>Customer</th>
                                <th>Presale Engineer</th>
                                <th>Title</th>
                                <th>Conditional Certificate</th>
                                <th>Conditional Date</th>
                                <th>Progress</th>
                                <th>Acceptance Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($designRequests as $request)
                                @php
                                    $conditionalCert = $request->conditionalCertificate;
                                    $certDate = $conditionalCert ? ($conditionalCert->certificate_date ?? $conditionalCert->created_at) : null;
                                    $daysSince = $certDate ? Carbon\Carbon::parse($certDate)->diffInDays(now()) : 0;
                                    $daysRemaining = $certDate ? max(0, ceil(30 - $daysSince)) : 0;
                                    $progressPercentage = $certDate ? min(100, round(($daysSince / 30) * 100)) : 0;
                                    $canGenerate = $certDate && $daysSince >= 30;
                                    $acceptanceExists = $request->acceptanceCertificate;
                                    $acceptanceCert = $request->acceptanceCertificate;
                                    $isMyRequest = $request->designer_id == Auth::id();
                                    $rowClass = $acceptanceExists ? 'table-success' : ($canGenerate && $isMyRequest ? 'table-primary' : '');
                                @endphp
                                <tr class="{{ $rowClass }}"
                                    data-status="{{ $acceptanceExists ? 'issued' : ($canGenerate ? 'ready' : ($conditionalCert ? 'waiting' : 'pending')) }}">
                                    <td>
                                        <strong>#{{ $request->request_number }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $request->id }}</small>
                                    </td>
                                    <td>{{ $request->customer->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-kp-green text-white rounded-circle">
                                                    {{ substr($request->designer->name ?? 'N/A', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <span>{{ $request->designer->name ?? 'N/A' }}</span>
                                                @if($isMyRequest)
                                                    <span class="badge bg-primary ms-1">You</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($request->title, 40) }}</td>
                                    <td>
                                        @if($conditionalCert)
                                            <span class="badge bg-success">{{ $conditionalCert->ref_number }}</span>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-user-circle me-1"></i>
                                                {{ $conditionalCert->ictEngineer->name ?? 'N/A' }}
                                            </small>
                                        @else
                                            <span class="badge bg-secondary">Not Issued</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($certDate)
                                            {{ Carbon\Carbon::parse($certDate)->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ Carbon\Carbon::parse($certDate)->diffForHumans() }}</small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($certDate)
                                            <div class="progress-wrapper" style="min-width: 100px;">
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span>{{ $daysSince }}/30 days</span>
                                                    <span>{{ $progressPercentage }}%</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $progressPercentage >= 100 ? 'success' : ($progressPercentage >= 50 ? 'warning' : 'info') }}"
                                                         role="progressbar"
                                                         style="width: {{ $progressPercentage }}%"
                                                         aria-valuenow="{{ $progressPercentage }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                @if($progressPercentage < 100)
                                                    <small class="text-muted">{{ $daysRemaining }} day{{ $daysRemaining != 1 ? 's' : '' }} remaining</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($acceptanceExists)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Issued
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $acceptanceCert->certificate_ref ?? '' }}</small>
                                        @elseif($canGenerate)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-file-signature me-1"></i>Ready
                                            </span>
                                        @elseif($conditionalCert)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-hourglass-half me-1"></i>Wait {{ $daysRemaining }}d
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group gap-1">
                                            @if($acceptanceExists)
                                                <a href="{{ route('designer.certificates.acceptance.show', $acceptanceCert) }}"
                                                   class="btn btn-sm btn-outline-info rounded-pill px-3"
                                                   title="View Certificate">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('designer.certificates.acceptance.download', $acceptanceCert) }}"
                                                   class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                   title="Download Certificate">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @elseif($canGenerate && $isMyRequest)
                                                <a href="{{ route('designer.certificates.acceptance.create', $request) }}"
                                                   class="btn btn-sm btn-success rounded-pill px-3"
                                                   title="Generate Certificate">
                                                    <i class="fas fa-file-signature me-1"></i> Generate
                                                </a>
                                            @elseif($canGenerate && !$isMyRequest)
                                                <button class="btn btn-sm btn-secondary rounded-pill px-3" disabled
                                                        title="Only the assigned presale engineer can generate this certificate">
                                                    <i class="fas fa-lock me-1"></i> Locked
                                                </button>
                                            @elseif($conditionalCert)
                                                <button class="btn btn-sm btn-secondary rounded-pill px-3" disabled>
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    Wait {{ $daysRemaining }}d
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary rounded-pill px-3" disabled>
                                                    <i class="fas fa-file-contract me-1"></i> Pending
                                                </button>
                                            @endif

                                            <a href="{{ route('designer.requests.show', $request) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                               title="View Request">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $designRequests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-signature fa-4x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">No Design Requests Found</h5>
                    <p class="text-muted mb-0">There are no design requests in the system yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: bold;
    font-size: 14px;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.btn-group .btn {
    transition: all 0.2s ease;
    white-space: nowrap;
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

.table-success {
    background-color: #d1e7dd !important;
}

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.3s ease;
}

.border-left-primary {
    border-left: 4px solid #0066B3;
}

.border-left-success {
    border-left: 4px solid #009639;
}

.border-left-warning {
    border-left: 4px solid #FFD700;
}

.border-left-info {
    border-left: 4px solid #17a2b8;
}

.text-kp-blue {
    color: #0066B3 !important;
}

.text-kp-green {
    color: #009639 !important;
}

.text-kp-yellow {
    color: #FFD700 !important;
}

.bg-kp-blue {
    background-color: #0066B3 !important;
}

.bg-kp-green {
    background-color: #009639 !important;
}

.bg-kp-yellow {
    background-color: #FFD700 !important;
    color: #333 !important;
}

@media (max-width: 768px) {
    .progress-wrapper {
        min-width: 80px;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

@media print {
    .btn, .btn-group, .d-flex.gap-2, .card-header .d-flex {
        display: none !important;
    }

    .table {
        font-size: 10pt;
    }

    .badge {
        border: 1px solid #ddd;
        background: none !important;
        color: #333 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
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
