@extends('layouts.app')

@section('title', 'Assign Surveyor - Design Request #' . $designRequest->request_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-user-plus text-primary"></i> Assign Surveyor
            </h1>
            <p class="text-muted">Assign a surveyor to design request #{{ $designRequest->request_number }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Design Request Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Request Number:</strong> #{{ $designRequest->request_number }}</p>
                            <p><strong>Title:</strong> {{ $designRequest->title }}</p>
                            <p><strong>Customer:</strong> {{ $designRequest->customer->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong>
                                <span class="badge bg-{{ $designRequest->status === 'pending' ? 'warning' : 'primary' }}">
                                    {{ ucfirst($designRequest->status) }}
                                </span>
                            </p>
                            <p><strong>Survey Status:</strong>
                                <span class="badge bg-{{ $designRequest->survey_status === 'not_required' ? 'secondary' : 'info' }}">
                                    {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Assign Surveyor</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account-manager.design-requests.assign-surveyor', $designRequest->request_number) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="surveyor_id" class="form-label">Select Surveyor *</label>
                            <select name="surveyor_id" id="surveyor_id"
                                    class="form-control @error('surveyor_id') is-invalid @enderror" required>
                                <option value="">Choose a surveyor...</option>
                                @foreach($surveyors as $surveyor)
                                    <option value="{{ $surveyor->id }}"
                                            {{ old('surveyor_id') == $surveyor->id ? 'selected' : '' }}>
                                        {{ $surveyor->name }}
                                        @if($surveyor->employee_id)
                                            ({{ $surveyor->employee_id }})
                                        @endif
                                        @if($surveyor->specialization)
                                            - {{ $surveyor->specialization }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('surveyor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="survey_requirements" class="form-label">Survey Requirements</label>
                            <textarea class="form-control @error('survey_requirements') is-invalid @enderror"
                                      id="survey_requirements"
                                      name="survey_requirements"
                                      rows="4"
                                      placeholder="Enter specific survey requirements...">{{ old('survey_requirements', $designRequest->survey_requirements) }}</textarea>
                            @error('survey_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="survey_estimated_hours" class="form-label">Estimated Hours</label>
                                    <input type="number"
                                           class="form-control @error('survey_estimated_hours') is-invalid @enderror"
                                           id="survey_estimated_hours"
                                           name="survey_estimated_hours"
                                           step="0.5"
                                           min="0.5"
                                           value="{{ old('survey_estimated_hours', $designRequest->survey_estimated_hours ?? 2) }}"
                                           placeholder="Estimated hours for survey">
                                    @error('survey_estimated_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="survey_scheduled_at" class="form-label">Scheduled Date & Time</label>
                                    <input type="datetime-local"
                                           class="form-control @error('survey_scheduled_at') is-invalid @enderror"
                                           id="survey_scheduled_at"
                                           name="survey_scheduled_at"
                                           value="{{ old('survey_scheduled_at', $designRequest->survey_scheduled_at ? $designRequest->survey_scheduled_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('survey_scheduled_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.design-requests.show', $designRequest->request_number) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Request
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-1"></i> Assign Surveyor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
