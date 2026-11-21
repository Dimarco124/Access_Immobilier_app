<?php

namespace App\Notifications;

use App\Models\Agence;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamMemberInvitation extends Notification
{
    use Queueable;

    protected $token;
    protected $agence;

    public function __construct($token, Agence $agence)
    {
        $this->token = $token;
        $this->agence = $agence;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('team-setup.accept-invitation', $this->token);

        return (new MailMessage)
                    ->subject('Invitation à rejoindre ' . $this->agence->nom)
                    ->greeting('Bonjour ' . $notifiable->nom_complet . ' !')
                    ->line('Vous avez été invité(e) à rejoindre l\'équipe de **' . $this->agence->nom . '**.')
                    ->line('Vous aurez accès aux modules suivants :')
                    ->line($notifiable->modules->pluck('nom')->join(', '))
                    ->action('Activer mon compte', $url)
                    ->line('Ce lien d\'invitation est valide pendant 7 jours.')
                    ->line('Si vous n\'avez pas demandé cette invitation, vous pouvez ignorer cet email.')
                    ->salutation('Cordialement, L\'équipe ' . $this->agence->nom);
    }
}
