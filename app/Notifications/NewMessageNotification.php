<?php
// app/Notifications/NewMessageNotification.php

namespace App\Notifications;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewMessageNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $conversation;

    public function __construct($message, $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;

        \Log::info('NewMessageNotification constructed', [
            'message_exists' => !is_null($message),
            'conversation_exists' => !is_null($conversation)
        ]);
    }

    public function via($notifiable)
    {
        \Log::info('Notification via method called for user: ' . $notifiable->id);
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        \Log::info('Creating notification array for user: ' . $notifiable->id);

        if (!$this->message || !$this->message->user) {
            \Log::error('Message or user is null in notification', [
                'message' => $this->message,
                'user' => $this->message->user ?? null
            ]);

            return [
                'message_id' => null,
                'conversation_id' => $this->conversation->id ?? null,
                'sender_id' => null,
                'sender_name' => 'Someone',
                'sender_avatar' => '?',
                'message_preview' => 'New message',
                'type' => 'new_message',
                'created_at' => now()->toDateTimeString()
            ];
        }

        $sender = $this->message->user;

        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'sender_avatar' => substr($sender->name, 0, 1),
            'message_preview' => substr($this->message->body, 0, 50) . (strlen($this->message->body) > 50 ? '...' : ''),
            'type' => 'new_message',
            'created_at' => now()->toDateTimeString()
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message ? $this->message->load('user') : null,
            'conversation' => $this->conversation ? $this->conversation->load('users') : null,
            'notification' => $this->toArray($notifiable)
        ]);
    }

    public function broadcastType()
    {
        return 'new-message';
    }
}
