{{-- resources/views/maintenance/requests-index.blade.php --}}
@extends('layouts.app')

@section('title', 'Maintenance Requests')

@section('styles')
<style>
    .btn-calculate-compensation {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: #fff;
        border: none;
    }

    .btn-calculate-compensation:hover {
        opacity: .9;
        color: #fff;
    }

    .compensation-amount {
        font-size: 2rem;
        font-weight: bold;
        color: #28a745;
    }

    .calculation-result {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
    }

    .currency-usd {
        color: #28a745;
    }

    .currency-ksh {
        color: #fd7e14;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">
                <i class="fas fa-tools me-2"></i>Maintenance Requests
            </h5>

            @can('create-maintenance-request')
                <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> New Request
                </a>
            @endcan
        </div>

        <div class="card-body">

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <select class="form-select" id="statusFilter">
                        <option value="all">All Status</option>
                        <option value="open">Open</option>
                        <option value="assigned">Assigned</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-select" id="priorityFilter">
                        <option value="all">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select class="form-select" id="typeFilter">
                        <option value="all">All Types</option>
                        <option value="fibre_cut">Fibre Cut</option>
                        <option value="equipment_failure">Equipment Failure</option>
                        <option value="signal_degradation">Signal Degradation</option>
                        <option value="power_issue">Power Issue</option>
                        <option value="environmental">Environmental</option>
                        <option value="preventive_maintenance">Preventive Maintenance</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control" id="searchFilter" placeholder="Search by title, description, lease...">
                </div>
            </div>

            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Request #</th>
                                <th>Title</th>
                                <th>Lease</th>
                                <th>Priority</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Reported By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($requests as $request)
                                @php
                                    $estimatedHours = match($request->priority) {
                                        'critical' => 4,
                                        'high' => 8,
                                        'medium' => 24,
                                        'low' => 48,
                                        default => 12
                                    };

                                    // Get data from lease relationship
                                    $monthlyCost = $request->lease ? $request->lease->monthly_cost : 0;
                                    $currency = $request->lease ? $request->lease->currency : 'USD';
                                    $leaseNumber = $request->lease ? $request->lease->lease_number : 'No Lease';
                                    $leaseTitle = $request->lease ? ($request->lease->title ?? 'Untitled') : 'No Lease Assigned';
                                    $leaseId = $request->lease ? $request->lease->id : null;

                                    // Build search string
                                    $searchString = strtolower($request->title . ' ' . ($request->description ?? '') . ' ' . $leaseNumber . ' ' . $leaseTitle);
                                @endphp

                                <tr class="request-row"
                                    data-status="{{ $request->status }}"
                                    data-priority="{{ $request->priority }}"
                                    data-type="{{ $request->issue_type }}"
                                    data-search="{{ $searchString }}"
                                    data-request-id="{{ $request->id }}"
                                    data-request-number="{{ $request->request_number }}"
                                    data-monthly-cost="{{ $monthlyCost }}"
                                    data-currency="{{ $currency }}"
                                    data-estimated-hours="{{ $estimatedHours }}"
                                    data-lease-number="{{ $leaseNumber }}"
                                    data-lease-title="{{ $leaseTitle }}"
                                    data-lease-id="{{ $leaseId }}">

                                    <td>
                                        <strong>{{ $request->request_number }}</strong>
                                    </td>

                                    <td>
                                        <strong>{{ $request->title }}</strong><br>
                                        <small class="text-muted">
                                            {{ Str::limit($request->description ?? '', 50) }}
                                        </small>
                                    </td>

                                    <td>
                                        @if($request->lease)
                                            <strong>{{ $leaseNumber }}</strong><br>
                                            <small class="text-muted">{{ $leaseTitle }}</small><br>
                                            <small class="text-{{ $currency === 'USD' ? 'success' : 'warning' }}">
                                                <i class="fas fa-{{ $currency === 'USD' ? 'dollar-sign' : 'shilling-sign' }}"></i>
                                                {{ $currency }} {{ number_format($monthlyCost, 2) }}/mo
                                            </small>
                                        @else
                                            <span class="text-muted">No lease assigned</span>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $priorityClass = match($request->priority) {
                                                'critical' => 'dark',
                                                'high' => 'danger',
                                                'medium' => 'warning',
                                                'low' => 'secondary',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $priorityClass }}">
                                            {{ ucfirst($request->priority) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-info">
                                            {{ str_replace('_', ' ', ucfirst($request->issue_type)) }}
                                        </span>
                                    </td>

                                    <td>
                                        @php
                                            $statusClass = match($request->status) {
                                                'open' => 'warning',
                                                'assigned' => 'info',
                                                'in_progress' => 'primary',
                                                'resolved' => 'success',
                                                'closed' => 'secondary',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $request->reporter->name ?? 'System' }}
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($request->reported_at ?? $request->created_at)->format('M j, Y') }}
                                    </td>

                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('maintenance.requests.show', $request->id) }}"
                                               class="btn btn-outline-primary"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <button type="button"
                                                    class="btn btn-calculate-compensation"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#compensationModal"
                                                    data-request-number="{{ $request->request_number }}"
                                                    data-monthly-cost="{{ $monthlyCost }}"
                                                    data-currency="{{ $currency }}"
                                                    data-estimated-hours="{{ $estimatedHours }}"
                                                    data-lease-number="{{ $leaseNumber }}"
                                                    data-lease-title="{{ $leaseTitle }}"
                                                    data-lease-id="{{ $leaseId }}"
                                                    title="Calculate Compensation">
                                                <i class="fas fa-calculator"></i>
                                                <span class="d-none d-md-inline ms-1">Compensation</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($requests->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                    <h4>No Maintenance Requests Found</h4>
                    <p class="text-muted">There are no maintenance requests matching your criteria.</p>
                    @can('create-maintenance-request')
                        <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus-circle me-1"></i> Create First Request
                        </a>
                    @endcan
                </div>
            @endif

        </div>
    </div>
</div>

<!-- Compensation Modal -->
<div class="modal fade"
     id="compensationModal"
     tabindex="-1"
     aria-labelledby="compensationModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="compensationModalLabel">
                    <i class="fas fa-calculator me-2"></i>Downtime Compensation Calculator
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="requestInfo" class="alert alert-info mb-3">
                    Select a request to calculate compensation.
                </div>

                <!-- Lease Selector (if multiple leases on same request) -->
                <div class="mb-3" id="leaseSelectorContainer" style="display:none;">
                    <label class="form-label">Select Lease</label>
                    <select class="form-select" id="modalLeaseSelector">
                        <option value="">-- Select Lease --</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        If this request has multiple leases, select the affected lease
                    </small>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Monthly Service Fee</label>
                        <div class="input-group">
                            <span class="input-group-text" id="modalCurrencySymbol">USD</span>
                            <input type="number"
                                   class="form-control"
                                   id="modalMonthlyFee"
                                   value="0"
                                   step="0.01"
                                   placeholder="Enter monthly fee">
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Downtime Start</label>
                        <input type="datetime-local"
                               class="form-control"
                               id="modalDowntimeStart">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Downtime End</label>
                        <input type="datetime-local"
                               class="form-control"
                               id="modalDowntimeEnd">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SLA Guarantee (%)</label>
                        <select class="form-select" id="modalSlaGuarantee">
                            <option value="99.99">99.99% Standard</option>
                            <option value="99.95">99.95% Premium</option>
                            <option value="99.9">99.9% Basic</option>
                            <option value="99.5">99.5% Economy</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Credit Rate (%)</label>
                        <select class="form-select" id="modalCreditRate">
                            <option value="5">5% Standard credit</option>
                            <option value="10">10% High impact</option>
                            <option value="25">25% Severe impact</option>
                            <option value="50">50% Critical impact</option>
                            <option value="100">100% Full refund</option>
                        </select>
                    </div>
                </div>

                <button type="button" class="btn btn-primary w-100" id="calculateBtn">
                    <i class="fas fa-calculator me-2"></i>Calculate Compensation
                </button>

                <div id="calculationResult" class="calculation-result mt-3" style="display:none;">
                    <div class="text-center">
                        <small class="text-muted">Calculated Refund Amount</small>
                        <h3 id="resultAmount" class="compensation-amount mb-0">USD 0.00</h3>
                        <small id="resultDetails" class="text-muted"></small>
                        <hr>
                        <div id="resultBreakdown" class="text-start small"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>

                <button type="button"
                        class="btn btn-success"
                        id="copyBtn">
                    <i class="fas fa-copy me-1"></i>Copy to Clipboard
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        // Global variables
        let currentRequest = {
            number: '',
            monthlyCost: 0,
            currency: 'USD',
            estimatedHours: 12,
            leaseNumber: '',
            leaseTitle: '',
            leaseId: null
        };

        let lastResult = null;
        let lastTriggerButton = null;

        // Store all leases from the table rows for selection
        let availableLeases = [];

        // Filter function
        function filterRequests() {
            const statusFilter = document.getElementById('statusFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;
            const typeFilter = document.getElementById('typeFilter').value;
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();

            document.querySelectorAll('.request-row').forEach(function(row) {
                const status = row.getAttribute('data-status');
                const priority = row.getAttribute('data-priority');
                const type = row.getAttribute('data-type');
                const search = (row.getAttribute('data-search') || '').toLowerCase();

                const statusMatch = statusFilter === 'all' || status === statusFilter;
                const priorityMatch = priorityFilter === 'all' || priority === priorityFilter;
                const typeMatch = typeFilter === 'all' || type === typeFilter;
                const searchMatch = searchFilter === '' || search.includes(searchFilter);

                row.style.display = (statusMatch && priorityMatch && typeMatch && searchMatch) ? '' : 'none';
            });
        }

        // Format date for datetime-local input
        function formatDateTimeLocal(date) {
            const offset = date.getTimezoneOffset();
            const localDate = new Date(date.getTime() - offset * 60000);
            return localDate.toISOString().slice(0, 16);
        }

        // Escape HTML
        function escapeHtml(value) {
            if (!value) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // Get currency symbol
        function getCurrencySymbol(currency) {
            return currency === 'USD' ? '$' : 'KSh';
        }

        // Build available leases from all request rows
        function buildAvailableLeases() {
            availableLeases = [];
            document.querySelectorAll('.request-row').forEach(function(row) {
                const leaseNumber = row.getAttribute('data-lease-number');
                const leaseTitle = row.getAttribute('data-lease-title');
                const monthlyCost = parseFloat(row.getAttribute('data-monthly-cost') || 0);
                const currency = row.getAttribute('data-currency') || 'USD';
                const requestNumber = row.getAttribute('data-request-number') || '';
                const leaseId = row.getAttribute('data-lease-id');

                if (leaseNumber && leaseNumber !== 'No Lease') {
                    // Check if lease already exists in array
                    const existingLease = availableLeases.find(l => l.number === leaseNumber);
                    if (!existingLease) {
                        availableLeases.push({
                            id: leaseId,
                            number: leaseNumber,
                            title: leaseTitle,
                            monthlyCost: monthlyCost,
                            currency: currency,
                            requestNumber: requestNumber
                        });
                    }
                }
            });

            console.log('Available leases:', availableLeases);
        }

        // Update lease selector dropdown
        function updateLeaseSelector(selectedLeaseNumber = null) {
            const leaseSelector = document.getElementById('modalLeaseSelector');
            const leaseSelectorContainer = document.getElementById('leaseSelectorContainer');

            if (!leaseSelector) return;

            // Clear existing options
            leaseSelector.innerHTML = '<option value="">-- Select Lease --</option>';

            if (availableLeases.length > 1) {
                // Show selector if multiple leases
                leaseSelectorContainer.style.display = 'block';

                availableLeases.forEach(lease => {
                    const option = document.createElement('option');
                    option.value = lease.number;
                    option.textContent = `${lease.number} - ${lease.title} (${lease.currency} ${lease.monthlyCost.toFixed(2)}/mo)`;
                    option.setAttribute('data-monthly-cost', lease.monthlyCost);
                    option.setAttribute('data-currency', lease.currency);
                    option.setAttribute('data-lease-title', lease.title);
                    option.setAttribute('data-lease-id', lease.id);

                    if (selectedLeaseNumber && lease.number === selectedLeaseNumber) {
                        option.selected = true;
                    }

                    leaseSelector.appendChild(option);
                });
            } else if (availableLeases.length === 1) {
                // Hide selector if only one lease
                leaseSelectorContainer.style.display = 'none';
            } else {
                leaseSelectorContainer.style.display = 'none';
            }
        }

        // Handle lease selection change
        function onLeaseChange() {
            const leaseSelector = document.getElementById('modalLeaseSelector');
            const selectedOption = leaseSelector.options[leaseSelector.selectedIndex];

            if (selectedOption && selectedOption.value) {
                const monthlyCost = parseFloat(selectedOption.getAttribute('data-monthly-cost') || 0);
                const currency = selectedOption.getAttribute('data-currency') || 'USD';
                const leaseTitle = selectedOption.getAttribute('data-lease-title') || '';
                const leaseNumber = selectedOption.value;
                const leaseId = selectedOption.getAttribute('data-lease-id');
                const currencySymbol = getCurrencySymbol(currency);

                // Update the monthly fee field
                document.getElementById('modalMonthlyFee').value = monthlyCost;
                document.getElementById('modalCurrencySymbol').innerHTML = currencySymbol;
                document.getElementById('modalCurrencySymbol').setAttribute('data-currency-code', currency);

                // Update currentRequest
                currentRequest.monthlyCost = monthlyCost;
                currentRequest.currency = currency;
                currentRequest.leaseTitle = leaseTitle;
                currentRequest.leaseNumber = leaseNumber;
                currentRequest.leaseId = leaseId;

                // Update request info display
                updateRequestInfoDisplay();
            }
        }

        // Update request info display
        function updateRequestInfoDisplay() {
            const currency = currentRequest.currency;
            const currencyClass = currency === 'USD' ? 'text-success' : 'text-warning';
            const currencyIcon = currency === 'USD' ? 'dollar-sign' : 'shilling-sign';

            document.getElementById('requestInfo').innerHTML = `
                <strong>Request #${escapeHtml(currentRequest.number)}</strong><br>
                Lease: <strong>${escapeHtml(currentRequest.leaseNumber)}</strong><br>
                <small class="text-muted">${escapeHtml(currentRequest.leaseTitle)}</small><br>
                Monthly Fee: <span class="${currencyClass}"><i class="fas fa-${currencyIcon}"></i> ${currency} ${currentRequest.monthlyCost.toFixed(2)}</span><br>
                Estimated Downtime: ${currentRequest.estimatedHours} hours
                ${currentRequest.monthlyCost === 0 ? '<br><span class="text-warning">⚠️ No monthly cost set. Please enter manually.</span>' : ''}
            `;
        }

        // Prepare modal with request data
        function prepareCompensationModal() {
            const currency = currentRequest.currency;
            const currencySymbol = getCurrencySymbol(currency);

            // Set monthly fee from the selected request
            document.getElementById('modalMonthlyFee').value = currentRequest.monthlyCost;
            document.getElementById('modalCurrencySymbol').innerHTML = currencySymbol;
            document.getElementById('modalCurrencySymbol').setAttribute('data-currency-code', currency);

            // Set default downtime start and end based on estimated hours
            const startTime = new Date();
            const endTime = new Date(startTime.getTime() + currentRequest.estimatedHours * 60 * 60 * 1000);

            document.getElementById('modalDowntimeStart').value = formatDateTimeLocal(startTime);
            document.getElementById('modalDowntimeEnd').value = formatDateTimeLocal(endTime);

            updateRequestInfoDisplay();

            document.getElementById('calculationResult').style.display = 'none';
            lastResult = null;

            // Update lease selector with current lease
            updateLeaseSelector(currentRequest.leaseNumber);
        }

        // Calculate compensation
        function calculateCompensation() {
            const monthlyFee = parseFloat(document.getElementById('modalMonthlyFee').value || 0);
            const startValue = document.getElementById('modalDowntimeStart').value;
            const endValue = document.getElementById('modalDowntimeEnd').value;
            const slaGuarantee = parseFloat(document.getElementById('modalSlaGuarantee').value) / 100;
            const creditRate = parseFloat(document.getElementById('modalCreditRate').value) / 100;
            const currencySymbol = document.getElementById('modalCurrencySymbol').innerHTML;
            const currencyCode = document.getElementById('modalCurrencySymbol').getAttribute('data-currency-code') || 'USD';

            if (monthlyFee <= 0) {
                alert('Please enter a valid monthly service fee.');
                return;
            }

            if (!startValue || !endValue) {
                alert('Please select both downtime start and downtime end.');
                return;
            }

            const startDate = new Date(startValue);
            const endDate = new Date(endValue);

            if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                alert('Invalid date values. Please select valid dates.');
                return;
            }

            if (endDate <= startDate) {
                alert('Downtime end must be after downtime start.');
                return;
            }

            const downtimeHours = (endDate - startDate) / (1000 * 60 * 60);
            const monthlyHours = 720; // 30 days * 24 hours
            const actualAvailability = (monthlyHours - downtimeHours) / monthlyHours;
            const slaViolated = actualAvailability < slaGuarantee;

            let compensationAmount = slaViolated
                ? monthlyFee * creditRate
                : monthlyFee * (downtimeHours / monthlyHours);

            compensationAmount = Math.min(compensationAmount, monthlyFee);

            lastResult = {
                amount: compensationAmount,
                currencySymbol: currencySymbol,
                currencyCode: currencyCode,
                monthlyFee: monthlyFee,
                downtimeHours: downtimeHours,
                actualAvailability: actualAvailability,
                slaGuarantee: slaGuarantee,
                slaViolated: slaViolated,
                creditRate: creditRate
            };

            document.getElementById('resultAmount').innerHTML = `${currencySymbol} ${compensationAmount.toFixed(2)}`;
            document.getElementById('resultDetails').innerHTML = slaViolated ? 'SLA violation compensation' : 'Pro-rated downtime compensation';
            document.getElementById('resultBreakdown').innerHTML = `
                <strong>Calculation Breakdown:</strong><br>
                Monthly Fee: ${currencySymbol} ${monthlyFee.toFixed(2)} (${currencyCode})<br>
                Downtime: ${downtimeHours.toFixed(2)} hours<br>
                Actual Availability: ${(actualAvailability * 100).toFixed(4)}%<br>
                SLA Target: ${(slaGuarantee * 100).toFixed(2)}%<br>
                SLA Violated: ${slaViolated ? 'Yes' : 'No'}<br>
                Credit Rate: ${(creditRate * 100).toFixed(0)}%<br>
                <strong>Compensation: ${currencySymbol} ${compensationAmount.toFixed(2)}</strong>
            `;

            document.getElementById('calculationResult').style.display = 'block';
        }

        // Copy compensation to clipboard
        function copyCompensation() {
            if (!lastResult) {
                alert('Please calculate compensation first.');
                return;
            }

            const note = `DOWNTIME COMPENSATION CALCULATION
Request #: ${currentRequest.number}
Lease: ${currentRequest.leaseNumber} - ${currentRequest.leaseTitle}
Monthly Fee: ${lastResult.currencyCode} ${lastResult.monthlyFee.toFixed(2)}
Downtime: ${lastResult.downtimeHours.toFixed(2)} hours
Actual Availability: ${(lastResult.actualAvailability * 100).toFixed(4)}%
SLA Target: ${(lastResult.slaGuarantee * 100).toFixed(2)}%
SLA Violated: ${lastResult.slaViolated ? 'Yes' : 'No'}
Credit Rate: ${(lastResult.creditRate * 100).toFixed(0)}%
Calculated Refund: ${lastResult.currencyCode} ${lastResult.amount.toFixed(2)}
Calculation Date: ${new Date().toLocaleString()}`;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(note)
                    .then(function() {
                        alert('✅ Compensation details copied to clipboard!');
                    })
                    .catch(function() {
                        alert('⚠️ Could not copy. Please copy manually:\n\n' + note);
                    });
            } else {
                alert('Please copy manually:\n\n' + note);
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const compensationModal = document.getElementById('compensationModal');
            const calculateBtn = document.getElementById('calculateBtn');
            const copyBtn = document.getElementById('copyBtn');
            const leaseSelector = document.getElementById('modalLeaseSelector');

            // Build available leases from table
            buildAvailableLeases();

            // Attach filter event listeners
            const statusFilter = document.getElementById('statusFilter');
            const priorityFilter = document.getElementById('priorityFilter');
            const typeFilter = document.getElementById('typeFilter');
            const searchFilter = document.getElementById('searchFilter');

            if (statusFilter) statusFilter.addEventListener('change', filterRequests);
            if (priorityFilter) priorityFilter.addEventListener('change', filterRequests);
            if (typeFilter) typeFilter.addEventListener('change', filterRequests);
            if (searchFilter) searchFilter.addEventListener('keyup', filterRequests);

            // Initial filter
            filterRequests();

            // Modal show event
            if (compensationModal) {
                compensationModal.addEventListener('show.bs.modal', function(event) {
                    lastTriggerButton = event.relatedTarget;

                    if (lastTriggerButton) {
                        currentRequest = {
                            number: lastTriggerButton.getAttribute('data-request-number') || '',
                            monthlyCost: parseFloat(lastTriggerButton.getAttribute('data-monthly-cost') || 0),
                            currency: lastTriggerButton.getAttribute('data-currency') || 'USD',
                            estimatedHours: parseFloat(lastTriggerButton.getAttribute('data-estimated-hours') || 12),
                            leaseNumber: lastTriggerButton.getAttribute('data-lease-number') || 'No Lease',
                            leaseTitle: lastTriggerButton.getAttribute('data-lease-title') || 'No Lease',
                            leaseId: lastTriggerButton.getAttribute('data-lease-id') || null
                        };
                    }

                    // Rebuild available leases and prepare modal
                    buildAvailableLeases();
                    prepareCompensationModal();
                });

                compensationModal.addEventListener('hidden.bs.modal', function() {
                    if (lastTriggerButton) {
                        lastTriggerButton.focus();
                    }
                });
            }

            // Lease selector change event
            if (leaseSelector) {
                leaseSelector.addEventListener('change', onLeaseChange);
            }

            // Calculate button
            if (calculateBtn) {
                calculateBtn.addEventListener('click', calculateCompensation);
            }

            // Copy button
            if (copyBtn) {
                copyBtn.addEventListener('click', copyCompensation);
            }

            console.log('Maintenance Requests page initialized');
        });
    })();
</script>
@endsection
