<div style="text-align:center">
    <h2>COMPLIANCE CERTIFICATE</h2>

    <p>This certifies that</p>

    <h3>{{ $record->licensee_name }}</h3>

    <p>has complied with CAK requirements for</p>

    <p>{{ $record->financial_year }} - {{ $record->quarter }}</p>

    <p>Date: {{ now()->format('Y-m-d') }}</p>
</div>
