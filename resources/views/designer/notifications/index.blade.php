{{-- resources/views/designer/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-bell me-2"></i>Notifications
                </h1>
                <button class="btn btn-outline-secondary" id="markAllReadBtn">
                    <i class="fas fa-check-double me-2"></i>Mark All as Read
                </button>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }} fa-2x text-{{ $notification->data['color'] ?? 'info' }}"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->data['message'] ?? '' }}</p>
                                    <div class="mt-2">
                                        <a href="{{ $notification->data['action_url'] ?? '#' }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        @if(!$notification->read_at)
                                            <button class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="{{ $notification->id }}">
                                                <i class="fas fa-check me-1"></i>Mark as Read
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Notifications</h5>
                    <p class="text-muted">You don't have any notifications yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
    fetch('{{ route("designer.notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        location.reload();
    });
});

document.querySelectorAll('.mark-read-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`/designer/notifications/${id}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            location.reload();
        });
    });
});
</script>
@endsection
