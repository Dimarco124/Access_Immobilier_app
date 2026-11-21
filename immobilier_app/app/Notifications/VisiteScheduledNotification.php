<?php

namespace App\Notifications;

use App\Models\Visite;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisiteScheduledNotification extends Notification
{
    use Queueable;

    protected $visite;

    public function __construct(Visite $visite)
    {
        $this->visite = $visite;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'visite_id' => $this->visite->id,
            'bien' => $this->visite->bien->nom_bien,
            'date_visite' => $this->visite->date_visite,
            'heure_visite' => $this->visite->heure_visite,
            'message' => "Visite programmée pour {$this->visite->bien->nom_bien} le {$this->visite->date_visite->format('d/m/Y')} à {$this->visite->heure_visite}.",
            'url' => route('visites.index'),
            'type' => 'visite_scheduled',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Visite programmée - {$this->visite->bien->nom_bien}")
            ->greeting("Bonjour {$this->visite->nom_visiteur},")
            ->line("Votre visite a été programmée avec succès.")
            ->line("**Bien:** {$this->visite->bien->nom_bien}")
            ->line("**Adresse:** {$this->visite->bien->adresse}, {$this->visite->bien->ville}")
            ->line("**Date:** {$this->visite->date_visite->format('d/m/Y')}")
            ->line("**Heure:** {$this->visite->heure_visite}")
            ->action('Voir les détails', route('visites.index'))
            ->line('Merci de confirmer votre présence.');
    }
}
