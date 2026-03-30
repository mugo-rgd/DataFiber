@extends('layouts.app')

@section('title', 'Lease Documents - ' . $lease->lease_number)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800 mb-0">
                    <i class="fas fa-file-contract me-2"></i>Lease Documents
                </h1>
                <div>
                    <a href="{{ route('customer.leases.show', $lease) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Lease
                    </a>
                </div>
            </div>

            <!-- Lease Information -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title">Lease #{{ $lease->lease_number }}</h5>
                    <p class="card-text">
                        <strong>Service:</strong> {{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}<br>
                        <strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($lease->status) }}</span>
                    </p>
                </div>
            </div>

            <!-- Documents -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-files me-2"></i>Lease Documents
                    </h5>
                </div>
                <div class="card-body">
                    @if($documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Document Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                <tr>
                                    <td>{{ $document->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $document->document_type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge bg-{{ $document->status === 'approved' ? 'success' : ($document->status === 'pending_review' ? 'warning' : 'danger') }}">
                                            {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $document->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('customer.documents.download', $document) }}"
                                               class="btn btn-outline-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Documents Found</h4>
                        <p class="text-muted">No documents have been uploaded for this lease yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
