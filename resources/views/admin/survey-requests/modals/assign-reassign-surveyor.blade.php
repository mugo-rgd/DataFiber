<div class="modal fade" id="surveyorModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.design-requests.assign-surveyor', $request->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            {{ $request->surveyor_id ? 'Reassign Surveyor' : 'Assign Surveyor' }}
            – Request #{{ $request->request_number }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Select Surveyor</label>
            <select name="surveyor_id" class="form-select @error('surveyor_id') is-invalid @enderror" required>
              <option value="">-- Choose --</option>
              @foreach($surveyors as $surveyor)
                <option value="{{ $surveyor->id }}"
                  {{ $request->surveyor_id == $surveyor->id ? 'selected' : '' }}>
                  {{ $surveyor->name }} ({{ $surveyor->email }})
                </option>
              @endforeach
            </select>
            @error('surveyor_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Survey Requirements</label>
            <textarea name="survey_requirements"
                      class="form-control @error('survey_requirements') is-invalid @enderror"
                      rows="3"
                      required>{{ old('survey_requirements', $request->survey_requirements) }}</textarea>
            @error('survey_requirements')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Scheduled At</label>
              <input type="datetime-local"
                     name="survey_scheduled_at"
                     class="form-control @error('survey_scheduled_at') is-invalid @enderror"
                     value="{{ old('survey_scheduled_at', optional($request->survey_scheduled_at)->format('Y-m-d\TH:i')) }}"
                     required>
              @error('survey_scheduled_at')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Estimated Hours</label>
              <input type="number"
                     name="survey_estimated_hours"
                     class="form-control @error('survey_estimated_hours') is-invalid @enderror"
                     step="0.5" min="0.5"
                     value="{{ old('survey_estimated_hours', $request->survey_estimated_hours ?? 2) }}"
                     required>
              @error('survey_estimated_hours')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-user-check me-1"></i>
            {{ $request->surveyor_id ? 'Reassign' : 'Assign' }} Surveyor
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
