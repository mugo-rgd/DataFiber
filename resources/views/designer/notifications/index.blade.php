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
                        @php
                            $notificationData = $notification->data;
                            $actionUrl = '#';

                            // Get certificate ID from notification data
                            $certificateId = $notificationData['certificate_id'] ?? null;
                            $certificateRef = $notificationData['certificate_ref'] ?? null;
                            $isDesignerNotification = $notificationData['is_designer_notification'] ?? false;

                            // Build the correct URL for designer
                            if ($certificateId) {
                                $actionUrl = route('designer.certificates.conditional.show', $certificateId);
                            } elseif (isset($notificationData['action_url'])) {
                                $actionUrl = $notificationData['action_url'];
                            }

                            $icon = $notificationData['icon'] ?? 'bell';
                            $color = $notificationData['color'] ?? 'info';
                            $title = $notificationData['title'] ?? 'Notification';
                            $message = $notificationData['message_preview'] ?? ($notificationData['message'] ?? '');

                            // For certificate notifications, use certificate icon
                            if ($certificateId) {
                                $icon = 'file-certificate';
                                $color = 'success';
                            }
                        @endphp

                        <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'bg-light border-start border-4 border-primary' }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background-color: rgba(0,102,179,0.1);">
                                        <i class="fas fa-{{ $icon }} fa-lg text-{{ $color }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $title }}</h6>
                                            <p class="mb-1 text-muted small">{{ $message }}</p>
                                        </div>
                                        <small class="text-muted ms-2">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>

                                    @if($certificateId)
                                        <div class="mt-2 mb-2">
                                            <span class="badge bg-info">
                                                <i class="fas fa-hashtag me-1"></i> Certificate ID: {{ $certificateId }}
                                            </span>
                                            @if($certificateRef)
                                                <span class="badge bg-secondary ms-1">
                                                    <i class="fas fa-barcode me-1"></i> {{ $certificateRef }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mt-2">
                                        {{-- @if($actionUrl && $actionUrl != '#')
                                            <a href="{{ $actionUrl }}" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </a>
                                        @endif --}}

                                        @if($certificateId)
    <a href="{{ route('ictengineer.certificates.conditional.download', $certificateId) }}"
       class="btn btn-sm btn-primary me-2">
        <i class="fas fa-download me-1"></i>Download Certificate
    </a>
@endif

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
