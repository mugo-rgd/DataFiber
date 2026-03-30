{{-- resources/views/admin/survey-requests/modals/assign-surveyor.blade.php --}}
<div class="modal fade" id="assignSurveyorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Assign Surveyor4
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- ✅ FIX: Remove the empty action and add ID for JavaScript to update --}}
            <form method="POST" id="assignSurveyorForm">
                @csrf
                <div class="modal-body">

                    <!-- Request Information -->
                    <div class="alert alert-info">
                        <div id="requestInfo">
                            <strong>Select a design request to assign a surveyor</strong>
                        </div>
                    </div>

                    <!-- Surveyor Selection -->
                    <div class="mb-4">
                        <label for="surveyor_id" class="form-label fw-bold">Select Surveyor *</label>
                        <select class="form-select" id="surveyor_id" name="surveyor_id" required
                                onchange="updateSurveyorDetails(this.value)">
                            <option value="">-- Choose a Surveyor --</option>
                            @foreach($availableSurveyors as $surveyor)
                                @php
                                    $workload = $surveyor->active_assignments_count;
                                    $workloadPercent = min(100, ($workload / 3) * 100); // 3 max assignments
                                @endphp
                                <option value="{{ $surveyor->id }}"
                                        data-workload="{{ $workloadPercent }}"
                                        data-assignments="{{ $workload }}"
                                        data-specialization="{{ $surveyor->specialization }}"
                                        data-email="{{ $surveyor->user->email }}"
                                        data-phone="{{ $surveyor->user->phone ?? 'N/A' }}"
                                        data-equipment="{{ $surveyor->survey_equipment ?? 'Standard' }}">
                                    {{ $surveyor->user->name }}
                                    ({{ $surveyor->employee_id }})
                                    - {{ $workload }} active assignment(s)
                                    @if($surveyor->specialization)
                                        - {{ $surveyor->specialization }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <!-- Surveyor Details Display -->
                        <div id="surveyorDetails" class="mt-2 p-3 border rounded bg-light" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Workload:</strong> <span id="detailWorkload" class="badge bg-info"></span></small><br>
                                    <small><strong>Active Assignments:</strong> <span id="detailAssignments"></span></small><br>
                                    <small><strong>Specialization:</strong> <span id="detailSpecialization"></span></small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Email:</strong> <span id="detailEmail"></span></small><br>
                                    <small><strong>Equipment:</strong> <span id="detailEquipment"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Survey Requirements -->
                    <div class="mb-3">
                        <label for="survey_requirements" class="form-label fw-bold">Survey Requirements *</label>
                        <textarea class="form-control" id="survey_requirements" name="survey_requirements"
                                  rows="5" placeholder="Describe exactly what needs to be surveyed..." required></textarea>
                        <div class="form-text">
                            Be specific about measurements, obstacles to identify, infrastructure to document, etc.
                        </div>
                    </div>

                    <!-- Scheduling -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="survey_scheduled_at" class="form-label fw-bold">Scheduled Date & Time *</label>
                            <input type="datetime-local" class="form-control" id="survey_scheduled_at"
                                   name="survey_scheduled_at" required
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   value="{{ now()->addDays(1)->format('Y-m-d\T09:00') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="survey_estimated_hours" class="form-label fw-bold">Estimated Hours *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="survey_estimated_hours"
                                       name="survey_estimated_hours" step="0.5" min="1" max="24"
                                       value="4" required>
                                <span class="input-group-text">hours</span>
                            </div>
                            <div class="form-text">Based on route complexity and distance</div>
                        </div>
                    </div>

                    <!-- Priority & Deadline -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="priority" class="form-label fw-bold">Survey Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="normal">Normal</option>
                                <option value="high">High Priority</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="deadline" class="form-label fw-bold">Completion Deadline</label>
                            <input type="date" class="form-control" id="deadline" name="deadline"
                                   min="{{ now()->format('Y-m-d') }}"
                                   value="{{ now()->addDays(3)->format('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label fw-bold">Admin Notes (Internal)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes"
                                  rows="2" placeholder="Any special instructions or internal notes for the surveyor..."></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  {{-- To this link --}}
<a href="{{ route('admin.design-requests.assign-surveyor-form', $request) }}"
   class="btn btn-info btn-sm">
    <i class="fas fa-map-marked-alt me-1"></i>Assign Surveyor5
</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateSurveyorDetails(surveyorId) {
    const detailsDiv = document.getElementById('surveyorDetails');
    const selectedOption = document.querySelector(`#surveyor_id option[value="${surveyorId}"]`);

    if (surveyorId && selectedOption) {
        detailsDiv.style.display = 'block';
        document.getElementById('detailWorkload').textContent = selectedOption.getAttribute('data-workload') + '%';
        document.getElementById('detailAssignments').textContent = selectedOption.getAttribute('data-assignments');
        document.getElementById('detailSpecialization').textContent = selectedOption.getAttribute('data-specialization') || 'General';
        document.getElementById('detailEmail').textContent = selectedOption.getAttribute('data-email');
        document.getElementById('detailEquipment').textContent = selectedOption.getAttribute('data-equipment');
    } else {
        detailsDiv.style.display = 'none';
    }
}

// ✅ FIX: Add this JavaScript to set the form action when modal opens
document.addEventListener('DOMContentLoaded', function() {
    const assignModal = document.getElementById('assignSurveyorModal');

    if (assignModal) {
        assignModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const requestId = button.getAttribute('data-request-id');
            const requestNumber = button.getAttribute('data-request-number');
            const requestTitle = button.getAttribute('data-request-title');
            const customerName = button.getAttribute('data-customer-name');
            const technicalReq = button.getAttribute('data-technical-requirements');

            // Update modal title
            const modalTitle = assignModal.querySelector('.modal-title');
            modalTitle.textContent = `Assign Surveyor - ${requestNumber}`;

            // ✅ CRITICAL FIX: Set the correct form action for POST request
            const form = document.getElementById('assignSurveyorForm');
            form.action = `/admin/design-requests/${requestId}/assign-surveyor`;

            // Update request info display
            const requestInfo = document.getElementById('requestInfo');
            if (requestInfo) {
                requestInfo.innerHTML = `
                    <strong>Request:</strong> ${requestNumber}<br>
                    <strong>Title:</strong> ${requestTitle}<br>
                    <strong>Customer:</strong> ${customerName}
                `;
            }

            // Pre-fill requirements with technical requirements
            const requirementsField = document.getElementById('survey_requirements');
            if (requirementsField && technicalReq) {
                requirementsField.value = `Survey requirements for: ${requestTitle}\n\nCustomer: ${customerName}\n\nTechnical Requirements:\n${technicalReq}\n\nPlease survey the route and identify:\n- Existing infrastructure\n- Obstacles and challenges\n- Recommended path\n- Distance measurements\n- GPS coordinates`;
            }

            // Reset surveyor details display
            document.getElementById('surveyorDetails').style.display = 'none';
            document.getElementById('surveyor_id').selectedIndex = 0;
        });
    }
});
</script>
