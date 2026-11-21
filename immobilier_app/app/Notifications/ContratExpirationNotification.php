<?php

namespace App\Notifications;

use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContratExpirationNotification extends Notification
{
    use Queueable;

    protected $contrat;
    protected $joursRestants;

    public function __construct(Contrat $contrat, int $joursRestants)
    {
        $this->contrat = $contrat;
        $this->joursRestants = $joursRestants;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'contrat_id' => $this->contrat->id,
            'bien' => $this->contrat->bien->nom_bien,
            'date_fin' => $this->contrat->date_fin,
            'jours_restants' => $this->joursRestants,
            'message' => "Votre contrat pour {$this->contrat->bien->nom_bien} expire dans {$this->joursRestants} jours (le {$this->contrat->date_fin->format('d/m/Y')}).",
            'url' => route('contrats.show', $this->contrat),
            'type' => 'contrat_expiration',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Expiration prochaine de votre contrat")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre contrat de location arrive à expiration.")
            ->line("**Bien:** {$this->contrat->bien->nom_bien}")
            ->line("**Date d'expiration:** {$this->contrat->date_fin->format('d/m/Y')}")
            ->line("**Jours restants:** {$this->joursRestants} jours")
            ->action('Voir le contrat', route('contrats.show', $this->contrat))
            ->line('Veuillez contacter l\'agence pour discuter du renouvellement.');
    }
}
