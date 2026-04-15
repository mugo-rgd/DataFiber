@extends('layouts.app')

@section('title', 'Lease Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 text-gray-800">
                        <i class="fas fa-file-contract text-primary"></i> Lease Management
                    </h1>
                    <p class="text-muted small">Manage all dark fibre lease agreements</p>
                </div>
                <a href="{{ route('admin.leases.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-2"></i>Create New Lease
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body py-2">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Leases
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $totalLeases }}</div>
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
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Leases
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $activeLeases }}</div>
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
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Leases
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $pendingLeases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
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

    <!-- Leases Table -->
    <div class="card shadow">
        <div class="card-header bg-white py-2">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.9rem;">
                        <i class="fas fa-list me-2"></i>All Leases
                    </h6>
                </div>
                <div class="col-auto position-relative">
                    <div class="d-flex gap-2">
                        <!-- Page Search (Filters current page) -->
                        <div class="input-group input-group-sm" style="width: 160px;">
                            <span class="input-group-text bg-light border-end-0 py-1">
                                <i class="fas fa-filter text-muted" style="font-size: 0.75rem;"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 py-1"
                                   style="font-size: 0.75rem;"
                                   placeholder="Filter page..."
                                   id="pageSearchInput"
                                   autocomplete="off">
                        </div>

                        <!-- Account Manager Search with Autocomplete -->
                        <div class="position-relative" style="width: 200px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 py-1">
                                    <i class="fas fa-user-tie text-muted" style="font-size: 0.75rem;"></i>
                                </span>
                                <input type="text"
                                       class="form-control border-start-0 py-1"
                                       style="font-size: 0.75rem;"
                                       placeholder="Search by manager..."
                                       id="managerSearchInput"
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary py-1" style="font-size: 0.75rem;" type="button" id="managerSearchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>

                            <!-- Account Manager Autocomplete Dropdown -->
                            <div id="managerAutocompleteContainer" class="position-absolute mt-1" style="width: 100%; z-index: 1001; display: none;">
                                <div id="managerAutocomplete" class="list-group shadow">
                                    <!-- Account managers will be listed here -->
                                </div>
                            </div>
                        </div>

                        <!-- Database Search (Searches entire database) -->
                        <div class="input-group input-group-sm" style="width: 220px;">
                            <span class="input-group-text bg-light border-end-0 py-1">
                                <i class="fas fa-database text-muted" style="font-size: 0.75rem;"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 py-1"
                                   style="font-size: 0.75rem;"
                                   placeholder="Search all fields..."
                                   id="searchInput"
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary py-1" style="font-size: 0.75rem;" type="button" id="searchBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Selected Manager Filter Badge -->
                    <div id="selectedManagerBadge" class="mt-1 small" style="display: none;">
                        <span class="badge bg-info text-white">
                            <span id="selectedManagerName"></span>
                            <button type="button" class="btn-close btn-close-white btn-xs ms-1" id="clearManagerFilter" aria-label="Clear" style="font-size: 0.5rem;"></button>
                        </span>
                    </div>

                    <!-- Search Results Dropdown (for database search) -->
                    <div id="searchResultsContainer" class="position-absolute mt-1" style="width: 350px; z-index: 1000; display: none; right: 0;">
                        <div id="searchResults" class="list-group shadow">
                            <!-- Results will be inserted here -->
                        </div>
                    </div>

                    <!-- Account Manager Search Results Dropdown -->
                    <div id="managerSearchResultsContainer" class="position-absolute mt-1" style="width: 350px; z-index: 1000; display: none; right: 380px;">
                        <div id="managerSearchResults" class="list-group shadow">
                            <!-- Manager search results will be inserted here -->
                        </div>
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
                            <th>Route</th>
                            <th>Monthly Cost</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="small">
                        @forelse($leases as $lease)
                            @php
                                // Safe date calculations
                                $startDate = $lease->start_date instanceof \Carbon\Carbon
                                    ? $lease->start_date
                                    : \Carbon\Carbon::parse($lease->start_date);

                                $endDate = $lease->end_date instanceof \Carbon\Carbon
                                    ? $lease->end_date
                                    : \Carbon\Carbon::parse($lease->end_date);

                                $isExpired = $endDate->isPast();
                                $daysUntilExpiry = now()->diffInDays($endDate, false);

                                // Status badge class
                                $statusClass = match($lease->status) {
                                    'draft' => 'secondary',
                                    'active' => 'success',
                                    'pending' => 'warning',
                                    'expired' => 'danger',
                                    'terminated' => 'dark',
                                    default => 'light'
                                };

                                // Get account manager through customer
                                $accountManager = null;
                                $accountManagerName = 'Unassigned';
                                $accountManagerEmail = '';
                                $accountManagerInitial = '?';

                                if ($lease->customer && $lease->customer->account_manager_id) {
                                    $accountManager = \App\Models\User::find($lease->customer->account_manager_id);
                                    if ($accountManager) {
                                        $accountManagerName = $accountManager->name;
                                        $accountManagerEmail = $accountManager->email;
                                        $accountManagerInitial = substr($accountManager->name, 0, 1);
                                    }
                                }
                            @endphp
                            <tr data-manager="{{ strtolower($accountManagerName) }} {{ strtolower($accountManagerEmail) }}" style="font-size: 0.8rem;">
                                <td>
                                    <strong>#{{ $lease->lease_number }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                            {{ substr($lease->customer->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.8rem;">{{ $lease->customer->name ?? 'N/A' }}</div>
                                            <small class="text-muted" style="font-size: 0.7rem;">{{ $lease->customer->email ?? 'No email' }}</small>
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
                                                <div class="fw-small" style="font-size: 0.8rem;">{{ $accountManagerName }}</div>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ $accountManagerEmail }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-user-slash me-1" style="font-size: 0.7rem;"></i>Unassigned
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;">{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</span>
                                    @if($lease->service_type == 'colocation' && $lease->host_location)
                                        <div class="mt-1">
                                            <span class="border border-primary rounded px-1 py-0 text-primary" style="font-size: 0.65rem;">
                                                <i class="fas fa-map-marker-alt me-1" style="font-size: 0.55rem;"></i>{{ strtoupper($lease->host_location) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            {{ $lease->start_location }} → {{ $lease->end_location }}
                                        </small>
                                        @if($lease->distance_km)
                                            <br><span class="text-primary" style="font-size: 0.65rem;">{{ $lease->distance_km }} km</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong style="font-size: 0.8rem;">{{ number_format($lease->monthly_cost, 2) }}</strong>
                                    <br>
                                    <small class="text-muted" style="font-size: 0.65rem;">{{ strtoupper($lease->currency) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusClass }}" style="font-size: 0.7rem;">
                                        {{ ucfirst($lease->status) }}
                                    </span>
                                    @if($isExpired && $lease->status !== 'expired')
                                        <br><span class="badge bg-warning" style="font-size: 0.65rem;">Expired</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.7rem;">
                                    {{ $startDate->format('M d, Y') }}
                                </td>
                                <td style="font-size: 0.7rem;">
                                    {{ $endDate->format('M d, Y') }}
                                    @if($isExpired)
                                        <br><small class="text-danger" style="font-size: 0.6rem;">Expired</small>
                                    @elseif($daysUntilExpiry < 30 && $daysUntilExpiry > 0)
                                        <br><small class="text-warning" style="font-size: 0.6rem;">{{ $daysUntilExpiry }} days left</small>
                                    @elseif($daysUntilExpiry <= 0)
                                        <br><small class="text-danger" style="font-size: 0.6rem;">Expired</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs" style="gap: 2px;">
                                        @if(method_exists($lease, 'generatePdf') || Route::has('admin.leases.pdf'))
                                            <a href="{{ route('admin.leases.pdf', $lease) }}" class="btn btn-outline-primary btn-xs" title="PDF Download" style="padding: 0.15rem 0.3rem; font-size: 0.7rem;">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.leases.show', $lease) }}" class="btn btn-outline-primary btn-xs" title="View" style="padding: 0.15rem 0.3rem; font-size: 0.7rem;">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(Route::has('admin.leases.edit'))
                                            <a href="{{ route('admin.leases.edit', $lease) }}" class="btn btn-outline-secondary btn-xs" title="Edit" style="padding: 0.15rem 0.3rem; font-size: 0.7rem;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        <!-- Approve Button -->
                                        @if(in_array($lease->status, ['pending', 'draft']) && Route::has('admin.leases.approve'))
                                            <button class="btn btn-outline-success btn-xs"
                                                    style="padding: 0.15rem 0.3rem; font-size: 0.7rem;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#approveModal{{ $lease->id }}"
                                                    title="Approve Lease">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        @if(Route::has('admin.leases.destroy'))
                                            <button class="btn btn-outline-danger btn-xs"
                                                    style="padding: 0.15rem 0.3rem; font-size: 0.7rem;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $lease->id }}"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Approve Modal -->
                                    @if(in_array($lease->status, ['pending', 'draft']) && Route::has('admin.leases.approve'))
                                        <div class="modal fade" id="approveModal{{ $lease->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header py-2">
                                                        <h6 class="modal-title">Approve Lease</h6>
                                                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-2 small">
                                                        <p>Are you sure you want to approve lease <strong>#{{ $lease->lease_number }}</strong>?</p>
                                                        <p class="mb-0">This will change the lease status to <span class="badge bg-success">Active</span>.</p>
                                                    </div>
                                                    <div class="modal-footer py-2">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.leases.approve', $lease) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check me-2"></i>Approve
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Delete Modal -->
                                    @if(Route::has('admin.leases.destroy'))
                                        <div class="modal fade" id="deleteModal{{ $lease->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header py-2">
                                                        <h6 class="modal-title">Confirm Delete</h6>
                                                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-2 small">
                                                        Are you sure you want to delete lease <strong>#{{ $lease->lease_number }}</strong>?
                                                        This action cannot be undone.
                                                    </div>
                                                    <div class="modal-footer py-2">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.leases.destroy', $lease) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="noLeasesRow">
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-contract fa-3x mb-2"></i>
                                        <h6>No leases found</h6>
                                        <p class="small">Get started by creating your first lease agreement.</p>
                                        <a href="{{ route('admin.leases.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus-circle me-2"></i>Create New Lease
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leases->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-2 small">
                    <div class="text-muted" style="font-size: 0.75rem;">
                        Showing {{ $leases->firstItem() }} to {{ $leases->lastItem() }} of {{ $leases->total() }} results
                    </div>
                    <div style="font-size: 0.75rem;">
                        {{ $leases->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - search functionality initializing');

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ========== PAGE SEARCH (Filter current page) ==========
    const pageSearchInput = document.getElementById('pageSearchInput');
    const tableBody = document.getElementById('tableBody');
    const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr')).filter(row => !row.id || row.id !== 'noLeasesRow') : [];
    const noLeasesRow = document.getElementById('noLeasesRow');

    console.log('Page search initialized:', {pageSearchInput, tableBody, rowCount: allRows.length});

    function filterCurrentPage(searchTerm) {
        if (!tableBody) return;

        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;

        allRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(term)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0 && term !== '') {
            if (noLeasesRow) {
                noLeasesRow.style.display = '';
                noLeasesRow.querySelector('td').innerHTML = `
                    <div class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-search fa-3x mb-2"></i>
                            <h6>No matching results on this page</h6>
                            <p class="small">Try searching by Account Manager or use the database search.</p>
                        </div>
                    </div>
                `;
            }
        } else if (noLeasesRow) {
            noLeasesRow.style.display = 'none';
            // Restore original content if it was changed
            if (noLeasesRow.querySelector('td div a') === null) {
                noLeasesRow.querySelector('td').innerHTML = `
                    <div class="text-muted">
                        <i class="fas fa-file-contract fa-3x mb-2"></i>
                        <h6>No leases found</h6>
                        <p class="small">Get started by creating your first lease agreement.</p>
                        <a href="{{ route('admin.leases.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle me-2"></i>Create New Lease
                        </a>
                    </div>
                `;
            }
        }
    }

    if (pageSearchInput) {
        pageSearchInput.addEventListener('input', debounce((e) => filterCurrentPage(e.target.value), 300));
        pageSearchInput.addEventListener('keyup', (e) => {
            if (e.target.value === '') {
                allRows.forEach(row => {
                    row.style.display = '';
                });
                if (noLeasesRow) {
                    noLeasesRow.style.display = 'none';
                }
            }
        });
    }

    // ========== DATABASE SEARCH ==========
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const resultsContainer = document.getElementById('searchResultsContainer');
    const resultsDiv = document.getElementById('searchResults');

    async function performSearch(searchTerm) {
        if (!searchTerm || searchTerm.length < 2) {
            if (resultsContainer) resultsContainer.style.display = 'none';
            return;
        }

        try {
            resultsDiv.innerHTML = '<div class="list-group-item text-center py-2"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="small">Searching...</span></div>';
            resultsContainer.style.display = 'block';

            const response = await fetch(`/api/search/leases?q=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) throw new Error(`Search failed: ${response.status}`);

            const results = await response.json();
            displaySearchResults(results);
        } catch (error) {
            console.error('Search failed:', error);
            resultsDiv.innerHTML = `<div class="list-group-item text-danger py-2 small">Search failed: ${error.message}</div>`;
        }
    }

    function displaySearchResults(results) {
        if (results.length === 0) {
            resultsDiv.innerHTML = '<div class="list-group-item text-muted py-2 small">No results found in database</div>';
            return;
        }

        let html = '';
        results.forEach(lease => {
            let statusBadge = getStatusBadge(lease.status);
            html += `
                <a href="${lease.url}" class="list-group-item list-group-item-action py-2">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 small">#${lease.lease_number}</h6>
                            <p class="mb-1 small" style="font-size: 0.7rem;">
                                <i class="fas fa-user text-muted me-1"></i>${lease.customer_name}
                            </p>
                            <p class="mb-1 small" style="font-size: 0.7rem;">
                                <i class="fas fa-user-tie text-muted me-1"></i>${lease.account_manager_name || 'Unassigned'}
                            </p>
                            <p class="mb-1 small" style="font-size: 0.7rem;">
                                <i class="fas fa-route text-muted me-1"></i>${lease.start_location} → ${lease.end_location}
                            </p>
                        </div>
                        <div class="text-end">
                            ${statusBadge}
                            <div class="mt-1 small fw-bold" style="font-size: 0.7rem;">
                                ${lease.currency || 'USD'} ${lease.monthly_cost || '0.00'}
                            </div>
                        </div>
                    </div>
                </a>
            `;
        });

        resultsDiv.innerHTML = html;
    }

    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => performSearch(e.target.value), 300));
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', () => performSearch(searchInput?.value || ''));
    }

    // ========== ACCOUNT MANAGER AUTOCOMPLETE ==========
    const managerSearchInput = document.getElementById('managerSearchInput');
    const managerAutocomplete = document.getElementById('managerAutocomplete');
    const managerAutocompleteContainer = document.getElementById('managerAutocompleteContainer');
    const selectedManagerBadge = document.getElementById('selectedManagerBadge');
    const selectedManagerName = document.getElementById('selectedManagerName');
    const managerClearFilter = document.getElementById('clearManagerFilter');
    const managerSearchBtn = document.getElementById('managerSearchBtn');
    const managerResultsContainer = document.getElementById('managerSearchResultsContainer');
    const managerResultsDiv = document.getElementById('managerSearchResults');

    let selectedManagerId = null;
    let selectedManagerNameValue = '';

    // Fetch account managers for autocomplete
    async function fetchAccountManagers(searchTerm) {
        if (!searchTerm || searchTerm.length < 1) {
            managerAutocompleteContainer.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`/api/search/account-managers?q=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) throw new Error('Failed to fetch managers');
            const managers = await response.json();
            displayManagerAutocomplete(managers);
        } catch (error) {
            console.error('Error fetching managers:', error);
            managerAutocompleteContainer.style.display = 'none';
        }
    }

    // Display manager autocomplete results
    function displayManagerAutocomplete(managers) {
        if (managers.length === 0) {
            managerAutocomplete.innerHTML = '<div class="list-group-item py-2 small text-muted">No managers found</div>';
            managerAutocompleteContainer.style.display = 'block';
            return;
        }

        let html = '';
        managers.forEach(manager => {
            html += `
                <a href="#" class="list-group-item list-group-item-action py-2 manager-option" data-manager-id="${manager.id}" data-manager-name="${manager.name}">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-info rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                             style="width: 24px; height: 24px; font-size: 0.7rem;">
                            ${manager.initial}
                        </div>
                        <div>
                            <div class="fw-small" style="font-size: 0.8rem;">${manager.name}</div>
                            <small class="text-muted" style="font-size: 0.65rem;">${manager.email}</small>
                        </div>
                    </div>
                </a>
            `;
        });

        managerAutocomplete.innerHTML = html;
        managerAutocompleteContainer.style.display = 'block';

        // Add click event to manager options
        document.querySelectorAll('.manager-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                selectManager(this.dataset.managerId, this.dataset.managerName);
            });
        });
    }

    // Select a manager
    function selectManager(managerId, managerName) {
        selectedManagerId = managerId;
        selectedManagerNameValue = managerName;

        managerSearchInput.value = managerName;
        selectedManagerName.textContent = managerName;
        selectedManagerBadge.style.display = 'block';
        managerAutocompleteContainer.style.display = 'none';

        performManagerSearchByManagerId(managerId, managerName);
    }

    // Clear manager filter
    function clearManagerFilter() {
        selectedManagerId = null;
        selectedManagerNameValue = '';
        managerSearchInput.value = '';
        selectedManagerBadge.style.display = 'none';
        managerResultsContainer.style.display = 'none';
    }

    // Perform manager search by manager ID
    async function performManagerSearchByManagerId(managerId, managerName) {
        try {
            managerResultsDiv.innerHTML = '<div class="list-group-item text-center py-2"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="small">Searching leases for ' + managerName + '...</span></div>';
            managerResultsContainer.style.display = 'block';

            const response = await fetch(`/api/search/leases/by-manager?manager_id=${managerId}`);
            if (!response.ok) throw new Error('Search failed');

            const results = await response.json();
            displayManagerSearchResults(results);
        } catch (error) {
            console.error('Manager search failed:', error);
            managerResultsDiv.innerHTML = `<div class="list-group-item text-danger py-2 small">Search failed: ${error.message}</div>`;
        }
    }

    // ========== ACCOUNT MANAGER TEXT SEARCH ==========
    async function performManagerSearch(searchTerm) {
        if (!searchTerm || searchTerm.length < 2) {
            managerResultsContainer.style.display = 'none';
            return;
        }

        try {
            managerResultsDiv.innerHTML = '<div class="list-group-item text-center py-2"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="small">Searching by manager...</span></div>';
            managerResultsContainer.style.display = 'block';

            const response = await fetch(`/api/search/leases/by-manager?q=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) throw new Error('Search failed');

            const results = await response.json();
            displayManagerSearchResults(results);
        } catch (error) {
            console.error('Manager search failed:', error);
            managerResultsDiv.innerHTML = `<div class="list-group-item text-danger py-2 small">Search failed: ${error.message}</div>`;
        }
    }

    // Display manager search results
    function displayManagerSearchResults(results) {
        if (results.length === 0) {
            managerResultsDiv.innerHTML = '<div class="list-group-item text-muted py-2 small">No leases found for this manager</div>';
            return;
        }

        let html = '';
        results.forEach(lease => {
            let statusBadge = getStatusBadge(lease.status);
            html += `
                <a href="${lease.url}" class="list-group-item list-group-item-action py-2">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 small">#${lease.lease_number}</h6>
                            <p class="mb-1 small" style="font-size: 0.7rem;">
                                <i class="fas fa-user text-muted me-1"></i>${lease.customer_name || 'N/A'}
                            </p>
                            <p class="mb-1 small" style="font-size: 0.7rem;">
                                <i class="fas fa-tag text-muted me-1"></i>${lease.service_type ? lease.service_type.replace(/_/g, ' ') : 'N/A'}
                            </p>
                            <p class="mb-1 small" style="font-size: 0.7rem;">
                                <i class="fas fa-map-marker-alt text-muted me-1"></i>${lease.start_location || 'N/A'} → ${lease.end_location || 'N/A'}
                            </p>
                        </div>
                        <div class="text-end">
                            ${statusBadge}
                            <div class="mt-1 small fw-bold" style="font-size: 0.7rem;">
                                ${lease.currency || 'USD'} ${lease.monthly_cost || '0.00'}
                            </div>
                        </div>
                    </div>
                    <div class="small text-muted mt-1" style="font-size: 0.65rem;">
                        <i class="fas fa-user-tie me-1"></i>Account Manager: <strong>${lease.account_manager_name || 'Unassigned'}</strong>
                    </div>
                </a>
            `;
        });

        managerResultsDiv.innerHTML = html;
    }

    function getStatusBadge(status) {
        switch(status) {
            case 'active': return '<span class="badge bg-success" style="font-size: 0.65rem;">Active</span>';
            case 'pending': return '<span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Pending</span>';
            case 'draft': return '<span class="badge bg-secondary" style="font-size: 0.65rem;">Draft</span>';
            case 'expired': return '<span class="badge bg-danger" style="font-size: 0.65rem;">Expired</span>';
            default: return `<span class="badge bg-light text-dark" style="font-size: 0.65rem;">${status || 'N/A'}</span>`;
        }
    }

    // Manager autocomplete event listeners
    if (managerSearchInput) {
        managerSearchInput.addEventListener('input', debounce((e) => {
            const value = e.target.value;
            if (value !== selectedManagerNameValue) {
                if (selectedManagerId) {
                    clearManagerFilter();
                }
                if (value.length >= 1) {
                    fetchAccountManagers(value);
                } else {
                    managerAutocompleteContainer.style.display = 'none';
                }
            }
        }, 300));

        managerSearchInput.addEventListener('focus', function() {
            if (this.value.length >= 1 && !selectedManagerId) {
                fetchAccountManagers(this.value);
            }
        });

        managerSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                managerAutocompleteContainer.style.display = 'none';
            }
        });
    }

    // Manager search button
    if (managerSearchBtn) {
        managerSearchBtn.addEventListener('click', function() {
            if (selectedManagerId) {
                performManagerSearchByManagerId(selectedManagerId, selectedManagerNameValue);
            } else if (managerSearchInput.value.length >= 2) {
                performManagerSearch(managerSearchInput.value);
            } else {
                alert('Please enter at least 2 characters or select a manager');
            }
        });
    }

    // Clear manager filter
    if (managerClearFilter) {
        managerClearFilter.addEventListener('click', clearManagerFilter);
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (managerAutocompleteContainer &&
            !managerSearchInput?.contains(event.target) &&
            !managerAutocompleteContainer.contains(event.target)) {
            managerAutocompleteContainer.style.display = 'none';
        }

        if (resultsContainer &&
            !searchInput?.contains(event.target) &&
            !resultsContainer.contains(event.target)) {
            resultsContainer.style.display = 'none';
        }

        if (managerResultsContainer &&
            !managerSearchInput?.contains(event.target) &&
            !managerResultsContainer.contains(event.target)) {
            managerResultsContainer.style.display = 'none';
        }
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush

@push('styles')
<style>
#searchResultsContainer, #managerSearchResultsContainer {
    max-height: 350px;
    overflow-y: auto;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    font-size: 0.8rem;
    background: white;
}

#searchResults .list-group-item,
#managerSearchResults .list-group-item {
    padding: 0.4rem 0.6rem;
    border-left: none;
    border-right: none;
    font-size: 0.75rem;
}

#searchResults .list-group-item:first-child,
#managerSearchResults .list-group-item:first-child {
    border-top: none;
}

#searchResults .list-group-item:last-child,
#managerSearchResults .list-group-item:last-child {
    border-bottom: none;
}

#searchResults .list-group-item:hover,
#managerSearchResults .list-group-item:hover {
    background-color: #f8f9fc;
}

/* Manager autocomplete styling */
#managerAutocompleteContainer {
    max-height: 250px;
    overflow-y: auto;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    background: white;
    z-index: 1001;
}

#managerAutocomplete .list-group-item {
    padding: 0.4rem 0.6rem;
    border-left: none;
    border-right: none;
    cursor: pointer;
    font-size: 0.75rem;
}

#managerAutocomplete .list-group-item:hover {
    background-color: #f8f9fc;
}

/* Selected manager badge */
#selectedManagerBadge .badge {
    padding: 0.35rem 0.5rem;
    font-weight: normal;
}

#selectedManagerBadge .btn-close-white {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    font-size: 0.5rem;
}

#selectedManagerBadge .btn-close-white:hover {
    opacity: 1;
}

.gap-2 { gap: 0.35rem; }
.position-relative { position: relative; }
.position-absolute { position: absolute; }

/* Input group styling */
.input-group-text.bg-light {
    background-color: #f8f9fc;
    border-right: none;
    padding: 0.2rem 0.5rem;
}

.input-group .form-control.border-start-0 {
    border-left: none;
}

.input-group .form-control.border-start-0:focus {
    box-shadow: none;
    border-color: #ced4da;
}

/* Table styling */
.table-sm > :not(caption) > * > * {
    padding: 0.3rem 0.3rem;
}

/* Button group xs size */
.btn-group-xs > .btn, .btn-xs {
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
    border-radius: 0.2rem;
}

/* Avatar sizes */
.avatar-sm {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* Pagination styling */
.pagination {
    margin-bottom: 0;
}

.page-link {
    padding: 0.2rem 0.5rem;
    font-size: 0.75rem;
}
</style>
@endpush
