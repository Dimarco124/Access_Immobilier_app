<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamPasswordResetNotification extends Notification
{
    use Queueable;

    protected $adminName;

    public function __construct($adminName = null)
    {
        $this->adminName = $adminName;
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        if (env('SEND_PASSWORD_RESET_EMAIL', false)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $admin = $this->adminName ? ' par ' . $this->adminName : '';
        return (new MailMessage)
                    ->subject('Votre mot de passe a été réinitialisé')
                    ->greeting('Bonjour ' . ($notifiable->name ?? $notifiable->nom_complet ?? ''))
                    ->line("Votre mot de passe a été réinitialisé$admin.")
                    ->line('Si vous n\'êtes pas à l\'origine de ce changement, contactez immédiatement un administrateur de votre agence.')
                    ->salutation('Cordialement, L\'équipe');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Mot de passe réinitialisé',
            'message' => 'Votre mot de passe a été réinitialisé par l\'administrateur de l\'agence.',
            'admin' => $this->adminName,
        ];
    }
}
