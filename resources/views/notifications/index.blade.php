@extends('layouts.app')

@section('title', 'All Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i>All Notifications
                    </h5>
                    <div>
                        @php
                            $unreadCount = auth()->user()->unreadNotifications->count();
                        @endphp
                        @if($unreadCount > 0)
                            <button class="btn btn-sm btn-success me-2" onclick="markAllAsRead()">
                                <i class="fas fa-check-double"></i> Mark All as Read ({{ $unreadCount }})
                            </button>
                        @endif
                        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-primary">
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
                            @endphp
                            <div class="list-group-item list-group-item-action notification-item {{ $isUnread ? 'unread' : 'read' }}"
                                 data-id="{{ $notification->id }}"
                                 style="{{ $isUnread ? 'border-left: 4px solid #0d6efd; background-color: #f8f9ff;' : 'border-left: 4px solid #dee2e6;' }}">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="avatar me-3">
                                            <div class="bg-{{ $isUnread ? 'primary' : 'secondary' }} rounded-circle text-white d-flex align-items-center justify-content-center"
                                                 style="width: 45px; height: 45px; font-size: 18px;">
                                                {{ $data['sender_avatar'] ?? '?' }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <h6 class="mb-1 {{ $isUnread ? 'fw-bold' : '' }}">
                                                    {{ $data['sender_name'] ?? 'Someone' }}
                                                </h6>
                                                @if($isUnread)
                                                    <span class="badge bg-primary ms-2">New</span>
                                                @endif
                                                <small class="text-muted ms-2">
                                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p class="mb-2 {{ $isUnread ? 'text-dark' : 'text-muted' }}">
                                                {{ $data['message_preview'] ?? 'New message' }}
                                            </p>
                                            <div class="d-flex gap-3">
                                                <a href="#" onclick="openChat({{ $data['conversation_id'] ?? 0 }})"
                                                   class="small text-primary text-decoration-none">
                                                    <i class="fas fa-comment"></i> Open Chat
                                                </a>
                                                @if($isUnread)
                                                    <a href="#" onclick="markAsRead('{{ $notification->id }}')"
                                                       class="small text-success text-decoration-none">
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
                                        <span class="badge bg-primary rounded-pill">New</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No notifications</h5>
                                <p class="text-muted mb-3">You're all caught up!</p>
                                <a href="{{ route('chat.index') }}" class="btn btn-primary">
                                    <i class="fas fa-comments"></i> Start Chatting
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if(method_exists($notifications, 'links'))
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(() => {
        window.location.reload();
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(() => {
        window.location.reload();
    })
    .catch(error => console.error('Error:', error));
}

function openChat(conversationId) {
    if (conversationId && conversationId > 0) {
        window.location.href = `{{ route('chat.index') }}?conversation=${conversationId}`;
    }
}
</script>
@endpush
