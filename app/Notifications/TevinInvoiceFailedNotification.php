<?php

namespace App\Notifications;

use App\Models\ConsolidatedBilling;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TevinInvoiceFailedNotification extends Notification
{
    use Queueable;

    protected ConsolidatedBilling $billing;
    protected \Exception $exception;

    public function __construct(ConsolidatedBilling $billing, \Exception $exception)
    {
        $this->billing = $billing;
        $this->exception = $exception;
    }

    public function via($notifiable): array
    {
        // Choose notification channels
        return ['mail', 'database'];
        // Alternative: return ['slack', 'mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('TEVIN Invoice Submission Failed')
            ->line('A TEVIN invoice submission has failed permanently.')
            ->line('Billing ID: ' . $this->billing->id)
            ->line('Billing Number: ' . $this->billing->billing_number)
            ->line('Error: ' . $this->exception->getMessage())
            ->line('Error Code: ' . $this->exception->getCode())
            ->action('View Billing', url('/billings/' . $this->billing->id))
            ->line('Please review and take appropriate action.');
    }

    public function toArray($notifiable): array
    {
        return [
            'billing_id' => $this->billing->id,
            'billing_number' => $this->billing->billing_number,
            'error_message' => $this->exception->getMessage(),
            'error_code' => $this->exception->getCode(),
            'timestamp' => now()->toISOString(),
            'action_url' => '/billings/' . $this->billing->id
        ];
    }

    // Optional: Slack notification
    public function toSlack($notifiable): App\Notifications\SlackMessage
    {
        return (new SlackMessage)
            ->error()
            ->content('TEVIN Invoice Submission Failed!')
            ->attachment(function ($attachment) {
                $attachment->title('Billing #' . $this->billing->billing_number)
                    ->fields([
                        'Billing ID' => $this->billing->id,
                        'Error' => $this->exception->getMessage(),
                        'Error Code' => $this->exception->getCode(),
                        'Time' => now()->format('Y-m-d H:i:s')
                    ]);
            });
    }
}
