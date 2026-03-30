<?php
// app/Http/Controllers/Api/ChatController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewMessageNotification;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Notification;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['lastMessage', 'users' => function ($query) use ($user) {
                $query->where('users.id', '!=', $user->id);
            }])
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                    ->from('messages')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->orderByDesc('created_at')
                    ->limit(1);
            })
            ->paginate(20);

        return view('chat.index', compact('conversations'));
    }
    /**
     * Search users for starting a new chat
     */
    public function searchUsers(Request $request)
    {
        try {
            $search = $request->get('search');
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            if (strlen($search) < 2) {
                return response()->json([]);
            }

            $users = User::where('id', '!=', $user->id)
                ->where(function($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%")
                          ->orWhere('company_name', 'LIKE', "%{$search}%");
                })
                ->where('status', 'active')
                ->select('id', 'name', 'email', 'role', 'company_name', 'phone')
                ->orderBy('name')
                ->limit(15)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'company_name' => $user->company_name,
                        'phone' => $user->phone,
                        'initial' => substr($user->name, 0, 1),
                    ];
                });

            return response()->json($users);

        } catch (\Exception $e) {
            Log::error('Error searching users:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to search users'], 500);
        }
    }

    /**
     * Start a new conversation
     */
    public function startConversation(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $user = Auth::user();
            $otherUserId = $request->user_id;

            // Check if conversation already exists
            $existingConversation = Conversation::whereHas('users', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereHas('users', function($query) use ($otherUserId) {
                    $query->where('user_id', $otherUserId);
                })
                ->first();

            if ($existingConversation) {
                // Load the other user info
                $existingConversation->load(['users' => function($query) {
                    $query->select('users.id', 'users.name', 'users.email', 'users.role');
                }]);

                return response()->json([
                    'conversation' => $existingConversation,
                    'existing' => true
                ]);
            }

            // Create new conversation
            $conversation = Conversation::create([
                'type' => 'private'
            ]);

            // Attach users
            $conversation->users()->attach([$user->id, $otherUserId]);

            // Load the other user info
            $conversation->load(['users' => function($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.role');
            }]);

            return response()->json([
                'conversation' => $conversation,
                'existing' => false
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error starting conversation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to start conversation'], 500);
        }
    }

    /**
     * Get conversation with messages
     */
    public function show($conversationId)
    {
        try {
            $user = Auth::user();

            // Find conversation where user is a participant
            $conversation = Conversation::where('id', $conversationId)
                ->whereHas('users', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['users' => function($query) {
                    $query->select('users.id', 'users.name', 'users.email', 'users.role');
                }])
                ->firstOrFail();

            // Get messages with pagination
            $messages = Message::where('conversation_id', $conversation->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get the other user
            $otherUser = $conversation->users
                ->where('id', '!=', $user->id)
                ->first();

            return response()->json([
                'conversation' => $conversation,
                'messages' => $messages,
                'other_user' => $otherUser
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading conversation:', [
                'conversation_id' => $conversationId,
                'message' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Conversation not found'], 404);
        }
    }

    /**
     * Send a message
     */

public function sendMessage(Request $request)
{
    try {
        \Log::info('=== SEND MESSAGE STARTED ===', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required|string',
            'type' => 'sometimes|in:text,image,file'
        ]);

        $user = Auth::user();

        // Verify user is part of the conversation
        $conversation = Conversation::where('id', $request->conversation_id)
            ->whereHas('users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();

        \Log::info('Conversation found', ['conversation_id' => $conversation->id]);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $request->body,
            'type' => $request->type ?? 'text'
        ]);

        \Log::info('Message created', ['message_id' => $message->id]);

        // Load relationships
        $message->load('user');

        // Update conversation updated_at timestamp
        $conversation->touch();

        // Get the other user in the conversation
        $otherUser = $conversation->users()
            ->where('user_id', '!=', $user->id)
            ->first();

        \Log::info('Other user found', ['other_user_id' => $otherUser?->id]);

        // Send notification to the other user
        if ($otherUser) {
            \Log::info('Attempting to send notification to user: ' . $otherUser->id);

            try {
                $notification = new \App\Notifications\NewMessageNotification($message, $conversation);
                $otherUser->notify($notification);
                \Log::info('Notification sent successfully');
            } catch (\Exception $e) {
                \Log::error('Failed to send notification: ' . $e->getMessage(), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            \Log::warning('No other user found in conversation');
        }

        // Broadcast event for real-time updates
        try {
            broadcast(new \App\Events\MessageSent($message, $conversation))->toOthers();
            \Log::info('Broadcast sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to broadcast: ' . $e->getMessage());
        }

        \Log::info('=== SEND MESSAGE COMPLETED ===');

        return response()->json($message, 201);

    } catch (\Exception $e) {
        \Log::error('=== SEND MESSAGE FAILED ===', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
    }
}

    /**
     * Mark messages as read
     */
    public function markAsRead($conversationId)
    {
        try {
            $user = Auth::user();

            // Find conversation where user is a participant
            $conversation = Conversation::where('id', $conversationId)
                ->whereHas('users', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->firstOrFail();

            // Mark unread messages from other users as read
            Message::where('conversation_id', $conversation->id)
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error marking messages as read:', [
                'message' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to mark messages as read'], 500);
        }
    }
}
