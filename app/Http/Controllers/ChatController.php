<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Display the chat interface (Web)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!Gate::allows('use-chat')) {
            abort(403, 'You do not have permission to access chat.');
        }

        // Get conversation IDs where user is a participant
        $conversationIds = DB::table('participants')
            ->where('user_id', $user->id)
            ->pluck('conversation_id');

        // Get conversations with last message
        $conversations = Conversation::whereIn('id', $conversationIds)
            ->with('lastMessage')
            ->get();

        // Manually load other participant for each conversation
        foreach ($conversations as $conversation) {
            $conversation->other_participant = DB::table('participants')
                ->join('users', 'users.id', '=', 'participants.user_id')
                ->where('participants.conversation_id', $conversation->id)
                ->where('participants.user_id', '!=', $user->id)
                ->select('users.id', 'users.name', 'users.email', 'users.role')
                ->first();

            // Calculate unread count
            $conversation->unread_count = Message::where('conversation_id', $conversation->id)
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        // Sort conversations by last message time
        $conversations = $conversations->sortByDesc(function ($conversation) {
            return $conversation->lastMessage ? $conversation->lastMessage->created_at : $conversation->created_at;
        });

        // Paginate
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $conversations = new \Illuminate\Pagination\LengthAwarePaginator(
            $conversations->forPage($currentPage, $perPage),
            $conversations->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        $selectedConversation = null;
        $selectedMessages = null;

        if ($request->get('conversation')) {
            $selectedConversation = Conversation::whereIn('id', $conversationIds)
                ->find($request->get('conversation'));

            if ($selectedConversation) {
                // Load other participant
                $selectedConversation->other_participant = DB::table('participants')
                    ->join('users', 'users.id', '=', 'participants.user_id')
                    ->where('participants.conversation_id', $selectedConversation->id)
                    ->where('participants.user_id', '!=', $user->id)
                    ->select('users.id', 'users.name', 'users.email', 'users.role')
                    ->first();

                // Load messages
                $selectedMessages = $selectedConversation->messages()
                    ->with('user')
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('chat.index', compact('conversations', 'selectedConversation', 'selectedMessages'));
    }

    /**
     * Search for users (AJAX endpoint)
     */
    public function searchUsers(Request $request)
    {
        try {
            $search = $request->get('search');

            if (strlen($search) < 2) {
                return response()->json([]);
            }

            $user = Auth::user();

            $users = User::where('id', '!=', $user->id)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->limit(10)
                ->get(['id', 'name', 'email', 'role']);

            return response()->json($users);

        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Start a new conversation (AJAX)
     */
    public function startConversation(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $user = Auth::user();
            $otherUser = User::findOrFail($request->user_id);

            // Check if conversation already exists
            $conversation = Conversation::where('type', 'direct')
                ->whereHas('participants', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereHas('participants', function ($q) use ($otherUser) {
                    $q->where('user_id', $otherUser->id);
                })
                ->first();

            if (!$conversation) {
                $conversation = Conversation::create(['type' => 'direct']);
                $conversation->participants()->createMany([
                    ['user_id' => $user->id, 'role' => 'member'],
                    ['user_id' => $otherUser->id, 'role' => 'member']
                ]);
            }

            return response()->json([
                'success' => true,
                'conversation' => $conversation
            ]);

        } catch (\Exception $e) {
            Log::error('Start conversation error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Download file from message
     */
    public function downloadFile($messageId)
    {
        try {
            $message = Message::findOrFail($messageId);

            // Check if user has access to this message
            $conversation = $message->conversation;
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                abort(403, 'Unauthorized access to this file.');
            }

            // Check if message has a file
            if (!in_array($message->type, ['file', 'image']) || !$message->attachment_path) {
                abort(404, 'File not found for this message.');
            }

            // Build the file path
            $filePath = storage_path('app/public/' . $message->attachment_path);

            // Also try alternative path if file not found
            if (!file_exists($filePath)) {
                $filePath = storage_path('app/' . $message->attachment_path);
            }

            if (!file_exists($filePath)) {
                abort(404, 'File not found on server.');
            }

            // Return file download response
            return response()->download($filePath, $message->attachment_name, [
                'Content-Type' => $message->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $message->attachment_name . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('File download error: ' . $e->getMessage());
            abort(404, 'Unable to download file: ' . $e->getMessage());
        }
    }

    /**
     * Store a new message (Main method for both text and file)
     */
    public function store(Request $request, $conversationId)
    {
        try {
            Log::info('Store message called', [
                'conversation_id' => $conversationId,
                'type' => $request->type,
                'has_file' => $request->hasFile('file'),
                'all_data' => $request->all()
            ]);

            $request->validate([
                'body' => 'nullable|string',
                'type' => 'required|in:text,file',
                'file' => 'required_if:type,file|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip'
            ]);

            $conversation = Conversation::findOrFail($conversationId);

            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $messageData = [
                'conversation_id' => $conversationId,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'read_at' => null
            ];

            if ($request->type === 'text') {
                $messageData['body'] = $request->body ?? '';
            } else {
                // Handle file upload
                $file = $request->file('file');

                Log::info('File details', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ]);

                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $filePath = $file->storeAs('chat/' . $conversationId, $fileName, 'public');

                Log::info('File saved', ['path' => $filePath]);

                $messageData['body'] = $request->body ?? 'Shared a file';
                $messageData['attachment_name'] = $file->getClientOriginalName();
                $messageData['attachment_path'] = $filePath;
                $messageData['mime_type'] = $file->getMimeType();
                $messageData['file_size'] = $file->getSize();
            }

            $message = Message::create($messageData);

            Log::info('Message created', ['message_id' => $message->id, 'type' => $message->type]);

            // Update conversation's last message
            $conversation->update([
                'last_message_at' => now(),
                'last_message' => $message->type === 'file' ? '📎 ' . $message->attachment_name : $message->body
            ]);

            // Load the message with user relationship
            $message->load('user');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => ['message' => $message]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Store message error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark messages as read (AJAX)
     */
    public function markAsRead($conversationId)
    {
        try {
            $user = Auth::user();
            $conversation = Conversation::findOrFail($conversationId);

            if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
                return response()->json(['error' => 'Not authorized'], 403);
            }

            $updated = $conversation->messages()
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['marked_count' => $updated]);

        } catch (\Exception $e) {
            Log::error('Mark as read error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get unread count (AJAX)
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();

            $unreadCount = Message::whereHas('conversation.participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();

            return response()->json(['unread_count' => $unreadCount]);

        } catch (\Exception $e) {
            Log::error('Get unread count error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get conversation with messages (AJAX)
     */
    public function getConversation($conversationId)
    {
        try {
            $user = Auth::user();

            // Verify user has access to this conversation
            $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($conversationId);

            // Get messages with pagination
            $messages = $conversation->messages()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            // Get other participant
            $otherParticipant = DB::table('participants')
                ->join('users', 'users.id', '=', 'participants.user_id')
                ->where('participants.conversation_id', $conversation->id)
                ->where('participants.user_id', '!=', $user->id)
                ->select('users.id', 'users.name', 'users.email', 'users.role')
                ->first();

            return response()->json([
                'success' => true,
                'conversation' => $conversation,
                'messages' => $messages,
                'other_user' => $otherParticipant
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Conversation not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Get conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the create conversation form (Web)
     */
    public function create()
    {
        $user = Auth::user();

        if (!Gate::allows('start-conversation')) {
            abort(403, 'You cannot start conversations.');
        }

        $users = User::where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('chat.create', compact('users'));
    }

    /**
     * Show a specific conversation (Web redirect)
     */
    public function show($conversationId)
    {
        $user = Auth::user();

        $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($conversationId);

        return redirect()->route('chat.index', ['conversation' => $conversationId]);
    }

    /**
     * Start a conversation from a user profile (Web)
     */
    public function startFromProfile($userId)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($userId);

        $conversation = Conversation::where('type', 'direct')
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(['type' => 'direct']);
            $conversation->participants()->createMany([
                ['user_id' => $user->id, 'role' => 'member'],
                ['user_id' => $userId, 'role' => 'member']
            ]);
        }

        return redirect()->route('chat.index', ['conversation' => $conversation->id]);
    }
}
