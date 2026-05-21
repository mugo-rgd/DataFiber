<div class="modal fade" id="rejectQuotationModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Reject Quotation
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="rejectQuotationId">

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Please provide a reason for rejecting this quotation.
                </div>

                <div class="mb-3">
                    <label for="rejectReason" class="form-label">
                        <i class="fas fa-comment me-1"></i>
                        Rejection Reason <span class="text-danger">*</span>
                    </label>

                    <textarea class="form-control"
                              id="rejectReason"
                              rows="4"
                              placeholder="Explain why this quotation is being rejected..."
                              required
                              maxlength="500"></textarea>

                    <div class="form-text text-end">
                        <span id="rejectReasonCounter">0</span>/500 characters
                    </div>
                </div>

                <div class="alert alert-danger mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    This action cannot be undone.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>

                <button type="button"
                        class="btn btn-danger"
                        id="rejectSubmitBtn"
                        onclick="quotationReject(this)">
                    <i class="fas fa-times me-1"></i>Reject Quotation
                </button>
            </div>

        </div>
    </div>
</div>
