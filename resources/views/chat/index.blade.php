@extends('layouts.app')

@section('title', 'Messages - Lease Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar with conversations -->
        <div class="col-md-4 col-lg-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Messages</h5>
                    <button onclick="showNewChatModal()" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> New Chat
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="conversationsList">
                        @forelse($conversations as $conversation)
                            @php
                                $otherUser = null;
                                if (isset($conversation->other_participant)) {
                                    $otherUser = $conversation->other_participant;
                                } elseif ($conversation->participants) {
                                    $otherUser = $conversation->participants->firstWhere('id', '!=', auth()->id());
                                }
                                $unreadCount = $conversation->unread_count ?? 0;
                                $userName = $otherUser->name ?? ($conversation->other_user_name ?? 'Unknown User');
                                $userRole = $otherUser->role ?? ($conversation->other_user_role ?? '');
                                $userEmail = $otherUser->email ?? ($conversation->other_user_email ?? '');
                            @endphp

                            <a href="#"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center conversation-item {{ $unreadCount > 0 ? 'bg-light' : '' }}"
                               data-id="{{ $conversation->id }}">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px;">
                                           {{ substr($userName, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $userName }}</h6>
                                        <small class="text-muted">
                                            @if($conversation->lastMessage)
                                                @if($conversation->lastMessage->type === 'file')
                                                    📎 {{ Str::limit($conversation->lastMessage->file_name, 30) }}
                                                @else
                                                    {{ Str::limit($conversation->lastMessage->body, 30) }}
                                                @endif
                                            @else
                                                No messages yet
                                            @endif
                                        </small>
                                        <br>
                                        <small class="text-muted">{{ $userRole }}</small>
                                    </div>
                                </div>
                                @if($unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill unread-badge">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted">No conversations yet</p>
                                <button onclick="showNewChatModal()" class="btn btn-primary">Start a Chat</button>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if(isset($conversations) && method_exists($conversations, 'links'))
                <div class="card-footer" id="pagination">
                    {{ $conversations->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Main chat area -->
        <div class="col-md-8 col-lg-9" id="chatMainArea">
            <!-- Empty state - no conversation selected -->
            <div class="card h-100" id="noChatSelected">
                <div class="card-body text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <h4>Select a conversation</h4>
                        <p class="mb-0">Choose a chat from the sidebar or start a new one</p>
                    </div>
                </div>
            </div>

            <!-- Chat area (hidden by default) -->
            <div class="card h-100" id="chatArea" style="display: none;">
                <div class="card-header bg-white d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary d-md-none me-2" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="d-flex align-items-center flex-grow-1" id="chatHeader">
                        <div class="avatar me-3">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                 style="width: 45px; height: 45px; font-size: 18px;" id="chatAvatar">
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0" id="chatUserName"></h5>
                            <small class="text-muted" id="chatUserRole"></small>
                            <small class="text-muted ms-2" id="chatUserEmail"></small>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="height: 500px; overflow-y: auto;" id="messagesContainer">
                    <div id="messages"></div>
                    <div id="loadingMore" style="display: none;" class="text-center py-2">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2">Loading more messages...</span>
                    </div>
                </div>

                <div class="card-footer">
                    <!-- File Upload Preview Area -->
                    <div id="filePreviewArea" class="mb-2" style="display: none;">
                        <div class="alert alert-info mb-2 p-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file me-2"></i>
                                <span id="selectedFileName"></span>
                                <small class="text-muted ms-2" id="selectedFileSize"></small>
                            </div>
                            <button type="button" class="btn-close" onclick="clearFileSelection()"></button>
                        </div>
                    </div>

                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" onclick="triggerFileUpload()" title="Attach file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="file" id="fileInput" style="display: none;" onchange="handleFileSelect(event)">
                        <textarea
                            class="form-control"
                            id="messageInput"
                            placeholder="Type a message..."
                            rows="1"
                            style="resize: none;"
                            onkeydown="handleMessageKeydown(event)"
                        ></textarea>
                        <button class="btn btn-primary" onclick="sendMessage()" id="sendButton">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                    <div class="small text-muted mt-2">
                        <i class="fas fa-info-circle"></i> Supported files: Images, PDF, DOC, DOCX, XLS, XLSX, ZIP (Max: 10MB)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newChatModalLabel">
                    <i class="fas fa-comment me-2"></i>Start New Chat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Search Users</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text"
                               class="form-control"
                               id="searchUsersInput"
                               placeholder="Type name or email..."
                               onkeyup="searchUsers(this.value)"
                               autocomplete="off">
                    </div>
                    <small class="text-muted">Type at least 2 characters to search</small>
                </div>

                <div id="searchResults" class="list-group" style="max-height: 350px; overflow-y: auto;"></div>

                <div class="mt-3">
                    <small class="text-muted d-block mb-2">Quick filter by role:</small>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-primary cursor-pointer" onclick="filterByRole('customer')">Customers</span>
                        <span class="badge bg-info cursor-pointer" onclick="filterByRole('account_manager')">Account Managers</span>
                        <span class="badge bg-success cursor-pointer" onclick="filterByRole('finance')">Finance</span>
                        <span class="badge bg-warning cursor-pointer" onclick="filterByRole('ict_engineer')">ICT Engineers</span>
                        <span class="badge bg-secondary cursor-pointer" onclick="filterByRole('technician')">Technicians</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ==================== STATE MANAGEMENT ====================
let currentConversation = null;
let currentPage = 1;
let messagesPagination = null;
let loadingMessages = false;
let hasMoreMessages = true;
let selectedFile = null;

let currentUser = {
    id: {{ auth()->id() }},
    name: "{{ auth()->user()->name }}",
    email: "{{ auth()->user()->email }}"
};

// ==================== INITIALIZATION ====================
function autoResizeTextarea() {
    const textarea = document.getElementById('messageInput');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Chat initialized');
    attachConversationClickHandlers();

    const urlParams = new URLSearchParams(window.location.search);
    const conversationId = urlParams.get('conversation');
    if (conversationId) {
        setTimeout(() => {
            openConversation(conversationId);
        }, 100);
    }

    setupInfiniteScroll();
    autoResizeTextarea();
    updateNotificationBadge();
    setInterval(updateNotificationBadge, 30000);
});

function attachConversationClickHandlers() {
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const conversationId = this.dataset.id;
            if (conversationId) {
                openConversation(conversationId);
            }
        });
    });
}

// ==================== FILE UPLOAD FUNCTIONS ====================
function triggerFileUpload() {
    document.getElementById('fileInput').click();
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('File size exceeds 10MB limit. Please choose a smaller file.');
        clearFileSelection();
        return;
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp',
                          'application/pdf', 'application/msword',
                          'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                          'application/vnd.ms-excel',
                          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                          'application/zip', 'application/x-zip-compressed'];

    if (!allowedTypes.includes(file.type) && !file.name.match(/\.(jpg|jpeg|png|gif|webp|pdf|doc|docx|xls|xlsx|zip)$/i)) {
        alert('File type not supported. Please upload images, PDF, DOC, DOCX, XLS, XLSX, or ZIP files.');
        clearFileSelection();
        return;
    }

    selectedFile = file;

    const previewArea = document.getElementById('filePreviewArea');
    const fileNameSpan = document.getElementById('selectedFileName');
    const fileSizeSpan = document.getElementById('selectedFileSize');

    fileNameSpan.textContent = file.name;
    fileSizeSpan.textContent = formatFileSize(file.size);
    previewArea.style.display = 'block';
}

function clearFileSelection() {
    selectedFile = null;
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreviewArea').style.display = 'none';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(mimeType) {
    if (!mimeType) return 'fa-file';
    const type = mimeType.toLowerCase();
    if (type.includes('image')) return 'fa-file-image';
    if (type.includes('pdf')) return 'fa-file-pdf';
    if (type.includes('word') || type.includes('document')) return 'fa-file-word';
    if (type.includes('excel') || type.includes('sheet')) return 'fa-file-excel';
    if (type.includes('zip') || type.includes('rar')) return 'fa-file-archive';
    return 'fa-file';
}

// ==================== SHOW NEW CHAT MODAL ====================
function showNewChatModal() {
    const modal = new bootstrap.Modal(document.getElementById('newChatModal'));
    modal.show();
    document.getElementById('searchUsersInput').value = '';
    document.getElementById('searchResults').innerHTML = '';
    setTimeout(() => {
        document.getElementById('searchUsersInput').focus();
    }, 500);
}

// ==================== OPEN CONVERSATION ====================
function openConversation(conversationId) {
    console.log('Opening conversation:', conversationId);

    updateUrlWithConversation(conversationId);
    document.getElementById('chatArea').style.display = 'block';
    document.getElementById('noChatSelected').style.display = 'none';

    document.getElementById('messages').innerHTML = `
        <div class="text-center text-muted py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Loading messages...</p>
        </div>
    `;

    if (window.innerWidth <= 768) {
        document.querySelector('.col-md-4').style.display = 'none';
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch(`/chat/${conversationId}/messages`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Conversation data loaded:', data);

        if (!data.success) {
            throw new Error(data.error || 'Failed to load conversation');
        }

        currentConversation = data.conversation;
        messagesPagination = data.messages;
        hasMoreMessages = messagesPagination?.current_page < messagesPagination?.last_page;

        const otherUser = data.other_user;
        updateChatHeader(otherUser);
        renderMessages(messagesPagination?.data || []);
        updateActiveConversation(conversationId);
        markMessagesAsRead(conversationId);
        scrollToBottom();
        document.getElementById('messageInput').focus();
    })
    .catch(error => {
        console.error('Error loading conversation:', error);
        document.getElementById('messages').innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-exclamation-circle fa-2x mb-3 text-danger"></i>
                <p>Failed to load messages.</p>
                <p class="small text-muted">Error: ${error.message}</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="openConversation(${conversationId})">
                    Try Again
                </button>
            </div>
        `;
    });
}

function updateChatHeader(user) {
    if (!user) return;
    document.getElementById('chatUserName').textContent = user.name || 'Unknown User';
    document.getElementById('chatUserRole').textContent = user.role || '';
    document.getElementById('chatUserEmail').textContent = user.email || '';
    const avatar = document.getElementById('chatAvatar');
    if (avatar) {
        avatar.textContent = getInitials(user.name);
    }
}

// ==================== RENDER MESSAGES ====================
function renderMessages(messages) {
    const container = document.getElementById('messages');
    if (!messages || messages.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-comment-dots fa-3x mb-3"></i>
                <p>No messages yet. Send your first message!</p>
            </div>
        `;
        return;
    }

    const sortedMessages = [...messages].reverse();
    container.innerHTML = sortedMessages.map(msg => {
        // Check if message has a file attachment
        if ((msg.type === 'file' || msg.type === 'image') && msg.attachment_path) {
            const fileIcon = getFileIcon(msg.mime_type);
            const fileName = msg.attachment_name || 'File';
            const fileSize = msg.file_size;
            const fileId = msg.id;

            return `
                <div class="message mb-3 ${msg.user_id === currentUser.id ? 'text-end' : 'text-start'}" data-id="${msg.id}">
                    <div class="d-inline-block ${msg.user_id === currentUser.id ? 'bg-primary text-white' : 'bg-light'}"
                         style="max-width: 70%; padding: 10px 15px; border-radius: 18px; ${msg.user_id === currentUser.id ? 'border-bottom-right-radius: 4px;' : 'border-bottom-left-radius: 4px;'}">
                        <div class="message-file text-center">
                            <i class="fas ${fileIcon} fa-2x mb-2"></i>
                            <div class="message-text">
                                <a href="/chat/download/${fileId}" target="_blank" class="${msg.user_id === currentUser.id ? 'text-white' : 'text-primary'} text-decoration-underline">
                                    ${escapeHtml(fileName)}
                                </a>
                            </div>
                            <div class="message-meta" style="font-size: 11px; margin-top: 5px; ${msg.user_id === currentUser.id ? 'color: rgba(255,255,255,0.8)' : 'color: #999'}">
                                <span>${formatFileSize(fileSize)}</span>
                                <span class="ms-2">${formatTime(msg.created_at)}</span>
                                ${msg.user_id === currentUser.id && msg.read_at ? '<span class="ms-1" title="Read">✓✓</span>' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Text message
            return `
                <div class="message mb-3 ${msg.user_id === currentUser.id ? 'text-end' : 'text-start'}" data-id="${msg.id}">
                    <div class="d-inline-block ${msg.user_id === currentUser.id ? 'bg-primary text-white' : 'bg-light'}"
                         style="max-width: 70%; padding: 10px 15px; border-radius: 18px; ${msg.user_id === currentUser.id ? 'border-bottom-right-radius: 4px;' : 'border-bottom-left-radius: 4px;'}">
                        <div class="message-text">${escapeHtml(msg.body)}</div>
                        <div class="message-meta" style="font-size: 11px; margin-top: 5px; ${msg.user_id === currentUser.id ? 'color: rgba(255,255,255,0.8)' : 'color: #999'}">
                            <span>${formatTime(msg.created_at)}</span>
                            ${msg.user_id === currentUser.id && msg.read_at ? '<span class="ms-1" title="Read">✓✓</span>' : ''}
                        </div>
                    </div>
                </div>
            `;
        }
    }).join('');
}

// ==================== SEND MESSAGE ====================
function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();

    if ((!message && !selectedFile) || !currentConversation) return;

    input.disabled = true;
    const sendButton = document.getElementById('sendButton');
    sendButton.disabled = true;
    sendButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Sending...';

    let formData = new FormData();

    // Always send body even if empty (for file messages)
    formData.append('body', message || '');
    formData.append('type', selectedFile ? 'file' : 'text');

    if (selectedFile) {
        formData.append('file', selectedFile);
    }

    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    // Log what we're sending
    console.log('Sending message:', {
        conversation_id: currentConversation.id,
        has_file: !!selectedFile,
        file_name: selectedFile?.name,
        message_length: message.length
    });

    fetch(`/chat/${currentConversation.id}/messages`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
            // Don't set Content-Type header - let browser set it with boundary for FormData
        },
        body: formData
    })
    .then(async response => {
        const text = await response.text();
        console.log('Response:', response.status, text);

        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            console.error('JSON parse error:', e);
            throw new Error('Invalid response from server');
        }

        if (!response.ok) {
            throw new Error(data.error || 'Failed to send message');
        }
        return data;
    })
    .then(data => {
        const newMessage = data.message || data.data?.message;
        if (newMessage) {
            addMessageToUI(newMessage);
            input.value = '';
            input.style.height = 'auto';
            clearFileSelection();
            const previewText = (newMessage.type === 'file' || newMessage.type === 'image') ? '📎 ' + (newMessage.attachment_name || 'File') : newMessage.body;
            updateConversationLastMessage(currentConversation.id, previewText);
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Failed to send message: ' + error.message);
    })
    .finally(() => {
        input.disabled = false;
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
        input.focus();
    });
}

function addMessageToUI(message) {
    const container = document.getElementById('messages');
    let messageHtml;

    if ((message.type === 'file' || message.type === 'image') && message.attachment_path) {
        const fileIcon = getFileIcon(message.mime_type);
        const fileName = message.attachment_name || 'File';
        const fileSize = message.file_size;
        const fileId = message.id;

        messageHtml = `
            <div class="message mb-3 text-end" data-id="${message.id}">
                <div class="d-inline-block bg-primary text-white"
                     style="max-width: 70%; padding: 10px 15px; border-radius: 18px; border-bottom-right-radius: 4px;">
                    <div class="message-file text-center">
                        <i class="fas ${fileIcon} fa-2x mb-2"></i>
                        <div class="message-text">
                            <a href="/chat/download/${fileId}" target="_blank" class="text-white text-decoration-underline">
                                ${escapeHtml(fileName)}
                            </a>
                        </div>
                        <div class="message-meta" style="font-size: 11px; margin-top: 5px; color: rgba(255,255,255,0.8)">
                            <span>${formatFileSize(fileSize)}</span>
                            <span class="ms-2">Just now</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else {
        messageHtml = `
            <div class="message mb-3 text-end" data-id="${message.id}">
                <div class="d-inline-block bg-primary text-white"
                     style="max-width: 70%; padding: 10px 15px; border-radius: 18px; border-bottom-right-radius: 4px;">
                    <div class="message-text">${escapeHtml(message.body)}</div>
                    <div class="message-meta" style="font-size: 11px; margin-top: 5px; color: rgba(255,255,255,0.8)">
                        <span>Just now</span>
                    </div>
                </div>
            </div>
        `;
    }

    container.insertAdjacentHTML('beforeend', messageHtml);
    scrollToBottom();
}

// ==================== MARK MESSAGES AS READ ====================
function markMessagesAsRead(conversationId) {
    fetch(`/chat/${conversationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        const conversationItem = document.querySelector(`.conversation-item[data-id="${conversationId}"]`);
        if (conversationItem) {
            const badge = conversationItem.querySelector('.unread-badge');
            if (badge) badge.remove();
            conversationItem.classList.remove('bg-light');
        }
    })
    .catch(error => console.error('Error marking messages as read:', error));
}

// ==================== SEARCH USERS ====================
function searchUsers(query) {
    const resultsEl = document.getElementById('searchResults');

    if (query.length < 2) {
        resultsEl.innerHTML = '';
        return;
    }

    resultsEl.innerHTML = `
        <div class="list-group-item text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="ms-2 small">Searching users...</span>
        </div>
    `;

    fetch(`/chat/search/users?search=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(async response => {
        if (!response.ok) throw new Error('Search failed');
        return response.json();
    })
    .then(users => {
        const usersArray = users.data || users;

        if (usersArray.length === 0) {
            resultsEl.innerHTML = `
                <div class="list-group-item text-center py-4">
                    <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No users found matching "${escapeHtml(query)}"</p>
                </div>
            `;
        } else {
            resultsEl.innerHTML = usersArray.map(user => `
                <a href="javascript:void(0)"
                   class="list-group-item list-group-item-action py-3"
                   onclick="startNewConversation(${user.id})">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="bg-${getUserColor(user.role)} rounded-circle text-white d-flex align-items-center justify-content-center"
                                 style="width: 45px; height: 45px; font-size: 16px;">
                                ${getInitials(user.name)}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1 fw-bold">${escapeHtml(user.name)}</h6>
                                <span class="badge bg-${getRoleBadgeColor(user.role)}">${user.role || 'User'}</span>
                            </div>
                            <p class="mb-0 small text-muted">
                                <i class="fas fa-envelope me-1"></i>${escapeHtml(user.email)}
                            </p>
                        </div>
                    </div>
                </a>
            `).join('');
        }
    })
    .catch(error => {
        console.error('Error searching users:', error);
        resultsEl.innerHTML = `
            <div class="list-group-item text-center py-4">
                <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                <p class="text-danger mb-0">Failed to search users. Please try again.</p>
                <small class="text-muted">${error.message}</small>
                <button class="btn btn-sm btn-outline-primary mt-2" onclick="searchUsers('${query}')">Retry</button>
            </div>
        `;
    });
}

// ==================== START NEW CONVERSATION ====================
function startNewConversation(userId) {
    const resultsEl = document.getElementById('searchResults');
    resultsEl.innerHTML = `
        <div class="list-group-item text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="ms-2 small">Creating conversation...</span>
        </div>
    `;

    fetch(`/chat/start`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(async response => {
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to start conversation');
        }
        return response.json();
    })
    .then(data => {
        const conversation = data.conversation || data.data?.conversation;
        if (conversation) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('newChatModal'));
            modal.hide();
            openConversation(conversation.id);
        }
    })
    .catch(error => {
        console.error('Error starting conversation:', error);
        alert('Failed to start conversation: ' + error.message);
        document.getElementById('searchUsersInput').value = '';
        document.getElementById('searchResults').innerHTML = '';
    });
}

// ==================== NOTIFICATION FUNCTIONS ====================
function updateNotificationBadge() {
    fetch(`/chat/unread-count`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        const count = data.unread_count || data.data?.unread_count || 0;
        const chatLink = document.querySelector('a[href*="chat.index"]');
        if (chatLink) {
            const existingBadge = chatLink.querySelector('.badge');
            if (count > 0) {
                if (existingBadge) {
                    existingBadge.textContent = count;
                } else {
                    chatLink.insertAdjacentHTML('beforeend', `<span class="badge bg-danger ms-1">${count}</span>`);
                }
            } else if (existingBadge) {
                existingBadge.remove();
            }
        }
    })
    .catch(error => console.error('Error updating notification badge:', error));
}

// ==================== HELPER FUNCTIONS ====================
function getInitials(name) {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
}

function formatTime(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    const now = new Date();
    if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    }
    return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

function handleMessageKeydown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

function toggleSidebar() {
    const sidebar = document.querySelector('.col-md-4');
    if (sidebar) {
        sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
    }
}

function updateUrlWithConversation(conversationId) {
    const url = new URL(window.location);
    url.searchParams.set('conversation', conversationId);
    window.history.pushState({}, '', url);
}

function updateActiveConversation(conversationId) {
    document.querySelectorAll('.conversation-item').forEach(item => {
        if (item.dataset.id == conversationId) {
            item.classList.add('active', 'bg-light');
        } else {
            item.classList.remove('active', 'bg-light');
        }
    });
}

function updateConversationLastMessage(conversationId, message) {
    const conversationItem = document.querySelector(`.conversation-item[data-id="${conversationId}"]`);
    if (conversationItem) {
        const messagePreview = conversationItem.querySelector('small.text-muted:first-of-type');
        if (messagePreview) {
            messagePreview.textContent = truncateText(message, 30);
        }
    }
}

function truncateText(text, length) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

function setupInfiniteScroll() {
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        messagesContainer.addEventListener('scroll', function() {
            if (this.scrollTop === 0 && hasMoreMessages && !loadingMessages && currentConversation) {
                loadMoreMessages();
            }
        });
    }
}

function loadMoreMessages() {
    if (loadingMessages || !hasMoreMessages || !currentConversation) return;
    loadingMessages = true;
    const loadingDiv = document.getElementById('loadingMore');
    if (loadingDiv) loadingDiv.style.display = 'block';
    const nextPage = (messagesPagination?.current_page || 1) + 1;

    fetch(`/chat/${currentConversation.id}/messages?page=${nextPage}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        messagesPagination = data.messages || data.data?.messages;
        hasMoreMessages = messagesPagination?.current_page < messagesPagination?.last_page;
        const olderMessages = (messagesPagination?.data || []).reverse().map(msg => {
            if ((msg.type === 'file' || msg.type === 'image') && msg.attachment_path) {
                const fileIcon = getFileIcon(msg.mime_type);
                const fileName = msg.attachment_name || 'File';
                const fileSize = msg.file_size;
                const fileId = msg.id;
                return `
                    <div class="message mb-3 ${msg.user_id === currentUser.id ? 'text-end' : 'text-start'}" data-id="${msg.id}">
                        <div class="d-inline-block ${msg.user_id === currentUser.id ? 'bg-primary text-white' : 'bg-light'}"
                             style="max-width: 70%; padding: 10px 15px; border-radius: 18px; ${msg.user_id === currentUser.id ? 'border-bottom-right-radius: 4px;' : 'border-bottom-left-radius: 4px;'}">
                            <div class="message-file text-center">
                                <i class="fas ${fileIcon} fa-2x mb-2"></i>
                                <div class="message-text">
                                    <a href="/chat/download/${fileId}" target="_blank" class="${msg.user_id === currentUser.id ? 'text-white' : 'text-primary'} text-decoration-underline">
                                        ${escapeHtml(fileName)}
                                    </a>
                                </div>
                                <div class="message-meta" style="font-size: 11px; margin-top: 5px; ${msg.user_id === currentUser.id ? 'color: rgba(255,255,255,0.8)' : 'color: #999'}">
                                    <span>${formatFileSize(fileSize)}</span>
                                    <span class="ms-2">${formatTime(msg.created_at)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div class="message mb-3 ${msg.user_id === currentUser.id ? 'text-end' : 'text-start'}" data-id="${msg.id}">
                        <div class="d-inline-block ${msg.user_id === currentUser.id ? 'bg-primary text-white' : 'bg-light'}"
                             style="max-width: 70%; padding: 10px 15px; border-radius: 18px; ${msg.user_id === currentUser.id ? 'border-bottom-right-radius: 4px;' : 'border-bottom-left-radius: 4px;'}">
                            <div class="message-text">${escapeHtml(msg.body)}</div>
                            <div class="message-meta" style="font-size: 11px; margin-top: 5px; ${msg.user_id === currentUser.id ? 'color: rgba(255,255,255,0.8)' : 'color: #999'}">
                                <span>${formatTime(msg.created_at)}</span>
                            </div>
                        </div>
                    </div>
                `;
            }
        }).join('');
        const container = document.getElementById('messages');
        if (container && olderMessages) {
            container.insertAdjacentHTML('afterbegin', olderMessages);
        }
    })
    .catch(error => console.error('Error loading more messages:', error))
    .finally(() => {
        loadingMessages = false;
        const loadingDiv = document.getElementById('loadingMore');
        if (loadingDiv) loadingDiv.style.display = 'none';
    });
}

function getUserColor(role) {
    const colors = {
        'admin': 'danger',
        'system_admin': 'dark',
        'account_manager': 'info',
        'finance': 'success',
        'customer': 'primary',
        'ict_engineer': 'warning',
        'technician': 'secondary',
        'surveyor': 'info'
    };
    return colors[role] || 'secondary';
}

function getRoleBadgeColor(role) {
    return getUserColor(role);
}

function filterByRole(role) {
    const searchInput = document.getElementById('searchUsersInput');
    searchInput.value = '';
    searchInput.placeholder = `Search ${role}...`;
    searchInput.focus();
}

window.addEventListener('popstate', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const conversationId = urlParams.get('conversation');
    if (conversationId) {
        openConversation(conversationId);
    } else {
        document.getElementById('chatArea').style.display = 'none';
        document.getElementById('noChatSelected').style.display = 'block';
        if (window.innerWidth <= 768) {
            document.querySelector('.col-md-4').style.display = 'block';
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover {
    opacity: 0.8;
}

#searchResults .list-group-item {
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

#searchResults .list-group-item:hover {
    border-left-color: #0d6efd;
    background-color: #f8f9fc;
}

#searchResults .list-group-item.active {
    border-left-color: #0d6efd;
    background-color: #e7f1ff;
}

.message .d-inline-block {
    word-wrap: break-word;
    max-width: 70%;
}

.avatar div {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.message-file {
    text-align: center;
    min-width: 150px;
}

.message-file a {
    text-decoration: underline;
    word-break: break-all;
}

.message-file a:hover {
    opacity: 0.8;
}

.btn-outline-secondary:hover {
    background-color: #e9ecef;
}

#filePreviewArea .alert {
    border-radius: 8px;
    background-color: #e7f1ff;
    border-color: #b6d4fe;
}
</style>
@endpush
