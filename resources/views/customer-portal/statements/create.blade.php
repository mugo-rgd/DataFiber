{{-- resources/views/customer-portal/statements/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Generate Custom Statement
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.statements.generate') }}" method="POST" id="statementForm">
                        @csrf

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Select a date range to generate a custom statement. You can preview it before downloading.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date', date('Y-m-01')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date"
                                       name="end_date"
                                       value="{{ old('end_date', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeDetails" checked>
                                    <label class="form-check-label" for="includeDetails">
                                        Include transaction details
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('customer.customer-dashboard') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="previewBtn">
                                <i class="fas fa-eye me-1"></i>Preview Statement
                            </button>
                            <button type="button" class="btn btn-success" id="downloadBtn">
                                <i class="fas fa-download me-1"></i>Download PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Section -->
            <div id="previewSection" class="mt-4" style="display: none;">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Statement Preview</h5>
                    </div>
                    <div class="card-body" id="previewContent">
                        <!-- Preview will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('statementForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const previewBtn = document.getElementById('previewBtn');
    const originalText = previewBtn.innerHTML;

    previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
    previewBtn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('previewSection').style.display = 'block';
        document.getElementById('previewContent').innerHTML = html;

        // Scroll to preview
        document.getElementById('previewSection').scrollIntoView({ behavior: 'smooth' });
    })
    .catch(error => {
        alert('Error generating preview: ' + error.message);
    })
    .finally(() => {
        previewBtn.innerHTML = originalText;
        previewBtn.disabled = false;
    });
});

document.getElementById('downloadBtn').addEventListener('click', function() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    // Create a form and submit it to download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("customer.statements.download") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const start = document.createElement('input');
    start.type = 'hidden';
    start.name = 'start_date';
    start.value = startDate;
    form.appendChild(start);

    const end = document.createElement('input');
    end.type = 'hidden';
    end.name = 'end_date';
    end.value = endDate;
    form.appendChild(end);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
});

// Validate dates
document.getElementById('start_date').addEventListener('change', function() {
    const endDate = document.getElementById('end_date');
    if (this.value > endDate.value) {
        endDate.value = this.value;
    }
    endDate.min = this.value;
});

document.getElementById('end_date').addEventListener('change', function() {
    const startDate = document.getElementById('start_date');
    if (this.value < startDate.value) {
        startDate.value = this.value;
    }
});
</script>
@endpush
