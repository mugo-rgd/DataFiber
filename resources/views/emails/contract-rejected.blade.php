@extends('emails.layouts.app')

@section('content')
<h2>Contract Revision Required</h2>

<p>Dear {{ $customer->name }},</p>

<p>Your contract <strong>{{ $contract->contract_number }}</strong> requires revisions before it can be approved.</p>

@if($rejectionReason)
<p><strong>Reason for Revision:</strong><br>
{{ $rejectionReason }}</p>
@endif

<p>Our team will review and update the contract. You will be notified once the revised contract is ready for your review.</p>

<p>We apologize for any inconvenience and appreciate your understanding.</p>

<p>Best regards,<br>KPLC Dark Fibre Team</p>
@endsection
