@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-server text-primary me-2"></i>ASP Compliance Returns
            </h4>
            <small class="text-muted">Application Service Provider quarterly compliance filings</small>
        </div>

        <a href="{{ route('asp.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New ASP Return
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
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th width="240">Actions</th>
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
                                        <a href="{{ route('asp.show', $record->id) }}" class="btn btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(in_array($record->status, ['draft', 'generated', 'rejected']))
                                            <a href="{{ route('asp.edit', $record->id) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('asp.print', $record->id) }}" class="btn btn-outline-dark" title="Download PDF">
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

                                        @can('delete', $record)
                                            <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete({{ $record->id }})">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-server fa-2x mb-2 d-block"></i>
                                    No ASP compliance returns found.
                                    <br>
                                    <a href="{{ route('asp.create') }}" class="btn btn-sm btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> Create Your First ASP Return
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-chart-line me-1"></i> Total: {{ $records->total() }} returns
                </small>
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitToCAK(recordId) {
    if(confirm('Submit this ASP return to the Communications Authority of Kenya for review?\n\nThis action cannot be undone.')) {
        fetch(`/asp/${recordId}/submit-to-cak`, {
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
                alert('✓ Return submitted to CAK successfully!');
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
    fetch(`/asp/${recordId}/cak-status`, {
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

function confirmDelete(recordId) {
    if(confirm('Are you sure you want to delete this ASP return?\n\nThis action cannot be undone.')) {
        fetch(`/asp/${recordId}`, {
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
