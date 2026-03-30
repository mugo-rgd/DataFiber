@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-4 col-lg-3 d-none d-md-block">
            @include('chat.partials.conversations-sidebar', ['conversations' => $user->conversations])
        </div>

        <!-- Main chat -->
        <div class="col-md-8 col-lg-9">
            <div class="card h-100 d-flex flex-column">
                <!-- Chat header -->
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline-secondary me-2 d-md-none">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    @php
                        $otherUser = $conversation->users->first();
                    @endphp
                    <div class="avatar me-3">
                        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            {{ $otherUser ? strtoupper(substr($otherUser->name, 0, 1)) : '?' }}
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $otherUser ? $otherUser->name : 'Unknown User' }}</h6>
                        <small class="text-muted">{{ $otherUser ? $otherUser->getFullRoleName() : '' }}</small>
                    </div>
                    <div class="ms-auto">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> View Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-ban me-2"></i> Block User</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Messages -->
                <div class="card-body flex-grow-1" id="chat-messages" style="overflow-y: auto;">
                    @foreach($conversation->messages->sortBy('created_at') as $message)
                        @if($message->user_id === auth()->id())
                            <!-- Sent message -->
                            <div class="message-wrapper mb-3 d-flex justify-content-end">
                                <div class="message bg-primary text-white rounded p-3" style="max-width: 70%;">
                                    <div class="message-content">
                                        @if($message->type === 'image')
                                            <img src="{{ Storage::url($message->attachment_path) }}"
                                                 alt="Image" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                        @elseif($message->type === 'file')
                                            <div class="file-attachment p-2 bg-white rounded mb-2">
                                                <i class="fas fa-file me-2 text-primary"></i>
                                                <a href="{{ Storage::url($message->attachment_path) }}"
                                                   download="{{ $message->attachment_name }}"
                                                   class="text-decoration-none">
                                                    {{ $message->attachment_name }}
                                                </a>
                                            </div>
                                        @endif
                                        <p class="mb-0">{{ $message->body }}</p>
                                    </div>
                                    <small class="d-block text-end mt-1 opacity-75">
                                        {{ $message->created_at->format('h:i A') }}
                                        @if($message->read_at)
                                            <i class="fas fa-check-double ms-1 text-success"></i>
                                        @else
                                            <i class="fas fa-check ms-1"></i>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @else
                            <!-- Received message -->
                            <div class="message-wrapper mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar me-2">
                                        <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center"
                                             style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="message bg-light rounded p-3" style="max-width: 70%;">
                                        <small class="text-muted fw-bold">{{ $message->user->name }}</small>
                                        <div class="message-content mt-1">
                                            @if($message->type === 'image')
                                                <img src="{{ Storage::url($message->attachment_path) }}"
                                                     alt="Image" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                            @elseif($message->type === 'file')
                                                <div class="file-attachment p-2 bg-white rounded mb-2">
                                                    <i class="fas fa-file me-2 text-primary"></i>
                                                    <a href="{{ Storage::url($message->attachment_path) }}"
                                                       download="{{ $message->attachment_name }}"
                                                       class="text-decoration-none">
                                                        {{ $message->attachment_name }}
                                                    </a>
                                                </div>
                                            @endif
                                            <p class="mb-0">{{ $message->body }}</p>
                                        </div>
                                        <small class="d-block text-muted mt-1">
                                            {{ $message->created_at->format('h:i A') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Attachment preview area (initially hidden) -->
                <div id="attachment-preview-area" class="border-top bg-light p-3 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachment Preview</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="remove-attachment-btn">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                    <div id="attachment-preview-content"></div>
                </div>

                <!-- Message input -->
                <div class="card-footer border-top-0">
                    <form id="send-message-form" data-conversation-id="{{ $conversation->id }}">
                        @csrf
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="attachment-btn" data-bs-toggle="dropdown">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <label class="dropdown-item" for="image-upload">
                                        <i class="fas fa-image me-2"></i> Image
                                    </label>
                                    <input type="file" id="image-upload" accept="image/*" class="d-none" data-type="image">
                                </li>
                                <li>
                                    <label class="dropdown-item" for="file-upload">
                                        <i class="fas fa-file me-2"></i> File
                                    </label>
                                    <input type="file" id="file-upload" class="d-none" data-type="file">
                                </li>
                            </ul>
                            <input type="text"
                                   id="message-input"
                                   class="form-control"
                                   placeholder="Type your message..."
                                   autocomplete="off">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    #chat-messages {
        height: calc(100vh - 300px);
        min-height: 400px;
    }

    .card {
        min-height: 600px;
    }

    #attachment-preview-area {
        transition: all 0.3s ease;
    }

    .attachment-preview-item {
        background: white;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #dee2e6;
    }

    .attachment-image-preview {
        max-height: 100px;
        max-width: 100px;
        object-fit: cover;
        border-radius: 6px;
    }

    .attachment-file-icon {
        font-size: 2rem;
        color: #6c757d;
    }

    .attachment-info {
        flex: 1;
    }

    .attachment-file-size {
        font-size: 0.85rem;
        color: #6c757d;
    }

    #remove-attachment-btn:hover {
        transform: scale(1.05);
        transition: transform 0.2s;
    }

    /* Smooth scroll for messages */
    #chat-messages::-webkit-scrollbar {
        width: 8px;
    }

    #chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    #chat-messages::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('send-message-form');
        const input = document.getElementById('message-input');
        const messagesContainer = document.getElementById('chat-messages');
        const imageUpload = document.getElementById('image-upload');
        const fileUpload = document.getElementById('file-upload');
        const attachmentPreviewArea = document.getElementById('attachment-preview-area');
        const attachmentPreviewContent = document.getElementById('attachment-preview-content');
        const removeAttachmentBtn = document.getElementById('remove-attachment-btn');

        // File attachment tracking
        let currentAttachment = {
            file: null,
            type: null,
            name: null,
            size: null
        };

        if (!form || !input) {
            console.error('Form or input not found');
            return;
        }

        console.log('Chat form loaded');

        // Function to add message to UI
        function addMessageToChat(message, isOwnMessage = true) {
            if (!messagesContainer) return;

            let attachmentHtml = '';
            if (message.type === 'image' && message.attachment_path) {
                attachmentHtml = `
                    <img src="/storage/${message.attachment_path}"
                         alt="Image" class="img-fluid rounded mb-2" style="max-height: 200px;">
                `;
            } else if (message.type === 'file' && message.attachment_path) {
                attachmentHtml = `
                    <div class="file-attachment p-2 bg-white rounded mb-2">
                        <i class="fas fa-file me-2 text-primary"></i>
                        <a href="/storage/${message.attachment_path}"
                           download="${message.attachment_name}"
                           class="text-decoration-none">
                            ${message.attachment_name}
                        </a>
                    </div>
                `;
            }

            const messageHtml = isOwnMessage ? `
                <div class="message-wrapper mb-3 d-flex justify-content-end">
                    <div class="message bg-primary text-white rounded p-3" style="max-width: 70%;">
                        <div class="message-content">
                            ${attachmentHtml}
                            <p class="mb-0">${message.body}</p>
                        </div>
                        <small class="d-block text-end mt-1 opacity-75">
                            ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                            <i class="fas fa-check ms-1"></i>
                        </small>
                    </div>
                </div>
            ` : `
                <div class="message-wrapper mb-3">
                    <div class="d-flex align-items-start">
                        <div class="avatar me-2">
                            <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px;">
                                ${message.user ? message.user.name.charAt(0).toUpperCase() : '?'}
                            </div>
                        </div>
                        <div class="message bg-light rounded p-3" style="max-width: 70%;">
                            <small class="text-muted fw-bold">${message.user ? message.user.name : 'Unknown'}</small>
                            <div class="message-content mt-1">
                                ${attachmentHtml}
                                <p class="mb-0">${message.body}</p>
                            </div>
                            <small class="d-block text-muted mt-1">
                                ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                            </small>
                        </div>
                    </div>
                </div>
            `;

            messagesContainer.insertAdjacentHTML('beforeend', messageHtml);

            // Scroll to bottom
            scrollToBottom();
        }

        // Handle image upload
        if (imageUpload) {
            imageUpload.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    currentAttachment = {
                        file: this.files[0],
                        type: 'image',
                        name: this.files[0].name,
                        size: formatFileSize(this.files[0].size)
                    };

                    // Show preview in attachment area
                    showAttachmentPreview(currentAttachment);

                    // Clear the input for next upload
                    this.value = '';

                    // Focus on message input
                    input.focus();
                }
            });
        }

        // Handle file upload
        if (fileUpload) {
            fileUpload.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    currentAttachment = {
                        file: this.files[0],
                        type: 'file',
                        name: this.files[0].name,
                        size: formatFileSize(this.files[0].size)
                    };

                    // Show preview in attachment area
                    showAttachmentPreview(currentAttachment);

                    // Clear the input for next upload
                    this.value = '';

                    // Focus on message input
                    input.focus();
                }
            });
        }

        // Show attachment preview in the dedicated area
        function showAttachmentPreview(attachment) {
            // Show the attachment preview area
            attachmentPreviewArea.classList.remove('d-none');

            let previewHtml = '';

            if (attachment.type === 'image') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewHtml = `
                        <div class="attachment-preview-item d-flex align-items-center">
                            <div class="me-3">
                                <img src="${e.target.result}"
                                     alt="Preview"
                                     class="attachment-image-preview rounded">
                            </div>
                            <div class="attachment-info">
                                <div class="fw-bold">${attachment.name}</div>
                                <div class="attachment-file-size">${attachment.size} • ${attachment.type.toUpperCase()}</div>
                            </div>
                        </div>
                    `;

                    attachmentPreviewContent.innerHTML = previewHtml;

                    // Adjust messages container height
                    adjustMessagesHeight();
                };
                reader.readAsDataURL(attachment.file);
            } else {
                // Get file icon based on extension
                const fileIcon = getFileIcon(attachment.name);

                previewHtml = `
                    <div class="attachment-preview-item d-flex align-items-center">
                        <div class="me-3">
                            <i class="${fileIcon} attachment-file-icon"></i>
                        </div>
                        <div class="attachment-info">
                            <div class="fw-bold">${attachment.name}</div>
                            <div class="attachment-file-size">${attachment.size} • ${getFileExtension(attachment.name).toUpperCase()} File</div>
                        </div>
                    </div>
                `;

                attachmentPreviewContent.innerHTML = previewHtml;

                // Adjust messages container height
                adjustMessagesHeight();
            }
        }

        // Remove attachment preview
        function removeAttachmentPreview() {
            // Hide the attachment preview area
            attachmentPreviewArea.classList.add('d-none');
            attachmentPreviewContent.innerHTML = '';
            currentAttachment = { file: null, type: null, name: null, size: null };

            // Restore messages container height
            adjustMessagesHeight();
        }

        // Remove attachment button handler
        if (removeAttachmentBtn) {
            removeAttachmentBtn.addEventListener('click', function() {
                removeAttachmentPreview();
            });
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Get file icon based on extension
        function getFileIcon(filename) {
            const ext = getFileExtension(filename).toLowerCase();

            const iconMap = {
                'pdf': 'fas fa-file-pdf text-danger',
                'doc': 'fas fa-file-word text-primary',
                'docx': 'fas fa-file-word text-primary',
                'xls': 'fas fa-file-excel text-success',
                'xlsx': 'fas fa-file-excel text-success',
                'ppt': 'fas fa-file-powerpoint text-warning',
                'pptx': 'fas fa-file-powerpoint text-warning',
                'txt': 'fas fa-file-alt text-secondary',
                'zip': 'fas fa-file-archive text-warning',
                'rar': 'fas fa-file-archive text-warning',
                'mp3': 'fas fa-file-audio text-info',
                'mp4': 'fas fa-file-video text-info',
                'jpg': 'fas fa-file-image text-info',
                'jpeg': 'fas fa-file-image text-info',
                'png': 'fas fa-file-image text-info',
                'gif': 'fas fa-file-image text-info'
            };

            return iconMap[ext] || 'fas fa-file text-secondary';
        }

        // Get file extension
        function getFileExtension(filename) {
            return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
        }

        // Adjust messages container height when attachment area is shown/hidden
        function adjustMessagesHeight() {
            if (messagesContainer) {
                if (attachmentPreviewArea.classList.contains('d-none')) {
                    messagesContainer.style.height = 'calc(100vh - 300px)';
                } else {
                    messagesContainer.style.height = 'calc(100vh - 380px)';
                }
                scrollToBottom();
            }
        }

        // Scroll to bottom of messages
        function scrollToBottom() {
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }

        // Form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Form submitted');

            const message = input.value.trim();
            const conversationId = this.dataset.conversationId;

            // Check if we have either a message or an attachment
            if (!message && !currentAttachment.file) {
                alert('Please enter a message or attach a file');
                return;
            }

            console.log('Sending to conversation:', conversationId);

            try {
                // Show sending indicator
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                submitBtn.disabled = true;

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                let response;

                if (currentAttachment.file) {
                    // Send with file attachment using FormData
                    const formData = new FormData();
                    formData.append('body', message || (currentAttachment.type === 'image' ? 'Sent an image' : 'Sent a file'));
                    formData.append('_token', csrfToken);
                    formData.append('attachment', currentAttachment.file);
                    formData.append('attachment_type', currentAttachment.type);

                    response = await fetch(`/chat/${conversationId}/messages/with-attachment`, {
                        method: 'POST',
                        body: formData
                    });
                } else {
                    // Send plain text message
                    response = await fetch(`/chat/${conversationId}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ body: message })
                    });
                }

                console.log('Response status:', response.status);

                if (response.ok) {
                    const data = await response.json();
                    console.log('Success:', data);

                    // Clear input and attachment
                    input.value = '';
                    removeAttachmentPreview();
                    currentAttachment = { file: null, type: null, name: null, size: null };

                    // Add message to UI
                    if (data.message) {
                        addMessageToChat(data.message, true);
                    }
                } else {
                    const error = await response.text();
                    console.error('Server error:', error);

                    try {
                        const errorData = JSON.parse(error);
                        alert('Error: ' + (errorData.message || errorData.error || 'Failed to send message'));
                    } catch {
                        alert('Error: Failed to send message. Status: ' + response.status);
                    }
                }
            } catch (error) {
                console.error('Network error:', error);
                alert('Network error. Please check your connection and try again.');
            } finally {
                // Restore button
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    submitBtn.disabled = false;
                }
            }
        });

        // Also allow sending with Enter key
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });

        // Auto-scroll to bottom on load
        scrollToBottom();
    });
</script>
@endpush
@endsection
