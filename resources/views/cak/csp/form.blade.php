@extends('layouts.app')

@section('title', 'CSP Compliance Return')
@section('page-title', 'Content Service Provider (CSP) Compliance Return')
<button type="button"
        class="btn btn-kp-warning btn-lg autofill-form-btn"
        data-url="{{ route('csp.autofill-record-2') }}"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Auto fill the form and make changes where necessary and save draft or submit">
    Auto Fill
</button>
@push('styles')
<style>
    .form-container { background:#fff; padding:25px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,.08); }
    .cak-header { text-align:center; border-bottom:2px solid #000; margin-bottom:20px; padding-bottom:15px; }
    .cak-header h4,.cak-header h5 { font-weight:700; margin-bottom:5px; }
    .instruction-box { border:1px solid #000; padding:12px 15px; margin-bottom:20px; font-size:12px; }
    .cak-section-title { background:#2c3e50; color:#fff; padding:8px 12px; margin:25px 0 12px; font-weight:bold; font-size:14px; text-transform:uppercase; }
    .cak-subtitle { font-weight:bold; font-size:13px; margin:15px 0 8px; color:#111; }
    .cak-form-table { width:100%; border-collapse:collapse; margin-bottom:15px; font-size:12px; }
    .cak-form-table th,.cak-form-table td { border:1.5px solid #000; padding:6px; vertical-align:middle; }
    .cak-form-table th { background:#f8f9fa; font-weight:700; }
    .cak-form-table input,.cak-form-table select,.cak-form-table textarea {
        width:100%; border:0; outline:none; box-shadow:none; font-size:12px; padding:4px; background:transparent;
    }
    .cak-form-table textarea { min-height:55px; resize:vertical; }
    .text-center { text-align:center; }
    .total-field { background:#f8f9fa !important; font-weight:bold; text-align:center; }
    .official-header {
        font-size:18px; font-weight:700; text-align:center; text-transform:uppercase;
        border-top:3px solid #000; border-bottom:1.5px solid #000; padding:10px 0; margin-top:35px;
    }
    .official-table th,.official-table td { height:70px; }
    .submit-buttons { margin-top:30px; padding-top:20px; border-top:2px solid #ddd; text-align:center; }
</style>
@endpush

@section('content')
@php
    $data = isset($record) ? ($record->form_data ?? []) : [];
    $oldValue = fn($key, $default = '') => old($key, data_get($data, $key, $default));
@endphp

<div class="form-container">

    <div class="cak-header">
        <div class="mb-2">
            <img src="{{ asset('images/cak.png') }}" alt="CAK Logo" style="max-height:80px;">
        </div>
        <h4>COMPLIANCE RETURN FORM</h4>
        <h5>CONTENT SERVICE PROVIDER (CSP)</h5>
        <p class="mb-0 small">
            PURSUANT TO THE PROVISIONS OF THE KENYA COMMUNICATIONS ACT 1998,
            KENYA COMMUNICATION REGULATIONS 2010 AND THE CSP LICENSE CONDITIONS
        </p>
    </div>

    <div class="instruction-box">
        <strong>Instructions</strong><br>
        i. Download the latest form from the Authority’s website at the end of each quarter.<br>
        ii. This form has provision for both quarterly and annual compliance reporting.<br>
        iii. Information is to be submitted within 15 days after the end of every quarter.<br>
        iv. Provide accurate information and explain fields where information is not available.<br>
        v. Where Nil returns are submitted, an explanation MUST be provided under Comments/Suggestions.<br>
        vi. Returns will only be accepted if the form is the latest version posted on the CA website.
    </div>

    <form method="POST"
          action="{{ isset($record) ? route('csp.update', $record->id) : route('csp.store') }}"
          enctype="multipart/form-data"
          id="cspForm">
        @csrf
        @isset($record)
            @method('PUT')
        @endisset

        {{-- 1. GENERAL INFORMATION --}}
        <div class="cak-section-title">1. General Information</div>

        <div class="cak-subtitle">1.1 License Details</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width:25%;">Name of Licensee <span class="text-danger">*</span></th>
                    <td colspan="3">
                        <input type="text" name="licensee_name" value="{{ old('licensee_name', $record->licensee_name ?? '') }}" required>
                        @error('licensee_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </td>
                </tr>
                <tr>
                    <th>License No.</th>
                    <td><input type="text" name="license_no" value="{{ old('license_no', $record->license_no ?? '') }}"></td>

                    <th>Other Licenses Held</th>
                    <td><input type="text" name="other_licenses" value="{{ old('other_licenses', $record->other_licenses ?? '') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.2 Period under Review</div>
       <table class="cak-form-table">
    <tbody>
        <tr>
            <th style="width:25%;">Financial Year <span class="text-danger">*</span></th>
            <td colspan="3">
                <select name="financial_year" id="financial_year" required>
                    <option value="">Select Financial Year</option>
                    @foreach(['2023/2024','2024/2025','2025/2026','2026/2027', '2027/2028', '2028/2029'] as $fy)
                        <option value="{{ $fy }}"
                            {{ old('financial_year', $defaultFinancialYear ?? $record->financial_year ?? '') == $fy ? 'selected' : '' }}>
                            {{ $fy }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted d-block">
                    <i class="fas fa-info-circle"></i>
                    Current Financial Year: <span id="display_financial_year"></span>
                </small>
            </td>
        </tr>
        <tr>
            <th>Quarter <span class="text-danger">*</span></th>
            <td colspan="3">
                <select name="quarter" id="quarter" required>
                    <option value="">Select Quarter</option>
                    <option value="Q1" {{ old('quarter', $defaultQuarter ?? $record->quarter ?? '') == 'Q1' ? 'selected' : '' }}>
                        Quarter 1 (1st July – 30th Sep)
                    </option>
                    <option value="Q2" {{ old('quarter', $defaultQuarter ?? $record->quarter ?? '') == 'Q2' ? 'selected' : '' }}>
                        Quarter 2 (1st Oct – 31st Dec)
                    </option>
                    <option value="Q3" {{ old('quarter', $defaultQuarter ?? $record->quarter ?? '') == 'Q3' ? 'selected' : '' }}>
                        Quarter 3 (1st Jan – 31st Mar)
                    </option>
                    <option value="Q4" {{ old('quarter', $defaultQuarter ?? $record->quarter ?? '') == 'Q4' ? 'selected' : '' }}>
                        Quarter 4 (1st Apr – 30th Jun)
                    </option>
                </select>
                <small class="text-muted d-block">
                    <i class="fas fa-info-circle"></i>
                    Current Quarter: <span id="display_quarter"></span>
                </small>
            </td>
        </tr>
    </tbody>
</table>

        <div class="cak-subtitle">1.3 Address</div>

        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>County</th>
                    <td><input type="text" name="county" value="{{ $oldValue('county') }}"></td>
                    <th>Town</th>
                    <td><input type="text" name="town" value="{{ $oldValue('town') }}"></td>
                    <th>Street/Road</th>
                    <td><input type="text" name="street_road" value="{{ $oldValue('street_road') }}"></td>
                </tr>
                <tr>
                    <th>Name of Building</th>
                    <td><input type="text" name="building_name" value="{{ $oldValue('building_name') }}"></td>
                    <th>Floor No.</th>
                    <td><input type="text" name="floor_no" value="{{ $oldValue('floor_no') }}"></td>
                    <th>Room No.</th>
                    <td><input type="text" name="room_no" value="{{ $oldValue('room_no') }}"></td>
                </tr>
                <tr>
                    <th>P.O. Box</th>
                    <td><input type="text" name="p_o_box" value="{{ $oldValue('p_o_box') }}"></td>
                    <th>Town</th>
                    <td><input type="text" name="postal_town" value="{{ $oldValue('postal_town') }}"></td>
                    <th>Code</th>
                    <td><input type="text" name="postal_code" value="{{ $oldValue('postal_code') }}"></td>
                </tr>
                <tr>
                    <th>Tel No.</th>
                    <td><input type="text" name="tel_no" value="{{ $oldValue('tel_no') }}"></td>
                    <th>Mobile No.</th>
                    <td><input type="text" name="mobile_no" value="{{ $oldValue('mobile_no') }}"></td>
                    <th>Other Tel. Nos.</th>
                    <td><input type="text" name="other_tel" value="{{ $oldValue('other_tel') }}"></td>
                </tr>
                <tr>
                    <th>Email Address</th>
                    <td colspan="2"><input type="email" name="email" value="{{ $oldValue('email') }}"></td>
                    <th>Web Address</th>
                    <td colspan="2"><input type="url" name="web_address" value="{{ $oldValue('web_address') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.4 Contact Details</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Name of Chief Executive Officer (CEO)</th>
                    <td colspan="3"><input type="text" name="ceo_name" value="{{ $oldValue('ceo_name') }}"></td>
                </tr>
                <tr>
                    <th>Name of Contact Person</th>
                    <td colspan="3"><input type="text" name="contact_person" value="{{ $oldValue('contact_person') }}"></td>
                </tr>
                <tr>
                    <th>Telephone Landline</th>
                    <td><input type="text" name="contact_landline" value="{{ $oldValue('contact_landline') }}"></td>
                    <th>Mobile</th>
                    <td><input type="text" name="contact_mobile" value="{{ $oldValue('contact_mobile') }}"></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td colspan="3"><input type="email" name="contact_email" value="{{ $oldValue('contact_email') }}"></td>
                </tr>
                <tr>
                    <th>Did any address information change during the quarter?</th>
                    <td colspan="3">
                        <label class="me-4"><input type="radio" name="address_changed" value="yes" {{ $oldValue('address_changed') == 'yes' ? 'checked' : '' }}> Yes</label>
                        <label><input type="radio" name="address_changed" value="no" {{ $oldValue('address_changed', 'no') == 'no' ? 'checked' : '' }}> No</label>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- PART A --}}
        <div class="cak-section-title">Part A: Quarterly Reporting Section</div>

        <div class="cak-section-title">2. Services</div>

        <div class="cak-subtitle">2.1 Services Provided to End Users Under the License</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Short Code</th>
                    <th>Service Provided</th>
                    <th>Company Name</th>
                    <th>Authorization by Content Control Agency & Validity Period</th>
                    <th>Charges (KES) per SMS / USSD Session</th>
                    <th>Month 1</th>
                    <th>Month 2</th>
                    <th>Month 3</th>
                    <th>Total No. of USSD Sessions / SMS</th>
                </tr>
            </thead>
            <tbody id="services_body">
                @for($i = 1; $i <= 10; $i++)
                <tr>
                    <td><input type="text" name="services[{{ $i }}][short_code]" value="{{ old("services.$i.short_code", data_get($data, "services.$i.short_code")) }}"></td>
                    <td><input type="text" name="services[{{ $i }}][service_provided]" value="{{ old("services.$i.service_provided", data_get($data, "services.$i.service_provided")) }}"></td>
                    <td><input type="text" name="services[{{ $i }}][company_name]" value="{{ old("services.$i.company_name", data_get($data, "services.$i.company_name")) }}"></td>
                    <td><textarea name="services[{{ $i }}][authorization]" rows="2">{{ old("services.$i.authorization", data_get($data, "services.$i.authorization")) }}</textarea></td>
                    <td><input type="number" step="0.01" min="0" name="services[{{ $i }}][charges]" value="{{ old("services.$i.charges", data_get($data, "services.$i.charges")) }}"></td>
                    <td><input type="number" min="0" name="services[{{ $i }}][m1]" class="service-month" value="{{ old("services.$i.m1", data_get($data, "services.$i.m1")) }}"></td>
                    <td><input type="number" min="0" name="services[{{ $i }}][m2]" class="service-month" value="{{ old("services.$i.m2", data_get($data, "services.$i.m2")) }}"></td>
                    <td><input type="number" min="0" name="services[{{ $i }}][m3]" class="service-month" value="{{ old("services.$i.m3", data_get($data, "services.$i.m3")) }}"></td>
                    <td><input type="text" name="services[{{ $i }}][total]" class="service-total total-field" readonly></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-subtitle">2.2 Money Transfer Service</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Indicator</th>
                    <th>1st Month in the Quarter</th>
                    <th>2nd Month in the Quarter</th>
                    <th>3rd Month in the Quarter</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $moneyRows = [
                        'active_agents' => 'Number of Active Agents',
                        'registered_active_subscriptions' => 'Number of Registered Active Subscriptions',
                        'c2b_value' => 'Value of Customer to Business - C2B Transfers (KES)',
                        'b2c_value' => 'Value of Business to Customer - B2C Transfers (KES)',
                        'b2b_value' => 'Value of Business to Business - B2B Transfers (KES)',
                        'g2c_value' => 'Value of Government to Citizen - G2C Transfers (KES)',
                        'c2g_value' => 'Value of Citizen to Government - C2G Transfers (KES)',
                        'volumes_sent_other_networks' => 'Volumes sent to other networks',
                        'volumes_received_other_networks' => 'Volumes received from other networks',
                        'value_sent_other_networks' => 'Value sent to other networks (KES)',
                        'value_received_other_networks' => 'Value received from other networks (KES)',
                        'p2p_volumes' => 'Volumes of P2P Transactions',
                        'p2p_received_other_networks' => 'Volumes received from other networks',
                        'p2p_value_sent_other_networks' => 'Value sent to other networks (Ksh.)',
                        'p2p_value_received_other_networks' => 'Value received from other networks (Ksh.)',
                    ];
                @endphp

                @foreach($moneyRows as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['m1','m2','m3'] as $m)
                    <td>
                        <input type="number" min="0" step="0.01"
                               name="money_transfer[{{ $key }}][{{ $m }}]"
                               value="{{ old("money_transfer.$key.$m", data_get($data, "money_transfer.$key.$m")) }}">
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">2.3 Numbering Resources</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Numbering Resource</th>
                    <th>Total Numbers Assigned</th>
                    <th>Numbers in Use</th>
                    <th>Numbers Not in Use</th>
                    <th>Reasons for Non Usage</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 7; $i++)
                <tr>
                    <td><input type="text" name="numbering_resources[{{ $i }}][resource]" value="{{ old("numbering_resources.$i.resource", data_get($data, "numbering_resources.$i.resource")) }}"></td>
                    <td><input type="number" min="0" name="numbering_resources[{{ $i }}][total_assigned]" value="{{ old("numbering_resources.$i.total_assigned", data_get($data, "numbering_resources.$i.total_assigned")) }}"></td>
                    <td><input type="number" min="0" name="numbering_resources[{{ $i }}][in_use]" value="{{ old("numbering_resources.$i.in_use", data_get($data, "numbering_resources.$i.in_use")) }}"></td>
                    <td><input type="number" min="0" name="numbering_resources[{{ $i }}][not_in_use]" value="{{ old("numbering_resources.$i.not_in_use", data_get($data, "numbering_resources.$i.not_in_use")) }}"></td>
                    <td><textarea name="numbering_resources[{{ $i }}][reason]" rows="2">{{ old("numbering_resources.$i.reason", data_get($data, "numbering_resources.$i.reason")) }}</textarea></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">3. Quality of Service</div>

        <div class="cak-subtitle">Complaints Resolution</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th rowspan="3">Complaint Type</th>
                    <th colspan="6">Number of Complaints During Quarter</th>
                </tr>
                <tr>
                    <th colspan="2">Month 1</th>
                    <th colspan="2">Month 2</th>
                    <th colspan="2">Month 3</th>
                </tr>
                <tr>
                    <th>Received</th><th>Resolved</th>
                    <th>Received</th><th>Resolved</th>
                    <th>Received</th><th>Resolved</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $complaints = [
                        'billing_charges' => 'Billing Charges',
                        'spamming' => 'Spamming',
                        'unsolicited_content' => 'Unsolicited Content',
                        'customer_care' => 'Customer Care Response Challenges',
                        'network_failures' => 'Network Failures and Service Inaccessibility',
                        'delays_onboarding' => 'Delays in Onboarding and Unsubscribing',
                        'others' => 'Others Please Specify',
                    ];
                @endphp

                @foreach($complaints as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['m1_received','m1_resolved','m2_received','m2_resolved','m3_received','m3_resolved'] as $field)
                    <td>
                        <input type="number" min="0"
                               name="complaints[{{ $key }}][{{ $field }}]"
                               class="complaint-input"
                               value="{{ old("complaints.$key.$field", data_get($data, "complaints.$key.$field")) }}">
                    </td>
                    @endforeach
                </tr>
                @endforeach

                <tr>
                    <th>TOTAL</th>
                    <td><input type="text" id="complaints_m1_received_total" class="total-field" readonly></td>
                    <td><input type="text" id="complaints_m1_resolved_total" class="total-field" readonly></td>
                    <td><input type="text" id="complaints_m2_received_total" class="total-field" readonly></td>
                    <td><input type="text" id="complaints_m2_resolved_total" class="total-field" readonly></td>
                    <td><input type="text" id="complaints_m3_received_total" class="total-field" readonly></td>
                    <td><input type="text" id="complaints_m3_resolved_total" class="total-field" readonly></td>
                </tr>
            </tbody>
        </table>

        {{-- PART B --}}
        <div class="cak-section-title">Part B: Annual Reporting Section</div>
        <p class="text-center"><strong>Information to be submitted at the end of the Quarter ending 30th June</strong></p>

        <div class="cak-section-title">1. Shareholding Information</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Current Certificate of Shareholding</th>
                    <td><input type="file" name="shareholding_cert"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">2. Financial Data</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Financial Year Start Date</th>
                    <td><input type="date" name="financial_year_start_date" value="{{ $oldValue('financial_year_start_date') }}"></td>
                    <th>End Date</th>
                    <td><input type="date" name="financial_year_end_date" value="{{ $oldValue('financial_year_end_date') }}"></td>
                </tr>
                <tr>
                    <th>Annual Audited Financial Statement</th>
                    <td colspan="3"><input type="file" name="audited_financials"></td>
                </tr>
                <tr>
                    <th>Valid Tax Compliance Certificate</th>
                    <td colspan="3"><input type="file" name="tax_compliance"></td>
                </tr>
                <tr>
                    <th>Certificate of Clearance from Kenya Copyright Board</th>
                    <td colspan="3"><input type="file" name="copyright_clearance"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">3. Compliance to Provision of Service and Facilities to Persons Living With Disability in Line With KS2952 Standard</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Aware of the Standard?</th>
                    <td>
                        <select name="pwd_aware">
                            <option value="">Select</option>
                            <option value="yes" {{ $oldValue('pwd_aware') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ $oldValue('pwd_aware') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Complied with the Standard?</th>
                    <td>
                        <select name="pwd_complied">
                            <option value="">Select</option>
                            <option value="yes" {{ $oldValue('pwd_complied') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ $oldValue('pwd_complied') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Standard Matrix Attachment</th>
                    <td><input type="file" name="pwd_standard_matrix"></td>
                </tr>
                <tr>
                    <th>If yes,give supporting reason</th>
                    <td><textarea name="pwd_reasons" rows="5">{{ $oldValue('pwd_reasons') }}</textarea></td>
                </tr>
                <tr>
                    <th>Actions taken to ensure accessibility to services and facilities by PWDs</th>
                    <td><textarea name="pwd_actions" rows="5">{{ $oldValue('pwd_actions') }}</textarea></td>
                </tr>
                <tr>
                    <th>Challenges or limitations faced in serving Persons Living With Disability</th>
                    <td><textarea name="pwd_challenges" rows="5">{{ $oldValue('pwd_challenges') }}</textarea></td>
                </tr>
                <tr>
                    <th>Future plans to enhance ICT inclusivity and accessibility for PWDs</th>
                    <td><textarea name="pwd_future_plans" rows="5">{{ $oldValue('pwd_future_plans') }}</textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">4. Environmental Sustainability Compliance</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>E-waste collection initiatives / take-back mechanisms</th>
                    <td><textarea name="ewaste_initiatives" rows="5">{{ $oldValue('ewaste_initiatives') }}</textarea></td>
                </tr>
                <tr>
                    <th>Carbon footprint reduction initiatives</th>
                    <td><textarea name="carbon_initiatives" rows="5">{{ $oldValue('carbon_initiatives') }}</textarea></td>
                </tr>
                <tr>
                    <th>Current status of adherence to EMCA Waste Management</th>
                    <td><textarea name="emca_status" rows="5">{{ $oldValue('emca_status') }}</textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">4. Comments / Suggestions</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <td>
                        <textarea name="comments" rows="5" placeholder="Please share any challenges faced and/or make suggestions to improve the communications regulatory environment.">{{ $oldValue('comments') }}</textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">Details of Individual Submitting the Form</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width:18%;">Signature</th>
                    <td style="width:55%;">
                        <input type="file" name="signature" id="signature" accept="image/png,image/jpeg,image/jpg">
                        <img id="signature_preview" src="" style="max-height:80px; margin-top:8px; display:none;">
                    </td>
                    <td rowspan="4" style="width:27%; text-align:center; vertical-align:bottom;">
                        <label style="display:block; font-weight:bold;">Company Stamp</label>
                        <input type="file" name="company_stamp" id="company_stamp" accept="image/png,image/jpeg,image/jpg">
                        <img id="stamp_preview" src="" style="max-height:100px; max-width:100%; margin-top:8px; display:none;">
                    </td>
                </tr>
                <tr>
                    <th>Name <span class="text-danger">*</span></th>
                    <td><input type="text" name="submitter_name" value="{{ $oldValue('submitter_name') }}" required></td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td><input type="text" name="submitter_title" value="{{ $oldValue('submitter_title') }}"></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><input type="date" name="submitter_date" value="{{ $oldValue('submitter_date', date('Y-m-d')) }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="text-center my-4">
            <strong>THANK YOU FOR COMPLETING THE FORM</strong>
        </div>

        <div class="official-header">
            FOR OFFICIAL USE ONLY - DO NOT FILL BELOW THIS LINE
        </div>

        <p><strong>These returns have been :)</strong></p>

        <table class="cak-form-table official-table">
            <thead>
                <tr>
                    <th style="width:12%;"></th>
                    <th style="width:28%;" class="text-center">Checked By:</th>
                    <th style="width:28%;" class="text-center">Verified by:</th>
                    <th style="width:32%;" class="text-center">
                        Approved ☐ &nbsp;&nbsp; Rejected ☐<br>
                        <small>Tick as appropriate</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr><th>Name</th><td></td><td></td><td></td></tr>
                <tr><th>Title</th><td></td><td></td><td></td></tr>
                <tr><th>Signature</th><td></td><td></td><td></td></tr>
                <tr><th>Date</th><td></td><td></td><td></td></tr>
            </tbody>
        </table>

        <p class="mt-3">
            <strong>
                A COMPLIANCE CERTIFICATE WILL NOT BE ISSUED IF THE COMPLIANCE RETURNS ARE
                SUBMITTED LATE OR REJECTED BY THE AUTHORITY
            </strong>
        </p>

        <div class="submit-buttons">
            <button type="submit" name="submit" value="submit" class="btn btn-kp-primary btn-lg">Submit</button>
<button type="button"
        class="btn btn-kp-warning btn-lg autofill-form-btn"
        data-url="{{ route('csp.autofill-record-2') }}">
    Auto Fill
</button>


         <button type="submit"
        name="save_draft"
        value="1"
        class="btn btn-secondary btn-lg"
        formnovalidate>
         Save Draft
        </button>
            <button type="button" class="btn btn-info btn-lg" onclick="window.print()">Print Preview</button>
            <a href="{{ route('csp.index') }}" class="btn btn-dark btn-lg">Back</a>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateServiceTotals() {
        document.querySelectorAll('#services_body tr').forEach(function (row) {
            const m1 = parseFloat(row.querySelector('[name*="[m1]"]')?.value || 0);
            const m2 = parseFloat(row.querySelector('[name*="[m2]"]')?.value || 0);
            const m3 = parseFloat(row.querySelector('[name*="[m3]"]')?.value || 0);

            const totalInput = row.querySelector('.service-total');
            if (totalInput) totalInput.value = m1 + m2 + m3;
        });
    }

    function calculateComplaintTotals() {
        const fields = [
            'm1_received', 'm1_resolved',
            'm2_received', 'm2_resolved',
            'm3_received', 'm3_resolved'
        ];

        fields.forEach(function (field) {
            let total = 0;

            document.querySelectorAll(`input[name*="[${field}]"]`).forEach(function (input) {
                total += parseInt(input.value || 0);
            });

            const target = document.getElementById(`complaints_${field}_total`);
            if (target) target.value = total;
        });
    }

    document.querySelectorAll('.service-month').forEach(input => input.addEventListener('input', calculateServiceTotals));
    document.querySelectorAll('.complaint-input').forEach(input => input.addEventListener('input', calculateComplaintTotals));

    calculateServiceTotals();
    calculateComplaintTotals();

    function previewImage(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (!input || !preview) return;

        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    previewImage('company_stamp', 'stamp_preview');
    previewImage('signature', 'signature_preview');
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function flattenObject(object, prefix = '') {
        const result = {};

        Object.entries(object || {}).forEach(([key, value]) => {
            const path = prefix ? `${prefix}[${key}]` : key;

            if (value !== null && typeof value === 'object' && !Array.isArray(value)) {
                Object.assign(result, flattenObject(value, path));
            } else {
                result[path] = value;
            }
        });

        return result;
    }

    function setField(name, value) {
        const fields = document.querySelectorAll(`[name="${CSS.escape(name)}"]`);

        fields.forEach(field => {
            if (field.type === 'file') return;

            if (field.type === 'radio') {
                field.checked = String(field.value) === String(value);
                return;
            }

            if (field.type === 'checkbox') {
                field.checked = Array.isArray(value)
                    ? value.map(String).includes(String(field.value))
                    : String(field.value) === String(value) || value === true;
                return;
            }

            field.value = value ?? '';
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    document.querySelectorAll('.autofill-form-btn').forEach(button => {
        button.addEventListener('click', async function () {
            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = 'Filling...';

            try {
                const response = await fetch(button.dataset.url, {
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) throw new Error('Failed to load saved data.');

                const data = await response.json();
                const flatData = flattenObject(data);

                Object.entries(flatData).forEach(([name, value]) => {
                    setField(name, value);
                });

                button.textContent = 'Auto Filled';
            } catch (error) {
                alert(error.message || 'Failed to auto-fill form.');
                button.textContent = originalText;
            } finally {
                button.disabled = false;
            }
        });
    });
});
</script>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display current info
    function updateDisplayInfo() {
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth();
        const currentYear = currentDate.getFullYear();

        // Calculate financial year
        let fyStart, fyEnd;
        if (currentMonth >= 6) { // July to December
            fyStart = currentYear;
            fyEnd = currentYear + 1;
        } else {
            fyStart = currentYear - 1;
            fyEnd = currentYear;
        }

        const financialYear = `${fyStart}/${fyEnd}`;
        document.getElementById('display_financial_year').innerHTML = `<strong>${financialYear}</strong>`;

        // Calculate quarter
        let quarter = '';
        if (currentMonth >= 6 && currentMonth <= 8) quarter = 'Q1 (Jul-Sep)';
        else if (currentMonth >= 9 && currentMonth <= 11) quarter = 'Q2 (Oct-Dec)';
        else if (currentMonth >= 0 && currentMonth <= 2) quarter = 'Q3 (Jan-Mar)';
        else if (currentMonth >= 3 && currentMonth <= 5) quarter = 'Q4 (Apr-Jun)';

        document.getElementById('display_quarter').innerHTML = `<strong>${quarter}</strong>`;
    }

    updateDisplayInfo();

    // Auto-select for new forms only
    const financialYearSelect = document.getElementById('financial_year');
    const quarterSelect = document.getElementById('quarter');

    // Only auto-select if no value is already selected (new form)
    if (financialYearSelect && !financialYearSelect.value) {
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth();
        const currentYear = currentDate.getFullYear();

        let fyStart, fyEnd;
        if (currentMonth >= 6) {
            fyStart = currentYear;
            fyEnd = currentYear + 1;
        } else {
            fyStart = currentYear - 1;
            fyEnd = currentYear;
        }

        const defaultFY = `${fyStart}/${fyEnd}`;

        // Check if option exists
        const option = Array.from(financialYearSelect.options).find(opt => opt.value === defaultFY);
        if (option) {
            financialYearSelect.value = defaultFY;
        }
    }

    if (quarterSelect && !quarterSelect.value) {
        const currentMonth = new Date().getMonth();
        let defaultQuarter = '';

        if (currentMonth >= 6 && currentMonth <= 8) defaultQuarter = 'Q1';
        else if (currentMonth >= 9 && currentMonth <= 11) defaultQuarter = 'Q2';
        else if (currentMonth >= 0 && currentMonth <= 2) defaultQuarter = 'Q3';
        else if (currentMonth >= 3 && currentMonth <= 5) defaultQuarter = 'Q4';

        if (defaultQuarter) {
            quarterSelect.value = defaultQuarter;
        }
    }
});
</script>
@endpush
