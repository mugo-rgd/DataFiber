@extends('layouts.app')

@section('title', 'Edit ASP Compliance Return')

@section('page-title', 'Edit Application Service Provider (ASP) Compliance Return')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('asp.index') }}">ASP Returns</a></li>
<li class="breadcrumb-item"><a href="{{ route('asp.show', $return->id) }}">Return Details</a></li>
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
        <h4 class="text-center mb-1" style="font-size: 18px; font-weight: bold;">APPLICATION SERVICE PROVIDER (ASP)</h4>
        <h5 class="text-center mb-2" style="font-size: 16px;">EDIT COMPLIANCE RETURN</h5>
        <p class="text-muted text-center small mb-0">
            Compliance ID: {{ $return->compliance_id ?? 'Draft' }} |
            Status: <span class="badge bg-warning">{{ ucfirst($return->status) }}</span>
        </p>
    </div>

    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i> You are editing a draft. Make your changes and submit when ready.
    </div>

    <form method="POST" action="{{ route('asp.update', $return->id) }}" enctype="multipart/form-data" id="aspForm">
        @csrf
        @method('PUT')

        @php
            $physical = $return->physical_address ?? [];
            $postal = $return->postal_address ?? [];
            $contacts = $return->contacts ?? [];
            $m2mServices = $return->m2m_services ?? [];
            $subscriptions = $return->subscriptions ?? [];
            $mobileDevices = $return->mobile_devices ?? [];
            $dataSubscriptions = $return->data_subscriptions ?? [];
            $cybersecurity = $return->cybersecurity ?? [];
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
                    <option value="Q1" {{ old('quarter', $return->quarter) == 'Q1' ? 'selected' : '' }}>Quarter 1 (1st July – 30th Sep)</option>
                    <option value="Q2" {{ old('quarter', $return->quarter) == 'Q2' ? 'selected' : '' }}>Quarter 2 (1st Oct – 31st Dec)</option>
                    <option value="Q3" {{ old('quarter', $return->quarter) == 'Q3' ? 'selected' : '' }}>Quarter 3 (1st Jan – 31st Mar)</option>
                    <option value="Q4" {{ old('quarter', $return->quarter) == 'Q4' ? 'selected' : '' }}>Quarter 4 (1st Apr – 30th Jun)</option>
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
            <div class="col-md-3"><input type="text" name="building_name" class="form-control" placeholder="Name of Building" value="{{ old('building_name', $physical['building_name'] ?? '') }}"></div>
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
            <div class="col-md-4"><input type="text" name="other_tel" class="form-control" placeholder="Other Tel. Nos." value="{{ old('other_tel', $contacts['other_tel'] ?? '') }}"></div>
        </div>

        <div class="sub-subsection-title">1.3.4 Email and Web Address</div>
        <div class="row mb-3">
            <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email address" value="{{ old('email', $contacts['email'] ?? '') }}"></div>
            <div class="col-md-6"><input type="url" name="web_address" class="form-control" placeholder="Web address" value="{{ old('web_address', $contacts['web_address'] ?? '') }}"></div>
        </div>

        <div class="subsection-title">1.4 Contact Details</div>
        <div class="row mb-3">
            <div class="col-md-4"><label class="form-label">CEO Name</label><input type="text" name="ceo_name" class="form-control" value="{{ old('ceo_name', $contacts['ceo_name'] ?? '') }}"></div>
            <div class="col-md-4"><label class="form-label">Contact Person</label><input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $contacts['contact_person'] ?? '') }}"></div>
            <div class="col-md-4"><label class="form-label">Contact Email</label><input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $contacts['contact_email'] ?? '') }}"></div>
        </div>

        <div class="mb-3">
            <label>Did any of the address information change during the quarter?</label>
            <div class="form-check form-check-inline">
                <input type="radio" name="address_changed" value="yes" class="form-check-input" {{ old('address_changed', $return->address_changed) ? 'checked' : '' }}> Yes
                <input type="radio" name="address_changed" value="no" class="form-check-input ms-3" {{ !old('address_changed', $return->address_changed) ? 'checked' : '' }}> No
            </div>
        </div>

        <!-- Section 2: Services -->
        <div class="section-title">2. SERVICES PROVIDED UNDER THE LICENSE</div>

        <div class="subsection-title">2.1 Machine to Machine Services (e.g Car tracking, IOT, etc)</div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr><th>No.</th><th>Service Provided</th><th>Brief Description</th><th>Number of Subscriptions</th><th></th></tr>
                </thead>
                <tbody id="m2m_body">
                    @if(!empty($m2mServices))
                        @foreach($m2mServices as $index => $service)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><input type="text" name="m2m_services[{{ $index }}][service]" class="form-control form-control-sm" value="{{ $service['service'] ?? '' }}"></td>
                            <td><input type="text" name="m2m_services[{{ $index }}][description]" class="form-control form-control-sm" value="{{ $service['description'] ?? '' }}"></td>
                            <td><input type="text" name="m2m_services[{{ $index }}][subscriptions]" class="form-control form-control-sm" value="{{ $service['subscriptions'] ?? '' }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                        </tr>
                        @endforeach
                    @else
                        @for($i = 1; $i <= 5; $i++)
                        <tr>
                            <td>{{ $i }}</td>
                            <td><input type="text" name="m2m_services[{{ $i }}][service]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="m2m_services[{{ $i }}][description]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="m2m_services[{{ $i }}][subscriptions]" class="form-control form-control-sm"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addM2MRow()">+ Add M2M Service</button>
        </div>

        <!-- Telecommunications Subscriptions -->
        <div class="subsection-title">2.2 Telecommunications Service Subscriptions</div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr><th rowspan="2">Category of Subscriptions</th><th colspan="3">Number of Registered Active Subscriptions</th></tr>
                    <tr class="table-secondary"><th>Month 1</th><th>Month 2</th><th>Month 3</th></tr>
                </thead>
                <tbody>
                    <tr class="table-secondary"><th colspan="4">Postpaid Services</th></tr>
                    <tr>
                        <th>GSM (SIM Cards)</th>
                        @foreach(['m1','m2','m3'] as $m)
                        <td><input type="text" name="subscriptions[postpaid_gsm][{{ $m }}]" class="form-control form-control-sm" value="{{ old('subscriptions.postpaid_gsm.'.$m, $subscriptions['postpaid_gsm'][$m] ?? '') }}"></td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Terrestrial Fixed Line</th>
                        @foreach(['m1','m2','m3'] as $m)
                        <td><input type="text" name="subscriptions[postpaid_fixed_line][{{ $m }}]" class="form-control form-control-sm" value="{{ old('subscriptions.postpaid_fixed_line.'.$m, $subscriptions['postpaid_fixed_line'][$m] ?? '') }}"></td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Terrestrial Fixed Wireless</th>
                        @foreach(['m1','m2','m3'] as $m)
                        <td><input type="text" name="subscriptions[postpaid_wireless][{{ $m }}]" class="form-control form-control-sm" value="{{ old('subscriptions.postpaid_wireless.'.$m, $subscriptions['postpaid_wireless'][$m] ?? '') }}"></td>
                        @endforeach
                    </tr>
                    <tr class="table-secondary"><th colspan="4">Prepaid Services</th></tr>
                    <tr>
                        <th>GSM (SIM Cards)</th>
                        @foreach(['m1','m2','m3'] as $m)
                        <td><input type="text" name="subscriptions[prepaid_gsm][{{ $m }}]" class="form-control form-control-sm" value="{{ old('subscriptions.prepaid_gsm.'.$m, $subscriptions['prepaid_gsm'][$m] ?? '') }}"></td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Terrestrial Fixed Line</th>
                        @foreach(['m1','m2','m3'] as $m)
                        <td><input type="text" name="subscriptions[prepaid_fixed_line][{{ $m }}]" class="form-control form-control-sm" value="{{ old('subscriptions.prepaid_fixed_line.'.$m, $subscriptions['prepaid_fixed_line'][$m] ?? '') }}"></td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Terrestrial Fixed Wireless</th>
                        @foreach(['m1','m2','m3'] as $m)
                        <td><input type="text" name="subscriptions[prepaid_wireless][{{ $m }}]" class="form-control form-control-sm" value="{{ old('subscriptions.prepaid_wireless.'.$m, $subscriptions['prepaid_wireless'][$m] ?? '') }}"></td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Devices -->
        <div class="subsection-title">2.3 Number of Mobile Phone Devices</div>
        <div class="row mb-4">
            <div class="col-md-4"><label>Feature Phone</label><input type="text" name="mobile_devices[feature_phones]" class="form-control" value="{{ old('mobile_devices.feature_phones', $mobileDevices['feature_phones'] ?? '') }}"></div>
            <div class="col-md-4"><label>Smart Phone</label><input type="text" name="mobile_devices[smart_phones]" class="form-control" value="{{ old('mobile_devices.smart_phones', $mobileDevices['smart_phones'] ?? '') }}"></div>
            <div class="col-md-4"><label>Others (Tablets)</label><input type="text" name="mobile_devices[others]" class="form-control" value="{{ old('mobile_devices.others', $mobileDevices['others'] ?? '') }}"></div>
        </div>

        <!-- Data Subscriptions -->
        <div class="subsection-title">2.4 Data/Internet Service Subscriptions</div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark"><tr><th>Technology</th><th>Month 1</th><th>Month 2</th><th>Month 3</th></tr></thead>
                <tbody>
                    @php $techs = ['Data Enabled SIM cards', 'Fiber To The Home', 'Fiber To The Office', 'Fixed Wireless', 'Satellite']; @endphp
                    @foreach($techs as $tech)
                    @php $key = Str::slug($tech); @endphp
                    <tr>
                        <th>{{ $tech }}</th>
                        @foreach(['m1','m2','m3'] as $m)
                        <td><input type="text" name="data_subscriptions[{{ $key }}][{{ $m }}]" class="form-control form-control-sm" value="{{ old("data_subscriptions.$key.$m", $dataSubscriptions[$key][$m] ?? '') }}"></td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Cybersecurity -->
        <div class="section-title">11. CYBERSECURITY READINESS ASSESSMENT</div>

        <div class="mb-3">
            <label>11.1 Team/officer for cybersecurity incidents?</label>
            <div class="form-check form-check-inline">
                <input type="radio" name="cybersecurity[has_team]" value="yes" class="form-check-input" {{ old('cybersecurity.has_team', $cybersecurity['has_team'] ?? '') == 'yes' ? 'checked' : '' }}> Yes
                <input type="radio" name="cybersecurity[has_team]" value="no" class="form-check-input ms-3" {{ old('cybersecurity.has_team', $cybersecurity['has_team'] ?? '') == 'no' ? 'checked' : '' }}> No
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4"><label>Total Staff</label><input type="text" name="cybersecurity[staff_total]" class="form-control" value="{{ old('cybersecurity.staff_total', $cybersecurity['staff_total'] ?? '') }}"></div>
            <div class="col-md-4"><label>Male</label><input type="text" name="cybersecurity[staff_male]" class="form-control" value="{{ old('cybersecurity.staff_male', $cybersecurity['staff_male'] ?? '') }}"></div>
            <div class="col-md-4"><label>Female</label><input type="text" name="cybersecurity[staff_female]" class="form-control" value="{{ old('cybersecurity.staff_female', $cybersecurity['staff_female'] ?? '') }}"></div>
        </div>

        <div class="mb-3">
            <label>11.3 Tools/systems for cybersecurity?</label>
            <div class="form-check form-check-inline">
                <input type="radio" name="cybersecurity[has_tools]" value="yes" class="form-check-input" {{ old('cybersecurity.has_tools', $cybersecurity['has_tools'] ?? '') == 'yes' ? 'checked' : '' }}> Yes
                <input type="radio" name="cybersecurity[has_tools]" value="no" class="form-check-input ms-3" {{ old('cybersecurity.has_tools', $cybersecurity['has_tools'] ?? '') == 'no' ? 'checked' : '' }}> No
            </div>
        </div>

        <div class="mb-3"><label>Tools deployed</label><textarea name="cybersecurity[tools_deployed]" rows="2" class="form-control">{{ old('cybersecurity.tools_deployed', $cybersecurity['tools_deployed'] ?? '') }}</textarea></div>

        <div class="mb-3">
            <label>11.5 Cyber incident in last 12 months?</label>
            <div class="form-check form-check-inline">
                <input type="radio" name="cybersecurity[had_incident]" value="yes" class="form-check-input" {{ old('cybersecurity.had_incident', $cybersecurity['had_incident'] ?? '') == 'yes' ? 'checked' : '' }}> Yes
                <input type="radio" name="cybersecurity[had_incident]" value="no" class="form-check-input ms-3" {{ old('cybersecurity.had_incident', $cybersecurity['had_incident'] ?? '') == 'no' ? 'checked' : '' }}> No
            </div>
        </div>

        <div class="mb-3">
            <label>11.6 Type of incident</label>
            <div class="row">
                @php $incidentTypes = $cybersecurity['incident_types'] ?? []; @endphp
                <div class="col-md-3"><input type="checkbox" name="cybersecurity[incident_types][]" value="malware" {{ in_array('malware', $incidentTypes) ? 'checked' : '' }}> Malware</div>
                <div class="col-md-3"><input type="checkbox" name="cybersecurity[incident_types][]" value="ransomware" {{ in_array('ransomware', $incidentTypes) ? 'checked' : '' }}> Ransomware</div>
                <div class="col-md-3"><input type="checkbox" name="cybersecurity[incident_types][]" value="web_attack" {{ in_array('web_attack', $incidentTypes) ? 'checked' : '' }}> Web Attack</div>
                <div class="col-md-3"><input type="checkbox" name="cybersecurity[incident_types][]" value="impersonation" {{ in_array('impersonation', $incidentTypes) ? 'checked' : '' }}> Impersonation</div>
            </div>
        </div>

        <div class="mb-3">
            <label>11.9 Cyber awareness initiatives?</label>
            <div class="form-check form-check-inline">
                <input type="radio" name="cybersecurity[has_awareness]" value="yes" class="form-check-input" {{ old('cybersecurity.has_awareness', $cybersecurity['has_awareness'] ?? '') == 'yes' ? 'checked' : '' }}> Yes
                <input type="radio" name="cybersecurity[has_awareness]" value="no" class="form-check-input ms-3" {{ old('cybersecurity.has_awareness', $cybersecurity['has_awareness'] ?? '') == 'no' ? 'checked' : '' }}> No
            </div>
        </div>

        <div class="mb-4"><label>Awareness activities</label><textarea name="cybersecurity[awareness_activities]" rows="2" class="form-control">{{ old('cybersecurity.awareness_activities', $cybersecurity['awareness_activities'] ?? '') }}</textarea></div>

        <!-- PWD Compliance -->
        <div class="section-title">12. PWD COMPLIANCE (KS2952 Standard)</div>

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
        <div class="mb-4"><label>Future plans for inclusivity</label><textarea name="pwd_future_plans" rows="2" class="form-control">{{ old('pwd_future_plans', $return->pwd_future_plans) }}</textarea></div>

        <!-- Environmental -->
        <div class="section-title">13. ENVIRONMENTAL SUSTAINABILITY</div>
        <div class="mb-3"><label>E-waste initiatives</label><textarea name="ewaste_initiatives" rows="2" class="form-control">{{ old('ewaste_initiatives', $return->ewaste_initiatives) }}</textarea></div>
        <div class="mb-3"><label>Carbon reduction</label><textarea name="carbon_initiatives" rows="2" class="form-control">{{ old('carbon_initiatives', $return->carbon_initiatives) }}</textarea></div>
        <div class="mb-4"><label>EMCA compliance status</label><textarea name="emca_status" rows="2" class="form-control">{{ old('emca_status', $return->emca_status) }}</textarea></div>

        <!-- Comments -->
        <div class="section-title">14. COMMENTS/SUGGESTIONS</div>
        <div class="mb-4"><textarea name="comments" rows="3" class="form-control" placeholder="Share challenges and suggestions...">{{ old('comments', $return->comments) }}</textarea></div>

        <!-- Submitter Details -->
        <div class="section-title">DETAILS OF INDIVIDUAL SUBMITTING THE FORM</div>
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="submitter_name" class="form-control" value="{{ old('submitter_name', $return->submitter_name) }}" required>
            </div>
            <div class="col-md-3">
                <label>Title</label>
                <input type="text" name="submitter_title" class="form-control" value="{{ old('submitter_title', $return->submitter_title) }}">
            </div>
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="submitter_date" class="form-control" value="{{ old('submitter_date', $return->submitter_date ? date('Y-m-d', strtotime($return->submitter_date)) : '') }}">
            </div>
            <div class="col-md-3">
                <label>Company Stamp</label>
                <input type="file" name="company_stamp" class="form-control" accept="image/*,.pdf">
                @if($return->company_stamp_path)
                    <small class="text-muted existing-doc">Current file: <a href="{{ Storage::url($return->company_stamp_path) }}" target="_blank">View existing stamp</a></small>
                @endif
            </div>
        </div>

        <!-- Mandatory Documents -->
        <div class="section-title">MANDATORY DOCUMENTS (Quarter 4)</div>
        <div class="mb-3">
            <label>Shareholding Certificate</label>
            <input type="file" name="shareholding_cert" class="form-control">
            @if(isset($documents['shareholding_cert']))
                <small class="text-muted existing-doc">Current: <a href="{{ Storage::url($documents['shareholding_cert']) }}" target="_blank">View existing</a></small>
            @endif
        </div>
        <div class="mb-3">
            <label>Audited Financial Statements</label>
            <input type="file" name="audited_financials" class="form-control">
            @if(isset($documents['audited_financials']))
                <small class="text-muted existing-doc">Current: <a href="{{ Storage::url($documents['audited_financials']) }}" target="_blank">View existing</a></small>
            @endif
        </div>
        <div class="mb-3">
            <label>Tax Compliance Certificate</label>
            <input type="file" name="tax_compliance" class="form-control">
            @if(isset($documents['tax_compliance']))
                <small class="text-muted existing-doc">Current: <a href="{{ Storage::url($documents['tax_compliance']) }}" target="_blank">View existing</a></small>
            @endif
        </div>
        <div class="mb-4">
            <label>Tariff Structure</label>
            <input type="file" name="tariff_structure" class="form-control">
            @if(isset($documents['tariff_structure']))
                <small class="text-muted existing-doc">Current: <a href="{{ Storage::url($documents['tariff_structure']) }}" target="_blank">View existing</a></small>
            @endif
        </div>

        <!-- Official Use Only Section -->
        <div class="section-title" style="background: #8B0000;">FOR OFFICIAL USE ONLY - DO NOT FILL BELOW THIS LINE</div>
        <div class="alert alert-warning text-center mb-3">
            <strong>⚠ STATUTORY NOTICE:</strong> A COMPLIANCE CERTIFICATE WILL NOT BE ISSUED IF THE COMPLIANCE RETURNS ARE SUBMITTED LATE OR REJECTED BY THE AUTHORITY.
        </div>

        <div class="btn-submit-section text-center">
            <button type="submit" name="submit" value="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane"></i> Submit to CAK
            </button>
            <button type="submit" name="save_draft" value="1" class="btn btn-secondary btn-lg">
                <i class="fas fa-save"></i> Save Draft
            </button>
            <button type="button" class="btn btn-info btn-lg" onclick="generatePDF()">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
            <a href="{{ route('asp.show', $return->id) }}" class="btn btn-dark btn-lg">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    let m2mCounter = {{ count($m2mServices) + 1 }};

    function addM2MRow() {
        const tbody = document.getElementById('m2m_body');
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${m2mCounter}</td>
            <td><input type="text" name="m2m_services[${m2mCounter}][service]" class="form-control form-control-sm"></td>
            <td><input type="text" name="m2m_services[${m2mCounter}][description]" class="form-control form-control-sm"></td>
            <td><input type="text" name="m2m_services[${m2mCounter}][subscriptions]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
        `;
        m2mCounter++;
        attachRemoveEvents();
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('tr');
                if (row && row.parentElement.children.length > 1) {
                    row.remove();
                    renumberRows();
                } else if (row) {
                    alert('At least one row must remain.');
                }
            };
        });
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#m2m_body tr');
        rows.forEach((row, idx) => {
            row.cells[0].textContent = idx + 1;
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, '[' + idx + ']'));
                }
            });
        });
    }

    function generatePDF() {
        const element = document.querySelector('.form-container');
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: 'ASP_Compliance_Return_{{ $return->id }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, letterRendering: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
    }

    document.addEventListener('DOMContentLoaded', function() {
        attachRemoveEvents();
    });
</script>
@endpush
@endsection
