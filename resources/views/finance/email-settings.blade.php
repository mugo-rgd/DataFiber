@extends('layouts.app')

@section('title', 'Email Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>Email Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-paper-plane me-2"></i>Test Email Configuration</h6>
                                    <hr>
                                    <div class="mb-3">
                                        <label class="form-label">Test Email Address</label>
                                        <input type="email" id="testEmail" class="form-control" value="{{ config('mail.from.address') }}">
                                    </div>
                                    <button onclick="sendTestEmail()" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i> Send Test Email
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-bell me-2"></i>Bulk Email Actions</h6>
                                    <hr>
                                    <button onclick="sendOverdueNotices()" class="btn btn-warning w-100 mb-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Send Overdue Notices
                                    </button>
                                    <button onclick="sendDueReminders()" class="btn btn-info w-100">
                                        <i class="fas fa-clock me-1"></i> Send Due Reminders
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-info-circle me-2"></i>Email Configuration Details</h6>
                                    <hr>
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="200">Mail Host:</th>
                                            <td>{{ config('mail.mailers.smtp.host') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mail Port:</th>
                                            <td>{{ config('mail.mailers.smtp.port') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mail Encryption:</th>
                                            <td>{{ config('mail.mailers.smtp.encryption') }}</td>
                                        </tr>
                                        <tr>
                                            <th>From Address:</th>
                                            <td>{{ config('mail.from.address') }}</td>
                                        </tr>
                                        <tr>
                                            <th>From Name:</th>
                                            <td>{{ config('mail.from.name') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendTestEmail() {
    const email = document.getElementById('testEmail').value;
    fetch('/finance/emails/test', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    });
}

function sendOverdueNotices() {
    if (confirm('Send overdue notices to ALL customers with overdue invoices?')) {
        fetch('/finance/emails/overdue-notices', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        });
    }
}

function sendDueReminders() {
    if (confirm('Send due reminders to customers with invoices due in next 3 days?')) {
        fetch('/finance/emails/due-reminders', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        });
    }
}
</script>
@endsection
