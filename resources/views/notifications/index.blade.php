@extends('layouts.app')

@section('title', 'All Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i>All Notifications
                    </h5>
                    <div>
                        @php
                            $unreadCount = auth()->user()->unreadNotifications->count();
                        @endphp
                        @if($unreadCount > 0)
                            <button class="btn btn-sm btn-kp-success me-2" onclick="markAllAsRead()">
                                <i class="fas fa-check-double"></i> Mark All as Read ({{ $unreadCount }})
                            </button>
                        @endif
                        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-kp-primary">
                            <i class="fas fa-comments"></i> Go to Chat
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group" id="notificationsFullList">
                        @forelse($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $isUnread = is_null($notification->read_at);
                                $isCertificateNotification = isset($data['type']) && $data['type'] === 'conditional_certificate';
                            @endphp
                            <div class="list-group-item list-group-item-action notification-item {{ $isUnread ? 'unread' : 'read' }}"
                                 data-id="{{ $notification->id }}"
                                 style="{{ $isUnread ? 'border-left: 4px solid #0d6efd; background-color: #f8f9ff;' : 'border-left: 4px solid #dee2e6;' }}">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="avatar me-3">
                                            <div class="bg-{{ $isUnread ? 'primary' : 'secondary' }} rounded-circle text-white d-flex align-items-center justify-content-center"
                                                 style="width: 45px; height: 45px; font-size: 18px;">
                                                @if($isCertificateNotification)
                                                    <i class="fas fa-file-contract"></i>
                                                @else
                                                    {{ $data['sender_avatar'] ?? '?' }}
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <h6 class="mb-1 {{ $isUnread ? 'fw-bold' : '' }}">
                                                    @if($isCertificateNotification)
                                                        Conditional Certificate Issued
                                                    @else
                                                        {{ $data['sender_name'] ?? 'System' }}
                                                    @endif
                                                </h6>
                                                @if($isUnread)
                                                    <span class="badge bg-kp-blue">New</span>
                                                @endif
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p class="mb-2 {{ $isUnread ? 'text-dark' : 'text-muted' }}">
                                                @if($isCertificateNotification)
                                                    <strong>{{ $data['certificate_ref'] ?? '' }}</strong><br>
                                                    {{ $data['message'] ?? $data['message_preview'] ?? 'Conditional certificate has been issued for your design request.' }}
                                                @else
                                                    {{ $data['message_preview'] ?? 'New notification' }}
                                                @endif
                                            </p>
                                            <div class="d-flex gap-3">
                                                @if($isCertificateNotification && isset($data['action_url']))
                                                    <a href="{{ $data['action_url'] }}" class="small text-kp-blue text-decoration-none">
                                                        <i class="fas fa-eye"></i> View Certificate
                                                    </a>
                                                @else
                                                    <a href="#" onclick="openChat({{ $data['conversation_id'] ?? 0 }})"
                                                       class="small text-kp-blue text-decoration-none">
                                                        <i class="fas fa-comment"></i> Open Chat
                                                    </a>
                                                @endif
                                                @if($isUnread)
                                                    <a href="#" onclick="event.preventDefault(); markAsRead('{{ $notification->id }}')"
                                                       class="small text-kp-green text-decoration-none">
                                                        <i class="fas fa-check-circle"></i> Mark as Read
                                                    </a>
                                                @else
                                                    <span class="small text-muted">
                                                        <i class="fas fa-check-double"></i> Read {{ $notification->read_at ? \Carbon\Carbon::parse($notification->read_at)->diffForHumans() : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($isUnread)
                                        <span class="badge bg-kp-blue rounded-pill">New</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No notifications</h5>
                                <p class="text-muted mb-3">You're all caught up!</p>
                                <a href="{{ route('chat.index') }}" class="btn btn-kp-primary">
                                    <i class="fas fa-comments"></i> Start Chatting
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if(method_exists($notifications, 'links') && $notifications->hasPages())
                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Option 1: Reload the page
            window.location.reload();

            // Option 2: Update the UI without reload (uncomment if preferred)
            // const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
            // if (notificationItem) {
            //     notificationItem.classList.remove('unread');
            //     notificationItem.classList.add('read');
            //     notificationItem.style.borderLeft = '4px solid #dee2e6';
            //     notificationItem.style.backgroundColor = '';
            //
            //     // Update the New badge
            //     const newBadge = notificationItem.querySelector('.badge.bg-kp-blue');
            //     if (newBadge) newBadge.remove();
            //
            //     // Update unread count in header
            //     updateUnreadCount();
            // }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to mark notification as read. Please try again.');
    });
}

function markAllAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to mark all notifications as read. Please try again.');
    });
}

function openChat(conversationId) {
    if (conversationId && conversationId > 0) {
        window.location.href = `{{ route('chat.index') }}?conversation=${conversationId}`;
    } else {
        window.location.href = '{{ route('chat.index') }}';
    }
}

function updateUnreadCount() {
    fetch('/notifications/unread-count', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.querySelector('.notification-badge');
        const headerButton = document.querySelector('.btn-kp-success');

        if (badge && data.count !== undefined) {
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        }

        if (headerButton && data.count !== undefined) {
            headerButton.innerHTML = `<i class="fas fa-check-double"></i> Mark All as Read (${data.count})`;
            if (data.count === 0 && headerButton.parentElement) {
                headerButton.remove();
            }
        }
    })
    .catch(error => console.error('Error updating count:', error));
}
</script>
@endpush

@push('styles')
<style>
.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    transform: translateX(3px);
    cursor: pointer;
}

.notification-item.unread {
    background-color: #f8f9ff;
    border-left: 4px solid #0066B3;
}

.notification-item.read {
    opacity: 0.85;
}

.avatar {
    flex-shrink: 0;
}
</style>
@endpush
