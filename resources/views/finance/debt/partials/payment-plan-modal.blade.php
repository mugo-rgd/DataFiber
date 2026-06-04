{{-- resources/views/finance/debt/partials/payment-plan-modal.blade.php --}}
<div class="modal fade" id="paymentPlanModal" tabindex="-1" aria-labelledby="paymentPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                    <h5 class="modal-title fw-bold" id="paymentPlanModalLabel">Create Payment Plan</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="paymentPlanForm">
                @csrf
                <input type="hidden" id="plan_invoice_id" name="invoice_id">

                <div class="modal-body p-4">
                    <!-- Invoice Summary Card -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fas fa-file-invoice me-2 text-primary"></i>Invoice Summary</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Invoice Number</small>
                                    <p class="fw-bold mb-0" id="summaryInvoiceNumber">-</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Customer</small>
                                    <p class="fw-bold mb-0" id="summaryCustomerName">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Total Amount</small>
                                    <p class="fw-bold mb-0" id="summaryTotalAmount">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Outstanding Balance</small>
                                    <p class="fw-bold text-danger mb-0" id="summaryOutstanding">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Due Date</small>
                                    <p class="mb-0" id="summaryDueDate">-</p>
                                </div>
                                <div class="col-md-12">
                                    <small class="text-muted d-block">Overdue Status</small>
                                    <p class="mb-0" id="summaryDaysOverdue">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Plan Configuration -->
                    <h6 class="fw-bold mb-3"><i class="fas fa-sliders-h me-2 text-primary"></i>Payment Plan Configuration</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Down Payment (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light" id="downPaymentCurrency">$</span>
                                <input type="number" step="0.01" class="form-control" id="downPayment" name="down_payment" value="0" min="0">
                            </div>
                            <small class="text-muted">Initial payment to reduce balance</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Number of Installments</label>
                            <select class="form-select" id="installmentCount" name="installment_count">
                                <option value="2">2 installments (2 months)</option>
                                <option value="3">3 installments (3 months)</option>
                                <option value="4">4 installments (4 months)</option>
                                <option value="6">6 installments (6 months)</option>
                                <option value="8">8 installments (8 months)</option>
                                <option value="12">12 installments (12 months)</option>
                                <option value="18">18 installments (18 months)</option>
                                <option value="24">24 installments (24 months)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Frequency</label>
                            <select class="form-select" id="frequency" name="frequency">
                                <option value="weekly">Weekly</option>
                                <option value="biweekly">Bi-weekly (Every 2 weeks)</option>
                                <option value="monthly" selected>Monthly</option>
                                <option value="quarterly">Quarterly</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes (Optional)</label>
                            <textarea class="form-control" id="planNotes" name="notes" rows="2" placeholder="Add any special notes or terms for this payment plan..."></textarea>
                        </div>
                    </div>

                    <!-- Installment Preview -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Installment Schedule Preview</h6>
                            <span class="badge bg-info" id="installmentAmountPreview">-</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="installmentPreviewTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Due Date</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            <i class="fas fa-calculator me-1"></i> Configure plan to see preview
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Row -->
                    <div class="row mt-3 g-3">
                        <div class="col-4 text-center">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted d-block">Down Payment</small>
                                <strong id="downPaymentPreview">$0.00</strong>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted d-block">Remaining Balance</small>
                                <strong id="remainingAmountPreview">$0.00</strong>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted d-block">Installment Amount</small>
                                <strong id="installmentPreviewAmount">$0.00</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4" id="submitPaymentPlanBtn">
                        <i class="fas fa-check me-1"></i> Create Payment Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
