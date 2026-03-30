{{-- <script>
// ==================== STATE MANAGEMENT ====================
let currentConversation = null;
let currentPage = 1;
let messagesPagination = null;
let loadingMessages = false;
let hasMoreMessages = true;
let currentUser = {
    id: {{ auth()->id() }},
    name: "{{ auth()->user()->name }}",
    email: "{{ auth()->user()->email }}"
};

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', function() {
    // Attach click handlers to existing conversation items
    attachConversationClickHandlers();

    // Check for conversation ID in URL (from redirect)
    const urlParams = new URLSearchParams(window.location.search);
    const conversationId = urlParams.get('conversation');
    if (conversationId) {
        // Small delay to ensure DOM is ready
        setTimeout(() => {
            openConversation(conversationId);
        }, 100);
    }

    // Setup infinite scroll for messages
    setupInfiniteScroll();
});

// Attach click handlers to conversation items
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

// ==================== OPEN CONVERSATION ====================
function openConversation(conversationId) {
    console.log('Opening conversation:', conversationId);

    // Update URL without page reload
    updateUrlWithConversation(conversationId);

    // Show chat area, hide empty state
    document.getElementById('chatArea').style.display = 'block';
    document.getElementById('noChatSelected').style.display = 'none';

    // Show loading state
    document.getElementById('messages').innerHTML = `
        <div class="text-center text-muted py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Loading messages...</p>
        </div>
    `;

    // On mobile, hide sidebar
    if (window.innerWidth <= 768) {
        document.querySelector('.col-md-4').style.display = 'none';
    }

    // Fetch conversation and messages
    fetch(`/api/chat/${conversationId}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load conversation');
        }
        return response.json();
    })
    .then(data => {
        console.log('Conversation data loaded:', data);

        // Store current conversation
        currentConversation = data.conversation;
        messagesPagination = data.messages;
        hasMoreMessages = data.messages.current_page < data.messages.last_page;

        // Get the other user from the conversation users array
        const otherUser = data.other_user || data.conversation.users[0];

        // Update chat header with user info
        updateChatHeader(otherUser);

        // Render messages
        renderMessages(data.messages.data);

        // Mark conversation as active in list
        updateActiveConversation(conversationId);

        // Focus on message input
        document.getElementById('messageInput').focus();

        // Mark messages as read
        markMessagesAsRead(conversationId);

        // Scroll to bottom
        scrollToBottom();
    })
    .catch(error => {
        console.error('Error loading conversation:', error);
        document.getElementById('messages').innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-exclamation-circle fa-2x mb-3 text-danger"></i>
                <p>Failed to load messages.</p>
                <button class="btn btn-sm btn-primary" onclick="openConversation(${conversationId})">
                    Try Again
                </button>
            </div>
        `;
    });
}

// Update chat header with user info
function updateChatHeader(user) {
    if (!user) return;

    document.getElementById('chatUserName').textContent = user.name || 'Unknown User';
    document.getElementById('chatUserRole').textContent = user.role || '';
    document.getElementById('chatUserEmail').textContent = user.email || '';

    // Update avatar
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

    // Messages come from API in descending order (newest first)
    // We want to display them in ascending order (oldest first)
    const sortedMessages = [...messages].reverse();

    container.innerHTML = sortedMessages.map(msg => `
        <div class="message mb-3 ${msg.user_id === currentUser.id ? 'text-end' : 'text-start'}" data-id="${msg.id}">
            <div class="d-inline-block ${msg.user_id === currentUser.id ? 'bg-primary text-white' : 'bg-light'}"
                 style="max-width: 70%; padding: 10px 15px; border-radius: 18px; ${msg.user_id === currentUser.id ? 'border-bottom-right-radius: 4px;' : 'border-bottom-left-radius: 4px;'}">
                <div class="message-text">${escapeHtml(msg.body)}</div>
                <div class="message-meta" style="font-size: 11px; margin-top: 5px; ${msg.user_id === currentUser.id ? 'color: rgba(255,255,255,0.8)' : 'color: #999'}">
                    <span>${formatTime(msg.created_at)}</span>
                    ${msg.user_id === currentUser.id && msg.read_at ?
                        '<span class="ms-1" title="Read">✓✓</span>' : ''}
                </div>
            </div>
        </div>
    `).join('');
}

// ==================== SEND MESSAGE ====================
function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();

    if (!message || !currentConversation) {
        alert('Please select a conversation and type a message');
        return;
    }

    // Disable input and button
    input.disabled = true;
    document.getElementById('sendButton').disabled = true;

    // Send message to server
    fetch(`/api/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            conversation_id: currentConversation.id,
            body: message,
            type: 'text'
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to send message');
        }
        return response.json();
    })
    .then(data => {
        // Add message to UI
        addMessageToUI(data);

        // Clear input
        input.value = '';
        input.style.height = 'auto';

        // Update conversation last message in sidebar
        updateConversationLastMessage(currentConversation.id, data.body);
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    })
    .finally(() => {
        // Re-enable input and button
        input.disabled = false;
        document.getElementById('sendButton').disabled = false;
        input.focus();
    });
}

// Add new message to UI
function addMessageToUI(message) {
    const container = document.getElementById('messages');
    const messageHtml = `
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

    container.insertAdjacentHTML('beforeend', messageHtml);
    scrollToBottom();
}

// Update conversation last message in sidebar
function updateConversationLastMessage(conversationId, message) {
    const conversationItem = document.querySelector(`.conversation-item[data-id="${conversationId}"]`);
    if (conversationItem) {
        const messagePreview = conversationItem.querySelector('small.text-muted');
        if (messagePreview) {
            messagePreview.textContent = truncateText(message, 30);
        }
    }
}

// Helper function to truncate text
function truncateText(text, length) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

// ==================== MARK MESSAGES AS READ ====================
function markMessagesAsRead(conversationId) {
    fetch(`/api/chat/${conversationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        // Remove unread badge from sidebar
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
function showNewChatModal() {
    const modal = new bootstrap.Modal(document.getElementById('newChatModal'));
    modal.show();
}

function searchUsers(query) {
    if (query.length < 2) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }

    fetch(`/api/chat/search/users?search=${encodeURIComponent(query)}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(users => {
        const resultsEl = document.getElementById('searchResults');

        if (users.length === 0) {
            resultsEl.innerHTML = '<div class="list-group-item text-muted">No users found</div>';
        } else {
            resultsEl.innerHTML = users.map(user => `
                <a href="javascript:void(0)"
                   class="list-group-item list-group-item-action"
                   onclick="startNewConversation(${user.id})">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${escapeHtml(user.name)}</strong>
                            <br>
                            <small class="text-muted">${escapeHtml(user.email)}</small>
                        </div>
                        <span class="badge bg-info">${user.role || 'User'}</span>
                    </div>
                </a>
            `).join('');
        }
    });
}

function startNewConversation(userId) {
    fetch(`/api/chat/start`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('newChatModal')).hide();

        // Open the conversation
        openConversation(data.conversation.id);

        // Reload to show new conversation
        location.reload();
    })
    .catch(error => {
        console.error('Error starting conversation:', error);
        alert('Failed to start conversation');
    });
}

// ==================== HELPER FUNCTIONS ====================
function getInitials(name) {
    if (!name) return '?';
    return name.split(' ')
        .map(n => n[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();
}

function formatTime(timestamp) {
    if (!timestamp) return '';

    const date = new Date(timestamp);
    const now = new Date();
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    }

    const daysDiff = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    if (daysDiff < 7) {
        return date.toLocaleDateString([], { weekday: 'short' });
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
    container.scrollTop = container.scrollHeight;
}

function handleMessageKeydown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

function toggleSidebar() {
    const sidebar = document.querySelector('.col-md-4');
    const currentDisplay = sidebar.style.display;
    sidebar.style.display = currentDisplay === 'none' ? 'block' : 'none';
}

function updateUrlWithConversation(conversationId) {
    const url = new URL(window.location);
    url.searchParams.set('conversation', conversationId);
    window.history.pushState({}, '', url);
}

function updateActiveConversation(conversationId) {
    document.querySelectorAll('.conversation-item').forEach(item => {
        if (item.dataset.id == conversationId) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

function setupInfiniteScroll() {
    const messagesContainer = document.getElementById('messagesContainer');

    messagesContainer.addEventListener('scroll', function() {
        if (this.scrollTop === 0 && hasMoreMessages && !loadingMessages) {
            loadMoreMessages();
        }
    });
}

function loadMoreMessages() {
    if (loadingMessages || !hasMoreMessages || !currentConversation) return;

    loadingMessages = true;
    document.getElementById('loadingMore').style.display = 'block';

    const nextPage = messagesPagination.current_page + 1;

    fetch(`/api/chat/${currentConversation.id}?page=${nextPage}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        messagesPagination = data.messages;
        hasMoreMessages = data.messages.current_page < data.messages.last_page;

        const container = document.getElementById('messages');
        const olderMessages = data.messages.data.reverse().map(msg => `
            <div class="message mb-3 ${msg.user_id === currentUser.id ? 'text-end' : 'text-start'}" data-id="${msg.id}">
                <div class="d-inline-block ${msg.user_id === currentUser.id ? 'bg-primary text-white' : 'bg-light'}"
                     style="max-width: 70%; padding: 10px 15px; border-radius: 18px; ${msg.user_id === currentUser.id ? 'border-bottom-right-radius: 4px;' : 'border-bottom-left-radius: 4px;'}">
                    <div class="message-text">${escapeHtml(msg.body)}</div>
                    <div class="message-meta" style="font-size: 11px; margin-top: 5px; ${msg.user_id === currentUser.id ? 'color: rgba(255,255,255,0.8)' : 'color: #999'}">
                        <span>${formatTime(msg.created_at)}</span>
                    </div>
                </div>
            </div>
        `).join('');

        container.insertAdjacentHTML('afterbegin', olderMessages);
    })
    .catch(error => console.error('Error loading more messages:', error))
    .finally(() => {
        loadingMessages = false;
        document.getElementById('loadingMore').style.display = 'none';
    });
}

// Handle browser back/forward buttons
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
</script> --}}
