{{-- resources/views/finance/debt/modals/reminder.blade.php --}}
<div class="modal fade" id="sendReminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="reminderForm" action="{{ route('finance.debt.send.reminders') }}" method="POST">
                @csrf
                <input type="hidden" name="invoice_ids" id="invoice_ids" value="">
                <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer->id ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope me-2"></i>Send Payment Reminder
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Recipient Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Recipient Name</label>
                                <input type="text" class="form-control" id="recipient_name"
                                       value="{{ $customer->name ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Recipient Email</label>
                                <input type="email" class="form-control" id="recipient_email"
                                       value="{{ $customer->email ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Reminder Type -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Reminder Type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="reminder_type" id="email_reminder" value="email" checked>
                                <label class="btn btn-outline-primary" for="email_reminder">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>

                                <input type="radio" class="btn-check" name="reminder_type" id="sms_reminder" value="sms">
                                <label class="btn btn-outline-success" for="sms_reminder">
                                    <i class="fas fa-sms me-2"></i>SMS
                                </label>

                                <input type="radio" class="btn-check" name="reminder_type" id="call_reminder" value="call">
                                <label class="btn btn-outline-info" for="call_reminder">
                                    <i class="fas fa-phone me-2"></i>Phone Call
                                </label>

                                <input type="radio" class="btn-check" name="reminder_type" id="letter_reminder" value="letter">
                                <label class="btn btn-outline-warning" for="letter_reminder">
                                    <i class="fas fa-file-alt me-2"></i>Letter
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Template Selection -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Template</label>
                                <select class="form-select" name="template_id" id="templateSelect">
                                    <option value="">Custom Message</option>
                                    <option value="1">Friendly Reminder (1-7 days overdue)</option>
                                    <option value="2">Standard Reminder (8-30 days overdue)</option>
                                    <option value="3">Urgent Reminder (31-60 days overdue)</option>
                                    <option value="4">Final Notice (61+ days overdue)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" name="subject"
                                       id="reminderSubject" placeholder="Payment Reminder - Invoice #INV-20240115">
                            </div>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" name="message" id="reminderMessage"
                                          rows="8" placeholder="Dear [Customer Name], ..."></textarea>
                            </div>
                            <div class="form-text">
                                Available variables:
                                <span class="badge bg-secondary">[Customer Name]</span>
                                <span class="badge bg-secondary">[Invoice Number]</span>
                                <span class="badge bg-secondary">[Amount Due]</span>
                                <span class="badge bg-secondary">[Due Date]</span>
                                <span class="badge bg-secondary">[Days Overdue]</span>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Options -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Send Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="send_option"
                                           id="send_now" value="now" checked>
                                    <label class="form-check-label" for="send_now">
                                        Send immediately
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="send_option"
                                           id="schedule_later" value="schedule">
                                    <label class="form-check-label" for="schedule_later">
                                        Schedule for later
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Date (hidden by default) -->
                    <div class="row mb-3" id="scheduleDateContainer" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Schedule Date</label>
                                <input type="date" class="form-control" name="schedule_date"
                                       min="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Schedule Time</label>
                                <input type="time" class="form-control" name="schedule_time"
                                       value="09:00">
                            </div>
                        </div>
                    </div>

                    <!-- Follow-up Action -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Follow-up Action</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="set_followup"
                                           id="set_followup" value="1">
                                    <label class="form-check-label" for="set_followup">
                                        Set follow-up date if no response
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Follow-up Date (hidden by default) -->
                    <div class="row mb-3" id="followupDateContainer" style="display: none;">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Follow-up Date</label>
                                <input type="date" class="form-control" name="followup_date"
                                       min="{{ now()->addDays(1)->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Reminder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide schedule date
    $('input[name="send_option"]').change(function() {
        if ($(this).val() === 'schedule') {
            $('#scheduleDateContainer').show();
        } else {
            $('#scheduleDateContainer').hide();
        }
    });

    // Show/hide followup date
    $('#set_followup').change(function() {
        if ($(this).is(':checked')) {
            $('#followupDateContainer').show();
        } else {
            $('#followupDateContainer').hide();
        }
    });

    // Template selection
    $('#templateSelect').change(function() {
        const templates = {
            '1': {
                subject: 'Friendly Reminder - Outstanding Payment',
                message: `Dear [Customer Name],

This is a friendly reminder that your invoice #[Invoice Number] for [Amount Due] was due on [Due Date].

If you've already made this payment, please disregard this message. If not, we kindly ask that you process the payment at your earliest convenience.

Thank you for your prompt attention to this matter.

Best regards,
Accounts Receivable Team`
            },
            '2': {
                subject: 'Payment Reminder - Invoice #[Invoice Number]',
                message: `Dear [Customer Name],

Our records show that invoice #[Invoice Number] for [Amount Due] was due on [Due Date] and is now [Days Overdue] days overdue.

Please arrange payment immediately to avoid any disruption to your service.

You can make payment via:
- Bank Transfer
- Credit Card
- Mobile Money

If you have any questions, please contact us.

Sincerely,
Accounts Receivable Department`
            },
            '3': {
                subject: 'URGENT: Payment Required - Invoice #[Invoice Number]',
                message: `URGENT NOTICE

Dear [Customer Name],

This is our third reminder regarding invoice #[Invoice Number] for [Amount Due], which was due on [Due Date] and is now [Days Overdue] days overdue.

Your account is now considered seriously delinquent. Immediate payment is required to avoid:
- Service suspension
- Late fees
- Collection proceedings

Please contact us immediately to discuss payment options.

Regards,
Collections Department`
            },
            '4': {
                subject: 'FINAL NOTICE: Immediate Payment Required - Invoice #[Invoice Number]',
                message: `FINAL NOTICE

Dear [Customer Name],

This is our FINAL NOTICE regarding invoice #[Invoice Number] for [Amount Due]. This invoice is [Days Overdue] days overdue.

FAILURE TO PAY WITHIN 7 DAYS will result in:
- Immediate service disconnection
- Account referral to collections agency
- Legal proceedings

This is your last opportunity to settle this debt before further action is taken.

Contact us immediately to avoid these consequences.

Sincerely,
Legal & Collections Department`
            }
        };

        if ($(this).val() && templates[$(this).val()]) {
            $('#reminderSubject').val(templates[$(this).val()].subject);
            $('#reminderMessage').val(templates[$(this).val()].message);
        }
    });

    // Form submission
    $('#reminderForm').submit(function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#sendReminderModal').modal('hide');
                showToast('success', 'Reminder sent successfully!');
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Send Reminder');
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Failed to send reminder');
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Send Reminder');
            }
        });
    });
});

function showToast(type, message) {
    // Toast implementation
    const toast = `<div class="toast align-items-center text-white bg-${type} border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>`;

    $('.toast-container').append(toast);
    $('.toast:last').toast('show');
}
</script>
@endpush
