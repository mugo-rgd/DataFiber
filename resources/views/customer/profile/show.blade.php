{{-- resources/views/customer/profile/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('customer.customer-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Profile</li>
                    </ol>
                </div>
                <h4 class="page-title">Company Profile</h4>
            </div>
        </div>
    </div>

    <!-- Company Profile Information -->
    @if($companyProfile)
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="header-title mb-0">Company Information</h4>
                        <a href="{{ route('customer.profile.edit') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit Profile
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <p class="form-control-static fw-bold">{{ $companyProfile->company_name ?? 'Not set' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">KRA PIN</label>
                                <p class="form-control-static">{{ $companyProfile->kra_pin ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Registration Number</label>
                                <p class="form-control-static">{{ $companyProfile->registration_number ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company Type</label>
                                <p class="form-control-static">{{ ucwords($companyProfile->company_type ?? 'Not set') }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <p class="form-control-static">{{ $companyProfile->phone_number ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact Person 1</label>
                                <p class="form-control-static">{{ $companyProfile->contact_name_1 ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        @if($companyProfile->contact_name_2)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact Person 2</label>
                                <p class="form-control-static">{{ $companyProfile->contact_name_2 }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact Phone 2</label>
                                <p class="form-control-static">{{ $companyProfile->contact_phone_2 }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Physical Address</label>
                                <p class="form-control-static">
                                    {{ $companyProfile->address ?? '' }}
                                    @if($companyProfile->road)
                                        <br>Road: {{ $companyProfile->road }}
                                    @endif
                                    @if($companyProfile->town)
                                        <br>Town: {{ $companyProfile->town }}
                                    @endif
                                    @if($companyProfile->code)
                                        <br>Postal Code: {{ $companyProfile->code }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($companyProfile->description)
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <p class="form-control-static">{{ $companyProfile->description }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Account Statistics</h4>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Account Type</h6>
                                <small class="text-muted">User role</small>
                            </div>
                            <span class="badge bg-primary">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Active Leases</h6>
                                <small class="text-muted">Current projects</small>
                            </div>
                            <span class="badge bg-success rounded-pill">
                                {{ $user->leases()->where('status', 'active')->count() }}
                            </span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Profile Documents</h6>
                                <small class="text-muted">Uploaded & approved</small>
                            </div>
                            <span class="badge bg-info rounded-pill">
                                {{ $approvedDocs }}/{{ $totalDocs }}
                            </span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Pending Review</h6>
                                <small class="text-muted">Awaiting approval</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">
                                {{ $pendingDocs }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="font-16">Account Created</h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ $user->created_at->format('F d, Y') }}
                        </p>
                        <p class="text-muted mb-0">
                            <i class="fas fa-clock me-2"></i>
                            {{ $user->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-building fa-4x text-muted mb-4"></i>
                    <h5 class="text-muted">No Company Profile</h5>
                    <p class="text-muted mb-4">You haven't created a company profile yet.</p>
                    <a href="{{ route('customer.profile.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Company Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Document Requirements -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Document Requirements</h4>

                    @if(count($missingDocuments) > 0)
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Missing Required Documents
                            </h6>
                            <p class="mb-2">Please upload the following documents:</p>
                            <ul class="mb-0">
                                @foreach($missingDocuments as $doc)
                                <li>{{ $doc }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            All required documents have been uploaded.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Required Documents Status</h6>
                        <a href="{{ route('customer.documents.profile.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload me-1"></i>Upload Document
                        </a>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requiredDocumentTypes as $docType)
                                @php
                                    $document = $documents->where('document_type', $docType->document_type)->first();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $docType->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $docType->description }}</small>
                                    </td>
                                    <td>
                                        @if($document)
                                            <span class="badge bg-{{ $document->status == 'approved' ? 'success' : ($document->status == 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($document->status) }}
                                                @if($document->expiry_date && $document->expiry_date->isPast())
                                                    <br><small class="text-danger">Expired</small>
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge bg-danger">Missing</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($document)
                                            {{ $document->updated_at->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($document)
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ Storage::url($document->file_path) }}"
                                                   target="_blank" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ Storage::url($document->file_path) }}"
                                                   download class="btn btn-outline-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ route('customer.documents.profile.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-upload me-1"></i>Upload
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Uploaded Documents -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-0">All Profile Documents</h4>
                        <a href="{{ route('customer.documents.profile.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Document
                        </a>
                    </div>

                    @if($documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Document</th>
                                        <th>Type</th>
                                        <th>Upload Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $doc)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    @if($doc->mime_type == 'application/pdf')
                                                        <i class="fas fa-file-pdf text-danger fs-4"></i>
                                                    @elseif(in_array($doc->mime_type, ['image/jpeg', 'image/png']))
                                                        <i class="fas fa-file-image text-success fs-4"></i>
                                                    @else
                                                        <i class="fas fa-file text-secondary fs-4"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">{{ $doc->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $doc->file_name }}
                                                        @if($doc->description)
                                                            <br>{{ Str::limit($doc->description, 50) }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucwords(str_replace('_', ' ', $doc->document_type)) }}
                                            </span>
                                        </td>
                                        <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($doc->status) }}
                                                @if($doc->expiry_date && $doc->expiry_date->isPast())
                                                    <br><small class="text-danger">Expired</small>
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ Storage::url($doc->file_path) }}"
                                                   target="_blank" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ Storage::url($doc->file_path) }}"
                                                   download class="btn btn-outline-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($doc->status !== 'approved')
                                                <form action="{{ route('customer.documents.profile.destroy', $doc->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Documents Uploaded</h5>
                            <p class="text-muted mb-3">You haven't uploaded any profile documents yet.</p>
                            <a href="{{ route('customer.documents.profile.create') }}" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Your First Document
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
