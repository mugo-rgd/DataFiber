@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Colocation Services</h1>
        <a href="{{ route('admin.colocation-services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Colocation Service
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Service Number</th>
                            <th>Customer</th>
                            <th>Service Type</th>
                            <th>Location</th>
                            <th>Monthly Price</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                        <tr>
                            <td>{{ $service->service_number }}</td>
                            <td>{{ $service->user->name }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ str_replace('_', ' ', ucfirst($service->service_type)) }}
                                </span>
                                @if($service->service_type === 'rack_space')
                                ({{ $service->rack_units }}U)
                                @elseif($service->service_type === 'cabinet')
                                ({{ str_replace('_', ' ', $service->cabinet_size) }})
                                @endif
                            </td>
                            <td>{{ $service->location_reference }}</td>
                            <td>${{ number_format($service->monthly_price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $service->status === 'active' ? 'success' : ($service->status === 'suspended' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td>{{ $service->start_date->format('M d, Y') }}</td>
                            <td>{{ $service->end_date ? $service->end_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.colocation-services.show', $service) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.colocation-services.edit', $service) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($service->status === 'active')
                                    <form action="{{ route('admin.colocation-services.suspend', $service) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Suspend this service?')">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    </form>
                                    @elseif($service->status === 'suspended')
                                    <form action="{{ route('admin.colocation-services.activate', $service) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Activate this service?')">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.colocation-services.destroy', $service) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
