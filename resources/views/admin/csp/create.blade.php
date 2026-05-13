@extends('layouts.app')

@section('title', 'CSP Compliance Return - Content Service Provider')
@section('page-title', 'Content Service Provider (CSP) Compliance Return')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('csp.index') }}">CSP Returns</a></li>
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

    .total-field {
        background: #f8f9fa !important;
        font-weight: bold;
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
        <h5>CONTENT SERVICE PROVIDER (CSP)</h5>

        <p class="mb-0 small">
            PURSUANT TO THE PROVISIONS OF THE KENYA COMMUNICATIONS ACT 1998,
            KENYA COMMUNICATION REGULATIONS 2010 AND THE CSP LICENSE CONDITIONS
        </p>
    </div>

    <div class="instruction-box">
        <strong>Instructions</strong><br>
        i. Please note that the latest version of this form must be downloaded from the Authority’s website at the end of each quarter in order to capture any official amendments.<br>
        ii. This form has provision for both quarterly and annual compliance reporting.<br>
        iii. Information to be submitted within 15 days after the end of every Quarter.<br>
        iv. Please provide information in the space provided. You may insert additional rows and pages as required.<br>
        v. Please provide accurate information and fill all fields as required. Please provide explanation for fields where you may not have relevant information.<br>
        vi. Where Nil returns are submitted, an explanation MUST be provided under the Comments/Suggestions section.<br>
        vii. The returns will only be accepted if the form is the most up to date as posted on the CA website.
    </div>

    <form method="POST" action="{{ route('csp.store') }}" enctype="multipart/form-data" id="cspForm">
        @csrf

        {{-- 1. GENERAL INFORMATION --}}
        <div class="cak-section-title">1. General Information</div>

        <div class="cak-subtitle">1.1 License Details</div>

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
                    <td>
                        <input type="text" name="license_no" value="{{ old('license_no') }}">
                    </td>

                    <th>Other Licenses Held</th>
                    <td>
                        <input type="text" name="other_licenses" value="{{ old('other_licenses') }}">
                    </td>
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
                                <option value="{{ $fy }}" {{ old('financial_year') == $fy ? 'selected' : '' }}>
                                    {{ $fy }}
                                </option>
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

        <div class="cak-subtitle">Physical Address</div>
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

        <div class="cak-subtitle">Postal Address</div>
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

        <div class="cak-subtitle">Telephone Contacts</div>
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
                    <td colspan="3">
                        <input type="text" name="other_tel" value="{{ old('other_tel') }}">
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">Email and Web Address</div>
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
                            <input type="radio" name="address_changed" value="yes" {{ old('address_changed') == 'yes' ? 'checked' : '' }}>
                            YES
                        </label>

                        <label>
                            <input type="radio" name="address_changed" value="no" {{ old('address_changed', 'no') == 'no' ? 'checked' : '' }}>
                            NO
                        </label>
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
                    <th style="width: 10%;">Short Code</th>
                    <th style="width: 14%;">Service Provided</th>
                    <th style="width: 14%;">Company Name</th>
                    <th style="width: 16%;">Authorization by Content Control Agency & Validity Period</th>
                    <th style="width: 12%;">Charges (KES) Per SMS / USSD Session</th>
                    <th style="width: 8%;">Month 1</th>
                    <th style="width: 8%;">Month 2</th>
                    <th style="width: 8%;">Month 3</th>
                    <th style="width: 10%;">Total No. of USSD Sessions / SMS</th>
                </tr>
            </thead>

            <tbody id="services_body">
                @for($i = 1; $i <= 10; $i++)
                    <tr>
                        <td><input type="text" name="services[{{ $i }}][shortcode]" value="{{ old("services.$i.shortcode") }}"></td>
                        <td><input type="text" name="services[{{ $i }}][service]" value="{{ old("services.$i.service") }}"></td>
                        <td><input type="text" name="services[{{ $i }}][company]" value="{{ old("services.$i.company") }}"></td>
                        <td><textarea name="services[{{ $i }}][authorization]" rows="2">{{ old("services.$i.authorization") }}</textarea></td>
                        <td><input type="number" min="0" step="0.01" name="services[{{ $i }}][charges]" value="{{ old("services.$i.charges") }}"></td>
                        <td><input type="number" min="0" name="services[{{ $i }}][month1]" class="service-month" data-row="{{ $i }}" value="{{ old("services.$i.month1") }}"></td>
                        <td><input type="number" min="0" name="services[{{ $i }}][month2]" class="service-month" data-row="{{ $i }}" value="{{ old("services.$i.month2") }}"></td>
                        <td><input type="number" min="0" name="services[{{ $i }}][month3]" class="service-month" data-row="{{ $i }}" value="{{ old("services.$i.month3") }}"></td>
                        <td><input type="text" name="services[{{ $i }}][total]" class="service-total total-field" readonly></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-subtitle">2.2 Money Transfer Service</div>

        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Indicator</th>
                    <th>1st Month in the Quarter</th>
                    <th>2nd Month in the Quarter</th>
                    <th>3rd Month in the Quarter</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $moneyIndicators = [
                        'active_agents' => 'Number of Active Agents',
                        'active_subscriptions' => 'Number of Registered Active Subscriptions',
                        'c2b' => 'Value of Customer to Business - C2B Transfers (KES)',
                        'b2c' => 'Value of Business to Customer - B2C Transfers (KES)',
                        'b2b' => 'Value of Business to Business - B2B Transfers (KES)',
                        'g2c' => 'Value of Government to Citizen - G2C Transfers (KES)',
                        'c2g' => 'Value of Citizen to Government - C2G Transfers (KES)',
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

                @foreach($moneyIndicators as $key => $label)
                    <tr>
                        <th>{{ $label }}</th>
                        <td><input type="number" min="0" step="0.01" name="money_transfer[{{ $key }}][m1]" value="{{ old("money_transfer.$key.m1") }}"></td>
                        <td><input type="number" min="0" step="0.01" name="money_transfer[{{ $key }}][m2]" value="{{ old("money_transfer.$key.m2") }}"></td>
                        <td><input type="number" min="0" step="0.01" name="money_transfer[{{ $key }}][m3]" value="{{ old("money_transfer.$key.m3") }}"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">2.3 Numbering Resources</div>

        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width: 24%;">Numbering Resource<br><small>(Short Codes, USSD Codes, Premium Call Numbers)</small></th>
                    <th style="width: 18%;">Total Numbers Assigned</th>
                    <th style="width: 18%;">Numbers in Use</th>
                    <th style="width: 18%;">Numbers Not in Use</th>
                    <th style="width: 22%;">Reasons for Non Usage</th>
                </tr>
            </thead>

            <tbody>
                @for($i = 1; $i <= 7; $i++)
                    <tr>
                        <td><input type="text" name="numbering[{{ $i }}][resource]" value="{{ old("numbering.$i.resource") }}"></td>
                        <td><input type="number" min="0" name="numbering[{{ $i }}][total]" value="{{ old("numbering.$i.total") }}"></td>
                        <td><input type="number" min="0" name="numbering[{{ $i }}][in_use]" value="{{ old("numbering.$i.in_use") }}"></td>
                        <td><input type="number" min="0" name="numbering[{{ $i }}][not_in_use]" value="{{ old("numbering.$i.not_in_use") }}"></td>
                        <td><textarea name="numbering[{{ $i }}][reasons]" rows="2">{{ old("numbering.$i.reasons") }}</textarea></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">3. Quality of Service</div>

        <div class="cak-subtitle">Complaints Resolution</div>

        <table class="cak-form-table">
            <thead>
                <tr>
                    <th rowspan="3" style="width: 25%;">Complaint Type</th>
                    <th colspan="6">Number of Complaints During Quarter</th>
                </tr>
                <tr>
                    <th colspan="2">Month 1</th>
                    <th colspan="2">Month 2</th>
                    <th colspan="2">Month 3</th>
                </tr>
                <tr>
                    <th>Received</th>
                    <th>Resolved</th>
                    <th>Received</th>
                    <th>Resolved</th>
                    <th>Received</th>
                    <th>Resolved</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $complaints = [
                        'billing_charges' => 'Billing Charges',
                        'spamming_unsolicited_content' => 'Spamming / Unsolicited Content',
                        'customer_care_response' => 'Customer Care Response Challenges',
                        'network_failures' => 'Network Failures and Service Inaccessibility',
                        'onboarding_delays' => 'Delays in Onboarding and Unsubscribing',
                        'others' => 'Others Please Specify',
                    ];
                @endphp

                @foreach($complaints as $key => $label)
                    <tr>
                        <th>{{ $label }}</th>
                        <td><input type="number" min="0" name="complaints[{{ $key }}][m1_received]" class="complaint-input" value="{{ old("complaints.$key.m1_received") }}"></td>
                        <td><input type="number" min="0" name="complaints[{{ $key }}][m1_resolved]" class="complaint-input" value="{{ old("complaints.$key.m1_resolved") }}"></td>
                        <td><input type="number" min="0" name="complaints[{{ $key }}][m2_received]" class="complaint-input" value="{{ old("complaints.$key.m2_received") }}"></td>
                        <td><input type="number" min="0" name="complaints[{{ $key }}][m2_resolved]" class="complaint-input" value="{{ old("complaints.$key.m2_resolved") }}"></td>
                        <td><input type="number" min="0" name="complaints[{{ $key }}][m3_received]" class="complaint-input" value="{{ old("complaints.$key.m3_received") }}"></td>
                        <td><input type="number" min="0" name="complaints[{{ $key }}][m3_resolved]" class="complaint-input" value="{{ old("complaints.$key.m3_resolved") }}"></td>
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
        <p class="text-center"><strong>(Information to be submitted at the end of the Quarter ending 30th June)</strong></p>

        <div class="cak-section-title">1. Shareholding Information</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 40%;">Current Certificate of Shareholding<br><small>(Issued not more than 3 months)</small></th>
                    <td><input type="file" name="shareholding_cert"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">2. Financial Data</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 25%;">Financial Year Start Date</th>
                    <td><input type="date" name="fy_start" value="{{ old('fy_start') }}"></td>

                    <th style="width: 25%;">End Date</th>
                    <td><input type="date" name="fy_end" value="{{ old('fy_end') }}"></td>
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

        <div class="cak-section-title">
            3. Compliance to Provision of Service and Facilities to Persons Living With Disability in Line With KS2952 Standard
        </div>

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
                    <th>Complied with the Standard?</th>
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
                    <th>Actions taken to ensure accessibility to services and facilities by PWDs</th>
                    <td><textarea name="pwd_actions" rows="5">{{ old('pwd_actions') }}</textarea></td>
                </tr>

                <tr>
                    <th>Challenges or limitations faced in serving Persons Living With Disability</th>
                    <td><textarea name="pwd_challenges" rows="5">{{ old('pwd_challenges') }}</textarea></td>
                </tr>

                <tr>
                    <th>Future plans to enhance ICT inclusivity and accessibility for PWDs</th>
                    <td><textarea name="pwd_future_plans" rows="5">{{ old('pwd_future_plans') }}</textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">4. Environmental Sustainability Compliance</div>

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

        <div class="cak-section-title">4. Comments / Suggestions</div>

        <table class="cak-form-table">
            <tbody>
                <tr>
                    <td>
                        <textarea name="comments" rows="5" placeholder="Please share any challenges faced and/or make suggestions to improve the communications regulatory environment.">{{ old('comments') }}</textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">Details of Individual Submitting the Form</div>

        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width: 18%;">Signature</th>
                    <td style="width: 55%;">
                        <input type="file" name="signature" id="signature" accept="image/png,image/jpeg,image/jpg">
                        <img id="signature_preview" src="" style="max-height:80px; margin-top:8px; display:none;">
                    </td>

                    <td rowspan="4" style="width: 27%; text-align:center; vertical-align:bottom;">
                        <label style="display:block; font-weight:bold;">Company Stamp</label>
                        <input type="file" name="company_stamp" id="company_stamp" accept="image/png,image/jpeg,image/jpg">
                        <img id="stamp_preview" src="" style="max-height:100px; max-width:100%; margin-top:8px; display:none;">
                    </td>
                </tr>

                <tr>
                    <th>Name <span class="text-danger">*</span></th>
                    <td><input type="text" name="submitter_name" value="{{ old('submitter_name') }}" required></td>
                </tr>

                <tr>
                    <th>Title</th>
                    <td><input type="text" name="submitter_title" value="{{ old('submitter_title') }}"></td>
                </tr>

                <tr>
                    <th>Date</th>
                    <td><input type="date" name="submitter_date" value="{{ old('submitter_date', date('Y-m-d')) }}"></td>
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
                    <th style="width: 12%;"></th>
                    <th style="width: 28%;" class="text-center">Checked By:</th>
                    <th style="width: 28%;" class="text-center">Verified by:</th>
                    <th style="width: 32%;" class="text-center">
                        Approved O or Rejected O<br>
                        <small>Tick as appropriate</small>
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

        <p class="mt-3">
            <strong>
                MB A COMPLIANCE CERTIFICATE WILL NOT BE ISSUED IF THE COMPLIANCE RETURNS ARE
                SUBMITTED LATE OR REJECTED BY THE AUTHORITY
            </strong>
        </p>

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

            <a href="{{ route('csp.index') }}" class="btn btn-dark btn-lg">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateServiceTotals() {
        document.querySelectorAll('#services_body tr').forEach(function (row) {
            const m1 = parseFloat(row.querySelector('[name*="[month1]"]')?.value || 0);
            const m2 = parseFloat(row.querySelector('[name*="[month2]"]')?.value || 0);
            const m3 = parseFloat(row.querySelector('[name*="[month3]"]')?.value || 0);

            const total = row.querySelector('.service-total');
            if (total) total.value = m1 + m2 + m3;
        });
    }

    function calculateComplaintTotals() {
        const fields = [
            'm1_received',
            'm1_resolved',
            'm2_received',
            'm2_resolved',
            'm3_received',
            'm3_resolved'
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

    document.querySelectorAll('.service-month').forEach(function (input) {
        input.addEventListener('input', calculateServiceTotals);
    });

    document.querySelectorAll('.complaint-input').forEach(function (input) {
        input.addEventListener('input', calculateComplaintTotals);
    });

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
