@extends('layouts.app')

@section('title', 'NFP Compliance Return - Network Facilities Provider')
@section('page-title', 'Network Facilities Provider (NFP) Compliance Return')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('nfp.index') }}">NFP Returns</a></li>
<li class="breadcrumb-item active">New Return</li>
@endsection

@push('styles')
<style>
    .form-container {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,.08);
    }

    .cak-header {
        text-align: center;
        border-bottom: 2px solid #000;
        margin-bottom: 20px;
        padding-bottom: 15px;
    }

    .cak-header h4,
    .cak-header h5 {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .instruction-box {
        border: 1px solid #000;
        padding: 12px 15px;
        margin-bottom: 20px;
        font-size: 12px;
        background: #fff;
    }

    .cak-section-title {
        background: #2c3e50;
        color: #fff;
        padding: 8px 12px;
        margin: 25px 0 12px;
        font-weight: bold;
        font-size: 14px;
        text-transform: uppercase;
    }

    .cak-subtitle {
        font-weight: bold;
        font-size: 13px;
        margin: 15px 0 8px;
        color: #111;
    }

    .cak-form-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 12px;
    }

    .cak-form-table th,
    .cak-form-table td {
        border: 1.5px solid #000;
        padding: 6px;
        vertical-align: middle;
    }

    .cak-form-table th {
        background: #f8f9fa;
        font-weight: 700;
    }

    .cak-form-table input,
    .cak-form-table select,
    .cak-form-table textarea {
        width: 100%;
        border: 0;
        outline: none;
        box-shadow: none;
        font-size: 12px;
        padding: 4px;
        background: transparent;
    }

    .cak-form-table textarea {
        min-height: 55px;
        resize: vertical;
    }

    .text-center {
        text-align: center;
    }

    .staff-input,
    .staff-total {
        text-align: center;
    }

    .official-header {
        font-size: 18px;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
        border-top: 3px solid #000;
        border-bottom: 1.5px solid #000;
        padding: 10px 0;
        margin-top: 35px;
    }

    .official-table th,
    .official-table td {
        height: 70px;
    }

    .submit-buttons {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #ddd;
        text-align: center;
    }

    @media print {
        .btn,
        .submit-buttons,
        .breadcrumb,
        .navbar,
        .sidebar {
            display: none !important;
        }

        .form-container {
            box-shadow: none;
            padding: 0;
        }

        .cak-section-title {
            background: #fff !important;
            color: #000 !important;
            border: 1.5px solid #000;
        }

        .cak-form-table th,
        .cak-form-table td {
            border: 1.5px solid #000 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="form-container">

    <div class="cak-header">
        <div class="mb-2">
            <img src="{{ asset('images/cak.png') }}" alt="CAK Logo" style="max-height: 80px;">
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
        i. Please note that the latest version of this form must be downloaded from the Authority’s website at the end of each quarter in order to capture any official amendments.<br>
        ii. This form has provision for both quarterly and annual compliance reporting.<br>
        iii. Information to be submitted within 15 days after the end of every Quarter.<br>
        iv. Please provide information in the space provided. You may insert additional rows and pages as required.<br>
        v. Please provide accurate information and fill all fields as required. Please provide explanation for fields where you may not have relevant information.<br>
        vi. Where Nil returns are submitted, an explanation MUST be provided under the Comments/Suggestions section.
    </div>

    <form method="POST" action="{{ route('nfp.store') }}" enctype="multipart/form-data" id="nfpForm">
        @csrf

        <div class="cak-section-title">1. General Information</div>

        <div class="cak-subtitle">1.1 Licence Details</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 25%;">Name of Licensee <span class="text-danger">*</span></th>
                    <td colspan="3">
                        <input type="text" name="licensee_name" value="{{ old('licensee_name') }}" required>
                        @error('licensee_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>License No.</th>
                    <td><input type="text" name="license_no" value="{{ old('license_no') }}"></td>
                    <th>Other Licenses Held</th>
                    <td><input type="text" name="other_licenses" value="{{ old('other_licenses') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.2 Period under review</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 25%;">Financial Year <span class="text-danger">*</span></th>
                    <td colspan="3">
                        <select name="financial_year" required>
                            <option value="">Select Financial Year</option>
                            @foreach(['2023/2024','2024/2025','2025/2026','2026/2027'] as $fy)
                                <option value="{{ $fy }}" {{ old('financial_year') == $fy ? 'selected' : '' }}>{{ $fy }}</option>
                            @endforeach
                        </select>
                        @error('financial_year')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>Quarter <span class="text-danger">*</span></th>
                    <td colspan="3">
                        <select name="quarter" required>
                            <option value="">Select Quarter</option>
                            <option value="Q1" {{ old('quarter') == 'Q1' ? 'selected' : '' }}>Quarter 1 (1st July – 30th Sep)</option>
                            <option value="Q2" {{ old('quarter') == 'Q2' ? 'selected' : '' }}>Quarter 2 (1st Oct – 31st Dec)</option>
                            <option value="Q3" {{ old('quarter') == 'Q3' ? 'selected' : '' }}>Quarter 3 (1st Jan – 31st Mar)</option>
                            <option value="Q4" {{ old('quarter') == 'Q4' ? 'selected' : '' }}>Quarter 4 (1st Apr – 30th Jun)</option>
                        </select>
                        @error('quarter')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3 Address</div>

        <div class="cak-subtitle">1.3.1 Physical Address</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>County</th>
                    <td><input type="text" name="county" value="{{ old('county') }}"></td>
                    <th>Town</th>
                    <td><input type="text" name="town" value="{{ old('town') }}"></td>
                    <th>Street/Road</th>
                    <td><input type="text" name="street_road" value="{{ old('street_road') }}"></td>
                </tr>
                <tr>
                    <th>Name of Building</th>
                    <td><input type="text" name="building_name" value="{{ old('building_name') }}"></td>
                    <th>Floor No.</th>
                    <td><input type="text" name="floor_no" value="{{ old('floor_no') }}"></td>
                    <th>Room No.</th>
                    <td><input type="text" name="room_no" value="{{ old('room_no') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.2 Postal Address</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>P.O. Box</th>
                    <td><input type="text" name="p_o_box" value="{{ old('p_o_box') }}"></td>
                    <th>Town</th>
                    <td><input type="text" name="postal_town" value="{{ old('postal_town') }}"></td>
                    <th>Code</th>
                    <td><input type="text" name="postal_code" value="{{ old('postal_code') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.3 Telephone Contacts</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Tel No.</th>
                    <td><input type="text" name="tel_no" value="{{ old('tel_no') }}"></td>
                    <th>Mobile No.</th>
                    <td><input type="text" name="mobile_no" value="{{ old('mobile_no') }}"></td>
                </tr>
                <tr>
                    <th>Other Tel. Nos.</th>
                    <td colspan="3"><input type="text" name="other_tel" value="{{ old('other_tel') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.4 Email and Web Address</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 25%;">Email Address</th>
                    <td><input type="email" name="email" value="{{ old('email') }}"></td>
                </tr>
                <tr>
                    <th>Web Address</th>
                    <td><input type="url" name="web_address" value="{{ old('web_address') }}"></td>
                </tr>
            </tbody>
        </table>

        {{-- After the email/web address section --}}
<div class="cak-subtitle">1.3.4 Email and Web Address</div>
<table class="cak-form-table">
    <tbody>
        <tr>
            <th style="width: 25%;">Email Address</th>
            <td><input type="email" name="email" value="{{ old('email') }}"></td>
        </tr>
        <tr>
            <th>Web Address</th>
            <td><input type="url" name="web_address" value="{{ old('web_address') }}"></td>
        </tr>
    </tbody>
</table>

{{-- Include the network location partial --}}
@include('cak.nfp.partials.network-location')

        <div class="cak-subtitle">1.4 Contact Details</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 30%;">Name of Chief Executive Officer (CEO)</th>
                    <td colspan="3"><input type="text" name="ceo_name" value="{{ old('ceo_name') }}"></td>
                </tr>
                <tr>
                    <th>Name of Contact Person</th>
                    <td colspan="3"><input type="text" name="contact_person" value="{{ old('contact_person') }}"></td>
                </tr>
                <tr>
                    <th>Telephone Landline</th>
                    <td><input type="text" name="contact_landline" value="{{ old('contact_landline') }}"></td>
                    <th>Mobile</th>
                    <td><input type="text" name="contact_mobile" value="{{ old('contact_mobile') }}"></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td colspan="3"><input type="email" name="contact_email" value="{{ old('contact_email') }}"></td>
                </tr>
                <tr>
                    <th>Did any address information change during the quarter?</th>
                    <td colspan="3">
                        <label class="me-4">
                            <input type="radio" name="address_changed" value="yes" {{ old('address_changed') == 'yes' ? 'checked' : '' }}> YES
                        </label>
                        <label>
                            <input type="radio" name="address_changed" value="no" {{ old('address_changed', 'no') == 'no' ? 'checked' : '' }}> NO
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">Part A: Quarterly Reporting Section</div>

        <div class="cak-section-title">2. Types of Infrastructure Deployed Under the License</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No.</th>
                    <th style="width: 32%;">Type of Infrastructure</th>
                    <th style="width: 60%;">Brief Description</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 6; $i++)
                    <tr>
                        <td class="text-center">{{ $i }}</td>
                        <td>
                            <select name="infrastructure[{{ $i }}][type]">
                                <option value="">Select</option>
                                <option value="Fibre Optic Cable">Fibre Optic Cable</option>
                                <option value="Telecommunication Mast/Tower">Telecommunication Mast/Tower</option>
                                <option value="Data Centre">Data Centre</option>
                                <option value="Microwave Link">Microwave Link</option>
                                <option value="Base Station">Base Station</option>
                                <option value="Satellite Infrastructure">Satellite Infrastructure</option>
                                <option value="Other">Other</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="infrastructure[{{ $i }}][description]" rows="2">{{ old("infrastructure.$i.description") }}</textarea>
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
                    <th style="width: 35%;">Resource</th>
                    <th style="width: 35%;">Primary Number Assignments by CA</th>
                    <th style="width: 30%;">Utilized Numbers</th>
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
                        <td><input type="number" min="0" name="primary_numbers[{{ $key }}][assigned]" value="{{ old("primary_numbers.$key.assigned") }}"></td>
                        <td><input type="number" min="0" name="primary_numbers[{{ $key }}][utilized]" value="{{ old("primary_numbers.$key.utilized") }}"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">3.2 Secondary Number Assignment</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 24%;">Name of the CSP</th>
                    <th style="width: 25%;">Shortcode/USSD Code(s) Assigned</th>
                    <th style="width: 25%;">Tariff/Rate (Ksh)</th>
                    <th style="width: 20%;">Volume</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 5; $i++)
                    <tr>
                        <td class="text-center">{{ $i }}</td>
                        <td><input type="text" name="secondary_numbers[{{ $i }}][csp_name]" value="{{ old("secondary_numbers.$i.csp_name") }}"></td>
                        <td><input type="text" name="secondary_numbers[{{ $i }}][shortcode]" value="{{ old("secondary_numbers.$i.shortcode") }}"></td>
                        <td><input type="number" step="0.01" min="0" name="secondary_numbers[{{ $i }}][tariff]" value="{{ old("secondary_numbers.$i.tariff") }}"></td>
                        <td><input type="number" min="0" name="secondary_numbers[{{ $i }}][volume]" value="{{ old("secondary_numbers.$i.volume") }}"></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-subtitle">3.3 Bulk SMS</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 34%;">Name of the CSP</th>
                    <th style="width: 30%;">Tariff/Rate (Ksh)</th>
                    <th style="width: 30%;">Volume</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 5; $i++)
                    <tr>
                        <td class="text-center">{{ $i }}</td>
                        <td><input type="text" name="bulk_sms[{{ $i }}][csp_name]" value="{{ old("bulk_sms.$i.csp_name") }}"></td>
                        <td><input type="number" step="0.01" min="0" name="bulk_sms[{{ $i }}][tariff]" value="{{ old("bulk_sms.$i.tariff") }}"></td>
                        <td><input type="number" min="0" name="bulk_sms[{{ $i }}][volume]" value="{{ old("bulk_sms.$i.volume") }}"></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">Part B: Annual Reporting Section</div>

        <div class="cak-section-title">4. Mandatory Documents</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 40%;">Current Certificate of Shareholding</th>
                    <td><input type="file" name="shareholding_cert"></td>
                </tr>
                <tr>
                    <th>Audited Financial Statements</th>
                    <td><input type="file" name="audited_financials"></td>
                </tr>
                <tr>
                    <th>Valid Tax Compliance Certificate</th>
                    <td><input type="file" name="tax_compliance"></td>
                </tr>
                <tr>
                    <th>Tariff Structure</th>
                    <td><input type="file" name="tariff_structure"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">5. Systems Capacities</div>
        <div class="cak-subtitle">5.1 Broadband Infrastructure</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width: 28%;">Type of Broadband Infrastructure</th>
                    <th style="width: 32%;">Infrastructure Ownership/Host</th>
                    <th style="width: 20%;">Capacity Owned/Leased (Gbps)</th>
                    <th style="width: 20%;">Utilized Capacity (Gbps)</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 6; $i++)
                    <tr>
                        <td><input type="text" name="broadband[{{ $i }}][type]" value="{{ old("broadband.$i.type") }}"></td>
                        <td><input type="text" name="broadband[{{ $i }}][ownership]" value="{{ old("broadband.$i.ownership") }}"></td>
                        <td><input type="number" step="0.01" min="0" name="broadband[{{ $i }}][capacity_owned]" value="{{ old("broadband.$i.capacity_owned") }}"></td>
                        <td><input type="number" step="0.01" min="0" name="broadband[{{ $i }}][capacity_utilized]" value="{{ old("broadband.$i.capacity_utilized") }}"></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">6. Staff</div>
        <table class="cak-form-table staff-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 28%;">Staff Category</th>
                    <th colspan="2">Local (Kenyan Citizens)</th>
                    <th colspan="2">Expatriates</th>
                    <th rowspan="2" style="width: 10%;">Total</th>
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
                        'Technical - Permanent' => 'tech_perm',
                        'Technical - Contract' => 'tech_cont',
                        'Technical - Temporary' => 'tech_temp',
                        'Non-Technical - Permanent' => 'nontech_perm',
                        'Non-Technical - Contract' => 'nontech_cont',
                        'Non-Technical - Temporary' => 'nontech_temp',
                    ];
                @endphp

                @foreach($staffCats as $label => $key)
                    <tr>
                        <th>{{ $label }}</th>
                        <td><input type="number" min="0" name="staff[{{ $key }}][local_m]" class="staff-input" value="{{ old("staff.$key.local_m") }}"></td>
                        <td><input type="number" min="0" name="staff[{{ $key }}][local_f]" class="staff-input" value="{{ old("staff.$key.local_f") }}"></td>
                        <td><input type="number" min="0" name="staff[{{ $key }}][exp_m]" class="staff-input" value="{{ old("staff.$key.exp_m") }}"></td>
                        <td><input type="number" min="0" name="staff[{{ $key }}][exp_f]" class="staff-input" value="{{ old("staff.$key.exp_f") }}"></td>
                        <td><input type="text" class="staff-total" readonly></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th><input type="text" id="total_local_m" readonly></th>
                    <th><input type="text" id="total_local_f" readonly></th>
                    <th><input type="text" id="total_exp_m" readonly></th>
                    <th><input type="text" id="total_exp_f" readonly></th>
                    <th><input type="text" id="grand_total" readonly></th>
                </tr>
            </tfoot>
        </table>

        <div class="cak-section-title">7. Compliance to Provision of Service and Facilities to Persons Living With Disability in Line With KS2952 Standard</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 40%;">Aware of the KS2952 Standard?</th>
                    <td>
                        <select name="pwd_aware">
                            <option value="">Select</option>
                            <option value="Yes" {{ old('pwd_aware') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('pwd_aware') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Complied with the standard?</th>
                    <td>
                        <select name="pwd_complied">
                            <option value="">Select</option>
                            <option value="Yes" {{ old('pwd_complied') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('pwd_complied') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Standard Matrix Attachment</th>
                    <td><input type="file" name="pwd_standard_matrix"></td>
                </tr>
                <tr>
                    <th>Actions taken to ensure accessibility</th>
                    <td><textarea name="pwd_actions" rows="5">{{ old('pwd_actions') }}</textarea></td>
                </tr>
                <tr>
                    <th>Challenges or limitations faced</th>
                    <td><textarea name="pwd_challenges" rows="5">{{ old('pwd_challenges') }}</textarea></td>
                </tr>
                <tr>
                    <th>Future plans to enhance ICT inclusivity and accessibility</th>
                    <td><textarea name="pwd_future_plans" rows="5">{{ old('pwd_future_plans') }}</textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">8. Environmental Sustainability Compliance</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 35%;">E-waste collection initiatives / take-back mechanisms</th>
                    <td><textarea name="ewaste_initiatives" rows="5">{{ old('ewaste_initiatives') }}</textarea></td>
                </tr>
                <tr>
                    <th>Carbon footprint reduction initiatives</th>
                    <td><textarea name="carbon_initiatives" rows="5">{{ old('carbon_initiatives') }}</textarea></td>
                </tr>
                <tr>
                    <th>Current status of adherence to EMCA Waste Management</th>
                    <td><textarea name="emca_status" rows="5">{{ old('emca_status') }}</textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">9. Comments / Suggestions</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <td>
                        <textarea name="comments" rows="5" placeholder="Please share any challenges faced and/or make suggestions to improve the regulatory environment.">{{ old('comments') }}</textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">Details of Individual Submitting the Form</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 18%;">Name <span class="text-danger">*</span></th>
                    <td style="width: 55%;"><input type="text" name="submitter_name" value="{{ old('submitter_name') }}" required></td>
                    <td rowspan="4" style="width: 27%; text-align:center; vertical-align:bottom;">
                        <label style="display:block; font-weight:bold;">Company Stamp</label>
                        <input type="file" name="company_stamp" id="company_stamp" accept="image/png,image/jpeg,image/jpg">
                        <img id="stamp_preview" src="" style="max-height:100px; max-width:100%; margin-top:8px; display:none;">
                    </td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td><input type="text" name="submitter_title" value="{{ old('submitter_title') }}"></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><input type="date" name="submitter_date" value="{{ old('submitter_date', date('Y-m-d')) }}"></td>
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

        <p><strong>These returns have been:</strong></p>

        <table class="cak-form-table official-table">
            <thead>
                <tr>
                    <th style="width: 12%;"></th>
                    <th style="width: 28%;" class="text-center">Checked By:</th>
                    <th style="width: 28%;" class="text-center">Verified by:</th>
                    <th style="width: 32%;" class="text-center">
                        Approved ☐ &nbsp;&nbsp; Rejected ☐<br>
                        <small>(Tick as appropriate)</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Name</th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Signature</th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="submit-buttons">
            <button type="submit" name="submit" value="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane"></i> Submit to CAK
            </button>

            <button type="submit" name="save_draft" value="1" class="btn btn-secondary btn-lg">
                <i class="fas fa-save"></i> Save Draft
            </button>

            <button type="button" class="btn btn-info btn-lg" onclick="window.print()">
                <i class="fas fa-print"></i> Print Preview
            </button>

            <button type="reset" class="btn btn-danger btn-lg">
                <i class="fas fa-eraser"></i> Clear Form
            </button>

            <a href="{{ route('nfp.index') }}" class="btn btn-dark btn-lg">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateStaffTotals() {
        let totalLocalM = 0;
        let totalLocalF = 0;
        let totalExpM = 0;
        let totalExpF = 0;
        let grandTotal = 0;

        document.querySelectorAll('.staff-table tbody tr').forEach(function (row) {
            const localM = parseInt(row.querySelector('[name*="[local_m]"]')?.value || 0);
            const localF = parseInt(row.querySelector('[name*="[local_f]"]')?.value || 0);
            const expM = parseInt(row.querySelector('[name*="[exp_m]"]')?.value || 0);
            const expF = parseInt(row.querySelector('[name*="[exp_f]"]')?.value || 0);

            const rowTotal = localM + localF + expM + expF;

            const totalInput = row.querySelector('.staff-total');
            if (totalInput) totalInput.value = rowTotal;

            totalLocalM += localM;
            totalLocalF += localF;
            totalExpM += expM;
            totalExpF += expF;
            grandTotal += rowTotal;
        });

        document.getElementById('total_local_m').value = totalLocalM;
        document.getElementById('total_local_f').value = totalLocalF;
        document.getElementById('total_exp_m').value = totalExpM;
        document.getElementById('total_exp_f').value = totalExpF;
        document.getElementById('grand_total').value = grandTotal;
    }

    document.querySelectorAll('.staff-input').forEach(function (input) {
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
