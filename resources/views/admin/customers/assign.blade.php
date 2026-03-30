@extends('layouts.app')

@section('title', 'Assign Customers to Account Managers')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-user-friends text-primary"></i> Assign Customers
            </h1>
            <p class="text-muted">Assign customers to account managers for better relationship management</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('role', 'customer')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Assigned Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('role', 'customer')->whereNotNull('account_manager_id')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Unassigned Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $unassignedCustomers->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user-plus me-2"></i>Assign Customers
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.customers.assign.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_manager_id" class="form-label">Account Manager *</label>
                                    <select name="account_manager_id" id="account_manager_id" class="form-control @error('account_manager_id') is-invalid @enderror" required>
                                        <option value="">Select Account Manager</option>
                                        @foreach($accountManagers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('account_manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }} ({{ $manager->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('account_manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_ids" class="form-label">Customers to Assign *</label>
                                    <select name="customer_ids[]" id="customer_ids" class="form-control @error('customer_ids') is-invalid @enderror" multiple required style="height: 150px;">
                                        @foreach($unassignedCustomers as $customer)
                                        <option value="{{ $customer->id }}" {{ in_array($customer->id, old('customer_ids', [])) ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->email }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('customer_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Hold Ctrl/Cmd to select multiple customers. {{ $unassignedCustomers->count() }} unassigned customers available.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-user-plus me-1"></i> Assign
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="assignment_notes" class="form-label">Assignment Notes (Optional)</label>
                            <textarea name="assignment_notes" id="assignment_notes" class="form-control" rows="2" placeholder="Add any notes about this assignment...">{{ old('assignment_notes') }}</textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Assignment Information
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <strong>Account Managers:</strong> Users who can manage customer relationships and accounts.
                    </p>
                    <p class="small text-muted">
                        <strong>Assignment Benefits:</strong>
                    </p>
                    <ul class="small text-muted">
                        <li>Better customer relationship management</li>
                        <li>Personalized customer service</li>
                        <li>Streamlined communication</li>
                        <li>Improved customer satisfaction</li>
                    </ul>
                    <p class="small text-muted">
                        <strong>Note:</strong> Customers can only be assigned to one account manager at a time.
                    </p>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-list me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.customers.assignments') }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="fas fa-eye me-1"></i> View All Assignments
                    </a>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-users me-1"></i> Manage All Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
