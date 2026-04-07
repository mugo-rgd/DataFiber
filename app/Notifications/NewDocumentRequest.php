<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewDocumentRequest extends Notification implements ShouldQueue
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
            ->subject('New Document Request - Dark Fibre CRM')
            ->greeting('Hello ' . ($notifiable->name ?? 'Admin') . '!')
            ->line('A new document request has been submitted and requires your attention.')
            ->line('Request ID: #' . $this->request->id)
            ->line('Customer: ' . ($this->request->user->company_name ?? $this->request->user->name ?? 'N/A'))
            ->line('Project: ' . ($this->request->lease->title ?? 'N/A'))
            ->line('Documents Requested: ' . $documentTypesString)
            ->line('Additional Notes: ' . ($this->request->additional_notes ?? 'None'))
            ->action('View Request', url('/admin/document-requests/' . $this->request->id))
            ->line('Please review and process this request within 2-3 business days.');
    }

    public function toArray($notifiable)
    {
        // Decode document_types from JSON string to array
        $documentTypes = $this->request->document_types;
        if (is_string($documentTypes)) {
            $documentTypes = json_decode($documentTypes, true);
        }

        return [
            'type' => 'new_document_request',
            'request_id' => $this->request->id,
            'customer_name' => $this->request->user->company_name ?? $this->request->user->name ?? 'N/A',
            'lease_title' => $this->request->lease->title ?? 'N/A',
            'document_types' => $documentTypes ?? [],
            'additional_notes' => $this->request->additional_notes,
            'status' => $this->request->status,
            'message' => 'New document request requires your attention'
        ];
    }
}
