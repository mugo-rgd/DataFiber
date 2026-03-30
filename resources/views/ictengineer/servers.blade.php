{{-- resources/views/ictengineer/servers.blade.php --}}
@extends('layouts.app')

@section('title', 'Servers')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-server text-primary"></i> Network Servers
                </h1>
                <a href="{{ route('ictengineer.requests') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Requests
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Server List</h5>
                </div>
                <div class="card-body">
                    @if($servers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>IP Address</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Location</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($servers as $server)
                                    <tr>
                                        <td><strong>{{ $server->name }}</strong></td>
                                        <td>{{ $server->ip_address ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($server->type) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $server->status === 'active' ? 'success' : ($server->status === 'maintenance' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($server->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $server->location ?? 'N/A' }}</td>
                                        <td>{{ $server->updated_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $servers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-server fa-3x text-muted mb-3"></i>
                            <h4>No Servers Found</h4>
                            <p class="text-muted">No network servers are currently registered.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
