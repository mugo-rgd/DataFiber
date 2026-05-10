@extends('layouts.app')

@section('title', 'NFP Compliance Return')
@section('page-title', 'Network Facilities Provider (NFP) Compliance Return')

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
        <h5>NETWORK FACILITIES PROVIDER</h5>
        <p class="mb-0 small">
            PURSUANT TO THE PROVISIONS OF THE KICA 1998 AND THE KICA AMENDMENT ACT, 2013,
            AND THE KENYA INFORMATION AND COMMUNICATIONS REGULATIONS AND THE LICENSE CONDITIONS
        </p>
    </div>

    <div class="instruction-box">
        <strong>Instructions</strong><br>
        i. Download the latest form from the Authority’s website at the end of each quarter.<br>
        ii. This form has provision for both quarterly and annual compliance reporting.<br>
        iii. Information is to be submitted within 15 days after the end of every quarter.<br>
        iv. Provide information in the spaces provided. Additional rows/pages may be inserted where required.<br>
        v. Provide accurate information and explain fields where information is not available.<br>
        vi. Where Nil returns are submitted, an explanation MUST be provided under Comments/Suggestions.
    </div>

    <form method="POST"
          action="{{ isset($record) ? route('nfp.update', $record->id) : route('nfp.store') }}"
          enctype="multipart/form-data"
          id="nfpForm">
        @csrf
        @isset($record)
            @method('PUT')
        @endisset

        <div class="cak-section-title">1. General Information</div>

        <div class="cak-subtitle">1.1 Licence Details</div>
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
                        <select name="financial_year" required>
                            <option value="">Select Financial Year</option>
                            @foreach(['2023/2024','2024/2025','2025/2026','2026/2027'] as $fy)
                                <option value="{{ $fy }}" {{ old('financial_year', $record->financial_year ?? '') == $fy ? 'selected' : '' }}>
                                    {{ $fy }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Quarter <span class="text-danger">*</span></th>
                    <td colspan="3">
                        <select name="quarter" required>
                            <option value="">Select Quarter</option>
                            <option value="Q1" {{ old('quarter', $record->quarter ?? '') == 'Q1' ? 'selected' : '' }}>Quarter 1 (1st July – 30th Sep)</option>
                            <option value="Q2" {{ old('quarter', $record->quarter ?? '') == 'Q2' ? 'selected' : '' }}>Quarter 2 (1st Oct – 31st Dec)</option>
                            <option value="Q3" {{ old('quarter', $record->quarter ?? '') == 'Q3' ? 'selected' : '' }}>Quarter 3 (1st Jan – 31st Mar)</option>
                            <option value="Q4" {{ old('quarter', $record->quarter ?? '') == 'Q4' ? 'selected' : '' }}>Quarter 4 (1st Apr – 30th Jun)</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.1 Physical Address</div>
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
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.2 Postal Address</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>P.O. Box</th>
                    <td><input type="text" name="p_o_box" value="{{ $oldValue('p_o_box') }}"></td>
                    <th>Town</th>
                    <td><input type="text" name="postal_town" value="{{ $oldValue('postal_town') }}"></td>
                    <th>Code</th>
                    <td><input type="text" name="postal_code" value="{{ $oldValue('postal_code') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.3 Telephone Contacts</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Tel No.</th>
                    <td><input type="text" name="tel_no" value="{{ $oldValue('tel_no') }}"></td>
                    <th>Mobile No.</th>
                    <td><input type="text" name="mobile_no" value="{{ $oldValue('mobile_no') }}"></td>
                </tr>
                <tr>
                    <th>Other Tel. Nos.</th>
                    <td colspan="3"><input type="text" name="other_tel" value="{{ $oldValue('other_tel') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.4 Email and Web Address</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Email Address</th>
                    <td><input type="email" name="email" value="{{ $oldValue('email') }}"></td>
                </tr>
                <tr>
                    <th>Web Address</th>
                    <td><input type="url" name="web_address" value="{{ $oldValue('web_address') }}"></td>
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

        <div class="cak-section-title">Part A: Quarterly Reporting Section</div>

        <div class="cak-section-title">2. Types of Infrastructure Deployed Under the License</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width:8%;">No.</th>
                    <th style="width:32%;">Type of Infrastructure</th>
                    <th style="width:60%;">Brief Description</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 6; $i++)
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td>
                        <select name="infrastructure[{{ $i }}][type]">
                            <option value="">Select</option>
                            <option value="Fibre Optic Cable" {{ old("infrastructure.$i.type", data_get($data, "infrastructure.$i.type")) == 'Fibre Optic Cable' ? 'selected' : '' }}>Fibre Optic Cable</option>
                            <option value="Telecommunication Mast/Tower" {{ old("infrastructure.$i.type", data_get($data, "infrastructure.$i.type")) == 'Telecommunication Mast/Tower' ? 'selected' : '' }}>Telecommunication Mast/Tower</option>
                            <option value="Data Centre" {{ old("infrastructure.$i.type", data_get($data, "infrastructure.$i.type")) == 'Data Centre' ? 'selected' : '' }}>Data Centre</option>
                            <option value="Microwave Link" {{ old("infrastructure.$i.type", data_get($data, "infrastructure.$i.type")) == 'Microwave Link' ? 'selected' : '' }}>Microwave Link</option>
                            <option value="Base Station" {{ old("infrastructure.$i.type", data_get($data, "infrastructure.$i.type")) == 'Base Station' ? 'selected' : '' }}>Base Station</option>
                            <option value="Satellite Infrastructure" {{ old("infrastructure.$i.type", data_get($data, "infrastructure.$i.type")) == 'Satellite Infrastructure' ? 'selected' : '' }}>Satellite Infrastructure</option>
                            <option value="Other" {{ old("infrastructure.$i.type", data_get($data, "infrastructure.$i.type")) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </td>
                    <td>
                        <textarea name="infrastructure[{{ $i }}][description]" rows="2">{{ old("infrastructure.$i.description", data_get($data, "infrastructure.$i.description")) }}</textarea>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">3. Number Utilization</div>

        <div class="cak-subtitle">3.1 Primary Number Assignments Utilization (NFP-T1 Only)</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Resource</th>
                    <th>Primary Number Assignments by CA</th>
                    <th>Utilized Numbers</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $primaryResources = [
                        'short_codes' => 'Short Codes',
                        'ussd_codes' => 'USSD Codes',
                        'premium_rate_numbers' => 'Premium Rate Numbers',
                        'toll_free_numbers' => 'Toll Free Numbers',
                    ];
                @endphp

                @foreach($primaryResources as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    <td><input type="number" min="0" name="primary_numbers[{{ $key }}][assigned]" value="{{ old("primary_numbers.$key.assigned", data_get($data, "primary_numbers.$key.assigned")) }}"></td>
                    <td><input type="number" min="0" name="primary_numbers[{{ $key }}][utilized]" value="{{ old("primary_numbers.$key.utilized", data_get($data, "primary_numbers.$key.utilized")) }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">3.2 Secondary Number Assignment</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width:6%;">No.</th>
                    <th>Name of the CSP</th>
                    <th>Shortcode / USSD Code(s) Assigned</th>
                    <th>Tariff / Rate (Ksh)</th>
                    <th>Volume</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 5; $i++)
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td><input type="text" name="secondary_numbers[{{ $i }}][csp_name]" value="{{ old("secondary_numbers.$i.csp_name", data_get($data, "secondary_numbers.$i.csp_name")) }}"></td>
                    <td><input type="text" name="secondary_numbers[{{ $i }}][shortcode_ussd]" value="{{ old("secondary_numbers.$i.shortcode_ussd", data_get($data, "secondary_numbers.$i.shortcode_ussd")) }}"></td>
                    <td><input type="number" step="0.01" min="0" name="secondary_numbers[{{ $i }}][tariff]" value="{{ old("secondary_numbers.$i.tariff", data_get($data, "secondary_numbers.$i.tariff")) }}"></td>
                    <td><input type="number" min="0" name="secondary_numbers[{{ $i }}][volume]" value="{{ old("secondary_numbers.$i.volume", data_get($data, "secondary_numbers.$i.volume")) }}"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-subtitle">3.3 Bulk SMS</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width:6%;">No.</th>
                    <th>Name of the CSP</th>
                    <th>Tariff / Rate (Ksh)</th>
                    <th>Volume</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 5; $i++)
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td><input type="text" name="bulk_sms[{{ $i }}][csp_name]" value="{{ old("bulk_sms.$i.csp_name", data_get($data, "bulk_sms.$i.csp_name")) }}"></td>
                    <td><input type="number" step="0.01" min="0" name="bulk_sms[{{ $i }}][tariff]" value="{{ old("bulk_sms.$i.tariff", data_get($data, "bulk_sms.$i.tariff")) }}"></td>
                    <td><input type="number" min="0" name="bulk_sms[{{ $i }}][volume]" value="{{ old("bulk_sms.$i.volume", data_get($data, "bulk_sms.$i.volume")) }}"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">Part B: Annual Reporting Section</div>

        <div class="cak-section-title">4. Mandatory Documents</div>
        <table class="cak-form-table">
            <tbody>
                <tr><th>Current Certificate of Shareholding</th><td><input type="file" name="shareholding_cert"></td></tr>
                <tr><th>Audited Financial Statements for Preceding Year</th><td><input type="file" name="audited_financials"></td></tr>
                <tr><th>Valid Tax Compliance Certificate</th><td><input type="file" name="tax_compliance"></td></tr>
                <tr><th>Tariff Structure</th><td><input type="file" name="tariff_structure"></td></tr>
            </tbody>
        </table>

        <div class="cak-section-title">5. Systems Capacities</div>

        <div class="cak-subtitle">5.1 Broadband Infrastructure</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Type of Broadband Infrastructure</th>
                    <th>Infrastructure Ownership / Host</th>
                    <th>Capacity Owned / Leased (Gbps)</th>
                    <th>Utilized Capacity (Gbps)</th>
                    <th>Shape File Attachment (.shp)</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 6; $i++)
                <tr>
                    <td><input type="text" name="broadband_infrastructure[{{ $i }}][type]" value="{{ old("broadband_infrastructure.$i.type", data_get($data, "broadband_infrastructure.$i.type")) }}"></td>
                    <td><input type="text" name="broadband_infrastructure[{{ $i }}][ownership]" value="{{ old("broadband_infrastructure.$i.ownership", data_get($data, "broadband_infrastructure.$i.ownership")) }}"></td>
                    <td><input type="number" step="0.01" min="0" name="broadband_infrastructure[{{ $i }}][capacity_owned]" value="{{ old("broadband_infrastructure.$i.capacity_owned", data_get($data, "broadband_infrastructure.$i.capacity_owned")) }}"></td>
                    <td><input type="number" step="0.01" min="0" name="broadband_infrastructure[{{ $i }}][capacity_utilized]" value="{{ old("broadband_infrastructure.$i.capacity_utilized", data_get($data, "broadband_infrastructure.$i.capacity_utilized")) }}"></td>
                    <td><input type="file" name="broadband_infrastructure_shape_file_{{ $i }}"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">6. Staff</div>
        <table class="cak-form-table staff-table">
            <thead>
                <tr>
                    <th rowspan="2">Staff Category</th>
                    <th colspan="2">Local (Kenyan Citizens)</th>
                    <th colspan="2">Expatriates</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Male</th>
                    <th>Female</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $staffCats = [
                        'technical_permanent' => 'Technical - Permanent',
                        'technical_contract' => 'Technical - Contract',
                        'technical_temporary' => 'Technical - Temporary',
                        'non_technical_permanent' => 'Non Technical - Permanent',
                        'non_technical_contract' => 'Non Technical - Contract',
                        'non_technical_temporary' => 'Non Technical - Temporary',
                    ];
                @endphp

                @foreach($staffCats as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['local_m','local_f','exp_m','exp_f'] as $field)
                    <td>
                        <input type="number" min="0"
                               name="staff[{{ $key }}][{{ $field }}]"
                               class="staff-input"
                               value="{{ old("staff.$key.$field", data_get($data, "staff.$key.$field")) }}">
                    </td>
                    @endforeach
                    <td><input type="text" class="staff-row-total total-field" readonly></td>
                </tr>
                @endforeach

                <tr>
                    <th>Total</th>
                    <td><input type="text" id="staff_total_local_m" class="total-field" readonly></td>
                    <td><input type="text" id="staff_total_local_f" class="total-field" readonly></td>
                    <td><input type="text" id="staff_total_exp_m" class="total-field" readonly></td>
                    <td><input type="text" id="staff_total_exp_f" class="total-field" readonly></td>
                    <td><input type="text" id="staff_grand_total" class="total-field" readonly></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">7. Compliance to Provision of Service and Facilities to Persons Living With Disability in Line With KS2952 Standard</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Aware of KS2952 Standard?</th>
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

        <div class="cak-section-title">8. Environmental Sustainability Compliance</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>E-waste collection initiatives / take-back mechanisms</th>
                    <td><textarea name="ewaste_initiatives" rows="5">{{ $oldValue('ewaste_initiatives') }}</textarea></td>
                </tr>
                <tr>
                    <th>Carbon footprint / environmental impact reduction initiatives</th>
                    <td><textarea name="carbon_initiatives" rows="5">{{ $oldValue('carbon_initiatives') }}</textarea></td>
                </tr>
                <tr>
                    <th>Current status of adherence to EMCA Waste Management</th>
                    <td><textarea name="emca_status" rows="5">{{ $oldValue('emca_status') }}</textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">9. Comments / Suggestions</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <td>
                        <textarea name="comments" rows="5" placeholder="Please share any challenges faced and/or make suggestions to improve the regulatory environment.">{{ $oldValue('comments') }}</textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">Details of Individual Submitting the Form</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width:18%;">Name <span class="text-danger">*</span></th>
                    <td style="width:55%;"><input type="text" name="submitter_name" value="{{ $oldValue('submitter_name') }}" required></td>
                    <td rowspan="4" style="width:27%; text-align:center; vertical-align:bottom;">
                        <label style="display:block; font-weight:bold;">Company Stamp</label>
                        <input type="file" name="company_stamp" id="company_stamp" accept="image/png,image/jpeg,image/jpg">
                        <img id="stamp_preview" src="" style="max-height:100px; max-width:100%; margin-top:8px; display:none;">
                    </td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td><input type="text" name="submitter_title" value="{{ $oldValue('submitter_title') }}"></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><input type="date" name="submitter_date" value="{{ $oldValue('submitter_date', date('Y-m-d')) }}"></td>
                </tr>
                <tr>
                    <th>Signature</th>
                    <td>
                        <input type="file" name="signature" id="signature" accept="image/png,image/jpeg,image/jpg">
                        <img id="signature_preview" src="" style="max-height:80px; margin-top:8px; display:none;">
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="text-center my-4">
            <strong>THANK YOU FOR COMPLETING THIS FORM</strong>
        </div>

        <div class="official-header">
            FOR OFFICIAL USE ONLY – DO NOT FILL BELOW THIS LINE
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
                        <small>(Tick as appropriate)</small>
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

        <div class="submit-buttons">
            <button type="submit" name="submit" value="submit" class="btn btn-primary btn-lg">Submit</button>
           <button type="submit"
        name="save_draft"
        value="1"
        class="btn btn-secondary btn-lg"
        formnovalidate>
         Save Draft
        </button>
            <button type="button" class="btn btn-info btn-lg" onclick="window.print()">Print Preview</button>
            <a href="{{ route('nfp.index') }}" class="btn btn-dark btn-lg">Back</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateStaffTotals() {
        let localM = 0;
        let localF = 0;
        let expM = 0;
        let expF = 0;
        let grand = 0;

        document.querySelectorAll('.staff-table tbody tr').forEach(function (row) {
            const lm = parseInt(row.querySelector('[name*="[local_m]"]')?.value || 0);
            const lf = parseInt(row.querySelector('[name*="[local_f]"]')?.value || 0);
            const em = parseInt(row.querySelector('[name*="[exp_m]"]')?.value || 0);
            const ef = parseInt(row.querySelector('[name*="[exp_f]"]')?.value || 0);

            if (!row.querySelector('[name*="[local_m]"]')) return;

            const rowTotal = lm + lf + em + ef;
            const totalInput = row.querySelector('.staff-row-total');

            if (totalInput) totalInput.value = rowTotal;

            localM += lm;
            localF += lf;
            expM += em;
            expF += ef;
            grand += rowTotal;
        });

        const totals = {
            staff_total_local_m: localM,
            staff_total_local_f: localF,
            staff_total_exp_m: expM,
            staff_total_exp_f: expF,
            staff_grand_total: grand
        };

        Object.entries(totals).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.value = value;
        });
    }

    document.querySelectorAll('.staff-input').forEach(input => {
        input.addEventListener('input', calculateStaffTotals);
    });

    calculateStaffTotals();

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
