@extends('layouts.app')

@section('title', 'ASP Compliance Return')
@section('page-title', 'Application Service Provider (ASP) Compliance Return')
<style>
/* Main animation */
@keyframes attractAttention {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.7);
    }
    20% {
        transform: scale(1.08);
        background: #ffc107;
    }
    40% {
        transform: scale(0.98);
    }
    60% {
        transform: scale(1.05);
        box-shadow: 0 0 0 15px rgba(255, 215, 0, 0.3);
    }
    80% {
        transform: scale(0.99);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 215, 0, 0);
    }
}

/* Shimmer effect */
@keyframes shimmer {
    0% {
        background-position: -200% center;
    }
    100% {
        background-position: 200% center;
    }
}

/* Text pulse */
@keyframes textPulse {
    0%, 100% {
        text-shadow: 0 0 0px #FFD700;
    }
    50% {
        text-shadow: 0 0 8px #FFD700;
        color: #003f20;
    }
}

.attract-button {
    animation: attractAttention 1.2s ease-in-out 3;
    position: relative;
    overflow: hidden;
}

.attract-button::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
    transform: rotate(45deg);
    pointer-events: none;
}

.pulse-text {
    animation: textPulse 0.8s ease-in-out 6;
}

.hand-pointer {
    display: inline-block;
    animation: bounce 0.5s ease infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(5px); }
}

/* Badge animation */
@keyframes badgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); background: #dc3545; }
}

.new-badge {
    animation: badgePulse 0.8s ease-in-out 3;
    display: inline-block;
}
</style>

<button type="button"
        id="magicAutoFillBtn"
        class="btn btn-kp-warning btn-lg autofill-asp-btn mt-5 pt-4 attract-button"
        data-url="{{ route('asp.autofill-record-2') }}"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="✨ Click to automatically fill the form! ✨">
    <i class="fas fa-magic me-2 pulse-text"></i>
    <span class="pulse-text">Auto Fill</span>
    <span class="hand-pointer ms-2">👉</span>
    <span class="badge bg-danger ms-2 new-badge">NEW!</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('magicAutoFillBtn');

    if (btn) {
        // Add hover tooltip enhancement
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'all 0.3s ease';

            // Optional: Play beep sound on hover (requires user interaction first)
            if (window.speechSynthesis) {
                const utterance = new SpeechSynthesisUtterance('Auto fill available');
                utterance.volume = 0.3;
                utterance.rate = 1.5;
                window.speechSynthesis.speak(utterance);
            }
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });

        // Log to console (for debugging)
        console.log('Auto Fill button is ready! User can click to auto-fill the form.');
    }
});
</script>


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

   .cak-form-table input:not([type="radio"]):not([type="checkbox"]),
.cak-form-table select,
.cak-form-table textarea {
    width:100%;
    border:1px solid #ced4da;
    outline:none;
    box-shadow:none;
    font-size:12px;
    padding:4px 6px;
    background:#fff;
    border-radius:3px;
}

.cak-form-table input[type="radio"],
.cak-form-table input[type="checkbox"] {
    width:auto;
    margin-right:4px;
}


.cak-form-table input[type="file"] {
    width:100%;
    border:1px solid #ddd;
    background:#fff;
}

    .text-center { text-align:center; }
    .total-field { background:#f8f9fa !important; font-weight:bold; text-align:center; }
    .official-header {
        font-size:18px; font-weight:700; text-align:center; text-transform:uppercase;
        border-top:3px solid #000; border-bottom:1.5px solid #000; padding:10px 0; margin-top:35px;
    }
    .official-table th,.official-table td { height:70px; }
    .submit-buttons { margin-top:30px; padding-top:20px; border-top:2px solid #ddd; text-align:center; }
    @media print {
        .btn,.submit-buttons,.breadcrumb,.navbar,.sidebar { display:none !important; }
        .form-container { box-shadow:none; padding:0; }
        .cak-section-title { background:#fff !important; color:#000 !important; border:1.5px solid #000; }
        .cak-form-table th,.cak-form-table td { border:1.5px solid #000 !important; }
        .cak-form-table input,
.cak-form-table select,
.cak-form-table textarea {
    border:0 !important;
    background:transparent !important;
    border-radius:0 !important;
}
.instruction-box,
.form-container p {
    text-align:justify;
}
.autofill-kp-btn {
    background: linear-gradient(135deg, #0066B3 0%, #005499 100%);
    border: none;
    color: red;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    border-radius: 0.5rem;
}

.autofill-kp-btn:hover {
    background: linear-gradient(135deg, #005499 0%, #004080 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 102, 179, 0.3);
}

.autofill-kp-btn:active {
    transform: translateY(0);
}

.autofill-kp-btn i {
    margin-right: 0.5rem;
}
    }
</style>
@endpush

@section('content')

@php
    $data = isset($record) ? ($record->form_data ?? []) : [];
    $attachments = isset($record) ? ($record->attachments ?? []) : [];

    $oldValue = fn($key, $default = '') => old($key, data_get($data, $key, $default));

    $cyberValue = fn($key, $default = null) => old("cybersecurity.$key", data_get($data, "cybersecurity.$key", $default));

    $incidentTypes = old(
        'cybersecurity.incident_types',
        data_get($data, 'cybersecurity.incident_types', [])
    );

    $reportedTo = old(
        'cybersecurity.reported_to',
        data_get($data, 'cybersecurity.reported_to', [])
    );

    $incidentTypes = is_array($incidentTypes) ? $incidentTypes : [];
    $reportedTo = is_array($reportedTo) ? $reportedTo : [];
@endphp


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
        i. Please download the latest form from the Authority’s website at the end of each quarter.<br>
        ii. This form has provision for both quarterly and annual compliance reporting.<br>
        iii. Information is to be submitted within 15 days after the end of every quarter.<br>
        iv. Provide information in the spaces provided. Additional rows/pages may be inserted where required.<br>
        v. Provide accurate information and explain fields where you may not have relevant information.<br>
        vi. Where Nil returns are submitted, an explanation MUST be provided under Comments/Suggestions.
    </div>

    <form method="POST"
          action="{{ isset($record) ? route('asp.update', $record->id) : route('asp.store') }}"
          enctype="multipart/form-data"
          id="aspForm">
        @csrf
        @isset($record)
            @method('PUT')
        @endisset

        {{-- 1 GENERAL INFORMATION --}}
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

        <div class="cak-subtitle">1.3.1 Physical Address</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>County</th><td><input type="text" name="county" value="{{ $oldValue('county') }}"></td>
                    <th>Town</th><td><input type="text" name="town" value="{{ $oldValue('town') }}"></td>
                    <th>Street/Road</th><td><input type="text" name="street_road" value="{{ $oldValue('street_road') }}"></td>
                </tr>
                <tr>
                    <th>Name of Building</th><td><input type="text" name="building_name" value="{{ $oldValue('building_name') }}"></td>
                    <th>Floor No.</th><td><input type="text" name="floor_no" value="{{ $oldValue('floor_no') }}"></td>
                    <th>Room No.</th><td><input type="text" name="room_no" value="{{ $oldValue('room_no') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.2 Postal Address</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>P.O. Box</th><td><input type="text" name="p_o_box" value="{{ $oldValue('p_o_box') }}"></td>
                    <th>Town</th><td><input type="text" name="postal_town" value="{{ $oldValue('postal_town') }}"></td>
                    <th>Code</th><td><input type="text" name="postal_code" value="{{ $oldValue('postal_code') }}"></td>
                </tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.3.3 Telephone Contacts</div>
        <table class="cak-form-table">
            <tbody>
                <tr>
                    <th>Tel No.</th><td><input type="text" name="tel_no" value="{{ $oldValue('tel_no') }}"></td>
                    <th>Mobile No.</th><td><input type="text" name="mobile_no" value="{{ $oldValue('mobile_no') }}"></td>
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
                <tr><th>Email Address</th><td><input type="email" name="email" value="{{ $oldValue('email') }}"></td></tr>
                <tr><th>Web Address</th><td><input type="url" name="web_address" value="{{ $oldValue('web_address') }}"></td></tr>
            </tbody>
        </table>

        <div class="cak-subtitle">1.4 Contact Details</div>
        <table class="cak-form-table">
            <tbody>
                <tr><th>Name of Chief Executive Officer (CEO)</th><td colspan="3"><input type="text" name="ceo_name" value="{{ $oldValue('ceo_name') }}"></td></tr>
                <tr><th>Name of Contact Person</th><td colspan="3"><input type="text" name="contact_person" value="{{ $oldValue('contact_person') }}"></td></tr>
                <tr>
                    <th>Telephone Landline</th><td><input type="text" name="contact_landline" value="{{ $oldValue('contact_landline') }}"></td>
                    <th>Mobile</th><td><input type="text" name="contact_mobile" value="{{ $oldValue('contact_mobile') }}"></td>
                </tr>
                <tr><th>Email</th><td colspan="3"><input type="email" name="contact_email" value="{{ $oldValue('contact_email') }}"></td></tr>
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

        <div class="cak-section-title">2. Services Provided Under the License</div>

        <div class="cak-subtitle">2.1 Machine to Machine Services</div>
        <table class="cak-form-table">
            <thead>
                <tr><th>No.</th><th>Service Provided</th><th>Brief Description</th><th>Number of Subscriptions</th></tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 10; $i++)
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td><input type="text" name="m2m_services[{{ $i }}][service]" value="{{ old("m2m_services.$i.service", data_get($data, "m2m_services.$i.service")) }}"></td>
                    <td><textarea name="m2m_services[{{ $i }}][description]" rows="2">{{ old("m2m_services.$i.description", data_get($data, "m2m_services.$i.description")) }}</textarea></td>
                    <td><input type="number" min="0" name="m2m_services[{{ $i }}][subscriptions]" value="{{ old("m2m_services.$i.subscriptions", data_get($data, "m2m_services.$i.subscriptions")) }}"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-subtitle">2.2 Telecommunications Service Subscriptions</div>
        <table class="cak-form-table">
            <thead>
                <tr><th colspan="2" rowspan="2">Category of Subscriptions</th><th colspan="3">Number of Registered Active Subscriptions</th></tr>
                <tr><th>Month 1</th><th>Month 2</th><th>Month 3</th></tr>
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
                    <td><input type="number" min="0" name="subscriptions[{{ $row[2] }}][{{ $m }}]" value="{{ old("subscriptions.{$row[2]}.$m", data_get($data, "subscriptions.{$row[2]}.$m")) }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">2.3 Number of Mobile Phone Devices</div>
        <table class="cak-form-table">
            <tbody>
                <tr><th>Feature Phone</th><td><input type="number" min="0" name="mobile_devices[feature_phone]" value="{{ old('mobile_devices.feature_phone', data_get($data, 'mobile_devices.feature_phone')) }}"></td></tr>
                <tr><th>Smart Phone</th><td><input type="number" min="0" name="mobile_devices[smart_phone]" value="{{ old('mobile_devices.smart_phone', data_get($data, 'mobile_devices.smart_phone')) }}"></td></tr>
                <tr><th>Others (e.g. Tablets)</th><td><input type="number" min="0" name="mobile_devices[others]" value="{{ old('mobile_devices.others', data_get($data, 'mobile_devices.others')) }}"></td></tr>
            </tbody>
        </table>

        <div class="cak-subtitle">2.4 Data/Internet Service Subscriptions By Technology</div>
        <table class="cak-form-table">
            <thead><tr><th>Technology</th><th>Month 1</th><th>Month 2</th><th>Month 3</th></tr></thead>
            <tbody>
                @php
                    $dataTechs = [
                        'data_enabled_sim'=>'Data Enabled SIM cards',
                        'ftth'=>'Fiber To The Home',
                        'ftto'=>'Fiber To The Office',
                        'fixed_wireless'=>'Terrestrial Fixed Wireless e.g. WiMax, WiFi',
                        'satellite'=>'Satellite',
                        'copper'=>'Copper Line (Dial-up & DSL, xDSL)',
                        'cable_modem'=>'Cable Modem',
                        'other_fixed'=>'Other Fixed, Please Specify',
                    ];
                @endphp
                @foreach($dataTechs as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['m1','m2','m3'] as $m)
                    <td><input type="number" min="0" name="data_subscriptions[{{ $key }}][{{ $m }}]" value="{{ old("data_subscriptions.$key.$m", data_get($data, "data_subscriptions.$key.$m")) }}"></td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">2.5 Broadband Service Subscriptions</div>
        <table class="cak-form-table">
            <thead>
                <tr><th rowspan="2">Technology</th><th colspan="3">Active Broadband Subscriptions</th><th rowspan="2">Data Volumes Consumed (GB)</th></tr>
                <tr><th>Month 1</th><th>Month 2</th><th>Month 3</th></tr>
            </thead>
            <tbody>
                @php
                    $broadbandTechs = ['3g'=>'3G','4g'=>'4G','5g'=>'5G','ftth'=>'Fiber To The Home','ftto'=>'Fiber To The Office','fixed_wireless'=>'Terrestrial Fixed Wireless (WiMax/WiFi)','satellite'=>'Satellite','copper'=>'Copper Line','cable_modem'=>'Cable Modem','other_fixed'=>'Other Fixed, Please Specify'];
                @endphp
                @foreach($broadbandTechs as $key => $label)
                <tr>
                    <th>{{ $label }}</th>
                    @foreach(['m1','m2','m3'] as $m)
                    <td><input type="number" min="0" name="broadband[{{ $key }}][{{ $m }}]" value="{{ old("broadband.$key.$m", data_get($data, "broadband.$key.$m")) }}"></td>
                    @endforeach
                    <td><input type="number" min="0" step="0.01" name="broadband[{{ $key }}][volume]" value="{{ old("broadband.$key.volume", data_get($data, "broadband.$key.volume")) }}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cak-subtitle">2.6 Fixed Data Subscriptions by Speed by Technology</div>

<table class="cak-form-table">
    <thead>
        <tr>
            <th>Technology</th>
            <th>&lt;256 Kbps</th>
            <th>&gt;=256 Kbps &lt;2 Mbps</th>
            <th>&gt;=2 Mbps &lt;10 Mbps</th>
            <th>&gt;=10 Mbps &lt;30 Mbps</th>
            <th>&gt;=30 Mbps &lt;100 Mbps</th>
            <th>&gt;100 Mbps &lt;=1 Gbps</th>
            <th>&gt;1 Gbps</th>
        </tr>
    </thead>

    <tbody>
        @php
            $speedTechs = [
                'ftth' => 'Fiber To The Home',
                'ftto' => 'Fiber To The Office',
                'fixed_wireless' => 'Terrestrial Fixed Wireless',
                'satellite' => 'Satellite',
                'copper' => 'Copper Line',
                'cable_modem' => 'Cable Modem',
                'other_fixed' => 'Other Fixed, Please Specify',
                'total' => 'Total',
            ];

            $speedBands = [
                'lt_256',
                'kbps_256_2mbps',
                'mbps_2_10',
                'mbps_10_30',
                'mbps_30_100',
                'mbps_100_1gbps',
                'gt_1gbps',
            ];
        @endphp

        @foreach($speedTechs as $key => $label)
            <tr>
                <th>{{ $label }}</th>

                @foreach($speedBands as $band)
                    <td>
                        <input type="number"
                               min="0"
                               class="speed-field {{ $key === 'total' ? 'total-field' : '' }}"
                               data-row="{{ $key }}"
                               data-band="{{ $band }}"
                               name="speed_subscriptions[{{ $key }}][{{ $band }}]"
                               value="{{ old("speed_subscriptions.$key.$band", data_get($data, "speed_subscriptions.$key.$band")) }}"
                               {{ $key === 'total' ? 'readonly' : '' }}>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

        <div class="cak-section-title">3. Mobile Number Portability</div>
        <table class="cak-form-table">
            <thead><tr><th>Operator</th><th>Number of In-Ports</th></tr></thead>
            <tbody>
                @for($i = 1; $i <= 4; $i++)
                <tr>
                    <td><input type="text" name="mnp[{{ $i }}][operator]" value="{{ old("mnp.$i.operator", data_get($data, "mnp.$i.operator")) }}"></td>
                    <td><input type="number" min="0" name="mnp[{{ $i }}][in_ports]" value="{{ old("mnp.$i.in_ports", data_get($data, "mnp.$i.in_ports")) }}"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="cak-section-title">4. Traffic for Telephone Services (Voice & SMS)</div>

<div class="cak-subtitle">4.1 Local Voice Traffic</div>

<table class="cak-form-table">
    <thead>
        <tr>
            <th rowspan="3">Name of Operator / Indicator</th>
            <th colspan="2">Voice</th>
            <th colspan="2">VoIP</th>
        </tr>
        <tr>
            <th>Minutes</th>
            <th>Calls</th>
            <th>Minutes</th>
            <th>Calls</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="5">Intra-Network</th>
        </tr>

        @foreach([
            'intra_mobile' => 'Mobile',
            'intra_fixed_wireless' => 'Fixed Wireless',
            'intra_fixed_line' => 'Fixed Line',
        ] as $key => $label)
            <tr>
                <th>{{ $label }}</th>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voice_minutes]" value="{{ old("voice_traffic.$key.voice_minutes", data_get($data, "voice_traffic.$key.voice_minutes")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voice_calls]" value="{{ old("voice_traffic.$key.voice_calls", data_get($data, "voice_traffic.$key.voice_calls")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voip_minutes]" value="{{ old("voice_traffic.$key.voip_minutes", data_get($data, "voice_traffic.$key.voip_minutes")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voip_calls]" value="{{ old("voice_traffic.$key.voip_calls", data_get($data, "voice_traffic.$key.voip_calls")) }}"></td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="cak-form-table">
    <thead>
        <tr>
            <th rowspan="3">Other Networks</th>
            <th colspan="4">Voice</th>
            <th colspan="4">VoIP</th>
        </tr>
        <tr>
            <th colspan="2">Incoming</th>
            <th colspan="2">Outgoing</th>
            <th colspan="2">Incoming</th>
            <th colspan="2">Outgoing</th>
        </tr>
        <tr>
            <th>Minutes</th>
            <th>Calls</th>
            <th>Minutes</th>
            <th>Calls</th>
            <th>Minutes</th>
            <th>Calls</th>
            <th>Minutes</th>
            <th>Calls</th>
        </tr>
    </thead>
    <tbody>
        @php
            $otherVoiceRows = [
                'other_1_mobile' => '1 Mobile',
                'other_1_fixed_line' => 'Fixed Line',
                'other_1_fixed_wireless' => 'Fixed Wireless',

                'other_2_mobile' => '2 Mobile',
                'other_2_fixed_line' => 'Fixed Line',
                'other_2_fixed_wireless' => 'Fixed Wireless',

                'other_3_mobile' => '3 Mobile',
                'other_3_fixed_line' => 'Fixed Line',
                'other_3_fixed_wireless' => 'Fixed Wireless',

                'other_4_mobile' => '4 Mobile',
                'other_4_fixed_line' => 'Fixed Line',
                'other_4_fixed_wireless' => 'Fixed Wireless',
            ];
        @endphp

        @foreach($otherVoiceRows as $key => $label)
            <tr>
                <th>{{ $label }}</th>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voice_in_minutes]" value="{{ old("voice_traffic.$key.voice_in_minutes", data_get($data, "voice_traffic.$key.voice_in_minutes")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voice_in_calls]" value="{{ old("voice_traffic.$key.voice_in_calls", data_get($data, "voice_traffic.$key.voice_in_calls")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voice_out_minutes]" value="{{ old("voice_traffic.$key.voice_out_minutes", data_get($data, "voice_traffic.$key.voice_out_minutes")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voice_out_calls]" value="{{ old("voice_traffic.$key.voice_out_calls", data_get($data, "voice_traffic.$key.voice_out_calls")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voip_in_minutes]" value="{{ old("voice_traffic.$key.voip_in_minutes", data_get($data, "voice_traffic.$key.voip_in_minutes")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voip_in_calls]" value="{{ old("voice_traffic.$key.voip_in_calls", data_get($data, "voice_traffic.$key.voip_in_calls")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voip_out_minutes]" value="{{ old("voice_traffic.$key.voip_out_minutes", data_get($data, "voice_traffic.$key.voip_out_minutes")) }}"></td>
                <td><input type="number" min="0" name="voice_traffic[{{ $key }}][voip_out_calls]" value="{{ old("voice_traffic.$key.voip_out_calls", data_get($data, "voice_traffic.$key.voip_out_calls")) }}"></td>
            </tr>
        @endforeach
    </tbody>
</table>


{{-- 4.2 LOCAL SMS TRAFFIC --}}
<div class="cak-subtitle">4.2 Local SMS Traffic</div>

<table class="cak-form-table">
    <thead>
        <tr>
            <th>Name of Operator / Indicator</th>
            <th colspan="2">No. of SMS (Excluding money transfer and Premium Rate)</th>
        </tr>
        <tr>
            <th></th>
            <th>Incoming</th>
            <th>Outgoing</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="3">Intra-Network</th>
        </tr>

        @foreach([
    'intra_mobile' => 'Mobile',
    'intra_fixed_wireless' => 'Fixed Wireless',
] as $key => $label)
    <tr>
        <th>{{ $label }}</th>
        <td colspan="2">
            <input type="number"
                   min="0"
                   name="sms_traffic[{{ $key }}][sms]"
                   value="{{ old("sms_traffic.$key.sms", data_get($data, "sms_traffic.$key.sms")) }}">
        </td>
    </tr>
@endforeach


        <tr>
            <th colspan="3">Other Networks</th>
        </tr>

        @foreach([
            'other_1_mobile' => '1. Mobile',
            'other_1_fixed_wireless' => 'Fixed Wireless',

            'other_2_mobile' => '2. Mobile',
            'other_2_fixed_wireless' => 'Fixed Wireless',

            'other_3_mobile' => '3. Mobile',
            'other_3_fixed_wireless' => 'Fixed Wireless',

            'other_4_mobile' => '4. Mobile',
            'other_4_fixed_wireless' => 'Fixed Wireless',

            'other_5_mobile' => '5. Mobile',
            'other_5_fixed_wireless' => 'Fixed Wireless',
        ] as $key => $label)
            <tr>
                <th>{{ $label }}</th>
                <td><input type="number" min="0" name="sms_traffic[{{ $key }}][incoming]" value="{{ old("sms_traffic.$key.incoming", data_get($data, "sms_traffic.$key.incoming")) }}"></td>
                <td><input type="number" min="0" name="sms_traffic[{{ $key }}][outgoing]" value="{{ old("sms_traffic.$key.outgoing", data_get($data, "sms_traffic.$key.outgoing")) }}"></td>
            </tr>
        @endforeach
    </tbody>
</table>


{{-- 4.3 INTERNATIONAL TRAFFIC --}}
<div class="cak-subtitle">4.3 International Traffic</div>

<table class="cak-form-table">
    <thead>
        <tr>
            <th>Country / Carrier</th>
            <th>Voice In Mobile</th>
            <th>Voice In Fixed</th>
            <th>Voice Out Mobile</th>
            <th>Voice Out Fixed</th>
            <th>VoIP In Mobile</th>
            <th>VoIP In Fixed</th>
            <th>VoIP Out Mobile</th>
            <th>VoIP Out Fixed</th>
            <th>SMS In</th>
            <th>SMS Out</th>
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

            $internationalFields = [
                'voice_in_mobile',
                'voice_in_fixed',
                'voice_out_mobile',
                'voice_out_fixed',
                'voip_in_mobile',
                'voip_in_fixed',
                'voip_out_mobile',
                'voip_out_fixed',
                'sms_in',
                'sms_out',
            ];
        @endphp

        @foreach($countries as $key => $label)
            <tr>
                <th>{{ $label }}</th>
                @foreach($internationalFields as $field)
                    <td>
                       <input type="number"
       class="calc-field international-field {{ $key === 'total' ? 'total-field' : '' }}"
       data-table="international_traffic"
       data-row="{{ $key }}"
       data-field="{{ $field }}"
       name="international_traffic[{{ $key }}][{{ $field }}]"
       value="{{ old("international_traffic.$key.$field", data_get($data, "international_traffic.$key.$field")) }}"
       {{ $key === 'total' ? 'readonly' : '' }}>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

{{-- 4.4 INTERNATIONAL MOBILE ROAMING --}}
<div class="cak-subtitle">4.4 International Mobile Roaming</div>
<div class="cak-subtitle">4.4.1 Out-Bound Mobile Roaming Traffic</div>

@include('cak.asp.partials.roaming-table', [
    'tableKey' => 'roaming_outbound',
    'countries' => $countries,
    'data' => $data
])

<div class="cak-subtitle">4.4.2 In-Bound Mobile Roaming Traffic</div>

@include('cak.asp.partials.roaming-table', [
    'tableKey' => 'roaming_inbound',
    'countries' => $countries,
    'data' => $data
])

        <div class="cak-section-title">5. Quality of Service</div>
        <table class="cak-form-table">
            <thead><tr><th>Indicator</th><th>Target</th><th>Score</th></tr></thead>
            <tbody>
                @php
                    $qosRows = [
                        'unsuccessful_call_ratio'=>['VOICE - Unsuccessful Call Ratio','<5%'],
                        'dropped_call_ratio'=>['VOICE - Dropped Call Ratio','<2%'],
                        'call_setup_time'=>['VOICE - Call Set Up Time','<8 Sec'],
                        'voice_quality'=>['VOICE - Voice Quality (POLQA MOS)','>3.4 NB'],
                        'handover_success'=>['VOICE - Handover Success Rate','>96%'],
                        'sms_successful_ratio'=>['SMS - Successful SMS Ratio','>95%'],
                        'sms_completion_rate'=>['SMS - Completion Rate SMS Ratio','>95%'],
                        'sms_delivery_ratio'=>['SMS - End to End SMS Delivery Ratio','>95%'],
                        'jitter_latency'=>['DATA - Jitter and Latency','>95%'],
                        'throughput'=>['DATA - Throughput','>95%'],
                        'browsing'=>['DATA - Browsing','>95%'],
                    ];
                @endphp
                @foreach($qosRows as $key => $row)
                <tr>
                    <th>{{ $row[0] }}</th>
                    <td>{{ $row[1] }}</td>
                    <td><input type="text" name="qos[{{ $key }}]" value="{{ old("qos.$key", data_get($data, "qos.$key")) }}"></td>
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

            $complaintFields = [
                'm1_received',
                'm1_resolved',
                'm2_received',
                'm2_resolved',
                'm3_received',
                'm3_resolved',
            ];
        @endphp

        @foreach($complaints as $key => $label)
            <tr>
                <th>{{ $label }}</th>

                @foreach($complaintFields as $field)
                    <td>
                        <input type="number"
                               min="0"
                               class="complaint-field {{ $key === 'total' ? 'total-field' : '' }}"
                               data-row="{{ $key }}"
                               data-field="{{ $field }}"
                               name="complaints[{{ $key }}][{{ $field }}]"
                               value="{{ old("complaints.$key.$field", data_get($data, "complaints.$key.$field")) }}"
                               {{ $key === 'total' ? 'readonly' : '' }}>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

        {{-- PART B --}}
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
                    <th>County / Indicator</th><th>Terrestrial Fixed Wireless</th><th>Terrestrial Fixed Line</th><th>Fiber To The Home</th>
                    <th>Fiber To The Office</th><th>Fixed Wireless</th><th>Satellite</th><th>Copper Line</th><th>Cable Modem</th><th>Other Fixed</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $counties = ['mombasa','kwale','kilifi','tana_river','lamu','taita_taveta','garissa','wajir','mandera','marsabit','isiolo','meru','tharaka_nithi','embu','kitui','machakos','makueni','nyandarua','nyeri','kirinyaga','muranga','kiambu','turkana','west_pokot','samburu','trans_nzoia','uasin_gishu','elgeyo_marakwet','nandi','baringo','laikipia','nakuru','narok','kajiado','kericho','bomet','kakamega','vihiga','bungoma','busia','siaya','kisumu','homa_bay','migori','kisii','nyamira','nairobi_city'];
                    $countyCols = ['terrestrial_fixed_wireless','terrestrial_fixed_line','ftth','ftto','fixed_wireless','satellite','copper','cable_modem','other_fixed'];
                @endphp
               @foreach($counties as $county)
<tr>
    <th>{{ ucwords(str_replace('_',' ', $county)) }}</th>

    @foreach($countyCols as $col)
        <td>
            <input type="number"
                   min="0"
                   name="county_subscriptions[{{ $county }}][{{ $col }}]"
                   value="{{ old("county_subscriptions.$county.$col", data_get($data, "county_subscriptions.$county.$col", 0)) }}">
        </td>
    @endforeach
</tr>
@endforeach
            </tbody>
        </table>

        <div class="cak-section-title">9. Staff</div>
        <table class="cak-form-table staff-table">
            <thead>
                <tr><th rowspan="2">Staff Category</th><th colspan="2">Local (Kenyan Citizens)</th><th colspan="2">Expatriates</th><th rowspan="2">Total</th></tr>
                <tr><th>Male</th><th>Female</th><th>Male</th><th>Female</th></tr>
            </thead>
            <tbody>
                @php
                    $staffCats = ['tech_perm'=>'Technical - Permanent','tech_cont'=>'Technical - Contract','tech_temp'=>'Technical - Temporary','nontech_perm'=>'Non Technical - Permanent','nontech_cont'=>'Non Technical - Contract','nontech_temp'=>'Non Technical - Temporary'];
                @endphp
                @foreach($staffCats as $key => $label)
<tr class="staff-data-row">
    <th>{{ $label }}</th>
    @foreach(['local_m','local_f','exp_m','exp_f'] as $field)
        <td>
            <input type="number"
                   min="0"
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
    <td><input type="text" name="staff_total[local_m]" id="staff_total_local_m" class="total-field" readonly></td>
    <td><input type="text" name="staff_total[local_f]" id="staff_total_local_f" class="total-field" readonly></td>
    <td><input type="text" name="staff_total[exp_m]" id="staff_total_exp_m" class="total-field" readonly></td>
    <td><input type="text" name="staff_total[exp_f]" id="staff_total_exp_f" class="total-field" readonly></td>
</tr>



            </tbody>
        </table>

        <div class="cak-section-title">10. Numbering Resources</div>
        @foreach(['fixed_numbering' => '10.1 Numbers for Fixed Telephony, Mobile Telephony, Free Phone and Other Services', 'other_numbering' => '10.2 Other Numbering Resources'] as $numKey => $numTitle)
        <div class="cak-subtitle">{{ $numTitle }}</div>
        <table class="cak-form-table">
            <thead>
                <tr>
                    <th>{{ $numKey === 'fixed_numbering' ? 'NDC' : 'Other Numbering Resources' }}</th>
                    <th>{{ $numKey === 'fixed_numbering' ? 'Number Series' : 'Purpose' }}</th>
                    <th>Total Numbers</th><th>Numbers In Use</th><th>Numbers Not In Use</th><th>Reasons for Non-Usage</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 3; $i++)
                <tr>
                    <td><input type="text" name="{{ $numKey }}[{{ $i }}][resource]" value="{{ old("$numKey.$i.resource", data_get($data, "$numKey.$i.resource")) }}"></td>
                    <td><input type="text" name="{{ $numKey }}[{{ $i }}][purpose]" value="{{ old("$numKey.$i.purpose", data_get($data, "$numKey.$i.purpose")) }}"></td>
                    <td><input type="number" min="0" name="{{ $numKey }}[{{ $i }}][total]" value="{{ old("$numKey.$i.total", data_get($data, "$numKey.$i.total")) }}"></td>
                    <td><input type="number" min="0" name="{{ $numKey }}[{{ $i }}][in_use]" value="{{ old("$numKey.$i.in_use", data_get($data, "$numKey.$i.in_use")) }}"></td>
                    <td><input type="number" min="0" name="{{ $numKey }}[{{ $i }}][not_in_use]" value="{{ old("$numKey.$i.not_in_use", data_get($data, "$numKey.$i.not_in_use")) }}"></td>
                    <td><textarea name="{{ $numKey }}[{{ $i }}][reason]" rows="2">{{ old("$numKey.$i.reason", data_get($data, "$numKey.$i.reason")) }}</textarea></td>
                </tr>
                @endfor
            </tbody>
        </table>
        @endforeach

        <div class="cak-section-title">11. Cybersecurity Readiness Assessment</div>
<table class="cak-form-table">
    <tbody>
        <tr>
            <th>Cybersecurity team/officer in place?</th>
            <td>
                <label>
                    <input type="radio" name="cybersecurity[has_team]" value="yes" {{ $cyberValue('has_team') === 'yes' ? 'checked' : '' }}>
                    Yes
                </label>
                <label class="ms-4">
                    <input type="radio" name="cybersecurity[has_team]" value="no" {{ $cyberValue('has_team') === 'no' ? 'checked' : '' }}>
                    No
                </label>
            </td>
        </tr>

        <tr>
            <th>Cybersecurity Staff</th>
            <td>
                <table class="cak-form-table">
                    <tr><th>Total</th><th>Male</th><th>Female</th></tr>
                    <tr>
                        <td><input type="number" min="0" name="cybersecurity[staff_total]" value="{{ $cyberValue('staff_total') }}"></td>
                        <td><input type="number" min="0" name="cybersecurity[staff_male]" value="{{ $cyberValue('staff_male') }}"></td>
                        <td><input type="number" min="0" name="cybersecurity[staff_female]" value="{{ $cyberValue('staff_female') }}"></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <th>Tools and systems to manage cybersecurity?</th>
            <td>
                <label>
                    <input type="radio" name="cybersecurity[has_tools]" value="yes" {{ $cyberValue('has_tools') === 'yes' ? 'checked' : '' }}>
                    Yes
                </label>
                <label class="ms-4">
                    <input type="radio" name="cybersecurity[has_tools]" value="no" {{ $cyberValue('has_tools') === 'no' ? 'checked' : '' }}>
                    No
                </label>
            </td>
        </tr>

        <tr>
            <th>Tools / systems deployed</th>
            <td><textarea name="cybersecurity[tools_deployed]" rows="4">{{ $cyberValue('tools_deployed') }}</textarea></td>
        </tr>

        <tr>
            <th>Cyber incident in last 12 months?</th>
            <td>
                <label>
                    <input type="radio" name="cybersecurity[had_incident]" value="yes" {{ $cyberValue('had_incident') === 'yes' ? 'checked' : '' }}>
                    Yes
                </label>
                <label class="ms-4">
                    <input type="radio" name="cybersecurity[had_incident]" value="no" {{ $cyberValue('had_incident') === 'no' ? 'checked' : '' }}>
                    No
                </label>
            </td>
        </tr>

        <tr>
            <th>Type of cyber incident</th>
            <td>
                @foreach(['malware'=>'Malware','ransomware'=>'Ransomware','web_attack'=>'Web Application Attack','impersonation'=>'Online Abuse (Impersonation)'] as $value => $label)
                    <label class="me-4">
                        <input type="checkbox"
                               name="cybersecurity[incident_types][]"
                               value="{{ $value }}"
                               {{ in_array($value, $incidentTypes, true) ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            </td>
        </tr>

        <tr>
            <th>Reported cyber incidents?</th>
            <td>
                <label>
                    <input type="radio" name="cybersecurity[reported]" value="yes" {{ $cyberValue('reported') === 'yes' ? 'checked' : '' }}>
                    Yes
                </label>
                <label class="ms-4">
                    <input type="radio" name="cybersecurity[reported]" value="no" {{ $cyberValue('reported') === 'no' ? 'checked' : '' }}>
                    No
                </label>
            </td>
        </tr>

        <tr>
            <th>Where reported?</th>
            <td>
                @foreach(['ca'=>'Communications Authority of Kenya','ke_cirt'=>'KE-CIRT','sector_cirt'=>'Sector CIRT','police'=>'National Police Service','others'=>'Others'] as $value => $label)
                    <label class="me-4">
                        <input type="checkbox"
                               name="cybersecurity[reported_to][]"
                               value="{{ $value }}"
                               {{ in_array($value, $reportedTo, true) ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            </td>
        </tr>

        <tr>
            <th>Cyber awareness initiatives?</th>
            <td>
                <label>
                    <input type="radio" name="cybersecurity[has_awareness]" value="yes" {{ $cyberValue('has_awareness') === 'yes' ? 'checked' : '' }}>
                    Yes
                </label>
                <label class="ms-4">
                    <input type="radio" name="cybersecurity[has_awareness]" value="no" {{ $cyberValue('has_awareness') === 'no' ? 'checked' : '' }}>
                    No
                </label>
            </td>
        </tr>

        <tr>
            <th>Awareness activities carried out</th>
            <td><textarea name="cybersecurity[awareness_activities]" rows="4">{{ $cyberValue('awareness_activities') }}</textarea></td>
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
            <option value="yes" {{ $oldValue('pwd_aware') === 'yes' ? 'selected' : '' }}>Yes</option>
            <option value="no" {{ $oldValue('pwd_aware') === 'no' ? 'selected' : '' }}>No</option>
        </select>
    </td>
</tr>
<tr>
    <th>Complied with the standard?</th>
    <td>
        <select name="pwd_complied">
            <option value="">Select</option>
            <option value="yes" {{ $oldValue('pwd_complied') === 'yes' ? 'selected' : '' }}>Yes</option>
            <option value="no" {{ $oldValue('pwd_complied') === 'no' ? 'selected' : '' }}>No</option>
        </select>
    </td>
</tr>
<tr>
    <th>Standard Matrix Attachment</th>
    <td><input type="file" name="pwd_standard_matrix"></td>
</tr>
<tr>
    <th>Actions taken for PWD accessibility</th>
    <td><textarea name="pwd_actions" rows="6">{{ $oldValue('pwd_actions') }}</textarea></td>
</tr>
<tr>
    <th>Challenges serving PWDs</th>
    <td><textarea name="pwd_challenges" rows="6">{{ $oldValue('pwd_challenges') }}</textarea></td>
</tr>
<tr>
    <th>Future plans for ICT inclusivity</th>
    <td><textarea name="pwd_future_plans" rows="6">{{ $oldValue('pwd_future_plans') }}</textarea></td>
</tr>


            </tbody>
        </table>

        <div class="cak-section-title">13. Environmental Sustainability Compliance</div>
        <table class="cak-form-table">
            <tbody>
                <tr><th>E-waste collection initiatives / take-back mechanisms</th><td><textarea name="ewaste_initiatives" rows="5">{{ $oldValue('ewaste_initiatives') }}</textarea></td></tr>
                <tr><th>Carbon footprint reduction initiatives</th><td><textarea name="carbon_initiatives" rows="5">{{ $oldValue('carbon_initiatives') }}</textarea></td></tr>
                <tr><th>EMCA Waste Management adherence status</th><td><textarea name="emca_status" rows="5">{{ $oldValue('emca_status') }}</textarea></td></tr>
            </tbody>
        </table>

        <div class="cak-section-title">14. Comments / Suggestions</div>
        <table class="cak-form-table">
            <tbody><tr><td><textarea name="comments" rows="5">{{ $oldValue('comments') }}</textarea></td></tr></tbody>
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
<img id="stamp_preview"
     src="{{ !empty($attachments['company_stamp']) ? asset('storage/' . $attachments['company_stamp']) : '' }}"
     style="max-height:100px; max-width:100%; margin-top:8px; {{ empty($attachments['company_stamp']) ? 'display:none;' : '' }}">

                    </td>
                </tr>
                <tr><th>Title</th><td><input type="text" name="submitter_title" value="{{ $oldValue('submitter_title') }}"></td></tr>
                <tr><th>Date</th><td><input type="date" name="submitter_date" value="{{ $oldValue('submitter_date', date('Y-m-d')) }}"></td></tr>
                <tr>
                    <th>Signature</th>
                    <td>
                        <input type="file" name="signature" id="signature" accept="image/png,image/jpeg,image/jpg">
<img id="signature_preview"
     src="{{ !empty($attachments['signature']) ? asset('storage/' . $attachments['signature']) : '' }}"
     style="max-height:80px; margin-top:8px; {{ empty($attachments['signature']) ? 'display:none;' : '' }}">

                    </td>
                </tr>
            </tbody>
        </table>

        <div class="text-center my-4"><strong>THANK YOU FOR COMPLETING THE FORM</strong></div>

        <div class="official-header">FOR OFFICIAL USE ONLY – DO NOT FILL BELOW THIS LINE</div>
        <p><strong>These returns have been :)</strong></p>

        <table class="cak-form-table official-table">
            <thead>
                <tr>
                    <th style="width:12%;"></th>
                    <th style="width:28%;" class="text-center">Checked By:</th>
                    <th style="width:28%;" class="text-center">Verified by:</th>
                    <th style="width:32%;" class="text-center">Approved ☐ &nbsp;&nbsp; Rejected ☐<br><small>(Tick as appropriate)</small></th>
                </tr>
            </thead>
            <tbody>
                <tr><th>Name</th><td></td><td></td><td></td></tr>
                <tr><th>Title</th><td></td><td></td><td></td></tr>
                <tr><th>Signature</th><td></td><td></td><td></td></tr>
                <tr><th>Date</th><td></td><td></td><td></td></tr>
            </tbody>
        </table>

        <p class="mt-3"><strong>A COMPLIANCE CERTIFICATE WILL NOT BE ISSUED IF THE COMPLIANCE RETURNS ARE SUBMITTED LATE OR REJECTED BY THE AUTHORITY</strong></p>

        <div class="submit-buttons">
            <button type="submit" name="submit" value="submit" class="btn btn-kp-primary btn-lg">Submit</button>

{{-- <button type="button"
        class="btn btn-kp-warning btn-lg"
        id="autofillAspBtn"
        data-url="{{ route('asp.autofill-record-2') }}">
    Auto Fill
</button> --}}
<button type="button"
        class="btn autofill-kp-btn autofill-asp-btn"
        data-url="{{ route('asp.autofill-record-2') }}"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Auto fill the form and make changes where necessary and save draft or submit">
    <i class="fas fa-magic"></i> Auto Fill
</button>



            <button type="submit"
                name="save_draft"
                value="1"
                class="btn btn-secondary btn-lg"
                formnovalidate>
                Save Draft
         </button>
            <button type="button" class="btn btn-info btn-lg" onclick="window.print()">Print Preview</button>
            <a href="{{ route('asp.index') }}" class="btn btn-dark btn-lg">Back</a>
        </div>
        <input type="hidden" name="asp_form_complete" value="1">

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
    const tables = [
        'international_traffic',
        'roaming_outbound',
        'roaming_inbound'
    ];

    function numberValue(input) {
        const value = parseFloat(input.value);
        return Number.isFinite(value) ? value : 0;
    }

    function calculateTable(tableName) {
        const fields = new Set();

        document
            .querySelectorAll(`[data-table="${tableName}"][data-row]:not([data-row="total"])`)
            .forEach(input => fields.add(input.dataset.field));

        fields.forEach(field => {
            let total = 0;

            document
                .querySelectorAll(`[data-table="${tableName}"][data-field="${field}"]:not([data-row="total"])`)
                .forEach(input => {
                    total += numberValue(input);
                });

            const totalInput = document.querySelector(
                `[data-table="${tableName}"][data-row="total"][data-field="${field}"]`
            );

            if (totalInput) {
                totalInput.value = total;
            }
        });
    }

    function calculateAll() {
        tables.forEach(calculateTable);
    }

    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('calc-field')) {
            calculateTable(event.target.dataset.table);
        }
    });

    calculateAll();
});
</script>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function numberValue(input) {
        const value = parseFloat(input.value);
        return Number.isFinite(value) ? value : 0;
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

            document
                .querySelectorAll(`.complaint-field[data-field="${field}"]:not([data-row="total"])`)
                .forEach(function (input) {
                    total += numberValue(input);
                });

            const totalInput = document.querySelector(
                `.complaint-field[data-row="total"][data-field="${field}"]`
            );

            if (totalInput) {
                totalInput.value = total;
            }
        });
    }

    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('complaint-field')) {
            calculateComplaintTotals();
        }
    });

    calculateComplaintTotals();
});
</script>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function numberValue(input) {
        const value = parseFloat(input.value);
        return Number.isFinite(value) ? value : 0;
    }

    function calculateSpeedTotals() {
        const bands = [
            'lt_256',
            'kbps_256_2mbps',
            'mbps_2_10',
            'mbps_10_30',
            'mbps_30_100',
            'mbps_100_1gbps',
            'gt_1gbps'
        ];

        bands.forEach(function (band) {
            let total = 0;

            document
                .querySelectorAll(`.speed-field[data-band="${band}"]:not([data-row="total"])`)
                .forEach(function (input) {
                    total += numberValue(input);
                });

            const totalInput = document.querySelector(
                `.speed-field[data-row="total"][data-band="${band}"]`
            );

            if (totalInput) {
                totalInput.value = total;
            }
        });
    }

    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('speed-field')) {
            calculateSpeedTotals();
        }
    });

    calculateSpeedTotals();
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function numberValue(value) {
        const parsed = parseInt(value, 10);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function calculateStaffTotals() {
        let localM = 0;
        let localF = 0;
        let expM = 0;
        let expF = 0;

        document.querySelectorAll('.staff-data-row').forEach(function (row) {
            const rowLocalM = numberValue(row.querySelector('[name*="[local_m]"]')?.value);
            const rowLocalF = numberValue(row.querySelector('[name*="[local_f]"]')?.value);
            const rowExpM = numberValue(row.querySelector('[name*="[exp_m]"]')?.value);
            const rowExpF = numberValue(row.querySelector('[name*="[exp_f]"]')?.value);

            const rowTotal = rowLocalM + rowLocalF + rowExpM + rowExpF;

            const rowTotalInput = row.querySelector('.staff-row-total');
            if (rowTotalInput) {
                rowTotalInput.value = rowTotal;
            }

            localM += rowLocalM;
            localF += rowLocalF;
            expM += rowExpM;
            expF += rowExpF;
        });

        document.getElementById('staff_total_local_m').value = localM;
        document.getElementById('staff_total_local_f').value = localF;
        document.getElementById('staff_total_exp_m').value = expM;
        document.getElementById('staff_total_exp_f').value = expF;
    }

    document.querySelectorAll('.staff-input').forEach(function (input) {
        input.addEventListener('input', calculateStaffTotals);
        input.addEventListener('change', calculateStaffTotals);
    });

    calculateStaffTotals();
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.autofill-asp-btn');

    if (!buttons.length) return;

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

        if (!fields.length) return;

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

    buttons.forEach(function (button) {
        button.addEventListener('click', async function () {
            const url = button.dataset.url;
            const originalText = button.textContent;

            buttons.forEach(btn => {
                btn.disabled = true;
                btn.textContent = 'Filling...';
            });

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to load ASP data.');
                }

                const data = await response.json();
                const flatData = flattenObject(data);

                Object.entries(flatData).forEach(([name, value]) => {
                    setField(name, value);
                });

                document.dispatchEvent(new Event('input', { bubbles: true }));

                buttons.forEach(btn => {
                    btn.textContent = 'Auto Filled';
                });
            } catch (error) {
                alert(error.message || 'Failed to auto-fill form.');

                buttons.forEach(btn => {
                    btn.textContent = originalText;
                });
            } finally {
                buttons.forEach(btn => {
                    btn.disabled = false;
                });
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

