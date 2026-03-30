@php
    $surveyors = $surveyors ?? collect();
@endphp
<div class="modal fade" id="reassignSurveyorModal{{ $request->id }}" tabindex="-1" aria-labelledby="reassignSurveyorModalLabel{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reassignSurveyorModalLabel{{ $request->id }}">
                    <i class="fas fa-sync-alt me-2"></i>Reassign Surveyor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.design-requests.assign-surveyor', $request) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Currently assigned to: <strong>{{ $request->surveyor->user->name }}</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Request:</strong> #{{ $request->request_number }}</p>
                            <p><strong>Title:</strong> {{ $request->title }}</p>
                            <p><strong>Customer:</strong> {{ $request->customer->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Survey Status:</strong>
                                <span class="badge bg-{{ $request->survey_status === 'assigned' ? 'primary' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->survey_status)) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="surveyor_id" class="form-label">Select New Surveyor *</label>
                        <select class="form-select @error('surveyor_id') is-invalid @enderror"
                                id="surveyor_id"
                                name="surveyor_id"
                                required>
                            <option value="">Choose a new surveyor...</option>
                            @foreach($surveyors as $surveyor)
                                <option value="{{ $surveyor->id }}"
                                        {{ old('surveyor_id') == $surveyor->id ? 'selected' : '' }}
                                        {{ $request->surveyor_id == $surveyor->id ? 'selected' : '' }}>
                                    {{ $surveyor->user->name }}
                                    ({{ $surveyor->employee_id }})
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
                        <label for="survey_requirements" class="form-label">Updated Survey Requirements</label>
                        <textarea class="form-control @error('survey_requirements') is-invalid @enderror"
                                  id="survey_requirements"
                                  name="survey_requirements"
                                  rows="3"
                                  placeholder="Update survey requirements if needed...">{{ old('survey_requirements', $request->survey_requirements) }}</textarea>
                        @error('survey_requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-sync-alt me-1"></i> Reassign Surveyor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
