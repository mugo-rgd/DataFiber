<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;

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
        // Decode document_types from JSON string to array
        $documentTypes = $this->request->document_types;
        if (is_string($documentTypes)) {
            $documentTypes = json_decode($documentTypes, true);
        }

        // Ensure it's an array
        if (!is_array($documentTypes)) {
            $documentTypes = [];
        }

        // Get formatted document types string
        $documentTypesString = !empty($documentTypes) ? implode(', ', $documentTypes) : 'No documents specified';

        return (new MailMessage)
            ->subject('Document Request Submitted - Dark Fibre CRM')
            ->greeting('Hello ' . ($notifiable->name ?? 'Customer') . '!')
            ->line('Your document request has been submitted successfully.')
            ->line('Request ID: #' . $this->request->id)
            ->line('Project: ' . ($this->request->lease->title ?? 'N/A'))
            ->line('Documents Requested: ' . $documentTypesString)
            ->action('View Request', url('/customer/documents/' . $this->request->id))
            ->line('We will process your request within 2-3 business days.')
            ->line('Thank you for using Dark Fibre CRM!');
    }

    public function toArray($notifiable)
    {
        // Decode document_types from JSON string to array
        $documentTypes = $this->request->document_types;
        if (is_string($documentTypes)) {
            $documentTypes = json_decode($documentTypes, true);
        }

        return [
            'type' => 'document_request_submitted',
            'request_id' => $this->request->id,
            'lease_title' => $this->request->lease->title ?? 'N/A',
            'document_types' => $documentTypes ?? [],
            'status' => $this->request->status,
            'message' => 'Your document request has been submitted successfully'
        ];
    }
}
