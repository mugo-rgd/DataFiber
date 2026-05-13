@extends('layouts.app')

@section('title', 'ASP Return Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>ASP Compliance Return Details</h2>
    <div>
        <a href="{{ route('asp.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        @if($return->status === 'submitted' && auth()->user()->role === 'admin')
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="bi bi-check-circle"></i> Review & Approve
            </button>
        @endif
        @if($return->status === 'draft')
            <a href="{{ route('asp.edit', $return->id) }}" class="btn btn-warning">
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
                <p>Building: {{ $physical['building_name'] ?? '' }}, Floor: {{ $physical['floor_no'] ?? '' }}, Room: {{ $physical['room_no'] ?? '' }}</p>

                <h6>Postal Address:</h6>
                <p>P.O. Box {{ $postal['p_o_box'] ?? '' }}, {{ $postal['postal_town'] ?? '' }} - {{ $postal['postal_code'] ?? '' }}</p>

                <h6>Contacts:</h6>
                <p>Tel: {{ $contacts['tel_no'] ?? '' }}, Mobile: {{ $contacts['mobile_no'] ?? '' }}</p>
                <p>Email: {{ $contacts['email'] ?? '' }}, Website: {{ $contacts['web_address'] ?? '' }}</p>
                <p>CEO: {{ $contacts['ceo_name'] ?? '' }}, Contact Person: {{ $contacts['contact_person'] ?? '' }}</p>
                <p>Address Changed: {{ $return->address_changed ? 'Yes' : 'No' }}</p>
            </div>
        </div>

        <!-- M2M Services -->
        @if($return->m2m_services)
        <div class="card mb-3">
            <div class="card-header bg-warning">
                <h5 class="mb-0">M2M Services (IoT, Car Tracking, etc.)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Service</th><th>Description</th><th>Subscriptions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($return->m2m_services as $service)
                            @if(!empty($service['service']))
                            <tr>
                                <td>{{ $service['service'] ?? 'N/A' }}</td>
                                <td>{{ $service['description'] ?? 'N/A' }}</td>
                                <td>{{ $service['subscriptions'] ?? 'N/A' }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Cybersecurity -->
        @if($return->cybersecurity)
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Cybersecurity Readiness</h5>
            </div>
            <div class="card-body">
                @php $cyber = $return->cybersecurity; @endphp
                <p><strong>Has Cybersecurity Team:</strong> {{ isset($cyber['has_team']) && $cyber['has_team'] === 'yes' ? 'Yes' : 'No' }}</p>
                <p><strong>Staff Count:</strong> Total: {{ $cyber['staff_total'] ?? 'N/A' }}, Male: {{ $cyber['staff_male'] ?? 'N/A' }}, Female: {{ $cyber['staff_female'] ?? 'N/A' }}</p>
                <p><strong>Has Security Tools:</strong> {{ isset($cyber['has_tools']) && $cyber['has_tools'] === 'yes' ? 'Yes' : 'No' }}</p>
                @if(!empty($cyber['tools_deployed']))
                    <p><strong>Tools Deployed:</strong> {{ $cyber['tools_deployed'] }}</p>
                @endif
                <p><strong>Had Cyber Incident:</strong> {{ isset($cyber['had_incident']) && $cyber['had_incident'] === 'yes' ? 'Yes' : 'No' }}</p>
                @if(!empty($cyber['incident_types']))
                    <p><strong>Incident Types:</strong> {{ implode(', ', $cyber['incident_types']) }}</p>
                @endif
                <p><strong>Has Awareness Initiatives:</strong> {{ isset($cyber['has_awareness']) && $cyber['has_awareness'] === 'yes' ? 'Yes' : 'No' }}</p>
                @if(!empty($cyber['awareness_activities']))
                    <p><strong>Awareness Activities:</strong> {{ $cyber['awareness_activities'] }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Quality of Service -->
        @if($return->quality_of_service)
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Quality of Service Metrics</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Indicator</th><th>Score</th></tr>
                        </thead>
                        <tbody>
                            @foreach($return->quality_of_service as $indicator => $score)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $indicator)) }}</td>
                                <td>{{ $score }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- PWD Compliance -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">PWD Compliance (KS2952 Standard)</h5>
            </div>
            <div class="card-body">
                <p><strong>Aware of Standard:</strong> {{ $return->pwd_aware ? 'Yes' : 'No' }}</p>
                <p><strong>Complied with Standard:</strong> {{ $return->pwd_complied ? 'Yes' : 'No' }}</p>

                @if($return->pwd_actions)
                    <p><strong>Actions Taken:</strong></p>
                    <p>{{ $return->pwd_actions }}</p>
                @endif

                @if($return->pwd_challenges)
                    <p><strong>Challenges Faced:</strong></p>
                    <p>{{ $return->pwd_challenges }}</p>
                @endif

                @if($return->pwd_future_plans)
                    <p><strong>Future Plans:</strong></p>
                    <p>{{ $return->pwd_future_plans }}</p>
                @endif
            </div>
        </div>

        <!-- Environmental Sustainability -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Environmental Sustainability</h5>
            </div>
            <div class="card-body">
                @if($return->ewaste_initiatives)
                    <p><strong>E-waste Initiatives:</strong></p>
                    <p>{{ $return->ewaste_initiatives }}</p>
                @endif

                @if($return->carbon_initiatives)
                    <p><strong>Carbon Footprint Reduction:</strong></p>
                    <p>{{ $return->carbon_initiatives }}</p>
                @endif

                @if($return->emca_status)
                    <p><strong>EMCA Compliance Status:</strong></p>
                    <p>{{ $return->emca_status }}</p>
                @endif
            </div>
        </div>

        <!-- Comments -->
        @if($return->comments)
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Comments/Suggestions</h5>
            </div>
            <div class="card-body">
                <p>{{ $return->comments }}</p>
            </div>
        </div>
        @endif

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
                            @if($return->official_remarks)
                                <br><small class="text-muted">Remarks: {{ $return->official_remarks }}</small>
                            @endif
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
                @if($return->documents && count($return->documents) > 0)
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

        <!-- Submitter Information -->
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Submitter Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $return->submitter_name }}</p>
                <p><strong>Title:</strong> {{ $return->submitter_title ?? 'N/A' }}</p>
                <p><strong>Date:</strong> {{ $return->submitter_date ? date('d/m/Y', strtotime($return->submitter_date)) : 'N/A' }}</p>
                @if($return->company_stamp_path)
                    <a href="{{ Storage::url($return->company_stamp_path) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="bi bi-image"></i> View Company Stamp
                    </a>
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
            <form action="{{ route('asp.approve', $return->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Review ASP Compliance Return</h5>
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
