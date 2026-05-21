@extends('layouts.app')

@section('title', 'ASP Compliance Return - Application Service Provider')
@section('page-title', 'Application Service Provider (ASP) Compliance Return')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('asp.index') }}">ASP Returns</a></li>
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

    .text-center { text-align: center; }

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
            <img src="{{ asset('images/cak.png') }}" alt="CAK Logo" style="max-height:80px;">
        </div>
        <h4>COMPLIANCE RETURN FORM</h4>
        <h5>APPLICATION SERVICE PROVIDER (ASP)</h5>
        <p class="mb-0 small">
            PURSUANT TO THE PROVISIONS OF THE KENYA COMMUNICATIONS ACT 1998,
            KENYA COMMUNICATION REGULATIONS 2010 AND THE ASP LICENSE CONDITIONS
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

    <form method="POST" action="{{ route('asp.store') }}" enctype="multipart/form-data" id="aspForm">
        @csrf

        <div class="cak-section-title">1. General Information</div>

        <div class="cak-subtitle">1.1 Licence Details</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th style="width:25%;">Name of Licensee <span class="text-danger">*</span></th>
                    <td colspan="3">
                        <input type="text" name="licensee_name" value="{{ old('licensee_name') }}" required>
                        @error('licensee_name') <div class="text-danger small">{{ $message }}</div> @enderror
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
                    <th style="width:25%;">Financial Year <span class="text-danger">*</span></th>
                    <td colspan="3">
                        <select name="financial_year" required>
                            <option value="">Select Financial Year</option>
                            @foreach(['2023/2024','2024/2025','2025/2026','2026/2027'] as $fy)
                                <option value="{{ $fy }}" {{ old('financial_year') == $fy ? 'selected' : '' }}>{{ $fy }}</option>
                            @endforeach
                        </select>
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
                    <th style="width:25%;">Email Address</th>
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
                    <th style="width:30%;">Name of Chief Executive Officer (CEO)</th>
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

        <div class="cak-section-title">2. Services Provided Under the License</div>

        <div class="cak-subtitle">2.1 Machine to Machine Services (e.g. Car tracking, IOT, etc)</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th style="width:7%;">No.</th>
                    <th style="width:28%;">Service Provided</th>
                    <th style="width:45%;">Brief Description</th>
                    <th style="width:20%;">Number of Subscriptions</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 10; $i++)
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td><input type="text" name="m2m_services[{{ $i }}][service]" value="{{ old("m2m_services.$i.service") }}"></td>
                    <td><textarea name="m2m_services[{{ $i }}][description]" rows="2">{{ old("m2m_services.$i.description") }}</textarea></td>
                    <td><input type="number" min="0" name="m2m_services[{{ $i }}][subscriptions]" value="{{ old("m2m_services.$i.subscriptions") }}"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-subtitle">2.2 Telecommunications Service Subscriptions</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th colspan="2" rowspan="2">Category of Subscriptions</th>
                    <th colspan="3">Number of Registered Active Subscriptions</th>
                </tr>
                <tr>
                    <th>Month 1</th>
                    <th>Month 2</th>
                    <th>Month 3</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $telecomRows = [
                        ['Postpaid Services', 'GSM (SIM Cards)', 'postpaid_gsm'],
                        ['Postpaid Services', 'Terrestrial Fixed Line', 'postpaid_fixed_line'],
                        ['Postpaid Services', 'Terrestrial Fixed Wireless', 'postpaid_fixed_wireless'],
                        ['Prepaid Services', 'GSM (SIM Cards)', 'prepaid_gsm'],
                        ['Prepaid Services', 'Terrestrial Fixed Line', 'prepaid_fixed_line'],
                        ['Prepaid Services', 'Terrestrial Fixed Wireless', 'prepaid_fixed_wireless'],
                        ['Voice over Internet Protocol (VoIP)', 'Mobile', 'voip_mobile'],
                        ['Voice over Internet Protocol (VoIP)', 'Fixed', 'voip_fixed'],
                        ['Leased Lines', 'Mobile', 'leased_mobile'],
                        ['Leased Lines', 'Fixed', 'leased_fixed'],
                    ];
                @endphp
                @foreach($telecomRows as $row)
                <tr>
                    <th>{{ $row[0] }}</th>
                    <th>{{ $row[1] }}</th>
                    @foreach(['m1','m2','m3'] as $m)
                    <td><input type="number" min="0" name="subscriptions[{{ $row[2] }}][{{ $m }}]" value="{{ old("subscriptions.{$row[2]}.$m") }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">2.3 Number of Mobile Phone Devices</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Type of Device</th>
                    <th>Number of Devices</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Feature Phone</th>
                    <td><input type="number" min="0" name="mobile_devices[feature_phone]" value="{{ old('mobile_devices.feature_phone') }}"></td>
                </tr>
                <tr>
                    <th>Smart Phone</th>
                    <td><input type="number" min="0" name="mobile_devices[smart_phone]" value="{{ old('mobile_devices.smart_phone') }}"></td>
                </tr>
                <tr>
                    <th>Others (e.g. Tablets)</th>
                    <td><input type="number" min="0" name="mobile_devices[others]" value="{{ old('mobile_devices.others') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">2.4 Data/Internet Service Subscriptions (Retail Customers) By Technology</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Subscriptions by Technology</th>
                    <th>Month 1</th>
                    <th>Month 2</th>
                    <th>Month 3</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dataTechs = [
                        'data_enabled_sim' => 'Data Enabled SIM cards',
                        'ftth' => 'Fiber To The Home',
                        'ftto' => 'Fiber To The Office',
                        'fixed_wireless' => 'Terrestrial Fixed Wireless e.g. WiMax, WiFi',
                        'satellite' => 'Satellite',
                        'copper' => 'Copper Line (Dial-up & DSL, xDSL)',
                        'cable_modem' => 'Cable Modem',
                        'other_fixed' => 'Other Fixed, Please Specify',
                    ];
                @endphp
                @foreach($dataTechs as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['m1','m2','m3'] as $m)
                    <td><input type="number" min="0" name="data_subscriptions[{{ $key }}][{{ $m }}]" value="{{ old("data_subscriptions.$key.$m") }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">2.5 Broadband Service Subscriptions (Retail Customers)</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th rowspan="2">Subscriptions by Technology</th>
                    <th colspan="3">Active Broadband Subscriptions</th>
                    <th rowspan="2">Data Volumes Consumed During the Quarter (GB)</th>
                </tr>
                <tr>
                    <th>Month 1</th>
                    <th>Month 2</th>
                    <th>Month 3</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $broadbandTechs = [
                        '3g' => '3G',
                        '4g' => '4G',
                        '5g' => '5G',
                        'ftth' => 'Fiber To The Home',
                        'ftto' => 'Fiber To The Office',
                        'fixed_wireless' => 'Terrestrial Fixed Wireless (WiMax/WiFi)',
                        'satellite' => 'Satellite',
                        'copper' => 'Copper Line (Dial-up & DSL, xDSL)',
                        'cable_modem' => 'Cable Modem',
                        'other_fixed' => 'Other Fixed, Please Specify',
                    ];
                @endphp
                @foreach($broadbandTechs as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['m1','m2','m3'] as $m)
                    <td><input type="number" min="0" name="broadband[{{ $key }}][{{ $m }}]" value="{{ old("broadband.$key.$m") }}"></td>
                    @endforeach
                    <td><input type="number" min="0" step="0.01" name="broadband[{{ $key }}][volume]" value="{{ old("broadband.$key.volume") }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">2.6 Fixed Data Subscriptions by Speed by Technology</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Technology</th>
                    <th>&lt; 256 Kbps</th>
                    <th>&gt;= 256 Kbps &lt; 2 Mbps</th>
                    <th>&gt;= 2 Mbps &lt; 10 Mbps</th>
                    <th>&gt;= 10 Mbps &lt; 30 Mbps</th>
                    <th>&gt;= 30 Mbps &lt; 100 Mbps</th>
                    <th>&gt; 100 Mbps &lt;= 1 Gbps</th>
                    <th>&gt; 1 Gbps</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $speedTechs = [
                        'ftth' => 'Fiber To The Home',
                        'ftto' => 'Fiber To The Office',
                        'fixed_wireless' => 'Terrestrial Fixed Wireless (WiMax/WiFi)',
                        'satellite' => 'Satellite',
                        'copper' => 'Copper Line (Dial-up & DSL, xDSL)',
                        'cable_modem' => 'Cable Modem',
                        'other_fixed' => 'Other Fixed, Please Specify',
                        'total' => 'Total',
                    ];
                    $speedBands = ['lt_256','kbps_256_2mbps','mbps_2_10','mbps_10_30','mbps_30_100','mbps_100_1gbps','gt_1gbps'];
                @endphp
                @foreach($speedTechs as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach($speedBands as $band)
                    <td><input type="number" min="0" name="speed_subscriptions[{{ $key }}][{{ $band }}]" value="{{ old("speed_subscriptions.$key.$band") }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-section-title">3. Mobile Number Portability</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Operator</th>
                    <th>Number of In-Ports</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 4; $i++)
                <tr>
                    <td><input type="text" name="mnp[{{ $i }}][operator]" value="{{ old("mnp.$i.operator") }}"></td>
                    <td><input type="number" min="0" name="mnp[{{ $i }}][in_ports]" value="{{ old("mnp.$i.in_ports") }}"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">4. Traffic for Telephone Services (Voice & SMS)</div>

        <div class="cak-subtitle">4.1 Local Voice Traffic</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Network</th>
                    <th>Service</th>
                    <th>Voice In Minutes</th>
                    <th>Voice Out Minutes</th>
                    <th>Calls In</th>
                    <th>Calls Out</th>
                    <th>VoIP In Minutes</th>
                    <th>VoIP Out Minutes</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $voiceRows = [
                        'intra_mobile' => ['Intra-Network', 'Mobile'],
                        'intra_fixed_wireless' => ['Intra-Network', 'Fixed Wireless'],
                        'intra_fixed_line' => ['Intra-Network', 'Fixed Line'],
                    ];
                    for ($n = 1; $n <= 4; $n++) {
                        $voiceRows["other_{$n}_mobile"] = ["Other Network $n", 'Mobile'];
                        $voiceRows["other_{$n}_fixed_line"] = ["Other Network $n", 'Fixed Line'];
                        $voiceRows["other_{$n}_fixed_wireless"] = ["Other Network $n", 'Fixed Wireless'];
                    }
                @endphp
                @foreach($voiceRows as $key => $labels)
                <tr>
                    <th>{{ $labels[0] }}</th>
                    <th>{{ $labels[1] }}</th>
                    @foreach(['voice_in','voice_out','calls_in','calls_out','voip_in','voip_out'] as $field)
                    <td><input type="number" min="0" name="voice_traffic[{{ $key }}][{{ $field }}]" value="{{ old("voice_traffic.$key.$field") }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">4.2 Local SMS Traffic</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Network</th>
                    <th>Service</th>
                    <th>Incoming SMS</th>
                    <th>Outgoing SMS</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $smsRows = [
                        'intra_mobile' => ['Intra-Network', 'Mobile'],
                        'intra_fixed_wireless' => ['Intra-Network', 'Fixed Wireless'],
                    ];
                    for ($n = 1; $n <= 5; $n++) {
                        $smsRows["other_{$n}_mobile"] = ["Other Network $n", 'Mobile'];
                        $smsRows["other_{$n}_fixed_wireless"] = ["Other Network $n", 'Fixed Wireless'];
                    }
                @endphp
                @foreach($smsRows as $key => $labels)
                <tr>
                    <th>{{ $labels[0] }}</th>
                    <th>{{ $labels[1] }}</th>
                    <td><input type="number" min="0" name="sms_traffic[{{ $key }}][incoming]" value="{{ old("sms_traffic.$key.incoming") }}"></td>
                    <td><input type="number" min="0" name="sms_traffic[{{ $key }}][outgoing]" value="{{ old("sms_traffic.$key.outgoing") }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">4.3 International Traffic</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Country / Carrier</th>
                    <th>Voice Incoming Mobile</th>
                    <th>Voice Incoming Fixed</th>
                    <th>Voice Outgoing Mobile</th>
                    <th>Voice Outgoing Fixed</th>
                    <th>VoIP Incoming Mobile</th>
                    <th>VoIP Incoming Fixed</th>
                    <th>VoIP Outgoing Mobile</th>
                    <th>VoIP Outgoing Fixed</th>
                    <th>SMS Incoming</th>
                    <th>SMS Outgoing</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $countries = [
                        'uganda' => 'Uganda',
                        'tanzania' => 'Tanzania',
                        'rwanda' => 'Rwanda',
                        'burundi' => 'Burundi',
                        'south_sudan' => 'South Sudan',
                        'drc' => 'Democratic Republic of Congo',
                        'others' => 'Others',
                        'total' => 'Total',
                    ];
                    $intlFields = ['voice_in_mobile','voice_in_fixed','voice_out_mobile','voice_out_fixed','voip_in_mobile','voip_in_fixed','voip_out_mobile','voip_out_fixed','sms_in','sms_out'];
                @endphp
                @foreach($countries as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach($intlFields as $field)
                    <td><input type="number" min="0" name="international_traffic[{{ $key }}][{{ $field }}]" value="{{ old("international_traffic.$key.$field") }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">4.4.1 Out-Bound Mobile Roaming Traffic</div>
        @includeWhen(false, 'none')
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Voice Incoming</th>
                    <th>Voice Outgoing</th>
                    <th>SMS Incoming</th>
                    <th>SMS Outgoing</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($countries as $key => $label)
                    @continue($key === 'total' ? false : false)
                    <tr>
                        <th>{{ $label }}</th>
                        @foreach(['voice_in','voice_out','sms_in','sms_out','data'] as $field)
                        <td><input type="number" min="0" name="roaming_outbound[{{ $key }}][{{ $field }}]" value="{{ old("roaming_outbound.$key.$field") }}"></td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">4.4.2 In-Bound Mobile Roaming Traffic</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Voice Incoming</th>
                    <th>Voice Outgoing</th>
                    <th>SMS Incoming</th>
                    <th>SMS Outgoing</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($countries as $key => $label)
                    <tr>
                        <th>{{ $label }}</th>
                        @foreach(['voice_in','voice_out','sms_in','sms_out','data'] as $field)
                        <td><input type="number" min="0" name="roaming_inbound[{{ $key }}][{{ $field }}]" value="{{ old("roaming_inbound.$key.$field") }}"></td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-section-title">5. Quality of Service</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Indicator</th>
                    <th>Target</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $qosRows = [
                        'unsuccessful_call_ratio' => ['VOICE - Unsuccessful Call Ratio', '<5%'],
                        'dropped_call_ratio' => ['VOICE - Dropped Call Ratio', '<2%'],
                        'call_setup_time' => ['VOICE - Call Set Up Time', '<8 Sec'],
                        'voice_quality' => ['VOICE - Voice Quality (POLQA MOS)', '>3.4 NB'],
                        'handover_success' => ['VOICE - Handover Success Rate', '>96%'],
                        'sms_successful_ratio' => ['SMS - Successful SMS Ratio', '>95%'],
                        'sms_completion_rate' => ['SMS - Completion Rate SMS Ratio', '>95%'],
                        'sms_delivery_ratio' => ['SMS - End to End SMS Delivery Ratio', '>95%'],
                        'jitter_latency' => ['DATA - Jitter and Latency', '>95%'],
                        'throughput' => ['DATA - Throughput', '>95%'],
                        'browsing' => ['DATA - Browsing', '>95%'],
                    ];
                @endphp
                @foreach($qosRows as $key => $row)
                <tr>
                    <th>{{ $row[0] }}</th>
                    <td>{{ $row[1] }}</td>
                    <td><input type="text" name="qos[{{ $key }}]" value="{{ old("qos.$key") }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-section-title">6. Consumer / Customer Complaints</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th rowspan="3">Complaint Type</th>
                    <th colspan="6">Number of Complaints</th>
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
                        'network_faults' => 'Network Faults / Downtimes',
                        'poor_service' => 'Poor Service Reception',
                        'disconnections' => 'Disconnections and SLA related complaints',
                        'billing' => 'Billing (charges)',
                        'customer_care' => 'Customer Care and Response Challenges',
                        'spam_malware' => 'Spam and Malware Control',
                        'online_scam' => 'Online Scam',
                        'childline' => 'Childline online abuse and exploitation',
                        'others' => 'Others (Please Specify)',
                        'total' => 'Total',
                    ];
                @endphp
                @foreach($complaints as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['m1_received','m1_resolved','m2_received','m2_resolved','m3_received','m3_resolved'] as $field)
                    <td><input type="number" min="0" name="complaints[{{ $key }}][{{ $field }}]" value="{{ old("complaints.$key.$field") }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-section-title">Part B: Annual Reporting Section</div>

        <div class="cak-section-title">7. Mandatory Documents</div>
        <table class="cak-form-table">
            <tbody>
                <tr><th>Current Certificate of Shareholding</th><td><input type="file" name="shareholding_cert"></td></tr>
                <tr><th>Audited Financial Statements</th><td><input type="file" name="audited_financials"></td></tr>
                <tr><th>Valid Tax Compliance Certificate</th><td><input type="file" name="tax_compliance"></td></tr>
                <tr><th>Tariff Structure</th><td><input type="file" name="tariff_structure"></td></tr>
            </tbody>
        </table>

        <div class="cak-section-title">8. Subscriptions by County</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>County / Indicator</th>
                    <th>Terrestrial Fixed Wireless</th>
                    <th>Terrestrial Fixed Line</th>
                    <th>Fiber To The Home</th>
                    <th>Fiber To The Office</th>
                    <th>Fixed Wireless</th>
                    <th>Satellite</th>
                    <th>Copper Line</th>
                    <th>Cable Modem</th>
                    <th>Other Fixed</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $counties = [
                        'mombasa','kwale','kilifi','tana_river','lamu','taita_taveta','garissa','wajir','mandera','marsabit','isiolo','meru',
                        'tharaka_nithi','embu','kitui','machakos','makueni','nyandarua','nyeri','kirinyaga','muranga','kiambu','turkana',
                        'west_pokot','samburu','trans_nzoia','uasin_gishu','elgeyo_marakwet','nandi','baringo','laikipia','nakuru','narok',
                        'kajiado','kericho','bomet','kakamega','vihiga','bungoma','busia','siaya','kisumu','homa_bay','migori','kisii',
                        'nyamira','nairobi_city'
                    ];
                    $countyCols = ['terrestrial_fixed_wireless','terrestrial_fixed_line','ftth','ftto','fixed_wireless','satellite','copper','cable_modem','other_fixed'];
                @endphp
                @foreach($counties as $county)
                <tr>
                    <th>{{ ucwords(str_replace('_',' ', $county)) }}</th>
                    @foreach($countyCols as $col)
                    <td><input type="number" min="0" name="county_subscriptions[{{ $county }}][{{ $col }}]" value="{{ old("county_subscriptions.$county.$col") }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-section-title">9. Staff</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th rowspan="2">Staff Category</th>
                    <th colspan="2">Local (Kenyan Citizens)</th>
                    <th colspan="2">Expatriates</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>Male</th><th>Female</th><th>Male</th><th>Female</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $staffCats = [
                        'tech_perm' => 'Technical - Permanent',
                        'tech_cont' => 'Technical - Contract',
                        'tech_temp' => 'Technical - Temporary',
                        'nontech_perm' => 'Non Technical - Permanent',
                        'nontech_cont' => 'Non Technical - Contract',
                        'nontech_temp' => 'Non Technical - Temporary',
                    ];
                @endphp
                @foreach($staffCats as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['local_m','local_f','exp_m','exp_f'] as $field)
                    <td><input type="number" min="0" name="staff[{{ $key }}][{{ $field }}]" class="staff-input" value="{{ old("staff.$key.$field") }}"></td>
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

        <div class="cak-section-title">10. Numbering Resources</div>
        <div class="cak-subtitle">10.1 Numbers for Fixed Telephony, Mobile Telephony, Free Phone and Other Services</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>National Destination Code (NDC)</th>
                    <th>Number Series</th>
                    <th>Total Numbers Allocated</th>
                    <th>Numbers in Use</th>
                    <th>Numbers Not in Use</th>
                    <th>Reasons for Non-Usage</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 3; $i++)
                <tr>
                    <td><input type="text" name="fixed_numbering[{{ $i }}][ndc]" value="{{ old("fixed_numbering.$i.ndc") }}"></td>
                    <td><input type="text" name="fixed_numbering[{{ $i }}][series]" value="{{ old("fixed_numbering.$i.series") }}"></td>
                    <td><input type="number" min="0" name="fixed_numbering[{{ $i }}][total]" value="{{ old("fixed_numbering.$i.total") }}"></td>
                    <td><input type="number" min="0" name="fixed_numbering[{{ $i }}][in_use]" value="{{ old("fixed_numbering.$i.in_use") }}"></td>
                    <td><input type="number" min="0" name="fixed_numbering[{{ $i }}][not_in_use]" value="{{ old("fixed_numbering.$i.not_in_use") }}"></td>
                    <td><textarea name="fixed_numbering[{{ $i }}][reason]" rows="2">{{ old("fixed_numbering.$i.reason") }}</textarea></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-subtitle">10.2 Other Numbering Resources</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>Other Numbering Resources</th>
                    <th>Purpose</th>
                    <th>Total Numbers Assigned</th>
                    <th>Numbers in Use</th>
                    <th>Numbers Not in Use</th>
                    <th>Reasons for Non-Usage</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 3; $i++)
                <tr>
                    <td><input type="text" name="other_numbering[{{ $i }}][resource]" value="{{ old("other_numbering.$i.resource") }}"></td>
                    <td><textarea name="other_numbering[{{ $i }}][purpose]" rows="2">{{ old("other_numbering.$i.purpose") }}</textarea></td>
                    <td><input type="number" min="0" name="other_numbering[{{ $i }}][total]" value="{{ old("other_numbering.$i.total") }}"></td>
                    <td><input type="number" min="0" name="other_numbering[{{ $i }}][in_use]" value="{{ old("other_numbering.$i.in_use") }}"></td>
                    <td><input type="number" min="0" name="other_numbering[{{ $i }}][not_in_use]" value="{{ old("other_numbering.$i.not_in_use") }}"></td>
                    <td><textarea name="other_numbering[{{ $i }}][reason]" rows="2">{{ old("other_numbering.$i.reason") }}</textarea></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">11. Cybersecurity Readiness Assessment</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>11.1 Cybersecurity team/officer in place?</th>
                    <td>
                        <label><input type="radio" name="cybersecurity[has_team]" value="yes" {{ old('cybersecurity.has_team') == 'yes' ? 'checked' : '' }}> Yes</label>
                        <label class="ms-4"><input type="radio" name="cybersecurity[has_team]" value="no" {{ old('cybersecurity.has_team', 'no') == 'no' ? 'checked' : '' }}> No</label>
                    </td>
                </tr>
                <tr>
                    <th>11.2 Cybersecurity Staff</th>
                    <td>
                        <table class="cak-form-table">
                            <tr>
                                <th>Total</th><th>Male</th><th>Female</th>
                            </tr>
                            <tr>
                                <td><input type="number" min="0" name="cybersecurity[staff_total]" value="{{ old('cybersecurity.staff_total') }}"></td>
                                <td><input type="number" min="0" name="cybersecurity[staff_male]" value="{{ old('cybersecurity.staff_male') }}"></td>
                                <td><input type="number" min="0" name="cybersecurity[staff_female]" value="{{ old('cybersecurity.staff_female') }}"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>11.3 Tools and systems to manage cybersecurity?</th>
                    <td>
                        <label><input type="radio" name="cybersecurity[has_tools]" value="yes" {{ old('cybersecurity.has_tools') == 'yes' ? 'checked' : '' }}> Yes</label>
                        <label class="ms-4"><input type="radio" name="cybersecurity[has_tools]" value="no" {{ old('cybersecurity.has_tools', 'no') == 'no' ? 'checked' : '' }}> No</label>
                    </td>
                </tr>
                <tr>
                    <th>11.4 Tools / systems deployed</th>
                    <td><textarea name="cybersecurity[tools_deployed]" rows="4">{{ old('cybersecurity.tools_deployed') }}</textarea></td>
                </tr>
                <tr>
                    <th>11.5 Cyber incident in last 12 months?</th>
                    <td>
                        <label><input type="radio" name="cybersecurity[had_incident]" value="yes" {{ old('cybersecurity.had_incident') == 'yes' ? 'checked' : '' }}> Yes</label>
                        <label class="ms-4"><input type="radio" name="cybersecurity[had_incident]" value="no" {{ old('cybersecurity.had_incident', 'no') == 'no' ? 'checked' : '' }}> No</label>
                    </td>
                </tr>
                <tr>
                    <th>11.6 Type of cyber incident</th>
                    <td>
                        @foreach(['malware'=>'Malware','ransomware'=>'Ransomware','web_attack'=>'Web Application Attack','impersonation'=>'Online Abuse (Impersonation)'] as $value => $label)
                            <label class="me-4"><input type="checkbox" name="cybersecurity[incident_types][]" value="{{ $value }}"> {{ $label }}</label>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>11.7 Reported cyber incidents?</th>
                    <td>
                        <label><input type="radio" name="cybersecurity[reported]" value="yes" {{ old('cybersecurity.reported') == 'yes' ? 'checked' : '' }}> Yes</label>
                        <label class="ms-4"><input type="radio" name="cybersecurity[reported]" value="no" {{ old('cybersecurity.reported', 'no') == 'no' ? 'checked' : '' }}> No</label>
                    </td>
                </tr>
                <tr>
                    <th>11.8 Where reported?</th>
                    <td>
                        @foreach(['ca'=>'Communications Authority of Kenya','ke_cirt'=>'KE-CIRT','sector_cirt'=>'Sector CIRT','police'=>'National Police Service','others'=>'Others'] as $value => $label)
                            <label class="me-4"><input type="checkbox" name="cybersecurity[reported_to][]" value="{{ $value }}"> {{ $label }}</label>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>11.9 Cyber awareness initiatives?</th>
                    <td>
                        <label><input type="radio" name="cybersecurity[has_awareness]" value="yes" {{ old('cybersecurity.has_awareness') == 'yes' ? 'checked' : '' }}> Yes</label>
                        <label class="ms-4"><input type="radio" name="cybersecurity[has_awareness]" value="no" {{ old('cybersecurity.has_awareness', 'no') == 'no' ? 'checked' : '' }}> No</label>
                    </td>
                </tr>
                <tr>
                    <th>11.10 Awareness activities carried out</th>
                    <td><textarea name="cybersecurity[awareness_activities]" rows="4">{{ old('cybersecurity.awareness_activities') }}</textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-section-title">12. Compliance to Provision of Service and Facilities to Persons Living With Disability in Line With KS2952 Standard</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Aware of KS2952 Standard?</th>
                    <td>
                        <select name="pwd_aware">
                            <option value="">Select</option>
                            <option value="yes" {{ old('pwd_aware') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ old('pwd_aware') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Complied with the standard?</th>
                    <td>
                        <select name="pwd_complied">
                            <option value="">Select</option>
                            <option value="yes" {{ old('pwd_complied') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ old('pwd_complied') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </td>
                </tr>
                <tr><th>Standard Matrix Attachment</th><td><input type="file" name="pwd_standard_matrix"></td></tr>
                <tr><th>Actions taken for PWD accessibility</th><td><textarea name="pwd_actions" rows="5">{{ old('pwd_actions') }}</textarea></td></tr>
                <tr><th>Challenges serving PWDs</th><td><textarea name="pwd_challenges" rows="5">{{ old('pwd_challenges') }}</textarea></td></tr>
                <tr><th>Future plans for ICT inclusivity</th><td><textarea name="pwd_future_plans" rows="5">{{ old('pwd_future_plans') }}</textarea></td></tr>
            </tbody>
        </table>

        <div class="cak-section-title">13. Environmental Sustainability Compliance</div>
        <table class="cak-form-table">
            <tbody>
                <tr><th>E-waste collection initiatives / take-back mechanisms</th><td><textarea name="ewaste_initiatives" rows="5">{{ old('ewaste_initiatives') }}</textarea></td></tr>
                <tr><th>Carbon footprint reduction initiatives</th><td><textarea name="carbon_initiatives" rows="5">{{ old('carbon_initiatives') }}</textarea></td></tr>
                <tr><th>EMCA Waste Management adherence status</th><td><textarea name="emca_status" rows="5">{{ old('emca_status') }}</textarea></td></tr>
            </tbody>
        </table>

        <div class="cak-section-title">14. Comments / Suggestions</div>
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
                    <th style="width:18%;">Name <span class="text-danger">*</span></th>
                    <td style="width:55%;"><input type="text" name="submitter_name" value="{{ old('submitter_name') }}" required></td>
                    <td rowspan="4" style="width:27%; text-align:center; vertical-align:bottom;">
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
            <strong>THANK YOU FOR COMPLETING THE FORM</strong>
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

        <p class="mt-3">
            <strong>
                A COMPLIANCE CERTIFICATE WILL NOT BE ISSUED IF THE COMPLIANCE RETURNS ARE SUBMITTED LATE OR REJECTED BY THE AUTHORITY
            </strong>
        </p>

        <div class="submit-buttons">
            <button type="submit" name="submit" value="submit" class="btn btn-kp-primary btn-lg">
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

            <a href="{{ route('asp.index') }}" class="btn btn-dark btn-lg">
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
        let localM = 0, localF = 0, expM = 0, expF = 0, grand = 0;

        document.querySelectorAll('.staff-input').forEach(function (input) {
            input.addEventListener('input', calculateStaffTotals);
        });

        document.querySelectorAll('input[name^="staff"]').forEach(function () {});

        document.querySelectorAll('.cak-form-table tr').forEach(function (row) {
            const lm = parseInt(row.querySelector('[name*="[local_m]"]')?.value || 0);
            const lf = parseInt(row.querySelector('[name*="[local_f]"]')?.value || 0);
            const em = parseInt(row.querySelector('[name*="[exp_m]"]')?.value || 0);
            const ef = parseInt(row.querySelector('[name*="[exp_f]"]')?.value || 0);

            const hasStaffFields = row.querySelector('[name*="[local_m]"]');
            if (!hasStaffFields) return;

            const rowTotal = lm + lf + em + ef;
            const totalInput = row.querySelector('.staff-row-total');
            if (totalInput) totalInput.value = rowTotal;

            localM += lm;
            localF += lf;
            expM += em;
            expF += ef;
            grand += rowTotal;
        });

        const ids = {
            staff_total_local_m: localM,
            staff_total_local_f: localF,
            staff_total_exp_m: expM,
            staff_total_exp_f: expF,
            staff_grand_total: grand
        };

        Object.entries(ids).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.value = value;
        });
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
