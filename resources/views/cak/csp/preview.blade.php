@extends('layouts.app')

@section('title', 'CSP Compliance Return Preview')
@section('page-title', 'Content Service Provider (CSP) Compliance Return')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('csp.index') }}">CSP Returns</a></li>
<li class="breadcrumb-item active">Preview #{{ $record->id }}</li>
@endsection

@section('content')
<div class="container">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-envelope me-2 text-kp-green"></i> CSP Compliance Preview
            </h4>
            <small class="text-muted">Return ID: #{{ $record->id }} | Created: {{ $record->created_at->format('d M Y H:i') }}</small>
        </div>

        <div>
            <span class="badge
                @if($record->status == 'draft') bg-secondary
                @elseif($record->status == 'generated') bg-dark
                @elseif($record->status == 'submitted') bg-kp-yellow text-dark
                @elseif($record->status == 'submitted_to_cak') bg-info
                @elseif($record->status == 'under_review') bg-kp-blue
                @elseif($record->status == 'approved') bg-kp-green
                @elseif($record->status == 'rejected') bg-danger
                @else bg-secondary @endif
                p-2 fs-6">
                <i class="fas
                    @if($record->status == 'draft') fa-pencil-alt
                    @elseif($record->status == 'approved') fa-check-circle
                    @elseif($record->status == 'rejected') fa-times-circle
                    @elseif($record->status == 'submitted_to_cak') fa-paper-plane
                    @else fa-clock
                    @endif me-1"></i>
                {{ strtoupper(str_replace('_', ' ', $record->status)) }}
            </span>

            @if($record->cak_reference_number)
                <span class="badge bg-info ms-2">
                    <i class="fas fa-hashtag me-1"></i> CAK Ref: {{ $record->cak_reference_number }}
                </span>
            @endif
        </div>
    </div>

    <!-- STATUS WORKFLOW INDICATOR -->
    <div class="card shadow-sm mb-3 bg-light">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap small">
                <div class="text-center {{ $record->status != 'draft' ? 'text-kp-green' : 'text-muted' }}">
                    <i class="fas fa-pencil-alt"></i> Draft
                </div>
                <div class="text-muted">→</div>
                <div class="text-center {{ $record->status == 'submitted' ? 'text-kp-yellow' : ($record->status != 'draft' ? 'text-kp-green' : 'text-muted') }}">
                    <i class="fas fa-paper-plane"></i> Submitted
                </div>
                <div class="text-muted">→</div>
                <div class="text-center {{ in_array($record->status, ['submitted_to_cak', 'under_review']) ? 'text-info' : ($record->status == 'approved' ? 'text-kp-green' : 'text-muted') }}">
                    <i class="fas fa-clock"></i> Under Review
                </div>
                <div class="text-muted">→</div>
                <div class="text-center {{ $record->status == 'approved' ? 'text-kp-green' : ($record->status == 'rejected' ? 'text-danger' : 'text-muted') }}">
                    <i class="fas {{ $record->status == 'approved' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    {{ $record->status == 'approved' ? 'Approved' : ($record->status == 'rejected' ? 'Rejected' : 'Final') }}
                </div>
            </div>
        </div>
    </div>

    <!-- BASIC INFO CARD -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <strong><i class="fas fa-info-circle me-1 text-kp-blue"></i> Basic Information</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted small text-uppercase">Licensee Name</label>
                    <div class="fw-bold">{{ $record->licensee_name ?? '—' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted small text-uppercase">License Number</label>
                    <div class="fw-bold">{{ $record->license_no ?? '—' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted small text-uppercase">Financial Year</label>
                    <div class="fw-bold">{{ $record->financial_year ?? '—' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted small text-uppercase">Quarter</label>
                    <div class="fw-bold">{{ $record->quarter ?? '—' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted small text-uppercase">Other Licenses</label>
                    <div class="fw-bold">{{ $record->other_licenses ?? '—' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted small text-uppercase">Submitted At</label>
                    <div class="fw-bold">
                        {{ $record->submitted_at ? \Carbon\Carbon::parse($record->submitted_at)->format('d M Y H:i') : 'Not submitted yet' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTACT INFORMATION -->
    @if($record->contact_person || $record->contact_mobile || $record->contact_email)
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <strong><i class="fas fa-address-card me-1 text-info"></i> Contact Information</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label class="text-muted small">Contact Person</label>
                    <div>{{ $record->contact_person ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="text-muted small">Mobile Number</label>
                    <div>{{ $record->contact_mobile ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="text-muted small">Email Address</label>
                    <div>{{ $record->contact_email ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ACTION BUTTONS -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">

                <!-- PRINT -->
                <a href="{{ route('csp.print', $record->id) }}" target="_blank" class="btn btn-kp-primary">
                    <i class="fas fa-print me-1"></i> Print / Download PDF
                </a>

                <!-- EDIT (only for draft or rejected) -->
                @if(in_array($record->status, ['draft', 'generated', 'rejected']))
                    <a href="{{ route('csp.edit', $record->id) }}" class="btn btn-kp-warning">
                        <i class="fas fa-edit me-1"></i> Edit Return
                    </a>
                @endif

                <!-- GENERATE PDF (if generated status) -->
                @if($record->status === 'generated')
                    <form method="POST" action="{{ route('csp.generate-pdf', $record->id) }}">
                        @csrf
                        <button class="btn btn-secondary">
                            <i class="fas fa-file-pdf me-1"></i> Regenerate PDF
                        </button>
                    </form>
                @endif

                <!-- SUBMIT TO CAK (if submitted status) -->
                @if($record->status === 'submitted')
                    <form method="POST" action="{{ route('csp.submit-to-cak', $record->id) }}"
                          onsubmit="return confirm('Submit this return to the Communications Authority of Kenya for review?\n\nThis action cannot be undone.')">
                        @csrf
                        <button class="btn btn-kp-success">
                            <i class="fas fa-paper-plane me-1"></i> Submit to CAK
                        </button>
                    </form>
                @endif

                <!-- EMAIL TO CAK -->
                @if(in_array($record->status, ['submitted_to_cak', 'under_review', 'approved']))
                    <form action="{{ route('csp.email-cak', $record->id) }}" method="POST"
                          onsubmit="return confirm('Send this return via email to CAK?')">
                        @csrf
                        <button class="btn btn-info">
                            <i class="fas fa-envelope me-1"></i> Email to CAK
                        </button>
                    </form>
                @endif

                <!-- CHECK CAK STATUS -->
                @if(in_array($record->status, ['submitted_to_cak', 'under_review']))
                    <button type="button" class="btn btn-outline-kp-primary" onclick="checkCAKStatus({{ $record->id }})">
                        <i class="fas fa-sync-alt me-1"></i> Check CAK Status
                    </button>
                @endif

                <!-- DELETE -->
                @if(in_array($record->status, ['draft', 'generated', 'rejected']))
                    <form method="POST" action="{{ route('csp.destroy', $record->id) }}"
                          onsubmit="return confirm('⚠️ WARNING: This action cannot be undone.\n\nAre you sure you want to delete this CSP return?')"
                          style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Return
                        </button>
                    </form>
                @endif

                <!-- BACK BUTTON -->
                <a href="{{ route('csp.index') }}" class="btn btn-dark">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>

            </div>
        </div>
    </div>

    <!-- FORM DATA DISPLAY (PARSED FROM JSON) -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong><i class="fas fa-database me-1 text-kp-green"></i> Captured Form Data</strong>
            <button class="btn btn-sm btn-outline-secondary" onclick="toggleRawData()">
                <i class="fas fa-code"></i> Toggle Raw JSON
            </button>
        </div>
        <div class="card-body">

            <!-- Display formatted data -->
            <div id="formattedData">
                @php
                    $formData = is_string($record->form_data) ? json_decode($record->form_data, true) : $record->form_data;
                @endphp

                @if($formData && count($formData) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr><th width="30%">Field</th><th>Value</th></tr>
                            </thead>
                            <tbody>
                                @foreach($formData as $key => $value)
                                    @if(!in_array($key, ['_token', '_method']))
                                        <tr>
                                            <td class="fw-bold">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                            <td>
                                                @if(is_array($value))
                                                    <pre class="mb-0 small bg-light p-2 rounded" style="max-height: 200px; overflow: auto;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @elseif(is_string($value) && Str::startsWith($value, ['http://', 'https://']))
                                                    <a href="{{ $value }}" target="_blank">{{ $value }}</a>
                                                @else
                                                    {{ $value ?: '—' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-1"></i> No additional form data captured.
                    </div>
                @endif
            </div>

            <!-- Raw JSON display (hidden by default) -->
            <div id="rawData" style="display: none;">
                <pre class="bg-dark text-light p-3 rounded" style="font-size: 12px; max-height: 500px; overflow: auto;">
{{ json_encode($record->form_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                </pre>
            </div>

        </div>
    </div>

    <!-- TIMELINE / AUDIT TRAIL -->
    @if($record->created_at || $record->updated_at || $record->submitted_at)
    <div class="card shadow-sm mt-3">
        <div class="card-header bg-white">
            <strong><i class="fas fa-history me-1"></i> Audit Trail</strong>
        </div>
        <div class="card-body">
            <div class="row small">
                <div class="col-md-4">
                    <label class="text-muted">Created:</label>
                    <div>{{ $record->created_at ? $record->created_at->format('d M Y H:i:s') : '—' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="text-muted">Last Modified:</label>
                    <div>{{ $record->updated_at ? $record->updated_at->format('d M Y H:i:s') : '—' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="text-muted">Submitted to CAK:</label>
                    <div>{{ $record->submitted_at ? \Carbon\Carbon::parse($record->submitted_at)->format('d M Y H:i:s') : '—' }}</div>
                </div>
            </div>
            @if($record->cak_approved_at)
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label class="text-muted">CAK Approved:</label>
                        <div class="text-kp-green">{{ \Carbon\Carbon::parse($record->cak_approved_at)->format('d M Y H:i:s') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
function checkCAKStatus(recordId) {
    fetch(`/csp/${recordId}/cak-status`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        let statusMessage = `CAK Status: ${data.status?.toUpperCase() || 'Unknown'}\n`;
        if (data.reference_number) {
            statusMessage += `Reference Number: ${data.reference_number}\n`;
        }
        if (data.message) {
            statusMessage += `Message: ${data.message}\n`;
        }
        statusMessage += `Last Updated: ${data.updated_at || 'N/A'}`;

        alert(statusMessage);

        if (data.status === 'approved' || data.status === 'rejected') {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Status check failed. Please try again.');
    });
}

function toggleRawData() {
    const formatted = document.getElementById('formattedData');
    const raw = document.getElementById('rawData');

    if (formatted.style.display === 'none') {
        formatted.style.display = 'block';
        raw.style.display = 'none';
    } else {
        formatted.style.display = 'none';
        raw.style.display = 'block';
    }
}
</script>
@endpush

@endsection
