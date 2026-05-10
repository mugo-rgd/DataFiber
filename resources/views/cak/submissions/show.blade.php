@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Submission Details</h4>

    <p><strong>Licensee:</strong> {{ $record->licensee_name }}</p>
    <p><strong>Status:</strong> {{ $record->status }}</p>

    <a href="{{ route($type.'.print', $record->id) }}" class="btn btn-info">Print PDF</a>
</div>
@endsection
