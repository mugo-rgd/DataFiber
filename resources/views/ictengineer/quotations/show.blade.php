@extends('layouts.app')

@section('title', 'View Quotation')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-invoice-dollar text-kp-blue me-2"></i>Quotation Details
                    </h1>
                    <p class="text-muted mb-0">Quotation #{{ $quotation->quotation_number }}</p>
                </div>
                <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Requests
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Quotation Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Quotation Number:</strong> {{ $quotation->quotation_number }}</p>
                    <p><strong>Customer:</strong> {{ $quotation->customer->name ?? 'N/A' }}</p>
                    <p><strong>Design Request:</strong> {{ $quotation->designRequest->request_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Amount:</strong> {{ $quotation->currency }} {{ number_format($quotation->total_amount, 2) }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $quotation->status === 'approved' ? 'success' : ($quotation->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </p>
                    <p><strong>Valid Until:</strong> {{ $quotation->valid_until->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
