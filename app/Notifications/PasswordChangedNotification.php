<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
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
            ->subject(__('Confirmation de changement de mot de passe - Ecoly'))
            ->greeting(__('Bonjour :name,', ['name' => $notifiable->first_name]))
            ->line(__('Votre mot de passe a été changé avec succès.'))
            ->line(__('Si vous n\'êtes pas à l\'origine de ce changement, contactez immédiatement l\'administration.'))
            ->action(__('Se connecter'), url('/login'))
            ->salutation(__('Cordialement, l\'équipe Ecoly'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'password_changed',
            'message' => __('Votre mot de passe a été changé avec succès.'),
        ];
    }
}
