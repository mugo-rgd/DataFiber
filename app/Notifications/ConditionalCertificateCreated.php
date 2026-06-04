<?php
// app/Notifications/ConditionalCertificateCreated.php

namespace App\Notifications;

use App\Models\ConditionalCertificate;
use App\Models\DesignRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConditionalCertificateCreated extends Notification
{
    use Queueable;

    protected $certificate;
    protected $designRequest;

    public function __construct(ConditionalCertificate $certificate, DesignRequest $designRequest)
    {
        $this->certificate = $certificate;
        $this->designRequest = $designRequest;
    }

    /**
     * Get the notification's delivery channels.
     * Only send email to designers, database to everyone
     */
    public function via($notifiable)
    {
        $channels = ['database'];

        // Only send email to designers (not to account managers)
        if ($notifiable->role === 'designer') {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $certificateDate = $this->certificate->certificate_date
            ? $this->certificate->certificate_date->format('F d, Y')
            : 'N/A';

        $commissioningEndDate = $this->certificate->commissioning_end_date
            ? $this->certificate->commissioning_end_date->format('F d, Y')
            : 'N/A';

        $customerName = $this->designRequest->customer->name ?? 'N/A';

        return (new MailMessage)
            ->subject('Conditional Certificate Issued - ' . $this->designRequest->request_number)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('A conditional certificate has been issued for your design request.')
            ->line('**Request Details:**')
            ->line("- Request #: {$this->designRequest->request_number}")
            ->line("- Customer: {$customerName}")
            ->line("- Title: {$this->designRequest->title}")
            ->line('')
            ->line('**Certificate Details:**')
            ->line("- Reference Number: {$this->certificate->ref_number}")
            ->line("- Link Name: {$this->certificate->link_name}")
            ->line("- Issue Date: {$certificateDate}")
            ->line("- Commissioning End Date: {$commissioningEndDate}")
            ->line('')
            ->line('**Next Steps:**')
            ->line('1. Review the conditional certificate')
            ->line('2. Acknowledge receipt of the certificate')
            ->line('3. After 30 days, generate the Acceptance Certificate')
            ->action('View Conditional Certificate', route('designer.certificates.conditional.show', $this->certificate))
            ->line('Thank you for using Dark Fibre CRM!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $isDesigner = $notifiable->role === 'designer';
        $isAccountManager = $notifiable->role === 'account_manager';

        $certificateDate = $this->certificate->certificate_date
            ? $this->certificate->certificate_date->format('M d, Y')
            : 'N/A';

        $commissioningEndDate = $this->certificate->commissioning_end_date
            ? $this->certificate->commissioning_end_date->format('M d, Y')
            : 'N/A';

        $customerName = $this->designRequest->customer->name ?? 'N/A';
        $actionUrl = '#';

        if ($isDesigner) {
            $actionUrl = route('designer.certificates.conditional.show', $this->certificate);
        } elseif ($isAccountManager && $this->designRequest->customer_id) {
            $actionUrl = route('account-manager.customers.show', $this->designRequest->customer_id);
        }

        if ($isDesigner) {
            $messagePreview = 'Conditional certificate issued for your request #' . $this->designRequest->request_number;
            $message = "A conditional certificate (#{$this->certificate->ref_number}) has been issued for your design request #{$this->designRequest->request_number}.\n\n"
                     . "Customer: {$customerName}\n"
                     . "Link Name: {$this->certificate->link_name}\n"
                     . "Issue Date: {$certificateDate}\n"
                     . "Commissioning End: {$commissioningEndDate}\n\n"
                     . "Please review and acknowledge the certificate.";
        } else {
            $designerName = $this->designRequest->designer->name ?? 'Not Assigned';
            $messagePreview = 'Conditional certificate issued for request #' . $this->designRequest->request_number;
            $message = "A conditional certificate (#{$this->certificate->ref_number}) has been issued for design request #{$this->designRequest->request_number}.\n\n"
                     . "Customer: {$customerName}\n"
                     . "Designer: {$designerName}\n"
                     . "Link Name: {$this->certificate->link_name}\n"
                     . "Issue Date: {$certificateDate}\n"
                     . "Commissioning End: {$commissioningEndDate}";
        }

        return [
            'type' => 'conditional_certificate',
            'certificate_id' => $this->certificate->id,
            'certificate_ref' => $this->certificate->ref_number,
            'design_request_id' => $this->designRequest->id,
            'request_number' => $this->designRequest->request_number,
            'customer_name' => $customerName,
            'customer_id' => $this->designRequest->customer_id,
            'sender_id' => auth()->id(),
            'sender_name' => auth()->user()->name,
            'sender_role' => auth()->user()->role,
            'sender_avatar' => strtoupper(substr(auth()->user()->name, 0, 1)),
            'message_preview' => $messagePreview,
            'message' => $message,
            'action_url' => $actionUrl,
            'icon' => 'file-contract',
            'color' => 'info',
            'title' => 'Conditional Certificate Issued',
            'created_at' => now()->toDateTimeString(),
            'is_designer_notification' => $isDesigner,
            'is_account_manager_notification' => $isAccountManager
        ];
    }
}
