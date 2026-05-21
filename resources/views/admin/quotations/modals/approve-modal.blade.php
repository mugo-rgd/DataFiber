<div class="modal fade" id="approveQuotationModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-kp-green text-white">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Final Admin Approval
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="approveQuotationId">

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This should only be done after the customer has accepted the quotation.
                </div>

                <div class="mb-3">
                    <label for="approveNotes" class="form-label">
                        <i class="fas fa-sticky-note me-1"></i>Approval Notes Optional
                    </label>

                    <textarea class="form-control"
                              id="approveNotes"
                              rows="3"
                              placeholder="Add approval notes..."
                              maxlength="500"></textarea>

                    <div class="form-text text-end">
                        <span id="approveNotesCounter">0</span>/500 characters
                    </div>
                </div>

                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    The quotation status will change to Admin Approved.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>

                <button type="button"
                        class="btn btn-kp-success"
                        id="approveSubmitBtn"
                        onclick="quotationApprove(this)">
                    <i class="fas fa-check me-1"></i>Approve Quotation
                </button>
            </div>

        </div>
    </div>
</div>
