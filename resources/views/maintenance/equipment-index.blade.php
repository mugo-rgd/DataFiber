@extends('layouts.app')

@section('title', 'Maintenance Equipment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-toolbox me-2"></i>Maintenance Equipment
                    </h5>
                    @can('manage-equipment')
                        <a href="{{ route('maintenance.equipment.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Equipment
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('maintenance.equipment.index') }}" class="row g-2">
                                <div class="col-md-3">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                                        <option value="available" {{ $status == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="in_use" {{ $status == 'in_use' ? 'selected' : '' }}>In Use</option>
                                        <option value="maintenance" {{ $status == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                        <option value="retired" {{ $status == 'retired' ? 'selected' : '' }}>Retired</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search equipment..." value="{{ $search }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('maintenance.equipment.index') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Equipment Table -->
                    @if($equipment->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Model</th>
                                        <th>Serial Number</th>
                                        <th>Status</th>
                                        <th>Location</th>
                                        <th>Last Calibration</th>
                                        <th>Next Calibration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipment as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->name }}</strong>
                                                @if($item->description)
                                                    <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item->model ?? 'N/A' }}</td>
                                            <td>{{ $item->serial_number ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $item->status == 'available' ? 'success' : ($item->status == 'in_use' ? 'primary' : ($item->status == 'maintenance' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $item->location ?? 'N/A' }}</td>
                                            <td>
                                                @if($item->last_calibration)
                                                    {{ $item->last_calibration->format('M j, Y') }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->next_calibration)
                                                    @if($item->next_calibration->isPast())
                                                        <span class="badge bg-danger">Overdue</span>
                                                    @elseif($item->next_calibration->diffInDays(now()) <= 30)
                                                        <span class="badge bg-warning">Due Soon</span>
                                                    @else
                                                        <span class="badge bg-success">On Schedule</span>
                                                    @endif
                                                    <br>
                                                    <small>{{ $item->next_calibration->format('M j, Y') }}</small>
                                                @else
                                                    <span class="text-muted">Not Scheduled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('maintenance.equipment.show', $item->id) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @can('manage-equipment')
                                                        <a href="{{ route('maintenance.equipment.edit', $item->id) }}" class="btn btn-outline-secondary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $equipment->firstItem() }} to {{ $equipment->lastItem() }} of {{ $equipment->total() }} results
                            </div>
                            {{ $equipment->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-toolbox fa-3x text-muted mb-3"></i>
                            <h5>No Equipment Found</h5>
                            <p class="text-muted">
                                @if($status !== 'all' || $search)
                                    Try adjusting your filters or search terms.
                                @else
                                    No maintenance equipment has been added yet.
                                @endif
                            </p>
                            @can('manage-equipment')
                                <a href="{{ route('maintenance.equipment.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Add First Equipment
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
