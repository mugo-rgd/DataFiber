@extends('layouts.app')

@section('title', 'ASP Compliance Returns')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Application Service Provider (ASP) Compliance Returns</h2>
    <div>
        <a href="{{ route('asp.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New ASP Return
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Compliance ID</th>
                        <th>Licensee Name</th>
                        <th>License No</th>
                        <th>Financial Year</th>
                        <th>Quarter</th>
                        <th>Status</th>
                        <th>Submitted By</th>
                        <th>Submitted Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr>
                        <td>{{ $return->compliance_id ?? 'N/A' }}</td>
                        <td>{{ $return->licensee_name }}</td>
                        <td>{{ $return->license_no ?? 'N/A' }}</td>
                        <td>{{ $return->financial_year }}</td>
                        <td>{{ $return->quarter }}</td>
                        <td>
                            @php
                                $badgeClass = [
                                    'draft' => 'secondary',
                                    'submitted' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ][$return->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ ucfirst($return->status) }}
                            </span>
                        </td>
                        <td>{{ $return->submitter->name }}</td>
                        <td>{{ $return->submitted_at ? $return->submitted_at->format('d/m/Y') : 'Draft' }}</td>
                        <td>
                            <a href="{{ route('asp.show', $return->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> View
                            </a>
                            @if($return->status === 'draft')
                                <a href="{{ route('asp.edit', $return->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteReturn({{ $return->id }})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <td><td colspan="9" class="text-center">No ASP compliance returns found. <a href="{{ route('asp.create') }}">Create your first return</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $returns->links() }}
    </div>
</div>

@push('scripts')
<script>
function deleteReturn(id) {
    if (confirm('Are you sure you want to delete this return?')) {
        $.ajax({
            url: `/asp/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting return: ' + (xhr.responseJSON?.error || 'Unknown error'));
            }
        });
    }
}
</script>
@endpush
@endsection
