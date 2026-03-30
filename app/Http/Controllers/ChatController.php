<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Show a specific conversation (for direct navigation).
     */
    public function show($conversationId)
    {
        $user = Auth::user();

        // Verify user has access to this conversation
        $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($conversationId);

        // Redirect to the main chat page with the conversation ID in the URL
        // The frontend JavaScript will handle loading it
        return redirect()->route('chat.index', ['conversation' => $conversationId]);
    }

    /**
     * Start a conversation from a user profile.
     */
    public function startFromProfile($userId)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($userId);

        // Check if user can chat with this user
        if (!$user->canChatWith($otherUser)) {
            return back()->with('error', 'You cannot chat with this user.');
        }

        // Find or create conversation
        $conversation = Conversation::directConversations($user->id, $userId)->first();

        if (!$conversation) {
            $conversation = Conversation::create(['type' => 'direct']);
            $conversation->participants()->createMany([
                ['user_id' => $user->id, 'role' => 'member'],
                ['user_id' => $userId, 'role' => 'member']
            ]);
        }

        // Redirect to chat with this conversation loaded
        return redirect()->route('chat.index', ['conversation' => $conversation->id]);
    }



//  public function show($conversationId)
// {
//     $user = Auth::user();

//     $conversation = Conversation::with(['messages.user', 'users' => function ($query) use ($user) {
//         $query->where('users.id', '!=', $user->id);
//     }])
//         ->whereHas('participants', function ($query) use ($user) {
//             $query->where('user_id', $user->id);
//         })
//         ->findOrFail($conversationId);

//     // Mark messages as read
//     $conversation->markAsReadForUser($user->id);

//     $messages = $conversation->messages()
//         ->orderBy('created_at', 'desc')
//         ->paginate(50);

//     // If it's an AJAX request, return JSON
//     if (request()->wantsJson()) {
//         return response()->json([
//             'conversation' => $conversation,
//             'messages' => $messages,
//             'other_user' => $conversation->getOtherParticipant($user->id)
//         ]);
//     }

//     // If it's a normal request, return the view
//     return view('chat.show', compact('conversation', 'messages'));
// }

    public function create()
    {
        $user = Auth::user();

        // Get users this user can chat with
        $users = User::where('id', '!=', $user->id)
            ->where(function ($query) use ($user) {
                if ($user->role === 'customer') {
                    $query->whereIn('role', [
                        'admin', 'system_admin', 'account_manager',
                        'accountmanager_admin', 'technical_admin'
                    ]);
                } else {
                    $query->where('role', '!=', 'customer');
                }
            })
            ->orderBy('name')
            ->get();

        return view('chat.create', compact('users'));
    }

    public function searchUsers(Request $request)
    {
        $search = $request->get('search');
        $user = Auth::user();

        $users = User::where('id', '!=', $user->id)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->where(function ($query) use ($user) {
                if ($user->role === 'customer') {
                    $query->whereIn('role', [
                        'admin', 'system_admin', 'account_manager',
                        'accountmanager_admin', 'technical_admin'
                    ]);
                } else {
                    $query->where('role', '!=', 'customer');
                }
            })
            ->limit(10)
            ->get();

        return response()->json($users);
    }
public function sendMessage(Request $request, $conversationId)
{
    $request->validate([
        'body' => 'required|string|max:5000',
    ]);

    $user = Auth::user();
    $conversation = Conversation::findOrFail($conversationId);

    // Check if user is part of conversation
    if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
        return response()->json(['error' => 'Not authorized'], 403);
    }

    $message = $conversation->messages()->create([
        'user_id' => $user->id,
        'body' => $request->body,
        'type' => 'text'
    ]);

    // Update conversation's last message
    $conversation->update(['last_message_id' => $message->id]);

    return response()->json([
        'message' => $message->load('user'),
        'success' => true
    ]);
}
public function sendMessageWithAttachment(Request $request, $conversationId)
{
    $request->validate([
        'body' => 'required|string|max:5000',
        'attachment' => 'required|file|max:10240', // 10MB max
        'attachment_type' => 'required|in:image,file'
    ]);

    $user = Auth::user();
    $conversation = Conversation::findOrFail($conversationId);

    // Check if user is part of conversation
    if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
        return response()->json(['error' => 'Not authorized'], 403);
    }

    // Handle file upload
    $attachmentPath = null;
    $attachmentName = null;

    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $path = $file->store('chat/attachments', 'public');
        $attachmentPath = $path;
        $attachmentName = $file->getClientOriginalName();
    }

    $message = $conversation->messages()->create([
        'user_id' => $user->id,
        'body' => $request->body,
        'type' => $request->attachment_type,
        'attachment_path' => $attachmentPath,
        'attachment_name' => $attachmentName
    ]);

    // Update conversation's last message
    $conversation->update(['last_message_id' => $message->id]);

    return response()->json([
        'message' => $message->load('user'),
        'success' => true
    ]);
}
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $otherUser = User::findOrFail($request->user_id);

        // Check if user can chat with this user
        if (!$user->canChatWith($otherUser)) {
            return back()->with('error', 'You cannot start a conversation with this user.');
        }

        // Check if conversation already exists
        $conversation = Conversation::directConversations($user->id, $otherUser->id)->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::create([
                'type' => 'direct',
                'title' => null
            ]);

            // Add participants
            $conversation->participants()->create([
                'user_id' => $user->id,
                'role' => 'member'
            ]);

            $conversation->participants()->create([
                'user_id' => $otherUser->id,
                'role' => 'member'
            ]);
        }

        // Add initial message if provided
        if ($request->message) {
            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body' => $request->message,
                'type' => 'text'
            ]);

            $conversation->update(['last_message_id' => $message->id]);
        }

        return redirect()->route('chat.show', $conversation->id);
    }
}
