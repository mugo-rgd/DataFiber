@if($customers->count() > 0)
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Assigned Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->company_name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </td>
                    <td>{{ $customer->assigned_at ? $customer->assigned_at->format('M d, Y') : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-4">
        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
        <p class="text-muted">No customers assigned to this manager.</p>
    </div>
@endif
