@extends('layouts.app')

@section('title', 'Assign Design Requests to ICT Engineers')

@section('content')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-microchip text-kp-blue"></i> Assign Design Requests to ICT Engineers
                    </h1>
                    <p class="text-muted">Assign design requests to ICT engineers based on their region</p>
                </div>
                <div>
                    <a href="{{ route('designer.requests.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to My Requests
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
                            <div class="text-xs font-weight-bold text-kp-blue text-uppercase mb-1">Total Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRequestsCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-list fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-kp-yellow text-uppercase mb-1">Pending Assignment</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingRequestsCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-kp-green text-uppercase mb-1">ICT Engineers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $regionalEngineers->count() ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Regions Covered</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($regions ?? []) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-map-marker-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content - Simplified Two Column Layout -->
    <div class="row">
        <!-- Left Column: Engineers by Region -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-kp-green text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>ICT Engineers by Region</h5>
                </div>
                <div class="card-body p-0">
                    <div class="accordion" id="engineersAccordion">
                        @foreach($engineersByRegion ?? [] as $region => $engineers)
                            @if(count($engineers) > 0)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ !$loop->first ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($region) }}">
                                            <div class="d-flex justify-content-between w-100 me-3">
                                                <span><i class="fas fa-map-marker-alt me-2 text-kp-blue"></i>{{ $region }}</span>
                                                <span class="badge bg-secondary">{{ count($engineers) }} engineers</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ Str::slug($region) }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#engineersAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="list-group list-group-flush">
                                                @foreach($engineers as $engineer)
                                                    <div class="list-group-item engineer-item" data-engineer-id="{{ $engineer->id }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $engineer->name }}</strong>
                                                                <br>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-building me-1"></i>{{ $engineer->county_name ?? 'No County' }}
                                                                    @if($engineer->specialization)
                                                                        | <i class="fas fa-tag me-1"></i>{{ $engineer->specialization }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-outline-kp-primary select-engineer-btn" data-engineer-id="{{ $engineer->id }}" data-engineer-name="{{ $engineer->name }}" data-engineer-region="{{ $region }}">
                                                                <i class="fas fa-check-circle me-1"></i>Select
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Pending Requests -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-kp-blue text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Pending Design Requests</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchRequests" placeholder="Search requests...">
                        </div>
                        <select class="form-select form-select-sm" id="regionFilter" style="width: 180px;">
                            <option value="">All Regions</option>
                            @foreach($regions ?? [] as $region)
                                <option value="{{ $region }}">{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($pendingRequestsCount > 0)
                        <div class="alert alert-info alert-sm mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Select an engineer from the left panel, then check the requests to assign.
                        </div>
                    @endif

                    <!-- Selected Engineer Info -->
                    <div id="selectedEngineerPanel" class="alert alert-success d-none mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-check me-2"></i>
                                <strong>Selected Engineer:</strong> <span id="selectedEngineerNameDisplay">-</span>
                                <span class="badge bg-kp-blue ms-2" id="selectedEngineerRegionDisplay"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearEngineerSelection">
                                <i class="fas fa-times me-1"></i>Change
                            </button>
                        </div>
                    </div>

                    <!-- Requests Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="requestsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllPending">
                                        </div>
                                    </th>
                                    <th>Request #</th>
                                    <th>Customer</th>
                                    <th>Title</th>
                                    <th>County</th>
                                    <th>Region</th>
                                    <th>Requested</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests ?? [] as $request)
                                    @if(!$request->assigned_ict_engineer_id)
                                        @php
                                            $requestRegion = $request->county->region ?? 'Unknown';
                                        @endphp
                                        <tr class="request-row" data-region="{{ $requestRegion }}" data-request-id="{{ $request->id }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input request-checkbox" type="checkbox" value="{{ $request->id }}" data-region="{{ $requestRegion }}">
                                                </div>
                                            </td>
                                            <td><strong>{{ $request->request_number }}</strong></td>
                                            <td>{{ $request->customer->name ?? 'N/A' }}</td>
                                            <td>{{ Str::limit($request->title, 50) }}</td>
                                            <td>
                                                @if($request->county)
                                                    <span class="badge bg-info">{{ $request->county->name }}</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Not Specified</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($requestRegion != 'Unknown')
                                                    <span class="badge bg-kp-blue">{{ $requestRegion }}</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Unknown</span>
                                                @endif
                                            </td>
                                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                            <p class="text-muted">No pending design requests for ICT assignment.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions -->
                    @if($pendingRequestsCount > 0)
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <span class="fw-bold" id="selectedCount">0</span> request(s) selected
                                    <span class="text-muted ms-2" id="selectedRegionsInfo"></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSelectionBtn">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </button>
                                    <button type="button" class="btn btn-kp-success btn-sm" id="assignSelectedBtn" disabled>
                                        <i class="fas fa-user-check me-1"></i>Assign to Selected Engineer
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmAssignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-kp-blue text-white">
                <h5 class="modal-title">Confirm Assignment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You are about to assign the following request(s) to:</p>
                <div class="alert alert-info">
                    <strong><i class="fas fa-user-tie me-2"></i><span id="confirmEngineerName"></span></strong>
                    <br>
                    <small id="confirmEngineerRegion"></small>
                </div>
                <div id="confirmRequestsList" class="mb-3" style="max-height: 200px; overflow-y: auto;"></div>
                <div class="mb-3">
                    <label class="form-label">Assignment Notes (Optional)</label>
                    <textarea id="assignmentNotes" class="form-control" rows="2" placeholder="Add any notes for the engineer..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <select id="assignmentPriority" class="form-select">
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-kp-primary" id="confirmAssignBtn">
                    <i class="fas fa-check me-2"></i>Confirm Assignment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // State
    let selectedEngineerId = null;
    let selectedEngineerName = null;
    let selectedEngineerRegion = null;
    let selectedRequestIds = [];

    // DOM Elements
    const searchInput = document.getElementById('searchRequests');
    const regionFilter = document.getElementById('regionFilter');
    const selectAllPending = document.getElementById('selectAllPending');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectedRegionsInfo = document.getElementById('selectedRegionsInfo');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const assignSelectedBtn = document.getElementById('assignSelectedBtn');
    const selectedEngineerPanel = document.getElementById('selectedEngineerPanel');
    const selectedEngineerNameDisplay = document.getElementById('selectedEngineerNameDisplay');
    const selectedEngineerRegionDisplay = document.getElementById('selectedEngineerRegionDisplay');
    const clearEngineerSelection = document.getElementById('clearEngineerSelection');
    const confirmAssignModal = new bootstrap.Modal(document.getElementById('confirmAssignModal'));
    const confirmEngineerName = document.getElementById('confirmEngineerName');
    const confirmEngineerRegion = document.getElementById('confirmEngineerRegion');
    const confirmRequestsList = document.getElementById('confirmRequestsList');
    const confirmAssignBtn = document.getElementById('confirmAssignBtn');

    // Helper function to check if an element is visible
    function isElementVisible(element) {
        if (!element) return false;
        const style = window.getComputedStyle(element);
        return style.display !== 'none' && style.visibility !== 'hidden';
    }

    // Helper function to get visible checkboxes
    function getVisibleCheckboxes() {
        const checkboxes = [];
        document.querySelectorAll('.request-checkbox').forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (row && isElementVisible(row)) {
                checkboxes.push(checkbox);
            }
        });
        return checkboxes;
    }

    // Engineer Selection
    document.querySelectorAll('.select-engineer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedEngineerId = this.dataset.engineerId;
            selectedEngineerName = this.dataset.engineerName;
            selectedEngineerRegion = this.dataset.engineerRegion;

            selectedEngineerNameDisplay.textContent = selectedEngineerName;
            selectedEngineerRegionDisplay.textContent = selectedEngineerRegion;
            selectedEngineerPanel.classList.remove('d-none');

            // Highlight selected engineer in list
            document.querySelectorAll('.engineer-item').forEach(item => {
                item.classList.remove('bg-light', 'border-primary');
            });
            this.closest('.engineer-item')?.classList.add('bg-light', 'border-primary');

            updateAssignButtonState();
        });
    });

    // Clear Engineer Selection
    clearEngineerSelection.addEventListener('click', function() {
        selectedEngineerId = null;
        selectedEngineerName = null;
        selectedEngineerRegion = null;
        selectedEngineerPanel.classList.add('d-none');

        document.querySelectorAll('.engineer-item').forEach(item => {
            item.classList.remove('bg-light', 'border-primary');
        });

        updateAssignButtonState();
    });

    // Request Checkbox Handling
    function updateSelectedRequests() {
        selectedRequestIds = [];
        document.querySelectorAll('.request-checkbox').forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (checkbox.checked && row && isElementVisible(row)) {
                selectedRequestIds.push(checkbox.value);
            }
        });

        selectedCountSpan.textContent = selectedRequestIds.length;

        // Show region info
        const regions = [];
        document.querySelectorAll('.request-checkbox:checked').forEach(checkbox => {
            const region = checkbox.dataset.region;
            if (region && !regions.includes(region)) {
                regions.push(region);
            }
        });
        selectedRegionsInfo.textContent = regions.length > 0 ? `(${regions.join(', ')})` : '';

        updateAssignButtonState();
    }

    function updateAssignButtonState() {
        assignSelectedBtn.disabled = !selectedEngineerId || selectedRequestIds.length === 0;
    }

    // Select All - FIXED: Remove :visible selector
    if (selectAllPending) {
        selectAllPending.addEventListener('change', function() {
            const isChecked = this.checked;
            getVisibleCheckboxes().forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateSelectedRequests();
        });
    }

    // Clear Selection
    clearSelectionBtn.addEventListener('click', function() {
        document.querySelectorAll('.request-checkbox:checked').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedRequests();
    });

    // Assign Button
    assignSelectedBtn.addEventListener('click', function() {
        if (!selectedEngineerId || selectedRequestIds.length === 0) return;

        // Get selected request details
        const requests = [];
        document.querySelectorAll('.request-checkbox:checked').forEach(checkbox => {
            const row = checkbox.closest('tr');
            const requestNumber = row.querySelector('td:nth-child(2) strong')?.textContent || 'N/A';
            const title = row.querySelector('td:nth-child(4)')?.textContent.trim() || 'No title';
            requests.push({ id: checkbox.value, number: requestNumber, title: title.substring(0, 60) });
        });

        // Populate modal
        confirmEngineerName.textContent = selectedEngineerName;
        confirmEngineerRegion.textContent = selectedEngineerRegion;

        confirmRequestsList.innerHTML = '';
        requests.forEach(req => {
            const div = document.createElement('div');
            div.className = 'mb-2 pb-2 border-bottom';
            div.innerHTML = `<strong>${req.number}</strong> - ${req.title}`;
            confirmRequestsList.appendChild(div);
        });

        confirmAssignModal.show();
    });

    // Confirm Assignment
    confirmAssignBtn.addEventListener('click', function() {
        const notes = document.getElementById('assignmentNotes').value;
        const priority = document.getElementById('assignmentPriority').value;

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Assigning...';

        // Create FormData for better compatibility
        const formData = new FormData();
        formData.append('request_ids', selectedRequestIds.join(','));
        formData.append('engineer_id', selectedEngineerId);
        formData.append('notes', notes);
        formData.append('priority', priority);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("designer.requests.assignict") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                confirmAssignModal.hide();
                showToast(data.message || 'Request(s) assigned successfully!', 'success');

                // Remove assigned rows from table
                selectedRequestIds.forEach(id => {
                    const row = document.querySelector(`tr[data-request-id="${id}"]`);
                    if (row) row.remove();
                });

                // Reset selection
                selectedRequestIds = [];
                updateSelectedRequests();

                // Update counts
                const remainingRows = document.querySelectorAll('#requestsTable tbody tr');
                const visibleRows = Array.from(remainingRows).filter(row => isElementVisible(row));
                if (visibleRows.length === 0) {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                throw new Error(data.message || 'Assignment failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast(error.message || 'Error assigning request(s)', 'error');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Assignment';
        });
    });

    // Search and Filter
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRegion = regionFilter.value;

        let visibleCount = 0;
        document.querySelectorAll('#requestsTable tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            const region = row.dataset.region;

            const matchesSearch = !searchTerm || text.includes(searchTerm);
            const matchesRegion = !selectedRegion || region === selectedRegion;

            const isVisible = matchesSearch && matchesRegion;
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Update select all state
        if (selectAllPending) {
            const visibleCheckboxes = getVisibleCheckboxes();
            const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);
            const someChecked = visibleCheckboxes.some(cb => cb.checked);

            selectAllPending.checked = allChecked;
            selectAllPending.indeterminate = someChecked && !allChecked;
        }

        updateSelectedRequests();
    }

    searchInput.addEventListener('input', filterTable);
    regionFilter.addEventListener('change', filterTable);

    // Event delegation for checkboxes
    document.querySelector('#requestsTable tbody')?.addEventListener('change', function(e) {
        if (e.target.classList.contains('request-checkbox')) {
            updateSelectedRequests();

            // Update select all state
            if (selectAllPending) {
                const visibleCheckboxes = getVisibleCheckboxes();
                const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);
                const someChecked = visibleCheckboxes.some(cb => cb.checked);

                selectAllPending.checked = allChecked;
                selectAllPending.indeterminate = someChecked && !allChecked;
            }
        }
    });

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 mb-2`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        container.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 5000 });
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    // Initial update
    updateSelectedRequests();
});
</script>
@endpush

@push('styles')
<style>
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
}

.border-left-primary { border-left: 4px solid var(--kp-blue); }
.border-left-warning { border-left: 4px solid var(--kp-yellow); }
.border-left-success { border-left: 4px solid var(--kp-green); }
.border-left-info { border-left: 4px solid #17a2b8; }

.bg-kp-blue { background-color: var(--kp-blue) !important; }
.bg-kp-green { background-color: var(--kp-green) !important; }
.text-kp-blue { color: var(--kp-blue) !important; }

.engineer-item {
    cursor: pointer;
    transition: all 0.2s ease;
}

.engineer-item:hover {
    background-color: #f8f9fa;
}

.engineer-item.bg-light.border-primary {
    background-color: #e8f4fd !important;
    border-left: 3px solid var(--kp-blue) !important;
}

.accordion-button:not(.collapsed) {
    background-color: #e8f4fd;
    color: var(--kp-blue);
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,102,179,0.25);
}

.request-row:hover {
    background-color: rgba(0,102,179,0.02);
}

#selectedEngineerPanel {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .accordion-button {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    .table-responsive {
        font-size: 0.85rem;
    }
}
</style>
@endpush
