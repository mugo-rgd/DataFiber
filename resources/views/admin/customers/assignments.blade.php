@extends('layouts.app')

@section('title', 'Customer Assignments')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-user-friends text-primary"></i> Customer Assignments
            </h1>
            <p class="text-muted">View all customer to account manager assignments</p>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Account Manager</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->accountManager->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                                <td>
                                    <form action="{{ route('admin.customers.assignments.destroy', $customer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this assignment?')">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No customer assignments found.</p>
                                    <a href="{{ route('admin.customers.assign') }}" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-2"></i>Assign Customers
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
