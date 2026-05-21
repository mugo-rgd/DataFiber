@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">
                <i class="fas fa-file-alt me-2" style="color: #0066B3;"></i> CAK Compliance Dashboard
            </h3>
            <small class="text-muted">
                <i class="fas fa-balance-scale me-1"></i> Kenya Information and Communications Act, 1998 — Section 27 Compliance
            </small>
        </div>

        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" style="background: #0066B3; color: white;">
                <i class="fas fa-plus me-1"></i> New Return
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('asp.create') }}">
                        <i class="fas fa-server me-2" style="color: #0066B3;"></i> ASP Return
                        <small class="text-muted d-block ms-4">Application Service Provider</small>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('csp.create') }}">
                        <i class="fas fa-envelope me-2" style="color: #009639;"></i> CSP Return
                        <small class="text-muted d-block ms-4">Content Service Provider</small>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('nfp.create') }}">
                        <i class="fas fa-network-wired me-2" style="color: #FFD700;"></i> NFP Return
                        <small class="text-muted d-block ms-4">Network Facility Provider</small>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- UPCOMING DEADLINES (CAK Requirement) --}}
    <div class="alert mb-4" role="alert" style="background: #fff3cd; border-color: #FFD700; color: #856404;">
        <div class="d-flex align-items-center">
            <i class="fas fa-calendar-alt fa-lg me-3" style="color: #FFD700;"></i>
            <div>
                <strong>CAK Filing Deadline:</strong> Quarterly returns must be submitted within <strong>15 days</strong> after the end of each quarter.
                <br>
                <small>Next deadline: {{ \Carbon\Carbon::now()->endOfQuarter()->format('d M Y') }}</small>
            </div>
            <div class="ms-auto">
                <span class="badge" style="background: #dc3545;">
                    {{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::now()->endOfQuarter(), false) }} days remaining
                </span>
            </div>
        </div>
    </div>

    {{-- SUMMARY CARDS WITH CAK LICENSE CATEGORIES --}}
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-left: 4px solid #0066B3;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted">ASP Returns</h6>
                            <h2>{{ $aspCount ?? 0 }}</h2>
                            <small class="text-muted">Application Service Providers</small>
                        </div>
                        <i class="fas fa-server fa-2x opacity-50" style="color: #0066B3;"></i>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between small">
                        <span>Licensed: <strong>{{ $aspLicensedCount ?? 0 }}</strong></span>
                        <span>Filed: <strong>{{ $aspFiledCount ?? 0 }}</strong></span>
                        <span><i class="fas fa-chart-line"></i> {{ $aspComplianceRate ?? 0 }}%</span>
                    </div>
                    <a href="{{ route('asp.index') }}" class="btn btn-sm mt-2 w-100" style="border-color: #0066B3; color: #0066B3;">
                        <i class="fas fa-list"></i> Manage ASP Returns
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-left: 4px solid #009639;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted">CSP Returns</h6>
                            <h2>{{ $cspCount ?? 0 }}</h2>
                            <small class="text-muted">Content Service Providers</small>
                        </div>
                        <i class="fas fa-envelope fa-2x opacity-50" style="color: #009639;"></i>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between small">
                        <span>Licensed: <strong>{{ $cspLicensedCount ?? 0 }}</strong></span>
                        <span>Filed: <strong>{{ $cspFiledCount ?? 0 }}</strong></span>
                        <span><i class="fas fa-chart-line"></i> {{ $cspComplianceRate ?? 0 }}%</span>
                    </div>
                    <a href="{{ route('csp.index') }}" class="btn btn-sm mt-2 w-100" style="border-color: #009639; color: #009639;">
                        <i class="fas fa-list"></i> Manage CSP Returns
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-left: 4px solid #FFD700;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted">NFP Returns</h6>
                            <h2>{{ $nfpCount ?? 0 }}</h2>
                            <small class="text-muted">Network Facility Providers</small>
                        </div>
                        <i class="fas fa-network-wired fa-2x opacity-50" style="color: #FFD700;"></i>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between small">
                        <span>Licensed: <strong>{{ $nfpLicensedCount ?? 0 }}</strong></span>
                        <span>Filed: <strong>{{ $nfpFiledCount ?? 0 }}</strong></span>
                        <span><i class="fas fa-chart-line"></i> {{ $nfpComplianceRate ?? 0 }}%</span>
                    </div>
                    <a href="{{ route('nfp.index') }}" class="btn btn-sm mt-2 w-100" style="border-color: #FFD700; color: #8B6914;">
                        <i class="fas fa-list"></i> Manage NFP Returns
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- CAK COMPLIANCE STATUS SUMMARY --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong><i class="fas fa-chart-pie me-1" style="color: #0066B3;"></i> CAK Compliance Summary</strong>
                    <small class="text-muted ms-2">Q{{ $currentQuarter ?? 1 }} {{ $currentYear ?? date('Y') }}</small>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-6 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <h6 class="text-muted mb-0">Total Licensees</h6>
                                <h3 class="mb-0" style="color: #0066B3;">{{ $totalLicensees ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <h6 class="text-muted mb-0">Returns Due</h6>
                                <h3 class="mb-0" style="color: #FFD700;">{{ $returnsDue ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <h6 class="text-muted mb-0">Submitted</h6>
                                <h3 class="mb-0" style="color: #17a2b8;">{{ $submittedCount ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <h6 class="text-muted mb-0">Approved by CAK</h6>
                                <h3 class="mb-0" style="color: #009639;">{{ $approvedCount ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <h6 class="text-muted mb-0">Non-Compliant</h6>
                                <h3 class="mb-0" style="color: #dc3545;">{{ $nonCompliantCount ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <div class="border rounded p-2 bg-light">
                                <h6 class="text-muted mb-0">Compliance Rate</h6>
                                <h3 class="mb-0" style="color: #0066B3;">{{ $overallComplianceRate ?? 0 }}%</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RETURN STATUS TRACKER (CAK Workflow) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong><i class="fas fa-tasks me-1" style="color: #0066B3;"></i> Return Status Workflow (CAK Compliance)</strong>
                    <small class="text-muted ms-2">Submission → Validation → Approval</small>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="text-center p-2 flex-grow-1">
                            <i class="fas fa-pencil-alt fa-2x text-secondary"></i>
                            <div class="mt-1">
                                <span class="badge bg-secondary">{{ $draftCount ?? 0 }}</span>
                                <div><small>Draft</small></div>
                            </div>
                        </div>
                        <div class="text-center p-2 flex-grow-1">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="text-center p-2 flex-grow-1">
                            <i class="fas fa-file-pdf fa-2x" style="color: #FFD700;"></i>
                            <div class="mt-1">
                                <span class="badge" style="background: #FFD700; color: #003f20;">{{ $generatedCount ?? 0 }}</span>
                                <div><small>Generated (PDF)</small></div>
                            </div>
                        </div>
                        <div class="text-center p-2 flex-grow-1">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="text-center p-2 flex-grow-1">
                            <i class="fas fa-paper-plane fa-2x" style="color: #17a2b8;"></i>
                            <div class="mt-1">
                                <span class="badge bg-info">{{ $submittedCount ?? 0 }}</span>
                                <div><small>Submitted</small></div>
                            </div>
                        </div>
                        <div class="text-center p-2 flex-grow-1">
                            <i class="fas fa-arrow-right fa-2x text-muted"></i>
                        </div>
                        <div class="text-center p-2 flex-grow-1">
                            <i class="fas fa-check-circle fa-2x" style="color: #009639;"></i>
                            <div class="mt-1">
                                <span class="badge" style="background: #009639;">{{ $approvedCount ?? 0 }}</span>
                                <div><small>CAK Approved</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT RETURNS WITH CAK-SPECIFIC DETAILS --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <strong><i class="fas fa-history me-1" style="color: #0066B3;"></i> Recent CAK Compliance Returns</strong>
                <small class="text-muted ms-2">Last 30 days activity</small>
            </div>
            <div>
                <span class="badge" style="background: #0066B3;">CAK Format v2.1</span>
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Type</th>
                        <th>Licensee Name</th>
                        <th>License No.</th>
                        <th>Financial Year</th>
                        <th>Quarter</th>
                        <th>CAK Status</th>
                        <th>Submission Date</th>
                        <th>CAK Ref. No.</th>
                        <th width="260">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(($recentReturns ?? []) as $item)
                        <tr>
                            <td>
                                <span class="badge
                                    @if($item['type'] === 'ASP')" style="background: #0066B3;"
                                    @elseif($item['type'] === 'CSP')" style="background: #009639;"
                                    @else" style="background: #FFD700; color: #003f20;"
                                    @endif>
                                    <i class="fas
                                        @if($item['type'] === 'ASP') fa-server
                                        @elseif($item['type'] === 'CSP') fa-envelope
                                        @else fa-network-wired
                                        @endif me-1"></i>
                                    {{ $item['type'] }}
                                </span>
                            </td>

                            <td>{{ $item['record']->licensee_name ?? '—' }}</td>
                            <td>{{ $item['record']->license_no ?? '—' }}</td>
                            <td>{{ $item['record']->financial_year ?? '—' }}</td>
                            <td>{{ $item['record']->quarter ?? '—' }}</td>

                            <td>
                                <span class="badge
                                    @if($item['record']->cak_status === 'draft') bg-secondary
                                    @elseif($item['record']->cak_status === 'generated')" style="background: #FFD700; color: #003f20;"
                                    @elseif($item['record']->cak_status === 'submitted') bg-kp-yellow
                                    @elseif($item['record']->cak_status === 'submitted_to_cak') bg-info
                                    @elseif($item['record']->cak_status === 'under_review')" style="background: #0066B3;"
                                    @elseif($item['record']->cak_status === 'approved')" style="background: #009639;"
                                    @elseif($item['record']->cak_status === 'rejected') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ strtoupper(str_replace('_', ' ', $item['record']->cak_status ?? $item['record']->status ?? 'draft')) }}
                                </span>
                            </td>

                            <td>{{ optional($item['record']->submitted_at)->format('d M Y H:i') ?? optional($item['record']->created_at)->format('d M Y') }}</td>
                            <td>
                                @if($item['record']->cak_reference_number)
                                    <code>{{ $item['record']->cak_reference_number }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route(strtolower($item['type']) . '.show', $item['record']->id) }}"
                                       class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route(strtolower($item['type']) . '.print', $item['record']->id) }}"
                                       class="btn btn-outline-dark" title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    @if(in_array($item['record']->cak_status ?? $item['record']->status ?? '', ['draft', 'generated', 'rejected']))
                                        <a href="{{ route(strtolower($item['type']) . '.edit', $item['record']->id) }}"
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(($item['record']->cak_status ?? $item['record']->status ?? '') === 'submitted')
                                        <button type="button"
                                                class="btn btn-outline-kp-primary"
                                                onclick="submitToCAK({{ $item['record']->id }}, '{{ $item['type'] }}')"
                                                title="Submit to CAK">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    @endif

                                    @if(($item['record']->cak_status ?? $item['record']->status ?? '') === 'submitted_to_cak')
                                        <button type="button"
                                                class="btn btn-outline-kp-success"
                                                onclick="checkCAKStatus({{ $item['record']->id }}, '{{ $item['type'] }}')"
                                                title="Check CAK Status">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    @endif
                                </div>
                             </td>
                         </tr>
                    @empty
                         <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No compliance returns found. Click "New Return" to begin.
                            </td>
                         </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-gavel me-1"></i> CAK Compliance Regulation 2023 — Section 42 (Return Filings)
                </small>
                <button onclick="window.print()" class="btn btn-link btn-sm" style="color: #0066B3;">
                    <i class="fas fa-print me-1"></i> Print Dashboard
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function submitToCAK(recordId, type) {
        if(confirm('Submit this return to CAK for review? This action cannot be undone.')) {
            fetch(`/${type.toLowerCase()}/${recordId}/submit-to-cak`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Return submitted to CAK successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => alert('Submission failed. Please try again.'));
        }
    }

    function checkCAKStatus(recordId, type) {
        fetch(`/${type.toLowerCase()}/${recordId}/cak-status`)
            .then(response => response.json())
            .then(data => {
                alert(`CAK Status: ${data.status}\nReference: ${data.reference_number || 'Pending'}\nLast Updated: ${data.updated_at}`);
                if(data.status !== 'under_review') {
                    location.reload();
                }
            })
            .catch(error => alert('Status check failed. Please try again.'));
    }
</script>
@endpush

@endsection
