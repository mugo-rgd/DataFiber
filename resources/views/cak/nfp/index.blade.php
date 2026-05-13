@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-network-wired text-warning me-2"></i>NFP Compliance Returns
            </h4>
            <small class="text-muted">Network Facility Provider quarterly compliance filings — Fibre optic, towers, and physical infrastructure</small>
        </div>

        <a href="{{ route('nfp.create') }}" class="btn btn-warning">
            <i class="fas fa-plus me-1"></i> New NFP Return
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        NFP returns cover: Fibre optic cables, transmission towers, base stations, ducts, and other network facilities
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-warning text-dark">CAK Class License</span>
                    <span class="badge bg-info ms-1">NFP Category</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">#</th>
                            <th>Licensee Name</th>
                            <th>License No.</th>
                            <th>Financial Year</th>
                            <th>Quarter</th>
                            <th>Infrastructure Type</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th width="260">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->licensee_name }}</td>
                                <td>{{ $record->license_no ?? '—' }}</td>
                                <td>{{ $record->financial_year }}</td>
                                <td>{{ $record->quarter }}</td>
                                <td>
                                    @php
                                        $infraTypes = [
                                            'fibre' => '<i class="fas fa-fiber-optic me-1"></i> Fibre Optic',
                                            'tower' => '<i class="fas fa-tower-broadcast me-1"></i> Tower',
                                            'duct' => '<i class="fas fa-road me-1"></i> Duct',
                                            'base_station' => '<i class="fas fa-broadcast-tower me-1"></i> Base Station',
                                            'backhaul' => '<i class="fas fa-microchip me-1"></i> Backhaul Link',
                                        ];
                                        $infraType = $record->infrastructure_type ?? 'fibre';
                                    @endphp
                                    {!! $infraTypes[$infraType] ?? '<i class="fas fa-network-wired me-1"></i> Other' !!}
                                 </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-secondary',
                                            'generated' => 'bg-dark',
                                            'submitted' => 'bg-warning text-dark',
                                            'submitted_to_cak' => 'bg-info',
                                            'under_review' => 'bg-primary',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                        ];
                                        $statusLabel = [
                                            'draft' => 'DRAFT',
                                            'generated' => 'GENERATED',
                                            'submitted' => 'SUBMITTED',
                                            'submitted_to_cak' => 'SENT TO CAK',
                                            'under_review' => 'UNDER REVIEW',
                                            'approved' => 'APPROVED',
                                            'rejected' => 'REJECTED',
                                        ];
                                        $class = $statusClasses[$record->status] ?? 'bg-secondary';
                                        $label = $statusLabel[$record->status] ?? ucfirst(str_replace('_', ' ', $record->status));
                                    @endphp
                                    <span class="badge {{ $class }}">
                                        {{ $label }}
                                    </span>
                                 </td>
                                <td>
                                    {{ $record->submitted_at ? \Carbon\Carbon::parse($record->submitted_at)->format('d M Y H:i') : '—' }}
                                 </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('nfp.show', $record->id) }}" class="btn btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(in_array($record->status, ['draft', 'generated', 'rejected']))
                                            <a href="{{ route('nfp.edit', $record->id) }}" class="btn btn-outline-warning" title="Edit Return">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('nfp.print', $record->id) }}" class="btn btn-outline-dark" title="Download PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>

                                        @if($record->status === 'submitted')
                                            <button type="button" class="btn btn-outline-primary" title="Submit to CAK" onclick="submitToCAK({{ $record->id }})">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        @endif

                                        @if(in_array($record->status, ['submitted_to_cak', 'under_review']))
                                            <button type="button" class="btn btn-outline-success" title="Check CAK Status" onclick="checkCAKStatus({{ $record->id }})">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        @endif

                                        @if($record->status === 'approved' && $record->cak_reference_number)
                                            <span class="btn btn-outline-secondary" title="CAK Ref: {{ $record->cak_reference_number }}">
                                                <i class="fas fa-check-circle text-success"></i>
                                            </span>
                                        @endif

                                        <button type="button" class="btn btn-outline-secondary" title="View Network Map" onclick="viewNetworkMap({{ $record->id }})" @if(!$record->latitude) disabled @endif>
                                            <i class="fas fa-map-marker-alt"></i>
                                        </button>

                                                 @can('delete', $record)
                                            <button type="button" class="btn btn-outline-danger" title="Delete Return" onclick="confirmDelete({{ $record->id }})">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endcan
                                    </div>
                                 </td>
                             </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-network-wired fa-2x mb-2 d-block"></i>
                                    No NFP compliance returns found.
                                    <br>
                                    <small class="text-muted">Network Facility Providers must submit quarterly returns on fibre, towers, and infrastructure deployment.</small>
                                    <br>
                                    <a href="{{ route('nfp.create') }}" class="btn btn-sm btn-warning mt-2">
                                        <i class="fas fa-plus me-1"></i> Create Your First NFP Return
                                    </a>
                                 </td>
                             </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <small class="text-muted">
                        <i class="fas fa-chart-line me-1"></i> Total: {{ $records->total() }} returns
                    </small>
                    <small class="text-muted ms-3">
                        <i class="fas fa-tower-broadcast me-1"></i> Fibre operators: {{ $fibreOperatorCount ?? 0 }}
                    </small>
                </div>
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitToCAK(recordId) {
    if(confirm('Submit this NFP return to the Communications Authority of Kenya for review?\n\nThis includes network facility infrastructure data (fibre, towers, etc.).')) {
        fetch(`/nfp/${recordId}/submit-to-cak`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('✓ NFP return submitted to CAK successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('✗ ' + (data.message || 'Submission failed. Please try again.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Please check your connection.');
        });
    }
}

function checkCAKStatus(recordId) {
    fetch(`/nfp/${recordId}/cak-status`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        let statusMessage = `CAK Status: ${data.status.toUpperCase()}\n`;
        if(data.reference_number) {
            statusMessage += `Reference Number: ${data.reference_number}\n`;
        }
        if(data.fibre_km) {
            statusMessage += `Reported Fibre: ${data.fibre_km} km\n`;
        }
        statusMessage += `Last Updated: ${data.updated_at}`;

        alert(statusMessage);

        if(data.status === 'approved' || data.status === 'rejected') {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Status check failed. Please try again.');
    });
}

function viewNetworkMap(recordId) {
    window.open(`/nfp/${recordId}/network-map`, '_blank', 'width=800,height=600');
}

function confirmDelete(recordId) {
    if(confirm('Are you sure you want to delete this NFP return?\n\nThis action cannot be undone and will remove all infrastructure data.')) {
        fetch(`/nfp/${recordId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Return deleted successfully.');
                location.reload();
            } else {
                alert('Delete failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Delete failed. Please try again.');
        });
    }
}
</script>
@endpush

@endsection
