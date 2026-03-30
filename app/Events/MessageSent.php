<?php
// app/Events/MessageSent.php

namespace App\Events;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversation;

    public function __construct(Message $message, Conversation $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;
    }

    public function broadcastOn()
    {
        // Create a private channel for each user in the conversation
        $channels = [];

        foreach ($this->conversation->users as $user) {
            if ($user->id !== $this->message->user_id) {
                $channels[] = new PrivateChannel('user.' . $user->id);
            }
        }

        // Also broadcast to the conversation channel
        $channels[] = new PrivateChannel('conversation.' . $this->conversation->id);

        return $channels;
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->load('user'),
            'conversation' => $this->conversation->load('users'),
            'sent_by' => $this->message->user_id
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
