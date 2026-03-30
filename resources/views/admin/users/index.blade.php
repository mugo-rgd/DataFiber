@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-users text-primary"></i> User Management
                    </h1>
                    <p class="text-muted">Manage system users and their roles</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Add New User
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('status', 'active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('role', 'customer')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Team Members
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::whereIn('role', ['designer', 'surveyor', 'admin'])->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>All Users
                    </h6>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search users..." id="searchInput">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->company ?? 'No company' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        <div><i class="fas fa-envelope me-1"></i> {{ $user->email }}</div>
                                        @if($user->phone)
                                            <div><i class="fas fa-phone me-1"></i> {{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($user->role) {
                                        'admin' => 'danger',
                                        'designer' => 'info',
                                        'surveyor' => 'warning',
                                        'customer' => 'success',
                                        default => 'secondary'
                                    } }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($user->status) {
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'suspended' => 'danger',
                                        default => 'light'
                                    } }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== Auth::id())
                                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Quick Role Actions -->
                                    @if($user->id !== Auth::id())
                                        <div class="mt-1">
                                            @if($user->role !== 'admin')
                                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="role" value="admin">
                                                    <button type="submit" class="btn btn-xs btn-success" title="Make Admin">
                                                        <i class="fas fa-shield-alt"></i> Admin
                                                    </button>
                                                </form>
                                            @endif

                                            @if($user->role === 'admin')
                                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="role" value="customer">
                                                    <button type="submit" class="btn btn-xs btn-warning" title="Remove Admin">
                                                        <i class="fas fa-user"></i> Customer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Delete Modal -->
                                    @if($user->id !== Auth::id())
                                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete user <strong>{{ $user->name }}</strong>?
                                                        This action cannot be undone and will remove all associated data.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete User</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-4x mb-3"></i>
                                        <h5>No users found</h5>
                                        <p>Get started by creating your first user.</p>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-2"></i>Add New User
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                    </div>
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
