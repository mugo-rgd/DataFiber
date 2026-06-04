@extends('layouts.app')

@section('title', 'Generate Acceptance Certificate')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-signature text-warning me-2"></i>Generate Acceptance Certificate
                    </h1>
                    <p class="text-muted mb-0">Request #{{ $request->request_number }}</p>
                </div>
                <a href="{{ route('designer.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Acceptance Certificate Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('designer.certificates.acceptance.store', $request) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Certificate Reference *</label>
                            <input type="text" name="certificate_ref" class="form-control"
                                   value="KPLC/AC/{{ date('Y') }}/{{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}" required>
                            <small class="text-muted">Unique reference number for this certificate</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Issue Date *</label>
                                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Effective Date *</label>
                                    <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Lessee Name *</label>
                                    <input type="text" name="lessee_name" class="form-control"
                                           value="{{ $request->customer->name ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Lessee Address *</label>
                                    <input type="text" name="lessee_address" class="form-control"
                                           value="{{ $request->customer->address ?? '' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Route Description *</label>
                            <textarea name="route_description" class="form-control" rows="3" required>{{ $request->title }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Fibre Cores *</label>
                                    <input type="number" name="fibre_cores" class="form-control" value="{{ $request->cores_required ?? 2 }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Distance (KM) *</label>
                                    <input type="number" name="distance_km" class="form-control" step="0.01" value="{{ $request->distance ?? 0 }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Additional Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Note:</strong> This acceptance certificate confirms that 30 days have passed since the conditional certificate was issued.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('designer.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Generate Certificate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0 fw-bold">Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Conditional Certificate Info:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Certificate #: {{ $conditionalCert->ref_number ?? 'N/A' }}</li>
                            <li>Issue Date: {{ $conditionalCert->certificate_date ? $conditionalCert->certificate_date->format('M d, Y') : 'N/A' }}</li>
                            <li>Status: {{ ucfirst($conditionalCert->certificate_status ?? 'N/A') }}</li>
                        </ul>
                    </div>
                    <div class="alert alert-success">
                        <strong>Eligibility:</strong>
                        <p class="mb-0 small mt-2">This acceptance certificate can be generated because 30 days have passed since the conditional certificate was issued.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
