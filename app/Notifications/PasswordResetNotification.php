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
            ->subject(__('Votre nouveau mot de passe - Ecoly'))
            ->greeting(__('Bonjour :name,', ['name' => $notifiable->first_name]))
            ->line(__('Votre mot de passe a été réinitialisé par un administrateur.'))
            ->line(__('Votre nouveau mot de passe est : **:password**', ['password' => $this->password]))
            ->line(__('Nous vous recommandons de le changer après votre première connexion.'))
            ->action(__('Se connecter'), url('/login'))
            ->line(__('Si vous n\'avez pas demandé cette réinitialisation, contactez l\'administration.'))
            ->salutation(__('Cordialement, l\'équipe Ecoly'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'password_reset',
            'message' => __('Votre mot de passe a été réinitialisé.'),
        ];
    }
}
