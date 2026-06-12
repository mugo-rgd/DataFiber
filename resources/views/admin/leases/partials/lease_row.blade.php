<tr class="lease-row" data-lease-id="{{ $lease->id }}" style="font-size: 0.8rem;">
    <td>
        <strong>#{{ $lease->lease_number }}</strong>
        @if($lease->status === 'pending')
            <span class="badge bg-warning text-dark d-block mt-1" style="font-size: 0.6rem;">
                Awaiting Approval
            </span>
        @endif
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="avatar-sm bg-kp-blue rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                 style="width: 24px; height: 24px; font-size: 0.7rem;">
                {{ strtoupper(substr($lease->customer->name ?? '?', 0, 1)) }}
            </div>
            <div>
                <div class="fw-bold" style="font-size: 0.8rem;">
                    {{ $lease->customer->name ?? 'N/A' }}
                </div>
                <small class="text-muted" style="font-size: 0.65rem;">
                    {{ $lease->customer->email ?? 'No email' }}
                </small>
            </div>
        </div>
    </td>
    <td>
        @php
            $accountManager = $lease->customer && $lease->customer->account_manager_id
                ? \App\Models\User::find($lease->customer->account_manager_id)
                : null;
        @endphp
        @if($accountManager)
            <div class="d-flex align-items-center">
                <div class="avatar-sm bg-info rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                     style="width: 22px; height: 22px; font-size: 0.65rem;">
                    {{ strtoupper(substr($accountManager->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size: 0.75rem;">{{ $accountManager->name }}</div>
                    <small class="text-muted" style="font-size: 0.6rem;">{{ $accountManager->email }}</small>
                </div>
            </div>
        @else
            <span class="text-muted" style="font-size: 0.75rem;">
                <i class="fas fa-user-slash me-1"></i>Unassigned
            </span>
        @endif
    </td>
    <td>
        <span class="badge bg-light text-dark">{{ ucfirst($lease->service_type) }}</span>
    </td>
    <td>
        @if($lease->service_type == 'colocation')
            <small>{{ $lease->host_location ?? 'N/A' }}</small>
        @else
            <small>{{ $lease->start_location ?? 'N/A' }} → {{ $lease->end_location ?? 'N/A' }}</small>
        @endif
    </td>
    <td>
        <strong>{{ $lease->currency ?? 'KES' }} {{ number_format($lease->monthly_cost ?? 0, 2) }}</strong>
    </td>
    <td>
        <span class="badge bg-{{ $lease->status === 'active' ? 'success' : ($lease->status === 'pending' ? 'warning' : 'secondary') }}">
            {{ ucfirst($lease->status) }}
        </span>
    </td>
    <td>
        <small>{{ $lease->created_at instanceof \Carbon\Carbon ? $lease->created_at->format('M d, Y') : \Carbon\Carbon::parse($lease->created_at)->format('M d, Y') }}</small>
    </td>
    <td class="text-center">
        <div class="btn-group btn-group-xs" style="gap: 2px;">
            <a href="{{ route('admin.leases.show', $lease) }}" class="btn btn-outline-primary btn-xs" title="View">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </td>
</tr>
