{{-- resources/views/conversion-data/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Conversion Data Details #' . $item->id)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('conversion-data.index') }}">Conversion Data</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View #{{ $item->id }}</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-database me-2"></i>Conversion Data Details
                        <span class="badge bg-secondary ms-2">ID: {{ $item->id }}</span>
                    </h1>
                    <p class="text-muted mb-0">View detailed information about this conversion record</p>
                </div>

                <div class="btn-toolbar">
                    <div class="btn-group me-2">
                        <a href="{{ route('conversion-data.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                        <a href="{{ route('conversion-data.edit', $item->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="window.print();">
                                    <i class="fas fa-print me-2"></i> Print
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                                    <i class="fas fa-download me-2"></i> Export
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('conversion-data.destroy', $item->id) }}" method="POST" id="deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i> Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Main Information Column --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Basic Information
                    </h5>
                    <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($item->status ?? 'N/A') }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Customer Reference</label>
                                <div class="d-flex align-items-center">
                                    <span class="lead">{{ $item->customer_ref ?? 'Not provided' }}</span>
                                    @if($item->customer_ref)
                                    <button class="btn btn-sm btn-link ms-2 copy-btn" data-text="{{ $item->customer_ref }}">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Data Source</label>
                                <p class="mb-0">
                                    <span class="badge bg-info">
                                        {{ $item->data_source ?? 'Manual Entry' }}
                                    </span>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Conversion Type</label>
                                <p class="mb-0">{{ $item->conversion_type ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Value</label>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold fs-5 {{ $item->value >= 0 ? 'text-success' : 'text-danger' }}">
                                        £{{ number_format($item->value ?? 0, 2) }}
                                    </span>
                                    @if($item->value_percentage)
                                    <span class="badge bg-light text-dark ms-2">
                                        {{ $item->value_percentage }}%
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Created By</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-user text-muted"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">{{ $item->created_by_name ?? 'System' }}</p>
                                        <small class="text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Last Updated</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="fas fa-history text-muted"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">{{ $item->updated_by_name ?? 'N/A' }}</p>
                                        <small class="text-muted">{{ $item->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Tags</label>
                                <div>
                                    @if($item->tags && is_array($item->tags))
                                        @foreach($item->tags as $tag)
                                            <span class="badge bg-light text-dark me-1 mb-1">{{ $tag }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No tags</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Details Card --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2 text-primary"></i>Additional Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Campaign</label>
                                <p class="mb-0">{{ $item->campaign_name ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Conversion Date</label>
                                <p class="mb-0">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ optional($item->conversion_date)->format('d/m/Y') ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Channel</label>
                                <p class="mb-0">{{ $item->channel ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Medium</label>
                                <p class="mb-0">{{ $item->medium ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Source</label>
                                <p class="mb-0">{{ $item->source ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Device Type</label>
                                <p class="mb-0">
                                    @if($item->device_type)
                                        <i class="fas fa-{{ $item->device_type === 'mobile' ? 'mobile-alt' : ($item->device_type === 'tablet' ? 'tablet-alt' : 'desktop') }} me-1"></i>
                                        {{ ucfirst($item->device_type) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Notes Section --}}
                    @if($item->notes)
                    <div class="mt-4 pt-3 border-top">
                        <label class="form-label text-muted small mb-1">Notes</label>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($item->notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar Column --}}
        <div class="col-lg-4">
            {{-- Status Card --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Status & Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Conversion Rate</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ min($item->conversion_rate ?? 0, 100) }}%;"
                                 aria-valuenow="{{ $item->conversion_rate ?? 0 }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                                {{ $item->conversion_rate ?? 0 }}%
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <div class="text-muted small mb-1">Sessions</div>
                                <div class="h4 mb-0">{{ $item->sessions ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <div class="text-muted small mb-1">Clicks</div>
                                <div class="h4 mb-0">{{ $item->clicks ?? 0 }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label text-muted small mb-1">Quality Score</label>
                        <div class="d-flex align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= ($item->quality_score ?? 0) ? 'text-warning' : 'text-light' }}"></i>
                            @endfor
                            <span class="ms-2">{{ $item->quality_score ?? 'N/A' }}/5</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Data Card --}}
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link me-2 text-primary"></i>Related Data
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @if($item->customer_id)
                        <a href="{{ route('conversion-data.show', $item->customer_id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user me-2"></i>
                                Customer Profile
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        @endif

                        @if($item->campaign_id)
                        <a href="{{ route('marketing-admin.campaigns', $item->campaign_id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-bullhorn me-2"></i>
                                Campaign Details
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        @endif

                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-history me-2"></i>
                                View Audit Log
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2 text-primary"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                            <i class="fas fa-copy me-1"></i> Duplicate Entry
                        </button>
                        <button class="btn btn-outline-secondary" onclick="downloadAsPDF()">
                            <i class="fas fa-file-pdf me-1"></i> Download as PDF
                        </button>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fas fa-share-alt me-1"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <a href="{{ route('conversion-data.export', ['id' => $item->id, 'format' => 'pdf']) }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-pdf text-danger me-2"></i> Export as PDF
                    </a>
                    <a href="{{ route('conversion-data.export', ['id' => $item->id, 'format' => 'csv']) }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-csv text-success me-2"></i> Export as CSV
                    </a>
                    <a href="{{ route('conversion-data.export', ['id' => $item->id, 'format' => 'excel']) }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-excel text-success me-2"></i> Export as Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('conversion-data.duplicate', $item->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateModalLabel">Duplicate Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Create a copy of this conversion data entry?</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="copyReferences" name="copy_references" checked>
                        <label class="form-check-label" for="copyReferences">
                            Copy all references
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Duplicate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Share This Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="shareLink" class="form-label">Shareable Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareLink" value="{{ url()->current() }}" readonly>
                        <button class="btn btn-outline-secondary copy-btn" type="button" data-target="#shareLink">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Share via</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope"></i> Email
                        </button>
                        <button class="btn btn-outline-info btn-sm">
                            <i class="fas fa-slack"></i> Slack
                        </button>
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-whatsapp"></i> WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
    .card-header {
        border-bottom: 2px solid #f0f0f0;
    }
    .form-label {
        font-weight: 500;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .progress {
        background-color: #e9ecef;
    }
    .copy-btn {
        cursor: pointer;
        transition: all 0.2s;
    }
    .copy-btn:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this conversion data? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    }

    function downloadAsPDF() {
        // Implement PDF download functionality
        alert('PDF download functionality would be implemented here.');
    }

    // Copy to clipboard functionality
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', function() {
            const text = this.getAttribute('data-text') ||
                         this.getAttribute('data-target') ?
                         document.querySelector(this.getAttribute('data-target')).value : '';

            navigator.clipboard.writeText(text).then(() => {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.classList.add('text-success');

                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.classList.remove('text-success');
                }, 2000);
            });
        });
    });

    // Print specific section
    function printSection() {
        const printContent = document.querySelector('.container-fluid').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
            <html>
                <head>
                    <title>Conversion Data #{{ $item->id }}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body>
                    <div class="container mt-4">
                        <h1 class="mb-4">Conversion Data Details #{{ $item->id }}</h1>
                        ${printContent}
                    </div>
                </body>
            </html>
        `;

        window.print();
        document.body.innerHTML = originalContent;
        window.location.reload();
    }
</script>
@endpush
