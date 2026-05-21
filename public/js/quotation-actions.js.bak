document.addEventListener('DOMContentLoaded', function() {
    console.log('Quotation actions loaded - DOM ready');

    // Debug: Check if modals exist
    console.log('Checking modals:');
    console.log('- Approve modal:', document.getElementById('approveQuotationModal') ? 'Found' : 'Not found');
    console.log('- Reject modal:', document.getElementById('rejectQuotationModal') ? 'Found' : 'Not found');
    console.log('- Send modal:', document.getElementById('sendQuotationModal') ? 'Found' : 'Not found');

    // Initialize everything
    initializeAll();
});

function initializeAll() {
    try {
        // 1. Setup modal event handlers
        setupModalHandlers();

        // 2. Setup character counters
        setupCharacterCounters();

        // 3. Setup button click handlers
        setupButtonHandlers();

        // 4. Setup modal cleanup on hide
        setupModalCleanup();

        // 5. Test if event listeners are working
        testEventListeners();

        console.log('All initialization complete');
    } catch (error) {
        console.error('Initialization error:', error);
    }
}

function setupModalHandlers() {
    console.log('Setting up modal handlers...');

    // Approve modal
    const approveModal = document.getElementById('approveQuotationModal');
    if (approveModal) {
        approveModal.addEventListener('show.bs.modal', function(event) {
            console.log('Approve modal show event triggered');
            const button = event.relatedTarget;
            if (button) {
                const quotationId = button.getAttribute('data-quotation-id');
                console.log('Setting approve quotation ID:', quotationId);

                // Set hidden input
                const hiddenInput = document.getElementById('approveQuotationId');
                if (hiddenInput) {
                    hiddenInput.value = quotationId;
                    console.log('Hidden input value set to:', hiddenInput.value);
                }

                // Reset form fields
                resetApproveForm();
            }
        });
    }

    // Reject modal
    const rejectModal = document.getElementById('rejectQuotationModal');
    if (rejectModal) {
        rejectModal.addEventListener('show.bs.modal', function(event) {
            console.log('Reject modal show event triggered');
            const button = event.relatedTarget;
            if (button) {
                const quotationId = button.getAttribute('data-quotation-id');
                console.log('Setting reject quotation ID:', quotationId);

                const hiddenInput = document.getElementById('rejectQuotationId');
                if (hiddenInput) {
                    hiddenInput.value = quotationId;
                }

                resetRejectForm();
            }
        });
    }

    // Send modal
    const sendModal = document.getElementById('sendQuotationModal');
    if (sendModal) {
        sendModal.addEventListener('show.bs.modal', function(event) {
            console.log('Send modal show event triggered');
            const button = event.relatedTarget;
            if (button) {
                const quotationId = button.getAttribute('data-quotation-id');
                console.log('Setting send quotation ID:', quotationId);

                const hiddenInput = document.getElementById('sendQuotationId');
                if (hiddenInput) {
                    hiddenInput.value = quotationId;
                }

                resetSendForm();
            }
        });
    }
}

function setupCharacterCounters() {
    console.log('Setting up character counters...');

    // Approve notes counter
    const approveNotes = document.getElementById('approveNotes');
    if (approveNotes) {
        approveNotes.addEventListener('input', function() {
            const counter = document.getElementById('approveNotesCounter');
            if (counter) {
                counter.textContent = this.value.length;
            }
        });
    }

    // Reject reason counter
    const rejectReason = document.getElementById('rejectReason');
    if (rejectReason) {
        rejectReason.addEventListener('input', function() {
            const counter = document.getElementById('rejectReasonCounter');
            if (counter) {
                counter.textContent = this.value.length;
            }
        });
    }

    // Send notes counter
    const sendNotes = document.getElementById('sendNotes');
    if (sendNotes) {
        sendNotes.addEventListener('input', function() {
            const counter = document.getElementById('sendNotesCounter');
            if (counter) {
                counter.textContent = this.value.length;
            }
        });
    }
}

function setupButtonHandlers() {
    console.log('Setting up button handlers...');

    // Approve button
    const approveBtn = document.getElementById('approveSubmitBtn');
    if (approveBtn) {
        console.log('Approve button found, adding click handler');
        approveBtn.addEventListener('click', function(e) {
            console.log('Approve button clicked');
            e.preventDefault();
            handleApprove();
        });
    } else {
        console.error('Approve button NOT found!');
    }

    // Reject button
    const rejectBtn = document.getElementById('rejectSubmitBtn');
    if (rejectBtn) {
        console.log('Reject button found, adding click handler');
        rejectBtn.addEventListener('click', function(e) {
            console.log('Reject button clicked');
            e.preventDefault();
            handleReject();
        });
    } else {
        console.error('Reject button NOT found!');
    }

    // Send button
    const sendBtn = document.getElementById('sendSubmitBtn');
    if (sendBtn) {
        console.log('Send button found, adding click handler');
        sendBtn.addEventListener('click', function(e) {
            console.log('Send button clicked');
            e.preventDefault();
            handleSend();
        });
    } else {
        console.error('Send button NOT found!');
    }
}

function setupModalCleanup() {
    console.log('Setting up modal cleanup...');

    const modals = [
        {id: 'approveQuotationModal', resetFn: resetApproveForm},
        {id: 'rejectQuotationModal', resetFn: resetRejectForm},
        {id: 'sendQuotationModal', resetFn: resetSendForm}
    ];

    modals.forEach(modal => {
        const modalElement = document.getElementById(modal.id);
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function() {
                console.log(`${modal.id} hidden, resetting form`);
                modal.resetFn();
            });
        }
    });
}

function resetApproveForm() {
    const notesField = document.getElementById('approveNotes');
    const counter = document.getElementById('approveNotesCounter');
    const button = document.getElementById('approveSubmitBtn');

    if (notesField) notesField.value = '';
    if (counter) counter.textContent = '0';
    if (button) {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check me-1"></i>Approve Quotation';
    }
}

function resetRejectForm() {
    const reasonField = document.getElementById('rejectReason');
    const counter = document.getElementById('rejectReasonCounter');
    const button = document.getElementById('rejectSubmitBtn');

    if (reasonField) reasonField.value = '';
    if (counter) counter.textContent = '0';
    if (button) {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-times me-1"></i>Reject Quotation';
    }
}

function resetSendForm() {
    const notesField = document.getElementById('sendNotes');
    const counter = document.getElementById('sendNotesCounter');
    const button = document.getElementById('sendSubmitBtn');

    if (notesField) notesField.value = '';
    if (counter) counter.textContent = '0';
    if (button) {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send Quotation';
    }
}

function testEventListeners() {
    console.log('Testing event listeners...');

    // Test modal trigger buttons
    const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
    console.log(`Found ${modalTriggers.length} modal trigger buttons`);

    modalTriggers.forEach((button, index) => {
        button.addEventListener('click', function() {
            console.log(`Modal trigger ${index + 1} clicked:`);
            console.log('  - Target:', this.getAttribute('data-bs-target'));
            console.log('  - Quotation ID:', this.getAttribute('data-quotation-id'));
        });
    });
}

// Alert system
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.global-quotation-alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} global-quotation-alert alert-dismissible fade show`;
    alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; min-width: 300px;';

    const icon = type === 'success' ? 'check-circle' :
                type === 'warning' ? 'exclamation-triangle' :
                type === 'info' ? 'info-circle' : 'exclamation-circle';

    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${icon} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.body.appendChild(alertDiv);

    // Auto remove
    setTimeout(() => {
        if (alertDiv.parentNode) {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }
    }, 5000);
}

// Handle Approve
async function handleApprove() {
    console.log('=== Starting approve process ===');

    const quotationId = document.getElementById('approveQuotationId').value;
    const notes = document.getElementById('approveNotes').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const button = document.getElementById('approveSubmitBtn');

    console.log('Quotation ID:', quotationId);
    console.log('Notes:', notes);
    console.log('CSRF Token exists:', !!csrfToken);

    if (!quotationId) {
        showAlert('warning', 'No quotation selected. Please try again.');
        return;
    }

    if (!button) {
        showAlert('danger', 'Submit button not found.');
        return;
    }

    // Save original state
    const originalHtml = button.innerHTML;
    const originalDisabled = button.disabled;

    // Set loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Approving...';
    button.disabled = true;

    try {
        console.log('Sending approve request...');

        const response = await fetch(`/admin/quotations/${quotationId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notes: notes })
        });

        console.log('Response status:', response.status);

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            showAlert('success', data.message || 'Quotation approved successfully!');

            // Close modal
            const modalEl = document.getElementById('approveQuotationModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                } else {
                    // Fallback: hide using data attributes
                    const bsModal = new bootstrap.Modal(modalEl);
                    bsModal.hide();
                }
            }

            // Reload page after delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);

        } else {
            showAlert('danger', data.message || 'Failed to approve quotation');
            button.innerHTML = originalHtml;
            button.disabled = originalDisabled;
        }

    } catch (error) {
        console.error('Approve error:', error);
        showAlert('danger', 'Network error. Please check your connection and try again.');
        button.innerHTML = originalHtml;
        button.disabled = originalDisabled;
    }
}

// Handle Reject
async function handleReject() {
    console.log('=== Starting reject process ===');

    const quotationId = document.getElementById('rejectQuotationId').value;
    const reason = document.getElementById('rejectReason').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const button = document.getElementById('rejectSubmitBtn');

    console.log('Quotation ID:', quotationId);
    console.log('Reason:', reason);

    if (!quotationId) {
        showAlert('warning', 'No quotation selected.');
        return;
    }

    if (!reason.trim()) {
        showAlert('warning', 'Please provide a rejection reason.');
        return;
    }

    if (!button) {
        showAlert('danger', 'Submit button not found.');
        return;
    }

    // Save original state
    const originalHtml = button.innerHTML;
    const originalDisabled = button.disabled;

    // Set loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Rejecting...';
    button.disabled = true;

    try {
        console.log('Sending reject request...');

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
            showAlert('success', data.message || 'Quotation rejected successfully!');

            // Close modal
            const modalEl = document.getElementById('rejectQuotationModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            }

            setTimeout(() => {
                window.location.reload();
            }, 1500);

        } else {
            showAlert('danger', data.message || 'Failed to reject quotation');
            button.innerHTML = originalHtml;
            button.disabled = originalDisabled;
        }

    } catch (error) {
        console.error('Reject error:', error);
        showAlert('danger', 'Network error. Please try again.');
        button.innerHTML = originalHtml;
        button.disabled = originalDisabled;
    }
}

// Handle Send
async function handleSend() {
    console.log('=== Starting send process ===');

    const quotationId = document.getElementById('sendQuotationId').value;
    const emailNotes = document.getElementById('sendNotes').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const button = document.getElementById('sendSubmitBtn');

    console.log('Quotation ID:', quotationId);
    console.log('Email notes:', emailNotes);

    if (!quotationId) {
        showAlert('warning', 'No quotation selected.');
        return;
    }

    if (!button) {
        showAlert('danger', 'Submit button not found.');
        return;
    }

    // Save original state
    const originalHtml = button.innerHTML;
    const originalDisabled = button.disabled;

    // Set loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';
    button.disabled = true;

    try {
        console.log('Sending quotation...');

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
            showAlert('success', data.message || 'Quotation sent to customer successfully!');

            // Close modal
            const modalEl = document.getElementById('sendQuotationModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            }

            setTimeout(() => {
                window.location.reload();
            }, 1500);

        } else {
            showAlert('danger', data.message || 'Failed to send quotation');
            button.innerHTML = originalHtml;
            button.disabled = originalDisabled;
        }

    } catch (error) {
        console.error('Send error:', error);
        showAlert('danger', 'Network error. Please try again.');
        button.innerHTML = originalHtml;
        button.disabled = originalDisabled;
    }
}

// Make duplicate function available globally
window.duplicateQuotation = function(quotationId) {
    if (confirm('Are you sure you want to duplicate this quotation?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Show loading
        showAlert('info', 'Duplicating quotation...');

        fetch(`/admin/quotations/${quotationId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Quotation duplicated successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('danger', data.message || 'Failed to duplicate quotation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Network error. Please try again.');
        });
    }
};
