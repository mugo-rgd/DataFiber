@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>NFP Compliance Returns</h4>

        <a href="{{ route('nfp.create') }}" class="btn btn-primary">
            + New NFP Return
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-sm">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Licensee</th>
                <th>License No</th>
                <th>Financial Year</th>
                <th>Quarter</th>
                <th>Status</th>
                <th width="220">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->licensee_name }}</td>
                    <td>{{ $record->license_no ?? '-' }}</td>
                    <td>{{ $record->financial_year }}</td>
                    <td>{{ $record->quarter }}</td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('nfp.show', $record->id) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('nfp.edit', $record->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <a href="{{ route('nfp.print', $record->id) }}" class="btn btn-sm btn-dark">PDF</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No NFP returns found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $records->links() }}
</div>
@endsection
