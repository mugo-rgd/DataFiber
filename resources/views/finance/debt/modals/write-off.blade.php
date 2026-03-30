{{-- resources/views/finance/debt/modals/write-off.blade.php --}}
<div class="modal fade" id="writeOffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="writeOffForm" action="{{ route('finance.debt.write.off') }}" method="POST">
                @csrf
                <input type="hidden" name="invoice_id" id="writeoff_invoice_id" value="">
                <input type="hidden" name="customer_id" id="writeoff_customer_id" value="{{ $customer->id ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-contract me-2"></i>Write Off Debt
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Warning Alert -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Writing off debt will permanently mark this invoice as uncollectible.
                        This action should only be taken after all collection efforts have been exhausted.
                    </div>

                    <!-- Invoice Selection -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Select Invoice to Write Off</label>
                                <select class="form-select" name="invoice_id" id="writeoffInvoiceSelect" required>
                                    <option value="">Select an invoice</option>
                                    @if(isset($customer) && $overdueInvoices)
                                        @foreach($overdueInvoices as $invoice)
                                        <option value="{{ $invoice->id }}" data-outstanding="{{ $invoice->total_amount - $invoice->paid_amount }}">
                                            {{ $invoice->billing_number }} - ${{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }} ({{ $invoice->days_overdue }} days overdue)
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Amount Details -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Outstanding Amount</small>
                                        <h5 id="writeoffAmount">$0.00</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Write-off Impact</small>
                                        <h5 id="writeoffImpact">-$0.00</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Write-off Type -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Write-off Type *</label>
                                <select class="form-select" name="write_off_type" id="writeoffType" required>
                                    <option value="">Select a reason</option>
                                    <option value="bad_debt">Bad Debt - Uncollectible</option>
                                    <option value="customer_bankruptcy">Customer Bankruptcy</option>
                                    <option value="customer_dispute">Customer Dispute</option>
                                    <option value="service_issue">Service/Product Issue</option>
                                    <option value="administrative_error">Administrative Error</option>
                                    <option value="customer_deceased">Customer Deceased</option>
                                    <option value="statute_limitations">Statute of Limitations</option>
                                    <option value="courtesy_discount">Courtesy Discount</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Reason Details -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Detailed Reason *</label>
                                <textarea class="form-control" name="reason" rows="4"
                                          placeholder="Provide detailed reason for write-off..." required></textarea>
                                <div class="form-text">
                                    Include: Collection attempts made, customer communication, and justification
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collection Efforts -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Collection Efforts Made</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="efforts[]" value="phone_calls" id="effort1">
                                    <label class="form-check-label" for="effort1">
                                        Multiple phone calls attempted
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="efforts[]" value="emails" id="effort2">
                                    <label class="form-check-label" for="effort2">
                                        Email reminders sent
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="efforts[]" value="letters" id="effort3">
                                    <label class="form-check-label" for="effort3">
                                        Collection letters sent
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="efforts[]" value="payment_plan" id="effort4">
                                    <label class="form-check-label" for="effort4">
                                        Payment plan offered and failed
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="efforts[]" value="legal_action" id="effort5">
                                    <label class="form-check-label" for="effort5">
                                        Legal action considered
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Implications -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Tax Implications</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This write-off may be tax deductible as a bad debt expense.
                                    Consult with your accountant for proper tax treatment.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Approver Name *</label>
                                <input type="text" class="form-control" name="approver_name"
                                       value="{{ auth()->user()->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Approval Date</label>
                                <input type="text" class="form-control"
                                       value="{{ now()->format('M d, Y') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Notes -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Approval Notes</label>
                                <textarea class="form-control" name="approval_notes" rows="3"
                                          placeholder="Additional notes for approval..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="confirmation"
                                       id="writeoffConfirmation" required>
                                <label class="form-check-label" for="writeoffConfirmation">
                                    I confirm that all collection efforts have been exhausted and
                                    this debt is uncollectible. I understand this action cannot be undone.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger" id="writeoffSubmitBtn" disabled>
                        <i class="fas fa-file-contract me-2"></i>Write Off Debt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Update write-off amount when invoice selected
    $('#writeoffInvoiceSelect').change(function() {
        const selectedOption = $(this).find('option:selected');
        const outstandingAmount = parseFloat(selectedOption.data('outstanding')) || 0;

        $('#writeoffAmount').text('$' + outstandingAmount.toFixed(2));
        $('#writeoffImpact').text('-$' + outstandingAmount.toFixed(2));

        // Update hidden field
        $('#writeoff_invoice_id').val($(this).val());
    });

    // Enable/disable submit button based on confirmation
    $('#writeoffConfirmation').change(function() {
        $('#writeoffSubmitBtn').prop('disabled', !$(this).is(':checked'));
    });

    // Form submission
    $('#writeOffForm').submit(function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to write off this debt? This action cannot be undone.')) {
            return;
        }

        const formData = $(this).serialize();
        const submitBtn = $('#writeoffSubmitBtn');

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#writeOffModal').modal('hide');
                showToast('success', 'Debt written off successfully!');
                // Reload page or update UI
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Failed to write off debt');
                submitBtn.prop('disabled', false).html('<i class="fas fa-file-contract me-2"></i>Write Off Debt');
            }
        });
    });

    // Auto-fill reason based on type
    $('#writeoffType').change(function() {
        const type = $(this).val();
        const textarea = $('textarea[name="reason"]');
        const invoiceSelect = $('#writeoffInvoiceSelect');
        const invoiceText = invoiceSelect.find('option:selected').text().split(' - ')[0];

        let defaultReason = '';

        switch(type) {
            case 'bad_debt':
                defaultReason = `Invoice ${invoiceText} is being written off as bad debt after exhaustive collection efforts. The customer has been unresponsive to multiple collection attempts including phone calls, emails, and letters. Based on our assessment, this debt is considered uncollectible.`;
                break;
            case 'customer_bankruptcy':
                defaultReason = `Invoice ${invoiceText} is being written off due to customer bankruptcy proceedings. The customer has filed for bankruptcy protection, making collection impossible.`;
                break;
            case 'customer_dispute':
                defaultReason = `Invoice ${invoiceText} is being written off due to an unresolved customer dispute. Despite efforts to resolve the issue, no satisfactory resolution was reached, and writing off is the most appropriate action.`;
                break;
            case 'service_issue':
                defaultReason = `Invoice ${invoiceText} is being written off due to service/product quality issues. The customer raised legitimate concerns about the service provided, and as a customer service gesture, we are writing off this invoice.`;
                break;
        }

        if (defaultReason && !textarea.val()) {
            textarea.val(defaultReason);
        }
    });
});
</script>
@endpush
