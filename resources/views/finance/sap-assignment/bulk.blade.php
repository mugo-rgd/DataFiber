{{-- resources/views/finance/sap-assignment/bulk.blade.php --}}
@extends('layouts.app')

@section('title', 'Bulk SAP Assignment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fas fa-layer-group"></i> Bulk SAP Assignment</h1>
            <p class="text-muted">Assign SAP codes to multiple customers at once</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('finance.sap-assignment.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Single Assignment
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Customers Without SAP Accounts</h5>
        </div>

        <div class="card-body">
            @if($customers->count() > 0)
            <form method="POST" action="{{ route('finance.sap-assignment.bulk-store') }}" id="bulkSapForm">
                @csrf

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Customer Name</th>
                                <th>Company</th>
                                <th>KRA Pin</th>
                                <th>Reg No.</th>
                                <th width="20%">SAP Account</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $index => $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $customer->name }}</strong><br>
                                    <small class="text-muted">{{ $customer->email }}</small>
                                </td>
                                <td>{{ $customer->company_name ?? 'N/A' }}</td>
                                <td>{{ $customer->companyProfile->kra_pin ?? 'N/A' }}</td>
                                <td>{{ $customer->companyProfile->registration_number ?? 'N/A' }}</td>
                                <td>
                                    <input type="hidden" name="sap_accounts[{{ $index }}][user_id]" value="{{ $customer->id }}">
                                    <input type="text"
                                           class="form-control form-control-sm sap-account-input"
                                           name="sap_accounts[{{ $index }}][sap_account]"
                                           pattern="[0-9]{6}"
                                           maxlength="6"
                                           placeholder="000000"
                                           oninput="autoGenerateNext(this, {{ $index }})">
                                    <div class="form-text text-xs">
                                        <button type="button" class="btn btn-xs btn-outline-secondary mt-1"
                                                onclick="suggestSapCode(this)">
                                            <i class="fas fa-magic"></i> Suggest
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Quick Actions</label><br>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="autoGenerateAll()">
                                    <i class="fas fa-bolt"></i> Auto-generate All
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearAll()">
                                    <i class="fas fa-eraser"></i> Clear All
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Assign {{ $customers->count() }} SAP Accounts
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @else
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> All customers already have SAP accounts assigned!
            </div>
            @endif
        </div>
    </div>
</div>

<script>
let lastSapCode = 100000;

function suggestSapCode(button) {
    const input = button.closest('td').querySelector('.sap-account-input');
    lastSapCode++;
    input.value = String(lastSapCode).padStart(6, '0');
}

function autoGenerateAll() {
    const inputs = document.querySelectorAll('.sap-account-input');
    let startCode = lastSapCode + 1;

    inputs.forEach((input, index) => {
        input.value = String(startCode + index).padStart(6, '0');
    });
}

function autoGenerateNext(input, currentIndex) {
    if (input.value.length === 6 && /^\d+$/.test(input.value)) {
        lastSapCode = Math.max(lastSapCode, parseInt(input.value));
    }
}

function clearAll() {
    document.querySelectorAll('.sap-account-input').forEach(input => {
        input.value = '';
    });
}

document.getElementById('bulkSapForm').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('.sap-account-input');
    let isValid = true;
    let filledCount = 0;

    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            filledCount++;
            if (!/^[0-9]{6}$/.test(input.value)) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }
    });

    if (filledCount === 0) {
        e.preventDefault();
        alert('Please enter at least one SAP account number');
        return false;
    }

    if (!isValid) {
        e.preventDefault();
        alert('Please ensure all SAP account numbers are 6 digits');
        return false;
    }
});
</script>

<style>
.sap-account-input {
    font-family: monospace;
    letter-spacing: 1px;
}
</style>
@endsection
