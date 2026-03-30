@extends('layouts.app')

@section('title', 'My Leases')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-contract text-primary"></i> My Leases
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    @if($leases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Lease Number</th>
                                        <th>Service Type</th>
                                        <th>Bandwidth</th>
                                        <th>Status</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leases as $lease)
                                    <tr>
                                        <td>{{ $lease->lease_number }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</td>
                                        <td>{{ $lease->bandwidth }}</td>
                                        <td>
                                            <span class="badge bg-{{ $lease->status === 'active' ? 'success' : ($lease->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($lease->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $lease->start_date->format('M d, Y') }}</td>
                                        <td>{{ $lease->end_date->format('M d, Y') }}</td>
                                        <td>
                                            <!-- Option 1: Use existing lease show route if available -->
 <a href="{{ url('/customer/leases/' . $lease->id) }}"
   class="btn btn-sm btn-primary">
    <i class="fas fa-eye"></i> View Details
</a>

                                            <!-- Option 2: If the above doesn't work, use this temporary solution -->
                                            <!--
                                            <a href="{{ url('/customer/leases/' . $lease->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                            -->

                                            <!-- Option 3: Temporary disabled button with message -->
                                            <!--
                                            <button class="btn btn-sm btn-primary" disabled title="Details coming soon">
                                                <i class="fas fa-eye"></i> View Details
                                            </button>
                                            -->
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($leases->hasPages())
                        <div class="mt-3">
                            {{ $leases->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No leases found for your account.</p>
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
