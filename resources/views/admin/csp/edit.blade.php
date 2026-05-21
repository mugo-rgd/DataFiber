@extends('layouts.app')

@section('title', 'Edit CSP Compliance Return')

@section('page-title', 'Edit Content Service Provider (CSP) Compliance Return')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('csp.index') }}">CSP Returns</a></li>
<li class="breadcrumb-item"><a href="{{ route('csp.show', $return->id) }}">Return Details</a></li>
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
    .total-field { background-color: #f8f9fc; font-weight: bold; }
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
        <h4 class="text-center mb-1" style="font-size: 18px; font-weight: bold;">CONTENT SERVICE PROVIDER (CSP)</h4>
        <h5 class="text-center mb-2" style="font-size: 16px;">EDIT COMPLIANCE RETURN</h5>
        <p class="text-muted text-center small mb-0">
            Compliance ID: {{ $return->compliance_id ?? 'Draft' }} |
            Status: <span class="badge bg-kp-yellow">{{ ucfirst($return->status) }}</span>
        </p>
    </div>

    <div class="alert alert-kp-warning">
        <i class="fas fa-info-circle"></i> You are editing a draft. Make your changes and submit when ready.
    </div>

    <form method="POST" action="{{ route('csp.update', $return->id) }}" enctype="multipart/form-data" id="cspForm">
        @csrf
        @method('PUT')

        @php
            $physical = $return->physical_address ?? [];
            $postal = $return->postal_address ?? [];
            $contacts = $return->contacts ?? [];
            $services = $return->services ?? [];
            $numbering = $return->numbering_resources ?? [];
            $complaints = $return->complaints ?? [];
            $moneyTransfer = $return->money_transfer ?? [];
            $documents = $return->documents ?? [];
        @endphp

        <!-- Section 1: Licence Details -->
        <div class="section-title">1. LICENCE DETAILS</div>

        <div class="subsection-title">1.1 Licence Details</div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Name of Licensee <span class="text-danger">*</span></label>
                <input type="text" name="licensee_name" class="form-control @error('licensee_name') is-invalid @enderror" value="{{ old('licensee_name', $return->licensee_name) }}" required>
                @error('licensee_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">License No.</label>
                <input type="text" name="license_no" class="form-control" value="{{ old('license_no', $return->license_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Other Licenses Held</label>
                <input type="text" name="other_licenses" class="form-control" value="{{ old('other_licenses', $return->other_licenses) }}">
            </div>
        </div>

        <div class="subsection-title">1.2 Period under review</div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Financial Year <span class="text-danger">*</span></label>
                <select name="financial_year" class="form-select @error('financial_year') is-invalid @enderror" required>
                    <option value="">Select Financial Year</option>
                    <option value="2023/2024" {{ old('financial_year', $return->financial_year) == '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                    <option value="2024/2025" {{ old('financial_year', $return->financial_year) == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                    <option value="2025/2026" {{ old('financial_year', $return->financial_year) == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                </select>
                @error('financial_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Quarter <span class="text-danger">*</span></label>
                <select name="quarter" class="form-select @error('quarter') is-invalid @enderror" required>
                    <option value="">Select Quarter</option>
                    <option value="Q1" {{ old('quarter', $return->quarter) == 'Q1' ? 'selected' : '' }}>Q1 (July-Sep)</option>
                    <option value="Q2" {{ old('quarter', $return->quarter) == 'Q2' ? 'selected' : '' }}>Q2 (Oct-Dec)</option>
                    <option value="Q3" {{ old('quarter', $return->quarter) == 'Q3' ? 'selected' : '' }}>Q3 (Jan-Mar)</option>
                    <option value="Q4" {{ old('quarter', $return->quarter) == 'Q4' ? 'selected' : '' }}>Q4 (Apr-Jun)</option>
                </select>
                @error('quarter')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <!-- Address Section -->
        <div class="subsection-title">1.3 Address</div>

        <div class="sub-subsection-title">1.3.1 Physical Address</div>
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

        <div class="sub-subsection-title">1.3.2 Postal Address</div>
        <div class="row mb-3">
            <div class="col-md-4"><input type="text" name="p_o_box" class="form-control" placeholder="P.O. Box" value="{{ old('p_o_box', $postal['p_o_box'] ?? '') }}"></div>
            <div class="col-md-4"><input type="text" name="postal_town" class="form-control" placeholder="Town" value="{{ old('postal_town', $postal['postal_town'] ?? '') }}"></div>
            <div class="col-md-4"><input type="text" name="postal_code" class="form-control" placeholder="Code" value="{{ old('postal_code', $postal['postal_code'] ?? '') }}"></div>
        </div>

        <div class="sub-subsection-title">1.3.3 Telephone Contacts</div>
        <div class="row mb-3">
            <div class="col-md-4"><input type="text" name="tel_no" class="form-control" placeholder="Tel No." value="{{ old('tel_no', $contacts['tel_no'] ?? '') }}"></div>
            <div class="col-md-4"><input type="text" name="mobile_no" class="form-control" placeholder="Mobile No." value="{{ old('mobile_no', $contacts['mobile_no'] ?? '') }}"></div>
            <div class="col-md-4"><input type="text" name="other_tel" class="form-control" placeholder="Other Tel." value="{{ old('other_tel', $contacts['other_tel'] ?? '') }}"></div>
        </div>

        <div class="sub-subsection-title">1.3.4 Email and Web Address</div>
        <div class="row mb-3">
            <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email', $contacts['email'] ?? '') }}"></div>
            <div class="col-md-6"><input type="url" name="web_address" class="form-control" placeholder="Website" value="{{ old('web_address', $contacts['web_address'] ?? '') }}"></div>
        </div>

        <div class="subsection-title">1.4 Contact Details</div>
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

        <!-- Services Section -->
        <div class="section-title">2. SERVICES PROVIDED</div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr><th>Short Code</th><th>Service</th><th>Company</th><th>Authorization</th><th>Charges</th><th>Month 1</th><th>Month 2</th><th>Month 3</th><th>Total</th><th></th></tr>
                </thead>
                <tbody id="services_body">
                    @if(!empty($services))
                        @foreach($services as $index => $service)
                        <tr>
                            <td><input type="text" name="services[{{ $index }}][shortcode]" class="form-control form-control-sm" value="{{ $service['shortcode'] ?? '' }}"></td>
                            <td><input type="text" name="services[{{ $index }}][service]" class="form-control form-control-sm" value="{{ $service['service'] ?? '' }}"></td>
                            <td><input type="text" name="services[{{ $index }}][company]" class="form-control form-control-sm" value="{{ $service['company'] ?? '' }}"></td>
                            <td><input type="text" name="services[{{ $index }}][authorization]" class="form-control form-control-sm" value="{{ $service['authorization'] ?? '' }}"></td>
                            <td><input type="text" name="services[{{ $index }}][charges]" class="form-control form-control-sm" value="{{ $service['charges'] ?? '' }}"></td>
                            <td><input type="number" name="services[{{ $index }}][month1]" class="form-control form-control-sm month-input" data-index="{{ $index }}" value="{{ $service['month1'] ?? '' }}"></td>
                            <td><input type="number" name="services[{{ $index }}][month2]" class="form-control form-control-sm month-input" data-index="{{ $index }}" value="{{ $service['month2'] ?? '' }}"></td>
                            <td><input type="number" name="services[{{ $index }}][month3]" class="form-control form-control-sm month-input" data-index="{{ $index }}" value="{{ $service['month3'] ?? '' }}"></td>
                            <td><input type="text" name="services[{{ $index }}][total]" class="form-control form-control-sm total-field" value="{{ $service['total'] ?? '' }}" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                        </tr>
                        @endforeach
                    @else
                        @for($i = 1; $i <= 3; $i++)
                        <tr>
                            <td><input type="text" name="services[{{ $i }}][shortcode]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="services[{{ $i }}][service]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="services[{{ $i }}][company]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="services[{{ $i }}][authorization]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="services[{{ $i }}][charges]" class="form-control form-control-sm"></td>
                            <td><input type="number" name="services[{{ $i }}][month1]" class="form-control form-control-sm month-input" data-index="{{ $i }}"></td>
                            <td><input type="number" name="services[{{ $i }}][month2]" class="form-control form-control-sm month-input" data-index="{{ $i }}"></td>
                            <td><input type="number" name="services[{{ $i }}][month3]" class="form-control form-control-sm month-input" data-index="{{ $i }}"></td>
                            <td><input type="text" name="services[{{ $i }}][total]" class="form-control form-control-sm total-field" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addServiceRow()">+ Add Service</button>
        </div>

        <!-- Numbering Resources -->
        <div class="section-title">NUMBERING RESOURCES</div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr><th>Resource</th><th>Total</th><th>In Use</th><th>Not in Use</th><th>Reasons</th><th></th></tr>
                </thead>
                <tbody id="numbering_body">
                    @if(!empty($numbering))
                        @foreach($numbering as $index => $num)
                        <tr>
                            <td><input type="text" name="numbering[{{ $index }}][resource]" class="form-control form-control-sm" value="{{ $num['resource'] ?? '' }}"></td>
                            <td><input type="text" name="numbering[{{ $index }}][total]" class="form-control form-control-sm" value="{{ $num['total'] ?? '' }}"></td>
                            <td><input type="text" name="numbering[{{ $index }}][in_use]" class="form-control form-control-sm" value="{{ $num['in_use'] ?? '' }}"></td>
                            <td><input type="text" name="numbering[{{ $index }}][not_in_use]" class="form-control form-control-sm" value="{{ $num['not_in_use'] ?? '' }}"></td>
                            <td><input type="text" name="numbering[{{ $index }}][reasons]" class="form-control form-control-sm" value="{{ $num['reasons'] ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                        </tr>
                        @endforeach
                    @else
                        @for($i = 1; $i <= 3; $i++)
                        <tr>
                            <td><input type="text" name="numbering[{{ $i }}][resource]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="numbering[{{ $i }}][total]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="numbering[{{ $i }}][in_use]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="numbering[{{ $i }}][not_in_use]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="numbering[{{ $i }}][reasons]" class="form-control form-control-sm"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addNumberingRow()">+ Add Resource</button>
        </div>

        <!-- PWD Compliance -->
        <div class="section-title">PWD COMPLIANCE (KS2952)</div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Aware of KS2952?</label>
                <select name="pwd_aware" class="form-select">
                    <option value="no" {{ old('pwd_aware', $return->pwd_aware ? 'yes' : 'no') == 'no' ? 'selected' : '' }}>No</option>
                    <option value="yes" {{ old('pwd_aware', $return->pwd_aware ? 'yes' : 'no') == 'yes' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Complied with standard?</label>
                <select name="pwd_complied" class="form-select">
                    <option value="no" {{ old('pwd_complied', $return->pwd_complied ? 'yes' : 'no') == 'no' ? 'selected' : '' }}>No</option>
                    <option value="yes" {{ old('pwd_complied', $return->pwd_complied ? 'yes' : 'no') == 'yes' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
        </div>
        <div class="mb-3"><label>Actions for PWD accessibility</label><textarea name="pwd_actions" rows="2" class="form-control">{{ old('pwd_actions', $return->pwd_actions) }}</textarea></div>
        <div class="mb-3"><label>Challenges serving PWDs</label><textarea name="pwd_challenges" rows="2" class="form-control">{{ old('pwd_challenges', $return->pwd_challenges) }}</textarea></div>
        <div class="mb-3"><label>Future plans for inclusivity</label><textarea name="pwd_future_plans" rows="2" class="form-control">{{ old('pwd_future_plans', $return->pwd_future_plans) }}</textarea></div>

        <!-- Environmental -->
        <div class="section-title">ENVIRONMENTAL SUSTAINABILITY</div>
        <div class="mb-3"><label>E-waste initiatives</label><textarea name="ewaste_initiatives" rows="2" class="form-control">{{ old('ewaste_initiatives', $return->ewaste_initiatives) }}</textarea></div>
        <div class="mb-3"><label>Carbon reduction</label><textarea name="carbon_initiatives" rows="2" class="form-control">{{ old('carbon_initiatives', $return->carbon_initiatives) }}</textarea></div>
        <div class="mb-3"><label>EMCA compliance status</label><textarea name="emca_status" rows="2" class="form-control">{{ old('emca_status', $return->emca_status) }}</textarea></div>

        <!-- Comments -->
        <div class="section-title">COMMENTS/SUGGESTIONS</div>
        <div class="mb-3"><textarea name="comments" rows="3" class="form-control">{{ old('comments', $return->comments) }}</textarea></div>

        <!-- Submitter -->
        <div class="section-title">SUBMITTER DETAILS</div>
        <div class="row mb-3">
            <div class="col-md-3"><label>Name <span class="text-danger">*</span></label><input type="text" name="submitter_name" class="form-control" value="{{ old('submitter_name', $return->submitter_name) }}" required></div>
            <div class="col-md-3"><label>Title</label><input type="text" name="submitter_title" class="form-control" value="{{ old('submitter_title', $return->submitter_title) }}"></div>
            <div class="col-md-3"><label>Date</label><input type="date" name="submitter_date" class="form-control" value="{{ old('submitter_date', $return->submitter_date ? date('Y-m-d', strtotime($return->submitter_date)) : '') }}"></div>
            <div class="col-md-3"><label>Company Stamp</label><input type="file" name="company_stamp" class="form-control">
                @if($return->company_stamp_path)
                    <small class="text-muted existing-doc">Current: <a href="{{ Storage::url($return->company_stamp_path) }}" target="_blank">View existing</a></small>
                @endif
            </div>
        </div>

        <!-- Documents -->
        <div class="section-title">MANDATORY DOCUMENTS</div>
        <div class="mb-3"><label>Shareholding Certificate</label><input type="file" name="shareholding_cert" class="form-control">
            @if(isset($documents['shareholding_cert']))<small>Current: <a href="{{ Storage::url($documents['shareholding_cert']) }}" target="_blank">View</a></small>@endif
        </div>
        <div class="row mb-3">
            <div class="col-md-3"><label>FY Start Date</label><input type="date" name="fy_start" class="form-control" value="{{ old('fy_start', $return->fy_start ? date('Y-m-d', strtotime($return->fy_start)) : '') }}"></div>
            <div class="col-md-3"><label>FY End Date</label><input type="date" name="fy_end" class="form-control" value="{{ old('fy_end', $return->fy_end ? date('Y-m-d', strtotime($return->fy_end)) : '') }}"></div>
        </div>
        <div class="mb-3"><label>Audited Financials</label><input type="file" name="audited_financials" class="form-control">
            @if(isset($documents['audited_financials']))<small>Current: <a href="{{ Storage::url($documents['audited_financials']) }}" target="_blank">View</a></small>@endif
        </div>
        <div class="mb-3"><label>Tax Compliance</label><input type="file" name="tax_compliance" class="form-control">
            @if(isset($documents['tax_compliance']))<small>Current: <a href="{{ Storage::url($documents['tax_compliance']) }}" target="_blank">View</a></small>@endif
        </div>
        <div class="mb-3"><label>Copyright Clearance</label><input type="file" name="copyright_clearance" class="form-control">
            @if(isset($documents['copyright_clearance']))<small>Current: <a href="{{ Storage::url($documents['copyright_clearance']) }}" target="_blank">View</a></small>@endif
        </div>

        <div class="btn-submit-section text-center">
            <button type="submit" name="submit" value="submit" class="btn btn-kp-primary btn-lg">
                <i class="fas fa-paper-plane"></i> Submit to CAK
            </button>
            <button type="submit" name="save_draft" value="1" class="btn btn-secondary btn-lg">
                <i class="fas fa-save"></i> Save Draft
            </button>
            <button type="button" class="btn btn-info btn-lg" onclick="generatePDF()">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
            <a href="{{ route('csp.show', $return->id) }}" class="btn btn-dark btn-lg">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    let serviceCounter = {{ count($services) + 1 }};
    let numberingCounter = {{ count($numbering) + 1 }};

    function calculateTotal(rowIndex) {
        const month1 = parseFloat(document.querySelector(`input[name="services[${rowIndex}][month1]"]`)?.value) || 0;
        const month2 = parseFloat(document.querySelector(`input[name="services[${rowIndex}][month2]"]`)?.value) || 0;
        const month3 = parseFloat(document.querySelector(`input[name="services[${rowIndex}][month3]"]`)?.value) || 0;
        const totalField = document.querySelector(`input[name="services[${rowIndex}][total]"]`);
        if (totalField) totalField.value = month1 + month2 + month3;
    }

    function attachMonthEvents() {
        document.querySelectorAll('.month-input').forEach(input => {
            input.removeEventListener('change', handleMonthChange);
            input.addEventListener('change', handleMonthChange);
        });
    }

    function handleMonthChange(e) {
        const index = e.target.getAttribute('data-index');
        if (index) calculateTotal(index);
    }

    function addServiceRow() {
        const tbody = document.getElementById('services_body');
        const row = tbody.insertRow();
        row.innerHTML = `
            <td><input type="text" name="services[${serviceCounter}][shortcode]" class="form-control form-control-sm"></td>
            <td><input type="text" name="services[${serviceCounter}][service]" class="form-control form-control-sm"></td>
            <td><input type="text" name="services[${serviceCounter}][company]" class="form-control form-control-sm"></td>
            <td><input type="text" name="services[${serviceCounter}][authorization]" class="form-control form-control-sm"></td>
            <td><input type="text" name="services[${serviceCounter}][charges]" class="form-control form-control-sm"></td>
            <td><input type="number" name="services[${serviceCounter}][month1]" class="form-control form-control-sm month-input" data-index="${serviceCounter}"></td>
            <td><input type="number" name="services[${serviceCounter}][month2]" class="form-control form-control-sm month-input" data-index="${serviceCounter}"></td>
            <td><input type="number" name="services[${serviceCounter}][month3]" class="form-control form-control-sm month-input" data-index="${serviceCounter}"></td>
            <td><input type="text" name="services[${serviceCounter}][total]" class="form-control form-control-sm total-field" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
        `;
        serviceCounter++;
        attachMonthEvents();
        attachRemoveEvents();
    }

    function addNumberingRow() {
        const tbody = document.getElementById('numbering_body');
        const row = tbody.insertRow();
        row.innerHTML = `
            <td><input type="text" name="numbering[${numberingCounter}][resource]" class="form-control form-control-sm"></td>
            <td><input type="text" name="numbering[${numberingCounter}][total]" class="form-control form-control-sm"></td>
            <td><input type="text" name="numbering[${numberingCounter}][in_use]" class="form-control form-control-sm"></td>
            <td><input type="text" name="numbering[${numberingCounter}][not_in_use]" class="form-control form-control-sm"></td>
            <td><input type="text" name="numbering[${numberingCounter}][reasons]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
        `;
        numberingCounter++;
        attachRemoveEvents();
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('tr');
                if (row && row.parentElement.children.length > 1) {
                    row.remove();
                } else if (row) {
                    alert('At least one row must remain. Clear the values instead of deleting.');
                }
            };
        });
    }

    function generatePDF() {
        const element = document.querySelector('.form-container');
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: 'CSP_Compliance_Return_{{ $return->id }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, letterRendering: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    document.addEventListener('DOMContentLoaded', function() {
        attachMonthEvents();
        attachRemoveEvents();
        @if(!empty($services))
            @foreach($services as $index => $service)
                calculateTotal({{ $index }});
            @endforeach
        @else
            for (let i = 1; i <= 3; i++) calculateTotal(i);
        @endif
    });
</script>
@endpush
@endsection
