@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Documents for Design Request #{{ $designRequest->request_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('design-requests.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Requests
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h5>Request Details</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Request ID:</th>
                                    <td>{{ $designRequest->id }}</td>
                                </tr>
                                <tr>
                                    <th>Customer:</th>
                                    <td>{{ $designRequest->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Route Name:</th>
                                    <td>{{ $designRequest->route_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Title:</th>
                                    <td>{{ $designRequest->title }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-8">
                            <h5>Document Summary</h5>
                            <div class="row">
                                @php
                                    $documents = [
                                        'quotation' => $designRequest->quotation,
                                        'conditional_certificate' => $designRequest->conditionalCertificate,
                                        'acceptance_certificate' => $designRequest->acceptanceCertificate,
                                        'contract' => $designRequest->quotation->contract ?? null,
                                        'lease' => $designRequest->lease,
                                    ];
                                @endphp

                                @foreach($documents as $type => $document)
                                <div class="col-md-4 mb-3">
                                    <div class="card card-document">
                                        <div class="card-body text-center">
                                            <div class="mb-2">
                                                @if($document)
                                                    <i class="fas fa-file-alt fa-3x text-success"></i>
                                                @else
                                                    <i class="fas fa-file-alt fa-3x text-secondary"></i>
                                                @endif
                                            </div>
                                            <h6 class="text-uppercase">{{ str_replace('_', ' ', $type) }}</h6>
                                            <p class="mb-1">
                                                @if($document)
                                                    <span class="badge badge-success">Available</span>
                                                @else
                                                    <span class="badge badge-secondary">Not Created</span>
                                                @endif
                                            </p>
                                            @if($document)
                                                <button class="btn btn-sm btn-info mt-2 view-document"
                                                        data-type="{{ $type }}"
                                                        data-id="{{ $document->id }}">
                                                    View Details
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Document Details Table</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Document Reference</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $type => $document)
                                        @if($document)
                                            <tr>
                                                <td class="text-capitalize">{{ str_replace('_', ' ', $type) }}</td>
                                                <td>
                                                    @switch($type)
                                                        @case('quotation')
                                                            {{ $document->quotation_number }}
                                                            @break
                                                        @case('conditional_certificate')
                                                            {{ $document->ref_number }}
                                                            @break
                                                        @case('acceptance_certificate')
                                                            {{ $document->certificate_ref }}
                                                            @break
                                                        @case('contract')
                                                            {{ $document->contract_number }}
                                                            @break
                                                        @case('lease')
                                                            {{ $document->lease_number }}
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ getStatusColor($document->status ?? $document->certificate_status ?? 'draft') }}">
                                                        {{ $document->status ?? $document->certificate_status ?? 'draft' }}
                                                    </span>
                                                </td>
                                                <td>{{ $document->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if(in_array($type, ['quotation', 'contract', 'lease']))
                                                            <a href="{{ route($type . 's.show', $document->id) }}"
                                                               class="btn btn-info" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif

                                                        @if($type === 'acceptance_certificate' || $type === 'conditional_certificate')
                                                            <a href="{{ route('certificates.download', ['type' => $type, 'id' => $document->id]) }}"
                                                               class="btn btn-primary" title="Download">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Document Details -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="documentDetails">
                <!-- Details will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-document {
        transition: transform 0.2s;
        height: 100%;
    }
    .card-document:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('.view-document').click(function() {
        const type = $(this).data('type');
        const id = $(this).data('id');

        $.ajax({
            url: '/documents/' + type + '/' + id + '/details',
            method: 'GET',
            success: function(response) {
                $('#documentDetails').html(response);
                $('#documentModal').modal('show');
            },
            error: function() {
                alert('Error loading document details');
            }
        });
    });
});
</script>
@endpush
