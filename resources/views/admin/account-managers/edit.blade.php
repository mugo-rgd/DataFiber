@extends('layouts.app')

@section('title', 'Edit Account Manager - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-edit me-2"></i>Edit Account Manager
        </h1>
        <div>
            <a href="{{ route('admin.account-managers.show', $manager->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>View Details
            </a>
            <a href="{{ route('admin.account-managers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Account Manager Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.account-managers.update', $manager->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $manager->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $manager->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone" value="{{ old('phone', $manager->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                               id="company_name" name="company_name" value="{{ old('company_name', $manager->company_name) }}">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password">
                        <small class="text-muted">Minimum 8 characters</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                            <option value="active" {{ old('status', $manager->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $manager->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account Created</label>
                        <input type="text" class="form-control" value="{{ $manager->created_at->format('F d, Y') }}" readonly>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Account Manager
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Assignment Summary -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assigned Customers</h6>
        </div>
        <div class="card-body">
            @php
                $customersCount = $manager->managedCustomers()->count();
            @endphp

            @if($customersCount > 0)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This account manager has <strong>{{ $customersCount }}</strong> assigned customers.
                    <a href="{{ route('admin.account-managers.show', $manager->id) }}" class="alert-link">View all customers</a>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No customers are currently assigned to this account manager.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
