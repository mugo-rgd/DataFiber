@extends('layouts.app')

@section('title', 'Manage Documents')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 text-gray-800 mb-4">
                <i class="fas fa-file-upload me-2"></i> Manage Documents
            </h1>

            @if($leases->count() > 0)
                <div class="row">
                    @foreach($leases as $lease)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Lease #{{ $lease->lease_number }}</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Service:</strong> {{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</p>
                                <p><strong>Documents:</strong> {{ $lease->documents->count() }}</p>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('customer.leases.show', $lease) }}#documents"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> View Documents
                                    </a>
                                    <a href="{{ route('customer.documents.store', $lease) }}"
                                       class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-upload me-1"></i> Upload New Document
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Leases Found</h4>
                        <p class="text-muted">You need to have an active lease to upload documents.</p>
                        <a href="{{ route('customer.leases.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i> View My Leases
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
