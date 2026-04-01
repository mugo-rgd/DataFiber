<?php $__env->startSection('title', 'Messages - Lease Management'); ?>

<?php $__env->startSection('content'); ?>
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
                        <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                                $unreadCount = $conversation->unreadCountForUser(auth()->id()) ?? 0;
                            ?>

                            <a href="#"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center conversation-item <?php echo e($unreadCount > 0 ? 'bg-light' : ''); ?>"
                               data-id="<?php echo e($conversation->id); ?>">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px;">
                                           <?php echo e(substr($otherUser->name ?? '?', 0, 1)); ?>

                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo e($otherUser->name ?? 'Unknown User'); ?></h6>
                                        <small class="text-muted">
                                            <?php if($conversation->lastMessage): ?>
                                                <?php echo e(Str::limit($conversation->lastMessage->body, 30)); ?>

                                            <?php else: ?>
                                                No messages yet
                                            <?php endif; ?>
                                        </small>
                                        <br>
                                        <small class="text-muted"><?php echo e($otherUser->role ?? ''); ?></small>
                                    </div>
                                </div>
                                <?php if($unreadCount > 0): ?>
                                    <span class="badge bg-danger rounded-pill unread-badge"><?php echo e($unreadCount); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No conversations yet</p>
                                <button onclick="showNewChatModal()" class="btn btn-primary">Start a Chat</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(method_exists($conversations, 'links')): ?>
                <div class="card-footer" id="pagination">
                    <?php echo e($conversations->links()); ?>

                </div>
                <?php endif; ?>
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
                    <div class="input-group">
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

                <!-- Search Results -->
                <div id="searchResults" class="list-group" style="max-height: 350px; overflow-y: auto;">
                    <!-- Results will appear here -->
                </div>

                <!-- Quick Filters (Optional) -->
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ==================== STATE MANAGEMENT ====================
let currentConversation = null;
let currentPage = 1;
let messagesPagination = null;
let loadingMessages = false;
let hasMoreMessages = true;
let currentUser = {
    id: <?php echo e(auth()->id()); ?>,
    name: "<?php echo e(auth()->user()->name); ?>",
    email: "<?php echo e(auth()->user()->email); ?>"
};

// ==================== INITIALIZATION ====================
// Auto-resize textarea as user types
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
    autoResizeTextarea();
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

// ==================== SHOW NEW CHAT MODAL ====================
function showNewChatModal() {
    const modal = new bootstrap.Modal(document.getElementById('newChatModal'));
    modal.show();

    // Clear previous search results and input
    document.getElementById('searchUsersInput').value = '';
    document.getElementById('searchResults').innerHTML = '';

    // Focus on search input
    setTimeout(() => {
        document.getElementById('searchUsersInput').focus();
    }, 500);
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
        const otherUser = data.other_user || data.conversation.users.find(u => u.id !== currentUser.id);

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

    if (!message) {
        return;
    }

    if (!currentConversation) {
        alert('Please select a conversation first');
        return;
    }

    // Disable input and button
    input.disabled = true;
    const sendButton = document.getElementById('sendButton');
    sendButton.disabled = true;
    sendButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch(`/api/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            conversation_id: currentConversation.id,
            body: message,
            type: 'text'
        })
    })
    .then(async response => {
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to send message');
        }
        return response.json();
    })
    .then(data => {
        console.log('Message sent:', data);

        // Add message to UI
        addMessageToUI(data);

        // Clear input and reset height
        input.value = '';
        input.style.height = 'auto';

        // Update conversation last message in sidebar
        updateConversationLastMessage(currentConversation.id, data.body);
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Failed to send message: ' + error.message);
    })
    .finally(() => {
        // Re-enable input and button
        input.disabled = false;
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
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
function searchUsers(query) {
    const resultsEl = document.getElementById('searchResults');

    if (query.length < 2) {
        resultsEl.innerHTML = '';
        return;
    }

    // Show loading state
    resultsEl.innerHTML = `
        <div class="list-group-item text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="ms-2 small">Searching users...</span>
        </div>
    `;

    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!token) {
        console.error('CSRF token not found');
        resultsEl.innerHTML = `
            <div class="list-group-item text-center py-4">
                <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                <p class="text-danger mb-0">Security token not found. Please refresh the page.</p>
            </div>
        `;
        return;
    }

    // Construct the full URL
    const url = `/api/chat/search/users?search=${encodeURIComponent(query)}`;
    console.log('Fetching users from:', url);

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        console.log('Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(users => {
        console.log('Users received:', users);

        if (users.length === 0) {
            resultsEl.innerHTML = `
                <div class="list-group-item text-center py-4">
                    <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No users found matching "${escapeHtml(query)}"</p>
                </div>
            `;
        } else {
            resultsEl.innerHTML = users.map(user => `
                <a href="javascript:void(0)"
                   class="list-group-item list-group-item-action py-3"
                   onclick="startNewConversation(${user.id})">
                    <div class="d-flex align-items-center">
                        <!-- User Avatar -->
                        <div class="avatar me-3">
                            <div class="bg-${getUserColor(user.role)} rounded-circle text-white d-flex align-items-center justify-content-center"
                                 style="width: 45px; height: 45px; font-size: 16px;">
                                ${user.initial || getInitials(user.name)}
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1 fw-bold">${escapeHtml(user.name)}</h6>
                                <span class="badge bg-${getRoleBadgeColor(user.role)}">${user.role || 'User'}</span>
                            </div>
                            <p class="mb-0 small text-muted">
                                <i class="fas fa-envelope me-1"></i>${escapeHtml(user.email)}
                            </p>
                            ${user.company_name ? `
                                <p class="mb-0 small text-muted mt-1">
                                    <i class="fas fa-building me-1"></i>${escapeHtml(user.company_name)}
                                </p>
                            ` : ''}
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
            </div>
        `;
    });
}

// ==================== START NEW CONVERSATION ====================
function startNewConversation(userId) {
    console.log('Starting conversation with user:', userId);

    // Show loading state in the modal
    const resultsEl = document.getElementById('searchResults');
    resultsEl.innerHTML = `
        <div class="list-group-item text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="ms-2 small">Creating conversation...</span>
        </div>
    `;

    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch(`/api/chat/start`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
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
        console.log('Conversation created:', data);

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('newChatModal'));
        modal.hide();

        // Open the conversation
        openConversation(data.conversation.id);
    })
    .catch(error => {
        console.error('Error starting conversation:', error);
        alert('Failed to start conversation: ' + error.message);

        // Restore search results
        document.getElementById('searchUsersInput').value = '';
        document.getElementById('searchResults').innerHTML = '';
    });
}

// Helper function to get user avatar color based on role
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

// Helper function to get badge color based on role
function getRoleBadgeColor(role) {
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

// ==================== REAL-TIME NOTIFICATIONS ====================
// ==================== REAL-TIME NOTIFICATIONS ====================
// Initialize Echo (only if Pusher is configured)
let echoInitialized = false;

function initializeEcho() {
    if (echoInitialized) return;

    // Check if Pusher JS is loaded
    if (typeof Pusher === 'undefined') {
        console.log('Pusher not loaded, real-time features disabled');
        return;
    }

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: document.querySelector('meta[name="pusher-key"]')?.content || '',
        cluster: document.querySelector('meta[name="pusher-cluster"]')?.content || 'mt1',
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        }
    });

    echoInitialized = true;
    listenForMessages();
}

// Listen for new messages
function listenForMessages() {
    if (!window.Echo) return;

    // Listen on user's private channel
    window.Echo.private(`user.${currentUser.id}`)
        .listen('.message.sent', (e) => {
            console.log('New message received:', e);

            // If the message is for the current conversation, add it to the chat
            if (currentConversation && e.conversation && e.conversation.id === currentConversation.id) {
                addMessageToUI(e.message);
                markMessagesAsRead(currentConversation.id);
            } else {
                // Show notification
                showNotification(e);
            }

            // Update conversation list
            if (e.conversation && e.message) {
                updateConversationList(e.conversation, e.message);
            }

            // Update notification badge
            updateNotificationBadge();
        });

    // Listen on conversation channel if in a conversation
    if (currentConversation) {
        window.Echo.private(`conversation.${currentConversation.id}`)
            .listen('.message.sent', (e) => {
                console.log('Message on conversation channel:', e);
            });
    }
}

// Show browser notification
function showNotification(data) {
    // Check if browser notifications are supported
    if (!("Notification" in window)) {
        console.log("This browser does not support desktop notification");
        return;
    }

    const messageData = data.message || {};
    const conversationData = data.conversation || {};
    const senderName = messageData.user?.name || 'Someone';

    if (Notification.permission === "granted") {
        const notification = new Notification(`New message from ${senderName}`, {
            body: messageData.body || 'You have a new message',
            icon: '/favicon.ico',
            tag: 'chat-message',
            silent: false
        });

        notification.onclick = function() {
            window.focus();
            if (conversationData.id) {
                openConversation(conversationData.id);
            }
            notification.close();
        };

        // Auto close after 5 seconds
        setTimeout(() => notification.close(), 5000);
    } else if (Notification.permission !== "denied") {
        Notification.requestPermission().then(permission => {
            if (permission === "granted") {
                showNotification(data);
            }
        });
    }
}

// Update notification badge
function updateNotificationBadge() {
    fetch('/notifications/unread-count', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        }
    })
    .catch(error => console.error('Error updating notification badge:', error));
}

// Update conversation list when new message arrives
function updateConversationList(conversation, message) {
    const conversationsList = document.getElementById('conversationsList');
    if (!conversationsList) return;

    // Check if conversation already exists in list
    const existingItem = document.querySelector(`.conversation-item[data-id="${conversation.id}"]`);

    if (existingItem) {
        // Update existing conversation item
        const messagePreview = existingItem.querySelector('small.text-muted');
        if (messagePreview) {
            messagePreview.textContent = truncateText(message.body, 30);
        }

        // Move conversation to top (if it's a new message)
        const listItem = existingItem.closest('.list-group-item');
        if (listItem) {
            conversationsList.prepend(listItem);
        }
    } else {
        // Add new conversation item
        const otherUser = conversation.users?.find(u => u.id !== currentUser.id);
        if (otherUser) {
            const newItem = createConversationItem(conversation, otherUser, message);
            conversationsList.insertAdjacentHTML('afterbegin', newItem);

            // Attach click handler to new item
            const newConversationItem = document.querySelector(`.conversation-item[data-id="${conversation.id}"]`);
            if (newConversationItem) {
                newConversationItem.addEventListener('click', function(e) {
                    e.preventDefault();
                    openConversation(conversation.id);
                });
            }
        }
    }
}

// Create conversation item HTML
function createConversationItem(conversation, user, lastMessage) {
    return `
        <a href="#"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center conversation-item bg-light"
           data-id="${conversation.id}">
            <div class="d-flex align-items-center">
                <div class="avatar me-3">
                    <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                         style="width: 40px; height: 40px;">
                       ${getInitials(user.name)}
                    </div>
                </div>
                <div>
                    <h6 class="mb-0">${escapeHtml(user.name)}</h6>
                    <small class="text-muted">${truncateText(lastMessage.body, 30)}</small>
                    <br>
                    <small class="text-muted">${user.role || ''}</small>
                </div>
            </div>
            <span class="badge bg-danger rounded-pill unread-badge">1</span>
        </a>
    `;
}

// Initialize Echo when user is authenticated and page is loaded
if (currentUser.id) {
    // Only initialize if Pusher is available
    if (typeof Pusher !== 'undefined') {
        initializeEcho();
    } else {
        // Load Pusher dynamically if not available
        const script = document.createElement('script');
        script.src = 'https://js.pusher.com/7.2/pusher.min.js';
        script.onload = () => {
            // Load Echo after Pusher
            const echoScript = document.createElement('script');
            echoScript.src = 'https://laravel.com/js/echo.js';
            echoScript.onload = initializeEcho;
            document.head.appendChild(echoScript);
        };
        document.head.appendChild(script);
    }

    updateNotificationBadge();

    // Periodically update notification badge (fallback for when WebSockets fail)
    setInterval(updateNotificationBadge, 30000);
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

// Quick filter by role
function filterByRole(role) {
    const searchInput = document.getElementById('searchUsersInput');

    // Set placeholder text based on role
    const roleLabels = {
        'customer': 'Customers',
        'account_manager': 'Account Managers',
        'finance': 'Finance',
        'ict_engineer': 'ICT Engineers',
        'technician': 'Technicians'
    };

    searchInput.value = '';
    searchInput.placeholder = `Search ${roleLabels[role] || role}...`;

    // You can implement role-based search if your API supports it
    // For now, we'll just focus the input
    searchInput.focus();
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
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover {
    opacity: 0.8;
}

/* Search results styling */
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

/* Message styling */
.message .d-inline-block {
    word-wrap: break-word;
    max-width: 70%;
}

/* Avatar styling */
.avatar div {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/chat/index.blade.php ENDPATH**/ ?>