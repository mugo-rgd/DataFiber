@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">My Profile</h1>
        <div>
            @if($companyProfile)
                <a href="{{ route('customer.profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            @else
                <a href="{{ route('customer.profile.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Profile
                </a>
            @endif
                    <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-primary">
    <i class="fas fa-folder me-2"></i>View Documents
</a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Personal Information -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Name:</div>
                        <div class="col-sm-8">{{ Auth::user()->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Email:</div>
                        <div class="col-sm-8">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Role:</div>
                        <div class="col-sm-8">
                            <span class="badge bg-info text-dark text-capitalize">
                                {{ Auth::user()->role ?? 'customer' }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Account Created:</div>
                        <div class="col-sm-8">{{ Auth::user()->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 fw-bold">Last Login:</div>
                        <div class="col-sm-8">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('M d, Y H:i') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Information -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>Company Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($companyProfile)
                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">KRA PIN:</div>
                            <div class="col-sm-8">{{ $companyProfile->kra_pin ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Phone Number:</div>
                            <div class="col-sm-8">{{ $companyProfile->phone_number ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Registration Number:</div>
                            <div class="col-sm-8">{{ $companyProfile->registration_number ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Company Type:</div>
                            <div class="col-sm-8">{{ $companyProfile->company_type ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Primary Contact:</div>
                            <div class="col-sm-8">
                                {{ $companyProfile->contact_name_1 ?? 'Not provided' }}
                                ({{ $companyProfile->contact_phone_1 ?? 'N/A' }})
                            </div>
                        </div>
                        @if($companyProfile->contact_name_2)
                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Secondary Contact:</div>
                            <div class="col-sm-8">
                                {{ $companyProfile->contact_name_2 }}
                                ({{ $companyProfile->contact_phone_2 ?? 'N/A' }})
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-sm-4 fw-bold">Address:</div>
                            <div class="col-sm-8">{{ $companyProfile->full_address ?? 'Not provided' }}</div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No company profile found.</p>
                            <a href="{{ route('customer.profile.create') }}" class="btn btn-primary">
                                Create Company Profile
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Section -->
<!-- Documents Section -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>Uploaded Documents
                    @if($documents && $documents->count() > 0)
                        <span class="badge bg-dark ms-2">{{ $documents->count() }} files</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($documents && $documents->count() > 0)
                    <!-- Documents Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center py-2">
                                    <h6 class="mb-1">{{ $documents->where('status', 'approved')->count() }}</h6>
                                    <small>Approved</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center py-2">
                                    <h6 class="mb-1">{{ $documents->where('status', 'pending_review')->count() }}</h6>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center py-2">
                                    <h6 class="mb-1">{{ $documents->where('status', 'rejected')->count() }}</h6>
                                    <small>Rejected</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center py-2">
                                    <h6 class="mb-1">{{ $documents->count() }}</h6>
                                    <small>Total</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents List -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Document Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Upload Date</th>
                                    <th>File Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas
                                                @if(str_contains($document->document_type, 'contract')) fa-file-contract
                                                @elseif(str_contains($document->document_type, 'certificate')) fa-file-certificate
                                                @elseif(str_contains($document->document_type, 'report')) fa-file-chart-line
                                                @else fa-file
                                                @endif
                                                me-3 text-primary">
                                            </i>
                                            <div>
                                                <strong>{{ $document->name }}</strong>
                                                @if($document->is_required)
                                                    <span class="badge bg-danger ms-2">Required</span>
                                                @endif
                                                @if($document->description)
                                                    <br><small class="text-muted">{{ Str::limit($document->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary text-capitalize">
                                            {{ str_replace('_', ' ', $document->document_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge bg-{{ $document->status === 'approved' ? 'success' : ($document->status === 'pending_review' ? 'warning' : 'danger') }}">
                                            <i class="fas
                                                @if($document->status === 'approved') fa-check-circle
                                                @elseif($document->status === 'pending_review') fa-clock
                                                @else fa-times-circle
                                                @endif
                                                me-1">
                                            </i>
                                            {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                        </span>
                                        @if($document->rejection_reason)
                                            <br><small class="text-danger">{{ Str::limit($document->rejection_reason, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $document->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $document->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        {{ number_format($document->file_size / 1024, 1) }} KB
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('customer.documents.download', $document) }}"
                                               class="btn btn-outline-success"
                                               title="Download Document">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('customer.documents.show', $document) }}"
                                               class="btn btn-outline-primary"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Upload More Button -->
                    <div class="text-center mt-4">
                        <a href="{{ route('customer.documents.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-2"></i>Upload More Documents
                        </a>
                        <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-folder me-2"></i>View All Documents
                        </a>
                    </div>
                @else
                    <!-- No Documents Message -->
                    <div class="text-center py-5">
                        <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No documents uploaded</h5>
                        <p class="text-muted mb-4">Upload your company documents to get started.</p>
                        <a href="{{ route('customer.documents.create') }}" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Documents
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

    <!-- Quick Stats -->
    @if($companyProfile)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Profile Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $companyProfile->isComplete() ? '✓' : '✗' }}</h3>
                                    <small class="text-muted">Profile Complete</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $totalDocumentCount ?? 0 }}</h3>
                                    <small class="text-muted">Total Documents</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $documentTypesCount ?? 0 }}</h3>
                                    <small class="text-muted">Document Types</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $companyProfile->created_at->diffForHumans() }}</h3>
                                    <small class="text-muted">Profile Age</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.document-item {
    transition: all 0.2s ease;
    border: 1px solid #e9ecef;
}

.document-item:hover {
    background-color: #f8f9fa !important;
    border-color: #007bff;
    transform: translateY(-1px);
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.badge {
    font-size: 0.7em;
}
</style>

<!-- Font Awesome for icons -->
<script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
@endsection
