<div class="modal fade" id="sendQuotationModal" tabindex="-1" aria-labelledby="sendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="sendModalLabel">
                    <i class="fas fa-paper-plane me-2"></i>Send Quotation to Customer
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="sendQuotationId">

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This will send the quotation to the customer for review and acceptance.
                </div>

                <div class="mb-3">
                    <label for="sendNotes" class="form-label">
                        <i class="fas fa-envelope me-1"></i>Email Message Optional
                    </label>

                    <textarea class="form-control"
                              id="sendNotes"
                              rows="4"
                              placeholder="Add a message for the customer..."
                              maxlength="500"></textarea>

                    <div class="form-text text-end">
                        <span id="sendNotesCounter">0</span>/500 characters
                    </div>
                </div>

                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Once sent, the quotation status will change to Sent.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>

                <button type="button"
                        class="btn btn-info text-white"
                        id="sendSubmitBtn"
                        onclick="quotationSend(this)">
                    <i class="fas fa-paper-plane me-1"></i>Send Quotation
                </button>
            </div>

        </div>
    </div>
</div>
