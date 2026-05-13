@extends('layouts.app')

@section('title', 'Edit NFP Compliance Return')

@section('page-title', 'Edit Numbering Framework Provider (NFP) Compliance Return')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('nfp.index') }}">NFP Returns</a></li>
<li class="breadcrumb-item"><a href="{{ route('nfp.show', $return->id) }}">Return Details</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@push('styles')
<style>
    .form-container { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .section-title { background: #2c3e50; color: white; padding: 8px 12px; margin-top: 20px; margin-bottom: 15px; border-radius: 5px; font-weight: bold; font-size: 14px; }
    .subsection-title { background: #34495e; color: white; padding: 6px 10px; margin-top: 15px; margin-bottom: 10px; border-radius: 3px; font-size: 13px; }
    .sub-subsection-title { font-weight: bold; margin-top: 10px; margin-bottom: 8px; font-size: 12px; color: #2c3e50; }
    .instruction-box { background: #f8f9fa; border-left: 4px solid #dc3545; padding: 12px 15px; margin-bottom: 20px; font-size: 12px; border-radius: 4px; }
    .form-label { font-weight: 500; font-size: 12px; }
    .form-control, .form-select { font-size: 12px; padding: 4px 8px; }
    table { font-size: 11px; }
    table td, table th { padding: 6px; vertical-align: middle; }
    .remove-row { cursor: pointer; }
    .btn-submit-section { margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; }
    .existing-doc { font-size: 11px; }
</style>
@endpush

@section('content')
<div class="form-container">
    <!-- Logo and Header Section -->
    <div class="text-center mb-4 pb-3 border-bottom">
        <div class="mb-2">
            <img src="{{ asset('images/cak.png') }}" alt="CAK Logo" class="img-fluid" style="max-height: 80px;" onerror="this.src='https://via.placeholder.com/80x80?text=CAK'">
        </div>
        <h4 class="text-center mb-1" style="font-size: 18px; font-weight: bold;">NUMBERING FRAMEWORK PROVIDER (NFP)</h4>
        <h5 class="text-center mb-2" style="font-size: 16px;">EDIT COMPLIANCE RETURN</h5>
        <p class="text-muted text-center small mb-0">
            Compliance ID: {{ $return->compliance_id ?? 'Draft' }} |
            Status: <span class="badge bg-warning">{{ ucfirst($return->status) }}</span>
        </p>
    </div>

    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i> You are editing a draft. Make your changes and submit when ready.
    </div>

    <form method="POST" action="{{ route('nfp.update', $return->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @php
            $physical = $return->physical_address ?? [];
            $postal = $return->postal_address ?? [];
            $contacts = $return->contacts ?? [];
            $infrastructure = $return->infrastructure ?? [];
            $primaryNumbers = $return->primary_numbers ?? [];
            $secondaryNumbers = $return->secondary_numbers ?? [];
            $bulkSms = $return->bulk_sms ?? [];
            $broadband = $return->broadband_infrastructure ?? [];
            $staff = $return->staff_data ?? [];
            $documents = $return->documents ?? [];
        @endphp

        <div class="section-title">1. GENERAL INFORMATION</div>
        <div class="subsection-title">1.1 Licence Details</div>
        <div class="row mb-3">
            <div class="col-md-4"><label>Name of Licensee <span class="text-danger">*</span></label><input type="text" name="licensee_name" class="form-control" value="{{ old('licensee_name', $return->licensee_name) }}" required></div>
            <div class="col-md-4"><label>License No.</label><input type="text" name="license_no" class="form-control" value="{{ old('license_no', $return->license_no) }}"></div>
            <div class="col-md-4"><label>Other Licenses</label><input type="text" name="other_licenses" class="form-control" value="{{ old('other_licenses', $return->other_licenses) }}"></div>
        </div>

        <div class="subsection-title">1.2 Period under review</div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Financial Year</label>
                <select name="financial_year" class="form-select" required>
                    <option value="2023/2024" {{ old('financial_year', $return->financial_year) == '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                    <option value="2024/2025" {{ old('financial_year', $return->financial_year) == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                    <option value="2025/2026" {{ old('financial_year', $return->financial_year) == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Quarter</label>
                <select name="quarter" class="form-select" required>
                    <option value="Q1" {{ old('quarter', $return->quarter) == 'Q1' ? 'selected' : '' }}>Q1 (July-Sep)</option>
                    <option value="Q2" {{ old('quarter', $return->quarter) == 'Q2' ? 'selected' : '' }}>Q2 (Oct-Dec)</option>
                    <option value="Q3" {{ old('quarter', $return->quarter) == 'Q3' ? 'selected' : '' }}>Q3 (Jan-Mar)</option>
                    <option value="Q4" {{ old('quarter', $return->quarter) == 'Q4' ? 'selected' : '' }}>Q4 (Apr-Jun)</option>
                </select>
            </div>
        </div>

        <!-- Address Section with existing data -->
        <div class="subsection-title">1.3 Address</div>
        <div class="row mb-2">
            <div class="col-md-3"><input type="text" name="county" class="form-control" placeholder="County" value="{{ old('county', $physical['county'] ?? '') }}"></div>
            <div class="col-md-3"><input type="text" name="town" class="form-control" placeholder="Town" value="{{ old('town', $physical['town'] ?? '') }}"></div>
            <div class="col-md-3"><input type="text" name="street_road" class="form-control" placeholder="Street/Road" value="{{ old('street_road', $physical['street_road'] ?? '') }}"></div>
            <div class="col-md-3"><input type="text" name="building_name" class="form-control" placeholder="Building" value="{{ old('building_name', $physical['building_name'] ?? '') }}"></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3"><input type="text" name="floor_no" class="form-control" placeholder="Floor No." value="{{ old('floor_no', $physical['floor_no'] ?? '') }}"></div>
            <div class="col-md-3"><input type="text" name="room_no" class="form-control" placeholder="Room No." value="{{ old('room_no', $physical['room_no'] ?? '') }}"></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4"><input type="text" name="p_o_box" class="form-control" placeholder="P.O. Box" value="{{ old('p_o_box', $postal['p_o_box'] ?? '') }}"></div>
            <div class="col-md-4"><input type="text" name="postal_town" class="form-control" placeholder="Town" value="{{ old('postal_town', $postal['postal_town'] ?? '') }}"></div>
            <div class="col-md-4"><input type="text" name="postal_code" class="form-control" placeholder="Code" value="{{ old('postal_code', $postal['postal_code'] ?? '') }}"></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4"><input type="text" name="tel_no" class="form-control" placeholder="Tel" value="{{ old('tel_no', $contacts['tel_no'] ?? '') }}"></div>
            <div class="col-md-4"><input type="text" name="mobile_no" class="form-control" placeholder="Mobile" value="{{ old('mobile_no', $contacts['mobile_no'] ?? '') }}"></div>
            <div class="col-md-4"><input type="text" name="other_tel" class="form-control" placeholder="Other Tel" value="{{ old('other_tel', $contacts['other_tel'] ?? '') }}"></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email', $contacts['email'] ?? '') }}"></div>
            <div class="col-md-6"><input type="url" name="web_address" class="form-control" placeholder="Website" value="{{ old('web_address', $contacts['web_address'] ?? '') }}"></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4"><label>CEO Name</label><input type="text" name="ceo_name" class="form-control" value="{{ old('ceo_name', $contacts['ceo_name'] ?? '') }}"></div>
            <div class="col-md-4"><label>Contact Person</label><input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $contacts['contact_person'] ?? '') }}"></div>
            <div class="col-md-4"><label>Contact Email</label><input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $contacts['contact_email'] ?? '') }}"></div>
        </div>

        <div class="mb-3">
            <label>Address changed during quarter?</label>
            <div class="form-check form-check-inline">
                <input type="radio" name="address_changed" value="yes" class="form-check-input" {{ old('address_changed', $return->address_changed) ? 'checked' : '' }}> Yes
                <input type="radio" name="address_changed" value="no" class="form-check-input ms-3" {{ !old('address_changed', $return->address_changed) ? 'checked' : '' }}> No
            </div>
        </div>

        <!-- Infrastructure Section with existing data -->
        <div class="section-title">2. INFRASTRUCTURE DEPLOYED</div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark"><tr><th>No.</th><th>Type</th><th>Description</th><th></th></tr></thead>
                <tbody id="infrastructure_body">
                    @if(!empty($infrastructure))
                        @foreach($infrastructure as $index => $infra)
                        <tr><td>{{ $loop->iteration }}</td><td><input type="text" name="infrastructure[{{ $index }}][type]" class="form-control form-control-sm" value="{{ $infra['type'] ?? '' }}"></td><td><input type="text" name="infrastructure[{{ $index }}][description]" class="form-control form-control-sm" value="{{ $infra['description'] ?? '' }}"></td><td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td></tr>
                        @endforeach
                    @else
                        @for($i = 1; $i <= 3; $i++)<tr><td>{{ $i }}</td><td><input type="text" name="infrastructure[{{ $i }}][type]" class="form-control form-control-sm"></td><td><input type="text" name="infrastructure[{{ $i }}][description]" class="form-control form-control-sm"></td><td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td></tr>@endfor
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addInfrastructureRow()">+ Add Infrastructure</button>
        </div>

        <!-- Primary Numbers -->
        <div class="section-title">3. NUMBER UTILIZATION</div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark"><tr><th>Resource</th><th>Utilized Numbers</th></tr></thead>
                <tbody>
                    <tr><th>Primary Number Assignments by CA</th><td><input type="text" name="primary_numbers[ca_assignments]" class="form-control form-control-sm" value="{{ old('primary_numbers.ca_assignments', $primaryNumbers['ca_assignments'] ?? '') }}"></td></tr>
                    <tr><th>Short Codes</th><td><input type="text" name="primary_numbers[short_codes]" class="form-control form-control-sm" value="{{ old('primary_numbers.short_codes', $primaryNumbers['short_codes'] ?? '') }}"></td></tr>
                    <tr><th>USSD Codes</th><td><input type="text" name="primary_numbers[ussd_codes]" class="form-control form-control-sm" value="{{ old('primary_numbers.ussd_codes', $primaryNumbers['ussd_codes'] ?? '') }}"></td></tr>
                    <tr><th>Premium Rate Numbers</th><td><input type="text" name="primary_numbers[premium_rate]" class="form-control form-control-sm" value="{{ old('primary_numbers.premium_rate', $primaryNumbers['premium_rate'] ?? '') }}"></td></tr>
                    <tr><th>Toll Free Numbers</th><td><input type="text" name="primary_numbers[toll_free]" class="form-control form-control-sm" value="{{ old('primary_numbers.toll_free', $primaryNumbers['toll_free'] ?? '') }}"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- PWD Compliance -->
        <div class="section-title">PWD COMPLIANCE</div>
        <div class="row mb-3">
            <div class="col-md-6"><label>Aware of KS2952?</label><select name="pwd_aware" class="form-select"><option value="no" {{ old('pwd_aware', $return->pwd_aware ? 'yes' : 'no') == 'no' ? 'selected' : '' }}>No</option><option value="yes" {{ old('pwd_aware', $return->pwd_aware ? 'yes' : 'no') == 'yes' ? 'selected' : '' }}>Yes</option></select></div>
            <div class="col-md-6"><label>Complied with standard?</label><select name="pwd_complied" class="form-select"><option value="no" {{ old('pwd_complied', $return->pwd_complied ? 'yes' : 'no') == 'no' ? 'selected' : '' }}>No</option><option value="yes" {{ old('pwd_complied', $return->pwd_complied ? 'yes' : 'no') == 'yes' ? 'selected' : '' }}>Yes</option></select></div>
        </div>
        <div class="mb-3"><label>Actions for PWDs</label><textarea name="pwd_actions" rows="2" class="form-control">{{ old('pwd_actions', $return->pwd_actions) }}</textarea></div>
        <div class="mb-3"><label>Challenges serving PWDs</label><textarea name="pwd_challenges" rows="2" class="form-control">{{ old('pwd_challenges', $return->pwd_challenges) }}</textarea></div>
        <div class="mb-3"><label>Future plans</label><textarea name="pwd_future_plans" rows="2" class="form-control">{{ old('pwd_future_plans', $return->pwd_future_plans) }}</textarea></div>

        <!-- Environmental -->
        <div class="section-title">ENVIRONMENTAL SUSTAINABILITY</div>
        <div class="mb-3"><label>E-waste initiatives</label><textarea name="ewaste_initiatives" rows="2" class="form-control">{{ old('ewaste_initiatives', $return->ewaste_initiatives) }}</textarea></div>
        <div class="mb-3"><label>Carbon reduction</label><textarea name="carbon_initiatives" rows="2" class="form-control">{{ old('carbon_initiatives', $return->carbon_initiatives) }}</textarea></div>
        <div class="mb-3"><label>EMCA status</label><textarea name="emca_status" rows="2" class="form-control">{{ old('emca_status', $return->emca_status) }}</textarea></div>

        <!-- Comments -->
        <div class="section-title">COMMENTS</div>
        <div class="mb-3"><textarea name="comments" rows="3" class="form-control">{{ old('comments', $return->comments) }}</textarea></div>

        <!-- Submitter -->
        <div class="section-title">SUBMITTER DETAILS</div>
        <div class="row mb-3">
            <div class="col-md-3"><label>Name <span class="text-danger">*</span></label><input type="text" name="submitter_name" class="form-control" value="{{ old('submitter_name', $return->submitter_name) }}" required></div>
            <div class="col-md-3"><label>Title</label><input type="text" name="submitter_title" class="form-control" value="{{ old('submitter_title', $return->submitter_title) }}"></div>
            <div class="col-md-3"><label>Date</label><input type="date" name="submitter_date" class="form-control" value="{{ old('submitter_date', $return->submitter_date ? date('Y-m-d', strtotime($return->submitter_date)) : '') }}"></div>
            <div class="col-md-3"><label>Company Stamp</label><input type="file" name="company_stamp" class="form-control">@if($return->company_stamp_path)<small class="existing-doc">Current: <a href="{{ Storage::url($return->company_stamp_path) }}" target="_blank">View</a></small>@endif</div>
        </div>

        <!-- Documents -->
        <div class="section-title">MANDATORY DOCUMENTS</div>
        <div class="mb-3"><label>Shareholding Certificate</label><input type="file" name="shareholding_cert" class="form-control">@if(isset($documents['shareholding_cert']))<small>Current: <a href="{{ Storage::url($documents['shareholding_cert']) }}" target="_blank">View</a></small>@endif</div>
        <div class="mb-3"><label>Audited Financials</label><input type="file" name="audited_financials" class="form-control">@if(isset($documents['audited_financials']))<small>Current: <a href="{{ Storage::url($documents['audited_financials']) }}" target="_blank">View</a></small>@endif</div>
        <div class="mb-3"><label>Tax Compliance</label><input type="file" name="tax_compliance" class="form-control">@if(isset($documents['tax_compliance']))<small>Current: <a href="{{ Storage::url($documents['tax_compliance']) }}" target="_blank">View</a></small>@endif</div>
        <div class="mb-3"><label>Tariff Structure</label><input type="file" name="tariff_structure" class="form-control">@if(isset($documents['tariff_structure']))<small>Current: <a href="{{ Storage::url($documents['tariff_structure']) }}" target="_blank">View</a></small>@endif</div>

        <div class="btn-submit-section text-center">
            <button type="submit" name="submit" value="submit" class="btn btn-primary btn-lg">Submit to CAK</button>
            <button type="submit" name="save_draft" value="1" class="btn btn-secondary btn-lg">Save Draft</button>
            <button type="button" class="btn btn-info btn-lg" onclick="generatePDF()">Download PDF</button>
            <a href="{{ route('nfp.show', $return->id) }}" class="btn btn-dark btn-lg">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    let infraCounter = {{ count($infrastructure) + 1 }};

    function addInfrastructureRow() {
        const tbody = document.getElementById('infrastructure_body');
        const row = tbody.insertRow();
        row.innerHTML = `<td>${infraCounter}</td><td><input type="text" name="infrastructure[${infraCounter}][type]" class="form-control form-control-sm"></td><td><input type="text" name="infrastructure[${infraCounter}][description]" class="form-control form-control-sm"></td><td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>`;
        infraCounter++;
        attachRemoveEvents();
        renumberRows();
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('tr');
                if (row.parentElement.children.length > 1) row.remove();
                else alert('At least one row must remain.');
                renumberRows();
            };
        });
    }

    function renumberRows() {
        document.querySelectorAll('#infrastructure_body tr').forEach((row, idx) => row.cells[0].textContent = idx + 1);
    }

    function generatePDF() {
        html2pdf().set({ margin: 0.5, filename: 'NFP_Compliance_Return_{{ $return->id }}.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' } }).from(document.querySelector('.form-container')).save();
    }

    document.addEventListener('DOMContentLoaded', attachRemoveEvents);
</script>
@endpush
@endsection
