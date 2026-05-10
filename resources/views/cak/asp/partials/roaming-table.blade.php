<table class="cak-form-table">
    <thead>
        <tr>
            <th>Country with Roaming Agreement</th>
            <th>Voice Incoming</th>
            <th>Voice Outgoing</th>
            <th>SMS Incoming</th>
            <th>SMS Outgoing</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        @foreach($countries as $key => $label)
        @php
            // SAFELY retrieve each value - prevent array errors
            $voiceIn = old("$tableKey.$key.voice_in", data_get($data, "$tableKey.$key.voice_in", 0));
            $voiceOut = old("$tableKey.$key.voice_out", data_get($data, "$tableKey.$key.voice_out", 0));
            $smsIn = old("$tableKey.$key.sms_in", data_get($data, "$tableKey.$key.sms_in", 0));
            $smsOut = old("$tableKey.$key.sms_out", data_get($data, "$tableKey.$key.sms_out", 0));
            $dataVal = old("$tableKey.$key.data", data_get($data, "$tableKey.$key.data", 0));

            // CRITICAL FIX: If any value is an array, convert to 0
            $voiceIn = is_array($voiceIn) ? 0 : $voiceIn;
            $voiceOut = is_array($voiceOut) ? 0 : $voiceOut;
            $smsIn = is_array($smsIn) ? 0 : $smsIn;
            $smsOut = is_array($smsOut) ? 0 : $smsOut;
            $dataVal = is_array($dataVal) ? 0 : $dataVal;
        @endphp
        <tr>
            <th>{{ $label }}</th>
            <td>
                <input type="number"
                       class="calc-field roaming-field {{ $key === 'total' ? 'total-field' : '' }}"
                       data-table="{{ $tableKey }}"
                       data-row="{{ $key }}"
                       data-field="voice_in"
                       name="{{ $tableKey }}[{{ $key }}][voice_in]"
                       value="{{ $voiceIn }}"
                       {{ $key === 'total' ? 'readonly' : '' }}>
            </td>
            <td>
                <input type="number"
                       class="calc-field roaming-field {{ $key === 'total' ? 'total-field' : '' }}"
                       data-table="{{ $tableKey }}"
                       data-row="{{ $key }}"
                       data-field="voice_out"
                       name="{{ $tableKey }}[{{ $key }}][voice_out]"
                       value="{{ $voiceOut }}"
                       {{ $key === 'total' ? 'readonly' : '' }}>
            </td>
            <td>
                <input type="number"
                       class="calc-field roaming-field {{ $key === 'total' ? 'total-field' : '' }}"
                       data-table="{{ $tableKey }}"
                       data-row="{{ $key }}"
                       data-field="sms_in"
                       name="{{ $tableKey }}[{{ $key }}][sms_in]"
                       value="{{ $smsIn }}"
                       {{ $key === 'total' ? 'readonly' : '' }}>
            </td>
            <td>
                <input type="number"
                       class="calc-field roaming-field {{ $key === 'total' ? 'total-field' : '' }}"
                       data-table="{{ $tableKey }}"
                       data-row="{{ $key }}"
                       data-field="sms_out"
                       name="{{ $tableKey }}[{{ $key }}][sms_out]"
                       value="{{ $smsOut }}"
                       {{ $key === 'total' ? 'readonly' : '' }}>
            </td>
            <td>
                <input type="number"
                       class="calc-field roaming-field {{ $key === 'total' ? 'total-field' : '' }}"
                       data-table="{{ $tableKey }}"
                       data-row="{{ $key }}"
                       data-field="data"
                       name="{{ $tableKey }}[{{ $key }}][data]"
                       value="{{ $dataVal }}"
                       {{ $key === 'total' ? 'readonly' : '' }}>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
