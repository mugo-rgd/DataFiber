@extends('layouts.app')

@section('title', 'Lease Management - Admin Approval')

@section('content')
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

    {{-- Statistics Cards --}}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body py-2">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-kp-yellow text-uppercase mb-1">
                                Pending Approval
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $pendingLeases ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-lg text-gray-300"></i>
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
                                Account Managers
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $accountManagers ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-headset fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <th>Submitted On</th>
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
                                            Installation:
                                            {{ strtoupper($lease->currency ?? 'KES') }}
                                            {{ number_format($lease->installation_fee, 2) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusClass }}" style="font-size: 0.7rem;">
                                        {{ ucfirst($lease->status) }}
                                    </span>

                                    @if($isExpired && $lease->status !== 'expired')
                                        <br>
                                        <span class="badge bg-danger" style="font-size: 0.6rem;">
                                            Expired
                                        </span>
                                    @elseif($daysUntilExpiry !== null && $daysUntilExpiry < 30 && $daysUntilExpiry > 0)
                                        <br>
                                        <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">
                                            {{ $daysUntilExpiry }} days left
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <small style="font-size: 0.7rem;">
                                        {{ $lease->created_at instanceof \Carbon\Carbon
                                            ? $lease->created_at->format('M d, Y')
                                            : \Carbon\Carbon::parse($lease->created_at)->format('M d, Y') }}

                                        <br>

                                        <span class="text-muted" style="font-size: 0.6rem;">
                                            by {{ $accountManagerName }}
                                        </span>
                                    </small>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-xs" style="gap: 2px;">
                                        {{-- <a href="{{ route('admin.leases.pdf', $lease) }}"
                                           class="btn btn-outline-secondary btn-xs"
                                           title="PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a> --}}
                                        @if($lease->pdf_path)
    <a href="{{ asset('storage/' . $lease->pdf_path) }}"
       target="_blank"
       class="btn btn-outline-dark">
        <i class="fas fa-print me-1"></i>Print / Download Lease PDF
    </a>
@endif

                                        <a href="{{ route('admin.leases.show', $lease) }}"
                                           class="btn btn-outline-primary btn-xs"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($canApproveOrReject)
                                            <button type="button"
                                                    class="btn btn-outline-success btn-xs"
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
                                <td colspan="9" class="text-center py-4">
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

{{-- Modals outside the table to avoid table/modal rendering issues --}}
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
                            <i class="fas fa-check-circle me-2"></i>Approve Lease
                        </h6>

                        <button type="button"
                                class="btn-close btn-close-white btn-sm"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-2 small">
                        <p>
                            Are you sure you want to approve
                            <strong>Lease #{{ $lease->lease_number }}</strong>?
                        </p>

                        <div class="alert alert-info py-1 mb-2 small">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Details:</strong>

                            <ul class="mb-0 mt-1">
                                <li>
                                    Customer:
                                    <strong>{{ $lease->customer->name ?? 'N/A' }}</strong>
                                </li>
                                <li>
                                    Account Manager:
                                    <strong>{{ $modalAccountManagerName }}</strong>
                                </li>
                                <li>
                                    Monthly Cost:
                                    <strong>
                                        {{ strtoupper($lease->currency ?? 'KES') }}
                                        {{ number_format($lease->monthly_cost ?? 0, 2) }}
                                    </strong>
                                </li>
                                <li>
                                    Term:
                                    {{ $lease->contract_term_months ?? 'N/A' }} months
                                </li>
                            </ul>
                        </div>

                        <div class="alert alert-warning py-1 small mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Effect:</strong>
                            This will change the lease status to
                            <span class="badge bg-success">Active</span>
                            and billing will begin.
                        </div>
                    </div>

                    <div class="modal-footer py-2">
                        <button type="button"
                                class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>

                        <form action="{{ route('admin.leases.approve', $lease) }}"
      method="POST"
      class="approval-form"
      data-confirm="true">

    @csrf
    @method('PATCH')

    <button type="submit"
            class="btn btn-success btn-sm approve-btn">
        <span class="btn-text">
            <i class="fas fa-check me-1"></i>
            Yes, Approve Lease
        </span>

        <span class="btn-loading d-none">
            <i class="fas fa-spinner fa-spin me-1"></i>
            Processing...
        </span>
    </button>
</form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectModal{{ $lease->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white py-2">
                        <h6 class="modal-title">
                            <i class="fas fa-ban me-2"></i>Reject Lease
                        </h6>

                        <button type="button"
                                class="btn-close btn-close-white btn-sm"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.leases.reject', $lease) }}"
                          method="POST"
                          class="approval-form">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body py-2 small">
                            <p>
                                Are you sure you want to reject
                                <strong>Lease #{{ $lease->lease_number }}</strong>?
                            </p>

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
                            <button type="button"
                                    class="btn btn-secondary btn-sm"
                                    data-bs-dismiss="modal">
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

                if (matched) {
                    visibleRows++;
                }
            });

            const noLeasesRow = document.getElementById('noLeasesRow');

            if (noLeasesRow) {
                noLeasesRow.style.display = visibleRows === 0 ? '' : 'none';
            }
        });
    }

    document.querySelectorAll('.approval-form').forEach(form => {
        form.addEventListener('submit', function () {
            const submitButton = this.querySelector('button[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
            }
        });
    });

    setTimeout(() => {
        document.querySelectorAll('.alert.alert-dismissible').forEach(alert => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        });
    }, 5000);
});
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
</style>
@endpush
