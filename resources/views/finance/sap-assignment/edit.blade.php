{{-- resources/views/finance/sap-assignment/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Assign SAP Account')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fas fa-key"></i> Assign SAP Account</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('finance.sap-assignment.index') }}">SAP Assignment</a></li>
                    <li class="breadcrumb-item active">Assign to {{ $customer->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('finance.sap-assignment.update', $customer->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <h6>Basic Information</h6>
                                    <p><strong>Name:</strong> {{ $customer->name }}</p>
                                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                                    <p><strong>Company:</strong> {{ $customer->company_name ?? 'N/A' }}</p>
                                    <p><strong>Phone:</strong> {{ $customer->companyProfile->phone_number ?? $customer->phone }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <h6>Company Details</h6>
                                    <p><strong>KRA Pin:</strong> {{ $customer->companyProfile->kra_pin ?? 'N/A' }}</p>
                                    <p><strong>Registration:</strong> {{ $customer->companyProfile->registration_number ?? 'N/A' }}</p>
                                    <p><strong>Type:</strong> {{ $customer->companyProfile->company_type ?? 'N/A' }}</p>
                                    <p><strong>Code:</strong> {{ $customer->companyProfile->code ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sap_account" class="form-label">
                                        <strong>SAP Account Number *</strong>
                                        <span class="text-muted">(6-digit number)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control @error('sap_account') is-invalid @enderror"
                                               id="sap_account"
                                               name="sap_account"
                                               value="{{ old('sap_account', $suggestedSap) }}"
                                               pattern="[0-9]{6}"
                                               maxlength="6"
                                               required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateSapCode()">
                                            <i class="fas fa-sync-alt"></i> Generate
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        Suggested: <span id="suggestedSap">{{ $suggestedSap }}</span>
                                        | Last assigned: {{ $lastSap->sap_account ?? 'None' }}
                                    </div>
                                    @error('sap_account')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="assignment_notes" class="form-label">Assignment Notes</label>
                                    <textarea class="form-control"
                                              id="assignment_notes"
                                              name="assignment_notes"
                                              rows="2"
                                              placeholder="Optional notes about this assignment...">{{ old('assignment_notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            This will assign the SAP customer code to:
                            <strong>{{ $customer->name }}</strong> ({{ $customer->email }})
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('finance.sap-assignment.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i> Assign SAP Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <h6>Primary Contact</h6>
                    <p><strong>Name:</strong> {{ $customer->companyProfile->contact_name_1 ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $customer->companyProfile->contact_phone_1 ?? 'N/A' }}</p>

                    @if($customer->companyProfile?->contact_name_2)
                    <hr>
                    <h6>Secondary Contact</h6>
                    <p><strong>Name:</strong> {{ $customer->companyProfile->contact_name_2 }}</p>
                    <p><strong>Phone:</strong> {{ $customer->companyProfile->contact_phone_2 }}</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Address Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Physical Location:</strong> {{ $customer->companyProfile->physical_location ?? 'N/A' }}</p>
                    <p><strong>Road/Street:</strong> {{ $customer->companyProfile->road ?? 'N/A' }}</p>
                    <p><strong>Town/City:</strong> {{ $customer->companyProfile->town ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $customer->companyProfile->address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateSapCode() {
    let current = document.getElementById('sap_account').value;
    if (!current || current === '000000') {
        current = '{{ $suggestedSap }}';
    }

    let next = String(parseInt(current) + 1).padStart(6, '0');
    document.getElementById('sap_account').value = next;
    document.getElementById('suggestedSap').textContent = next;
}
</script>

<style>
.info-box {
    background-color: #f8f9fa;
    border-left: 4px solid #0d6efd;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}
.info-box h6 {
    color: #0d6efd;
    margin-bottom: 10px;
}
</style>
@endsection
