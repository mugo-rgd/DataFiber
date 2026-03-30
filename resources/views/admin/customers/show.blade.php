@extends('layouts.app')

@section('title', 'Customer Details - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-circle me-2"></i>Customer Details
        </h1>
        <div>
    @if(isset($managerId) && $managerId)
        <a href="{{ url('admin/account-managers/' . $managerId) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Account Manager
        </a>
    @else
        <a href="{{ url('admin/customers') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Customers
        </a>
    @endif
</div>
    </div>

    <!-- Customer Profile -->
    <div class="row">
        <!-- Profile Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3"
                         style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 48px;">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <h4 class="mb-1">{{ $customer->name }}</h4>
                    <p class="text-muted mb-2">{{ $customer->email }}</p>
                    <div class="mb-3">
                        @if($customer->status === 'active')
                            <span class="badge bg-success px-3 py-2">Active</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">Inactive</span>
                        @endif
                    </div>

                    <hr>

                    <div class="text-start">
                        <p><strong><i class="fas fa-phone me-2"></i>Phone:</strong> {{ $customer->phone ?? 'Not provided' }}</p>
                        <p><strong><i class="fas fa-building me-2"></i>Company:</strong> {{ $customer->company_name ?? 'Not provided' }}</p>
                        <p><strong><i class="fas fa-calendar me-2"></i>Joined:</strong> {{ $customer->created_at->format('F d, Y') }}</p>
                        <p><strong><i class="fas fa-user-tie me-2"></i>Account Manager:</strong>
                            {{ $customer->accountManager->name ?? 'Unassigned' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Profile Card -->
        @if($customer->companyProfile)
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building me-2"></i>Company Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Company Name:</strong> {{ $customer->companyProfile->company_name ?? $customer->company_name }}</p>
                            <p><strong>KRA PIN:</strong> {{ $customer->companyProfile->kra_pin ?? 'N/A' }}</p>
                            <p><strong>Registration Number:</strong> {{ $customer->companyProfile->registration_number ?? 'N/A' }}</p>
                            <p><strong>SAP Account:</strong> {{ $customer->companyProfile->sap_account ?? 'N/A' }}</p>
                            <p><strong>Company Type:</strong> {{ ucfirst($customer->companyProfile->company_type ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Contact Person 1:</strong> {{ $customer->companyProfile->contact_name_1 ?? 'N/A' }}</p>
                            <p><strong>Contact Phone 1:</strong> {{ $customer->companyProfile->contact_phone_1 ?? 'N/A' }}</p>
                            @if($customer->companyProfile->contact_name_2)
                            <p><strong>Contact Person 2:</strong> {{ $customer->companyProfile->contact_name_2 }}</p>
                            <p><strong>Contact Phone 2:</strong> {{ $customer->companyProfile->contact_phone_2 ?? 'N/A' }}</p>
                            @endif
                        </div>
                    </div>

                    @if($customer->companyProfile->physical_location || $customer->companyProfile->road || $customer->companyProfile->town)
                    <hr>
                    <h6 class="font-weight-bold">Address Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Physical Location:</strong> {{ $customer->companyProfile->physical_location ?? 'N/A' }}</p>
                            <p><strong>Road:</strong> {{ $customer->companyProfile->road ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Town:</strong> {{ $customer->companyProfile->town ?? 'N/A' }}</p>
                            <p><strong>Address:</strong> {{ $customer->companyProfile->address ?? 'N/A' }}</p>
                            @if($customer->companyProfile->code)
                            <p><strong>Code:</strong> {{ $customer->companyProfile->code }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($customer->companyProfile->description)
                    <hr>
                    <p><strong>Description:</strong> {{ $customer->companyProfile->description }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Additional Information -->
    <div class="row">
        <!-- Billing Information -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card me-2"></i>Billing Information
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="150">Billing Frequency:</th>
                            <td>{{ ucfirst($customer->billing_frequency ?? 'Not set') }}</td>
                        </tr>
                        <tr>
                            <th>Monthly Rate:</th>
                            <td>${{ number_format($customer->monthly_rate ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Next Billing Date:</th>
                            <td>{{ $customer->next_billing_date ? $customer->next_billing_date->format('M d, Y') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>Auto Billing:</th>
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
                            <th>Lease Start Date:</th>
                            <td>{{ \Carbon\Carbon::parse($customer->lease_start_date)->format('M d, Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="150">Customer ID:</th>
                            <td>#{{ $customer->id }}</td>
                        </tr>
                        <tr>
                            <th>Email Verified:</th>
                            <td>
                                @if($customer->email_verified_at)
                                    {{ $customer->email_verified_at->format('M d, Y') }}
                                @else
                                    <span class="badge bg-warning">Not Verified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Last Login:</th>
                            <td>{{ $customer->last_login_at ? $customer->last_login_at->diffForHumans() : 'Never' }}</td>
                        </tr>
                        <tr>
                            <th>Profile Completed:</th>
                            <td>
                                @if($customer->profile_completed_at)
                                    {{ $customer->profile_completed_at->format('M d, Y') }}
                                @else
                                    <span class="badge bg-warning">Incomplete</span>
                                @endif
                            </td>
                        </tr>
                        @if($customer->assigned_at)
                        <tr>
                            <th>Assigned to Manager:</th>
                            <td>{{ $customer->assigned_at->format('M d, Y') }}</td>
                        </tr>
                        @endif
                        @if($customer->assignment_notes)
                        <tr>
                            <th>Assignment Notes:</th>
                            <td><small>{{ $customer->assignment_notes }}</small></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ url('admin/customers/' . $customer->id . '/edit') }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>Edit Customer
                    </a>
                    <a href="{{ url('admin/customers/' . $customer->id . '/quotations') }}" class="btn btn-info me-2">
                        <i class="fas fa-file-invoice me-2"></i>View Quotations
                    </a>
                    <a href="{{ url('admin/customers/' . $customer->id . '/requests') }}" class="btn btn-primary me-2">
                        <i class="fas fa-drafting-compass me-2"></i>Design Requests
                    </a>
                    @if($customer->account_manager_id)
                    <button type="button" class="btn btn-danger" onclick="disassignManager({{ $customer->id }})">
                        <i class="fas fa-user-times me-2"></i>Remove Manager
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.table-sm th {
    color: #6c757d;
    font-weight: 600;
}
.btn-group .btn {
    margin-right: 2px;
}
.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

<script>
function disassignManager(customerId) {
    if (confirm('Are you sure you want to remove the account manager from this customer?')) {
        fetch(`/admin/customers/${customerId}/disassign-manager`, {
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
            alert('An error occurred');
        });
    }
}
</script>
@endsection
