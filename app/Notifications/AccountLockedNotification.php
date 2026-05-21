<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountLockedNotification extends Notification
{
    use Queueable;

    protected $minutes;

    public function __construct($minutes)
    {
        $this->minutes = $minutes;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Account Locked - DarkFibre CRM')
            ->line('Your account has been temporarily locked due to 5 failed login attempts.')
            ->line("Your account will be unlocked after {$this->minutes} minutes.")
            ->line('If this was not you, please contact your system administrator immediately.')
            ->action('Reset Password', url('/password/reset'));
    }
}
