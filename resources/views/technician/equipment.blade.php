{{-- resources/views/technician/equipment.blade.php --}}
@extends('layouts.app')

@section('title', 'Equipment - Technician')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-toolbox me-2"></i>Equipment Management
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?status=all">All Equipment</a></li>
                            <li><a class="dropdown-item" href="?status=available">Available</a></li>
                            <li><a class="dropdown-item" href="?status=in_use">In Use</a></li>
                            <li><a class="dropdown-item" href="?status=maintenance">Under Maintenance</a></li>
                            <li><a class="dropdown-item" href="?status=calibration">Needs Calibration</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if($equipment->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Equipment Name</th>
                                        <th>Model</th>
                                        <th>Serial Number</th>
                                        <th>Status</th>
                                        <th>Last Calibration</th>
                                        <th>Next Calibration</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipment as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->name }}</strong>
                                                @if($item->is_critical)
                                                    <span class="badge bg-danger ms-1">Critical</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->model }}</td>
                                            <td>
                                                <code>{{ $item->serial_number }}</code>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'available' => 'success',
                                                        'in_use' => 'primary',
                                                        'maintenance' => 'warning',
                                                        'out_of_service' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($item->last_calibration)
                                                    {{ $item->last_calibration->format('M j, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $item->last_calibration->diffForHumans() }}</small>
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
                                                    <small class="text-muted">{{ $item->next_calibration->format('M j, Y') }}</small>
                                                @else
                                                    <span class="text-muted">Not scheduled</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->location ?? 'Not specified' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary"
                                                            data-bs-toggle="modal" data-bs-target="#equipmentModal{{ $item->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($item->status === 'available')
                                                        <button type="button" class="btn btn-outline-success"
                                                                onclick="updateEquipmentStatus({{ $item->id }}, 'in_use')">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    @elseif($item->status === 'in_use')
                                                        <button type="button" class="btn btn-outline-warning"
                                                                onclick="updateEquipmentStatus({{ $item->id }}, 'available')">
                                                            <i class="fas fa-stop"></i>
                                                        </button>
                                                    @endif
                                                    @if($item->next_calibration && $item->next_calibration->isPast())
                                                        <button type="button" class="btn btn-outline-danger"
                                                                onclick="reportCalibrationIssue({{ $item->id }})">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Equipment Details Modal -->
                                                <div class="modal fade" id="equipmentModal{{ $item->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Equipment Details - {{ $item->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h6>Basic Information</h6>
                                                                        <p><strong>Model:</strong> {{ $item->model }}</p>
                                                                        <p><strong>Serial:</strong> {{ $item->serial_number }}</p>
                                                                        <p><strong>Category:</strong> {{ $item->category ?? 'N/A' }}</p>
                                                                        <p><strong>Location:</strong> {{ $item->location ?? 'N/A' }}</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6>Status & Maintenance</h6>
                                                                        <p><strong>Status:</strong>
                                                                            <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                                                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                                            </span>
                                                                        </p>
                                                                        <p><strong>Last Calibration:</strong>
                                                                            {{ $item->last_calibration ? $item->last_calibration->format('M j, Y') : 'Never' }}
                                                                        </p>
                                                                        <p><strong>Next Calibration:</strong>
                                                                            {{ $item->next_calibration ? $item->next_calibration->format('M j, Y') : 'Not scheduled' }}
                                                                        </p>
                                                                        @if($item->specifications)
                                                                            <p><strong>Specifications:</strong><br>
                                                                                <small class="text-muted">{{ $item->specifications }}</small>
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                @if($item->notes)
                                                                    <div class="row mt-3">
                                                                        <div class="col-12">
                                                                            <h6>Notes</h6>
                                                                            <p class="text-muted">{{ $item->notes }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                @if($item->status === 'available')
                                                                    <button type="button" class="btn btn-primary"
                                                                            onclick="updateEquipmentStatus({{ $item->id }}, 'in_use')">
                                                                        Mark as In Use
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($equipment->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $equipment->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-toolbox fa-4x text-muted mb-3"></i>
                            <h4>No Equipment Found</h4>
                            <p class="text-muted">No equipment matches your current filter criteria.</p>
                            <a href="?status=all" class="btn btn-primary">
                                <i class="fas fa-sync me-1"></i>Show All Equipment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Form -->
<form id="equipmentStatusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" id="equipment_id" name="equipment_id">
    <input type="hidden" id="new_status" name="status">
</form>
@endsection

@section('scripts')
<script>
function updateEquipmentStatus(equipmentId, status) {
    if (confirm('Are you sure you want to update the equipment status?')) {
        document.getElementById('equipment_id').value = equipmentId;
        document.getElementById('new_status').value = status;
        document.getElementById('equipmentStatusForm').action = `/technician/equipment/${equipmentId}/status`;
        document.getElementById('equipmentStatusForm').submit();
    }
}

function reportCalibrationIssue(equipmentId) {
    if (confirm('Report this equipment as needing calibration?')) {
        // You can implement calibration reporting logic here
        alert('Calibration issue reported for equipment #' + equipmentId);
    }
}

// Filter equipment by status
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status && status !== 'all') {
        // Highlight the active filter
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            if (item.getAttribute('href') === `?status=${status}`) {
                item.classList.add('active');
            }
        });
    }
});
</script>
@endsection
