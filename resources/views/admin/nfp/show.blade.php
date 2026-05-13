@extends('layouts.app')

@section('title', 'NFP Return Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>NFP Compliance Return Details</h2>
    <div>
        <a href="{{ route('nfp.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        @if($return->status === 'submitted' && auth()->user()->role === 'admin')
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="bi bi-check-circle"></i> Review & Approve
            </button>
        @endif
        @if($return->status === 'draft')
            <a href="{{ route('nfp.edit', $return->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- License Information -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">License Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Licensee Name:</th><td>{{ $return->licensee_name }}</td></tr>
                    <tr><th>License No:</th><td>{{ $return->license_no ?? 'N/A' }}</td></tr>
                    <tr><th>Other Licenses:</th><td>{{ $return->other_licenses ?? 'N/A' }}</td></tr>
                    <tr><th>Financial Year:</th><td>{{ $return->financial_year }}</td></tr>
                    <tr><th>Quarter:</th><td>{{ $return->quarter }}</td></tr>
                </table>
            </div>
        </div>

        <!-- Address Information -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Address Information</h5>
            </div>
            <div class="card-body">
                @php
                    $physical = $return->physical_address ?? [];
                    $postal = $return->postal_address ?? [];
                    $contacts = $return->contacts ?? [];
                @endphp
                <h6>Physical Address:</h6>
                <p>{{ $physical['county'] ?? '' }}, {{ $physical['town'] ?? '' }}, {{ $physical['street_road'] ?? '' }}</p>
                <h6>Postal Address:</h6>
                <p>P.O. Box {{ $postal['p_o_box'] ?? '' }}, {{ $postal['postal_town'] ?? '' }} - {{ $postal['postal_code'] ?? '' }}</p>
                <h6>Contacts:</h6>
                <p>Tel: {{ $contacts['tel_no'] ?? '' }}, Mobile: {{ $contacts['mobile_no'] ?? '' }}</p>
                <p>Email: {{ $contacts['email'] ?? '' }}, Website: {{ $contacts['web_address'] ?? '' }}</p>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Status Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="mb-3">
                        <strong>Submitted:</strong>
                        {{ $return->submitted_at ? $return->submitted_at->format('d/m/Y H:i') : 'Not submitted' }}
                        @if($return->submitted_at)
                            <br><small class="text-muted">By: {{ $return->submitter->name }}</small>
                        @endif
                    </div>
                    @if($return->approved_at)
                        <div class="mb-3">
                            <strong>Approved/Rejected:</strong>
                            {{ $return->approved_at->format('d/m/Y H:i') }}
                            <br><small class="text-muted">By: {{ $return->approver->name ?? 'N/A' }}</small>
                            <br><small class="text-muted">Decision: {{ ucfirst($return->official_decision) }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Documents -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Attached Documents</h5>
            </div>
            <div class="card-body">
                @if($return->documents)
                    @foreach($return->documents as $key => $doc)
                        <div class="mb-2">
                            <a href="{{ Storage::url($doc) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-file-pdf"></i> {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </a>
                        </div>
                    @endforeach
                @else
                    <p>No documents attached.</p>
                @endif
            </div>
        </div>

        <!-- Compliance Info -->
        @if($return->status === 'approved')
        <div class="card mb-3 bg-success text-white">
            <div class="card-header">
                <h5 class="mb-0">Compliance Certificate</h5>
            </div>
            <div class="card-body">
                <p><strong>Certificate Number:</strong> {{ $return->certificate_number }}</p>
                <p><strong>Valid Until:</strong> {{ $return->certificate_valid_until ? date('d/m/Y', strtotime($return->certificate_valid_until)) : 'N/A' }}</p>
                <p><strong>Tracking Code:</strong> {{ $return->tracking_code }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Approval Modal -->
@if($return->status === 'submitted' && auth()->user()->role === 'admin')
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('nfp.approve', $return->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Review Compliance Return</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Decision:</label>
                        <div class="form-check">
                            <input type="radio" name="official_decision" value="approved" class="form-check-input" required>
                            <label class="form-check-label text-success">Approve</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="official_decision" value="rejected" class="form-check-input">
                            <label class="form-check-label text-danger">Reject</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Remarks:</label>
                        <textarea name="official_remarks" rows="4" class="form-control" placeholder="Enter approval/rejection remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Decision</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
    .timeline {
        padding-left: 20px;
        border-left: 2px solid #ddd;
    }
</style>
@endpush
@endsection
