@extends('layouts.app')

@section('title', 'Start New Chat')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Start New Chat</h5>
                        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Chats
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Search Bar -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                   id="user-search"
                                   class="form-control border-start-0"
                                   placeholder="Search users by name or email..."
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block">Type to search for users you can chat with</small>
                    </div>

                    <!-- User List -->
                    <div id="user-list">
                        <div class="list-group" id="available-users">
                            @foreach($users as $user)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center user-item"
                                     data-user-id="{{ $user->id }}"
                                     data-user-name="{{ $user->name }}"
                                     data-user-email="{{ $user->email }}"
                                     data-user-role="{{ $user->role }}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">
                                                {{ $user->email }}
                                                <span class="badge bg-secondary ms-2">{{ $user->getFullRoleName() }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary start-chat-btn">
                                        <i class="fas fa-comment me-1"></i> Chat
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        @if($users->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No users available to chat with</h5>
                                <p class="text-muted">You don't have permission to start chats with other users.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Loading indicator -->
                    <div id="loading-indicator" class="text-center py-3 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Searching users...</p>
                    </div>

                    <!-- No results message -->
                    <div id="no-results" class="text-center py-5 d-none">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No users found</h5>
                        <p class="text-muted">Try searching with a different name or email.</p>
                    </div>
                </div>

                <!-- Chat Form (Hidden) -->
                <form id="start-chat-form" action="{{ route('chat.start') }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="user_id" id="selected-user-id">
                    <input type="hidden" name="message" id="initial-message" value="">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .user-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .avatar {
        flex-shrink: 0;
    }

    #user-search:focus {
        box-shadow: none;
        border-color: #86b7fe;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('user-search');
        const clearSearchBtn = document.getElementById('clear-search');
        const userList = document.getElementById('available-users');
        const loadingIndicator = document.getElementById('loading-indicator');
        const noResults = document.getElementById('no-results');
        const startChatForm = document.getElementById('start-chat-form');
        const selectedUserIdInput = document.getElementById('selected-user-id');
        const initialMessageInput = document.getElementById('initial-message');

        let searchTimeout = null;

        // Clear search button
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterUsers('');
            clearSearchBtn.classList.add('d-none');
        });

        // Search input event
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Show/hide clear button
            if (query.length > 0) {
                clearSearchBtn.classList.remove('d-none');
            } else {
                clearSearchBtn.classList.add('d-none');
            }

            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Set new timeout for debouncing
            searchTimeout = setTimeout(() => {
                if (query.length >= 2) {
                    performSearch(query);
                } else if (query.length === 0) {
                    resetUserList();
                } else {
                    filterUsers(query);
                }
            }, 300);
        });

        // Filter users locally (client-side)
        function filterUsers(query) {
            const userItems = document.querySelectorAll('.user-item');
            let hasVisibleItems = false;

            userItems.forEach(item => {
                const userName = item.dataset.userName.toLowerCase();
                const userEmail = item.dataset.userEmail.toLowerCase();
                const searchTerm = query.toLowerCase();

                if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                    item.style.display = 'flex';
                    hasVisibleItems = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (!hasVisibleItems && query.length > 0) {
                noResults.classList.remove('d-none');
                userList.classList.add('d-none');
            } else {
                noResults.classList.add('d-none');
                userList.classList.remove('d-none');
            }
        }

        // Perform server-side search
        function performSearch(query) {
            loadingIndicator.classList.remove('d-none');
            userList.classList.add('d-none');
            noResults.classList.add('d-none');

            fetch(`{{ route('chat.search.users') }}?search=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(users => {
                loadingIndicator.classList.add('d-none');

                if (users.length === 0) {
                    noResults.classList.remove('d-none');
                    return;
                }

                // Clear current list
                userList.innerHTML = '';

                // Add new users
                users.forEach(user => {
                    const userItem = document.createElement('div');
                    userItem.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center user-item';
                    userItem.dataset.userId = user.id;
                    userItem.dataset.userName = user.name;
                    userItem.dataset.userEmail = user.email;
                    userItem.dataset.userRole = user.role;

                    const avatarLetter = user.name ? user.name.charAt(0).toUpperCase() : '?';
                    const roleName = getRoleName(user.role);

                    userItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    ${avatarLetter}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">${user.name}</h6>
                                <small class="text-muted">
                                    ${user.email}
                                    <span class="badge bg-secondary ms-2">${roleName}</span>
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary start-chat-btn">
                            <i class="fas fa-comment me-1"></i> Chat
                        </button>
                    `;

                    userList.appendChild(userItem);
                });

                userList.classList.remove('d-none');
                attachChatButtonListeners();
            })
            .catch(error => {
                console.error('Search error:', error);
                loadingIndicator.classList.add('d-none');
                userList.classList.remove('d-none');
            });
        }

        // Reset to initial user list
        function resetUserList() {
            const userItems = document.querySelectorAll('.user-item');
            userItems.forEach(item => {
                item.style.display = 'flex';
            });
            noResults.classList.add('d-none');
            userList.classList.remove('d-none');
        }

        // Get readable role name
        function getRoleName(role) {
            const roleMap = {
                'admin': 'Administrator',
                'system_admin': 'System Admin',
                'technical_admin': 'Technical Admin',
                'accountmanager_admin': 'Marketing Admin',
                'account_manager': 'Account Manager',
                'debt_manager': 'Debt Manager',
                'technician': 'Field Technician',
                'ict_engineer': 'ICT Engineer',
                'finance': 'Finance Manager',
                'designer': 'Network Designer',
                'surveyor': 'Field Surveyor',
                'customer': 'Customer'
            };
            return roleMap[role] || role.replace('_', ' ');
        }

        // Attach event listeners to chat buttons
        function attachChatButtonListeners() {
            document.querySelectorAll('.start-chat-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const userItem = this.closest('.user-item');
                    startChatWithUser(userItem.dataset.userId);
                });
            });

            // Also allow clicking the entire user item
            document.querySelectorAll('.user-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('start-chat-btn')) {
                        startChatWithUser(this.dataset.userId);
                    }
                });
            });
        }

        // Start chat with selected user
        function startChatWithUser(userId) {
            // Optional: Show a modal to enter initial message
            const userConfirmed = confirm('Start a chat with this user?');

            if (userConfirmed) {
                selectedUserIdInput.value = userId;
                startChatForm.submit();
            }
        }

        // Initial attachment of event listeners
        attachChatButtonListeners();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchInput.value = '';
                filterUsers('');
                clearSearchBtn.classList.add('d-none');
            }
        });
    });
</script>
@endpush
