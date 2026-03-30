@extends('layouts.app')

@section('title', 'Welcome - Complete Your Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @php
                use App\Models\DocumentType;
                use App\Models\Document;

                // ============================================
                // PROFILE FIELDS CALCULATION (60% weight)
                // ============================================
                $totalProfileFields = 11;
                $completedProfileFields = 0;
                $profileFields = [
                    'kra_pin', 'phone_number', 'registration_number', 'company_type',
                    'contact_name_1', 'contact_phone_1', 'physical_location', 'road',
                    'town', 'address', 'code'
                ];

                if ($user->companyProfile) {
                    $profile = $user->companyProfile->toArray();
                    foreach ($profileFields as $field) {
                        if (!empty($profile[$field])) {
                            $completedProfileFields++;
                        }
                    }
                }

                $profileCompletionPercentage = $totalProfileFields > 0
                    ? ($completedProfileFields / $totalProfileFields) * 100
                    : 0;

                // ============================================
                // DOCUMENTS CALCULATION (40% weight)
                // ============================================
                // Get all required document types
                $requiredDocTypes = DocumentType::where('is_required', true)
                    ->pluck('document_type')
                    ->toArray();

                // Get uploaded document types with status filter
                $uploadedDocTypes = Document::where('user_id', $user->id)
                    ->whereIn('status', ['approved', 'pending_review', 'pending'])
                    ->distinct('document_type')
                    ->pluck('document_type')
                    ->toArray();

                // Calculate document stats
                $uploadedDocumentTypes = count($uploadedDocTypes);
                $totalRequiredDocumentTypes = count($requiredDocTypes);
                $missingDocumentTypes = array_diff($requiredDocTypes, $uploadedDocTypes);
                $allDocumentsUploaded = $uploadedDocumentTypes >= $totalRequiredDocumentTypes;

                $documentCompletionPercentage = $totalRequiredDocumentTypes > 0
                    ? ($uploadedDocumentTypes / $totalRequiredDocumentTypes) * 100
                    : 0;

                // ============================================
                // TOTAL COMPLETION (60% profile + 40% documents)
                // ============================================
                $weightedProfileCompletion = ($completedProfileFields / $totalProfileFields) * 60;
                $weightedDocumentCompletion = $totalRequiredDocumentTypes > 0
                    ? ($uploadedDocumentTypes / $totalRequiredDocumentTypes) * 40
                    : 0;

                $totalCompletion = $weightedProfileCompletion + $weightedDocumentCompletion;
                $totalCompletionRounded = min(100, (int) round($totalCompletion));

                // Get pending documents for warning message
                $pendingDocs = Document::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->get();

                // Determine threshold (70% for access)
                $canAccessDashboard = $totalCompletionRounded >= 70;
            @endphp

            <!-- Debug Info (remove in production) -->
            <!--
            Profile Fields: {{ $completedProfileFields }}/{{ $totalProfileFields }} ({{ round($profileCompletionPercentage, 1) }}%)
            Documents: {{ $uploadedDocumentTypes }}/{{ $totalRequiredDocumentTypes }} ({{ round($documentCompletionPercentage, 1) }}%)
            Weighted Profile: {{ round($weightedProfileCompletion, 1) }}%
            Weighted Documents: {{ round($weightedDocumentCompletion, 1) }}%
            Total Completion: {{ $totalCompletionRounded }}%
            -->

            <!-- Show warning about pending documents -->
            @if($pendingDocs->count() > 0)
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Documents Under Review:</strong>
                    You have {{ $pendingDocs->count() }} document(s) awaiting review.
                    @foreach($pendingDocs as $doc)
                        <br><small>- {{ ucwords(str_replace('_', ' ', $doc->document_type)) }}</small>
                    @endforeach
                </div>
            @endif

            <div class="card border-{{ $canAccessDashboard ? 'success' : 'warning' }}">
                <div class="card-header bg-{{ $canAccessDashboard ? 'success' : 'warning' }} text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Welcome to Your Dashboard
                    </h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        @if($canAccessDashboard)
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        @else
                            <i class="fas fa-user-check fa-4x text-warning mb-3"></i>
                        @endif
                        <h3>Welcome, {{ $user->name }}!</h3>
                        <p class="text-muted">
                            @if($allDocumentsUploaded && $pendingDocs->count() == 0 && $profileCompletionPercentage >= 80)
                                Your profile is complete! You can now access all system features.
                            @elseif($allDocumentsUploaded && $pendingDocs->count() > 0)
                                All documents uploaded! Waiting for review.
                            @else
                                We're excited to have you on board. Please complete your profile to get started.
                            @endif
                        </p>
                    </div>

                    <!-- Profile Completion Progress -->
                    <div class="mb-4">
                        <h5>Profile Completion: {{ number_format($totalCompletionRounded, 1) }}%</h5>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-{{ $canAccessDashboard ? 'success' : 'warning' }}"
                                 style="width: {{ $totalCompletionRounded }}%">
                                {{ number_format($totalCompletionRounded, 1) }}%
                            </div>
                        </div>

                        <!-- Detailed Progress Breakdown -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-id-card me-2"></i>Profile Fields (60%)</h6>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-info"
                                                 style="width: {{ $profileCompletionPercentage }}%">
                                            </div>
                                        </div>
                                        <small>{{ $completedProfileFields }}/{{ $totalProfileFields }} fields completed</small>
                                        <br>
                                        <small class="text-muted">Weighted: {{ number_format($weightedProfileCompletion, 1) }}%</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-file-alt me-2"></i>Required Documents (40%)</h6>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-success"
                                                 style="width: {{ $documentCompletionPercentage }}%">
                                            </div>
                                        </div>
                                        <small>{{ $uploadedDocumentTypes }}/{{ $totalRequiredDocumentTypes }} document types uploaded</small>
                                        <br>
                                        <small class="text-muted">Weighted: {{ number_format($weightedDocumentCompletion, 1) }}%</small>

                                        @if($uploadedDocumentTypes >= $totalRequiredDocumentTypes)
                                            @if($pendingDocs->count() > 0)
                                                <div class="text-warning mt-1">
                                                    <i class="fas fa-clock"></i> Awaiting review ({{ $pendingDocs->count() }} pending)
                                                </div>
                                            @else
                                                <div class="text-success mt-1">
                                                    <i class="fas fa-check-circle"></i> All documents approved!
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-warning mt-1">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $totalRequiredDocumentTypes - $uploadedDocumentTypes }} more required
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($canAccessDashboard && $pendingDocs->count() == 0)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Congratulations!</strong> Your profile is complete and all documents are approved. You now have full access to all system features.
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('customer.customer-dashboard') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                            </a>
                            <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-primary btn-lg ms-2">
                                <i class="fas fa-folder me-2"></i>View Documents
                            </a>
                        </div>
                    @elseif($allDocumentsUploaded && $pendingDocs->count() > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Documents Under Review!</strong> You've uploaded all required documents.
                            Please wait for admin review. This usually takes 1-2 business days.
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('customer.documents.index') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-folder me-2"></i>Check Document Status
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            To access all features, please complete your company profile and upload required documents.
                            <br>
                            <strong>Profile Progress:</strong> {{ $completedProfileFields }}/{{ $totalProfileFields }} fields completed
                            <br>
                            <strong>Document Progress:</strong> {{ $uploadedDocumentTypes }}/{{ $totalRequiredDocumentTypes }} document types uploaded
                            <br>
                            <strong>Overall Completion:</strong> {{ $totalCompletionRounded }}% (70% required for full access)
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('customer.profile.edit') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Complete Your Profile
                            </a>
                        </div>

                        <!-- Show limited access options -->
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Limited Access Available</h6>
                                </div>
                                <div class="card-body">
                                    <p>You can still access these features while completing your profile:</p>
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-info">
                                            <i class="fas fa-folder me-2"></i>My Documents
                                        </a>
                                        <a href="{{ route('customer.profile.show') }}" class="btn btn-outline-info">
                                            <i class="fas fa-id-card me-2"></i>View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Document Upload Status Details -->
                    @if(count($missingDocumentTypes) > 0)
                        <div class="mt-4 text-start">
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Missing Documents ({{ count($missingDocumentTypes) }}):</h6>
                                <ul class="mb-0">
                                    @foreach($missingDocumentTypes as $type)
                                        <li>{{ ucwords(str_replace('_', ' ', $type)) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Missing Profile Fields (Optional) -->
                    @php
                        $missingProfileFields = [];
                        if ($user->companyProfile) {
                            $profile = $user->companyProfile->toArray();
                            foreach ($profileFields as $field) {
                                if (empty($profile[$field])) {
                                    $missingProfileFields[] = $field;
                                }
                            }
                        }
                    @endphp

                    @if(count($missingProfileFields) > 0 && $completedProfileFields < $totalProfileFields)
                        <div class="mt-4 text-start">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-id-card me-2"></i>Incomplete Profile Fields:</h6>
                                <ul class="mb-0">
                                    @foreach($missingProfileFields as $field)
                                        <li>{{ ucwords(str_replace('_', ' ', $field)) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .progress {
        border-radius: 10px;
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .btn-lg {
        padding: 12px 30px;
    }
</style>
@endpush
