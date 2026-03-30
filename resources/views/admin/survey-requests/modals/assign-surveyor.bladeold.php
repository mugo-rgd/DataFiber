@php
    $surveyors = $surveyors ?? collect();
@endphp

{{-- assign-surveyor.blade.php --}}
<div class="modal fade" id="assignSurveyorModal{{ $designRequest->id }}" tabindex="-1" aria-labelledby="assignSurveyorModalLabel{{ $designRequest->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignSurveyorModalLabel{{ $designRequest->id }}">
                    <i class="fas fa-user-plus me-2"></i>Assign Surveyor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.design-requests.assign-surveyor', $designRequest->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Surveyors found: {{ $surveyors->count() }}
                        @if($surveyors->count() > 0)
                            | IDs: {{ $surveyors->pluck('id')->join(', ') }}
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Request:</strong> #{{ $designRequest->request_number }}</p>
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

                    <div class="mb-3 mt-3">
                        <label for="surveyor_id" class="form-label">Select Surveyor *</label>
                        <select class="form-select @error('surveyor_id') is-invalid @enderror"
                                id="surveyor_id"
                                name="surveyor_id"
                                required>
                            <option value="">Choose a surveyor...</option>
                            @foreach($surveyors as $surveyor)
                                <option value="{{ $surveyor->user->id }}"
                                    {{ old('surveyor_id') == $surveyor->user->id ? 'selected' : '' }}>
                                    {{ $surveyor->user->name }} ({{ $surveyor->user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('surveyor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" {{ $surveyors->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-user-plus me-1"></i> Assign Surveyor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

