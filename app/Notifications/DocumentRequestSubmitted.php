<?php

// app/Notifications/DocumentRequestSubmitted.php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentRequestSubmitted extends Notification
{
    use Queueable;

    public $request;

    public function __construct(DocumentRequest $request)
    {
        $this->request = $request;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Document Request Submitted - Dark Fibre CRM')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your document request has been submitted successfully.')
            ->line('Request ID: #' . $this->request->id)
            ->line('Project: ' . ($this->request->lease->title ?? 'N/A'))
            ->line('Documents Requested: ' . implode(', ', $this->request->document_types))
            ->action('View Request', url('/admin/document-requests/' . $this->request->id))
            ->line('We will process your request within 2-3 business days.')
            ->line('Thank you for using our service!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'document_request_submitted',
            'request_id' => $this->request->id,
            'lease_title' => $this->request->lease->title ?? 'N/A',
            'document_types' => $this->request->document_types,
        ];
    }
}
