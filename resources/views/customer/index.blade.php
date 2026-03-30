@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Customers</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>Customers
                        </h3>
                        <a href="{{ route('customers.create') }}" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i>Add Customer
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Auto Billing</th>
                                        <th>Billings</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light rounded me-2">
                                                    <i class="fas fa-user text-primary p-2"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $customer->name }}</strong>
                                                    @if($customer->company)
                                                    <br>
                                                    <small class="text-muted">{{ $customer->company }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $customer->auto_billing_enabled ? 'success' : 'secondary' }}">
                                                <i class="fas fa-{{ $customer->auto_billing_enabled ? 'check' : 'times' }} me-1"></i>
                                                {{ $customer->auto_billing_enabled ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $customer->leaseBillings->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('customers.show', $customer->id) }}"
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('customers.edit', $customer->id) }}"
                                                   class="btn btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('finance.billing.create', ['customer_id' => $customer->id]) }}"
                                                   class="btn btn-outline-success" title="Create Bill">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $customers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Customers Found</h4>
                            <p class="text-muted">Get started by adding your first customer.</p>
                            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add First Customer
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
