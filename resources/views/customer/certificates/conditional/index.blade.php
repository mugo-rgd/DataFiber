@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Conditional Certificates</h3>
                </div>
                <div class="card-body">
                    @if($certificates->isEmpty())
                        <div class="alert alert-info">No conditional certificates found.</div>
                    @else
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Reference Number</th>
                                    <th>Link Name</th>
                                    <th>Site A</th>
                                    <th>Site B</th>
                                    <th>Issue Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($certificates as $cert)
                                <tr>
                                    <td>{{ $cert->ref_number }}</td>
                                    <td>{{ $cert->link_name }}</td>
                                    <td>{{ $cert->site_a }}</td>
                                    <td>{{ $cert->site_b }}</td>
                                    <td>{{ $cert->certificate_date }}</td>
                                    <td>
                                        <a href="{{ route('customer.certificates.conditional.show', $cert->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('customer.certificates.conditional.preview', $cert->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Preview
                                        </a>
                                        <a href="{{ route('customer.certificates.conditional.download', $cert->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $certificates->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
