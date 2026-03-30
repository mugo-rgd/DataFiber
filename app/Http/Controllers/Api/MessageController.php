<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Send a message
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required_without:attachment|string|max:5000',
            'attachment' => 'nullable|file|max:10240' // 10MB max
        ]);

        $user = Auth::user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        // Check if user is part of conversation
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $messageData = [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'type' => 'text',
            'body' => $request->body ?? ''
        ];

        // Handle file attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat/attachments', 'public');

            $messageData['type'] = $this->getFileType($file->getMimeType());
            $messageData['attachment_path'] = $path;
            $messageData['attachment_name'] = $file->getClientOriginalName();
            $messageData['body'] = $request->body ?: 'Sent a file';
        }

        $message = Message::create($messageData);

        // Update conversation's last message
        $conversation->update(['last_message_id' => $message->id]);

        // Broadcast the message (for real-time)
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => $message->load('user'),
            'conversation' => $conversation->fresh()
        ]);
    }

    // Get more messages (pagination)
    public function index(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

        // Check if user is part of conversation
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json($messages);
    }

    // Mark messages as read
    public function markAsRead(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

        // Mark all messages in conversation as read for this user
        Message::where('conversation_id', $conversationId)
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Update participant's last read
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        return response()->json(['success' => true]);
    }

    // Delete a message
    public function destroy($messageId)
    {
        $message = Message::findOrFail($messageId);
        $user = Auth::user();

        // Only allow sender or admin to delete
        if ($message->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        // Delete file if exists
        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    private function getFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } else {
            return 'file';
        }
    }
}
