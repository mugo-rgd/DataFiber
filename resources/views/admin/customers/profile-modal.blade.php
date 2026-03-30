@if(isset($customer))
<div class="customer-profile">
    <!-- Profile Header -->
    <div class="text-center mb-4">
        <div class="avatar-xl mx-auto mb-3">
            <div class="avatar-title bg-primary text-white rounded-circle" style="width: 80px; height: 80px; font-size: 32px;">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
        </div>
        <h4 class="mb-1">{{ $customer->name }}</h4>
        @if($customer->company_name)
            <p class="text-muted mb-2">{{ $customer->company_name }}</p>
        @endif
        <p class="text-muted mb-2">Customer ID: #{{ $customer->id }}</p>
        <div class="d-flex justify-content-center gap-2">
            @if($customer->status === 'active')
                <span class="badge bg-success">Active Account</span>
            @elseif($customer->status === 'inactive')
                <span class="badge bg-secondary">Inactive Account</span>
            @else
                <span class="badge bg-warning">{{ ucfirst($customer->status) }}</span>
            @endif
            <span class="badge bg-info">Member since {{ $customer->created_at->format('M Y') }}</span>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <h6 class="mb-0"><i class="fas fa-address-card text-primary me-2"></i>Contact Information</h6>
        </div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="100">Email:</td>
                            <td>
                                <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1 text-muted"></i>{{ $customer->email }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone:</td>
                            <td>
                                @if($customer->phone)
                                    <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1 text-muted"></i>{{ $customer->phone }}
                                    </a>
                                @else
                                    <span class="text-muted"><i class="fas fa-phone me-1"></i>Not provided</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="100">Address:</td>
                            <td>
                                @if($customer->address)
                                    <i class="fas fa-map-marker-alt me-1 text-muted"></i>{{ $customer->address }}
                                    @if($customer->city || $customer->country)
                                        <br>
                                        <small>
                                            {{ $customer->city ?? '' }} {{ $customer->country ? ', ' . $customer->country : '' }}
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Not provided</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Details -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0"><i class="fas fa-user-tie text-primary me-2"></i>Account Manager</h6>
                </div>
                <div class="card-body pt-0">
                    @if($customer->accountManager)
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-success text-white rounded-circle">
                                    {{ substr($customer->accountManager->name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $customer->accountManager->name }}</h6>
                                <small class="text-muted">{{ $customer->accountManager->email }}</small>
                                @if($customer->assigned_at)
                                    <br>
                                    <small class="text-muted">Assigned: {{ $customer->assigned_at->format('M d, Y') }}</small>
                                @endif
                            </div>
                        </div>
                        @if($customer->assignment_notes)
                            <div class="mt-2 p-2 bg-light rounded">
                                <small><strong>Notes:</strong> {{ $customer->assignment_notes }}</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No account manager assigned</p>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="prepareAssignManager({{ $customer->id }}, '{{ $customer->name }}')" data-bs-dismiss="modal">
                                <i class="fas fa-user-tie me-1"></i>Assign Manager
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Billing Information</h6>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Billing Frequency:</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($customer->billing_frequency ?? 'monthly') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Monthly Rate:</td>
                            <td class="fw-bold">${{ number_format($customer->monthly_rate ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Auto Billing:</td>
                            <td>
                                @if($customer->auto_billing_enabled)
                                    <span class="badge bg-success">Enabled</span>
                                @else
                                    <span class="badge bg-secondary">Disabled</span>
                                @endif
                            </td>
                        </tr>
                        @if($customer->lease_start_date)
                        <tr>
                            <td class="text-muted">Lease Start:</td>
                            <td>{{ \Carbon\Carbon::parse($customer->lease_start_date)->format('M d, Y') }}</td>
                        </tr>
                        @endif
                        @if($customer->next_billing_date)
                        <tr>
                            <td class="text-muted">Next Billing:</td>
                            <td>{{ \Carbon\Carbon::parse($customer->next_billing_date)->format('M d, Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- County Information (if applicable) -->
    @if($customer->county_id)
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <h6 class="mb-0"><i class="fas fa-map text-primary me-2"></i>County Assignment</h6>
        </div>
        <div class="card-body pt-0">
            <p class="mb-1"><strong>County ID:</strong> {{ $customer->county_id }}</p>
            @if($customer->county_assigned_at)
                <p class="mb-1"><small class="text-muted">Assigned: {{ $customer->county_assigned_at->format('M d, Y') }}</small></p>
            @endif
            @if($customer->county_notes)
                <div class="p-2 bg-light rounded">
                    <small><strong>Notes:</strong> {{ $customer->county_notes }}</small>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Profile Completion -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <h6 class="mb-0"><i class="fas fa-check-circle text-primary me-2"></i>Profile Completion</h6>
        </div>
        <div class="card-body pt-0">
            <div class="progress mb-2" style="height: 10px;">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: {{ $customer->profile_completion_percentage ?? 0 }}%"></div>
            </div>
            <div class="d-flex justify-content-between">
                <small class="text-muted">{{ $customer->profile_completion_percentage ?? 0 }}% complete</small>
                @if(($customer->profile_completion_percentage ?? 0) == 100)
                    <small class="text-success"><i class="fas fa-check-circle"></i> Complete</small>
                @else
                    <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Incomplete</small>
                @endif
            </div>
        </div>
    </div>

    <!-- Account Status History -->
    <div class="mt-3 text-muted small">
        <i class="fas fa-clock me-1"></i> Last updated: {{ $customer->updated_at->format('M d, Y H:i') }}
        @if($customer->profile_completed_at)
            <br><i class="fas fa-check-circle me-1"></i> Profile completed: {{ $customer->profile_completed_at->format('M d, Y') }}
        @endif
    </div>
</div>

<style>
.avatar-xl {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>
@else
<div class="alert alert-warning m-3">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Customer information not available.
</div>
@endif
