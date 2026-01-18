<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    protected string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your new password - Ecoly'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->first_name]))
            ->line(__('Your password has been reset by an administrator.'))
            ->line(__('Your new password is: **:password**', ['password' => $this->password]))
            ->line(__('We recommend you change it after your first login.'))
            ->action(__('Se connecter'), url('/login'))
            ->line(__('If you did not request this reset, please contact administration.'))
            ->salutation(__('Regards,') . ' ' . __('the Ecoly team'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'password_reset',
            'message' => __('Your password has been reset.'),
        ];
    }
}
