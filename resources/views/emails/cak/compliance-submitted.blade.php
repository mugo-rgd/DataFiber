<p>Dear CAK Compliance Team,</p>

<p>
    Please find attached the {{ $type }} Compliance Return for
    <strong>{{ $form->licensee_name }}</strong>.
</p>

<p>
    <strong>License No:</strong> {{ $form->license_no }}<br>
    <strong>Financial Year:</strong> {{ $form->financial_year }}<br>
    <strong>Quarter:</strong> {{ $form->quarter }}
</p>

<p>Regards,<br>{{ config('app.name') }}</p>
