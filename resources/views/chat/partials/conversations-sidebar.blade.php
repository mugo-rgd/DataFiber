<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Messages</h5>
        <a href="{{ route('chat.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @foreach($conversations as $conversation)
                @php
                    $otherUser = $conversation->users->first();
                    $unreadCount = $conversation->unreadCountForUser(auth()->id());
                    $lastMessage = $conversation->messages()->latest()->first();
                @endphp
                <a href="{{ route('chat.show', $conversation->id) }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $conversation->id == request()->route('conversation') ? 'active' : '' }} {{ $unreadCount > 0 ? 'bg-light' : '' }}">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                 style="width: 40px; height: 40px;">
                                {{ $otherUser ? strtoupper(substr($otherUser->name, 0, 1)) : '?' }}
                            </div>
                        </div>
                        <div class="text-truncate" style="max-width: 150px;">
                            <h6 class="mb-0">{{ $otherUser ? $otherUser->name : 'Unknown User' }}</h6>
                            <small class="text-muted">
                                @if($lastMessage)
                                    {{ Str::limit($lastMessage->body, 20) }}
                                @else
                                    No messages
                                @endif
                            </small>
                        </div>
                    </div>
                    @if($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
