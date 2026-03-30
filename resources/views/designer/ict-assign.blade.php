@extends('layouts.app')

@section('title', 'Assign Design Requests to Regions')

@section('content')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-drafting-compass text-primary"></i> Assign Design Requests
                    </h1>
                    <p class="text-muted">Assign design requests to regional engineers by Kenya Power region</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('designer.requests.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to My Requests
                    </a>
                    <a href="{{ route('designer.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRequestsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Assignment
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingRequestsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Available Engineers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $regionalEngineers->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Regions Covered
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($regions) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list me-2 text-primary"></i>All Design Requests - Assign by Region
                </h5>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-primary">
                        <i class="fas fa-filter me-1"></i>
                        Filter: <span id="currentFilter">All</span>
                    </span>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="filterAll">
                            All Requests
                        </button>
                        <button type="button" class="btn btn-outline-warning" id="filterPending">
                            Pending Only
                        </button>
                        <button type="button" class="btn btn-outline-info" id="filterICT">
                            ICT Only
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($pendingRequestsCount > 0)
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fs-5"></i>
                        <div>
                            <strong>You have {{ $pendingRequestsCount }} design request(s) pending assignment</strong>
                            <p class="mb-0 small">Select a Kenya Power region, choose an engineer, and assign requests. Use filters to view specific statuses.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Region Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-map me-2"></i>Select Kenya Power Region</h6>
                        </div>
                        <div class="card-body">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="regionSearch" placeholder="Search region...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    Clear
                                </button>
                            </div>
                            <div class="mt-3" style="max-height: 200px; overflow-y: auto;">
                                <div class="list-group" id="regionList">
                                    <button type="button" class="list-group-item list-group-item-action region-btn active"
                                            data-region="All">
                                        <i class="fas fa-globe me-2 text-primary"></i>
                                        All Regions
                                        <span class="badge bg-secondary float-end">
                                            {{ $totalRequestsCount }}
                                        </span>
                                    </button>
                                    @foreach($regions as $region)
                                        <button type="button" class="list-group-item list-group-item-action region-btn"
                                                data-region="{{ $region }}">
                                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                            {{ $region }}
                                            <span class="badge bg-secondary float-end">
                                                {{ $regionRequests[$region] ?? 0 }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Available Regional Engineers</h6>
                        </div>
                        <div class="card-body">
                            <select class="form-select" id="regionalEngineerSelect">
                                <option value="">Select Regional Engineer...</option>
                                @foreach($regionalEngineers as $engineer)
                                    <option value="{{ $engineer->id }}"
                                            data-region="{{ $engineer->region ?? 'All' }}"
                                            data-specialization="{{ $engineer->specialization ?? 'General' }}">
                                        {{ $engineer->name }}
                                        @if($engineer->title)
                                            ({{ $engineer->title }})
                                        @endif
                                        @if($engineer->region)
                                            - {{ $engineer->region }}
                                        @endif
                                        @if($engineer->specialization)
                                            <small class="text-muted">[{{ $engineer->specialization }}]</small>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-3" id="engineerInfo" style="display: none;">
                                <div class="alert alert-light border">
                                    <h6 class="fw-bold" id="selectedEngineerName"></h6>
                                    <p class="mb-1"><strong>Region:</strong> <span id="selectedEngineerRegion"></span></p>
                                    <p class="mb-1"><strong>Specialization:</strong> <span id="selectedEngineerSpecialization"></span></p>
                                    <p class="mb-1"><strong>Email:</strong> <span id="selectedEngineerEmail"></span></p>
                                    <p class="mb-0"><strong>Phone:</strong> <span id="selectedEngineerPhone"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="requestsTable">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th>Request #</th>
                            <th>Customer</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Region</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            @php
                                $isICT = str_contains(strtolower($request->technical_requirements), 'ict') ||
                                         str_contains(strtolower($request->title), 'ict') ||
                                         str_contains(strtolower($request->project_type), 'ict') ||
                                         str_contains(strtolower($request->tags), 'ict');
                            @endphp
                            <tr class="request-row"
                                data-region="{{ $request->region ?? 'Unknown' }}"
                                data-status="{{ $request->status }}"
                                data-ict="{{ $isICT ? '1' : '0' }}">
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input request-checkbox"
                                               type="checkbox"
                                               value="{{ $request->id }}"
                                               data-request-id="{{ $request->id }}">
                                    </div>
                                </td>
                                <td><strong>#{{ $request->request_number }}</strong></td>
                                <td>{{ $request->customer->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{ Str::limit($request->title, 40) }}
                                        @if($isICT)
                                            <span class="badge bg-purple ms-2" title="ICT Request">
                                                ICT
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $request->project_type ?? 'General' }}
                                    </span>
                                </td>
                                {{-- <td>
                                    @if($request->region)
                                        <span class="badge bg-primary">
                                            <i class="fas fa-map me-1"></i>
                                            {{ $request->county->region }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-question me-1"></i>
                                            Not Specified
                                        </span>
                                    @endif
                                </td> --}}
                                <td>
                                    <span class="badge bg-info">
                                        {{ $request->county->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->status == 'pending' ? 'warning' : 'secondary' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <!-- View Request -->
                                        <a href="{{ route('designer.requests.show', $request) }}"
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Quick Assign Button -->
                                        <button type="button"
                                                class="btn btn-outline-success assign-single-btn"
                                                data-request-id="{{ $request->id }}"
                                                title="Quick Assign">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-drafting-compass fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No design requests available for assignment.</p>
                                    <p class="text-muted small">All design requests have been assigned or there are no pending requests.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            @if($requests->count() > 0)
                <div class="card mt-4 border-primary">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Bulk Assign Selected Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold" id="selectedCount">0</span> request(s) selected
                                <span class="text-muted ms-3" id="selectedTypes"></span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="clearSelection">
                                    <i class="fas fa-times me-2"></i>Clear Selection
                                </button>
                                <button type="button" class="btn btn-success" id="bulkAssignBtn" disabled>
                                    <i class="fas fa-user-check me-2"></i>Assign Selected
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div class="text-muted">
                        Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} requests
                    </div>
                    <div class="d-flex gap-2">
                        {{ $requests->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalTitle">Assign Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignForm">
                    @csrf
                    <input type="hidden" name="request_ids" id="requestIds">
                    <input type="hidden" name="is_bulk" id="isBulk" value="0">

                    <div class="mb-3">
                        <label class="form-label">Selected Requests</label>
                        <div id="selectedRequestsList" class="alert alert-light border" style="max-height: 200px; overflow-y: auto;">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="engineer_id" class="form-label">Assign to Regional Engineer</label>
                        <select class="form-select" id="engineer_id" name="engineer_id" required>
                            <option value="">Select Regional Engineer...</option>
                            @foreach($regionalEngineers as $engineer)
                                <option value="{{ $engineer->id }}">
                                    {{ $engineer->name }} - {{ $engineer->county ? $engineer->county->name : 'No County' }}
                                    @if($engineer->title)
                                        ({{ $engineer->title }})
                                    @endif
                                    @if($engineer->region)
                                        - {{ $engineer->region }}
                                    @endif
                                    @if($engineer->specialization)
                                        [{{ $engineer->specialization }}]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="county_id" class="form-label">Select County</label>
                        <select class="form-select" id="county_id" name="county_id">
                            <option value="">Select County...</option>
                            @foreach($counties as $county)
                                <option value="{{ $county->id }}">
                                    {{ $county->name }} ({{ $county->code }})
                                    @if($county->region)
                                        - {{ $county->region }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select the county for this assignment</small>
                    </div>

                    <div class="mb-3">
                        <label for="assignment_notes" class="form-label">Assignment Notes (Optional)</label>
                        <textarea class="form-control" id="assignment_notes" name="assignment_notes"
                                  rows="3" placeholder="Add any notes for the regional engineer..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority Level</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAssign">
                    <i class="fas fa-user-check me-2"></i>Assign Request(s)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const assignModal = new bootstrap.Modal(document.getElementById('assignModal'));
    let selectedRegion = 'All';
    let currentFilter = 'All';

    // Region Search
    const regionSearch = document.getElementById('regionSearch');
    const regionList = document.getElementById('regionList');
    const clearSearchBtn = document.getElementById('clearSearch');
    const filterAllBtn = document.getElementById('filterAll');
    const filterPendingBtn = document.getElementById('filterPending');
    const filterICTBtn = document.getElementById('filterICT');
    const selectAllCheckbox = document.getElementById('selectAll');
    const bulkAssignBtn = document.getElementById('bulkAssignBtn');
    const clearSelectionBtn = document.getElementById('clearSelection');
    const confirmAssignBtn = document.getElementById('confirmAssign');

    // Cache DOM elements
    let requestCheckboxes = document.querySelectorAll('.request-checkbox');

    // Initialize
    updateSelectedCount();

    // Region Search
    if (regionSearch) {
        regionSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const regionButtons = regionList.querySelectorAll('.region-btn');

            regionButtons.forEach(btn => {
                const region = btn.getAttribute('data-region').toLowerCase();
                btn.style.display = (region.includes(searchTerm) || region === 'all') ? 'block' : 'none';
            });
        });
    }

    // Clear Search
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            regionSearch.value = '';
            regionSearch.dispatchEvent(new Event('input'));
        });
    }

    // Region Selection - using event delegation
    if (regionList) {
        regionList.addEventListener('click', function(e) {
            const regionBtn = e.target.closest('.region-btn');
            if (!regionBtn) return;

            // Remove active class from all buttons
            regionList.querySelectorAll('.region-btn').forEach(b => {
                b.classList.remove('active', 'bg-primary', 'text-white');
                b.classList.add('list-group-item-action');
            });

            // Add active class to clicked button
            regionBtn.classList.add('active', 'bg-primary', 'text-white');
            regionBtn.classList.remove('list-group-item-action');

            selectedRegion = regionBtn.getAttribute('data-region');
            filterTable();
        });
    }

    // Table Filtering
    if (filterAllBtn) {
        filterAllBtn.addEventListener('click', () => setFilter('All'));
    }

    if (filterPendingBtn) {
        filterPendingBtn.addEventListener('click', () => setFilter('Pending'));
    }

    if (filterICTBtn) {
        filterICTBtn.addEventListener('click', () => setFilter('ICT'));
    }

    function setFilter(filter) {
        currentFilter = filter;
        updateFilterDisplay();
        filterTable();
    }

    function updateFilterDisplay() {
        const currentFilterEl = document.getElementById('currentFilter');
        if (currentFilterEl) {
            currentFilterEl.textContent = currentFilter;
        }
    }

    function filterTable() {
        const rows = document.querySelectorAll('#requestsTable .request-row');

        rows.forEach(row => {
            const rowRegion = row.getAttribute('data-region');
            const rowStatus = row.getAttribute('data-status');
            const rowICT = row.getAttribute('data-ict');
            const hasCounty = row.querySelector('td:nth-child(7) .badge.bg-success') !== null;

            let showRow = true;

            // Region filter
            if (selectedRegion !== 'All') {
                if (selectedRegion === 'Unknown' || selectedRegion === 'No County') {
                    showRow = !hasCounty;
                } else {
                    showRow = (rowRegion === selectedRegion && hasCounty);
                }
            }

            // Status filter
            if (currentFilter === 'Pending' && rowStatus !== 'pending') {
                showRow = false;
            }

            // ICT filter
            if (currentFilter === 'ICT' && rowICT !== '1') {
                showRow = false;
            }

            row.style.display = showRow ? '' : 'none';
        });

        updateSelectedCount();
    }

    // Checkbox Selection
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;

            document.querySelectorAll('.request-checkbox').forEach(checkbox => {
                const row = checkbox.closest('.request-row');
                if (row && row.style.display !== 'none') {
                    checkbox.checked = isChecked;
                }
            });

            updateSelectedCount();
        });
    }

    // Refresh checkbox list when needed
    function refreshCheckboxList() {
        requestCheckboxes = document.querySelectorAll('.request-checkbox');
        requestCheckboxes.forEach(checkbox => {
            // Remove existing listener to avoid duplicates
            checkbox.removeEventListener('change', updateSelectedCount);
            checkbox.addEventListener('change', updateSelectedCount);
        });
    }

    function updateSelectedCount() {
        const selectedCheckboxes = document.querySelectorAll('.request-checkbox:checked');
        const selectedCount = selectedCheckboxes.length;
        const selectedCountEl = document.getElementById('selectedCount');
        const selectedTypesEl = document.getElementById('selectedTypes');

        if (selectedCountEl) {
            selectedCountEl.textContent = selectedCount;
        }

        if (bulkAssignBtn) {
            bulkAssignBtn.disabled = selectedCount === 0;
        }

        // Update request types summary
        let ictCount = 0;
        let generalCount = 0;

        selectedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('.request-row');
            if (row) {
                const isICT = row.getAttribute('data-ict') === '1';
                isICT ? ictCount++ : generalCount++;
            }
        });

        let typeSummary = '';
        if (ictCount > 0) {
            typeSummary += `${ictCount} ICT`;
        }
        if (generalCount > 0) {
            if (typeSummary) typeSummary += ', ';
            typeSummary += `${generalCount} General`;
        }

        if (selectedTypesEl) {
            selectedTypesEl.textContent = typeSummary ? `(${typeSummary})` : '';
        }

        // Update select all checkbox state
        if (selectAllCheckbox) {
            const visibleRows = document.querySelectorAll('#requestsTable .request-row[style=""]');
            const visibleCheckboxes = document.querySelectorAll('.request-checkbox:checked');

            const totalVisible = Array.from(visibleRows).reduce((count, row) => {
                return count + (row.querySelector('.request-checkbox') ? 1 : 0);
            }, 0);

            const visibleChecked = Array.from(visibleCheckboxes).reduce((count, checkbox) => {
                const row = checkbox.closest('.request-row');
                return count + (row && row.style.display !== 'none' ? 1 : 0);
            }, 0);

            selectAllCheckbox.checked = totalVisible > 0 && visibleChecked === totalVisible;
            selectAllCheckbox.indeterminate = visibleChecked > 0 && visibleChecked < totalVisible;
        }
    }

    // Clear Selection
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            document.querySelectorAll('.request-checkbox:checked').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        });
    }

    // Bulk Assign
    if (bulkAssignBtn) {
        bulkAssignBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.request-checkbox:checked'))
                .map(checkbox => checkbox.value)
                .filter(id => id); // Filter out null/undefined

            if (selectedIds.length === 0) return;

            showAssignModal(selectedIds, true);
        });
    }

    // Single Assign - Event delegation for dynamic content
    document.addEventListener('click', function(e) {
        const assignBtn = e.target.closest('.assign-single-btn');
        if (assignBtn) {
            e.preventDefault();
            e.stopPropagation();

            const requestId = assignBtn.getAttribute('data-request-id');
            if (requestId) {
                console.log('Assign clicked for request:', requestId);
                showAssignModal([requestId], false);
            }
        }
    });

    function showAssignModal(requestIds, isBulk) {
        console.log('Showing assign modal for IDs:', requestIds);

        const requestIdsInput = document.getElementById('requestIds');
        const isBulkInput = document.getElementById('isBulk');
        const engineerSelect = document.getElementById('engineer_id');
        const countySelect = document.getElementById('county_id');
        const notesTextarea = document.getElementById('assignment_notes');
        const prioritySelect = document.getElementById('priority');
        const requestsList = document.getElementById('selectedRequestsList');
        const modalTitle = document.getElementById('assignModalTitle');

        if (!requestIdsInput || !isBulkInput || !requestsList || !modalTitle) {
            console.error('Required modal elements not found');
            return;
        }

        requestIdsInput.value = requestIds.join(',');
        isBulkInput.value = isBulk ? '1' : '0';

        // Reset form fields
        if (engineerSelect) engineerSelect.value = '';
        if (countySelect) countySelect.value = '';
        if (notesTextarea) notesTextarea.value = '';
        if (prioritySelect) prioritySelect.value = 'normal';

        // Populate selected requests list
        requestsList.innerHTML = '';

        requestIds.forEach(id => {
            const checkbox = document.querySelector(`.request-checkbox[value="${id}"]`);
            if (!checkbox) return;

            const row = checkbox.closest('tr');
            if (!row) return;

            const requestNumber = row.querySelector('td:nth-child(2) strong')?.textContent || 'N/A';
            const title = row.querySelector('td:nth-child(4)')?.textContent.trim() || 'No title';
            const projectTypeBadge = row.querySelector('td:nth-child(5) .badge');
            const regionBadge = row.querySelector('td:nth-child(6) .badge');
            const countyBadge = row.querySelector('td:nth-child(7) .badge');

            const projectType = projectTypeBadge?.textContent || 'General';
            const region = regionBadge?.textContent || 'Unknown';
            const county = countyBadge?.textContent || 'No county';

            const requestItem = document.createElement('div');
            requestItem.className = 'mb-2 pb-2 border-bottom';
            requestItem.innerHTML = `
                <div>
                    <strong>${requestNumber}</strong> - ${title}
                    <div class="mt-1">
                        <span class="badge bg-secondary me-1">${projectType}</span>
                        <span class="badge bg-primary me-1">
                            <i class="fas fa-map me-1"></i>${region}
                        </span>
                        <span class="badge bg-info">
                            <i class="fas fa-map-marker-alt me-1"></i>${county}
                        </span>
                    </div>
                </div>
            `;
            requestsList.appendChild(requestItem);
        });

        // Update modal title
        modalTitle.textContent = isBulk
            ? `Assign ${requestIds.length} Request${requestIds.length > 1 ? 's' : ''}`
            : 'Assign Request';

        assignModal.show();
    }

    // Confirm Assignment
    if (confirmAssignBtn) {
        confirmAssignBtn.addEventListener('click', function() {
            const form = document.getElementById('assignForm');
            const engineerSelect = document.getElementById('engineer_id');

            if (!form || !engineerSelect) {
                showToast('Form elements not found', 'error');
                return;
            }

            // Validate required fields
            if (!engineerSelect.value) {
                showToast('Please select a regional engineer', 'error');
                return;
            }

            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Assigning...';

            const formData = new FormData(form);

            fetch(form.action || '{{ route("designer.requests.assignict") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    assignModal.hide();
                    showToast(data.message || 'Request(s) assigned successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Error assigning request(s)');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Error assigning request(s)', 'error');
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    }

    // Helper Functions
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 5000 });
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1055';
        document.body.appendChild(container);
        return container;
    }

    // Refresh checkbox list on table updates
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'subtree') {
                refreshCheckboxList();
            }
        });
    });

    const table = document.getElementById('requestsTable');
    if (table) {
        observer.observe(table, {
            childList: true,
            subtree: true
        });
    }

    // Initial checkbox setup
    refreshCheckboxList();
});
</script>
@endpush

@push('styles')
<style>
.region-btn:hover {
    background-color: #e9ecef;
    cursor: pointer;
}

.region-btn.active {
    background-color: #0d6efd !important;
    color: white !important;
    border-color: #0d6efd;
}

.request-row:hover {
    background-color: rgba(13, 110, 253, 0.02);
}

#regionList {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

#regionList .list-group-item {
    border: none;
    border-bottom: 1px solid #dee2e6;
    border-radius: 0;
}

#regionList .list-group-item:first-child.active {
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

#regionList .list-group-item:last-child {
    border-bottom: none;
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

.badge.bg-purple {
    background-color: #6f42c1 !important;
}

.card-header {
    font-weight: 600;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

#requestsTable th {
    font-weight: 600;
    background-color: #f8f9fa;
}

/* Region badge styling */
.badge.bg-primary {
    background-color: #0d6efd !important;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
}

.badge.bg-light {
    background-color: #f8f9fa !important;
    color: #212529 !important;
    border: 1px solid #dee2e6;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .row.mb-4 > div {
        margin-bottom: 1rem;
    }

    .btn-group {
        flex-wrap: wrap;
    }

    .btn-group .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }

    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start !important;
    }

    #requestsTable {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    #requestsTable th,
    #requestsTable td {
        padding: 0.5rem;
    }

    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
}
</style>
@endpush
