{{-- resources/views/finance/debt/modals/payment-plan.blade.php --}}
<div class="modal fade" id="paymentPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="paymentPlanForm" action="{{ route('finance.debt.create.payment.plan') }}" method="POST">
                @csrf
                <input type="hidden" name="invoice_id" id="plan_invoice_id" value="">
                <input type="hidden" name="customer_id" id="plan_customer_id" value="{{ $customer->id ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt me-2"></i>Create Payment Plan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Invoice Selection -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Select Invoice</label>
                                <select class="form-select" name="invoice_id" id="invoiceSelect" required>
                                    <option value="">Select an invoice</option>
                                    @if(isset($customer) && $overdueInvoices)
                                        @foreach($overdueInvoices as $invoice)
                                        <option value="{{ $invoice->id }}" data-amount="{{ $invoice->total_amount - $invoice->paid_amount }}">
                                            {{ $invoice->billing_number }} - ${{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }} ({{ $invoice->days_overdue }} days overdue)
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Amount Summary -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Total Amount</small>
                                        <h6 id="totalAmountDisplay">$0.00</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Outstanding</small>
                                        <h6 id="outstandingAmount">$0.00</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Balance After Plan</small>
                                        <h6 id="remainingBalance">$0.00</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Configuration -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Down Payment</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                           name="down_payment" id="downPayment" value="0">
                                </div>
                                <div class="form-text">Optional initial payment</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Number of Installments</label>
                                <select class="form-select" name="installment_count" id="installmentCount">
                                    <option value="2">2 installments</option>
                                    <option value="3" selected>3 installments</option>
                                    <option value="4">4 installments</option>
                                    <option value="6">6 installments</option>
                                    <option value="12">12 installments</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Installment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control"
                                           name="installment_amount" id="installmentAmount" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Options -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date"
                                       id="startDate" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Frequency</label>
                                <select class="form-select" name="frequency" id="frequency">
                                    <option value="weekly">Weekly</option>
                                    <option value="biweekly" selected>Bi-weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Installment Preview -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Installment Schedule Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                        <table class="table table-sm" id="installmentPreview">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Due Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="installmentPreviewBody">
                                                <!-- Dynamic content -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Terms & Conditions</label>
                                <textarea class="form-control" name="terms" rows="4" placeholder="Enter payment plan terms...">
1. All installments must be paid on or before the due date.
2. Late payments may incur additional fees.
3. Failure to make two consecutive payments may result in plan termination.
4. The creditor reserves the right to take legal action for non-payment.
                                </textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Internal Notes</label>
                                <textarea class="form-control" name="notes" rows="3"
                                          placeholder="Internal notes about this payment plan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Payment Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate installment amounts
    function calculateInstallments() {
        const invoiceSelect = $('#invoiceSelect');
        const selectedOption = invoiceSelect.find('option:selected');
        const outstandingAmount = parseFloat(selectedOption.data('amount')) || 0;
        const downPayment = parseFloat($('#downPayment').val()) || 0;
        const installmentCount = parseInt($('#installmentCount').val()) || 1;

        const remainingBalance = outstandingAmount - downPayment;
        const installmentAmount = remainingBalance / installmentCount;

        // Update displays
        $('#totalAmountDisplay').text('$' + outstandingAmount.toFixed(2));
        $('#outstandingAmount').text('$' + outstandingAmount.toFixed(2));
        $('#remainingBalance').text('$' + remainingBalance.toFixed(2));
        $('#installmentAmount').val(installmentAmount.toFixed(2));

        // Generate installment preview
        generateInstallmentPreview();
    }

    // Generate installment preview
    function generateInstallmentPreview() {
        const startDate = new Date($('#startDate').val());
        const frequency = $('#frequency').val();
        const installmentCount = parseInt($('#installmentCount').val());
        const installmentAmount = parseFloat($('#installmentAmount').val()) || 0;

        let html = '';
        let currentDate = new Date(startDate);

        for (let i = 1; i <= installmentCount; i++) {
            let dueDate = new Date(currentDate);

            // Calculate next due date based on frequency
            if (frequency === 'weekly') {
                currentDate.setDate(currentDate.getDate() + 7);
            } else if (frequency === 'biweekly') {
                currentDate.setDate(currentDate.getDate() + 14);
            } else if (frequency === 'monthly') {
                currentDate.setMonth(currentDate.getMonth() + 1);
            } else if (frequency === 'quarterly') {
                currentDate.setMonth(currentDate.getMonth() + 3);
            }

            html += `<tr>
                <td>${i}</td>
                <td>${dueDate.toLocaleDateString()}</td>
                <td>$${installmentAmount.toFixed(2)}</td>
                <td><span class="badge bg-secondary">Pending</span></td>
            </tr>`;
        }

        $('#installmentPreviewBody').html(html);
    }

    // Event listeners
    $('#invoiceSelect, #downPayment, #installmentCount, #startDate, #frequency').on('change input', function() {
        calculateInstallments();
    });

    // Form submission
    $('#paymentPlanForm').submit(function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#paymentPlanModal').modal('hide');
                showToast('success', 'Payment plan created successfully!');
                // Reload page or update UI
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Failed to create payment plan');
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Create Payment Plan');
            }
        });
    });

    // Initialize
    calculateInstallments();
});
</script>
@endpush
