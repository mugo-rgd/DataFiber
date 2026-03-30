{{-- resources/views/ictengineer/requests/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Design Request Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-drafting-compass text-primary"></i> Design Request Details
                </h1>
                <a href="{{ route('ictengineer.requests') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Requests
                </a>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ictengineer.requests') }}">Design Requests</a></li>
                    <li class="breadcrumb-item active">{{ $request->request_id }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Design Request Header -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Design Request: {{ $request->request_id }}</h5>
                        <div>
                            <span class="badge bg-{{ match($request->status) {
                                'pending' => 'secondary',
                                'in_progress' => 'warning',
                                'design_completed' => 'info',
                                'review' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'on_hold' => 'dark',
                                default => 'secondary'
                            } }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                            <span class="badge bg-{{ match($request->priority) {
                                'low' => 'success',
                                'medium' => 'warning',
                                'high' => 'danger',
                                default => 'secondary'
                            } }}">
                                {{ ucfirst($request->priority) }} Priority
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Request ID:</strong> {{ $request->request_id }}</p>
                            <p><strong>Customer:</strong> {{ $request->customer->name ?? 'Not Assigned' }}</p>
                            <p><strong>Account Manager:</strong> {{ $request->accountManager->name ?? 'Not Assigned' }}</p>
                            <p><strong>Site Location:</strong> {{ $request->site_location ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Created:</strong> {{ $request->created_at->format('M d, Y') }}</p>
                            <p><strong>Updated:</strong> {{ $request->updated_at->format('M d, Y') }}</p>
                            @if($request->required_date)
                                <p><strong>Required Date:</strong> {{ $request->required_date->format('M d, Y') }}</p>
                            @endif
                            <p><strong>Type:</strong> {{ ucfirst($request->request_type) }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>ICT Engineer:</strong> {{ $request->ictEngineer->name ?? 'Not Assigned' }}</p>
                            <p><strong>Designer:</strong> {{ $request->designer->name ?? 'Not Assigned' }}</p>
                            <p><strong>Project Reference:</strong> {{ $request->project_reference ?? 'N/A' }}</p>
                            <p><strong>Project Value:</strong>
                                @if($request->estimated_value)
                                    ${{ number_format($request->estimated_value, 2) }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Design Items Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Design Items</h5>
                    @if($request->ict_engineer_id == auth()->id() && $request->status != 'cancelled' && $request->status != 'completed')
                    {{-- <a href="{{ route('design-items.create', ['design_request_id' => $request->id]) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Add Design Item
                    </a> --}}
                    @endif
                </div>
                <div class="card-body">
                    @if($request->designItems && $request->designItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Technology Type</th>
                                        <th>Link Class</th>
                                        <th>Cores</th>
                                        <th>Distance (km)</th>
                                        <th>Unit Cost</th>
                                        <th>Monthly Cost</th>
                                        <th>Annual Cost</th>
                                        <th>Total Contract</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request->designItems as $item)
                                    @php
                                        $monthlyCost = $item->unit_cost * $item->distance;
                                        $annualCost = $monthlyCost * 12;
                                        $totalContract = $annualCost * $item->terms;
                                        $taxAmount = $totalContract * $item->tax_rate;
                                        $totalWithTax = $totalContract + $taxAmount;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->request_number }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->technology_type }}</span>
                                        </td>
                                        <td>{{ $item->link_class }}</td>
                                        <td>{{ $item->cores_required }}</td>
                                        <td>{{ number_format($item->distance, 2) }} km</td>
                                        <td>${{ number_format($item->unit_cost, 2) }}</td>
                                        <td><strong>${{ number_format($monthlyCost, 2) }}</strong></td>
                                        <td><strong>${{ number_format($annualCost, 2) }}</strong></td>
                                        <td>
                                            <strong>${{ number_format($totalContract, 2) }}</strong>
                                            <small class="text-muted d-block">+ tax: ${{ number_format($taxAmount, 2) }}</small>
                                            <small class="text-success d-block">Total: ${{ number_format($totalWithTax, 2) }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('design-items.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($request->ict_engineer_id == auth()->id())
                                            <a href="{{ route('design-items.edit', $item->id) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('design-items.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @php
                                        $totalMonthly = 0;
                                        $totalAnnual = 0;
                                        $totalContractValue = 0;
                                        $totalTax = 0;
                                        $grandTotal = 0;

                                        foreach($request->designItems as $item) {
                                            $monthly = $item->unit_cost * $item->distance;
                                            $annual = $monthly * 12;
                                            $contract = $annual * $item->terms;
                                            $tax = $contract * $item->tax_rate;

                                            $totalMonthly += $monthly;
                                            $totalAnnual += $annual;
                                            $totalContractValue += $contract;
                                            $totalTax += $tax;
                                            $grandTotal += $contract + $tax;
                                        }
                                    @endphp
                                    <tr class="table-dark">
                                        <td colspan="6" class="text-end"><strong>TOTALS:</strong></td>
                                        <td><strong>${{ number_format($totalMonthly, 2) }}</strong></td>
                                        <td><strong>${{ number_format($totalAnnual, 2) }}</strong></td>
                                        <td>
                                            <strong>${{ number_format($totalContractValue, 2) }}</strong>
                                            <small class="text-muted d-block">+ tax: ${{ number_format($totalTax, 2) }}</small>
                                            <small class="text-success d-block">Grand Total: ${{ number_format($grandTotal, 2) }}</small>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Design Items Summary -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Design Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <p><strong>Total Items:</strong> {{ $request->designItems->count() }}</p>
                                                <p><strong>Total Cores:</strong> {{ $request->designItems->sum('cores_required') }}</p>
                                                <p><strong>Avg Distance:</strong> {{ number_format($request->designItems->avg('distance'), 2) }} km</p>
                                            </div>
                                            <div class="col-6">
                                                <p><strong>Technology Types:</strong></p>
                                                <ul class="list-unstyled">
                                                    @php
                                                        $techTypes = $request->designItems->groupBy('technology_type');
                                                    @endphp
                                                    @foreach($techTypes as $type => $items)
                                                        <li>{{ $type }}: {{ $items->count() }} items</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Financial Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Total Monthly Cost:</td>
                                                <td class="text-end"><strong>${{ number_format($totalMonthly, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Total Annual Cost:</td>
                                                <td class="text-end"><strong>${{ number_format($totalAnnual, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Total Contract Value:</td>
                                                <td class="text-end"><strong>${{ number_format($totalContractValue, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Total Tax ({{ $request->designItems->first()->tax_rate * 100 ?? 0 }}%):</td>
                                                <td class="text-end"><strong>${{ number_format($totalTax, 2) }}</strong></td>
                                            </tr>
                                            <tr class="table-success">
                                                <td><strong>Grand Total:</strong></td>
                                                <td class="text-end"><strong class="h5">${{ number_format($grandTotal, 2) }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-drafting-compass fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No design items created for this request yet.</p>
                            @if($request->ict_engineer_id == auth()->id() && $request->status != 'cancelled')
                            {{-- <a href="{{ route('design-items.create', ['design_request_id' => $request->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Design Item
                            </a> --}}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details & Description -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Request Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Subject:</strong>
                        <p class="mt-1">{{ $request->subject }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Scope Description:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->description)) !!}
                        </div>
                    </div>

                    <div>
                        <strong>Requirements:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->requirements ?? 'No specific requirements provided.')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Technical Specifications</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Bandwidth:</strong> {{ $request->bandwidth ?? 'N/A' }}</p>
                            <p><strong>Service Type:</strong> {{ $request->service_type ?? 'N/A' }}</p>
                            <p><strong>Circuit Type:</strong> {{ $request->circuit_type ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <p><strong>Term Length:</strong> {{ $request->term_length ?? 'N/A' }}</p>
                            <p><strong>Redundancy:</strong> {{ $request->has_redundancy ? 'Yes' : 'No' }}</p>
                            <p><strong>SLA Required:</strong> {{ $request->sla_required ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>

                    @if($request->technical_notes)
                    <div class="mt-3">
                        <strong>Technical Notes:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->technical_notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status Update (Only for assigned ICT Engineer) -->
            @if($request->ict_engineer_id == auth()->id())
            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Update Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('ictengineer.requests.update', $request->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $request->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="design_completed" {{ $request->status == 'design_completed' ? 'selected' : '' }}>Design Completed</option>
                                        <option value="review" {{ $request->status == 'review' ? 'selected' : '' }}>Review</option>
                                        <option value="on_hold" {{ $request->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select name="priority" id="priority" class="form-select">
                                        <option value="low" {{ $request->priority == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $request->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ $request->priority == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="engineer_notes" class="form-label">Engineer Notes</label>
                            <textarea name="engineer_notes" id="engineer_notes" class="form-control" rows="3"
                                      placeholder="Add any technical notes or comments...">{{ old('engineer_notes', $request->engineer_notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Request
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quotations Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quotations</h5>
                    @if($request->ict_engineer_id == auth()->id() && $request->status != 'cancelled')
                    {{-- <a href="{{ route('quotations.create', ['design_request_id' => $request->id]) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Create Quotation
                    </a> --}}
                    @endif
                </div>
                <div class="card-body">
                    @if($request->quotations && $request->quotations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Quotation #</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Valid Until</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request->quotations as $quotation)
                                    <tr>
                                        <td>
                                            <strong>{{ $quotation->quotation_number }}</strong>
                                        </td>
                                        <td>
                                            @if($quotation->total_amount)
                                                <strong>${{ number_format($quotation->total_amount, 2) }}</strong>
                                            @else
                                                <span class="text-muted">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ match($quotation->status) {
                                                'draft' => 'secondary',
                                                'sent' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                default => 'secondary'
                                            } }}">
                                                {{ ucfirst($quotation->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $quotation->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($quotation->valid_until)
                                                {{ $quotation->valid_until->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        {{-- <td>
                                            <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if($quotation->status == 'draft' && $request->ict_engineer_id == auth()->id())
                                            <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            @endif
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No quotations created for this request yet.</p>
                            @if($request->ict_engineer_id == auth()->id() && $request->status != 'cancelled')
                            <a href="{{ route('quotations.create', ['design_request_id' => $request->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Quotation
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status change confirmation
        const statusForm = document.querySelector('form');
        if(statusForm) {
            statusForm.addEventListener('submit', function(e) {
                const statusSelect = document.getElementById('status');
                if(statusSelect && statusSelect.value === 'cancelled') {
                    if(!confirm('Are you sure you want to cancel this request? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                }
            });
        }

        // Real-time status badge update
        const statusSelect = document.getElementById('status');
        const prioritySelect = document.getElementById('priority');

        function updateStatusBadge() {
            const statusBadge = document.querySelector('.card-header .badge.bg-primary');
            if(statusBadge && statusSelect) {
                const statusText = statusSelect.options[statusSelect.selectedIndex].text;
                const statusColor = getStatusColor(statusSelect.value);
                statusBadge.textContent = statusText;
                statusBadge.className = `badge bg-${statusColor}`;
            }
        }

        function updatePriorityBadge() {
            const priorityBadge = document.querySelectorAll('.card-header .badge')[1];
            if(priorityBadge && prioritySelect) {
                const priorityText = prioritySelect.options[prioritySelect.selectedIndex].text + ' Priority';
                const priorityColor = getPriorityColor(prioritySelect.value);
                priorityBadge.textContent = priorityText;
                priorityBadge.className = `badge bg-${priorityColor}`;
            }
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'secondary';
                case 'in_progress': return 'warning';
                case 'design_completed': return 'info';
                case 'review': return 'info';
                case 'approved': return 'success';
                case 'rejected': return 'danger';
                case 'on_hold': return 'dark';
                default: return 'secondary';
            }
        }

        function getPriorityColor(priority) {
            switch(priority) {
                case 'low': return 'success';
                case 'medium': return 'warning';
                case 'high': return 'danger';
                default: return 'secondary';
            }
        }

        if(statusSelect) {
            statusSelect.addEventListener('change', updateStatusBadge);
        }

        if(prioritySelect) {
            prioritySelect.addEventListener('change', updatePriorityBadge);
        }
    });
</script>
@endsection
