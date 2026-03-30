@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Admin Access Requests</h2>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table of requests --}}
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Employee ID</th>
                <th>Department</th>
                <th>Justification</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($requests as $req)
            <tr>
                <td>{{ $req->id }}</td>
                <td>{{ $req->name }}</td>
                <td>{{ $req->email }}</td>
                <td>{{ $req->employee_id }}</td>
                <td>{{ ucfirst($req->department) }}</td>
                <td>{{ $req->justification }}</td>
                <td>
                    @if($req->status === 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif($req->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </td>
                <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    @if($req->status === 'pending')
                        <form action="{{ route('admin.requests.approve', $req) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <form action="{{ route('admin.requests.reject', $req) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                    @else
                        <em>No actions</em>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">No requests found</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $requests->links() }}
    </div>
</div>
@endsection
