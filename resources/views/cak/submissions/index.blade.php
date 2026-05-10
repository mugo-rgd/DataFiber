@extends('layouts.app')

@section('content')
<div class="container">
    <h4>All Submissions</h4>

    <table class="table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Licensee</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach($records as $r)
            <tr>
                <td>{{ $r->type }}</td>
                <td>{{ $r->licensee_name }}</td>
                <td>{{ $r->status }}</td>
                <td><a href="{{ route($r->type.'.show', $r->id) }}">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
