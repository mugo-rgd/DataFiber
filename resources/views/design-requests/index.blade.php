<table class="table">
    <thead>
        <tr>
            <th>Request ID</th>
            <th>Customer</th>
            <th>Route Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($designRequests as $request)
        <tr>
            <td>{{ $request->request_number }}</td>
            <td>{{ $request->customer->name ?? 'N/A' }}</td>
            <td>{{ $request->route_name }}</td>
            <td>
                <span class="badge badge-{{ getStatusColor($request->status) }}">
                    {{ $request->status }}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('design-requests.show', $request->id) }}"
                       class="btn btn-info" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('design-requests.documents', $request->id) }}"
                       class="btn btn-primary" title="View Documents">
                        <i class="fas fa-file-alt"></i> Documents
                    </a>
                    @if($request->documents_count > 0)
                        <span class="badge badge-danger badge-pill"
                              style="position: absolute; top: -5px; right: -5px;">
                            {{ $request->documents_count }}
                        </span>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
