@extends('layouts.app')

@section('title', 'Lease Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-contract text-primary"></i> Lease Details
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('account-manager.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('account-manager.leases.index') }}">Leases</a></li>
                    <li class="breadcrumb-item active">Lease #{{ $lease->id }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Lease Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Lease Information #{{ $lease->id }} - {{ $lease->title }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Lease Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Lease ID:</strong></td>
                                    <td>#{{ $lease->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $lease->status === 'active' ? 'success' : ($lease->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($lease->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Bandwidth:</strong></td>
                                    <td>{{ $lease->bandwidth }} Mbps</td>
                                </tr>
                                 <tr>
                                    <td><strong>Cores:</strong></td>
                                    <td>{{ $lease->cores_required }} Core(s)</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $lease->contract_term_months }} months</td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly Cost:</strong></td>
                                    <td>{{ $lease->currency}} - {{ number_format($lease->monthly_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Value:</strong></td>
                                    <td>{{ $lease->currency}} -  {{ number_format($lease->monthly_cost * $lease->duration_months, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary">Dates</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $lease->start_date ? $lease->start_date->format('M d, Y') : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>End Date:</strong></td>
                                    <td>{{ $lease->end_date ? $lease->end_date->format('M d, Y') : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $lease->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $lease->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($lease->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary">Description</h6>
                            <div class="border rounded p-3 bg-light">
                                {{ $lease->description }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($lease->acceptance_certificate_path)
<!-- Acceptance Certificate Card -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0">
            <i class="fas fa-file-certificate me-2"></i>
            Acceptance Certificate
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Certificate File:</strong></td>
                        <td>
                            <a href="{{ Storage::url($lease->acceptance_certificate_path) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View Certificate
                            </a>
                            <a href="{{ Storage::url($lease->acceptance_certificate_path) }}"
                               download
                               class="btn btn-sm btn-outline-success">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Generated On:</strong></td>
                        <td>{{ $lease->acceptance_certificate_generated_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Generated
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Additional Actions -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.leases.regenerate-certificate', $lease->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm"
                                onclick="return confirm('Regenerate acceptance certificate? This will create a new version.')">
                            <i class="fas fa-sync-alt me-1"></i>Regenerate Certificate
                        </button>
                    </form>

                    <form action="{{ route('admin.leases.delete-certificate', $lease->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete acceptance certificate? This action cannot be undone.')">
                            <i class="fas fa-trash me-1"></i>Delete Certificate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- Generate Certificate Card (when no certificate exists) -->
<div class="card shadow mb-4">
    <div class="card-header bg-secondary text-white py-3">
        <h5 class="mb-0">
            <i class="fas fa-file-certificate me-2"></i>
            Acceptance Certificate
        </h5>
    </div>
    <div class="card-body text-center">
        <p class="text-muted mb-4">No acceptance certificate has been generated for this lease.</p>
        <form action="{{ route('admin.leases.generate-certificate', $lease->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Generate Acceptance Certificate
            </button>
        </form>
    </div>
</div>
@endif

            @if($lease->test_report_path)
<!-- Test Report Information Card -->
<div class="card shadow mb-4">
    <div class="card-header bg-success text-white py-3">
        <h5 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>
            Test Report Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Report Type:</strong></td>
                        <td>{{ ucfirst(str_replace('_', ' ', $lease->test_report_type)) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Test Date:</strong></td>
                        <td>{{ $lease->test_date ? $lease->test_date->format('M d, Y') : 'Not set' }}</td>
                    </tr>
                    <tr>
                        <td><strong>File:</strong></td>
                        <td>
                            <a href="{{ Storage::url($lease->test_report_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download Report
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @if($lease->test_report_description)
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-primary">Description</h6>
                <div class="border rounded p-3 bg-light">
                    {{ $lease->test_report_description }}
                </div>
            </div>
        </div>
        @endif
            </div>
        </div>
        @endif

            <!-- Customer Information Card -->
            @if($lease->customer)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Customer Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $lease->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $lease->customer->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $lease->customer->phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Company:</strong></td>
                                    <td>{{ $lease->customer->company ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $lease->customer->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.leases.edit', $lease->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Lease
                        </a>
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-money-bill me-2"></i>Record Payment
                        </button>
                        {{-- <a href="{{ route('admin.leases.invoice', $lease->id) }}" class="btn btn-primary">
                            <i class="fas fa-file-invoice me-2"></i>Generate Invoice
                        </a> --}}
<form action="{{ route('admin.leases.invoice.generate', $lease->id) }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-primary"
            onclick="return confirm('Are you sure you want to generate an invoice for this lease?')">
        <i class="fas fa-file-invoice me-2"></i>Generate Invoice
    </button>
</form>


                      @if($lease->acceptance_certificate_path)
    <a href="{{ Storage::url($lease->acceptance_certificate_path) }}"
       target="_blank"
       class="btn btn-success">
        <i class="fas fa-file-certificate me-2"></i>View Acceptance Certificate
    </a>
@else
    <form action="{{ route('admin.leases.generate-certificate', $lease->id) }}" method="POST" class="d-grid">
        @csrf
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-file-certificate me-2"></i>Generate Acceptance Certificate
        </button>
    </form>
@endif
                                          <!-- Upload Test Report Button -->
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadTestReportModal">
                            <i class="fas fa-upload me-2"></i>Upload Test Report
                        </button>

                        @if($lease->status === 'active')
                        <form action="{{ route('admin.leases.terminate', $lease->id) }}" method="POST" class="d-grid">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to terminate this lease?')">
                                <i class="fas fa-times me-2"></i>Terminate Lease
                            </button>
                        </form>
                        @elseif($lease->status === 'pending')
                        <form action="{{ route('admin.leases.activate', $lease->id) }}" method="POST" class="d-grid">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to activate this lease?')">
                                <i class="fas fa-check me-2"></i>Activate Lease
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment History Card -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Recent Payments
                    </h5>
                </div>
                <div class="card-body">
                    @if($lease->payments && $lease->payments->count() > 0)
                        @foreach($lease->payments->take(5) as $payment)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $lease->currency}} -  {{ number_format($payment->amount, 2) }}</strong>
                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                            <small class="text-muted">
                                {{ $payment->payment_date->format('M d, Y') }}
                            </small>
                        </div>
                        @endforeach
                        @if($lease->payments->count() > 5)
                        <a href="{{ route('admin.payments.index', ['lease_id' => $lease->id]) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                            View All Payments
                        </a>
                        @endif
                    @else
                        <p class="text-muted text-center">No payments recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.payments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lease_id" value="{{ $lease->id }}">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount ({{ $lease->currency}})</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="reference" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference" name="reference">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Test Report Modal -->
<div class="modal fade" id="uploadTestReportModal" tabindex="-1" aria-labelledby="uploadTestReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadTestReportModalLabel">Upload Test Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.leases.upload-test-report', $lease->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="test_report" class="form-label">Select Test Report File</label>
                        <input type="file" class="form-control" id="test_report" name="test_report" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        <div class="form-text">
                            Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG. Maximum file size: 10MB
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-control" id="report_type" name="report_type" required>
                            <option value="">Select Report Type</option>
                            <option value="speed_test">Speed Test</option>
                            <option value="latency_test">Latency Test</option>
                            <option value="quality_test">Quality Test</option>
                            <option value="installation_test">Installation Test</option>
                            <option value="maintenance_test">Maintenance Test</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="test_date" class="form-label">Test Date</label>
                        <input type="date" class="form-control" id="test_date" name="test_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Add any additional notes about the test report..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Upload Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateForm = document.getElementById('generateCertificateForm');
    const generateBtn = document.getElementById('generateCertificateBtn');
    const messageDiv = document.getElementById('generateCertificateMessage');

    if (generateForm) {
        generateForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const originalText = generateBtn.innerHTML;
            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
            generateBtn.disabled = true;
            messageDiv.innerHTML = '';

            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    // Reload page after 2 seconds to show the new certificate
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else if (data.error) {
                    messageDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = `<div class="alert alert-danger">An error occurred while generating the certificate.</div>`;
            })
            .finally(() => {
                // Reset button
                generateBtn.innerHTML = originalText;
                generateBtn.disabled = false;
            });
        });
    }
});
</script>
@endsection
