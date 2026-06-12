@extends('layouts.app')

@section('title', 'Lease Management - Admin Approval')

@section('content')

@php
    $inconsistentLeases = $leases->filter(function($lease) {
        return $lease->status === 'active' && is_null($lease->approved_at);
    });
@endphp

@if($inconsistentLeases->count() > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Warning!</strong>
    {{ $inconsistentLeases->count() }} lease(s) are active but missing approval data.

    @can('admin')
    <button type="button" class="btn btn-sm btn-warning ms-2" onclick="fixInconsistentLeases()">
        <i class="fas fa-tools me-1"></i>Fix Now
    </button>
    @endcan

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

@push('scripts')
<script>
function fixInconsistentLeases() {
    if (confirm('This will fix {{ $inconsistentLeases->count() }} lease(s). Continue?')) {
        fetch('{{ route("admin.leases.fix-inconsistent") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to fix leases. Please run php artisan leases:fix-inconsistent');
        });
    }
}
</script>
@endpush
@endif
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-contract text-kp-blue"></i>
                        Lease Management
                    </h1>
                    <p class="text-muted mb-0">
                        Review and approve lease agreements submitted by account managers
                    </p>
                </div>

                <div class="btn-group">
                    <a href="{{ route('admin.leases.create') }}"
                       class="btn btn-kp-primary btn-sm disabled"
                       aria-disabled="true">
                        <i class="fas fa-plus-circle me-2"></i>Create New Lease
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body py-2">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-kp-blue text-uppercase mb-1">
                                Total Leases
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $totalLeases ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body py-2">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-kp-green text-uppercase mb-1">
                                Active Leases
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $activeLeases ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body py-2">
            <div class="row no-gutters align-items-center">
                <div class="col">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Avg. Approval Time
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        @php
                            $avgTime = 0;
                            try {
                                $avgTime = \App\Models\Lease::whereNotNull('approved_at')
                                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours')
                                    ->value('avg_hours') ?? 0;
                            } catch (\Exception $e) {
                                $avgTime = 0;
                            }
                        @endphp
                        {{ round($avgTime) }} hours
                    </div>
                    <small class="text-muted">Target: < 24 hours</small>
                </div>
                <div class="col-auto">
                    <i class="fas fa-chart-line fa-lg text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body py-2">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg. Approval Time
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $avgTime = \App\Models\Lease::whereNotNull('approved_at')
                                        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours')
                                        ->value('avg_hours');
                                @endphp
                                {{ round($avgTime ?? 0) }} hours
                            </div>
                            <small class="text-muted">Target: < 24 hours</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action Banner for Pending Leases --}}
    @if(request('status') == 'pending' && ($pendingLeases ?? 0) > 0)
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            <i class="fas fa-clock me-1"></i>
                            {{ $pendingLeases }} lease(s) awaiting your decision
                        </div>
                        <div>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary me-2"
                                    id="autoRefreshToggle">
                                <i class="fas fa-sync-alt me-1"></i>
                                Auto-refresh: <span id="refreshStatus">ON</span>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#batchApproveModal">
                                <i class="fas fa-check-double me-1"></i>
                                Batch Approve All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-kp-success alert-dismissible fade show py-2" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <small>{{ session('success') }}</small>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <small>{{ session('error') }}</small>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Status Filter Tabs --}}
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == '' ? 'active' : '' }}"
                       href="{{ route('admin.leases.index') }}">
                        <i class="fas fa-list me-1"></i>All Leases
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}"
                       href="{{ route('admin.leases.index', ['status' => 'pending']) }}">
                        <i class="fas fa-clock text-warning me-1"></i>Pending Approval
                        @if(($pendingLeases ?? 0) > 0)
                            <span class="badge bg-warning text-dark ms-1">
                                {{ $pendingLeases }}
                            </span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'active' ? 'active' : '' }}"
                       href="{{ route('admin.leases.index', ['status' => 'active']) }}">
                        <i class="fas fa-check-circle text-success me-1"></i>Active
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'draft' ? 'active' : '' }}"
                       href="{{ route('admin.leases.index', ['status' => 'draft']) }}">
                        <i class="fas fa-pen text-secondary me-1"></i>Draft
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'terminated' ? 'active' : '' }}"
                       href="{{ route('admin.leases.index', ['status' => 'terminated']) }}">
                        <i class="fas fa-ban text-danger me-1"></i>Terminated
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Leases Table --}}
    <div class="card shadow">
        <div class="card-header bg-white py-2">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-kp-blue" style="font-size: 0.9rem;">
                        <i class="fas fa-list me-2"></i>
                        @if(request('status') == 'pending')
                            Leases Pending Approval
                        @elseif(request('status') == 'active')
                            Active Leases
                        @elseif(request('status') == 'draft')
                            Draft Leases
                        @elseif(request('status') == 'terminated')
                            Terminated Leases
                        @else
                            All Leases
                        @endif
                        <span class="badge bg-secondary ms-1">
                            {{ $leases->total() }}
                        </span>
                    </h6>
                </div>

                <div class="col-auto">
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <span class="input-group-text bg-light border-end-0 py-1">
                            <i class="fas fa-search text-muted" style="font-size: 0.75rem;"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0 py-1"
                               placeholder="Search lease, customer, manager..."
                               id="searchInput"
                               style="font-size: 0.75rem;">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="small">
                        <tr>
                            <th>Lease #</th>
                            <th>Customer</th>
                            <th>Account Manager</th>
                            <th>Service Type</th>
                            <th>Route/Location</th>
                            <th>Monthly Cost</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Waiting Time</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        @forelse($leases as $lease)
                            @php
                                $startDate = $lease->start_date
                                    ? ($lease->start_date instanceof \Carbon\Carbon ? $lease->start_date : \Carbon\Carbon::parse($lease->start_date))
                                    : null;

                                $endDate = $lease->end_date
                                    ? ($lease->end_date instanceof \Carbon\Carbon ? $lease->end_date : \Carbon\Carbon::parse($lease->end_date))
                                    : null;

                                $isExpired = $endDate ? $endDate->isPast() : false;
                                $daysUntilExpiry = $endDate ? now()->diffInDays($endDate, false) : null;

                                $statusClass = match($lease->status) {
                                    'draft' => 'secondary',
                                    'active' => 'success',
                                    'pending' => 'warning',
                                    'expired' => 'danger',
                                    'terminated' => 'dark',
                                    'rejected' => 'danger',
                                    default => 'light'
                                };

                                $accountManager = null;
                                $accountManagerName = 'Unassigned';
                                $accountManagerEmail = '';
                                $accountManagerInitial = '?';

                                if ($lease->customer && $lease->customer->account_manager_id) {
                                    $accountManager = \App\Models\User::find($lease->customer->account_manager_id);
                                    if ($accountManager) {
                                        $accountManagerName = $accountManager->name;
                                        $accountManagerEmail = $accountManager->email;
                                        $accountManagerInitial = strtoupper(substr($accountManager->name, 0, 1));
                                    }
                                }

                                $canApproveOrReject = in_array($lease->status, ['pending', 'draft']);

                                $submittedDate = $lease->created_at instanceof \Carbon\Carbon
                                    ? $lease->created_at
                                    : \Carbon\Carbon::parse($lease->created_at);
                                $hoursWaiting = $submittedDate->diffInHours(now());
                                $waitingClass = $hoursWaiting > 48 ? 'danger' : ($hoursWaiting > 24 ? 'warning' : 'secondary');
                            @endphp

                            <tr class="lease-row" style="font-size: 0.8rem;">
                                <td>
                                    <strong>#{{ $lease->lease_number }}</strong>
                                    @if($lease->status === 'pending')
                                        <span class="badge bg-warning text-dark d-block mt-1" style="font-size: 0.6rem;">
                                            Awaiting Approval
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-kp-blue rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                                             style="width: 24px; height: 24px; font-size: 0.7rem;">
                                            {{ strtoupper(substr($lease->customer->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.8rem;">
                                                {{ $lease->customer->name ?? 'N/A' }}
                                            </div>
                                            <small class="text-muted" style="font-size: 0.65rem;">
                                                {{ $lease->customer->email ?? 'No email' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($accountManager)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                                                 style="width: 22px; height: 22px; font-size: 0.65rem;">
                                                {{ $accountManagerInitial }}
                                            </div>
                                            <div>
                                                <div style="font-size: 0.75rem;">
                                                    {{ $accountManagerName }}
                                                </div>
                                                <small class="text-muted" style="font-size: 0.6rem;">
                                                    {{ $accountManagerEmail }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            <i class="fas fa-user-slash me-1" style="font-size: 0.65rem;"></i>
                                            Unassigned
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                        {{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}
                                    </span>
                                    @if($lease->service_type == 'colocation' && $lease->host_location)
                                        <div class="mt-1">
                                            <span class="border border-info rounded px-1 py-0 text-info" style="font-size: 0.6rem;">
                                                <i class="fas fa-building me-1"></i>
                                                {{ strtoupper($lease->host_location) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($lease->technology)
                                        <div class="mt-1">
                                            <span class="text-muted" style="font-size: 0.6rem;">
                                                <i class="fas fa-microchip me-1"></i>
                                                {{ strtoupper($lease->technology) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if($lease->service_type == 'colocation')
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $lease->host_location ?? 'N/A' }}
                                        </small>
                                    @else
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            {{ $lease->start_location ?? 'N/A' }}
                                            →
                                            {{ $lease->end_location ?? 'N/A' }}
                                        </small>
                                        @if($lease->distance_km)
                                            <br>
                                            <span class="text-kp-blue" style="font-size: 0.65rem;">
                                                {{ $lease->distance_km }} km
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <td>
                                    <strong style="font-size: 0.8rem;">
                                        {{ strtoupper($lease->currency ?? 'KES') }}
                                        {{ number_format($lease->monthly_cost ?? 0, 2) }}
                                    </strong>
                                    @if(($lease->installation_fee ?? 0) > 0)
                                        <br>
                                        <small class="text-muted" style="font-size: 0.6rem;">
                                            Installation: {{ strtoupper($lease->currency ?? 'KES') }} {{ number_format($lease->installation_fee, 2) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusClass }}" style="font-size: 0.7rem;">
                                        {{ ucfirst($lease->status) }}
                                    </span>
                                    @if($isExpired && $lease->status !== 'expired')
                                        <br>
                                        <span class="badge bg-danger" style="font-size: 0.6rem;">Expired</span>
                                    @elseif($daysUntilExpiry !== null && $daysUntilExpiry < 30 && $daysUntilExpiry > 0)
                                        <br>
                                        <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">
                                            {{ $daysUntilExpiry }} days left
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <small style="font-size: 0.7rem;">
                                        {{ $submittedDate->format('M d, Y') }}
                                        <br>
                                        <span class="text-muted" style="font-size: 0.6rem;">
                                            by {{ $accountManagerName }}
                                        </span>
                                    </small>
                                </td>

                               <td>
    <div class="text-nowrap">
        @php
            $hoursWaiting = (int) $submittedDate->diffInHours(now());
            $waitingClass = $hoursWaiting > 48 ? 'danger' : ($hoursWaiting > 24 ? 'warning' : 'secondary');
        @endphp
        <span class="badge bg-{{ $waitingClass }}" style="font-size: 0.7rem;">
            <i class="fas fa-hourglass-half me-1"></i>
            {{ $hoursWaiting }}h
        </span>
        @if($hoursWaiting > 48)
            <span class="badge bg-danger d-block mt-1" style="font-size: 0.6rem;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Overdue!
            </span>
        @elseif($hoursWaiting > 24)
            <span class="badge bg-warning text-dark d-block mt-1" style="font-size: 0.6rem;">
                <i class="fas fa-clock me-1"></i>
                Urgent!
            </span>
        @endif
    </div>
</td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-xs" style="gap: 2px;">
                                        @if($lease->pdf_path)
                                            <a href="{{ asset('storage/' . $lease->pdf_path) }}"
                                               target="_blank"
                                               class="btn btn-outline-dark btn-xs"
                                               title="Print / Download PDF">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.leases.show', $lease) }}"
                                           class="btn btn-outline-primary btn-xs"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($canApproveOrReject)
                                            <button type="button"
                                                    class="btn btn-outline-success btn-xs approve-btn-trigger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#approveModal{{ $lease->id }}"
                                                    title="Approve Lease">
                                                <i class="fas fa-check"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-outline-danger btn-xs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $lease->id }}"
                                                    title="Reject Lease">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        <button type="button"
                                                class="btn btn-outline-secondary btn-xs disabled"
                                                disabled
                                                title="Edit disabled - Admin approval only">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noLeasesRow">
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-contract fa-3x mb-2"></i>
                                        <h6>No leases found</h6>
                                        <p class="small mb-0">
                                            @if(request('status') == 'pending')
                                                No leases pending approval.
                                            @elseif(request('status') == 'active')
                                                No active leases found.
                                            @else
                                                All leases are up to date.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($leases->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-2 small">
                    <div class="text-muted" style="font-size: 0.75rem;">
                        Showing {{ $leases->firstItem() }} to {{ $leases->lastItem() }} of {{ $leases->total() }} results
                    </div>
                    <div style="font-size: 0.75rem;">
                        {{ $leases->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Batch Approve Modal --}}
<div class="modal fade" id="batchApproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title">
                    <i class="fas fa-check-double me-2"></i>
                    Batch Approve All Pending Leases
                </h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                <p>Are you sure you want to approve all <strong>{{ $pendingLeases }}</strong> pending leases?</p>

                <div class="alert alert-warning py-1 small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    This action cannot be undone. Please review the list below:
                </div>

                <div class="list-group small" style="max-height: 300px; overflow-y: auto;">
                    @foreach($leases->where('status', 'pending') as $pending)
                        <div class="list-group-item py-1">
                            <strong>#{{ $pending->lease_number }}</strong>
                            - {{ $pending->customer->name ?? 'N/A' }}
                            ({{ ucfirst($pending->service_type) }})
                            - {{ number_format($pending->monthly_cost ?? 0, 2) }}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer py-2">
                <form action="{{ route('admin.leases.batch-approve') }}" method="POST" id="batchApproveForm">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check me-1"></i>
                        Approve All ({{ $pendingLeases }})
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Individual Approve/Reject Modals --}}
@foreach($leases as $lease)
    @php
        $canApproveOrReject = in_array($lease->status, ['pending', 'draft']);
        $modalAccountManager = null;
        $modalAccountManagerName = 'Unassigned';

        if ($lease->customer && $lease->customer->account_manager_id) {
            $modalAccountManager = \App\Models\User::find($lease->customer->account_manager_id);
            $modalAccountManagerName = $modalAccountManager->name ?? 'Unassigned';
        }
    @endphp

    @if($canApproveOrReject)
        {{-- Approve Modal --}}
        <div class="modal fade" id="approveModal{{ $lease->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white py-2">
                        <h6 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>Approve Lease #{{ $lease->lease_number }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.leases.approve', $lease) }}" method="POST" class="approval-form">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body py-2 small">
                            <p>Are you sure you want to approve <strong>Lease #{{ $lease->lease_number }}</strong>?</p>

                            {{-- Approval Checklist --}}
                            <div class="alert alert-info py-1 mb-2 small">
                                <i class="fas fa-clipboard-list me-1"></i>
                                <strong>Approval Checklist:</strong>

                                <div class="row mt-2">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input check-item" type="checkbox" id="check_docs_{{ $lease->id }}">
                                            <label class="form-check-label small" for="check_docs_{{ $lease->id }}">
                                                ✓ Documents verified
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input check-item" type="checkbox" id="check_pricing_{{ $lease->id }}">
                                            <label class="form-check-label small" for="check_pricing_{{ $lease->id }}">
                                                ✓ Pricing approved
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input check-item" type="checkbox" id="check_capacity_{{ $lease->id }}">
                                            <label class="form-check-label small" for="check_capacity_{{ $lease->id }}">
                                                ✓ Capacity available
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input check-item" type="checkbox" id="check_compliance_{{ $lease->id }}">
                                            <label class="form-check-label small" for="check_compliance_{{ $lease->id }}">
                                                ✓ Compliance check
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning py-1 small mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Effect:</strong> This will change the lease status to
                                <span class="badge bg-success">Active</span> and billing will begin.
                            </div>
                        </div>

                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-sm approve-submit">
                                <span class="btn-text">
                                    <i class="fas fa-check me-1"></i>
                                    Yes, Approve Lease
                                </span>
                                <span class="btn-loading d-none">
                                    <i class="fas fa-spinner fa-spin me-1"></i>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectModal{{ $lease->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white py-2">
                        <h6 class="modal-title">
                            <i class="fas fa-ban me-2"></i>Reject Lease #{{ $lease->lease_number }}
                        </h6>
                        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.leases.reject', $lease) }}" method="POST" class="approval-form">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body py-2 small">
                            <p>Are you sure you want to reject <strong>Lease #{{ $lease->lease_number }}</strong>?</p>

                            <div class="mb-2">
                                <label for="rejection_reason_{{ $lease->id }}" class="form-label">
                                    Rejection Reason <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control form-control-sm"
                                          id="rejection_reason_{{ $lease->id }}"
                                          name="rejection_reason"
                                          rows="3"
                                          required
                                          placeholder="Please provide a reason for rejection..."></textarea>
                            </div>

                            <div class="alert alert-warning py-1 small mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                The account manager will be notified of this rejection.
                            </div>
                        </div>

                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-ban me-1"></i>Yes, Reject Lease
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');

    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('tr.lease-row');
            let visibleRows = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matched = text.includes(searchTerm);
                row.style.display = matched ? '' : 'none';
                if (matched) visibleRows++;
            });

            const noLeasesRow = document.getElementById('noLeasesRow');
            if (noLeasesRow) {
                noLeasesRow.style.display = visibleRows === 0 ? '' : 'none';
            }
        });
    }

    // ==================== APPROVAL HANDLING WITH PAGE REFRESH ====================

    // Handle approve/reject form submissions
    document.querySelectorAll('.approval-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitButton = this.querySelector('button[type="submit"]');
            const modal = this.closest('.modal');
            const originalHtml = submitButton.innerHTML;
            const isApprove = this.action.includes('approve');
            const actionText = isApprove ? 'approving' : 'rejecting';

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';

            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: this.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Close modal properly
                    if (modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }

                    // Clean up modal backdrops
                    setTimeout(() => {
                        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                            backdrop.remove();
                        });
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }, 100);

                    // Show success message
                    showToast(data.message || `Lease ${isApprove ? 'approved' : 'rejected'} successfully! Refreshing page...`, 'success');

                    // Refresh the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);

                } else {
                    // Show error message
                    showToast(data.message || 'Processing failed', 'error');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalHtml;
                }

            } catch (error) {
                console.error('Error:', error);
                showToast('Network error. Please try again.', 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = originalHtml;
            }
        });
    });

    // ==================== BATCH APPROVE WITH PAGE REFRESH ====================

    const batchForm = document.getElementById('batchApproveForm');
    if (batchForm) {
        batchForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const modal = document.getElementById('batchApproveModal');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing all leases...';

            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: this.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Close modal
                    if (modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) bsModal.hide();
                    }

                    // Clean up backdrops
                    setTimeout(() => {
                        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                    }, 100);

                    showToast(data.message || 'Batch approval completed! Refreshing page...', 'success');

                    // Refresh the page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    showToast(data.message || 'Batch approval failed', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }

            } catch (error) {
                console.error('Error:', error);
                showToast('Network error. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // ==================== TOAST NOTIFICATION ====================

    function showToast(message, type = 'success') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.custom-toast');
        existingToasts.forEach(toast => toast.remove());

        const bgColor = type === 'success' ? 'success' : (type === 'error' ? 'danger' : 'info');
        const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle');

        const toastHtml = `
            <div class="custom-toast position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
                <div class="toast align-items-center text-white bg-${bgColor} border-0 show" role="alert" data-bs-autohide="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${icon} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', toastHtml);

        // Auto remove after 3 seconds
        setTimeout(() => {
            const toast = document.querySelector('.custom-toast');
            if (toast) toast.remove();
        }, 3000);
    }

    // ==================== KEYBOARD SHORTCUTS ====================

    let shortcutListener = function(e) {
        // Check if user is typing in an input/textarea
        const activeElement = document.activeElement;
        const isTyping = activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA' || activeElement.isContentEditable);

        if (isTyping) return;

        // Ctrl + Shift + A: Approve first pending
        if (e.ctrlKey && e.shiftKey && e.key === 'A') {
            e.preventDefault();
            const firstApproveBtn = document.querySelector('.lease-row:visible .btn-outline-success');
            if (firstApproveBtn) {
                firstApproveBtn.click();
                showToast('Opening approve modal...', 'info');
            } else {
                showToast('No pending leases to approve', 'info');
            }
        }

        // Ctrl + Shift + R: Reject first pending
        if (e.ctrlKey && e.shiftKey && e.key === 'R') {
            e.preventDefault();
            const firstRejectBtn = document.querySelector('.lease-row:visible .btn-outline-danger');
            if (firstRejectBtn) {
                firstRejectBtn.click();
                showToast('Opening reject modal...', 'info');
            } else {
                showToast('No pending leases to reject', 'info');
            }
        }

        // Ctrl + Shift + B: Open batch approve
        if (e.ctrlKey && e.shiftKey && e.key === 'B') {
            e.preventDefault();
            const batchBtn = document.querySelector('[data-bs-target="#batchApproveModal"]');
            if (batchBtn) {
                batchBtn.click();
                showToast('Opening batch approve modal...', 'info');
            }
        }

        // Escape key: Close any open modal
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const bsModal = bootstrap.Modal.getInstance(openModal);
                if (bsModal) bsModal.hide();
            }
        }
    };

    document.addEventListener('keydown', shortcutListener);

    // ==================== AUTO-REFRESH (Disabled when page will refresh anyway) ====================

    let autoRefreshEnabled = true;
    let refreshInterval = null;

    function startAutoRefresh() {
        if (refreshInterval) clearInterval(refreshInterval);

        refreshInterval = setInterval(() => {
            // Only auto-refresh on pending tab and if not processing an approval
            if (autoRefreshEnabled && window.location.href.includes('status=pending')) {
                const openModal = document.querySelector('.modal.show');
                if (openModal) return;

                fetch(window.location.href + '&ajax=1', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newPendingCount = doc.querySelector('.border-left-warning .h6')?.textContent;

                    // Update pending count badge without full refresh
                    if (newPendingCount) {
                        const pendingBadge = document.querySelector('.nav-link[href*="status=pending"] .badge');
                        if (pendingBadge && pendingBadge.textContent !== newPendingCount) {
                            pendingBadge.textContent = newPendingCount;
                            if (newPendingCount === '0') {
                                pendingBadge.style.display = 'none';
                            } else {
                                pendingBadge.style.display = '';
                            }
                        }
                    }
                })
                .catch(error => console.error('Auto-refresh error:', error));
            }
        }, 30000);
    }

    const refreshToggle = document.getElementById('autoRefreshToggle');
    const refreshStatus = document.getElementById('refreshStatus');

    if (refreshToggle) {
        startAutoRefresh();

        refreshToggle.addEventListener('click', function() {
            autoRefreshEnabled = !autoRefreshEnabled;
            refreshStatus.textContent = autoRefreshEnabled ? 'ON' : 'OFF';
            refreshStatus.className = autoRefreshEnabled ? 'text-success fw-bold' : 'text-danger fw-bold';

            if (!autoRefreshEnabled && refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
                showToast('Auto-refresh disabled', 'info');
            } else if (autoRefreshEnabled && !refreshInterval) {
                startAutoRefresh();
                showToast('Auto-refresh enabled', 'success');
            }
        });
    }

    // ==================== CLEAN UP ALERTS ====================

    setTimeout(() => {
        document.querySelectorAll('.alert.alert-dismissible').forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) bsAlert.close();
            }, 4000);
        });
    }, 1000);

    // ==================== SHOW SHORTCUT HELP ====================

    if (!localStorage.getItem('shortcutsShown')) {
        setTimeout(() => {
            showToast('💡 Tip: Use Ctrl+Shift+A to approve, Ctrl+Shift+R to reject, Ctrl+Shift+B for batch approve!', 'info');
            localStorage.setItem('shortcutsShown', 'true');
        }, 2000);
    }

    // ==================== MODAL BACKDROP CLEANUP ====================

    // Clean up any lingering modals when page loads
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';

    // Watch for modal hidden events to clean up
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    });

    // ==================== PREVENT DOUBLE SUBMISSION ====================

    // Add loading indicator to page during refresh
    window.addEventListener('beforeunload', function() {
        // Optional: Show loading overlay
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'page-loading-overlay';
        loadingOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        `;
        loadingOverlay.innerHTML = `
            <div class="text-center">
                <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                <p>Refreshing page...</p>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    });
});

// Add CSRF token meta tag if not present
if (!document.querySelector('meta[name="csrf-token"]')) {
    const meta = document.createElement('meta');
    meta.name = 'csrf-token';
    meta.content = '{{ csrf_token() }}';
    document.head.appendChild(meta);
}
</script>
@endpush

@push('styles')
<style>
.btn-group-xs > .btn,
.btn-xs {
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    border-radius: 0.2rem;
}

.avatar-sm {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.nav-tabs .nav-link {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}

.nav-tabs .nav-link.active {
    font-weight: 600;
}

.table-sm > :not(caption) > * > * {
    padding: 0.5rem 0.3rem;
    vertical-align: middle;
}

.badge {
    transition: all 0.2s ease;
}

.modal.fade .modal-dialog {
    transition: transform 0.2s ease-out;
}

#searchInput:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.lease-row {
    transition: background-color 0.2s ease;
}

.lease-row:hover {
    background-color: rgba(78, 115, 223, 0.05);
}

.btn-xs i {
    font-size: 0.7rem;
}

.text-nowrap {
    white-space: nowrap;
}

.list-group-item {
    font-size: 0.75rem;
}

/* Toast animations */
.toast {
    opacity: 0;
    animation: slideIn 0.3s ease forwards;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.7rem;
    }

    .btn-xs {
        padding: 0.15rem 0.3rem;
    }

    .modal-backdrop {
    z-index: 1040 !important;
}

.modal {
    z-index: 1050 !important;
}

.btn-group-xs > .btn,
.btn-xs {
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    border-radius: 0.2rem;
}

.custom-toast {
    z-index: 9999;
}

.toast {
    min-width: 300px;
}

#page-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-family: Arial, sans-serif;
}

#page-loading-overlay .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
}
</style>
@endpush
