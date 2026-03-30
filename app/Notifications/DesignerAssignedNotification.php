<?php

namespace App\Notifications;

use App\Models\DesignRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DesignerAssignedNotification extends Notification
{
    use Queueable;

    public $designRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(DesignRequest $designRequest)
    {
        $this->designRequest = $designRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Design Request Assigned')
                    ->line('You have been assigned to a new design request.')
                    ->action('View Design Request', route('designer.requests.show', $this->designRequest->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'design_request_id' => $this->designRequest->id,
            'title' => 'New Design Request Assigned',
            'message' => 'You have been assigned to design request #' . $this->designRequest->id,
            'link' => route('designer.requests.show', $this->designRequest->id),
        ];
    }
}
