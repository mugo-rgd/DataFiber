{{-- resources/views/finance/sap-assignment/index.blade.php --}}
@extends('layouts.app')

@section('title', 'SAP Account Assignment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fas fa-users-cog"></i> SAP Account Assignment</h1>
            <p class="text-muted">Assign SAP customer codes to active customers</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button class="btn btn-success" disabled>
                    <i class="fas fa-layer-group"></i> Bulk Assignment (Disabled)
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-0">Customers Pending SAP Assignment</h5>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2"
                               placeholder="Search by name, email, or company..."
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Customer</th>
                            <th>Company</th>
                            <th>KRA Pin</th>
                            <th>Registration No.</th>
                            <th>Phone</th>
                            <th>Company Type</th>
                            <th>SAP Account</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($customer->companyProfile?->profile_photo)
                                    <img src="{{ asset('storage/' . $customer->companyProfile->profile_photo) }}"
                                         class="rounded-circle me-2" width="32" height="32" alt="{{ $customer->name }}">
                                    @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2"
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <strong>{{ $customer->name }}</strong><br>
                                        <small class="text-muted">{{ $customer->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $customer->companyProfile->kra_pin ?? 'N/A' }}</td>
                            <td>{{ $customer->companyProfile->registration_number ?? 'N/A' }}</td>
                            <td>{{ $customer->companyProfile->phone_number ?? $customer->phone }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $customer->companyProfile->company_type ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($customer->companyProfile && $customer->companyProfile->sap_account)
                                <span class="badge bg-success">{{ $customer->companyProfile->sap_account }}</span>
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if(empty($customer->companyProfile?->sap_account))
                                <a href="{{ route('finance.sap-assignment.edit', $customer->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-key"></i> Assign SAP
                                </a>
                                @else
                                <a href="{{ route('finance.sap-assignment.edit', $customer->id) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Reassign
                                </a>
                                @endif
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
            <div class="alert alert-info">
                <i class="fas fa-check-circle"></i> All active customers have SAP accounts assigned.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
