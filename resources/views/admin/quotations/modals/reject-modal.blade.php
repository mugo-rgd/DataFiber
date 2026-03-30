<div class="modal fade" id="rejectQuotationModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Reject Quotation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="rejectQuotationId">

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Are you sure you want to reject this quotation? Please provide a reason below.
                </div>

                <div class="mb-3">
                    <label for="rejectReason" class="form-label">
                        <i class="fas fa-comment me-1"></i>Rejection Reason <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control"
          id="rejectReason"
          rows="4"
          placeholder="Explain why this quotation is being rejected..."
          required
          maxlength="500"
          oninput="document.getElementById('rejectReasonCounter').textContent = this.value.length"></textarea>
                    <div class="form-text text-end">
                        <span id="rejectReasonCounter">0</span>/500 characters
                    </div>
                </div>

                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    This action cannot be undone. The quotation status will change to "Rejected".
                </div>
            </div>

          <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="fas fa-times me-1"></i>Cancel
    </button>
    <button type="button"
            class="btn btn-danger"
            id="rejectSubmitBtn"
            onclick="quotationReject(this); return false;">
        <i class="fas fa-times me-1"></i>Reject Quotation
    </button>
</div>
        </div>
    </div>
</div>
<script>
// quotation-modals-clean.js - SINGLE VERSION, NO DUPLICATES
console.log('Loading clean quotation modals...');

// ====== GLOBAL FUNCTIONS ======
window.quotationApprove = async function(button) {
    console.log('=== APPROVE START ===');

    // Prevent multiple clicks
    if (button.disabled) {
        console.log('Button already disabled, ignoring click');
        return false;
    }

    const quotationId = document.getElementById('approveQuotationId')?.value;
    const notes = document.getElementById('approveNotes')?.value || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    console.log('Data:', { quotationId, notesLength: notes.length, hasCSRF: !!csrfToken });

    if (!quotationId) {
        alert('❌ Error: No quotation selected.');
        return false;
    }

    // Disable button and show loading
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Approving...';

    try {
        const response = await fetch(`/admin/quotations/${quotationId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notes: notes })
        });

        const data = await response.json();
        console.log('Response:', data);

        if (data.success) {
            alert('✅ ' + (data.message || 'Quotation approved successfully!'));

            // Close modal
            const modalEl = document.getElementById('approveQuotationModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }

            // Reload page
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('❌ ' + (data.message || 'Failed to approve quotation'));
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Network error. Please try again.');
        button.disabled = false;
        button.innerHTML = originalText;
    }

    return false;
};

window.quotationReject = async function(button) {
    console.log('=== REJECT START ===');

    if (button.disabled) {
        console.log('Button already disabled, ignoring click');
        return false;
    }

    const quotationId = document.getElementById('rejectQuotationId')?.value;
    const reason = document.getElementById('rejectReason')?.value || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    console.log('Data:', { quotationId, reason });

    if (!quotationId) {
        alert('❌ Error: No quotation selected.');
        return false;
    }

    if (!reason.trim()) {
        alert('⚠️ Please provide a rejection reason.');
        return false;
    }

    // Disable button and show loading
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Rejecting...';

    try {
        const response = await fetch(`/admin/quotations/${quotationId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        });

        const data = await response.json();

        if (data.success) {
            alert('✅ ' + (data.message || 'Quotation rejected successfully!'));

            // Close modal
            const modalEl = document.getElementById('rejectQuotationModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }

            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('❌ ' + (data.message || 'Failed to reject quotation'));
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Network error. Please try again.');
        button.disabled = false;
        button.innerHTML = originalText;
    }

    return false;
};

window.quotationSend = async function(button) {
    console.log('=== SEND START ===');

    if (button.disabled) {
        console.log('Button already disabled, ignoring click');
        return false;
    }

    const quotationId = document.getElementById('sendQuotationId')?.value;
    const emailNotes = document.getElementById('sendNotes')?.value || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    console.log('Data:', { quotationId, emailNotes });

    if (!quotationId) {
        alert('❌ Error: No quotation selected.');
        return false;
    }

    // Disable button and show loading
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';

    try {
        const response = await fetch(`/admin/quotations/${quotationId}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email_notes: emailNotes })
        });

        const data = await response.json();

        if (data.success) {
            alert('✅ ' + (data.message || 'Quotation sent to customer successfully!'));

            // Close modal
            const modalEl = document.getElementById('sendQuotationModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }

            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('❌ ' + (data.message || 'Failed to send quotation'));
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Network error. Please try again.');
        button.disabled = false;
        button.innerHTML = originalText;
    }

    return false;
};

// ====== CHARACTER COUNTERS ======
// Keep your existing inline oninput handlers OR use this:
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up character counters...');

    // Simple character counter setup
    const counters = [
        { textarea: 'approveNotes', counter: 'approveNotesCounter' },
        { textarea: 'rejectReason', counter: 'rejectReasonCounter' },
        { textarea: 'sendNotes', counter: 'sendNotesCounter' }
    ];

    counters.forEach(item => {
        const textarea = document.getElementById(item.textarea);
        const counter = document.getElementById(item.counter);

        if (textarea && counter) {
            // Set initial value
            counter.textContent = textarea.value.length;

            // Add event listener ONLY if not already present
            if (!textarea._hasCounterListener) {
                textarea.addEventListener('input', function() {
                    counter.textContent = this.value.length;
                });
                textarea._hasCounterListener = true;
            }
        }
    });
});

// ====== MODAL DATA TRANSFER ======
// Only set quotation ID when modal opens
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up modal data transfer...');

    // Remove any existing listeners first
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
        btn.replaceWith(btn.cloneNode(true));
    });

    // Add fresh listener
    document.addEventListener('click', function(event) {
        const trigger = event.target.closest('[data-bs-toggle="modal"]');
        if (!trigger) return;

        const quotationId = trigger.getAttribute('data-quotation-id');
        const modalTarget = trigger.getAttribute('data-bs-target');

        console.log('Modal trigger:', { quotationId, modalTarget });

        if (!quotationId || !modalTarget) return;

        if (modalTarget === '#approveQuotationModal') {
            const input = document.getElementById('approveQuotationId');
            if (input) {
                input.value = quotationId;
                console.log('Set approve ID:', quotationId);
            }
        }
        else if (modalTarget === '#rejectQuotationModal') {
            const input = document.getElementById('rejectQuotationId');
            if (input) input.value = quotationId;
        }
        else if (modalTarget === '#sendQuotationModal') {
            const input = document.getElementById('sendQuotationId');
            if (input) input.value = quotationId;
        }
    });
});

// ====== DUPLICATE FUNCTION ======
window.duplicateQuotation = function(quotationId) {
    if (confirm('Are you sure you want to duplicate this quotation?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(`/admin/quotations/${quotationId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Quotation duplicated successfully!');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Network error. Please try again.');
        });
    }
};
</script>
